<?php

namespace app\controllers;

use Yii;
use app\models\MgLotto;
use app\models\MgDocumentoRigaDettaglio;
use app\models\MgMovimentoMagazzino;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD dell'anagrafica lotti.
 */
class MglottoController extends Controller
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
            'query' => MgLotto::find()->with('articolo')->orderBy(['codice_lotto' => SORT_ASC]),
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
        $model = new MgLotto();

        if ($from !== null && ($source = MgLotto::findOne((int) $from)) !== null) {
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

        if (MgDocumentoRigaDettaglio::find()->where(['id_lotto' => $id])->exists()
            || MgMovimentoMagazzino::find()->where(['id_lotto' => $id])->exists()) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare il lotto: è utilizzato in righe documento o movimenti di magazzino.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgLotto::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Lotto non trovato.');
    }
}
