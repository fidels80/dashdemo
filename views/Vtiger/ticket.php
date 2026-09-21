<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use onmotion\apexcharts\ApexchartsWidget;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $listaclienti array */
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
 
$listatc = [];
yii::error($tracking);

 
?>

<?php 
//echo  (Yii::$app->request->get('listap')!=null) ? 'Stai vedendo i dati per il progetto <B>'.Yii::$app->request->get('listap').'</B><br>' :'';
//echo  (Yii::$app->request->get('listac')!=null) ? 'Stai vedendo i dati per il cliente <B>'.Yii::$app->request->get('listac').'</B><br>' :'';
?>




<div class="d-flex flex-wrap justify-content-center align-items-start full-width">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline row g-3'], // Aggiungi la classe g-3 per spaziatura tra le colonne
    ]); ?>

    <div class="form-row">
        <!-- Giorno Da -->
         <table class="table">
            <tr>
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
</tr><tr>
        <td>

            <?= Html::label('Clienti', 'Clienti', ['class' => 'form-label']) ?>
            <?= Select2::widget([
                'name' => 'listac',
                'value'=>$listatc,
               
                'data' => $listac,
                'options' => ['placeholder' => 'Seleziona Cliente', 'class' => 'form-control select2', ],
                'pluginOptions' => ['allowClear' => true,'multiple'=>true],
            ]) ?>
        </td>

        
        <!-- Bottone di ricerca -->
        <td>
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-block']) ?>
            </td>

        <!-- Bottone di reset -->
        <td>
            </tr>  
            <tr>  
            <td>  
             <button type="button" class="btn btn-secondary btn-block"
              id="resetFilters">Reset</button>
            </td>
        <td>
            <button type="button" class="btn btn-primary btn-block" id="aprivtiger">Apri Vtiger</button>
            </td>
            <td>
            <?php echo Html::a('Apri Operatori', ['vtiger/search'], [
    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
]);?>
            </td>
</tr>
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


       // yii::warning($cdCfs);
?>


<div class="d-flex flex-wrap justify-content-center align-items-start">
       <div class="custom-card card" >
    <div class="card-body">
       <h5  class="card-title">
        Tipi eventi assistenza</h5>
   
            <?php     
 


            echo  ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => array_column($resultstipoass, 'name')//$listaeventi,
                ],
                'series' => array_column($resultstipoass, 'data') ,
            ])  ?>
        </div>
    </div>
       <div class="custom-card card" >
    <div class="card-body">
       <h5  class="card-title">
        Prodotti</h5>
         
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
                    'labels' => array_column($resultsprod , 'name')
                ],
                'series' => array_column($resultsprod , 'data'),
            ]) ?>
        </div>
    </div>
    <div class="custom-card card" >
    <div class="card-body">

<?php 
//yii::warning($dataArray = $dataProvider->getModels());

?>

       <h5  class="card-title">
        Prodotti Complementari</h5>
        
            <?=  ApexchartsWidget::widget([
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
        'legend'=>['fontSize'=>'10px'],

   
        'labels' =>  array_column($resultscomprod , 'name') //$labels1,
    ],
    'series' =>  array_column($resultscomprod , 'data')
]) ?>
 
        </div> <p class="card-text"><small class="text-muted"></small></p>
    </div>
</div>


<?php

 


?>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

  <div class=" custom-card card" >
    <div class="card-body">
      <h5  class="card-title"> Stato Assistenze</h5>
         
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
                    'labels' => array_column($resultsstato,'name'),
                ],
                'series' => array_column($resultsstato,'data'),
            ]) ?>
        
            </div>
    </div>

<div class="custom-card card" >
    <div class="card-body">
       <h5  class="card-title">Operatori per Ore ticket</h5>
                   <?php $nomi = array_column($resultsutentiore, 'name');
$stringa_nomi = "'" . implode("','", $nomi) . "'";
$dati = array_column($resultsutentiore, 'data');
$stringa_dati = implode(', ', $dati);

 //yii::error(json_encode($stringa_nomi)  );
