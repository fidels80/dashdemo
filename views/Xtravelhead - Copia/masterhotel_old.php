 
<?php

$this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);


use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;

use onmotion\apexcharts\ApexchartsWidget;
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);



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
    width: 50%;
min-height: 500px;
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


');

?>
<div class="xtravelhead-view">
    <p></p>
    <table>
        <tr>
            <td width='50%'>
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'th_id',

                        [                      // the owner name of the model
                            'label' => 'Data',
                            'value' => date('d/m/Y', strtotime($model->datath))
                        ],
                        'numero',
                        'descrizione',

                        [                      // the owner name of the model
                            'label' => 'Totale Tasse',
                            'value' => '€' . round($model->tax, 2),
                        ],
                        [                      // the owner name of the model
                            'label' => 'Totale Fee',
                            'value' => '€' . round($model->fee, 2),
                        ],
                        [                      // the owner name of the model
                            'label' => 'Totale Servizi',
                            'value' => '€' . round($model->totaleservizi, 2),
                        ],
                        [                      // the owner name of the model
                            'label' => 'Totale Fatturato',
                            'value' => '€' . round($model->totft, 2),
                        ],


                    ],
                ]) ?>
            </td>
            <td>
                <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
                echo '<thead>';
                echo '<tr>';
                //echo '<th>id_agenda</th>';
                echo '<th width="70%">file</th>';
                echo '<th width="30%">';
                echo '</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                //yii::error( ($model->xid_testa ));
                //yii::warning($model->filesall);
                foreach ($model->filesall as $value) {
                    // echo '<tr>';

                    if ($value['entita'] = 'Xtravelrow') {
                        echo '<tr>';
                        echo '<td>';

                        echo Html::a(
                            $value['nomefile'],
                            [
                                'allfiles/genfile',
                                'id' => $value['id'],
                                'file' => str_replace(' ', '_', $value['nomefile'])
                            ]
                        );

                        echo '</td>';

                        echo '<td>';

                        if ($value['origine'] == 'S') {
                            //echo 'usrld';

                            echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                            Yii::$app->fontawesome->name(
                                'user',
                                'solid'
                            )->fill('#003865');
                        } else {

                            echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
                            Yii::$app->fontawesome->name(
                                'server',
                                'solid'
                            )->fill('#003865');
                        }

                        echo '</td>';

                        echo '</tr>';
                    }
                }
                echo '<tr id="files-new-parcel-block" style="display: none;">';
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                ?>















            </td>
        </tr>
    </table>
</div>



<?php
/*<!-div class="form-group"->
    <!-input id="search-input" type="text" class="form-control" placeholder="Cerca...">
</div>
*/
$tappe2 = array_map(function ($tappe) {
    unset($tappe['check_in']);
    return $tappe;
}, $tappe);

$uniqueTappe = [];
foreach ($tappe2 as $tappa) {
    $key = $tappa['citta'] . '_' . $tappa['cd_cf_ft']; // Combina citta e cd_cf_ft per creare una chiave unica
    if (!isset($uniqueTappe[$key])) {
        $uniqueTappe[$key] = $tappa;
    }
}

