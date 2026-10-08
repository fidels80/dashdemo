<?php

namespace app\controllers;

use Yii;
use app\models\MgAliquotaIva;
use app\models\MgArticolo;
use app\models\MgAnagrafica;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD delle aliquote IVA (codice, descrizione, percentuale).
 */
class MgaliquotaivaController extends Controller
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
            'query' => MgAliquotaIva::find()->orderBy(['percentuale' => SORT_DESC, 'codice' => SORT_ASC]),
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
        $model = new MgAliquotaIva();
        $model->attivo = true;

        if ($from !== null && ($source = MgAliquotaIva::findOne((int) $from)) !== null) {
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

        $inUso = MgArticolo::find()
                ->where(['id_iva_vendita' => $id])
                ->orWhere(['id_iva_acquisto' => $id])
                ->exists()
            || MgAnagrafica::find()->where(['id_aliquota_iva' => $id])->exists();
        if ($inUso) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare l\'aliquota: è utilizzata da articoli o anagrafiche.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgAliquotaIva::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Aliquota IVA non trovata.');
    }
}
