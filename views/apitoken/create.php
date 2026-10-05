<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiToken */
/* @var $entita app\models\DashApiEntita[] */
/* @var $operazioni array<string, string> */
/* @var $selezionati array<string, string[]> */

$this->title = 'Nuovo token API';
$this->params['breadcrumbs'][] = ['label' => 'Token API', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apitoken-create">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Descrizione</label>
                <input type="text" name="descrizione" class="form-control" required
                       placeholder="es. Integrazione ERP sola lettura">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Scadenza (opzionale)</label>
                <input type="date" name="scadenza" class="form-control">
            </div>
        </div>
    </div>

    <?= $this->render('_permessi', [
        'entita' => $entita,
        'operazioni' => $operazioni,
        'selezionati' => $selezionati,
    ]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="fas fa-key"></i> Genera token', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
