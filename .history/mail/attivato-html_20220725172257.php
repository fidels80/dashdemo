<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\User $user */
 
?>
<head></head><body lang=IT link='#0563C1' vlink='#954F72' >
<div class="password-reset">
    <p>Benvenuto  <?=Html::encode($user->username)?>,</p>

    <p>la tua utenza è stata attivata accedi al potarle per cominciare!!</p>

    <?php echo Url::base(true);
?>
    <p> </p>

</div>

 <img src="<?php echo Url::base(true).Yii::getAlias('@web') . '/uploads/logomail.jpg' ?>
"
 style=width:304px;height:228px>