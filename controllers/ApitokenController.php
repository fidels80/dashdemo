<?php

namespace app\controllers;

use Yii;
use app\models\ApiToken;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Gestione dei token Bearer per il servizio REST.
 */
class ApitokenController extends Controller
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
            'query' => ApiToken::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 50],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new ApiToken();
        $descrizione = Yii::$app->request->post('descrizione');
        $scopes = Yii::$app->request->post('scopes', '*');
        $scadenza = Yii::$app->request->post('scadenza');

        if (Yii::$app->request->isPost) {
            $expiresAt = null;
            if (!empty($scadenza)) {
                $expiresAt = strtotime($scadenza . ' 23:59:59');
            }

            $plain = ApiToken::generate(
                $descrizione ?: 'Token API',
                Yii::$app->user->id,
                $expiresAt,
                $scopes ?: '*'
            );

            if ($plain) {
                Yii::$app->session->setFlash('token_generato', $plain);
                return $this->redirect(['index']);
            }
            Yii::$app->session->setFlash('error', 'Impossibile generare il token.');
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Token revocato.');
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = ApiToken::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Token non trovato.');
    }
}
