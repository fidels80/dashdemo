<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use onmotion\apexcharts\ApexchartsWidget;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $users array */
/* @var $dayFrom string */
/* @var $dayTo string */
/* @var $utente string */
/* @var $listap string */
/* @var $listac string */

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
.custom-card2 {
    width: 100%;
    height: 800px;
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
  width: 100%; /*auto !important;*/
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

$listatc = [];
//yii::error($dataProvider);
//yii::error($listac);

?>

<?php
if (Yii::$app->request->get('listap') != null) {
    if (is_array($listap)) {
        echo 'Stai vedendo i dati per i progetti <B><br>';
        $projectNames = []; // Array per tenere traccia dei nomi unici

        foreach ($resultstotali as $xit) {
            if (!in_array($xit['projectname'], $projectNames)) { // Controlla se il nome è già stato aggiunto
                $projectNames[] = $xit['projectname']; // Aggiungi il nome alla lista
                echo $xit['projectname'] . '<br>'; // Stampa il nome
            }
        }
        echo '</b>';
    } else {
        echo (Yii::$app->request->get('listap') != null) ? 'Stai vedendo i dati per il progetto <B>' . Yii::$app->request->get('listap') . '</B><br>' : '';
    }
}
echo (Yii::$app->request->get('listac') != null) ? 'Stai vedendo i dati per il cliente <B>' . Yii::$app->request->get('listac') . '</B><br>' : '';
?>




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
            <td> <?= Html::label('Utente', 'utente', ['class' => 'form-label']) ?>
                <?= Select2::widget([
                    'name' => 'utente',
                    'value' => $utente,
                    'data' => array_combine($users, $users),
                    'options' => ['placeholder' => 'Select user', 'class' => 'form-control select2'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?></td>

            <!-- Utente -->
            <td width=200px;>
                <?= Html::label('Clienti', 'Clisenti', ['class' => 'form-label']) ?>
                <?= Select2::widget([
                    'name' => 'listac',
                    'value' => $listatc,

                    'data' => array_combine($listac, $listac),
                    'options' => ['placeholder' => 'Select user', 'class' => 'form-control select2',],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </td>
            <td width=400px;>
                <?= Html::label('Progetti', 'Progetti', ['class' => 'form-label']) ?>
                <?= Select2::widget([
                    'name' => 'listap',
                    'value' => $listatc,
                    'data' => array_combine($listap, $listap),
                    'options' => [
                        'placeholder' => 'Seleziona Progetto',
                        'class' => 'form-control select2',
                        'value' => '',
                        'multiple' => true
                    ],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
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
                <?php echo Html::a('Apri Operatori', ['vtiger/search'], [
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

<?php




/*foreach ($resultstotali as &$item) {
    if ($item['monteore'] === null || $item['tipo']=='AP') {
        $item['monteore'] = -1;
    }
}*/

// Rimuovi il riferimento
unset($item);
//yii::warning($tsql);
yii::warning($resultstotali);
//yii::error($tracking);


$cdCfs = [];
$totaleLavorato = [];
$totaleTicket = [];
foreach ($resultstotali as $result) {
    $cdCfs[] = $result['projectname']; // Giorni per l'asse X
    if ($result['tipo'] == 'AP') {
        $totaleLavorato[] = round($result['totaleore'], 2); // Somma ore lavorate
    }
    if ($result['tipo'] <> 'AP') {
        $totaleTicket[] = round($result['totaleore'], 2); // Somma ore lavorate
    }
}



$totalequotdiano = [];
foreach ($resultstotali as $result) {
    $cdCfs[] = $result['projectname']; // Giorni per l'asse X
    $totalequotdiano[] = $result['monteore']; // Somma ore lavorate
}
$cdCfs = array_unique($cdCfs);
$totalequotdiano = array_unique($totalequotdiano);
yii::warning($resultstotalitra);

$totaletrasferta = [];
foreach ($resultstotalitra as $result) {
    $cdCfs[] = $result['projectname']; // 
    $totaletrasferta[] = $result['totaleore'];
}


?>


<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Andamento Progetto</h5>

            <?php
            $tmptot = round(array_sum($totalequotdiano), 2);

            // Verifica se il totale è zero e imposta il valore della chiave 'name'
            $tmptot = empty($tmptot) ? -1 : $tmptot;


            echo ApexchartsWidget::widget([
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
                        'name' => 'Monte Ore  ' . $tmptot,
                        'data' => $totalequotdiano,
                    ],
                    [
                        'name' => 'Totale Lavorato  ' . round(array_sum($totaleLavorato), 2),
                        'data' => $totaleLavorato,
                    ],
                    [
                        'name' => 'Totale Ticket   ' . round(array_sum($totaleTicket), 2),
                        'data' => $totaleTicket,
                    ],
                    [
                        'name' => 'Totale Trasferta   ' . round(array_sum($totaletrasferta), 2),
                        'data' => $totaletrasferta,
                    ],

                ]
            ]) ?>
        </div>
    </div>
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Rapporto Ore/attività</h5>

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
                    'labels' => ['Ore Lavorate', 'Ore Totali'],
                ],
                'series' => [array_sum($totaleLavorato), array_sum($totalequotdiano)],
            ]) ?>
        </div>
    </div>
    <div class="custom-card card">
        <div class="card-body">

            <?php
            //yii::warning($dataArray = $dataProvider->getModels());
            $dataArray = $results1; //$dataProvider->getModels();
            $etichetta1 = [];
            $serie1 = [];
            foreach ($dataArray as $result) {
                $etichetta1[] = $result['oggetto']; // Giorni per l'asse X
                $serie1[] = $result['oredelta']; // Somma ore lavorate
            }

            $result = [];

            // Itera attraverso i dati
            foreach ($dataArray as $entry) {

                $oggetto = $entry['oggetto'];
                $oredelta = floatval($entry['oredelta']); // Converti in numero decimale

                // Se la chiave esiste già, aggiungi al valore esistente
                if (isset($result[$oggetto])) {
                    $result[$oggetto] += $oredelta;
                } else {
                    // Altrimenti, inizializza con il valore corrente

                    $result[$oggetto] = $oredelta;
                }
            }

            $filteredData = array_filter($result, function ($value, $key) {
                return $key !== '';
            }, ARRAY_FILTER_USE_BOTH);
            //yii::error($dataArray);
            $labels1 = array_keys($filteredData);
            $series1 = array_values($filteredData);

            $labels1 = array_map(function ($label) {
                // Usa preg_replace per inserire '\n' dopo ogni 25 caratteri
                return wordwrap($label, 25, "<br>", true);
            }, $labels1);

            ?>

            <h5 class="card-title">
                Attività e peso orario</h5>

            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',

                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ]
                    ],
                    'dataLabels' => [
                        'enabled' => true,
                        'style' => [
                            'fontSize' => '16px', // Modifica la dimensione a piacimento
                        ]

                    ],
                    'legend' => ['fontSize' => '10px'],


                    'labels' => $labels1,
                ],
                'series' => $series1
            ]) ?>

        </div>
        <p class="card-text"><small class="text-muted"></small></p>
    </div>
