<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Planning */

$this->title = "Dettaglio Attività #" . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Planning', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

\yii\web\YiiAsset::register($this);
?>

<div class="planning-view card p-4 shadow-sm border-0">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary m-0"><i class="fa fa-calendar-check"></i> <?= Html::encode($this->title) ?></h1>
        <div>
                 <?= Html::a('<i class="fa fa-copy"></i> Duplica', ['duplicate', 'id' => $model->id], [
            'class' => 'btn btn-info shadow-sm text-white',
            'data' => [
                'confirm' => 'Vuoi duplicare questa attività?',
                'method' => 'post',
            ],
        ]) ?>
            <?= Html::a('<i class="fa fa-edit"></i> Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-warning shadow-sm']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger shadow-sm',
                'data' => [
                    'confirm' => 'Sei sicuro di voler eliminare questa pianificazione?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Torna al Calendario', ['index'], ['class' => 'btn btn-outline-secondary ms-2']) ?>
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
                        'label' => '📅 Data Appuntamento',
                    ],
                    [
                        'label' => '⏰ Orario',
                        'value' => substr($model->ora_inizio, 0, 5) . ' - ' . substr($model->ora_fine, 0, 5),
                    ],
                    [
                        'attribute' => 'giro',
                        'label' => '🔢 Ordine Giro',
                        'value' => function($model) {
                            return "Giro " . $model->giro;
                        },
                    ],
                    [
                        'attribute' => 'cd_cf',
                        'label' => '🏢 Cliente / Committente',
                        'value' => function ($model) {
                            return $model->cliente ? $model->cliente->Descrizione : '<span class="text-muted">Nessun cliente associato</span>';
                        },
                        'format' => 'raw',
                    ],
                    [
                        'label' => '👥 Personale Assegnato',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (empty($model->personali)) {
                                return '<span class="text-danger">Nessun operatore assegnato</span>';
                            }

                            $links = [];
                            foreach ($model->personali as $p) {
                                $links[] = Html::a(
                                    "<i class='fa fa-user'></i> " . Html::encode($p->cognome . " " . $p->nome),
                                    ['personale/view', 'id' => $p->id],
                                    [
                                        'class' => 'badge bg-light text-primary border border-primary p-2 mb-1 text-decoration-none shadow-sm d-inline-block ms-1',
                                        'style' => 'font-size: 0.9rem;'
                                    ]
                                );
                            }

                            return implode(' ', $links);
                        },
                    ],
                    [
                        'label' => '🏗️ Supporto Esterno',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (!$model->ditta_esterna) {
                                return '<span class="text-muted">Gestione interna (Nessuna ditta esterna)</span>';
                            }

                            $nomeDitta = $model->dittaEsternaRel ? $model->dittaEsternaRel->descrizione : $model->ditta_esterna;
                            $html = "<strong>" . Html::encode($nomeDitta) . "</strong>";
                            
                            if ($model->qta_operai > 0) {
                                $html .= " <span class='badge bg-secondary ms-2'><i class='fa fa-hard-hat'></i> {$model->qta_operai} operai</span>";
                            }
                            
                            return $html;
                        },
                    ],
                    [
                        // --- MODIFICATO: GESTIONE VEICOLI MULTIPLI CON BADGE ---
                        'label' => '🚚 Veicoli Utilizzati',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (empty($model->veicoliListRel)) {
                                return '<span class="text-muted">Nessun mezzo associato</span>';
                            }

                            $linksV = [];
                            foreach ($model->veicoliListRel as $v) {
                                $linksV[] = Html::a(
                                    "<i class='fa fa-car'></i> " . Html::encode($v->targa . " (" . $v->marca_modello . ")"),
                                    ['veicoli/view', 'id' => $v->id],
                                    [
                                        'class' => 'badge bg-dark p-2 mb-1 text-decoration-none shadow-sm d-inline-block ms-1',
                                        'style' => 'font-size: 0.9rem;'
                                    ]
                                );
                            }

                            return implode(' ', $linksV);
                        },
                    ],
                    [
                        'attribute' => 'indirizzo',
                        'label' => '📍 Luogo Intervento',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if (!$model->indirizzo) return '<span class="text-muted">N/D</span>';
                            $link = "https://www.google.com/maps/search/?api=1&query=" . urlencode($model->indirizzo);
                            return Html::encode($model->indirizzo) . " " .
                                Html::a('<i class="fa fa-map-marked-alt"></i> Naviga', $link, [
                                    'target' => '_blank',
                                    'class' => 'btn btn-xs btn-outline-info ms-2 py-0',
                                    'style' => 'font-size: 0.8rem;'
                                ]);
                        }
                    ],
                    [
                        'attribute' => 'stato_completamento',
                        'label' => '🚩 Stato Attività',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $class = 'bg-primary';
                            if ($model->stato_completamento == 'Completato') $class = 'bg-success';
                            if ($model->stato_completamento == 'Annullato') $class = 'bg-danger';
                            if ($model->stato_completamento == 'In Corso') $class = 'bg-info text-dark';

                            return "<span class='badge {$class} px-3 py-2 shadow-sm'>" . Html::encode($model->stato_completamento) . "</span>";
                        },
                    ],
                ],
            ]) ?>
        </div>

        <div class="col-md-4">
            <div class="card bg-light border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-muted mb-3"><i class="fa fa-comment-dots"></i> Note e Descrizione</h5>
                    <p class="card-text text-dark" style="white-space: pre-line; line-height: 1.6;">
                        <?= $model->descrizione ? Html::encode($model->descrizione) : '<em>Nessuna nota aggiuntiva inserita per questa attività.</em>' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

</div>