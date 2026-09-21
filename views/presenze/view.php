<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Presenze */

$this->title = '';
// 'Dettaglio Presenza: ' . $model->getNominativoPersonale();

// CSS Custom per mantenere lo stile uniforme
$this->registerCss("
    .section-title { border-left: 5px solid #198754; padding-left: 15px; margin-bottom: 20px; margin-top: 10px; font-weight: bold; }
    .info-table th { background-color: #f8f9fa; width: 20%; font-weight: 600; }
    .badge-presenza { font-size: 1rem; padding: 8px 15px; }
    .costo-evidenza { font-size: 1.5rem; color: #198754; font-weight: bold; }
");
?>

<div class="presenze-view card p-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode('Dettaglio Presenza: ' . $model->getNominativoPersonale()) ?></h1>
        <div>
            <?= Html::a('<i class="fa fa-edit"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('<i class="fa fa-list"></i> Torna al Registro', ['index'], ['class' => 'btn btn-secondary btn-lg']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="section-title text-success">Dati Registrazione</h3>
            <div class="table-responsive">
                <table class="table table-bordered info-table" style="font-size: 1.1rem;">
                    <tr>
                        <th>Dipendente</th>
                        <td colspan="3">
                            <strong><?= Html::encode($model->getNominativoPersonale()) ?></strong>
                            (ID: <?= $model->personale_id ?>)
                        </td>
                    </tr>
                    <tr>
                        <th>Data Presenza</th>
                        <td><?= Yii::$app->formatter->asDate($model->data_presenza, 'php:d/m/Y') ?></td>
                        <th>Tipologia</th>
                        <td>
                            <?php
                            $desc = $model->getDescrizionePresenza();
                            $class = 'primary';
                            if ($model->tipo_assenza == 'MAL') $class = 'danger';
                            if ($model->tipo_assenza == 'FER') $class = 'warning text-dark';
                            ?>
                            <span class="badge bg-<?= $class ?> badge-presenza">
                                <?= Html::encode($desc) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ora Ingresso</th>
                        <td><?= $model->ora_ingresso ? date('H:i', strtotime($model->ora_ingresso)) : '-' ?></td>
                        <th>Ora Uscita</th>
                        <td><?= $model->ora_uscita ? date('H:i', strtotime($model->ora_uscita)) : '-' ?></td>
                    </tr>
                    <tr>
                        <th>Ore Lavorate Totali</th>
                        <td><span class="badge bg-dark badge-lg"><?= $model->ore_lavorate ?> ore</span></td>
                        <th>Ritardo</th>
                        <td>
                            <span class="text-<?= $model->ritardo_minuti > 0 ? 'danger' : 'muted' ?>">
                                <?= $model->ritardo_minuti ?> minuti
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Costo Orario</th>
                        <td colspan="3" class="costo-evidenza">
                            <?php
                          
                            echo '€ ' . number_format($model->prz_ora, 2, ',', '.');
                            ?>
                             
                        </td>
                    </tr>
                    
                    <tr>
                        <th>Costo Calcolato</th>
                        <td colspan="3" class="costo-evidenza">
                            <?php
                         $tariffa = ($model->prz_ora > 0) 
    ? $model->prz_ora 
    : (($model->personale && $model->personale->tariffa_oraria > 0) 
        ? $model->personale->tariffa_oraria 
        : ($model->personale->tariffa_oraria ?? 0));
            echo '€ ' . number_format($model->ore_lavorate * $tariffa, 2, ',', '.');
                            ?>
                            <small class="text-muted" style="font-size: 0.9rem; font-weight: normal;">
                                (Basato su tariffa oraria di € <?= number_format($tariffa, 2, ',', '.') ?>)
                            </small>
                        </td>
                    </tr>
                    <tr>
                        <th>Note / Giustificativi</th>
                        <td colspan="3">
                            <div class="p-3 bg-light border rounded">
                                <?= $model->note ? nl2br(Html::encode($model->note)) : '<em>Nessuna nota inserita</em>' ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="text-end">
        <?= Html::a('<i class="fa fa-trash"></i> Elimina questa registrazione', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-outline-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare definitivamente questa registrazione?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
</div>