</div>


<?php
$resultoperatore = [];

// Itera attraverso l'array di dati
foreach ($dataArray as $entry) {
    $utentecreatore = $entry['utente_destinatario'];
    $oredelta = floatval($entry['oredelta']); // Converti 'oredelta' in numero decimale

    // Se l'utentecreatore esiste già, aggiungi il valore di oredelta al totale
    if (isset($resultoperatore[$utentecreatore])) {
        $resultoperatore[$utentecreatore] += $oredelta;
    } else {
        // Altrimenti, inizializza con il valore corrente
        $resultoperatore[$utentecreatore] = $oredelta;
    }
}


$filteredDataOp = array_filter($resultoperatore, function ($value, $key) {
    return $key !== '';
}, ARRAY_FILTER_USE_BOTH);
//yii::error($dataArray);
$labels2 = array_keys($filteredDataOp);
$series2 = array_values($filteredDataOp);

//yii::error($results1);



?>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

    <div class=" custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Attivita per operatore</h5>

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
            <h5 class="card-title"> Stato X Attività</h5>
            <?php
            $resultstato = [];
            foreach ($dataArray as $result) {
                $value = $result['STATUS'];
                //var_dump($result);
                if (!isset($resultstato[$value])) {
                    $resultstato[$value] = 0;
                }
                $resultstato[$value]++;
            }
            $labelsstato = array_keys($resultstato);
            $seriesstato = array_values($resultstato);


            ?>
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
                    'labels' => $labelsstato,
                ],
                'series' => $seriesstato,
            ]) ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <div class="custom-card card">
        <div id="chart01"></div>
        <?php

        $categories = [];
        $monteoreSeries = [];
        $totaleoreSeries = [];

        foreach ($resultstotali as $item) {
            if ($item['tipo'] <> 'TP') {
                $categories[] = $item['projectname'];
                $monteoreSeries[] = Round($item['monteore'] ?? 0, 2); // Imposta a 0 se null
                $totaleoreSeries[] = round($item['totaleore'] * -1, 2);
            }
        }

        $chartData = [
            'categories' => $categories,
            'series' => [
                [
                    'name' => 'Monte Ore',
                    'data' => $monteoreSeries,
                ],
                [
                    'name' => 'Totale Ore',
                    'data' => $totaleoreSeries,
                ],
            ],
        ];
        //  yii::warning($chartData);
        $this->registerJs("
            var options = {
          series: " . json_encode($chartData['series']) . ",
          chart: {
          type: 'bar',
          height: 350,
          stacked: true,
        },
        plotOptions: {
          bar: {
            horizontal: false,
            dataLabels: {
              total: {
                enabled: true,
                offsetX: 0,
                formatter: function (val) {
          return Number(val).toFixed(2); // Arrotonda i totali
        },
                style: {
                  fontSize: '13px',
                  fontWeight: 900
                }
              }
            }
          },
        },
        stroke: {
          width: 1,
          colors: ['#fff']
        },
        title: {
          text: 'Andamento progetti e Monte Ore'
        },
        xaxis: {
          categories: " . json_encode($chartData['categories']) . "
         /* labels: {
            formatter: function (val) {
              return val + 'K'
            }
          }*/
        },
        yaxis: {
          title: {
            text: 'ORE'
          },
        },
        tooltip: {
          y: {
            formatter: function (val) {
               return parseFloat(val).toFixed(2) + 'H'
            }
          }
        },
        fill: {
          opacity: 1
        },
        legend: {
          position: 'top',
          horizontalAlign: 'left',
          offsetX: 40
        }
        };

        var chart = new ApexCharts(document.querySelector('#chart01'), options);
        chart.render();
            
            
            ", View::POS_READY); ?>
    </div>
    <div class="custom-card2 card">
        <div class="card-body">
            <h5 class="card-title">
                Timeline del progetto</h5>
            <?php //yii::warning($Timelineprogetti);

            // Definizione dell'array PHP
            /*  $zseries = [];

            // Itera attraverso i dati
            foreach ($Timelineprogetti as $item) {
                $codiceProgetto = $item['codiceprogetto'];

                // Se il progetto non è ancora presente, inizializzalo
                if (!isset($zseries[0])) {
                    $zseries[0] = ['data' => []];
                }

                // Aggiungi i dati all'array
                $zseries[0]['data'][] = [
                    'x' => $item['oggetto'],
                    'y' => [
                        strtotime($item['orainizioevento']) * 1000, // Convertito a millisecondi
                        strtotime($item['orafineevento']) * 1000    // Convertito a millisecondi
                    ]
                ];
            }
            // Mostra il risultato
            //yii::warning($zseries);

            //yii::error($pinos);
            //yii::error($filteredDataOp);
            $tseries = [
                ['data' => $filteredDataOp]
            ];

            $dataj = json_encode($zseries);*/

            $zseries2 = [];


            foreach ($Timelineprogetti as $item) {
                $codiceProgetto = $item['codiceprogetto'];

                // Se il progetto non è ancora presente, inizializzalo
                if (!isset($zseries2[$codiceProgetto])) {
                    $zseries2[$codiceProgetto] = [
                        'name' => $codiceProgetto,
                        'data' => [],
                    ];
                }
                $starttimestamp =     (new DateTime(
                    $item['orainizioevento'],
                    new DateTimeZone('Europe/Rome')
                ))->getTimestamp() * 1000;

                $endDateTime = new DateTime(
                    $item['orafineevento'],
                    new DateTimeZone('Europe/Rome')
                );
                $endDateTime->setTimezone(new DateTimeZone('UTC'));
                $endDateTime->modify('-1 day');
                $endTimestamp = $endDateTime->getTimestamp() * 1000;
                $zseries2[$codiceProgetto]['data'][] = [
                    'x' => $item['oggetto'],
                    'y' => [
                        // strtotime($item['orainizioevento']) * 1000, // In millisecondi
                        // strtotime($item['orafineevento']) * 1000,  // In millisecondi
                        $starttimestamp,        /*((new DateTime($item['orafineevento'], 
                        new DateTimeZone('Europe/Rome')))->getTimestamp() - 1)
                         * 1000
                         */
                        $endTimestamp,


                    ],
                ];
            }

            // Reindicizza l'array per ApexCharts
            $zseries2 = array_values($zseries2);

            $dataj2 = json_encode($zseries2);


            //yii::warning($dataj);
            //yii::warning($dataj2);






            ?>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
            <style>
                #chart {
                    width: 100%;
                    height: 100%;
                }
            </style>
            <div id="chart"></div>
            <?php
            $this->registerJs("
    var seriesData = " . $dataj2 . ";



    console.log(seriesData);
    
                     var options = {
          series: seriesData
        ,
        chart: {
                height: 700,
                type: 'rangeBar'
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '100%'
                }
            },
            xaxis: {
                type: 'datetime',
                labels: {
        datetimeUTC: false, // Disabilita l'interpretazione come UTC
        format: 'dd MMM yyyy HH:mm',
    },
            },
            tooltip: {
    x: {
        format: 'dd MMM yyyy HH:mm', // Formato tooltip
    },
},
            stroke: {
                width: 1
            },
            fill: {
                type: 'solid',
                opacity: 0.6
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            }
        };

        var chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    ", View::POS_READY); ?>

        </div>
    </div>
