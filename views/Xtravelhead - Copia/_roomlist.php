<?php

use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
$this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);






?>

<?php
if ($tipo == 1) {

// Definisci le colonne che desideri esportare

$gridColumns = [
    [
        'attribute' => 'guest',
        'label' => 'Ospite',
         
    ],
    [
        'attribute' => 'cd_Ar',
        'label' => 'Tipo Camera',
         
    ],
    [
        'attribute' => 'note',
        'label' => 'Note',
     ],
  ];




$dataProvider = new ArrayDataProvider([
    'allModels' => $roomlist,
    'pagination' => [
        'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
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

echo '<br>';
echo '<br>';


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





    echo '
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Ospite</th>
        <th>Tipo Camera</th>
        <th>Note</th>
    </thead>';

    foreach ($roomlist as $value) {
        echo '<tr>';
        echo '<td>';
        echo isset($value['guest']) ? str_replace(["'", '"', "’", "´"], '', $value['guest']) : '';


        echo '</td>';
        echo '<td>';
        echo isset($value['cd_Ar'])?$value['cd_Ar']:'';
        echo '</td>';
        echo '<td>';
        echo isset($value['note'])?$value['note']:'';
        echo '</td>';

        echo '</tr>';
    }
} else {

    echo '<h3>Elenco Tappe</h3>
    <table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>DATA SHOW</th>
        <th> CITTA\'</th>
        <th>VENUE</th>
        <th> Media costo Camera</th>
        <th>Tot. Imponibile</th>
        <th>City Tax</th>
        <th>IVA Pagata</th>
        <th>Totale Pagato</th>
        <th>TOTALE FEE</th>
        <th>TOTALE IMPONIBILE FATTURA</th>
    </thead>

';


    foreach ($roomlist['tappetour'] as $value) {
        echo '<tr>';
        echo '<td>';
        echo 'Dal ' . date('d/m/Y', strtotime($value['datainizio'])) . ' al ' . date('d/m/Y', strtotime($value['datafine']));
        echo '</td>';
        echo '<td>';
        echo $value['citta'];
        echo '</td>';
        echo '<td>';
        echo $value['citta'];
        echo '</td>';
        echo '<td>€ ';
        echo round($value['mprezzo'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['imponibile'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['ctax'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['iva'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['totale'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['fee'], 2);
        echo '</td>';
        echo '<td>€ ';
        echo round($value['totaleimpfatt'], 2);
        echo '</td>';
        echo '</tr>';
    }
    echo '<tr  class="table-total bg-primary">';
    echo '<td colspan=3> <StRONG> Totali </td>';
    echo '<td> <StRONG>€ ';
    $eta = array_column($roomlist['tappetour'], 'mprezzo');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);

    // Calcola il numero di elementi
    $numeroElementi = count($eta);

    // Calcola la media
    $mediaEta = $sommaEta / $numeroElementi;
    echo round($mediaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'imponibile');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'ctax');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'iva');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'totale');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'fee');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
    $eta = array_column($roomlist['tappetour'], 'totaleimpfatt');

    // Calcola la somma degli anni
    $sommaEta = array_sum($eta);
    echo '<td> <StRONG>€ ';
    echo round($sommaEta, 2) . '</td>';
}



?>

</table>
<br>
<br>
<?php
if ($tipo <> 1) {

    // Inizializza variabili
    $previousClient = null;
    $totals = [
        'mprezzo' => 0,
        'imponibile' => 0,
        'ctax' => 0,
        'iva' => 0,
        'totale' => 0,
        'fee' => 0,
        'totaleimpfatt' => 0
    ];

    // Funzione per chiudere la tabella e mostrare i totali
    function closeTablex($totals)
    {
        echo '<tr  class="table-total bg-primary">';
        echo '<td colspan=2> <strong>Totali</strong> </td>';
        echo '<td> <strong>€ ' . round($totals['mprezzo'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['imponibile'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['ctax'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['iva'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['totale'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['fee'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['totaleimpfatt'], 2) . '</strong></td>';
        echo '<td> <strong>€ ' . round($totals['totaleimpfatt'], 2)+round($totals['fee'], 2)  .'</strong></td>';
        echo '</tr>';
        echo '</table>';
    }

    // Scorri l'array
    foreach ($roomlist['tappetourcli'] as $value) {
        if ($previousClient !== $value['descli']) {
            // Se non è il primo cliente, chiudi la tabella precedente
            if ($previousClient !== null) {
                closeTablex($totals);
            }

            // Inizia una nuova tabella per il nuovo cliente
            echo '<h3>Elenco Clienti</h3>
            <table class="table table-sm table-hover table-responsive-sm table-fit">
            <thead class="thead-dark">
            <th>Cliente</th>
            <th>TAPPA</th>    
            <th>Imponibile Hotel</th>
            <th>CityTax</th>
            <th>IVA Hotel</th>
            <th>Totale Pagato Hotel</th>
            <th>FEE Agenzia</th>
            <th>FT da Emettere Imponibile</th>
            <th>IVA</th>        
            <th>TOTALE FT</th>
        </thead>';

            // Resetta i totali
            $totals = [
                'mprezzo' => 0,
                'imponibile' => 0,
                'ctax' => 0,
                'iva' => 0,
                'totale' => 0,
                'fee' => 0,
                'totaleimpfatt' => 0
            ];

            $previousClient = $value['descli'];
        }

        // Aggiungi i dati alla tabella
        echo '<tr>';
        echo '<td>' . $value['descli'] . '</td>';
        echo '<td>' . $value['citta'] . '</td>';
        echo '<td>€ ' . round($value['mprezzo'], 2) . '</td>';
        echo '<td>€ ' . round($value['imponibile'], 2) . '</td>';
        echo '<td>€ ' . round($value['ctax'], 2) . '</td>';
        echo '<td>€ ' . round($value['iva'], 2) . '</td>';
        echo '<td>€ ' . round($value['totale'], 2) . '</td>';
        echo '<td>€ ' . round($value['fee'], 2) . '</td>';
        echo '<td>€ ' . round($value['totaleimpfatt'], 2) . '</td>';
        echo '<td>€ ' . round($value['totaleimpfatt'], 2)+ round($value['fee'], 2) . '</td>';
        echo '</tr>';

        // Aggiorna i totali
        $totals['mprezzo'] += $value['mprezzo'];
        $totals['imponibile'] += $value['imponibile'];
        $totals['ctax'] += $value['ctax'];
        $totals['iva'] += $value['iva'];
        $totals['totale'] += $value['totale'];
        $totals['fee'] += $value['fee'];
        $totals['totaleimpfatt'] += $value['totaleimpfatt'];
    }

    // Chiudi l'ultima tabella
    closeTablex($totals);

/* 'dotes' => [
        [
            'DataDoc' => '2024-06-07 00:00:00',
            'NumeroDoc' => '    22/3  ',
            'cd_do' => 'FVH',
            'TotaPagareV' => '476.750000',
            'AccontoV' => '.000000',
            'TotDocumentoV' => '476.750000',
        ],*/


   echo '<h3>Elenco Documenti Emessi</h3>
   <table class="table table-sm table-hover table-responsive-sm table-fit">
            <thead class="thead-dark">
            <th>Codice Documento</th>
            <th>Numero</th>    
            <th>Data</th>
            <th>Totale a pagare</th>
            <th>Acconto</th>
            <th>Totale Documento</th>
            </thead>
            ';
 foreach ($roomlist['dotes']as $value){
        echo '<tr>';
        echo '<td>' . $value['cd_do'] . '</td>';
         echo '<td>' . $value['NumeroDoc'] . '</td>';
        echo '<td>' .  date('d/m/Y', strtotime($value['DataDoc'] )). '</td>';
         echo '<td>€ ' . round($value['TotDocumentoV'],2) . '</td>';
        echo '<td>€ ' . round($value['AccontoV'],2) . '</td>';
        echo '<td>€ ' . round($value['TotaPagareV'],2) . '</td>';
        echo '</tr>';

 }

echo '</table>';
}
?>
