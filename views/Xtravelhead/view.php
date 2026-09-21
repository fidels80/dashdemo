<style>
    .table-wrapper {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
        width: 100%;
    }

    table {
        font-size: 0.8rem;
        width: 100%;
        table-layout: fixed;
        /* Forza la larghezza fissa delle colonne */
    }

    .table th,
    .table td {
        overflow: hidden;
        /* text-overflow: ellipsis;
        white-space: nowrap;*/
        min-width: 100px;
        /* Imposta una larghezza minima per le celle */
    }

    .table thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        color: #fff;
        z-index: 1;
    }

    tfoot {
        background-color: #e9ecef;
        font-weight: bold;
    }

    tfoot td {
        min-width: 100px;
        /* Imposta una larghezza minima per le celle del footer */
    }

    /* Personalizzazione della barra di scorrimento */
    .table-wrapper::-webkit-scrollbar {
        width: 22px;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 6px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }
</style>
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Xtravelrow;
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */

$this->title = $model->descrizione;
$this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
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
                        'datath',
                        'numero',
                        'descrizione',
                        'timeins',
                        'evaso_A',
                        'evaso_p',
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

<div class="form-group">
    <input id="search-input" type="text" class="form-control" placeholder="Cerca...">
</div>



<?php



$ticketstat_res = Xtravelrow::find()
    ->select([
        'sottocommessa',
        'cd_Ar',
        'descrizione',
        'qta',
        'prezzo',
        'stato',
        'guest',
        'ruolo',
        'cd_cf_ft',
        'descli',
        'citta',
        'fornitore',
        'desfor',
        'struttura',
        'citta_da',
        'citta_a',
        'check_in',
        'check_out',
        'pnr',
        'nr_biglietto',
        'data_pg',
        'cd_pg',
        'contabile',
        'totale',
        'tax',
        'fee',
        'fee_perc',
        'imponibile',
        'iva',
        'Totalegenerale',
        'tax_unit',
        'note',
        'descontab',
        'totfattura',
        'codiva',
        'pagato',
        'x_scdesc',
        'x_pagato'
    ])
    ->where(['th_id' => $model->th_id])
    ->asArray()
    ->all();


yii::warning($ticketstat_res);
?>
<!-- Inserisci Tabulator e Select2 tramite CDN -->
<button id="reset-filters">Reset Filtri</button>

<!-- Bottoni per esportare -->
<button id="download-csv">Esporta in CSV</button>
<button id="download-xlsx">Esporta in Excel</button>
<button id="history-undo">Undo Edit</button>
<button id="history-redo">Redo Edit</button>
<div id="example-table" style="height:500px;"></div>


<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