</div>




<style>
    .gantt_control.gantt_add {
        display: none !important;
    }
</style>
<?php

// $times = Agenda::find()
// ->asArray()
// ->all();
//   yii::warning($times);
$this->registerCssFile('@web/gantt/dhtmlxgantt.css');
$this->registerJsFile('@web/gantt/dhtmlxgantt.js');
$this->registerJsFile('@web/gantt/moment.js');
//$this->registerJsFile('@web/gantt/locale/locale-it.js');

$this->registerCss("
    #ganttContainer {
        width: 100%;
        height: 600px; /* Modifica l'altezza a tua scelta */
         overflow: scroll; /* Abilita le barre di scorrimento */
        scrollbar-width: auto; /* Per i browser compatibili */
        scrollbar-color: #888 #f1f1f1; /* Colore della barra di scorrimento */
    }
    #ganttContainer::-webkit-scrollbar {
        width: 10px; /* Larghezza della barra di scorrimento */
        height: 10px; /* Altezza della barra di scorrimento */
    }

    #ganttContainer::-webkit-scrollbar-thumb {
        background: #888; /* Colore del cursore */
        border-radius: 10px; /* Forma arrotondata */
    }

    #ganttContainer::-webkit-scrollbar-thumb:hover {
        background: #555; /* Colore al passaggio del mouse */
    }

    #ganttContainer::-webkit-scrollbar-track {
        background: #f1f1f1; /* Colore dello sfondo */
    }
