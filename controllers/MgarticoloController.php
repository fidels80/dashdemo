<?php

namespace app\controllers;

use Yii;
use app\models\MgArticolo;
use app\models\MgArticoloUm;
use app\models\MgAliquotaIva;
use app\models\MgAttributoArticolo;
use app\models\MgUnitaMisura;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD articoli del microgestionale.
 */
class MgarticoloController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => MgArticolo::find()
                ->with(['ivaVendita', 'ivaAcquisto', 'marca', 'modello', 'tessuto', 'taglia', 'colore'])
                ->orderBy(['descrizione' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new MgArticolo();
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveUnita($model, Yii::$app->request->post('unita', []));
            return $this->redirect([ 'index' ]);
        }

        return $this->render('create', $this->formData($model));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveUnita($model, Yii::$app->request->post('unita', []));
            return $this->redirect([ 'index' ]);
        }

        return $this->render('update', $this->formData($model));
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Dati comuni alle form create/update (aliquote, attributi, unità).
     */
    private function formData($model)
    {
        return [
            'model' => $model,
            'aliquote' => MgAliquotaIva::mapAttivi(),
            'unita' => MgUnitaMisura::mapAttivi(),
            'marche' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_MARCA),
            'modelli' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_MODELLO),
            'tessuti' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TESSUTO),
            'taglie' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_TAGLIA),
            'colori' => MgAttributoArticolo::mapByTipo(MgAttributoArticolo::TIPO_COLORE),
            'unitaArticolo' => $model->isNewRecord ? [] : $model->unitaMisura,
        ];
    }

    /**
     * Sincronizza le unità di misura dell'articolo (con fattore e predefinita).
     */
    private function saveUnita($model, $unita)
    {
        MgArticoloUm::deleteAll(['id_articolo' => $model->id]);

        $predefinitaAssegnata = false;
        $primaId = null;
        $visti = [];
        foreach ((array) $unita as $u) {
            $idUm = $u['id_unita_misura'] ?? null;
            if (empty($idUm) || isset($visti[(int) $idUm])) {
                continue;
            }
            $visti[(int) $idUm] = true;
            $riga = new MgArticoloUm();
            $riga->id_articolo = $model->id;
            $riga->id_unita_misura = (int) $idUm;
            $riga->fattore = ($u['fattore'] ?? '') === '' ? 1 : $u['fattore'];
            $predef = !empty($u['predefinita']);
            if ($predef && !$predefinitaAssegnata) {
                $riga->predefinita = 1;
                $predefinitaAssegnata = true;
            } else {
                $riga->predefinita = 0;
            }
            $riga->attivo = true;
            $riga->save(false);
            if ($primaId === null) {
                $primaId = $riga->id;
            }
        }

        // Se nessuna predefinita è stata scelta, imposta la prima unità.
        if (!$predefinitaAssegnata && $primaId !== null) {
            MgArticoloUm::updateAll(['predefinita' => 1], ['id' => $primaId]);
        }

        // Mantiene allineato il vecchio campo um con l'unità predefinita.
        unset($model->unitaMisura);
        $def = $model->unitaPredefinita;
        $model->um = $def && $def->unitaMisura ? $def->unitaMisura->codice : null;
        $model->save(false, ['um']);
    }

    /**
     * Creazione rapida di un articolo (AJAX), usata dalla form documenti.
     */
    public function actionCreateAjax()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgArticolo();
        $model->attivo = true;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            // Unità di misura scelta dalla tendina oppure creata dal campo testuale "um".
            $idUm = Yii::$app->request->post('id_unita_misura');
            if (!empty($idUm)) {
                $um = MgUnitaMisura::findOne($idUm);
            } else {
                $umCodice = trim((string) $model->um);
                $um = $umCodice !== '' ? MgUnitaMisura::findOrCreate($umCodice) : null;
            }
            if ($um) {
                $riga = new MgArticoloUm();
                $riga->id_articolo = $model->id;
                $riga->id_unita_misura = $um->id;
                $riga->fattore = 1;
                $riga->predefinita = 1;
                $riga->attivo = true;
                $riga->save(false);
                $model->um = $um->codice;
                $model->save(false, ['um']);
            }

            // Ricarica per ottenere il guid generato dal database.
            $model->refresh();

            $vendita = $model->ivaVendita;
            $acquisto = $model->ivaAcquisto;
            return [
                'success' => true,
                'articolo' => [
                    'id' => (int) $model->id,
                    'guid' => $model->guid,
                    'codice' => $model->codice,
                    'descrizione' => $model->descrizione,
                    'um' => $model->um,
                    'prezzo' => (float) $model->prezzo,
                    'iva' => (float) $model->iva,
                    'id_iva_vendita' => $model->id_iva_vendita ? (int) $model->id_iva_vendita : null,
                    'id_iva_acquisto' => $model->id_iva_acquisto ? (int) $model->id_iva_acquisto : null,
                    'iva_vendita_perc' => $vendita ? (float) $vendita->percentuale : (float) $model->iva,
                    'iva_acquisto_perc' => $acquisto ? (float) $acquisto->percentuale : (float) $model->iva,
                    'unita' => $this->unitaJson($model),
                ],
            ];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    /**
     * Elenco unità di misura di un articolo in formato JSON (per le righe documento).
     */
    public function actionUnita($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = MgArticolo::findOne($id);
        if (!$model) {
            return ['success' => false, 'unita' => []];
        }

        return ['success' => true, 'unita' => $this->unitaJson($model)];
    }

    /**
     * Serializza le unità di misura di un articolo.
     */
    private function unitaJson($model)
    {
        $out = [];
        foreach ($model->unitaMisura as $u) {
            $out[] = [
                'id' => (int) $u->id_unita_misura,
                'codice' => $u->unitaMisura ? $u->unitaMisura->codice : '',
                'etichetta' => $u->unitaMisura ? $u->unitaMisura->etichetta : '',
                'fattore' => (float) $u->fattore,
                'predefinita' => (int) $u->predefinita,
            ];
        }
        return $out;
    }

    protected function findModel($id)
    {
        if (($model = MgArticolo::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Articolo non trovato.');
    }
}