<script type="text/javascript">
    var sottocommessa = <?= json_encode(array_values(array_unique(array_column(
                            $ticketstat_res,
                            'sottocommessa'
                        )))) ?>;
    var ospiti = <?= json_encode(array_values(array_unique(array_filter(
                        array_column($ticketstat_res, 'guest')
                    )))) ?>;
    //console.log(ospiti);
    var citta = <?= json_encode(array_values(array_unique(array_filter(
                    array_column($ticketstat_res, 'citta')
                )))) ?>;
    var fornitore = <?= json_encode(array_values(array_unique(array_filter(
                        array_column($ticketstat_res, 'fornitore')
                    )))) ?>;
    var descrizione = <?= json_encode(array_values(array_unique(array_filter(
                            array_column($ticketstat_res, 'citta')
                        )))) ?>;
    var articolo = <?= json_encode(array_values(
                        array_unique(array_filter(array_column(
                            $ticketstat_res,
                            'cd_Ar'
                        )))
                    ))
                    ?>;
    var dcli = <?= json_encode(array_values(
                    array_unique(array_filter(array_column(
                        $ticketstat_res,
                        'descli'
                    )))
                ))
                ?>;
    var ruoli = <?= json_encode(array_values(
                    array_unique(array_filter(array_column(
                        $ticketstat_res,
                        'ruolo'
                    )))
                ))
                ?>;
    var dfor = <?= json_encode(array_values(
                    array_unique(array_filter(array_column(
                        $ticketstat_res,
                        'desfor'
                    )))
                ))
                ?>;
    var lstruttura = <?= json_encode(array_values(
                            array_unique(array_filter(array_column(
                                $ticketstat_res,
                                'struttura'
                            )))
                        ))
                        ?>;
    /*cd_Ar
            function sumOredelta(data) {
                return data.reduce(function(sum, row) {
                    return sum + (parseFloat(row.oredelta) || 0);
                }, 0);
            }
    */




    // Configurazione di Tabulator
    var table = new Tabulator("#example-table", {
        data: <?= json_encode($ticketstat_res) ?>, // Dati per la tabella
        layout: "fitColumns", // Adatta le colonne alla larghezza della tabella
        movableColumns: true, // Colonne trascinabili
        history: true, //per undo e redo
        /* persistence: {
              sort: false,
              filter: false,
              columns: true,
          },
          persistenceID: <?= $model->th_id ?>,*/
        columns: [{
                title: "Commessa",
                field: "sottocommessa",
                sorter: "string",
                headerFilter: "input" // Filtro input
            },
            {
                title: "Articolo",
                field: "cd_Ar",
                sorter: "string",
                headerFilter: "list", // Usa una lista a tendina come filtro
                headerFilterParams: {
                    values: articolo, // Passa la lista dinamica di clienti
                    clearable: true // Opzione per permettere di resettare il filtro
                }
            },
            {
                title: "descrizione articolo",
                field: "descrizione",
                editor: "input",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro

            },
            {
                title: "Qta",
                field: "qta",
                sorter: "number",
                headerFilter: "input",
                bottomCalc: "sum" // Usa una lista a tendina come filtro

            },
            {
                title: "Prz ",
                field: "prezzo",
                sorter: "number",
                headerFilter: "input",
                bottomCalc: "avg" // Usa una lista a tendina come filtro

            },
            {
                title: "Ospiti",
                field: "guest",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro

            },

            {
                title: "Ruolo",
                field: "ruolo",
                sorter: "string",
                headerFilter: "list", // Usa una lista a tendina come filtro
                headerFilterParams: {
                    values: ruoli, // Passa la lista dinamica di clienti
                    clearable: true // Opzione per permettere di resettare il filtro
                }
            },


            {
                title: "Cod Cliente",
                field: "cd_cf_ft",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro

            },
            {
                title: "Des.Cli",
                field: "descli",
                sorter: "string",
                headerFilter: "list", // Usa una lista a tendina come filtro
                headerFilterParams: {
                    values: dcli, // Passa la lista dinamica di clienti
                    clearable: true // Opzione per permettere di resettare il filtro
                }
            },
            {
                title: "Città",
                field: "citta",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro
                headerFilterParams: {
                    values: citta, // Passa la lista dinamica di clienti
                    clearable: true // Opzione per permettere di resettare il filtro
                }
            },
            {
                title: "Cod Fornitore",
                field: "fornitore",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro

            },
            {
                title: "Des. Fornitore",
                field: "desfor",
                sorter: "string",
                headerFilter: "list", // Usa una lista a tendina come filtro
                headerFilterParams: {
                    values: dfor, // Passa la lista dinamica di clienti
                    clearable: true // Opzione per permettere di resettare il filtro
                }
            },
            {
                title: "Struttura",
                field: "struttura",
                sorter: "string",
                headerFilter: "input", // Usa una lista a tendina come filtro
                //headerFilterParams: {
                //    values: lstruttura, // Passa la lista dinamica di clienti
                //    clearable: true // Opzione per permettere di resettare il filtro
                //},

                // editor: select2Editor,

            },

            {
                title: "Check_in",
                field: "check_in",
                editor: "datetime",
                headerFilter: "input",
                editorParams: {
                    format: "dd/MM/yyyy hh:mm", // the format of the date value stored in the cell
                    verticalNavigation: "table", //navigate cursor around table without changing the value
                    elementAttributes: {
                        title: "slide bar to choose option" // custom tooltip
                    }
                }
            },
            {
                title: "check_out",
                field: "check_out",
                hozAlign: "center",
                editor: "datetime",
                sorter: "date",
                headerFilter: "input",
                editorParams: {
                    format: "dd/MM/yyyy hh:mm", // the format of the date value stored in the cell
                    verticalNavigation: "table", //navigate cursor around table without changing the value
                    elementAttributes: {
                        title: "slide bar to choose option" // custom tooltip
                    }
                }
            },
        ],

    });

    // Pulsante per resettare tutti i filtri
    document.getElementById('reset-filters').addEventListener('click', function() {
        table.clearHeaderFilter(); // Resetta tutti i filtri
    });


    // Esporta in CSV
    document.getElementById('download-csv').addEventListener('click', function() {
        table.download("csv", "dati.csv");
    });
    // Esporta in Excel (XLSX)
    document.getElementById('download-xlsx').addEventListener('click', function() {
        table.download("xlsx", "dati.xlsx", {
            sheetName: "Dati"
        });
    });




    document.getElementById("history-undo").addEventListener("click", function() {
        table.undo();
    });

    //redo button
    document.getElementById("history-redo").addEventListener("click", function() {
        table.redo();
    });
