<?php

namespace app\controllers;

use Yii;
use app\models\MgMatricola;
use app\models\MgDocumentoRigaDettaglio;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD dell'anagrafica matricole / numeri di serie.
 */
class MgmatricolaController extends Controller
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
            'query' => MgMatricola::find()->with('articolo')->orderBy(['matricola' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate($from = null)
    {
        $model = new MgMatricola();
        $model->attivo = true;

        if ($from !== null && ($source = MgMatricola::findOne((int) $from)) !== null) {
            $model = \app\components\Duplicate::copy($source);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (MgDocumentoRigaDettaglio::find()->where(['id_matricola' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare la matricola: è utilizzata in righe documento.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgMatricola::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Matricola non trovata.');
    }
}
