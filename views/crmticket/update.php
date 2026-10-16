<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */

$this->title = 'Modifica ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Ticket CRM', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->etichetta, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="crmticket-update card p-3 shadow-sm">
    <?= $this->render('_form', [
        'model' => $model,
        'tipi' => $tipi,
        'stati' => $stati,
        'priorita' => $priorita,
        'anagrafiche' => $anagrafiche,
        'articoli' => $articoli,
        'matricole' => $matricole,
        'operatori' => $operatori,
    ]) ?>
</div>