");

$ganttData = [];

//$query2 = $db->createCommand($query2)->queryAll();

foreach ($query as $time) {
    //$ganttData[];
    $perc = intval($time['progress']);
    // yii::error($perc . '-' . $time['codiceprogetto']);
    if ($perc == 0) {
        $color = 'Default';
    } elseif ($perc < 50) {

        $color = 'MediumSlateBlue';
    } elseif ($perc > 50) {
        $color = 'Orange';
    } elseif ($perc == 100) {
        $color = 'LimeGreen';
    }
    //    yii::error($color);

    $row = [
        'id'         => $time['id'],
        'text'       => str_replace(
            "'",
            " ",
            ($time['codiceprogetto'] ?? $time['codiceprogetto']) .
                ' [' . $time['progress'] . ']'
        ),
        'start_date' => date("d-m-Y", strtotime($time['da'])),
        'end_date'   => date("d-m-Y", strtotime($time['a'])),
        'open'       => false,
        "progress"   => intval(str_replace('%', '', $time['progress'])) / 100,
        'color'      => $color,
    ];
    $ganttData[] = $row;
}

$ganttData2 = [];
foreach ($query2 as $time2) {


    $perc = intval($time2['progress']);
    if (0 == $perc) {
        $color = 'Default';
    } elseif ($perc < 50) {

        $color = 'MediumSlateBlue';
    } elseif ($perc > 50) {
        $color = 'Orange';
    } elseif ($perc  = 100) {
        $color = 'LimeGreen';
    }



    if (date("d-m-Y", strtotime($time2['a'])) == date("d-m-Y", strtotime($time2['da']))) {
        $df      = date("d-m-Y", strtotime($time2['a']));
        $df      = date("d-m-Y", strtotime("+1 day", strtotime($df)));
        $newDate = $df;
    } else {
        $newDate = date("d-m-Y", strtotime($time2['a']));
    }
    $row2 = [
        'id'         => $time2['tid'] ?? 999,
        'text'       => str_replace("'", " ", ($time2['oggetto'] ?? 'vericare campo task')
            . ' [' . $time2['progress'] . ']'),
        'start_date' => date("d-m-Y", strtotime($time2['da'])),
        'end_date'   => $newDate,
        'parent'     => $time2['id'],
        "progress"   => intval(str_replace('%', '', $time2['progress'])) / 100,
        "color"      => $color,

    ];
    $ganttData2[] = $row2;
}
$query3 = '';
$ganttDataJson2 = json_encode($ganttData2);
$ganttDataJson1 = json_encode($ganttData);
$ganttDataJson = json_encode(array_merge(json_decode($ganttDataJson2, true), json_decode($ganttDataJson1, true)));


