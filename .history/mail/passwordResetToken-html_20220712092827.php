<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

$resetLink = Yii::$app->urlManager
->createAbsoluteUrl(['user/resetp', 'token' => $user->password_reset_token]);
//->createUrl(['user/resetp', 'token' => $user->password_reset_token]);

?>
<div class="password-reset">
    <p>Ciao  <?=Html::encode($user->username)?>,</p>

    <p>Clicca sul link per andare a reimpostare la password</p>

    <p><?=Html::a(Html::encode($resetLink), $resetLink)?></p>
</div>