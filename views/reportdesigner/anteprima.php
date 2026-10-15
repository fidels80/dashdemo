<?php

use yii\helpers\Html;

/* @var $report app\models\DashReport */
/* @var $def array */
/* @var $result array|null */
/* @var $error string|null */

$this->title = 'Anteprima: ' . $report->nome;
?>
<div class="reportdesigner-anteprima container-fluid py-3">
    <h4 class="mb-3"><i class="fas fa-eye text-info"></i> <?= Html::encode($this->title) ?></h4>

    <?php if ($error): ?>
        <div class="alert alert-danger"><strong>Errore:</strong> <?= Html::encode($error) ?></div>
    <?php elseif ($result && (!empty($result['righe']) || !empty($result['gruppi']))): ?>
        <?= $this->render('/report/_tabella', ['result' => $result, 'tableId' => 'anteprima-table']) ?>
    <?php elseif ($result): ?>
        <div class="alert alert-info">Nessun dato con la definizione corrente.</div>
    <?php endif; ?>
</div>