yii::warning($recap);
$items = [];
$items[] = [
    'label' => '<i class="fas fa-toolbox"> Tools</i>',
    'id' => 'tools',
    'content' =>
    $this->render('_tools', [
        'dettaglio' => $dettaglio,
        'th_id'=>$model->th_id,
        'pivot' => $pivot,
        'labels' => $labels,
        'series' => $series,
        'tappe'=> $uniqueTappe,
        'roomlist'=>$roomlist,
        'analisitappe' => $analisitappe,
        'listatappetool'=> $listatappetool,
        'listaart'=> $listaart
    ])
    // 'active' => true,
];
$items[] = [
    'label' => '<i class="fas fa-user"> Cost Analisys</i>',
    'id'=>'costanalsys',
    'content' =>
    $this->render('_analisi', [
        'dettaglio' => $dettaglio,
        
        'pivot'=>$pivot,
        'labels' => $labels,
        'series' => $series,
       
        'analisitappe' => $analisitappe,
    ])
    // 'active' => true,
];
$items[] = [
    'label' => '<i class="fas fa-user"> Room List</i>',
    'id'=>'roomlist',
    'content' =>
    $this->render('_roomlist', [
        'roomlist' => $roomlist,
        'tipo'=>1
   ])
    // 'active' => true,
];
$items[] = [
    'label' => '<i class="fas fa-user"> Riepilogo</i>',
    'id' => 'roomlist2',
    'content' =>
    $this->render('_roomlist', [
        'roomlist' => $recap,
        'tipo'=>2
    ]),
    'active' => true
    // 'active' => true,
];
$i=1;
foreach ($uniqueTappe as $key => $value) {
   /* old_ rimosso il 29/08/24
   $items[] = [
        'label' => '<i class="fas fa-home">' . $value['citta'] . '</i>',
 
        'linkOptions' => ['data-url' => Url::to([
            '/xtravelhead/loadtappa',
            'th_id' => $model->th_id,
            'cliente' => $value['cd_cf_ft'],
            'citta' => $value['citta']
        ])],
        //' - ' . $value['citta'] . ' - ' . $value['cd_cf_ft'],
        // 'active' => true,
    ];
*/
    /*$this->renderajax('_detail', ['dettaglio'=> $results,
'th_id'=>$th_id,
'cli'=>$cliente,
'citta'=>$citta,
'totale'=>$totale,
'pagamenti'=>$pagamenti]);*/

$r=xxLoadtappa2($model->th_id, $value['cd_cf_ft'], $value['citta']);
    $items[] = [
        'label' =>
        '<i class="fas fa-home">' . $value['citta'] . '</i>',
        'id' => 'dettaglio_'.$i,
        'content' =>
        $this->render('_detail2', [
                'dettaglio' =>$r['dettaglio'],
                'th_id' => $r['th_id'],
                'cli' => $r['cli'],
                'citta' => $r['citta'],
                'totale' => $r['totale'],
                'pagamenti' => $r['pagamenti']
            
        ]),

    ];
$i++;



}
 
 
 echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_LEFT,
    'bordered' => true,
    'encodeLabels' => false,
    'pluginOptions'=>[
        'enableCache'=>false,
    ]
]);
?>


 




<script>/*
    document.addEventListener('DOMContentLoaded', function() {
        function calculateTotals() {
            let totalQuantity = 0;
            let totalPrice = 0;
            let totalIva = 0;
            let totalTax = 0;
            let totalFee = 0;
            let totalImponibile = 0;
            let totalTotal = 0;

            const rows = document.querySelectorAll('#xtravel-rows-body tr');

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const cells = row.querySelectorAll('td');
                    totalQuantity += parseFloat(cells[14].textContent) || 0;
                    totalPrice += parseFloat(cells[17].textContent) || 0;
                    totalIva += parseFloat(cells[18].textContent) || 0;
                    totalTax += parseFloat(cells[19].textContent) || 0;
                    totalFee += parseFloat(cells[20].textContent) || 0;
                    totalImponibile += parseFloat(cells[21].textContent) || 0;
                    totalTotal += parseFloat(cells[22].textContent) || 0;
                }
            });

            document.getElementById('total-quantity').textContent = totalQuantity.toFixed(2);
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
            document.getElementById('total-iva').textContent = totalIva.toFixed(2);
            document.getElementById('total-tax').textContent = totalTax.toFixed(2);
            document.getElementById('total-fee').textContent = totalFee.toFixed(2);
            document.getElementById('total-imponibile').textContent = totalImponibile.toFixed(2);
            document.getElementById('total-total').textContent = totalTotal.toFixed(2);
        }

        var searchInput = document.getElementById('search-input');
        var table = document.getElementById('xtravel-rows');
        var tableRows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            var filter = searchInput.value.toLowerCase();

            for (var i = 1; i < tableRows.length; i++) {
                var row = tableRows[i];
                var cells = row.getElementsByTagName('td');
                var match = false;

                for (var j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        match = true;
                        break;
                    }
                }

                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
            calculateTotals();
        });
    });*/
</script>




<?php

 function xxLoadtappa2($th_id, $cliente, $citta)
    {
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses[] = "citta='" . str_replace("'", "''", $citta) . "'";
        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";



            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses2[] = "citta='" . str_replace("'", "''", $citta) . "'";
        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);
        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $pagamenti = $command->queryAll();
        // Yii::debug("SQL Query: $sql");
        //Yii::debug("Params: " . json_encode($params));
        //   var_dump(empty($pagamenti));
        if (1 == 2) {
            //(empty($pagamenti)==true ){
            var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;

            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses3[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
            $where3 = 'WHERE ' . implode(' AND ', $whereClauses2);


            $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where3  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
        $dettatappa= [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
        return $dettatappa;
    }

?>