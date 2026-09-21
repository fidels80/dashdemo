<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use onmotion\apexcharts\ApexchartsWidget;
//yii::error($t);
//yii::error($ticketstat_res);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $users array */
/* @var $dayFrom string */
/* @var $dayTo string */
/* @var $utente string */

$this->registerCss('
    .full-screen-container {
        width: 100%;
        max-width: 600px;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #fff;
        box-shadow: 0px 0px 10px 0px rgba(0,0,0,0.1);
        margin: 10px; /* Aggiungiamo margine per separare i blocchi */
    }

    @media (min-width: 768px) {
        .full-screen-container {
            width: calc(33.33% - 20px); /* Calcoliamo la larghezza per fare 3 colonne in una riga */
        }
    }

    .AGE-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .AGE-info {
        margin-bottom: 15px;
    }

    .AGE-info-label {
        font-weight: bold;
    }

    .AGE-info-value {
        margin-left: 10px;
    }

    .AGE-actions {
        margin-top: 20px;
    }

    .AGE-actions .btn {
        margin-right: 10px;
    }

    .full-width {
        width: 100% !important;
        max-width: none !important;
    }
        .chart-container {
    width: 300px;
    height: 200px;
}
    .card-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
}

.custom-card {
    width: 33%;
    height: 350px;
    margin-bottom: 20px;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .custom-card {
        width: 100%;
    }
table.table-fit {
  width: auto !important;
  table-layout: auto !important;
}
table.table-fit thead th,
table.table-fit tbody td,
table.table-fit tfoot th,
table.table-fit tfoot td {
  width: auto !important;
}
  table td {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.form-group {
        margin-bottom: 1rem; /* Aggiungi margine inferiore tra le righe */
    }

    .form-group + .form-group {
        margin-left: 1rem; /* Aggiungi margine sinistro tra le colonne */
    }

');


$this->registerJs('
    function adjustChartSize() {
        var containers = document.querySelectorAll(".full-screen-container");
        containers.forEach(function(container) {
            var chart = container.querySelector(".apexcharts-canvas");
            if (chart) {
                var seriesCount = container.dataset.seriesCount || 0; // Ottieni il conteggio delle serie in un altro modo
                var newHeight = seriesCount > 3 ? (seriesCount * 30) + "px" : "270px";
                console.log("New height: ", newHeight);
                chart.style.height = newHeight;
                chart.style.width = newHeight; // Imposta una larghezza appropriata
            }
        });
    }

    document.addEventListener("DOMContentLoaded", adjustChartSize);
    window.addEventListener("resize", adjustChartSize);
');


?>
<style>
    @media screen and (width: 1440px) {

        .card-title,
        .table,
        .thead-dark,
        td,
        th,
        .btn,
        .panel-title,
        .panel-heading {
            font-size: calc(100% - 3px) !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .table td,
        .table th {
            padding: 0.3rem !important;
        }

        .fas {
            font-size: smaller !important;
        }

        .form-control,
        .select2-selection {
            font-size: smaller !important;
        }

        /* For kartik GridView components */
        .kv-panel-before,
        .kv-panel-after {
            font-size: smaller !important;
        }
    }

    @media screen and (width: 1920px) {

        .card-title,
        .table,
        .thead-dark,
        td,
        th,
        .btn,
        .panel-title,
        .panel-heading {
            font-size: calc(100% - 2px) !important;
        }

        .card-body {
            padding: 0.85rem !important;
        }

        .table td,
        .table th {
            padding: 0.2rem !important;
        }

        .fas {
            font-size: smaller !important;
        }

        .form-control,
        .select2-selection {
            font-size: smaller !important;
        }

        /* For kartik GridView components */
        .kv-panel-before,
        .kv-panel-after {
            font-size: smaller !important;
        }
    }
    
</style>
<div class="d-flex flex-wrap justify-content-center align-items-start full-width">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline row g-3'], // Aggiungi la classe g-3 per spaziatura tra le colonne
    ]); ?>

    <div class="form-row">
        <!-- Giorno Da -->
        <table class="table">
            <td>
                <?= Html::label('Day From', 'dayFrom', ['class' => 'form-label']) ?>
                <?= Html::input('date', 'dayFrom', $dayFrom, ['class' => 'form-control']) ?>
            </td>

            <!-- Giorno A -->
            <td>
                <?= Html::label('Day To', 'dayTo', ['class' => 'form-label']) ?>
                <?= Html::input('date', 'dayTo', $dayTo, ['class' => 'form-control']) ?>
            </td>

            <!-- Utente -->
            <td>
                <?= Html::label('Utente', 'utente', ['class' => 'form-label']) ?>
                <?= Select2::widget([
                    'name' => 'utente',
                    'value' => $utente,
                    'data' => array_combine($users, $users),
                    'options' => ['placeholder' => 'Select user', 'class' => 'form-control select2'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </td>


            <td>
                <?php if (Yii::$app->user->identity->level  >= 80): ?>

                    <?= Html::label('Gruppi', 'groupusers', ['class' => 'form-label']) ?>
                    <?= Select2::widget([
                        'name' => 'groupusers',
                        //'value' => $groupusers,
                        'data' => array_combine($groupusers, $groupusers),
                        'options' => ['placeholder' => 'Seleziona Gruppo', 'class' => 'form-control select2'],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                <?php endif; ?>
            </td>





            <!-- Bottone di ricerca -->
            <td>
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']) ?>
            </td>

            <!-- Bottone di reset -->
            <td>
                <button type="button" class="btn btn-secondary btn-block" id="resetFilters">Reset</button>
            </td>
            <td>
                <button type="button" class="btn btn-primary btn-block" id="aprivtiger">Apri Vtiger</button>
            </td>
            <td>
                <?php echo Html::a('Esegui Progetti', ['vtiger/progetti'], [
                    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
                ]); ?>
            </td>
            <td>
                <?php echo Html::a('Esegui gantt', ['vtiger/ganttprogetti'], [
                    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
                ]); ?>
            </td>

            <td>
                <?php echo Html::a('Esegui Ticket', ['vtiger/ticket'], [
                    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
                ]); ?>
            </td>
        </table>
    </div>

    <?php ActiveForm::end(); ?>
</div>




<script>
    document.getElementById('resetFilters').addEventListener('click', function() {
        // Reset all input fields
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.value = ''; // Reset all input fields
        });

        // Reset Select2 widgets
        $('.select2').val(null).trigger('change');
    });
    document.getElementById('aprivtiger').addEventListener('click', function() {
        window.open('http://crm.ilvbc.it:8090/index.php', '_blank').focus();
    });
</script>



<!-- Visualizza il grafico a torta -->
<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

    <div class=" custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Lavori per ditta</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => $labels,
                ],
                'series' => $series,
            ]) ?>

        </div>
    </div>

    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Occupazione software</h5>


            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => $labels2,
                ],
                'series' => $series2,
            ]) ?>
        </div>
    </div>
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Assistenze per Cliente</h5>
            <?php //yii::warning($tree);

            // I tuoi dati PHP
            $pinos = [
                ['x' => 'New Delhi', 'y' => 218],
                ['x' => 'Kolkata', 'y' => 149],
                ['x' => 'Mumbai', 'y' => 184],
                ['x' => 'Ahmedabad', 'y' => 55],
                ['x' => 'Bangaluru', 'y' => 84],
                ['x' => 'Pune', 'y' => 31],
                ['x' => 'Chennai', 'y' => 70]
            ];
            // yii::error($pinos);
            // yii::error($tree);
            $tseries = [
                ['data' => $tree]
            ];




            ?>



            <?= ApexchartsWidget::widget([
                'type' => 'treemap',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    // 'labels' => $labels3,
                ],
                'series' => $tseries,
            ]) ?>
        </div>
    </div>
