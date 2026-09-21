 <?php

    $this->registerCssFile(
        '@web/css/custom-styles.css',
        ['depends' => [\yii\web\YiiAsset::class]]
    );


    use yii\helpers\Html;
    use yii\widgets\DetailView;
    use kartik\tabs\TabsX;
    use yii\helpers\Url;
    use yii\helpers\ArrayHelper;
    use kartik\nav\NavX;
    use kartik\select2\Select2;
    use onmotion\apexcharts\ApexchartsWidget;
    use yii\helpers\Json;
    use kartik\dialog\Dialog;
    use yii\web\JsExpression;
    use yii\data\ArrayDataProvider;
    use kartik\export\ExportMenu;
    use yii\bootstrap4\Modal;
    use app\models\XVenue;
    use PHPUnit\Framework\Constraint\IsNull;
    use yii\web\View;
    /* @var $this yii\web\View */
    /* @var $model app\models\Xtravelhead */

    $this->title = $model->descrizione;
    // $this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
    //$this->params['breadcrumbs'][] = $this->title;
    \yii\web\YiiAsset::register($this);
    $connection = Yii::$app->db5;
    $command = $connection->createCommand(
        "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
    );
    $tipo = $command->bindValue(':id', $model->th_id)->queryOne();
    function formatEuro($number)
    {
        return number_format($number, 2, ',', '.'); // 2 decimali, ',' come separatore decimali, '.' come separatore migliaia
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
min-height: 565px;
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
.hidden {
display: none !important;
visibility: hidden !important;
}



');


    $roomlist2 = $roomlist;
    function isValidUuid($uuid)
    {
        return preg_match('/^\{?[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}\}?$/', $uuid);
    }

    ?>

 <style>
     #rotate-notice {
         display: none;
         position: fixed;
         inset: 0;
         background: #0009;
         color: white;
         font-size: 1.5em;
         text-align: center;
         justify-content: center;
         align-items: center;
         z-index: 9999;
     }

     @media screen and (orientation: portrait) {
         #rotate-notice {
             display: flex;
         }
     }
 </style>

 <div id="rotate-notice">
     Ruota il dispositivo in orizzontale per continuare 🔄
 </div>
 <div class="xtravelhead-view">
     <p></p>
     <table>
         <tr>
             <td width='50%'>
                 <?php /*echo DetailView::widget([
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
                    ])*/ ?>
             </td>
             <td>
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




    //  yii::warning($recap);
    $items = [];
    $items[] = [
        'label' => ' ',
        'id' => 's',
        'content' => '',

        'active' => true,
    ];
    $items[] = [
        'label' => '<i class="fas fa-toolbox"> Tools</i>',
        'id' => 'tools',
        'content' =>
        $this->render('_tools', [
            'dettaglio' => $dettaglio,
            'th_id' => $model->th_id,
            'pivot' => $pivot,
            'labels' => $labels,
            'series' => $series,
            'tappe' => $uniqueTappe,
            'roomlist' => $roomlist,
            'analisitappe' => $analisitappe,
            'listatappetool' => $listatappetool,
            'listaart' => $listaart
        ])
        // 'active' => true,
    ];

    $dettaglio_ = $dettaglio;
    $pivot_ = $pivot;
    $labels_ = $labels;
    $series_ = $series;
    $uniqueTappe_ = $uniqueTappe;
    $roomlist_ = $roomlist;
    $analisitappe_ = $analisitappe;
    $listatappetool_ = $listatappetool;
    $listaart_ = $listaart;

    $items[] = [
        'label' => '<i class="fas fa-user"> Cost Analisys</i>',
        'id' => 'costanalsys',
        'content' =>
        $this->render('_analisi', [
            'dettaglio' => $dettaglio,

            'pivot' => $pivot,
            'labels' => $labels,
            'series' => $series,

            'analisitappe' => $analisitappe,
        ])
        // 'active' => true,
    ];
    /* $items[] = [
        'label' => '<i class="fas fa-user"> Room List</i>',
        'id' => 'roomlist',
        'content' =>
        $this->render('_roomlist', [
            'roomlist' => $roomlist,
            'tipo' => 1
        ])
        // 'active' => true,
    ];*/
    /* $items[] = [
        'label' => '<i class="fas fa-user"> Riepilogo</i>',
        'id' => 'roomlist2',
        'content' =>
        $this->render('_roomlist', [
            'roomlist' => $recap,
            'tipo' => 2
        ]),
        'active' => false
        // 'active' => true,
    ];*/
    $i = 1;
    $recap2 = $recap;
    $roomlist = $recap;


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

        $r = xxLoadtappa2(
            $model->th_id,
            $value['cd_cf_ft'],
            $value['citta'],
            null,
            $tipo
        );

        $i++;
    }



    ?>











 <?php

    function xxLoadtappa2(
        $th_id,
        $cliente = null,
        $citta,
        $xcliente = null,
        $tipo = null
    ) {


        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        if (isset($cliente)) {
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        }
        $xcliente = $xcliente ?? null;
        $citta = str_replace("'", "_", $citta);

        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $whereClauses[] = "citta='" . str_replace("'", "''", $citta) . "'";
                break;
            case 2:
                $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                break;
            case 3:
                $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                if ($xcliente <> '') {
                    $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                }
                break;
        }



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
            if (isset($cliente)) {
                $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            }
            // $whereClauses[] = "citta_da='" . str_replace("'", "''", $citta) . "'";

            switch ($tipo['x_tiposhow']) {
                case null:
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 2:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if ($xcliente <> '') {
                        $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }


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
        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
                break;
            case 2:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,
        --CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS 
        sottocommessa as 
        citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,sottocommessa 
order by min(check_in) asc ";
                break;
            case 3:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,sottocommessa AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,sottocommessa
order by min(check_in) asc ";
                break;
        }
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        if (isset($cliente)) {
            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
        }
        //$whereClauses2[] = "citta='" . str_replace("'", "''", $citta) . "'";
        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $whereClauses2[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
                break;
            case 2:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                break;
            case 3:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                if ($xcliente <> '') {
                    $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                }
                break;
        }
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
            //var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;
            if (isset($cliente)) {
                $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
            }
            // $whereClauses3[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
            switch ($tipo['x_tiposhow']) {
                case null:
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses3[] = "citta_da='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 2:
                    $whereClauses3[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses3[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
            }

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
        $dettatappa = [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
        return $dettatappa;
    }
    // Funzione per chiudere la tabella e mostrare i totali
    function closeTable($totals)
    {
        echo '<tr  class="table-total bg-primary">';
        echo '<td colspan=2 class="table-tothead" > <strong>Totali</strong> </td>';
        echo '<td  class="table-tothead"> <strong>€ ' . formatEuro($totals['mprezzo']) . '</strong></td>';
        echo '<td  class="table-tothead"> <strong>€ ' . formatEuro($totals['imponibile']) . '</strong></td>';
        echo '<td class="table-tothead"> <strong>€ ' . formatEuro($totals['ctax']) . '</strong></td>';
        echo '<td class="table-tothead"> <strong>€ ' . formatEuro($totals['iva']) . '</strong></td>';
        echo '<td class="table-tothead"> <strong>€ ' . formatEuro($totals['totale']) . '</strong></td>';
        // echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['fee'] ) . '</strong></td>';
        // echo '<td style="font-size: 16px;"> <strong>€ ' . formatEuro($totals['totaleimpfatt'] ) . '</strong></td>';
        echo '<td class="table-tothead"> <strong>€ '
            . formatEuro($totals['totaleimpfatt'] + $totals['fee'])  . '</strong></td>';
        echo '</tr>';
        echo '</table>';
    }
    ?>



 <div class="row">
     <div class="col-12 text-left">
         <h2 class="titolo-header"><?= Html::encode($this->title) ?></h2>
     </div>
 </div>

 <nav>

     <!-- Pulsante per aprire la dialog -->
     <?php


        $mdettaglio = ($dettaglio_);
        $mth_id = ($model->th_id);
        $mpivot = ($pivot_);
        $mlabels = ($labels_);
        $mseries = ($series_);
        $mtappe = ($uniqueTappe_);
        $mroomlist = ($roomlist_);
        $manalisitappe = ($analisitappe_);
        $mlistatappetool = ($listatappetool_);
        $mlistaart = ($listaart_);



        if (Yii::$app->user->identity->level  >= 80) {

            echo Html::button('Elenco Ospiti', [
                'class' => 'button-base button-lift',
                'data-toggle' => 'modal',
                'data-target' => '#ospiti-modal'
            ]);
        }

        ?>
     <?php  /* echo Html::button('Scheda Hotel', [
    'class' => 'button-base button-lift btn-hotel',
    //'data-toggle' => 'modal',
    //'data-target' => '#ospiti-modal'
]) */ ?>
     <?php  /* echo Html::button('Scheda Ticketing', [
    'class' => 'button-base button-lift btn-ticketing',
    //'data-toggle' => 'modal',
    //'data-target' => '#ospiti-modal'
]) */ ?>
     <?php
        $script = <<<JS
$('.btn-hotel, .btn-ticketing').on('click', function() {
    alert('Procedura in costruzione');
});
JS;

        $this->registerJs($script);
        ?>

     <!-- Pulsante per aprire la dialog -->
     <!--button id="open-dialog2" class="button-base button-lift">Tappe</button>-->
     <?php

        if (Yii::$app->user->identity->level  >= 80) {
            echo Html::button('Analisi Dati', [
                'class' => 'button-base button-lift',
                'data-toggle' => 'modal',
                'data-target' => '#chart-modal'
            ]);
        } ?>

     <?php /*echo Html::button('Elenco Clienti', [
            'class' => 'button-base button-lift',
            'data-toggle' => 'modal',
            'data-target' => '#elecli-modal'
        ]) */ ?>

     <?php echo Html::button('File', [
            'class' => 'button-base button-lift',
            'data-toggle' => 'modal',
            'data-target' => '#file-modal'
        ]) ?>

     <?php if (Yii::$app->user->identity->level  >= 80): ?>
         <?php
            // Salva i dati in sessione
            Yii::$app->session->set('tool_data', [
                'mdettaglio' => $mdettaglio,
                'mth_id' => $mth_id,
                'mpivot' => $mpivot,
                'mlabels' => $mlabels,
                'mseries' => $mseries,
                'mtappe' => $mtappe,
                'mroomlist' => $mroomlist,
                'manalisitappe' => $manalisitappe,
                'mlistaart' => $mlistaart,
                'mlistaart2' => $mlistaart,
                'mlistatappetool' => $mlistatappetool,
            ]);
            ?>

         <?= Html::button('Tools', [
                'class' => 'button-base button-lift',
                'onclick' => "window.location.href='" .
                    \yii\helpers\Url::to(['xtravelhead/tool', 'id' => $mth_id]) . "'"
            ]) ?>
     <?php endif; ?>


     <?php

        echo Html::button('Estratto Conto', [
            'class' => 'button-base button-lift',
            /* 'onclick' => 'window.location.href = "' .
                Url::to([
                    'xtravelhead/export-to-excel',
                    'id' => $model->th_id
                ]) . '"'*/
            // 'onclick' => 'scegliEstrattoConto(' . $model->th_id . ')'
            'data-toggle' => 'modal',
            'data-target' => '#estrattoContoModal'
        ]);



        ?>
     <script>
         function confermaEstrattoConto(id) {
             let tipo = $('#tipo-estratto').val();
             if (!tipo) {
                 alert("Seleziona un tipo di estratto conto!");
                 return;
             }
             let url = "<?= Url::to(['xtravelhead/export-to-excel']) ?>&id=" + id + "&tipoexp=" + tipo;
             console.log(url);
             window.location.href = url;
         }
     </script>


     <?php
        if (Yii::$app->user->identity->level  >= 80) {

            echo Html::button('Carica Locandina', [
                'class' => 'button-base button-lift',
                'data-toggle' => 'modal',
                'data-target' => '#upload-modal'
            ]);
        }
        ?>





     <?php

        $backUrl = Yii::$app->request->referrer ?: Url::to(['index']);
        echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
            'class' => 'button-base-support button-lift',
            'style' => 'background-color: #6c757d !important;',
            'onclick' => 'window.location.href = "' . $backUrl . '"'
        ]); ?>


 </nav>
 <pre></pre>
 <NAV>

     <?php
        $url = Url::to(['site/contatti', 'id' => $model->th_id]);

        echo Html::button(
            '<i class="fa fa-envelope"></i> Invia Comunicazione',
            [
                'class' => 'button-base-support button-lift',
                'onclick' => 'window.location.href = "' . $url . '"',
                'encode' => false // importante per non scappare l'HTML dell'icona
            ]
        );

        ?>
 </NAV>
 <?php
    $this->registerCss("
    /* Stile principale del box Tippy identico al .pill */
    .tippy-box[data-theme~='pill-theme'] {
        background-color: var(--primary-color);
        color: white;
        border-radius: 16px; /* Arrotondato, ma leggermente meno di 32px per contenere bene la lista testuale */
        font-weight: bold;
        font-size: var(--font-xxs); /* Usa la stessa dimensione del testo */
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15); /* Stessa ombra dell'hover del bottone */
    }

    /* Colore della freccina dinamico in base alla tua variabile */
    .tippy-box[data-theme~='pill-theme'][data-placement^='top'] > .tippy-arrow::before {
        border-top-color: var(--primary-color);
    }
    .tippy-box[data-theme~='pill-theme'][data-placement^='bottom'] > .tippy-arrow::before {
        border-bottom-color: var(--primary-color);
    }
    .tippy-box[data-theme~='pill-theme'][data-placement^='left'] > .tippy-arrow::before {
        border-left-color: var(--primary-color);
    }
    .tippy-box[data-theme~='pill-theme'][data-placement^='right'] > .tippy-arrow::before {
        border-right-color: var(--primary-color);
    }

    /* Sistemiamo i margini interni della lista per non farla sbattere sui bordi */
    .tippy-box[data-theme~='pill-theme'] > .tippy-content {
        padding: 10px 15px;
        text-align: left;
    }
    
    .tippy-box[data-theme~='pill-theme'] ul {
        margin-top: 5px;
        margin-bottom: 0;
        padding-left: 15px;
        font-weight: normal; /* Teniamo l'elenco normale, lasciando in bold solo i nomi degli hotel */
    }
