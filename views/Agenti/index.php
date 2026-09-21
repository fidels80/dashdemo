<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<?php
/* @var $this yii\web\View */
/* @var $result array */
yii::error($eleagenti);
use onmotion\apexcharts\ApexchartsWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
$agents = [];
foreach ($eleagenti as $agente) {
    $agents[$agente['cd_agente']] = $agente['descrizione'];
}

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
');




?>
<div class="container">
  <div class="row">
    <div class="col-sm">
    
    </div>
    <div class="col-md">
      <div class="select-container">
    <?= Select2::widget([
        'name' => 'cd_agente',
        'data' => $agents,
        'options' => [
            'id' => 'cd-agente-select',
            'placeholder' => 'Seleziona un agente...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]) ?>
    <?= Html::button('Invia', ['class' => 'btn btn-primary', 'id' => 'submit-button']) ?>
</div>
    </div>
    <div class="col-sm">
      
    </div>
  </div>
</div>


<?php
$this->registerJs('
    $("#submit-button").on("click", function() {
        var cdAgente = $("#cd-agente-select").val();
        if (cdAgente) {
            window.location.href = "' . Url::to(['agenti/index']) . '&cdAgente=" + cdAgente;
        } else {
            alert("Seleziona un agente.");
        }
    });
');
?>



<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="full-screen-container">
        Percentuale Maturata
        <div class="AGE-details">
            <?= ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '400px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => ['Non Maturata', 'Maturata Da Liquidare'],
                ],
                'series' => [$sumNonMaturata, $sumMaturataDaLiquidare],
            ]) ?>
        </div>
    </div>

    <div class="full-screen-container">
        <div class="AGE-details">
            Fattutato e Provvigione per Cliente
            <div class="AGE-title"></div>


            <?= ApexchartsWidget::widget([
                'type' => 'bar',
                'height' => '310',
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
                            'text' => 'Valori (€)'
                        ]
                    ],
                    'fill' => [
                        'opacity' => 1
                    ],
                    'tooltip' => [
                        'y' => [
                            'formatter' => new \yii\web\JsExpression("function (val) {
                    return val + ' €';
                }")
                        ]
                    ]
                ],
                'series' => [
                    [
                        'name' => 'Totale Fatturato',
                        'data' => $totaleFatturato
                    ],
                    [
                        'name' => 'Totale Provvigioni',
                        'data' => $totaleProvvigioni
                    ],
                ]
            ]) ?>

        </div>
    </div>

    <div class="full-screen-container">
        <div class="AGE-details">
            <div class="AGE-title"><?php //Html::encode("Ordini") 
                                    ?></div>
            <div class="AGE-info">
                <span class="AGE-info-label">Agente</span>
                <span class="AGE-info-value"><?php //echo  date('d/m/Y', $randomTimestamp); 
                                                yii::warning($result3);
                                                //echo $result3[0]['Cd_Agente'] . ' ' .
                                                 $result3[0]['Descrizione'];
                                                ?></span>
            </div>
            <div class="AGE-info">
                <span class="AGE-info-label">Indirizzo</span>
                <span class="AGE-info-value"><?php  //echo $randomNumber 
                                                echo $result3[0]['Indirizzo']
                                                ?></span>
            </div>
            <div class="AGE-info">
                <span class="AGE-info-label">Località</span>
                <span class="AGE-info-value"><?php  //echo $randomNumber 
                                                echo $result3[0]['Localita']
                                                ?></span>
            </div>
            <div class="AGE-info">
                <span class="AGE-info-label">Codice Fiscale</span>
                <span class="AGE-info-value"><?php  //echo $randomNumberWithDecimals 
                                                echo $result3[0]['CodiceFiscale']     ?> </span>

            </div>
            <div class="AGE-info">
                <span class="AGE-info-label">Partita Iva</span>
                <span class="AGE-info-value"><?php  //echo $randomNumberWithDecimals 
                                                echo $result3[0]['PartitaIva']     ?> </span>

            </div>
            <div class="AGE-info">
                <h5> I dati presenti in questa scheda sono a mero scopo dimostrativo e
                    non rappresentano la realtà ma quello che potremmo inserire all'interno
                    di questa scheda</h5>
            </div>
        </div>
    </div>
    <!-- Ripetere il blocco full-screen-container per ogni libro -->
    <!-- Esempio: -->
    <!-- <div class="full-screen-container"> ... </div> -->
    <div class="full-screen-container full-width">
        <div class="AGE-details">
            <div class="AGE-title"><?php //Html::encode("Ordini") 
                                    ?></div>

            <?php
            $series = [];
            foreach ($data as $cdCf => $values) {
                $series[] = [
                    'name' => $cdCf,
                    'data' => $values,
                ];
            }

            echo ApexchartsWidget::widget([
                'type' => 'line',
                'height' => '400',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true,
                        ],
                    ],
                    'xaxis' => [
                        'type' => 'datetime',
                    ],
                    'yaxis' => [
                        'title' => [
                            'text' => 'Importo Fatturato (€)',
                        ],
                    ],
                    'tooltip' => [
                        'x' => [
                            'format' => 'dd MMM yyyy',
                        ],
                    ],
                ],
                'series' => $series,
            ]);
            ?>
        </div>
    </div>
</div>