</div>
<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Andamento Settimanale</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'bar',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'plotOptions' => [
                        'bar' => [
                            'horizontal' => false,
                            'columnWidth' => '55%',
                            'endingShape' => 'rounded'
                        ],
                    ],
                    'dataLabels' => [
                        'enabled' => false
                    ],
                    'stroke' => [
                        'show' => true,
                        'width' => 2,
                        'colors' => ['transparent']
                    ],
                    'xaxis' => [
                        'categories' => $cdCfs,
                    ],
                    'yaxis' => [
                        'title' => [
                            'text' => 'Valori (H)'
                        ]
                    ],
                    'fill' => [
                        'opacity' => 1
                    ],
                    'tooltip' => [
                        'y' => [
                            'formatter' => new \yii\web\JsExpression("function (val) {
                        return val + ' H';
                    }")
                        ]
                    ]
                ],
                'series' => [
                    [
                        'name' => 'Totale Quotidiano  ' . array_sum($totaleQuotidiano),
                        'data' => $totaleQuotidiano,
                    ],
                    [
                        'name' => 'Totale Lavorato  ' . array_sum($totaleLavorato),
                        'data' => $totaleLavorato,
                    ],
                ]
            ]) ?>
        </div>
    </div>
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Rapporto Ore/Lavoro</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => ['Ore Lavorate', 'Ore Lavorabili'],
                ],
                'series' => [array_sum($totaleLavorato), array_sum($totaleQuotidiano)],
            ]) ?>
        </div>
    </div>
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Attività per giorno</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => $labels4,
                ],
                'series' => $series4,
            ]) ?>
        </div>
        <p class="card-text"><small class="text-muted">[AP=attività progetto,T=ticket,Tp=Ticket Progetto,TA=Ticket attività]</small></p>
    </div>
