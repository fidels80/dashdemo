<?php

namespace app\controllers;

use Yii;
use app\models\MgCrmTicket;
use app\models\MgCrmTicketMessaggio;
use app\models\MgCrmTipo;
use app\models\MgCrmStato;
use app\models\MgCrmPriorita;
use app\models\MgCrmUtenteAnagrafica;
use app\models\MgCrmAllegato;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgMatricola;
use app\models\MgDocumento;
use app\components\AllegatiCrm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use yii\filters\VerbFilter;

/**
 * Portale clienti (utenti esterni): elenco delle proprie richieste,
 * apertura nuove richieste (assistenza/amministrative/preventivi) e
 * consultazione in sola lettura di documenti e scadenze delle aziende
 * a cui l'utente e' collegato.
 */
class PortaleController extends Controller
{
    public $layout = 'portale';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'richiesta-rispondi' => ['POST'],
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $identity = Yii::$app->user->identity;
        if (!$identity) {
            return $this->redirect(['site/login']);
        }

        return true;
    }

    public function actionIndex()
    {
        $query = MgCrmTicket::queryEsterno(Yii::$app->user->id)
            ->with(['tipo', 'stato', 'priorita']);

        $totale = (clone $query)->count();
        $aperti = (clone $query)->andWhere(['id_stato' => $this->statiAperti()])->count();

        $recenti = (clone $query)->orderBy(['id' => SORT_DESC])->limit(10)->all();

        return $this->render('index', [
            'totale' => $totale,
            'aperti' => $aperti,
            'recenti' => $recenti,
            'documenti' => $this->conteggioDocumenti(),
            'scadenze' => $this->conteggioScadenze(),
        ]);
    }

    public function actionRichieste()
    {
        $get = Yii::$app->request->get();

        $query = MgCrmTicket::queryEsterno(Yii::$app->user->id)
            ->with(['tipo', 'stato', 'priorita', 'anagrafica'])
            ->orderBy(['id' => SORT_DESC]);

        if (!empty($get['id_stato'])) {
            $query->andWhere(['id_stato' => (int) $get['id_stato']]);
        }
        if (!empty($get['id_tipo'])) {
            $query->andWhere(['id_tipo' => (int) $get['id_tipo']]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->render('richieste', [
            'dataProvider' => $dataProvider,
            'filtri' => $get,
            'tipi' => MgCrmTipo::mapAttivi(),
            'stati' => MgCrmStato::mapAttivi(),
        ]);
    }

    public function actionRichiesta($id)
    {
        $model = $this->findTicket($id);

        return $this->render('richiesta', [
            'model' => $model,
            'messaggio' => new MgCrmTicketMessaggio(),
        ]);
    }

    public function actionRichiestaCreate()
    {
        $model = new MgCrmTicket();
        $model->id_tipo = $this->idByCodice(MgCrmTipo::className(), 'assistenza');
        $model->id_stato = $this->idByCodice(MgCrmStato::className(), 'nuovo');
        $model->id_priorita = $this->idByCodice(MgCrmPriorita::className(), 'normale');
        $model->id_utente = Yii::$app->user->id;

        if ($model->load(Yii::$app->request->post())) {
            $model->id_utente = Yii::$app->user->id;
            $model->id_stato = $this->idByCodice(MgCrmStato::className(), 'nuovo');
            if ($model->validate() && $this->anagraficaConsentita($model->id_anagrafica) && $model->save(false)) {
                AllegatiCrm::salvaMultipli($model->id, null, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', 'Richiesta ' . $model->etichetta . ' inviata.');
                return $this->redirect(['richiesta', 'id' => $model->id]);
            }
            if (!$this->anagraficaConsentita($model->id_anagrafica)) {
                $model->addError('id_anagrafica', 'Azienda non valida.');
            }
        }

        return $this->render('richiesta_create', [
            'model' => $model,
            'tipi' => MgCrmTipo::mapAttivi(),
            'anagrafiche' => $this->anagraficheUtente(),
            'articoli' => MgArticolo::map(),
            'matricole' => MgMatricola::mapAttivi(),
            'documenti' => $this->documentiUtente(),
        ]);
    }

    public function actionRichiestaRispondi($id)
    {
        $model = $this->findTicket($id);

        $messaggio = new MgCrmTicketMessaggio();
        $messaggio->id_ticket = $model->id;
        $messaggio->user_id = Yii::$app->user->id;
        $messaggio->is_nota_interna = 0;

        if ($messaggio->load(Yii::$app->request->post())) {
            $messaggio->is_nota_interna = 0;
            $messaggio->is_soluzione = 0;
            if ($messaggio->save()) {
                AllegatiCrm::salvaMultipli($model->id, $messaggio->id, Yii::$app->user->id);
                Yii::$app->session->setFlash('success', 'Risposta inviata.');
            }
        }

        return $this->redirect(['richiesta', 'id' => $model->id]);
    }

    /**
     * Scarica/visualizza un allegato di un ticket visibile all'utente.
     */
    public function actionAllegato($id, $download = 0)
    {
        $allegato = MgCrmAllegato::findOne($id);
        if ($allegato === null) {
            throw new NotFoundHttpException('Allegato non trovato.');
        }

        $idTicket = $allegato->id_ticket;
        if (!$idTicket && $allegato->messaggio) {
            $idTicket = $allegato->messaggio->id_ticket;
        }
        $consentito = $idTicket && MgCrmTicket::queryEsterno(Yii::$app->user->id)
            ->andWhere(['id' => (int) $idTicket])->exists();
        if (!$consentito) {
            throw new ForbiddenHttpException('Allegato non accessibile.');
        }

        AllegatiCrm::invia($allegato, (bool) $download);
    }

    public function actionDocumenti()
    {
        $get = Yii::$app->request->get();
        $ids = $this->idsAnagrafiche();

        $query = MgDocumento::find()
            ->with(['tipo', 'anagrafica'])
            ->where($ids ? ['id_anagrafica' => $ids] : '1=0')
            ->orderBy(['data' => SORT_DESC, 'id' => SORT_DESC]);

        if (!empty($get['id_anagrafica']) && in_array((int) $get['id_anagrafica'], $ids, true)) {
            $query->andWhere(['id_anagrafica' => (int) $get['id_anagrafica']]);
        }
        if (!empty($get['q'])) {
            $query->andWhere(['like', 'descrizione', trim($get['q'])]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->render('documenti', [
            'dataProvider' => $dataProvider,
            'filtri' => $get,
            'anagrafiche' => $this->anagraficheUtente(),
        ]);
    }

    public function actionDocumento($id)
    {
        $model = MgDocumento::find()->with(['righe.articolo', 'tipo', 'anagrafica'])->where(['id' => (int) $id])->one();
        if ($model === null) {
            throw new NotFoundHttpException('Documento non trovato.');
        }
        if (!$this->anagraficaConsentita($model->id_anagrafica)) {
            throw new ForbiddenHttpException('Documento non accessibile.');
        }

        return $this->render('documento', [
            'model' => $model,
        ]);
    }

    public function actionScadenze()
    {
        $ids = $this->idsAnagrafiche();
        $dataProvider = new ActiveDataProvider([
            'query' => \app\models\MgScadenza::find()
                ->alias('s')
                ->innerJoin('mg_documento d', 'd.id = s.id_documento')
                ->with(['documento.anagrafica', 'documento.tipo'])
                ->where($ids ? ['d.id_anagrafica' => $ids] : '1=0')
                ->orderBy(['s.data_scadenza' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('scadenze', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    /**
     * Id delle anagrafiche collegate all'utente corrente.
     */
    private function idsAnagrafiche()
    {
        return MgCrmUtenteAnagrafica::idsAnagraficheDiUtente(Yii::$app->user->id);
    }

    /**
     * Tendina id => ragione sociale delle sole aziende dell'utente.
     */
    private function anagraficheUtente()
    {
        $ids = $this->idsAnagrafiche();
        if (empty($ids)) {
            return [];
        }
        return \yii\helpers\ArrayHelper::map(
            MgAnagrafica::find()->where(['id' => $ids])->orderBy(['ragione_sociale' => SORT_ASC])->all(),
            'id',
            'ragione_sociale'
        );
    }

    /**
     * Tendina documenti (id => etichetta) delle aziende dell'utente.
     */
    private function documentiUtente()
    {
        $ids = $this->idsAnagrafiche();
        if (empty($ids)) {
            return [];
        }
        $out = [];
        foreach (MgDocumento::find()->where(['id_anagrafica' => $ids])
                     ->orderBy(['data' => SORT_DESC])->limit(200)->all() as $d) {
            $out[$d->id] = $d->etichetta;
        }
        return $out;
    }

    private function conteggioDocumenti()
    {
        $ids = $this->idsAnagrafiche();
        return $ids ? (int) MgDocumento::find()->where(['id_anagrafica' => $ids])->count() : 0;
    }

    private function conteggioScadenze()
    {
        $ids = $this->idsAnagrafiche();
        if (empty($ids)) {
            return 0;
        }
        return (int) \app\models\MgScadenza::find()
            ->innerJoin('mg_documento d', 'd.id = mg_scadenza.id_documento')
            ->where(['d.id_anagrafica' => $ids])
            ->count();
    }

    private function anagraficaConsentita($idAnagrafica)
    {
        return in_array((int) $idAnagrafica, $this->idsAnagrafiche(), true);
    }

    private function findTicket($id)
    {
        $model = MgCrmTicket::queryEsterno(Yii::$app->user->id)
            ->with(['tipo', 'stato', 'priorita', 'anagrafica', 'articolo', 'matricola', 'documento'])
            ->where(['id' => (int) $id])
            ->one();

        if ($model === null) {
            throw new NotFoundHttpException('Richiesta non trovata.');
        }
        return $model;
    }

    private function statiAperti()
    {
        return array_map('intval', MgCrmStato::find()->select('id')->where(['is_chiuso' => 0])->column());
    }

    private function idByCodice($class, $codice)
    {
        $row = $class::find()->select('id')->where(['codice' => $codice])->scalar();
        return $row ? (int) $row : null;
    }
}
