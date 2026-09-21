<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Planning */

$this->title = '';
$this->params['breadcrumbs'][] = ['label' => 'Planning', 'url' => ['index']];
$this->params['breadcrumbs'][] = '';
$this->title;

\yii\web\YiiAsset::register($this);
?>

<div class="planning-view card p-4 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary m-0"><i class="fa fa-calendar-check"></i> <?= Html::encode("Dettaglio Attività #" . $model->id) ?></h1>
        <div>
            <?= Html::a('<i class="fa fa-edit"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sei sicuro di voler eliminare questa pianificazione?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Torna al Planning', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'options' => ['class' => 'table table-striped table-bordered detail-view'],
                'attributes' => [
                    [
                        'attribute' => 'data_attivita',
                        'format' => ['date', 'php:d/m/Y'],
                        'label' => ' Data Appuntamento',
                    ],
                    [
                        'label' => ' Orario',
                        'value' => substr($model->ora_inizio, 0, 5) . ' - ' . substr($model->ora_fine, 0, 5),
                    ],
                    [
                        'attribute' => 'personale_id',
                        'label' => ' Dipendente Assegnato',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->personale
                                ? "<strong>" . $model->personale->cognome . " " . $model->personale->nome . "</strong>"
                                : '<span class="text-danger">Non assegnato</span>';
                        },
                    ],
                    [
                        'attribute' => 'veicolo_id',
                        'label' => ' Veicolo Utilizzato',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->veicolo
                                ? Html::a($model->veicolo->targa . " (" . $model->veicolo->marca_modello . ")", ['veicoli/view', 'id' => $model->veicolo_id], ['class' => 'badge bg-dark p-2 text-decoration-none'])
                                : '<span class="text-muted">Nessun mezzo</span>';
                        },
                    ],
                    [
                        'attribute' => 'indirizzo',
                        'label' => 'Luogo Intervento',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $link = "https://www.google.com/maps/search/?api=1&query=" . urlencode($model->indirizzo);
                            return Html::encode($model->indirizzo) . " " . Html::a('<i class="fa fa-map-marked-alt"></i> Apri Mappe', $link, ['target' => '_blank', 'class' => 'btn btn-xs btn-outline-info ms-2']);
                        }
                    ],
                    [
                        'attribute' => 'stato_completamento',
                        'label' => 'Stato',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $class = 'bg-primary';
                            if ($model->stato_completamento == 'Completato') $class = 'bg-success';
                            if ($model->stato_completamento == 'Annullato') $class = 'bg-danger';
                            if ($model->stato_completamento == 'In Corso') $class = 'bg-info text-dark';

                            return "<span class='badge {$class} px-3 py-2'>" . $model->stato_completamento . "</span>";
                        },
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <div class="alert alert-light border">
                <h5 class="text-muted"><i class="fa fa-comment-dots"></i> Descrizione Attività</h5>
                <p class="lead" style="font-size: 1rem;">
                    <?= $model->descrizione ? nl2br(Html::encode($model->descrizione)) : '<em>Nessuna nota inserita.</em>' ?>
                </p>
            </div>
        </div>
    </div>

</div>