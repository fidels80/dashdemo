<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use onmotion\apexcharts\ApexchartsWidget;
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

.card {
  width: 33%;
  margin-bottom: 20px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
  .card {
    width: 100%;
  }
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

<div class="search-form">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline row'], // Aggiungi la classe form-inline per layout orizzontale
    ]); ?>

    <div class="form-row">
        <!-- Giorno Da -->
        <div class="form-group col-12 col-md-3">
            <?= Html::label('Day From', 'dayFrom', ['class' => 'form-label']) ?>
            <?= Html::input('date', 'dayFrom', $dayFrom, ['class' => 'form-control']) ?>
        </div>

        <!-- Giorno A -->
        <div class="form-group col-12 col-md-3">
            <?= Html::label('Day To', 'dayTo', ['class' => 'form-label']) ?>
            <?= Html::input('date', 'dayTo', $dayTo, ['class' => 'form-control']) ?>
        </div>

        <!-- Utente -->
        <div class="form-group col-12 col-md-4">
            <?= Html::label('User', 'utente', ['class' => 'form-label']) ?>
            <?= Select2::widget([
                'name' => 'utente',
                'value' => $utente,
                'data' => array_combine($users, $users),
                'options' => ['placeholder' => 'Select user'],
                'pluginOptions' => ['allowClear' => true],
                'options' => ['class' => 'form-control']
            ]) ?>
        </div>

        <!-- Bottone di ricerca -->
        <div class="form-group col-12 col-md-2">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- Visualizza il grafico a torta -->
<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

  <div class="card" style="width: 33%;">
    <div class="card-body">
      <h5  class="card-title"> Lavori per ditta</h5>
         
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '100%',
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

    <div class="full-screen-container">
        Occupazione software
        <div class="AGE-details">
            
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '100%',
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
    <div class="full-screen-container">
        Assistenze per Cliente
        <div class="AGE-details">
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '100%',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => $labels3,
                ],
                'series' => $series3,
            ]) ?>
        </div>
    </div>
</div>
<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="full-screen-container">
        Andamento Settimanale
        <div class="AGE-details">
            <?= ApexchartsWidget::widget([
                'type' => 'bar',
                'height' => '100%',
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
                        'name' => 'Totale Quotidiano' . array_sum($totaleQuotidiano),
                        'data' => $totaleQuotidiano,
                    ],
                    [
                        'name' => 'Totale Lavorato' . array_sum($totaleLavorato),
                        'data' => $totaleLavorato,
                    ],
                ]
            ]) ?>
        </div>
    </div>
    <div class="full-screen-container">
        Rapporto Ore/Lavoro
        <div class="AGE-details">
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '100%',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => ['Ore Lavorate', 'Ore Libere'],
                ],
                'series' => [array_sum($totaleLavorato), array_sum($totaleQuotidiano)],
            ]) ?>
        </div>
    </div>
    <div class="full-screen-container">
        Attività per giorno [AP=attività progetto,T=ticket,Tp=Ticket Progetto,TA=Ticket attività]
        <div class="AGE-details">
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
    </div>
</div>
<div class="full-screen-container full-width">
    <div class="AGE-details">
        <div class="AGE-title"><?php //Html::encode("Ordini") ?>
        </div>

        <?php
        echo  ApexchartsWidget::widget([
            'type' => 'line',
            'height' => '100%',
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
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="full-screen-container">
        Tipologia Assistenza
        <div class="AGE-details">
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '100%',
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
    <div class="full-screen-container">
        Ultimi Ticket Aperti
        <div class="AGE-details">
            <table class="table  table-sm table-hover table-responsive-sm">
                <thead class="thead-dark">
                    <TH>soggetto</TH>
                    <TH>dataevento</TH>
                    <TH>TIPOEVENTO</TH>
                    <TH>Operatore</TH>
                    <TH>STATUS</TH>
                </thead>
                <?php
                yii::warning($resultstk);

                foreach ($resultstk as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['soggetto'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['dataevento'];
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
    <div class="full-screen-container">
        TIcket Ingrombanti
        <div class="AGE-details">
            <table class="table  table-sm table-hover table-responsive-sm">
                <thead class="thead-dark">
                    <TH>soggetto</TH>
                    <TH>dataevento</TH>
                    <TH>TIPOEVENTO</TH>
                    <TH>Ore</TH>
                    <TH>STATUS</TH>
                </thead>
                <?php
                yii::warning($resultstk);

                foreach ($resultBIGTK as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['soggetto'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['dataevento'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['TIPOEVENTO'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo round($row['oredelta'],2);
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

</div>

<?php
yii::error($dataProvider);
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'soggetto',
        'dataevento',
        'AREA',
        'TIPOEVENTO',
        'orainizioevento',
        'orafineevento',
        'oredelta',
        'codicesoggetto',
        'codicestatoevento',
        'oggetto',
        'noteevento',
        'codiceprogetto',
        'Custom1',
        'custom2',
        'custom3',
        'custom4',
        'custom5',
        'STATUS',
        'descrizioneprogetto',
        'utentecreatore',
        'utente_destinatario',
        'dataprevistachiusura',
        'pid',
        'tid',
        'tipo',
        'xtipologia',
        'tipoeventoagg'
    ],
    'toolbar' => [
        '{export}',
        '{toggleData}',
    ],
    'exportConfig' => [
        GridView::CSV => [],
        GridView::EXCEL => [],
    ],
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> Risultati</h3>',
    ],
]); 
?>
