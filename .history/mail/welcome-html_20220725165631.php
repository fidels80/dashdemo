<?php

use yii\helpers\Html;

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
<img width="1" height="1" src="https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE.png" class="attachment-large size-large" alt="" loading="lazy" srcset="https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE.png 270w, https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE-260x38.png 260w, https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE-50x7.png 50w, https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE-150x22.png 150w"
 sizes="(max-width: 270px) 100vw, 270px"<br><br>
 <img src="https://www.vivendasrl.it/wp-content/uploads/2020/09/VIVENDA-RS-VETTORIALE-260x38.png"
 style=width:304px;height:228px>