?>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
             <style>
        #chart {
            width: 100%;
            height: 450px;
        }
    </style>
            <div id="chart"></div>
       <?php
    $this->registerJs("
    var seriesData = " . json_encode(array_column($resultsutenti,'name')) . ";
    console.log(seriesData);
                     var options = {
          series: [{
          data: [".$stringa_dati."]
        }],
          chart: {
          type: 'bar',
          height: 270
        },
        plotOptions: {
          bar: {
            borderRadius: 2,
            borderRadiusApplication: 'end',
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: [".$stringa_nomi."
          ],
        }
        };

        var chart = new ApexCharts(document.querySelector('#chart'), options);
        chart.render();
    ", View::POS_READY);?>
        </div>
    </div>


    
   <div class="custom-card card" >
    <div class="card-body">
       <h5  class="card-title">
        Ticket per Operatori</h5>
        <?php //yii::warning($Timelineprogetti);

// Definizione dell'array PHP
        


 //yii::error(array_column($resultsutenti , 'data'));

        ?>



                   <?php $nomi = array_column($resultsutenti, 'name');
$stringa_nomi = "'" . implode("','", $nomi) . "'";
$dati = array_column($resultsutenti, 'data');
$stringa_dati = implode(', ', $dati);

 //yii::error(json_encode($stringa_nomi)  );
?>
            <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
             <style>
        #chart {
            width: 100%;
            height: 450px;
        }
    </style>
            <div id="chart1"></div>
       <?php
    $this->registerJs("
    var seriesData = " . json_encode(array_column($resultsutenti,'name')) . ";
    console.log(seriesData);
                     var options = {
          series: [{
          data: [".$stringa_dati."]
        }],
          chart: {
          type: 'bar',
          height: 270
        },
        plotOptions: {
          bar: {
            borderRadius: 2,
            borderRadiusApplication: 'end',
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: false
        },
        xaxis: {
          categories: [".$stringa_nomi."
          ],
        }
        };

        var chart = new ApexCharts(document.querySelector('#chart1'), options);
        chart.render();
    ", View::POS_READY);?>
                
        </div>
    </div>
</div>


<div class="full-screen-container full-width">
    <div class="AGE-details">
        <div class="AGE-title"><?php //Html::encode("Ordini") ?>
           <h5  class="card-title">
        Ticket per Cliente</h5>
        </div>

        <?php
        echo  ApexchartsWidget::widget([
                'type' => 'pie',
                'height' => '270px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                    ],
                    'labels' => array_column(   $resultscf,'name'),
                ],
                'series' => array_column(   $resultscf,'data'),
            ]);
        ?>
    </div>
</div>






<?php
//yii::error($dataProvider);
echo GridView::widget([
    'dataProvider' => $dataProvider,
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
            "pid","tid",	
            "tipo",
            "xtipologia",
            "ticketid",
            "from_portal",
            "cf_871",
            "cf_873",
            "cf_879",
            "cf_881",
            "cf_883",
            "cf_885",
            "cf_887",
            "cf_889",
            "cf_891",
            "cf_893",
            "cf_903",
            "cf_905",
            "cf_907",
            "cf_909",
            "ticketid",
            "ticket_no",
            "groupname",
            "parent_id",
            "product_id",
            "priority",
            "severity",
            "status",
            "category",
            "title",
            "solution",
            "update_log",
            "version_id",
            "hours",
            "days",
            "contact_id",
            "tags",
            "productid",
            "product_no",
            "productname",
            "productcode",
            "productcategory",
            "manufacturer",
            "qty_per_unit",
            "unit_price",
            "weight",
            "pack_size",
            "sales_start_date",
            "sales_end_date",
            "start_date",
            "expiry_date",
            "cost_factor",
            "commissionrate",
            "commissionmethod",
            "discontinued",
            "usageunit",
            "reorderlevel",
            "website",
            "taxclass",
            "mfr_part_no",
            "vendor_part_no",
            "serialno",
            "qtyinstock",
            "productsheet",
            "qtyindemand",
            "glacct",
            	"vendor_id",
                	"imagename",	"currency_id",	"is_subproducts_viewable",
                	"purchase_cost"	,"tags"

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
 

 <?php 

 
 
 
 //yii::warning($resultstotali);