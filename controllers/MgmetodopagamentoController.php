<?php

namespace app\controllers;

use Yii;
use app\models\MgMetodoPagamento;
use app\models\MgMetodoPagamentoRata;
use app\models\MgTipoPagamento;
use app\models\MgDocumento;
use app\models\MgScadenza;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CRUD dei metodi di pagamento (tabellati) con le relative rate.
 */
class MgmetodopagamentoController extends Controller
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
            'query' => MgMetodoPagamento::find()->orderBy(['descrizione' => SORT_ASC]),
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
        $model = new MgMetodoPagamento();
        $model->attivo = true;
        $model->partenza = MgMetodoPagamento::PARTENZA_EMISSIONE;

        $ratePost = Yii::$app->request->post('rate', []);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $err = $this->validaRate($ratePost);
            if ($err !== null) {
                $model->addError('n_rate', $err);
            } else {
                $model->created_at = new \yii\db\Expression('GETDATE()');
                if ($model->save(false)) {
                    $this->saveRate($model, $ratePost);
                    return $this->redirect([ 'index' ]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'tipi' => MgTipoPagamento::mapAttivi(),
            'rate' => [],
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $ratePost = Yii::$app->request->post('rate', []);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $err = $this->validaRate($ratePost);
            if ($err !== null) {
                $model->addError('n_rate', $err);
            } elseif ($model->save(false)) {
                $this->saveRate($model, $ratePost);
                return $this->redirect([ 'index' ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'tipi' => MgTipoPagamento::mapAttivi(),
            'rate' => $model->rate,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        $inUso = MgDocumento::find()->where(['id_metodo_pagamento' => $id])->exists()
            || MgScadenza::find()->where(['id_metodo_pagamento' => $id])->exists();
        if ($inUso) {
            Yii::$app->session->setFlash('error',
                'Impossibile eliminare il metodo: è utilizzato da documenti o scadenze.');
            return $this->redirect(['view', 'id' => $id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Valida le rate: almeno una, percentuali non negative e totale non superiore a 100%.
     *
     * @return string|null messaggio di errore oppure null se valide
     */
    private function validaRate($rate)
    {
        $totale = 0.0;
        $n = 0;
        foreach ((array) $rate as $r) {
            if (!isset($r['percentuale']) || $r['percentuale'] === '') {
                continue;
            }
            $p = (float) $r['percentuale'];
            if ($p < 0) {
                return 'Le percentuali delle rate non possono essere negative.';
            }
            $totale += $p;
            $n++;
        }

        if ($n === 0) {
            return 'Inserire almeno una rata con la relativa percentuale.';
        }
        if ($totale > 100.0001) {
            return 'Il totale delle percentuali delle rate non può superare il 100% (attuale: '
                . number_format($totale, 2, ',', '.') . '%).';
        }
        return null;
    }

    /**
     * Salva le rate del metodo (progressivo, giorni, percentuale) e allinea n_rate.
     */
    private function saveRate($model, $rate)
    {
        MgMetodoPagamentoRata::deleteAll(['id_metodo' => $model->id]);

        $progressivo = 0;
        foreach ((array) $rate as $r) {
            $progressivo++;
            $rata = new MgMetodoPagamentoRata();
            $rata->id_metodo = $model->id;
            $rata->progressivo = $progressivo;
            $rata->giorni = isset($r['giorni']) && $r['giorni'] !== '' ? (int) $r['giorni'] : 0;
            $rata->percentuale = isset($r['percentuale']) ? (float) $r['percentuale'] : 0;
            $rata->save(false);
        }

        $model->n_rate = $progressivo;
        $model->save(false, ['n_rate']);
    }

    protected function findModel($id)
    {
        if (($model = MgMetodoPagamento::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Metodo di pagamento non trovato.');
    }
}
