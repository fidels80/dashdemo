<?php

namespace app\controllers;

use Yii;
use app\models\MgMagazzino;
use app\models\MgTipoDocumento;
use app\models\MgDocumentoRiga;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD dell'anagrafica dei magazzini (codice, descrizione, anagrafica).
 */
class MgmagazzinoController extends Controller
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
            'query' => MgMagazzino::find()->with('anagrafica')->orderBy(['codice' => SORT_ASC]),
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
        $model = new MgMagazzino();
        $model->attivo = true;

        if ($from !== null && ($source = MgMagazzino::findOne((int) $from)) !== null) {
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

        if (MgTipoDocumento::find()->where(['id_magazzino_partenza' => $id])->exists()
            || MgTipoDocumento::find()->where(['id_magazzino_arrivo' => $id])->exists()
            || MgDocumentoRiga::find()->where(['id_magazzino_partenza' => $id])->exists()
            || MgDocumentoRiga::find()->where(['id_magazzino_arrivo' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare il magazzino: è utilizzato da tipi documento o righe documento.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgMagazzino::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Magazzino non trovato.');
    }
}