//yii::warning(($ganttDataJson2));
$proc = <<<EOF
var colors = [
		{key: "", label: "Default"},
		{key: "#4B0082", label: "Indigo"},
		{key: "#FFFFF0", label: "Ivory"},
		{key: "#F0E68C", label: "Khaki"},
		{key: "#B0C4DE", label: "LightSteelBlue"},
		{key: "#32CD32", label: "LimeGreen"},
		{key: "#7B68EE", label: "MediumSlateBlue"},
		{key: "#FFA500", label: "Orange"},
		{key: "#FF4500", label: "OrangeRed"},

	];

gantt.config.columns = [
		{name: "text", label:"Progetto", tree: true, width: 300, min_width: 300, resize: true},
		{name: "start_date",label:"Inizio", align: "center", resize: true,width: 300, min_width: 300,},
		//{name: "duration",label:"Durata", align: "center",width: 300, min_width: 300,},
     {name: "sp",label:"", align: "center",width: 1, resize: true},
    {name: "end_date",label:"Fine", align: "center", resize: true},
    {name: "sp",label:"", align: "left", width: 1,resize: true},

  ];
gantt.config.scales = [
		{unit: "month", step: 1, format: "%F, %Y"},
		{unit: "day", step: 1, format: "%j, %D"}
	];
gantt . i18n . setLocale("it");

// Inizializza il grafico di Gantt
    gantt.init('ganttContainer');



   // var ganttData = JSON.parse('$ganttDataJson');
   // for (var i = 0; i < ganttData.length; i++) {
   //     var task = ganttData[i];
   //     task.start_date = moment(task.start_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
   // }
    gantt.parse({data: ganttData});
gantt.config.readonly=true;
<div id="ganttContainer"></div>
EOF;
//$this->registerJs($proc);

?>

<!-- HTML markup per il contenitore del grafico di Gantt -->









