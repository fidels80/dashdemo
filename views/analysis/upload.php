<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>

<div class="analysis-upload">
    <h1>Analisi Documenti Forestali</h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
    <?= $form->field($model, 'pdfFile')->fileInput() ?>
    <?= $form->field($model, 'testoDocumento')->textarea(['rows' => 10, 'placeholder' => 'Incolla qui il testo estratto dal PDF...']) ?>
    <div class="form-group">
        <?= Html::submitButton('Analizza con IA', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end() ?>

    <hr>

    <?php if ($risultato): ?>
        <div class="alert alert-info">
            <h3>Esito Analisi IA:</h3>
            <div style="white-space: pre-wrap;">
                <?= Html::encode($risultato) ?>
            </div>
        </div>
    <?php endif; ?>
</div>