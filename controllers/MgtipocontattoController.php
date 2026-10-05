<?php

namespace app\controllers;

use Yii;
use app\models\MgTipoContatto;
use app\models\MgAnagraficaContatto;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD dei tipi di contatto delle anagrafiche (email, PEC, cellulare, ...).
 */
class MgtipocontattoController extends Controller
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
            'query' => MgTipoContatto::find()->orderBy(['ordine' => SORT_ASC, 'descrizione' => SORT_ASC]),
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
        $model = new MgTipoContatto();
        $model->attivo = true;
        $model->ordine = 0;

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

        if (MgAnagraficaContatto::find()->where(['id_tipo_contatto' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare il tipo di contatto: è utilizzato da una o più anagrafiche.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgTipoContatto::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Tipo contatto non trovato.');
    }
}
