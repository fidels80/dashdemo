<?php

namespace app\controllers;

use Yii;
use app\components\AccessControl;
use app\components\Licensing;
use app\models\DashLicenzaLog;
use yii\web\Controller;

/**
 * Gestione licenza e aggiornamenti (riservata ai supervisori).
 */
class LicenzaController extends Controller
{
    public function beforeAction($action)
    {
        if (!AccessControl::isSuper(Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('error', 'Funzione riservata agli amministratori.');
            Yii::$app->response->redirect(['site/index'])->send();
            return false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        return $this->render('index', [
            'summary' => Licensing::summary(),
            'serverUrl' => Licensing::serverUrl(),
            'appVersion' => Licensing::appVersion(),
        ]);
    }

    public function actionActivate()
    {
        $key = trim((string) Yii::$app->request->post('license_key', ''));
        $res = Licensing::activate($key);

        Yii::$app->session->setFlash($res['ok'] ? 'success' : 'error', $res['message']);
        return $this->redirect(['index']);
    }

    public function actionHeartbeat()
    {
        $res = Licensing::heartbeat();
        Yii::$app->session->setFlash($res['ok'] ? 'success' : 'error', $res['message']);
        return $this->redirect(['index']);
    }

    public function actionCheckUpdate()
    {
        $res = Licensing::checkUpdate();
        if (!$res['ok']) {
            Yii::$app->session->setFlash('error', $res['message']);
        } elseif (!empty($res['data']['update_available'])) {
            Yii::$app->session->setFlash('success', 'Aggiornamento disponibile: v' . $res['data']['latest_version']);
        } else {
            Yii::$app->session->setFlash('info', 'Nessun aggiornamento disponibile.');
        }
        return $this->redirect(['index']);
    }

    public function actionDownload()
    {
        $version = trim((string) Yii::$app->request->post('version', ''));
        $res = Licensing::downloadUpdate($version);

        if ($res['ok']) {
            Yii::$app->session->setFlash('success', $res['message'] . '. File: ' . $res['path']);
        } else {
            Yii::$app->session->setFlash('error', $res['message']);
        }
        return $this->redirect(['index']);
    }
}
