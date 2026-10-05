<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tessuti array */
/* @var $tessutiSelezionati array */
/* @var $form yii\bootstrap4\ActiveForm */
?>
<div class="mgmodello-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3"><?= $form->field($model, 'codice')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'attivo')->checkbox() ?></div>
    </div>

    <div class="form-group">
        <label>Tessuti associati</label>
        <?php if (empty($tessuti)): ?>
            <div class="text-muted">Nessun tessuto disponibile. <?= Html::a('Crea un tessuto', ['mgattributo/create', 'tipo' => 'tessuto']) ?>.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($tessuti as $id => $label): ?>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="tessuti[]" value="<?= (int) $id ?>"
                                   id="tessuto-<?= (int) $id ?>" <?= in_array((int) $id, $tessutiSelezionati, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="tessuto-<?= (int) $id ?>"><?= Html::encode($label) ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-muted small mt-1">I tessuti selezionati sono proposti dal Wizard prodotti per generare le varianti dell'articolo.</div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', $model->isNewRecord ? ['index'] : ['view', 'id' => $model->id], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