</script>




<!-- Inclusione della libreria XLSX per esportazione Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.1/xlsx.core.min.js"></script>












<br>
<br><br><br><br><br><br><br><br><br><br><br>
<br>
<br><br><br><br><br><br><br><br><br><br><br>















<div class="table-responsive-sm">
    <div class="table-wrapper">

        <table id="xtravel-rows" class="table table-striped table-bordered table-hover">
            <thead class="thead-dark">
                <tr>
                    <!-- Intestazioni della tabella -->
                    <th scope="col">Commessa</th>
                    <th scope="col">Guest</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Descrizione</th>
                    <th scope="col">Città</th>
                    <th scope="col">Cd.Ar</th>
                    <th scope="col">Descrizione</th>
                    <th scope="col">Fornitore</th>
                    <th scope="col">Descrizione</th>
                    <th scope="col">Struttura</th>
                    <th scope="col">Da</th>
                    <th scope="col">A</th>
                    <th scope="col">Check In</th>
                    <th scope="col">Check Out</th>
                    <th scope="col">Qta</th>
                    <th scope="col">Pnr</th>
                    <th scope="col">Nr Big.</th>
                    <th scope="col">Prezzo</th>
                    <th scope="col">Iva</th>
                    <th scope="col">Tax</th>
                    <th scope="col">Fee</th>
                    <th scope="col">Imp.</th>
                    <th scope="col">Tot.Generale</th>
                </tr>
            </thead>
            <tbody id="xtravel-rows-body">
                <?php
                $rows = Xtravelrow::find()
                    ->select(['sottocommessa', 'cd_Ar', 'descrizione', 'qta', 'prezzo', 'stato', 'guest', 'ruolo', 'cd_cf_ft', 'descli', 'citta', 'fornitore', 'desfor', 'struttura', 'citta_da', 'citta_a', 'check_in', 'check_out', 'pnr', 'nr_biglietto', 'data_pg', 'cd_pg', 'contabile', 'totale', 'tax', 'fee', 'fee_perc', 'imponibile', 'iva', 'Totalegenerale', 'tax_unit', 'note', 'descontab', 'totfattura', 'codiva', 'pagato', 'x_scdesc', 'x_pagato'])
                    ->where(['th_id' => $model->th_id])
                    ->asArray()
                    ->all();
                $tqta = 0;
                $tprice = 0;
                $tiva = 0;
                $tax = 0;
                $tfee = 0;
                $timponibile = 0;
                $totalgen = 0;
                foreach ($rows as $value) {
                    echo '<tr class="xtravel-row">';
                    echo '<td>' . Html::encode($value['sottocommessa']) . '</td>';
                    echo '<td>' . Html::encode($value['guest']) . '</td>';
                    echo '<td>' . Html::encode($value['cd_cf_ft']) . '</td>';
                    echo '<td>' . Html::encode($value['descli']) . '</td>';
                    echo '<td>' . Html::encode($value['citta']) . '</td>';
                    echo '<td>' . Html::encode($value['cd_Ar']) . '</td>';
                    echo '<td>' . Html::encode($value['descrizione']) . '</td>';
                    echo '<td>' . Html::encode($value['fornitore']) . '</td>';
                    echo '<td>' . Html::encode($value['desfor']) . '</td>';
                    echo '<td>' . Html::encode($value['struttura']) . '</td>';
                    echo '<td>' . Html::encode($value['citta_da']) . '</td>';
                    echo '<td>' . Html::encode($value['citta_a']) . '</td>';
                    echo '<td>' . Html::encode($value['check_in']) . '</td>';
                    echo '<td>' . Html::encode($value['check_out']) . '</td>';
                    echo '<td>' . Html::encode($value['qta']) . '</td>';
                    echo '<td>' . Html::encode($value['pnr']) . '</td>';
                    echo '<td>' . Html::encode($value['nr_biglietto']) . '</td>';
                    echo '<td>' . Html::encode($value['prezzo']) . '</td>';
                    echo '<td>' . Html::encode($value['iva']) . '</td>';
                    echo '<td>' . Html::encode($value['tax']) . '</td>';
                    echo '<td>' . Html::encode($value['fee']) . '</td>';
                    echo '<td>' . Html::encode($value['imponibile']) . '</td>';
                    echo '<td>' . Html::encode($value['Totalegenerale']) . '</td>';
                    echo '</tr>';
                    $tqta += $value['qta'];
                    $tprice += $value['prezzo'];
                    $tiva += $value['iva'];
                    $tax += $value['tax'];
                    $tfee += $value['fee'];
                    $timponibile += $value['imponibile'];
                    $totalgen += $value['Totalegenerale'];
                }
                ?>
            </tbody>
            <tfoot id="xtravelfoot">
                <tr id="totals-row">
                    <td colspan="14"></td> <!-- Colonne non numeriche -->
                    <td id="total-quantity"><?php echo $tqta; ?></td>
                    <td></td>
                    <td></td>
                    <td id="total-price"><?php echo $tprice; ?></td>
                    <td id="total-iva"><?php echo $tiva; ?></td>
                    <td id="total-tax"><?php echo $tax; ?></td>
                    <td id="total-fee"><?php echo $tfee; ?></td>
                    <td id="total-imponibile"><?php echo $timponibile; ?></td>
                    <td id="total-total"><?php echo $totalgen; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<br>