<?php
//yii::error($dataProvider);
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => null, // O il tuo SearchModel se ne hai uno
    'pjax' => true,        // Consigliato per non ricaricare tutta la pagina
    'export' => [
        'fontAwesome' => true,
        'showConfirmAlert' => false,
        'target' => GridView::TARGET_BLANK,
    ],
    'columns' => [
        "soggetto",
        "dataevento",
        "AREA",
        "TIPOEVENTO",
        "orainizioevento",
        "orafineevento",
        "oredelta",
        "codicesoggetto",
        "codicestatoevento",
        "oggetto",
        "noteevento",
        "codiceprogetto",
        "Custom1",
        "custom2",
        "custom3",
        "custom4",
        "custom5",
        "STATUS",
        "descrizioneprogetto",
        "utentecreatore",
        "utente_destinatario",
        "dataprevistachiusura",
        "pid",
        "tid",
        "tipo",
        "xtipologia",
        "monteore",
        "projectid",
        "projectname",
        "project_no",
        "startdate",
        "targetenddate",
        "actualenddate",
        "targetbudget",
        "projecturl",
        "projectstatus",
        "projectpriority",
        "projecttype",
        "progress",
        "linktoaccountscontacts",
        "tags",
        "isconvertedfrompotential",
        "potentialid",
    ],
    'toolbar' => [
        '{export}',
        '{toggleData}',
    ],
    'exportConfig' => [
        GridView::CSV => ['label' => 'Esporta CSV'],
        GridView::EXCEL => ['label' => 'Esporta Excel'],
    ],
    'panel' => [
        'type' => GridView::TYPE_PRIMARY,
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-list-alt"></i> Risultati</h3>',
    ],
]);
?>


<?php

// CSS per DataTables e Bottoni
$this->registerCssFile('https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css');
$this->registerCssFile('https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css');

// JS necessari
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

// Librerie per Export (Excel, PDF, CSV)
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// Assicurati che in cima alla vista ci siano questi con 'depends'
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);

//yii::warning($resultstotali);
?>



<?php
// Disabilita la paginazione del dataProvider per passare tutti i dati a DataTables
$dataProvider->pagination = false;
$models = $dataProvider->getModels();

// Definiamo le colonne in un array per generare l'header e il body velocemente
$colonne = [
    "soggetto",
    "dataevento",
    "AREA",
    "TIPOEVENTO",
    "orainizioevento",
    "orafineevento",
    "oredelta",
    "codicesoggetto",
    "codicestatoevento",
    "oggetto",
    "noteevento",
    "codiceprogetto",
    "Custom1",
    "custom2",
    "custom3",
    "custom4",
    "custom5",
    "STATUS",
    "descrizioneprogetto",
    "utentecreatore",
    "utente_destinatario",
    "dataprevistachiusura",
    "pid",
    "tid",
    "tipo",
    "xtipologia",
    "monteore",
    "projectid",
    "projectname",
    "project_no",
    "startdate",
    "targetenddate",
    "actualenddate",
    "targetbudget",
    "projecturl",
    "projectstatus",
    "projectpriority",
    "projecttype",
    "progress",
    "linktoaccountscontacts",
    "tags",
    "isconvertedfrompotential",
    "potentialid"
];
?>

<div class="custom-card2 card" style="padding: 20px; background: white;">
    <h3 class="panel-title" style="margin-bottom: 20px;"><i class="glyphicon glyphicon-list-alt"></i> Risultati Esportabili</h3>

    <div class="table-responsive">
        <table id="datatable-export" class="table table-striped table-bordered nowrap" style="width:100%">
            <thead>
                <tr>
                    <?php foreach ($colonne as $col): ?>
                        <th><?= strtoupper(str_replace('_', ' ', $col)) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $row): ?>
                    <tr>
                        <?php foreach ($colonne as $col): ?>
                            <td><?= \yii\helpers\Html::encode($row[$col] ?? '') ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$js = <<<JS
$(document).ready(function() {
    // Distruggi eventuali istanze precedenti per evitare duplicati
    if ($.fn.DataTable.isDataTable('#datatable-export')) {
        $('#datatable-export').DataTable().destroy();
    }

    var table = $('#datatable-export').DataTable({
        "dom": '<"row"<"col-md-6"B><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
        "buttons": [
            { 
                extend: 'copy', 
                className: 'btn btn-secondary', 
                text: 'Copia Dati'
            },
            { 
                extend: 'excel', 
                className: 'btn btn-success', 
                text: 'Scarica Excel' 
            },
            { 
                extend: 'csv', 
                className: 'btn btn-info', 
                text: 'Scarica CSV' 
            },
            { 
                extend: 'print', 
                className: 'btn btn-primary', 
                text: 'Stampa Tabella' 
            }
        ],
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json" },
        "paging": true,
        "pageLength": 25,
        "scrollX": true, // Fondamentale per le tue 40+ colonne
        "searching": true,
        "ordering": true,
        "info": true
    });
});
JS;
$this->registerJs($js);
?>