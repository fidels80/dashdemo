<?php

namespace app\controllers;

use Yii;
use app\models\MgAnagrafica;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD anagrafica clienti/fornitori del microgestionale.
 */
class MganagraficaController extends Controller
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
            'query' => MgAnagrafica::find()->orderBy(['ragione_sociale' => SORT_ASC]),
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
        $model = new MgAnagrafica();
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Creazione rapida di un'anagrafica (AJAX), usata dalla form documenti.
     */
    public function actionCreateAjax()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        if (!Yii::$app->request->isPost) {
            return ['success' => false, 'error' => 'Richiesta non valida.'];
        }

        $model = new MgAnagrafica();
        $model->attivo = true;
        $model->load(Yii::$app->request->post(), '');

        if ($model->save()) {
            return [
                'success' => true,
                'anagrafica' => [
                    'id' => (int) $model->id,
                    'codice' => $model->codice,
                    'ragione_sociale' => $model->ragione_sociale,
                    'partita_iva' => $model->partita_iva,
                ],
            ];
        }

        return ['success' => false, 'errors' => $model->getErrors()];
    }

    protected function findModel($id)
    {
        if (($model = MgAnagrafica::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Anagrafica non trovata.');
    }
}
