<?php

use kartik\export\ExportMenu;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

    $this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);


?>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title">
                Tappa <?php echo $citta; ?></h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Commessa</TH>
                    <TH>CLiente</TH>
                    <TH>Data Inizio</TH>
                    <TH>Data Fine</TH>
                    <TH>Totale</TH>
                    <TH>Tassa</TH>
                    <TH>Fee</TH>
                    <TH>Totale Generale</TH>
                </thead>
                <?php
                $tottassa = 0;
                $totfee = 0;
                $tottalecard = 0;
                $totgen = 0;
                foreach ($totale as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo $row['x_scdesc'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo $row['descli'];
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['startdate']));
                    echo '</td>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['enddate']));
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo round($row['totale'], 2);
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo round($row['tassa'], 2);
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo round($row['fee'], 2);
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo round($row['totalegenerale'], 2);
                    echo '</td>';
                    echo '</tr>';
                    $tottassa = $tottassa + $row['tassa'];
                    $totfee = $totfee + $row['fee'];
                    $tottalecard = $tottalecard + $row['totale'];
                    $totgen = $totgen + $row['totalegenerale'];
                }
                echo '<TR>';
                echo '<td scope="col" colspan=4 >';
                echo '<strong>TOTALE GENERALE</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€ ' . $tottalecard . '</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€ ' . $tottassa . '</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€ ' . $totfee . '</strong>';
                echo '</td>';
                echo '<td scope="col">';
                echo '<strong>€ ' . $totgen . '</strong>';
                echo '</td>';
                echo '</tr>'

                ?>

            </table>
        </div>
    </div>


    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Pagamenti</h5>

            <table class="table  table-sm table-hover table-responsive-sm table-fit">
                <thead class="thead-dark">
                    <TH>Data</TH>
                    <TH>Importo</TH>
                    <TH>Residuo</TH>

                </thead>
                <?php
                $i = 1;
                $tempdapagare = 0;
                $tpag = 0;
                foreach ($pagamenti as $row) {
                    echo '<TR>';
                    echo '<td scope="col">';
                    echo date('d/m/Y', strtotime($row['data_pg']));
                    echo '</td>';
                    echo '<td scope="col">€ ';
                    echo  round($row['xpagato'], 2);
                    echo '</td>';
                    echo '<td scope="col">€ ' ;
                    $tpag = $tpag + round($row['xpagato'], 2);
                    if ($i == 1) {
                        echo  $totgen - round($row['xpagato'], 2);
                        $tempdapagare = $totgen - round($row['xpagato'], 2);
                    } else {
                        echo  $tempdapagare - round($row['xpagato'], 2);
                        $tempdapagare = $tempdapagare - round($row['xpagato'], 2);
                    }
                    echo '</td>';
                    echo '</tr>';
                    $i++;
                }
                echo '<TR>';
                echo '<td><strong>Totali</strong>';
                echo '</td>';
                echo '<td>';
                echo '<strong>€ ' . $tpag;
                echo '</strong></td>';
                echo '<td><strong>';
                echo '€ ' . $tempdapagare;
                echo '</strong></td>';
                echo '</tr>';

                ?>
            </table>
        </div>
    </div>




</div>


<br>Dettaglio<br>
<?php


// Definisci le colonne che desideri esportare

