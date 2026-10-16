<?php

/* @var $this yii\web\View */
/* @var $model app\models\MgCrmTicket */

$this->title = 'Nuova richiesta CRM';
$this->params['breadcrumbs'][] = ['label' => 'Ticket CRM', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crmticket-create card p-3 shadow-sm">
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