");
    ?>
 <?php
    $this->registerJsFile('https://unpkg.com/@popperjs/core@2', ['position' => \yii\web\View::POS_HEAD]);
    $this->registerJsFile('https://unpkg.com/tippy.js@6', ['position' => \yii\web\View::POS_HEAD]);
    $this->registerCssFile('https://unpkg.com/tippy.js@6/animations/scale.css');

    $this->registerJs("
    tippy('.btn-tappa-tooltip', {
        allowHTML: true,
        animation: 'scale',
        theme: 'pill-theme', // Usa il nostro nuovo stile clonato!
        placement: 'right', // O 'top', come preferisci
        maxWidth: 350,
    });
", \yii\web\View::POS_READY);
    ?>



 <table class="table">
     <!--class="table table-sm table-hover table-responsive-sm table-fit"-->
     <!--thead class="thead-dark"-->
     <thead class="totali">
         <?php if (Yii::$app->user->identity->level ?? 0 >= 80) { ?>
             <th class="table-tothead" width="10%">Show</th>

         <?php } ?>
         <th class="table-tothead" width="11%">Città</th>
         <th class="table-tothead" width="11%">Venue</th>
         <th class="table-tothead" width="6%">Impon.</th>
         <th class="table-tothead" width="6%">Non Imp.</th>
         <th class="table-tothead" width="6%">IVA Srv</th>
         <th class="table-tothead" width="6%">Pagato</th>
         <th class="table-tothead" width="6%">FEE</th>
         <th class="table-tothead" width="6%">Imp. Ft.</th>
         <th class="table-tothead" width="1%"></th>
         <th class="table-tothead" width="6%"> Media</th>
         <th class="table-tothead" width="5%"> % Bdg</th>
     </thead>

     <?php
        // yii::warning($venueMap);
        // yii::warning($cityDataMap);


        $xtmptappe = $roomlist['tappetour'];
        // print_r($xtmptappe);
        $totalGeneral = 0;
        foreach ($roomlist['tappetour'] as $value) {
            $totalGeneral += $value['fee'] + $value['imponibile'] + $value['ctax'];
        }



        foreach ($roomlist['tappetour'] as $value) {
            //yii::error($value);

            echo '<tr>';

            if (Yii::$app->user->identity->level ?? 0 >= 80) {
                if (preg_match(
                    '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
                    $value['citta']
                )) {


                    $data = (new \yii\db\Query())
                        ->select(['data'])
                        ->from('adb_auxcoop.dbo.xtravelrow')
                        ->leftJoin('x_tappe', 'x_tappe.id_tappa=xtravelrow.id_tappa
and x_tappe.th_id=xtravelrow.th_id')
                        ->where(['xtravelrow.citta' => $value['citta']])
                        ->andWhere(['xtravelrow.th_id' => $model->th_id])
                        // fornitore è cd_cf_ft immagino
                        ->scalar(Yii::$app->db5);
                    echo '<td class="table-info">';
                    if (!empty($data)) {
                        echo '' .
                            date('d/m/y', strtotime($data));
                    } else {
                        echo '' . date('d/m/y', strtotime($value['datainizio']))
                            .
                            ' a ' . date('d/m/y', strtotime($value['datafine'] ??
                                $value['datainizio']));
                    }

                    echo '</td>';
                } else {
                    echo '<td class="table-info">';
                    echo '' . date('d/m/y', strtotime($value['datainizio']))
                        .
                        ' a ' . date('d/m/y', strtotime($value['datafine'] ??
                            $value['datainizio']));
                    echo '</td>';
                }
            }

            echo '<td class="table-info">';


            $value['citta'] = trim($value['citta']);

            if (preg_match(
                '/^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$/',
                $value['citta']
            )) {
                // yii::error($value['citta']);
                $venueData = $venueMap[$value['citta']] ?? null;

                if (is_array($venueData)) {
                    // $venueMap contiene array da ->asArray()
                    $Tcitta = $venueData['citta'] ?? 'N.d.';
                    $venue = $venueData['venue'] ?? 'N.d.';
                } elseif (is_object($venueData)) {
                    // nel caso raro che siano oggetti ActiveRecord
                    $Tcitta = $venueData->citta ?? 'N.d.';
                    $venue = $venueData->venue ?? 'N.d.';
                } else {
                    // fallback se non c'è corrispondenza in $venueMap
                    $Tcitta = $value['citta'] ?? 'N.d.';
                    $venue = $value['citta'] ?? 'N.d.';
                }
                // --- INIZIO QUERY PER IL TOOLTIP ---
                $cittaPerQuery = str_replace("'", "''", $value['citta']);
                $sqlTooltip = "
                SELECT 
                    coalesce( x_struttura.struttura  , xtravelrow.struttura) as struttura, 
                    stato, 
                    SUM(qta) as totale_stanze
                FROM xtravelrow 
                 left join x_struttura on CAST(x_struttura.id AS VARCHAR(50))=xtravelrow.struttura
                WHERE th_id = :th_id AND xtravelrow.citta = :citta
             and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
        and Cd_ARClasse2 in ('ACC'              
        )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
        GROUP BY xtravelrow.struttura,x_struttura.struttura, stato
        ORDER BY xtravelrow.struttura
            ";

                $datiTappa = Yii::$app->db5->createCommand($sqlTooltip)
                    ->bindValue(':th_id', $model->th_id)
                    ->bindValue(':citta', $value['citta'])
                    ->queryAll();

                // Costruiamo l'HTML e controlliamo se tutto è prenotato
                $tooltipHtml = "<div style='text-align:left; font-size:12px;'>";
                $tuttoChiuso = false; // Inizializziamo il flag a false

                if (empty($datiTappa)) {
                    $tooltipHtml .= "<i>Nessuna struttura presente</i>";
                } else {
                    $tuttoChiuso = true; // Assumiamo sia vero, e lo smentiamo se troviamo altro
                   // $tooltipHtml .= "<strong>Riepilogo Strutture:</strong><ul style='padding-left:15px; margin-bottom:0;'>";

                    $struttureAggregato = [];
                    foreach ($datiTappa as $dt) {
                        $nomeStruttura = $dt['struttura'] ?: 'N.D.';
                        if (strpos($nomeStruttura, '|') !== false) {
                            $nomeStruttura = trim(explode('|', $nomeStruttura)[0]);
                        }

                        $statoRaw = trim(strtoupper($dt['stato'] ?? ''));
                        $stato = $dt['stato'] ?: 'Da Prenotare';
                        $stanze = (int)$dt['totale_stanze'];

                        // Se lo stato NON è "CHIUSO" e NON è "IN PENALE", allora il flag diventa falso
                        if (!in_array($statoRaw, ['CHIUSO', 'IN PENALE','CANCELLATO'])) {
                            $tuttoChiuso = false;
                        }
                        if (!isset($struttureAggregato[$nomeStruttura])) {
                            $struttureAggregato[$nomeStruttura] = [];
                        }
                        $struttureAggregato[$nomeStruttura][] = "Notti: $stanze   $stato";
                    }
                    // 2. ORA che sappiamo se è tutto chiuso, decidiamo il titolo e iniziamo la lista
                    $titoloTooltip = $tuttoChiuso ? "Consuntivo" : "Riepilogo Strutture";
                    $tooltipHtml .= "<strong>{$titoloTooltip}:</strong><ul style='padding-left:15px; margin-bottom:0;'>";
                    foreach ($struttureAggregato as $struttura => $dettagli) {
                        $tooltipHtml .= "<li><b>$struttura</b>:<br> " . implode(', ', $dettagli) . "</li>";
                    }
                    $tooltipHtml .= "</ul>";
                }
                $tooltipHtml .= "</div>";

                // Usiamo FontAwesome con il colore azzurro/ciano dello stato CHIUSO (#00cff3)
                $iconaChiuso = $tuttoChiuso ? ' <i class="fas fa-check-circle" style="color: #00cff3; font-size: 1.3em; margin-left: 5px; vertical-align: middle;"></i>' : '';
                $testoBottone = (($Tcitta) ?? 'N.d.') . $iconaChiuso;

                // Creiamo il bottone pulito (solo per la modale) 
                $bottoneCitta = Html::button($testoBottone, [
                    'class' => 'pill',
                    'encode' => false, // permette di mostrare l'icona FontAwesome
                    'data-toggle' => 'modal',
                    'data-target' => '#citta_' . preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']) . preg_replace('/\s+/', '', ($value['cd_cf_ft'] ?? '')),
                ]);

                // Lo avvolgiamo nello span che gestisce il tooltip 
                echo Html::tag('span', $bottoneCitta, [
                    'class' => 'btn-tappa-tooltip',
                    'data-tippy-content' => $tooltipHtml,
                    'style' => 'display: inline-block; cursor: help;'
                ]);
            } else {
                $venue = $value['citta'] ?? ($value['cittada'] ?? null);
                // --- INIZIO QUERY PER IL TOOLTIP ---
                $cittaPerQuery = str_replace("'", "''", $venue);
                $sqlTooltip = "
                SELECT 
                    coalesce( x_struttura.struttura  , xtravelrow.struttura) as struttura, 
                    stato, 
                    SUM(qta) as totale_stanze
                FROM xtravelrow 
                 left join x_struttura on CAST(x_struttura.id AS VARCHAR(50))=xtravelrow.struttura
                WHERE th_id = :th_id AND xtravelrow.citta = :citta
             and xtravelrow.cd_Ar in (select cd_ar from ar where Cd_ARClasse1='TRV' 
        and Cd_ARClasse2 in ('ACC'              
        )) and xtravelrow.cd_ar not in (select cd_ar from ar where x_isacconto=1)
        GROUP BY xtravelrow.struttura,x_struttura.struttura, stato
        ORDER BY xtravelrow.struttura
            ";

                $datiTappa = Yii::$app->db5->createCommand($sqlTooltip)
                    ->bindValue(':th_id', $model->th_id)
                    ->bindValue(':citta', $venue)
                    ->queryAll();

                // Costruiamo l'HTML e controlliamo se tutto è prenotato
                $tooltipHtml = "<div style='text-align:left; font-size:12px;'>";
                $tuttoChiuso = false;

                if (empty($datiTappa)) {
                    $tooltipHtml .= "<i>Nessuna struttura presente</i>";
                } else {
                    $tuttoChiuso = true;
                   // $tooltipHtml .= "<strong>Riepilogo Strutture:</strong><ul style='padding-left:15px; margin-bottom:0;'>";

                    $struttureAggregato = [];
                    foreach ($datiTappa as $dt) {
                        $nomeStruttura = $dt['struttura'] ?: 'N.D.';
                        if (strpos($nomeStruttura, '|') !== false) {
                            $nomeStruttura = trim(explode('|', $nomeStruttura)[0]);
                        }

                        $statoRaw = trim(strtoupper($dt['stato'] ?? ''));
                        $stato = $dt['stato'] ?: 'Da Prenotare';
                        $stanze = (int)$dt['totale_stanze'];

                        // Se lo stato NON è "CHIUSO" e NON è "IN PENALE", allora il flag diventa falso
                        if (!in_array($statoRaw, ['CHIUSO', 'IN PENALE','CANCELLATO'])) {
                            $tuttoChiuso = false;
                        }

                        if (!isset($struttureAggregato[$nomeStruttura])) {
                            $struttureAggregato[$nomeStruttura] = [];
                        }
                        $struttureAggregato[$nomeStruttura][] = "Notti: $stanze   $stato";
                    }


                    // 2. ORA che sappiamo se è tutto chiuso, decidiamo il titolo e iniziamo la lista
                    $titoloTooltip = $tuttoChiuso ? "Consuntivo" : "Riepilogo Strutture";
                    $tooltipHtml .= "<strong>{$titoloTooltip}:</strong><ul style='padding-left:15px; margin-bottom:0;'>";

                    foreach ($struttureAggregato as $struttura => $dettagli) {
                        $tooltipHtml .= "<li><b>$struttura</b>:<br> " . implode(', ', $dettagli) . "</li>";
                    }
                    $tooltipHtml .= "</ul>";
                }
                $tooltipHtml .= "</div>";

                // Usiamo FontAwesome con il colore azzurro/ciano dello stato CHIUSO (#00cff3)
                $iconaChiuso = $tuttoChiuso ? ' <i class="fas fa-check-circle" style="color: #00cff3; font-size: 1.3em; margin-left: 5px; vertical-align: middle;"></i>' : '';
                $testoBottone = (($venue) ?? 'N.d.') . $iconaChiuso;

                // Creiamo il bottone pulito (solo per la modale)
                $bottoneCitta = Html::button($testoBottone, [
                    'class' => 'pill',
                    'encode' => false, // permette di mostrare l'icona FontAwesome
                    'data-toggle' => 'modal',
                    'data-target' => '#citta_' . preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']) . preg_replace('/\s+/', '', ($value['cd_cf_ft'] ?? '')),
                ]);

                // Lo avvolgiamo nello span che gestisce il tooltip
                echo Html::tag('span', $bottoneCitta, [
                    'class' => 'btn-tappa-tooltip',
                    'data-tippy-content' => $tooltipHtml,
                    'style' => 'display: inline-block; cursor: help;'
                ]);
            }






            echo '</td>';
            echo '<td class="table-info">';

            /*    $connection = Yii::$app->db5;
            $command = $connection->createCommand(
                "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
            );
            $tipo = $command->bindValue(':id', $model->th_id)->queryOne();
*/
            switch ($tipo['x_tiposhow']) {
                case null:
                case 1:
                    echo   $venue;
                    break;
                case 2:
                    echo   $venue;
                    break;
                case 3:
                    echo   $venue . ' Cliente:' . $value['cd_cf_ft'] ?? '';
                    break;
            }
            //YII::warning($value);
            $venue = 'N.d.';
            echo '</td>';

            echo '<td class="table-info">€ ';
            echo formatEuro($value['imponibile'], 2);
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['ctax'], 2);
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['iva'], 2);
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['totale'], 2);
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['fee'], 2);
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['fee'] +
                $value['imponibile']
                + $value['ctax'], 2);
            echo '</td>';
            echo '<td scope="col" style="background-color:rgb(255, 255, 255);">';
            echo '</td>';
            echo '<td class="table-info">€ ';
            echo formatEuro($value['mprezzo'], 2);
            echo '</td>';
            $partialValue = $value['fee'] + $value['imponibile'] + $value['ctax'];
            $percentage = ($totalGeneral > 0) ? ($partialValue / $totalGeneral) * 100 : 0;
            echo '<td class="table-info">' . number_format($percentage, 2) . '%</td>';
            $r = xxLoadtappa2($model->th_id, null, $value['citta'], null, $tipo);
            $xc = preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']);





            echo '</tr>';
        }
        ?>

     <tr>
         <td class="table-totheadviaggift" colspan=3> <StRONG>Totale Viaggi</td>
         <?php
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ "  . formatEuro($dataviaggitotali['timponibile'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ " . formatEuro($dataviaggitotali['ttassa'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ "  . formatEuro($dataviaggitotali['tiva'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ "  . formatEuro(($dataviaggitotali['tpagato'] ?? 0)) . "</td>";
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ "  . formatEuro($dataviaggitotali['tfee'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadviaggift\"> <StRONG>€ "  . //($dataviaggitotali[0]['ttotale'] ?? 0) 
                formatEuro(($dataviaggitotali['tfee'] ?? 0)
                    + ($dataviaggitotali['timponibile'] ?? 0) +
                    ($dataviaggitotali['ttassa'] ?? 0))



                . "</td>";
            echo "<td  class=\"table-totheadviaggift\"></td>";
            echo "<td class=\"table-totheadviaggift\"><StRONG>€ " . formatEuro($dataviaggitotali['mprezzo'] ?? 0) . "</td>";
            echo "<td  class=\"table-totheadviaggift\" ><StRONG>" . ($dataviaggitotali['bdg'] ?? 0) . " %</td>";

            ?>
     </tr>
     <tr>
         <td class="table-totheadhotel" colspan=3> <StRONG>Totale Hotel</td>
         <?php // yii::error(($datahoteltotali));
            echo "<td class=\"table-totheadhotel\"> <StRONG>€ " . formatEuro($datahoteltotali['timponibile'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadhotel\"> <StRONG>€ "  . formatEuro($datahoteltotali['ttassa'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadhotel\"> <StRONG>€ "  . formatEuro($datahoteltotali['tiva'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadhotel\"> <StRONG>€ "  . formatEuro(($datahoteltotali['tpagato'] ?? 0))
                . "</td>";
            echo "<td class=\"table-totheadhotel\"> <StRONG>€ "  . formatEuro($datahoteltotali['tfee'] ?? 0) . "</td>";

            echo "<td class=\"table-totheadhotel\"> <StRONG>€ "  . //($dataviaggitotali[0]['ttotale'] ?? 0) 
                formatEuro(($datahoteltotali['tfee'] ?? 0)
                    + ($datahoteltotali['timponibile'] ?? 0) +
                    ($datahoteltotali['ttassa'] ?? 0))



                . "</td>";
            // yii::warning($datahoteltotali['ttassa']);
            echo "<td class=\"table-totheadhotel\"></td>";
            echo "<td class=\"table-totheadhotel\"><StRONG>€ " . formatEuro($datahoteltotali['mprezzo'] ?? 0) . "</td>";
            echo "<td class=\"table-totheadhotel\"><StRONG>" . ($datahoteltotali['bdg'] ?? 0) . " %</td>";
            ?>
     </tr>
     <?php
        echo '<tr   "class="totali">';
        if (Yii::$app->user->identity->level  ?? 0 >= 80) {
            echo '<td class="table-tothead2" colspan=3> <StRONG> Totali </td>';
        } else {
            echo '<td class="table-tothead2" colspan=2> <StRONG> Totali </td>';
        }


        $eta = array_column($roomlist['tappetour'], 'imponibile');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        echo '<td class="table-tothead2"> <StRONG>€ ';
        $timpo = $sommaEta;
        echo formatEuro($sommaEta, 2) . '</td>';
        $eta = array_column($roomlist['tappetour'], 'ctax');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        echo '<td class="table-tothead2"> <StRONG>€ ';
        $ttax = $sommaEta;
        echo formatEuro($sommaEta, 2) . '</td>';
        $eta = array_column($roomlist['tappetour'], 'iva');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        echo '<td class="table-tothead2"> <StRONG>€ ';
        echo formatEuro($sommaEta, 2) . '</td>';
        $eta = array_column($roomlist['tappetour'], 'totale');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        echo '<td class="table-tothead2"> <StRONG>€ ';
        echo formatEuro($sommaEta, 2) . '</td>';
        $eta = array_column($roomlist['tappetour'], 'fee');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        $tfee = $sommaEta;
        echo '<td class="table-tothead2"> <StRONG>€ ';
        echo formatEuro($sommaEta, 2) . '</td>';
        $eta = array_column($roomlist['tappetour'], 'ttotalegenerale');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);
        echo '<td class="table-tothead2"> <StRONG>€ ';
        echo formatEuro($tfee + $ttax + $timpo, 2) . '</td>';
        echo '<td scope="col" class="table-tothead2">';
        echo '</td>';
        echo '<td class="table-tothead2"> <StRONG >€ ';
        $eta = array_column($roomlist['tappetour'], 'mprezzo');

        // Calcola la somma degli anni
        $sommaEta = array_sum($eta);

        // Calcola il numero di elementi
        $numeroElementi = count($eta);
        //yii::error($numeroElementi);
        // Calcola la media
        if ($numeroElementi > 0) {
            $mediaEta = $sommaEta / ($numeroElementi + 1);
        } else {
            $mediaEta = 0; // O un altro valore di default
        }

        // Estrai i valori
        $mprezzoViaggi = $dataviaggitotali['mprezzo'] ?? 0;
        $mprezzoHotel = $datahoteltotali['mprezzo'] ?? 0;

        // Calcola la media o usa il valore non zero
        if ($mprezzoViaggi != 0 && $mprezzoHotel != 0) {
            // Entrambi i valori sono diversi da zero, calcola la media
            $mediaPrezzi = ($mprezzoViaggi + $mprezzoHotel) / 2;
        } elseif ($mprezzoViaggi != 0) {
            // Solo il valore dei viaggi è diverso da zero
            $mediaPrezzi = $mprezzoViaggi;
        } else {
            // In tutti gli altri casi (solo hotel o entrambi zero), usa il valore dell'hotel
            $mediaPrezzi = $mprezzoHotel;
        }


        echo formatEuro($mediaPrezzi) . '</td>';


        // echo formatEuro($mediaEta, 2) . '</td>';
        echo '<td scope="col" class="table-tothead2" >';
        echo '</td>';
        ?>
     </tr>

 </table>
 <br>
 <br>
 <?php
    $modaltappetour = $roomlist['tappetourcli'];








    echo '<h3>Elenco Documenti Emessi</h3>
<table class="table">
    <thead>
        <tr>
            <th class="table-tothead" width="10%">Codice Documento</th>
            <th class="table-tothead" width="11%">Numero</th>
            <th class="table-tothead" width="41%" colspan="5">Data</th>
            <th class="table-tothead" width="6%">Imp.Ft.</th>
            <th class="table-tothead" width="1%"></th>
            <th class="table-tothead" width="6%">Totale a pagare</th>
            <th class="table-tothead" width="5%"> </th>
        </tr>
    </thead>
    <tbody>
';

    $ttotimponibilev = 0;
    $tTotDocumentoV = 0;

    foreach ($roomlist['dotes'] as $value) {
        echo '<tr>';
        echo '<td class="table-info">' . $value['cd_do'] . '</td>';
        echo '<td class="table-info">' . $value['NumeroDoc'] . '</td>';
        echo '<td class="table-info" colspan="5">' . date('d/m/Y', strtotime($value['DataDoc'])) . '</td>';
        echo '<td class="table-info">€ ' . formatEuro($value['totimponibilev'], 2) . '</td>';
        echo '<td class="table-info"></td>';
        echo '<td class="table-info">€ ' . formatEuro($value['TotDocumentoV'], 2) . '</td>';
        echo '<td class="table-info"></td>';
        echo '</tr>';

        $ttotimponibilev += $value['totimponibilev'];
        $tTotDocumentoV += $value['TotDocumentoV'];
    }

    echo '
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" class="table-tothead text-right">Totali</th>
            <th class="table-tothead">€ ' . formatEuro($ttotimponibilev, 2) . '</th>
            <th class="table-tothead"></th>
            <th class="table-tothead">€ ' . formatEuro($tTotDocumentoV, 2) . '</th>
            <th class="table-tothead"></th>
        </tr>
    </tfoot>
</table>';
    ?>




 <?php
    //echo 'asdadasda';

    // Register ApexCharts library
    $this->registerJsFile('https://cdn.jsdelivr.net/npm/apexcharts', [
        'position' => \yii\web\View::POS_HEAD
    ]);

    // Register our custom JavaScript with proper escaping
    $js = <<<JS
// First, let's create a more robust chart initialization function
const initializeChart = (containerId, options) => {
    // Use standard string concatenation instead of template literals for the error message
    const container = document.querySelector(containerId);
    if (!container) {
        console.error('Chart container ' + containerId + ' not found');
        return null;
    }

    // Create and render the chart
    const chart = new ApexCharts(container, options);
    return chart.render().then(() => chart).catch(err => {
        console.error("Chart rendering error:", err);
        return null;
    });
};

// Define chart options that we'll reuse
const getChartOptions = () => ({
    chart: {
        type: 'bar',
        height: 350,
        background: '#ffffff',
        animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800
        }
    },
    series: [{
        name: 'Valori',
        data: [10, 15, 20, 25, 30]
    }],
    xaxis: {
        categories: ["Gen", "Feb", "Mar", "Apr", "Mag"]
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                height: 300
            }
        }
    }],
    colors: ['#008FFB']
});

// Add console logs for debugging
const debugChart = (containerId) => {
    const container = document.querySelector(containerId);
    console.log('Container:', container);
    if (container) {
        console.log('Container dimensions:', container.getBoundingClientRect());
    }
};

// Initialize modal chart when modal is fully shown
let modalChart = null;
$('#chart-modal').on('shown.bs.modal', function() {
    debugChart("#chart");
    setTimeout(() => {
        if (!modalChart) {
            console.log('Initializing modal chart...');
            modalChart = initializeChart("#chart", getChartOptions());
        }
    }, 300);
});

// Cleanup modal chart when modal is hidden
$('#chart-modal').on('hidden.bs.modal', function() {
    console.log('Modal hidden, cleaning up chart...');
    if (modalChart) {
        modalChart.then(chart => {
            if (chart) {
                chart.destroy();
            }
            modalChart = null;
        });
    }
});

// Initialize standalone chart
document.addEventListener("DOMContentLoaded", function() {
    debugChart("#chart1");
    initializeChart("#chart1", getChartOptions());
});
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

 <?php


    $this->registerCss(<<<CSS
    .modal-dialog {
        max-width: 98%;
    }
    
    .modal-content {
        background-color: #ffffff;
        height: auto;
        min-height: 500px;
    }
    
    #chart, #chart1 {
        width: 100% !important;
        height: 350px !important;
        position: relative;
    }
CSS);

    Modal::begin([
        'id' => 'chart-modal',
        'title' => 'Grafico',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'closeButton' => ['label' => 'Chiudi'],
        'options' => [
            'class' => 'fade',
            'tabindex' => false,
            'style' => 'display: none', // Ensure modal starts hidden
        ],
        'bodyOptions' => [
            'class' => 'modal-body p-3',
            'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
        ]
    ]);
    ?>

 <?php echo $this->render('_modal_analisi', [
        'dettaglio' => $dettaglio,
        'labels' => $labels,
        'series' => $series,
        'analisitappe' => $analisitappe,
        'pivot' => $pivot
    ]); ?>

 <?php Modal::end(); ?>

 <!-- Standalone chart container -->

 <!-- Button to open modal -->






 <?php

    Modal::begin([
        'id' => 'ospiti-modal',
        'title' => 'Elenco Ospiti',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'closeButton' => ['label' => 'Chiudi'],
        'options' => [
            'class' => 'fade',
            'tabindex' => false,
            'style' => 'display: none', // Ensure modal starts hidden
        ],
        'bodyOptions' => [
            'class' => 'modal-body p-3',
            'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
        ]
    ]);
    ?>
 <?php echo $this->render('_modal_eleosp', ['roomlist2' => $roomlist2]); ?>
 <?php Modal::end(); ?>




 <?php

    function xLoadtappa2(
        $th_id,
        $cliente = null,
        $citta,
        $isservizio = null,
        $xcliente = null,
        $tipo = null,
        $punto = null
    ) {


        if ($tipo === null) {
            // yii::warning('xloadtappa2 dentro ' .  $punto ?? 'nessun punto');
            // Yii::warning($tipo);
            $connection = Yii::$app->db5;
            $command = $connection->createCommand(
                "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
            );
            $tipo = $command->bindValue(':id', $th_id)->queryOne();
        }


        $xcliente = $xcliente ?? null;

        $chk = 0;
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        if (rtrim(ltrim($cliente))  <> '') {
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        }
        $citta = str_replace("'", "", $citta);
        if ($isservizio == 1) {

            $chk = 1;
        }

        if ($chk == 0) {
            switch ($tipo['x_tiposhow']) {
                case null:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta)
                        . "'";
                    break;
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta)
                        . "'";
                    break;
                case 2:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if (rtrim(ltrim($xcliente))  <> '') {
                        $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }
        } else {
            // $whereClauses[] = "citta_da='" . $citta . "'";


            switch ($tipo['x_tiposhow']) {
                case null:
                    $whereClauses[] = "replace(citta,'''','') ='" . $citta . "'";
                    break;
                case 1:

                    $whereClauses[] = "replace(citta,'''','') ='" . $citta . "'";
                    break;
                case 2:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if (rtrim(ltrim($xcliente)) <> '') {
                        $whereClauses[] = "cd_cf_ft='"
                            . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }
        }


        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        //yii::error('query result' . $sql);
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        //yii::error('erro' . $sql);
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            // $whereClauses[] = "cd_cf_ft='" . $cliente . "'";

            switch ($tipo['x_tiposhow']) {
                case null:
                    $whereClauses[] = "replace(citta_da,'''','')='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 1:

                    $whereClauses[] = "replace(citta_da,'''','')='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 2:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if ($xcliente <> '') {
                        $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }


            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";


            //   yii::error('query art' . $sql);
            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,
        CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale,
--sum(prezzo) as imponibile,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as imponibile,

sum(iva) as iva,
sum(x_pagato) as pagato
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
                break;

            case 2:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,
        --CASE WHEN citta IS NULL THEN citta_da ELSE citta END
		sottocommessa
		AS citta,
sum(fee) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale,
--sum(prezzo) as imponibile,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as imponibile,

sum(iva) as iva,
sum(x_pagato) as pagato
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
$where   


		group by x_scdesc,descli,
	--	CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
	sottocommessa
order by min(check_in) asc  ";
                break;
            case 3:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, 
        max(check_out) as enddate ,
       -- CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
       sottocommessa
       AS citta,
sum(fee) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale,
--sum(prezzo) as imponibile,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as imponibile,

sum(iva) as iva,
sum(x_pagato) as pagato
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
left join aliquota on aliquota.cd_aliquota=xtravelrow.codiva
$where   
group by x_scdesc,descli,sottocommessa 
order by min(check_in) asc ";
                break;
        }
        $connection = Yii::$app->db5;
        //yii::error($where);
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        switch ($tipo['x_tiposhow']) {
            case null:

                $whereClauses2[] = "replace(citta,'''','')='" . $citta . "'";
                break;
            case 1:
                $whereClauses2[] = "replace(citta,'''','')='" . $citta . "'";
                break;
            case 2:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                break;
            case 3:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                if ($xcliente <> '') {
                    $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                }
                break;
        }



        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);



        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end
         as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";








        //   yii::error('query gradissca' . $sql);



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
            //   yii::error('query gradissca' . $sql);
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
        /*    $html = $this->renderajax('_detail', [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ]);*/
        //return Json::encode($html);
        return [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
    }

    function xLoadtappa2h(
        $th_id,
        $cliente = null,
        $citta,
        $isservizio = null,
        $xcliente = null,
        $tipo = null
    ) {

        $chk = 0;
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        if ($cliente <> '') {
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        }
        $xcliente = $xcliente ?? null;
        $citta = str_replace("'", "", $citta);
        if ($tipo === null) {
            // yii::warning('xloadtappa2 tipo nulla' . $th_id);
            $connection = Yii::$app->db5;
            $command = $connection->createCommand(
                "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
            );
            $tipo = $command->bindValue(':id', $th_id)->queryOne();
        }
        //Yii::warning($tipo);

        switch ($tipo['x_tiposhow']) {
            case null:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $whereClauses[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta) . "'";
                break;
            case 1:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $whereClauses[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta) . "'";
                break;
            case 2:
                $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                break;
            case 3:
                $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                //     $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                if ($xcliente <> '') {
                    $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                }
                break;
        }


        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        // yii::error('query gradissca'.$sql);
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            switch ($tipo['x_tiposhow']) {
                case null:
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 2:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if ($xcliente <> '') {
                        $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";

            //  yii::error('query 2' . $sql);
            //yii::warning($sql);
            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate,
         max(check_out) as enddate ,CASE WHEN citta IS NULL THEN 
         citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax_unit*qta)as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da
 ELSE citta END 
order by min(check_in) asc ";
        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
                break;
            case 2:
                $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,
--CASE WHEN citta IS NULL THEN citta_da ELSE citta END
sottocommessa
AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar 
$where   
group by x_scdesc,descli,
--CASE WHEN citta IS NULL THEN citta_da ELSE citta END
sottocommessa
order by min(check_in) asc ";
                break;
            case 3:
                $sql = "select x_scdesc,descli,min(check_in) as startdate,
                 max(check_out) as enddate ,
                 sottocommessa as  citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax_unit*qta) as  tassa,
SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,sottocommessa 
order by min(check_in) asc ";
                break;
        }








        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        switch ($tipo['x_tiposhow']) {
            case null:
            case 1:
                //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                $whereClauses2[] = "replace(citta,'''','')='" . str_replace("'", "''", $citta) . "'";
                break;
            case 2:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                break;
            case 3:
                $whereClauses2[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";

                break;
        }
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



            switch ($tipo['x_tiposhow']) {
                case null:
                case 1:
                    //   $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
                    $whereClauses3[] = "replace(citta_da,'''','')='" . $citta . "'";
                    break;
                case 2:
                    $whereClauses3[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    break;
                case 3:
                    $whereClauses3[] = "sottocommessa='" . str_replace("'", "''", $citta) . "'";
                    if ($xcliente <> '') {
                        $whereClauses[] = "cd_cf_ft='" . str_replace("'", "''", $xcliente) . "'";
                    }
                    break;
            }



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
        /*    $html = $this->renderajax('_detail', [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ]);*/
        //return Json::encode($html);
        return [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
    }

    ?>



 <?php $imageUrl = $model->imageFile ?? '/uploads/l_mancante.jpg'; ?>

 <?php
    Modal::begin([
        'id' => 'elecli-modal',
        'title' => 'Elenco Clienti',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'closeButton' => ['label' => 'Chiudi'],
        'options' => [
            'class' => 'fade',
            'tabindex' => false,
            'style' => 'display: none', // Ensure modal starts hidden
        ],
        'bodyOptions' => [
            'class' => 'modal-body p-3',
            'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
        ]
    ]);
    ?>

 <?php
    // yii::error($roomlist);
    //, ['roomlist' => $roomlist]
    echo $this->render('_modal_cli', ['roomlist' => $roomlist]); ?>
 <?php Modal::end() ?>






 <?php

    Modal::begin([
        'id' => 'file-modal',
        'title' => 'Elenco File',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'closeButton' => ['label' => 'Chiudi'],
        'options' => [
            'class' => 'fade',
            'tabindex' => false,
            'style' => 'display: none', // Ensure modal starts hidden
        ],
        'bodyOptions' => [
            'class' => 'modal-body p-3',
            'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
        ]
    ]);
    ?>
 <?php echo $this->render('_modal_file', ['model' => $model]); ?>

 <?php Modal::end() ?>



 <?php
    Modal::begin([
        'id' => 'upload-modal',
        'title' => 'Carica  Immagine',
        'size' => Modal::SIZE_EXTRA_LARGE,
        'closeButton' => ['label' => 'Chiudi'],
        'options' => [
            'class' => 'fade',
            'tabindex' => false,
            'style' => 'display: none', // Ensure modal starts hidden
        ],
        'bodyOptions' => [
            'class' => 'modal-body p-3',
            'style' => 'min-height: 400px; background-color: #ffffff;width:  94%;' // Ensure minimum height and background
        ]
    ]);
    ?>
 <?php echo $this->render('_modal_upload_image', ['model' => $model]); ?>

 <?php Modal::end() ?>

 <?php foreach ($xtmptappe as $value): ?>



     <?php
        if (preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $value['citta']
        )) {
            $venueData = $venueMap[$value['citta']] ?? null;

            if (is_array($venueData)) {
                // $venueMap contiene array da ->asArray()
                $Tcitta = $venueData['citta'] ?? 'N.d.';
                $venue = $venueData['venue'] ?? 'N.d.';
            } elseif (is_object($venueData)) {
                // nel caso raro che siano oggetti ActiveRecord
                $Tcitta = $venueData->citta ?? 'N.d.';
                $venue = $venueData->venue ?? 'N.d.';
            } else {
                // fallback se non c'è corrispondenza in $venueMap
                $Tcitta = $value['citta'] ?? 'N.d.';
                $venue = $value['citta'] ?? 'N.d.';
            }
        } else {
            $venue = $value['citta'];
        }

        Modal::begin([
            'id' => 'citta_' . preg_replace('/[^a-zA-Z0-9]/', '', $value['citta']) .
                preg_replace(
                    '/\s+/',
                    '',
                    ($value['cd_cf_ft'] ?? '')
                ),
            'title' =>  Html::img($imageUrl, ['alt' => 'Icona', 'style' => 'height:20px; margin-right:5px;']) .
                Html::encode($model->descrizione) . ' Dettaglio Venue ' .



                Html::encode($venue),

            'size' => Modal::SIZE_EXTRA_LARGE,
            'closeButton' => ['label' => 'Chiudi'],
            'options' => [
                'class' => 'fade',
                'tabindex' => false,
                'style' => 'display: none', // Ensure modal starts hidden
            ],
            'bodyOptions' => [
                'class' => 'modal-body p-3',
                'style' => 'min-height: 400px; background-color: #ffffff;width:  98%;' // Ensure minimum height and background
            ]
        ]);

        ?>
     <?php echo $this->render('_modal_detail_', [
            'model' => $model,
            'value' => $value,
            'tipo' => $tipo
        ]);
        //  yii::error($value);
        //     yii::error($model);

        ?>
     <?php Modal::end(); ?>
 <?php endforeach; ?>


 <?php
    Modal::begin([
        'id' => 'estrattoContoModal',
        'title' => '<h5>Seleziona il tipo di estratto conto</h5>',
    ]);

    echo Select2::widget([
        'name' => 'tipo_estratto',
        'id' => 'tipo-estratto',
        'data' => [
            1 => 'Solo Hotel',
            2 => 'Solo Viaggi',
            3 => 'Sia Hotel che Viaggi',
        ],
        'options' => [
            'placeholder' => 'Seleziona...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]);

    echo Html::button('Conferma', [
        'class' => 'btn btn-success mt-3',
        'onclick' => 'confermaEstrattoConto(' . $model->th_id . ')'
    ]);

    Modal::end();
    ?>