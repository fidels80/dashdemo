<?php

namespace app\controllers;

use Yii;
use app\models\MgAnagrafica;
use app\models\MgAnagraficaContatto;
use app\models\MgTipoContatto;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * Elenco globale dei contatti delle anagrafiche, con ricerca e modifica.
 */
class MgcontattoController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-ajax' => ['POST'],
                    'save-ajax' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = trim((string) Yii::$app->request->get('q'));
        $idTipo = Yii::$app->request->get('id_tipo_contatto');
        $idAnagrafica = Yii::$app->request->get('id_anagrafica');

        $query = MgAnagraficaContatto::find()
            ->alias('c')
            ->with(['anagrafica', 'tipoContatto'])
            ->joinWith(['anagrafica a', 'tipoContatto t'])
            ->orderBy(['a.ragione_sociale' => SORT_ASC, 't.ordine' => SORT_ASC, 'c.valore' => SORT_ASC]);

        if ($q !== '') {
            $query->andWhere(['or',
                ['like', 'c.valore', $q],
                ['like', 'c.etichetta', $q],
                ['like', 'a.codice', $q],
                ['like', 'a.ragione_sociale', $q],
            ]);
        }
        if (!empty($idTipo)) {
            $query->andWhere(['c.id_tipo_contatto' => (int) $idTipo]);
        }
        if (!empty($idAnagrafica)) {
            $query->andWhere(['c.id_anagrafica' => (int) $idAnagrafica]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'tipi' => MgTipoContatto::map(),
            'anagrafiche' => MgAnagrafica::map(),
            'filtroQ' => $q,
            'filtroTipo' => $idTipo,
            'filtroAnagrafica' => $idAnagrafica,
        ]);
    }

    /**
     * Salvataggio di un contatto (AJAX: crea o modifica).
     */
    public function actionSaveAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $id = Yii::$app->request->post('id');
        if ($id) {
            $model = MgAnagraficaContatto::findOne($id);
            if ($model === null) {
                return ['success' => false, 'error' => 'Contatto non trovato.'];
            }
        } else {
            $model = new MgAnagraficaContatto();
            $model->attivo = true;
        }

        $model->load(Yii::$app->request->post());

        if ($model->save()) {
            if ($model->predefinito) {
                MgAnagraficaContatto::updateAll(
                    ['predefinito' => 0],
                    [
                        'and',
                        ['id_anagrafica' => $model->id_anagrafica],
                        ['id_tipo_contatto' => $model->id_tipo_contatto],
                        ['not', ['id' => $model->id]],
                    ]
                );
            }
            return ['success' => true, 'id' => (int) $model->id];
        }

        $errors = [];
        foreach ($model->getErrors() as $attribute => $messages) {
            $errors[] = implode(' ', $messages);
        }
        return ['success' => false, 'errors' => $errors];
    }

    /**
     * Eliminazione di un contatto (AJAX).
     */
    public function actionDeleteAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = MgAnagraficaContatto::findOne(Yii::$app->request->post('id'));
        if ($model === null) {
            return ['success' => false, 'error' => 'Contatto non trovato.'];
        }

        $model->delete();
        return ['success' => true];
    }

    /**
     * Eliminazione classica (POST), usata dall'elenco come fallback.
     */
    public function actionDelete($id)
    {
        $model = MgAnagraficaContatto::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Contatto non trovato.');
        }
        $model->delete();
        return $this->redirect(['index']);
    }
}