<br><br><br><br><br><br><br><br><br><br><br>
<br>
<br><br><br><br><br><br><br><br><br><br><br>

<script>
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
    });
</script>

<br>
<br><br><br><br><br><br><br><br><br><br><br>
<br>
<br><br><br><br><br><br><br><br><br><br><br>

<?php
use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;

// Creazione dell'ActiveDataProvider
$dataProvider = new ActiveDataProvider([
    'query' => Xtravelrow::find()
        ->select(['sottocommessa', 'cd_Ar', 'descrizione', 'qta', 'prezzo', 'stato', 'guest', 'ruolo'])
        ->where(['th_id' => $model->th_id]),
    'pagination' => [
        'pageSize' => 20,
    ],
]);

// Configurazione delle colonne con raggruppamento
$columns = [
    ['class' => 'kartik\grid\SerialColumn'], // Colonna numerica
    [
        'attribute' => 'sottocommessa',
        'vAlign' => 'middle',
        'hAlign' => 'center',
        'group' => true, // Abilita il raggruppamento per questa colonna
        'groupFooter' => function ($model, $key, $index, $widget) {
            // Esempio di footer di gruppo: calcolo di totali
            return [
                'mergeColumns' => [[0, 2]], // Unisci le prime due colonne nel footer
                'content' => [ // Testo del footer per ogni colonna
                    1 => 'Totale per gruppo:',
                     3 => GridView::F_SUM, // Colonna `qta`
                        4 => GridView::F_SUM, // Colonna `prezzo`
                ],
                'contentFormats' => [ // Formatta i dati (per esempio numeri con decimali)
                    3 => ['decimal', 2],
                    4 => ['decimal', 2], // Colonna `prezzo`
                ],
                'contentOptions' => [ // Stile delle colonne nel footer
                    1 => ['style' => 'font-weight:bold;'],
                    3 => ['style' => 'text-align:right;'],
                      4 => ['style' => 'text-align:right; font-weight:bold;'],
         
                ],
                'options' => ['class' => 'info', 'style' => 'font-weight:bold;'],
            ];
        },
    ],
    [
        'attribute' => 'cd_Ar',
        'vAlign' => 'middle',
         'group' => true, // Abi
    ],
    [
        'attribute' => 'descrizione',
        'vAlign' => 'middle',
    ],
    [
        'attribute' => 'qta',
        'vAlign' => 'middle',
        'hAlign' => 'right',
        'format' => ['decimal', 2],
            'pageSummary' => true,
            'footer' => true, 
    ],
    [
        'attribute' => 'prezzo',
        'vAlign' => 'middle',
        'hAlign' => 'right',
        'format' => ['decimal', 2],
            'pageSummary' => true,
            'footer' => true, 
    ],
    [
        'attribute' => 'stato',
        'vAlign' => 'middle',
    ],
    ['class' => 'kartik\grid\ActionColumn'], // Colonna per azioni (modifica/elimina)
];

