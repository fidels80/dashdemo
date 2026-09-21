<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Tipologiapresenza */

$this->title = ''; // Lasciamo vuoto

// CSS Custom per mantenere lo stile uniforme
$this->registerCss("
    .section-title { border-left: 5px solid #198754; padding-left: 15px; margin-bottom: 20px; margin-top: 10px; font-weight: bold; }
    .info-table th { background-color: #f8f9fa; width: 25%; font-weight: 600; }
    .badge-codice { font-size: 1.2rem; padding: 8px 15px; }
");
?>

<div class="tipologiapresenza-view card p-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode('Dettaglio Causale: ' . $model->codice) ?></h1>
        <div>
            <?= Html::a('<i class="fa fa-edit"></i> Modifica', ['update', 'codice' => $model->codice], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('<i class="fa fa-list"></i> Torna alla Lista', ['index'], ['class' => 'btn btn-secondary btn-lg']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3 class="section-title text-success">Dati Causale Presenza</h3>
            <div class="table-responsive">
                <table class="table table-bordered info-table" style="font-size: 1.1rem;">
                    <tr>
                        <th>Codice Univoco</th>
                        <td>
                            <span class="badge bg-dark badge-codice">
                                <?= Html::encode($model->codice) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Descrizione Causale</th>
                        <td>
                            <div class="p-2 border rounded" style="background-color: #fcfcfc;">
                                <strong><?= Html::encode($model->descrizione) ?></strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="text-end">
        <?= Html::a('<i class="fa fa-trash"></i> Elimina questa causale', ['delete', 'codice' => $model->codice], [
            'class' => 'btn btn-outline-danger',
            'data' => [
                'confirm' => 'Sei sicuro di voler eliminare definitivamente questa causale?',
                'method' => 'post',
            ],
        ]) ?>
    </div>
</div>