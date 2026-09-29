<?php

namespace app\controllers;

use Yii;
use app\models\MgAnagrafica;
use app\models\MgCommessa;
use app\models\MgSottocommessa;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD delle sottocommesse del microgestionale (collegate a una commessa padre).
 */
class MgsottocommessaController extends Controller
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
            'query' => MgSottocommessa::find()
                ->with(['commessa', 'anagrafica'])
                ->orderBy(['codice' => SORT_ASC]),
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
        $model = new MgSottocommessa();
        $model->attivo = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([ 'index' ]);
        }

        return $this->render('create', [
            'model' => $model,
            'commesse' => $this->commesse(),
            'anagrafiche' => $this->anagrafiche(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([ 'index' ]);
        }

        return $this->render('update', [
            'model' => $model,
            'commesse' => $this->commesse(),
            'anagrafiche' => $this->anagrafiche(),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgSottocommessa::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Sottocommessa non trovata.');
    }

    private function commesse()
    {
        return \yii\helpers\ArrayHelper::map(
            MgCommessa::find()->where(['attivo' => 1])->orderBy(['codice' => SORT_ASC])->all(),
            'id',
            function ($c) {
                return $c->codice . ' - ' . $c->descrizione;
            }
        );
    }

    private function anagrafiche()
    {
        return \yii\helpers\ArrayHelper::map(
            MgAnagrafica::find()->orderBy(['ragione_sociale' => SORT_ASC])->all(),
            'id',
            function ($a) {
                return $a->codice . ' - ' . $a->ragione_sociale;
            }
        );
    }
}
