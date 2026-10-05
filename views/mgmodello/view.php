<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\MgAttributoArticolo */
/* @var $tessuti app\models\MgModelloTessuto[] */
/* @var $articoli app\models\MgArticolo[] */
/* @var $matrice array */

$this->title = 'Modello ' . $model->etichetta;
$this->params['breadcrumbs'][] = ['label' => 'Modelli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$campo = function ($label, $valore, $col = 'col-md-3') {
    echo '<div class="' . $col . ' mb-3">'
        . '<label class="text-muted small mb-0 d-block">' . Html::encode($label) . '</label>'
        . '<div class="font-weight-bold">' . ($valore === '' || $valore === null ? '<span class="text-muted">—</span>' : Html::encode($valore)) . '</div>'
        . '</div>';
};
?>
<div class="mgmodello-view">

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
        <div>
            <?= Html::a('<i class="fas fa-pen"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fas fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Eliminare questo modello?', 'method' => 'post'],
            ]) ?>
            <?= Html::a('<i class="fas fa-magic"></i> Genera varianti', ['mgwizard/index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Dati modello</div>
        <div class="card-body">
            <div class="row">
                <?php
                $campo('ID', (int) $model->id, 'col-md-2');
                $campo('Codice', $model->codice, 'col-md-3');
                $campo('Attivo', $model->attivo ? 'Sì' : 'No', 'col-md-2');
                $campo('Articoli generati', count($articoli), 'col-md-2');
                ?>
            </div>
            <div class="row">
                <?php $campo('Descrizione', $model->descrizione, 'col-md-12'); ?>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Tessuti associati</div>
        <div class="card-body p-0">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th>Attivo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($tessuti as $t): ?>
                    <tr>
                        <td><?= Html::encode($t->tessuto ? $t->tessuto->codice : '') ?></td>
                        <td><?= Html::encode($t->tessuto ? $t->tessuto->descrizione : '') ?></td>
                        <td><?= $t->attivo ? 'Sì' : 'No' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($tessuti)): ?>
                    <tr><td colspan="3" class="text-muted p-3">Nessun tessuto associato. <?= Html::a('Modifica il modello', ['update', 'id' => $model->id]) ?> per associarne.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (!empty($matrice['righe'])): ?>
        <div class="card mb-3">
            <div class="card-header">Matrice taglie</div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-bordered mb-0">
                    <thead>
                    <tr>
                        <th>Tessuto</th>
                        <th>Colore</th>
                        <?php foreach ($matrice['taglie'] as $t): ?>
                            <th class="text-center"><?= Html::encode($t['label']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($matrice['righe'] as $riga): ?>
                        <tr>
                            <td><?= Html::encode($riga['tessuto']) ?></td>
                            <td><?= Html::encode($riga['colore']) ?></td>
                            <?php foreach ($matrice['taglie'] as $t): ?>
                                <?php $cella = $riga['celle'][$t['key']] ?? null; ?>
                                <td class="text-center">
                                    <?php if ($cella): ?>
                                        <a href="<?= Url::to(['mgarticolo/view', 'id' => $cella['id_articolo']]) ?>"
                                           title="<?= Html::encode($cella['descrizione']) ?>"><?= Html::encode($cella['codice']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Articoli del modello</span>
            <span class="text-muted small"><?= count($articoli) ?> articoli</span>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead>
                <tr>
                    <th>Codice</th>
                    <th>Descrizione</th>
                    <th>Tessuto</th>
                    <th>Colore</th>
                    <th>Taglia</th>
                    <th class="text-right">Prezzo</th>
                    <th>Attivo</th>
                    <th class="text-center text-nowrap">Azioni</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($articoli as $a): ?>
                    <tr>
                        <td><?= Html::encode($a->codice) ?></td>
                        <td><?= Html::encode($a->descrizione) ?></td>
                        <td><?= Html::encode($a->tessuto ? $a->tessuto->descrizione : '') ?></td>
                        <td><?= Html::encode($a->colore ? $a->colore->descrizione : '') ?></td>
                        <td><?= Html::encode($a->taglia ? $a->taglia->descrizione : '') ?></td>
                        <td class="text-right"><?= number_format((float) $a->prezzo, 4, ',', '.') ?></td>
                        <td><?= $a->attivo ? 'Sì' : 'No' ?></td>
                        <td class="text-center text-nowrap">
                            <?= Html::a('<i class="fas fa-eye"></i>', ['mgarticolo/view', 'id' => $a->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi articolo']) ?>
                            <?= Html::a('<i class="fas fa-pen"></i>', ['mgarticolo/update', 'id' => $a->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica articolo']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($articoli)): ?>
                    <tr><td colspan="8" class="text-muted p-3">Nessun articolo generato per questo modello. Usa il <?= Html::a('Wizard prodotti', ['mgwizard/index']) ?> per creare le varianti.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