</div>
<div class="full-screen-container full-width">
    <div class="AGE-details">
        <div class="AGE-title"><?php //Html::encode("Ordini") 
                                ?>
        </div>

        <?php
        echo  ApexchartsWidget::widget([
            'type' => 'line',
            'height' => '370px',
            'width' => '100%',
            'chartOptions' => [
                'chart' => [
                    'toolbar' => [
                        'show' => true,
                    ],
                ],
                'xaxis' => [
                    'type' => 'datetime',
                    'categories' => $categories,
                ],
                'yaxis' => [
                    'title' => [
                        'text' => 'Numero Di richieste',
                    ],
                ],
                'tooltip' => [
                    'x' => [
                        'format' => 'dd MMM yyyy',
                    ],
                ],
            ],
            'series' => $series7,
        ]);
        ?>
    </div>
</div>

<!-- Include Bootstrap CSS se non è già incluso -->


<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Tipologia Assistenza</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => $labels5,
                ],
                'series' => $series5,
            ]) ?>
        </div>
    </div>


    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Ultimi Ticket Aperti</h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>soggetto</TH>
                    <TH>dataevento</TH>
                    <TH>TIPOEVENTO</TH>
                    <TH>Operatore</TH>
                    <TH>STATUS</TH>
                </thead>
                <?php
                //   yii::warning($resultstk);

                foreach ($resultstk as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['soggetto'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['dataevento']));

                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['TIPOEVENTO'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['utente_destinatario'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['STATUS'];
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
            </table>
        </div>
    </div>



    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                TIcket Ingrombanti</h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>soggetto</TH>
                    <TH>dataevento</TH>
                    <TH>TIPOEVENTO</TH>
                    <TH>Ore</TH>
                    <TH>STATUS</TH>
                </thead>
                <?php
                //yii::warning($resultstk);

                foreach ($resultBIGTK as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['soggetto'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['dataevento']));
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['TIPOEVENTO'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo round($row['oredelta'], 2);
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['STATUS'];
                    echo '</td>';
                    echo '</tr>';
                }



                //   yii::error(json_encode(array_values(array_unique(array_column($ticketstat_res, 'productname')))));
                ?>
            </table>


        </div>
    </div>
</div>




<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="panel-title m-0" style="font-size: 1.1rem;">
            <i class="fas fa-list-alt mr-2"></i> Risultati Ricerca
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="table-results-final" class="table table-hover table-striped mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Soggetto</th>
                        <th>Data</th>
                        <th>Area</th>
                        <th>Tipo</th>
                        <th>Inizio</th>
                        <th>Fine</th>
                        <th>Delta</th>
                        <th>Cod. Soggetto</th>
                        <th>Stato Evento</th>
                        <th>Oggetto</th>
                        <th>Note</th>
                        <th>Progetto</th>
                        <th>C1</th>
                        <th>C2</th>
                        <th>C3</th>
                        <th>C4</th>
                        <th>C5</th>
                        <th>Status</th>
                        <th>Desc. Progetto</th>
                        <th>Creatore</th>
                        <th>Destinatario</th>
                        <th>Prev. Chiusura</th>
                        <th>PID</th>
                        <th>TID</th>
                        <th>Tipo</th>
                        <th>Tipologia</th>
                        <th>Agg.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Estraiamo i dati dal DataProvider
                    $models = $dataProvider->getModels();
                    foreach ($models as $m): ?>
                        <tr>
                            <td><?= Html::encode($m['soggetto'] ?? '') ?></td>
                            <td><?= !empty($m['dataevento']) ? date('d/m/Y', strtotime($m['dataevento'])) : '' ?></td>
                            <td><?= Html::encode($m['AREA'] ?? '') ?></td>
                            <td><?= Html::encode($m['TIPOEVENTO'] ?? '') ?></td>
                            <td><?= Html::encode($m['orainizioevento'] ?? '') ?></td>
                            <td><?= Html::encode($m['orafineevento'] ?? '') ?></td>
                            <td><?= number_format($m['oredelta'] ?? 0, 2) ?></td>
                            <td><?= Html::encode($m['codicesoggetto'] ?? '') ?></td>
                            <td><?= Html::encode($m['codicestatoevento'] ?? '') ?></td>
                            <td><?= Html::encode($m['oggetto'] ?? '') ?></td>
                            <td title="<?= Html::encode($m['noteevento'] ?? '') ?>">
                                <?= mb_strimwidth(Html::encode($m['noteevento'] ?? ''), 0, 30, "...") ?>
                            </td>
                            <td><?= Html::encode($m['codiceprogetto'] ?? '') ?></td>
                            <td><?= Html::encode($m['Custom1'] ?? '') ?></td>
                            <td><?= Html::encode($m['custom2'] ?? '') ?></td>
                            <td><?= Html::encode($m['custom3'] ?? '') ?></td>
                            <td><?= Html::encode($m['custom4'] ?? '') ?></td>
                            <td><?= Html::encode($m['custom5'] ?? '') ?></td>
                            <td>
                                <span class="badge <?= ($m['STATUS'] == 'Chiuso') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= Html::encode($m['STATUS'] ?? '') ?>
                                </span>
                            </td>
                            <td title="<?= Html::encode($m['descrizioneprogetto'] ?? '') ?>">
                                <?= mb_strimwidth(Html::encode($m['descrizioneprogetto'] ?? ''), 0, 100, "...") ?>
                            </td>
                            <td><?= Html::encode($m['utentecreatore'] ?? '') ?></td>
                            <td><?= Html::encode($m['utente_destinatario'] ?? '') ?></td>
                            <td><?= !empty($m['dataprevistachiusura']) ? date('d/m/Y', strtotime($m['dataprevistachiusura'])) : '' ?></td>
                            <td><?= Html::encode($m['pid'] ?? '') ?></td>
                            <td><?= Html::encode($m['tid'] ?? '') ?></td>
                            <td><?= Html::encode($m['tipo'] ?? '') ?></td>
                            <td><?= Html::encode($m['xtipologia'] ?? '') ?></td>
                            <td><?= Html::encode($m['tipoventoagg'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Asset per DataTables (se non già caricati nel layout)
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css');

// 2. Registrazione JS
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap5.min.css');
$this->registerJsFile('https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// 3. CSS Custom per uniformare lo stile (Header chiaro, bottoni e allineamento)
$this->registerCss("
    .dataTables_filter { text-align: left !important; float: left !important; }
    .dataTables_filter label { display: flex; align-items: center; gap: 10px; }
    .dt-buttons { float: right !important; margin-bottom: 15px; }
    /* Rimuoviamo il look scuro dell'header per farlo uguale all'anagrafica */
    #presenze-table thead tr { background-color: #f8f9fa !important; color: #212529 !important; }
    #presenze-table th { border-bottom: 1px solid #dee2e6 !important; }
    .btn-xs { padding: 4px 10px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; font-weight: 600; min-width: 90px; justify-content: center; }
    .btn-xs i { font-size: 0.9rem; }
    .td-azioni { min-width: 150px !important; }
    .costo-cella { font-weight: bold; color: #198754; text-align: right !important; }
.col-costo { width: 100px !important; min-width: 100px !important; max-width: 100px !important; white-space: nowrap; }

");

?>
<?php
$js = <<<JS
$(document).ready(function() {
    $('#table-results-final').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tutti"]],
        "order": [[1, "desc"]], // Ordina per Data decrescente
        
        // Ho aggiunto 'l' prima di 'f' per mostrare il selettore di quantità
        "dom": '<"row px-3 py-2"<"col-md-3"l><"col-md-4"f><"col-md-5 text-end"B>>rt<"row px-3 py-2"<"col-md-6"i><"col-md-6"p>>',
        
        "language": {
            "search": "Cerca:",
            "lengthMenu": "Mostra _MENU_ record",
            "info": "Visualizzati da _START_ a _END_ di _TOTAL_ record",
            "infoEmpty": "Nessun record disponibile",
            "infoFiltered": "(filtrati da _MAX_ record totali)",
            "paginate": {
                "first": "Inizio",
                "last": "Fine",
                "next": "Successivo",
                "previous": "Precedente"
            },
            "buttons": {
                "copyTitle": "Copiato",
                "copySuccess": {
                    "_": "%d righe copiate",
                    "1": "1 riga copiata"
                }
            }
        },
        "buttons": [
            { extend: 'copy', className: 'btn btn-secondary btn-sm', text: '<i class="fa-solid fa-copy">Copia</i>' },
            { extend: 'excel', className: 'btn btn-success btn-sm', text: '<i class="fa-solid fa-file-excel">Excel</i>' },
            { 
                extend: 'pdfHtml5', 
                className: 'btn btn-danger btn-sm', 
                text: '<i class="fa-solid fa-file-pdf">Pdf</i>',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            { extend: 'print', className: 'btn btn-primary btn-sm', text: '<i class="fa-solid fa-print">Stampa</i>' }
        ],
    });
});
JS;
$this->registerJs($js);
?>