// Widget DynaGrid con raggruppamenti
echo DynaGrid::widget([
    'columns' => $columns,
    'storage' => DynaGrid::TYPE_COOKIE, // Salva le impostazioni nei cookie
    'theme' => 'panel-primary',
    'gridOptions' => [
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel, // Scommenta se hai un filtro
        'showPageSummary' => true, // Abilita il riepilogo (sommario globale)
         'showFooter' => true, // Assicura che i footer siano visibili
        'toolbar' => [
            ['content' =>
                \yii\helpers\Html::button('<i class="glyphicon glyphicon-plus"></i>', [
                    'type' => 'button',
                    'title' => 'Add Book',
                    'class' => 'btn btn-success',
                ]) . ' ' .
                \yii\helpers\Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['grid-demo'], [
                    'data-pjax' => 0,
                    'class' => 'btn btn-default',
                    'title' => 'Reset Grid',
                ])
            ],
            ['content' => '{export}{toggleData}'],
            ['content' => '{dynagrid}'], // Pulsante per la personalizzazione
        ],
        'panel' => [
            'heading' => '<h3 class="panel-title">Library</h3>',
            'before' => '<em>* Adjust columns, sorting, and more using DynaGrid options.</em>',
        ],
        'export' => [
            'fontAwesome' => true, // Abilita icone FontAwesome
        ],
    ],
    'options' => ['id' => 'dynagrid-1'],
]);
?>


<?php
 

?>








<?php
// Ottieni i dati da Yii2
$rows = Xtravelrow::find()
    ->select(['sottocommessa', 'cd_Ar', 'descrizione', 'qta', 'prezzo', 'stato', 'guest', 'ruolo', 'cd_cf_ft', 'descli', 'citta', 'fornitore', 'desfor', 'struttura', 'citta_da', 'citta_a', 'check_in', 'check_out', 'pnr', 'nr_biglietto', 'data_pg', 'cd_pg', 'contabile', 'totale', 'tax', 'fee', 'fee_perc', 'imponibile', 'iva', 'Totalegenerale', 'tax_unit', 'note', 'descontab', 'totfattura', 'codiva', 'pagato', 'x_scdesc', 'x_pagato'])
    ->where(['th_id' => $model->th_id])
    ->asArray()
    ->all();
?>
 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>

<table id="table"
 
  >
  <thead>
    <tr>
      <th data-field="sottocommessa">ID</th>
      <th data-field="cd_Ar">Item Name</th>
      <th data-field="descrizione">Item Price</th>
    </tr>
  </thead>
</table>




 <script>
  var $table = $('#table')

  $(function() {
    var data =  <?php json_encode($rows)?>
    $table.bootstrapTable({data: data})
  })
</script>