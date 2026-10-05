<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgTipoContatto */
/* @var $form yii\bootstrap4\ActiveForm */

$icone = [
    'fas fa-envelope', 'fas fa-certificate', 'fas fa-mobile-alt', 'fas fa-phone',
    'fas fa-phone-alt', 'fas fa-fax', 'fas fa-globe', 'fas fa-address-card',
    'fas fa-comment', 'fas fa-comments', 'fab fa-linkedin', 'fab fa-skype',
    'fab fa-discord', 'fab fa-whatsapp', 'fab fa-telegram', 'fab fa-facebook',
    'fab fa-instagram', 'fab fa-x-twitter', 'fab fa-github', 'fab fa-microsoft',
];
?>
<div class="mgtipocontatto-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'ordine')->input('number') ?></div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'icona')->textInput([
                'maxlength' => true,
                'list' => 'icone-contatto',
                'placeholder' => 'es. fas fa-envelope',
            ])->hint('Classe Font Awesome mostrata accanto al contatto.') ?>
            <datalist id="icone-contatto">
                <?php foreach ($icone as $i): ?>
                    <option value="<?= Html::encode($i) ?>"></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div class="col-md-2"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
