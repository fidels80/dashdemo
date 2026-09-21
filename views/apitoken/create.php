<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiToken */

$this->title = 'Nuovo token API';
$this->params['breadcrumbs'][] = ['label' => 'Token API', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apitoken-create">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Descrizione</label>
                <input type="text" name="descrizione" class="form-control" placeholder="es. Integrazione ERP">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Permessi (scopes)</label>
                <input type="text" name="scopes" class="form-control" value="*">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Scadenza (opzionale)</label>
                <input type="date" name="scadenza" class="form-control">
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-key"></i> Genera token', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
