<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$resetLink = Yii::$app->urlManager
    ->createAbsoluteUrl(['user/resetp', 'token' => $user->password_reset_token]);
//->createUrl(['user/resetp', 'token' => $user->password_reset_token]);

?>
<head></head><body lang=IT link='#0563C1' vlink='#954F72' >
<div class="password-reset">
    <p>Benvenuto  <?=Html::encode($user->username)?>,</p>

    <p>A breve riceverai una notifica di attivazione</p>

    <p> </p>

</div>
<img src="<?php echo Url::base(true).Yii::getAlias('@web') . '/uploads/login_logo.jpg' ?>
"
 style=width:304px;height:228px>