$gridColumns = [
    [
        'attribute' => 'x_scdesc',
        'label' => 'Commessa',
        'group' => true,
    ],
    [
        'attribute' => 'descli',
        'label' => 'Cliente',
        'group' => true,
        'subGroupOf' => 0,
    ],
    [
        'attribute' => 'struttura',
        'label' => 'Struttura',
        'group' => true,
        'subGroupOf' => 1,
    ],
    [
        'attribute' => 'guest',
        'label' => 'Ospite',
    ],
    [
        'attribute' => 'ruolo',
        'label' => 'Ruolo',
    ],
    [
        'attribute' => 'check_in',
        'label' => 'Data Check-in',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'attribute' => 'check_out',
        'label' => 'Data Check-out',
        'format' => ['date', 'php:d/m/Y'],
    ],
    [
        'attribute' => 'qta',
        'label' => 'Quantità',
        'format' => ['integer'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'prezzo',
        'label' => 'Prezzo per Notte',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'tax_unit',
        'label' => 'Tassa per Unità',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'totale',
        'label' => 'Totale',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'tax',
        'label' => 'Tassa',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'fee',
        'label' => 'Fee',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
    [
        'attribute' => 'iva',
        'label' => 'IVA',
        'format' => ['currency', 'EUR'],
        'pageSummary' => true,
    ],
];




$dataProvider = new ArrayDataProvider([
    'allModels' => $dettaglio,
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    ],
    'sort' => [
        'attributes' => ['x_scdesc', 'descli', 'struttura', 'guest'], // Attributi ordinabili
    ],
]);
// Crea il menu di esportazione

$exportConfig = [
    ExportMenu::FORMAT_EXCEL => [
        'label' => 'Excel',
        'filename' => 'Export_Excel_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Excel verrà generato per il download.',
        'options' => ['title' => 'Esporta in Excel'],
    ],
    ExportMenu::FORMAT_CSV => [
        'label' => 'CSV',
        'filename' => 'Export_CSV_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file CSV verrà generato per il download.',
        'options' => ['title' => 'Esporta in CSV'],
    ],
    ExportMenu::FORMAT_TEXT => [
        'label' => 'Text',
        'filename' => 'Export_Text_' . date('Y-m-d_H-i-s'),
        'alertMsg' => 'Il file Text verrà generato per il download.',
        'options' => ['title' => 'Esporta in Text'],
    ],
    // Disabilita altri formati se non necessari
    ExportMenu::FORMAT_PDF => false,
    ExportMenu::FORMAT_HTML => false,
];
$defaultStyle = [
    'borders' => [
        'outline' => [
            //'//borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['argb' => 'black'],
        ],
        'inside' => [
         //   'borderStyle' => Border::BORDER_DOTTED,
            'color' => ['argb' => 'BLACK'],
        ]
    ],
];



echo ExportMenu::widget([
    'id' => 'exp_button', // Imposta l'ID per il bottone
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'exportConfig' => $exportConfig,
    'filename' => 'Export_' . date('Y-m-d_H-i-s'),
    'target' => ExportMenu::TARGET_BLANK,
    'showColumnSelector' => true,
    'clearBuffers' => true,
 //   'class'=>'pino',
    'dropdownOptions' => [
        'label' => 'Esporta Dati',
        'class' => 'PErsonale', // Solo classi personalizzate
        'title' => 'Esporta i dati nel formato selezionato',
        'data-toggle' => 'dropdown',
        'aria-haspopup' => 'true',
        'aria-expanded' => 'false',
    ],
]);
?>

<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Commessa</th>
        <th>Cliente</th>
        <th>Cognome - Nome</th>
        <th>RUOLO</th>
        <th>PARTY</th>
        <th>STRUTTURA</th>
        <th>DATA IN</th>
        <th>DATA OUT</th>
        <th>TOT.NOTTI</th>
        <th>Notti Tax</th>
        <th>CONFERMA</th>
        <th>TIPOLOGIA</th>
        <th>COSTO Notte</th>
        <th>CITY TAX</th>
        <th>TOT COST</th>
        <th>TOT CITY</th>
        <th>TAX PARK</th>
        <th>EXTRAS</th>
        <th>TOTALE Complessivo</th>
        <th>7%</th>
        <th>FEE</th>
        <th>IVA</th>

    </thead>
    <?php
    $tqta = 0;
    $ttotale = 0;
    $ttax = 0;
    $tfee = 0;
    $tiva = 0;
    $prev_scdesc = '';
    $prev_descli = '';
    $prev_struttura = '';

    foreach ($dettaglio as $value) {

        // Check if x_scdesc, descli, or struttura has changed
        if ($prev_scdesc != '' && ($prev_scdesc != $value['x_scdesc'] || $prev_descli != $value['descli'] || $prev_struttura != $value['struttura'])) {
            // Print the totals for the previous group
            echo '<tr class="table-total bg-primary">';
            echo '<td colspan="8" ><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
            echo '<td><strong>' . $tqta . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Notti Tax"
            echo '<td></td>'; // Colonna vuota per "Conferma"
            echo '<td></td>'; // Colonna vuota per "Tipologia"
            echo '<td></td>'; // Colonna vuota per "Costo Notte"
            echo '<td></td>'; // Colonna vuota per "City Tax"
            echo '<td><strong>€ ' . round($ttotale, 2) . '</strong></td>';
            echo '<td><strong>€ ' . round($ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "Tax Park"
            echo '<td></td>'; // Colonna vuota per "Extras"
            echo '<td><strong>€ ' . round($ttotale + $ttax, 2) . '</strong></td>';
            echo '<td></td>'; // Colonna vuota per "7%"
            echo '<td><strong>€ ' . round($tfee, 2) . '</strong></td>';
            echo '<td><strong>€ ' . round($tiva, 2) . '</strong></td>';
            // echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
            echo '</tr>';

            // Reset totals
            $tqta = 0;
            $ttotale = 0;
            $ttax = 0;
            $tfee = 0;
            $tiva = 0;
        }

        // Print the current row
        echo '<tr>';
        echo '<td scope="col">' . $value['x_scdesc'] . '</td>';
        echo '<td scope="col">' . $value['descli'] . '</td>';
        echo '<td scope="col">' . $value['guest'] . '</td>';
        echo '<td scope="col">' . $value['ruolo'] . '</td>';
        echo '<td scope="col">' . $value['party'] . '</td>';
        echo '<td scope="col">' . $value['struttura'] . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_in'])) . '</td>';
        echo '<td scope="col">' . date('d/m/Y', strtotime($value['check_out'])) . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col">' . $value['qta'] . '</td>';
        echo '<td scope="col"> si</td>';
        echo '<td scope="col"> ' . $value['cd_Ar'] . '</td>';
        echo '<td scope="col"> € ' . round($value['prezzo'], 2) . '</td>';
        echo '<td scope="col"> € ' . round($value['tax_unit'], 2) . '</td>';
        echo '<td scope="col"> € ' . round($value['totale'], 2) . '</td>';
        echo '<td scope="col"> € ' . round($value['tax'], 2) . '</td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col"> </td>';
        echo '<td scope="col"> € ' . (round($value['totale'], 2) + round($value['tax'], 2)) . '</td>';
        echo '<td scope="col">' . round($value['fee_perc'], 2) . '% </td>';
        echo '<td scope="col"> € ' . round($value['fee'], 2) . ' </td>';
        echo '<td scope="col"> € ' . round($value['iva'], 2) . ' </td>';
        echo '</tr>';

        // Accumulate totals
        $tqta += $value['qta'];
        $ttotale += round($value['totale'], 2);
        $ttax += round($value['tax'], 2);
        $tfee += round($value['fee'], 2);
        $tiva += round($value['iva'], 2);

        // Update the previous values
        $prev_scdesc = $value['x_scdesc'];
        $prev_descli = $value['descli'];
        $prev_struttura = $value['struttura'];
    }

    // Print the totals for the last group
    if ($prev_scdesc != '') {
        echo '<tr class="table-total bg-primary">';
        echo '<td colspan="8"><strong>Totale per ' . $prev_scdesc . ' - ' . $prev_descli . ' - ' . $prev_struttura . '</strong></td>';
        echo '<td><strong>' . $tqta . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "Notti Tax"
        echo '<td></td>'; // Colonna vuota per "Conferma"
        echo '<td></td>'; // Colonna vuota per "Tipologia"
        echo '<td></td>'; // Colonna vuota per "Costo Notte"
        echo '<td></td>'; // Colonna vuota per "City Tax"
        echo '<td><strong>€ ' . round($ttotale, 2) . '</strong></td>';
        echo '<td><strong>€ ' . round($ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "Tax Park"
        echo '<td></td>'; // Colonna vuota per "Extras"
        echo '<td><strong>€ ' . round($ttotale + $ttax, 2) . '</strong></td>';
        echo '<td></td>'; // Colonna vuota per "7%"
        echo '<td><strong>€ ' . round($tfee, 2) . '</strong></td>';
        echo '<td><strong>€ ' . round($tiva, 2) . '</strong></td>';
        //  echo '<td colspan="3"></td>'; // Colonne vuote per "Note di riga", "Note Hotel", "Cancellation Policy"
        echo '</tr>';
    }
    ?>
</table>



<script>
  /*  document.addEventListener('DOMContentLoaded', function() {
        function applyStyles() {
            var button = document.querySelector('.btn.btn-primary.btn-outline-secondary.dropdown-toggle');
            if (button) {
                button.style.color = '#007bff';
                button.style.backgroundColor = '#fff';
                button.style.borderColor = '#007bff';
                button.style.boxShadow = 'none';
            }
        }

        // Applica gli stili inizialmente
        applyStyles();

        // Applica gli stili anche ogni 500ms per coprire i cambiamenti dinamici
        setInterval(applyStyles, 500);
    });
*/
    document.addEventListener('DOMContentLoaded', function() {
        // Funzione per applicare gli stili
        function applyStyles() {
            var button = document.querySelector('.btn.btn-primary.btn-outline-secondary.dropdown-toggle');
            if (button) {
                button.style.color = '#007bff';
                button.style.backgroundColor = '#fff';
                button.style.borderColor = '#007bff';
                button.style.boxShadow = 'none';
            }
        }

        // Applica gli stili inizialmente
        applyStyles();

        // Osservatore per monitorare le modifiche al DOM
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    applyStyles(); // Applica gli stili quando ci sono modifiche nel DOM
                }
            });
        });

        // Configura l'osservatore per monitorare il corpo del documento
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>