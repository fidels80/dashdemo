<?php

namespace app\controllers;

use Yii;
use app\models\DashChangelog;
use app\components\AccessControl;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Changelog applicativo: pagina di log con tutto cio' che e' stato modificato
 * e in che modo. Visibile esclusivamente agli operatori di livello elevato
 * (livello 100 o email in params['superEmails']).
 */
class DashchangelogController extends Controller
{
    /**
     * Doppia barriera: oltre al controllo ACL globale (default deny),
     * qui verifichiamo esplicitamente che l'utente sia un supervisore.
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $user = Yii::$app->user->identity;
        if (Yii::$app->user->isGuest || !AccessControl::isSuper($user)) {
            Yii::$app->session->setFlash('error', 'Pagina riservata agli operatori di livello elevato.');
            Yii::$app->response->redirect(['site/index'])->send();
            return false;
        }

        return true;
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DashChangelog::find()
                ->orderBy(['data_commit' => SORT_DESC, 'id' => SORT_DESC]),
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

    protected function findModel($id)
    {
        if (($model = DashChangelog::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Voce di changelog non trovata.');
    }
}
