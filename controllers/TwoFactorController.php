<?php

namespace app\controllers;

use app\models\TwoFactorForm;
use app\models\User;
use app\models\UserTrustedDevice;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;

class TwoFactorController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['verify'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Verifica del codice 2FA durante il login.
     */
    public function actionVerify()
    {
        $loginUrl = Url::to(['site/login']);

        if (!Yii::$app->session->has('pending_2fa_user_id')) {
            return $this->redirect($loginUrl);
        }

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['planning/index']);
        }

        $model = new TwoFactorForm();

        if ($model->load(Yii::$app->request->post()) && $model->verify()) {
            return $this->redirect(['planning/index']);
        }

        return $this->render('verify', [
            'model' => $model,
        ]);
    }

    public function actionSetup()
    {
        $user = Yii::$app->user->identity;

        if ($user->isTwoFactorEnabled()) {
            $trustedCount = UserTrustedDevice::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['>=', 'expires_at', time()])
                ->count();

            return $this->render('setup_active', [
                'model' => $user,
                'trustedCount' => (int) $trustedCount,
            ]);
        }

        $secret = $user->generateTwoFactorSecret();
        $qrCodeUrl = $user->getTwoFactorQRCodeUrl();

        return $this->render('setup', [
            'model' => $user,
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    public function actionEnable()
    {
        $user = Yii::$app->user->identity;
        $code = Yii::$app->request->post('code');

        if (empty($code)) {
            Yii::$app->session->setFlash('error', 'Inserisci il codice di verifica.');
            return $this->redirect(['two-factor/setup']);
        }

        if ($user->enableTwoFactor($code)) {
            Yii::$app->session->setFlash('success',
                'Autenticazione a due fattori attivata con successo!');
            return $this->redirect(['two-factor/setup']);
        }

        Yii::$app->session->setFlash('error',
            'Codice non valido. Assicurati di aver scansionato il QR code correttamente.');
        return $this->redirect(['two-factor/setup']);
    }

    public function actionDisable()
    {
        $user = Yii::$app->user->identity;
        $password = Yii::$app->request->post('password');

        if (empty($password)) {
            Yii::$app->session->setFlash('error', 'Inserisci la password per disattivare la 2FA.');
            return $this->redirect(['two-factor/setup']);
        }

        if (!$user->validatePassword($password)) {
            Yii::$app->session->setFlash('error', 'Password non corretta.');
            return $this->redirect(['two-factor/setup']);
        }

        $user->disableTwoFactor();
        Yii::$app->session->setFlash('success', '2FA disattivata con successo.');
        return $this->redirect(['two-factor/setup']);
    }

    /**
     * Revoca tutti i dispositivi fidati dell'utente.
     */
    public function actionRevokeTrustedDevices()
    {
        $user = Yii::$app->user->identity;

        UserTrustedDevice::revokeAllForUser($user->id);

        // Elimina anche il cookie del dispositivo corrente
        Yii::$app->response->cookies->remove(new \yii\web\Cookie([
            'name' => 'dash_trusted_device',
        ]));

        Yii::$app->session->setFlash('success',
            'Tutti i dispositivi fidati sono stati revocati.');
        return $this->redirect(['two-factor/setup']);
    }
}
