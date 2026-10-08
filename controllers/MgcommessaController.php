<?php

namespace app\controllers;

use Yii;
use app\models\MgAnagrafica;
use app\models\MgCommessa;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgSottocommessa;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD delle commesse del microgestionale.
 */
class MgcommessaController extends Controller
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
            'query' => MgCommessa::find()
                ->with('sottocommesse')
                ->joinWith('anagrafica')
                ->orderBy(['mg_commessa.codice' => SORT_ASC]),
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
        $model = new MgCommessa();
        $model->attivo = true;

        if ($from !== null && ($source = MgCommessa::findOne((int) $from)) !== null) {
            $model = \app\components\Duplicate::copy($source);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([ 'index' ]);
        }

        return $this->render('create', [
            'model' => $model,
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
            'anagrafiche' => $this->anagrafiche(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $ids = MgSottocommessa::find()->select('id')->where(['id_commessa' => $model->id])->column();
        if (!empty($ids)) {
            $usata = MgDocumento::find()->where(['id_sottocommessa' => $ids])->exists()
                || MgDocumentoRiga::find()->where(['id_sottocommessa' => $ids])->exists();
            if ($usata) {
                Yii::$app->session->setFlash('error',
                    'La commessa ha sottocommesse usate in documenti: non è possibile eliminarla.');
                return $this->redirect(['index']);
            }
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgCommessa::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Commessa non trovata.');
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
