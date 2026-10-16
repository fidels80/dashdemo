<?php

namespace app\controllers;

use Yii;
use app\models\MgCrmFaq;
use app\models\MgArticolo;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Catalogo FAQ generate dai ticket CRM.
 */
class CrmfaqController extends Controller
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
        $get = Yii::$app->request->get();

        $query = MgCrmFaq::find()
            ->with(['articolo', 'ticket'])
            ->orderBy(['id' => SORT_DESC]);

        if (!empty($get['q'])) {
            $q = trim($get['q']);
            $query->andWhere(['or',
                ['like', 'domanda', $q],
                ['like', 'soluzione', $q],
                ['like', 'codice_articolo', $q],
            ]);
        }
        if (isset($get['attivo']) && $get['attivo'] !== '') {
            $query->andWhere(['attivo' => (int) $get['attivo']]);
        }

        return $this->render('index', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $query,
                'pagination' => false,
            ]),
            'filtri' => $get,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate($id_ticket = null, $id_articolo = null)
    {
        $model = new MgCrmFaq();
        if ($id_ticket) {
            $model->id_ticket = (int) $id_ticket;
        }
        if ($id_articolo) {
            $model->id_articolo = (int) $id_articolo;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'articoli' => MgArticolo::map(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'articoli' => MgArticolo::map(),
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = MgCrmFaq::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('FAQ non trovata.');
    }
}
