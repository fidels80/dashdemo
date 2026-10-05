<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ApiToken */
/* @var $entita app\models\DashApiEntita[] */
/* @var $operazioni array<string, string> */
/* @var $selezionati array<string, string[]> */

$this->title = 'Permessi del token';
$this->params['breadcrumbs'][] = ['label' => 'Token API', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apitoken-update">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Stai modificando i permessi di <strong><?= Html::encode($model->descrizione) ?></strong>
        (creato il <?= Html::encode(date('d/m/Y H:i', $model->created_at)) ?>).
        Il token in chiaro non cambia: continua a funzionare lo stesso.
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <?= $this->render('_permessi', [
        'entita' => $entita,
        'operazioni' => $operazioni,
        'selezionati' => $selezionati,
    ]) ?>

    <div class="form-group mt-3">
        <?= Html::submitButton('<i class="fas fa-save"></i> Salva i permessi', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Annulla', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
