<?php

namespace app\controllers;

use Yii;
use app\models\MgTipoDocumento;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD per l'anagrafica dei tipi documento.
 */
class MgtipodocumentoController extends Controller
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
            'query' => MgTipoDocumento::find()
                ->with('magazzinoPartenza', 'magazzinoArrivo')
                ->orderBy(['codice' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate($from = null)
    {
        $model = new MgTipoDocumento();
        $model->anno = (int) date('Y');
        $model->usa_progressivo = true;
        $model->congruita = true;
        $model->attivo = true;
        $model->destinazione = MgTipoDocumento::DEST_CLIENTE;
        $model->fe_tipo_documento = 'TD01';
        $model->fe_regime_fiscale = 'RF01';
        $model->fe_divisa = 'EUR';
        $model->fe_condizioni_pagamento = 'TP02';
        $model->fe_modalita_pagamento = 'MP05';
        $model->fe_esigibilita_iva = 'I';

        if ($from !== null && ($source = MgTipoDocumento::findOne((int) $from)) !== null) {
            $model = \app\components\Duplicate::copy($source);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = new \yii\db\Expression('GETDATE()');
            if ($model->save()) {
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([ 'index' ]);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgTipoDocumento::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Tipo documento non trovato.');
    }
}
