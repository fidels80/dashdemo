<?php

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Html;
use kartik\select2\Select2;

$gridColumns = [
    [
        'attribute' => 'party',
        'label' => 'Party',

    ],
    [
        'attribute' => 'ttotale',
        'label' => 'Totale',

    ],
    [
        'attribute' => 'timponibile',
        'label' => 'Imponibile',
    ],
    [
        'attribute' => 'ttassa',
        'label' => 'Tassa',
    ],
    [
        'attribute' => 'tiva',
        'label' => 'Iva',
    ],
    [
        'attribute' => 'tfee',
        'label' => 'Fee',
    ],
];




$dataProvider = new ArrayDataProvider([
    'allModels' => $dettaglio,
    //'pagination' => [
    //    'pageSize' => 20, // Puoi modificare la paginazione a tuo piacimento
    //],

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
    'id' => 'exp_button_roomlist', // Imposta l'ID per il bottone
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


<?php
$this->registerJsFile('@web/js/echarts.js', [
    'depends' => [\yii\web\YiiAsset::class], // opzionale, carica dopo jQuery/YiiAsset
]);
?>

<h3>Costo Party</h3>
<table class="table">
    <thead>
        <th style="background-color: #f1eef6; color: #002c48;">Party</th>
        <th style="background-color: #f1eef6; color: #002c48;">Totale</th>
        <th style="background-color: #f1eef6; color: #002c48;">Pagato Hotel</th>
        <th style="background-color: #f1eef6; color: #002c48;">Imponibile</th>
        <th style="background-color: #f1eef6; color: #002c48;">City Tax</th>
        <th style="background-color: #f1eef6; color: #002c48;">Iva</th>
        <th style="background-color: #f1eef6; color: #002c48;">Fee</th>
    </thead>
    <?php


    $tttotale = 0;
    $ttimponibile = 0;
    $ttimponibile = 0;
    $tttassa = 0;
    $ttiva = 0;
    $ttfee = 0;
    if (isset($dettaglio)) {
        foreach ($dettaglio as $value) {
            /*party,sum(tax) as ttassa,
sum(imponibile) as timponibile,sum(iva) as tiva,
sum(fee) as tfee,sum(totale) as ttotale*/
            echo '<tr>';
            echo '<td scope="col"> ' . $value['party'] . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['ttotale']  +  $value['ttassa']) . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['ttotale'], 2) . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['timponibile'], 2) . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['ttassa'], 2) . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['tiva'], 2) . '</td>';
            echo '<td scope="col"> € ' . formatEuro($value['tfee'], 2) . '</td>';
            echo '</tR>';
            $tttotale = $tttotale + round($value['ttotale'], 2);
            $ttimponibile =  $ttimponibile + round($value['timponibile'], 2);
            $tttassa = $tttassa + round($value['ttassa'], 2);
            $ttiva = $ttiva + round($value['tiva'], 2);
            $ttfee = $ttfee + round($value['tfee'], 2);
        }
    }
    echo '<tr  class="table-total bg-primary">';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>Totale  ' . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($tttotale + $tttassa, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($tttotale, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($ttimponibile, 2)  . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($tttassa, 2)  . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($ttiva, 2) . '</strong></td>';
    echo '<td  style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong>€ ' . formatEuro($ttfee, 2) . '</strong></td>';
    ?>

</table>

<div class="d-flex flex-wrap justify-content-center align-items-start">
    <!--<div class="full-screen-container">-->

    <div class=" custom-card card">
        <div class="card-body">
            <h5 class="card-title">Party Group Cost</h5>
            <?= ApexchartsWidget::widget([
                'type' => 'pie',

                'height' => '400px',
                'width' => '100%',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true
                        ],
                        'id' => 'chart-pie',
                    ],
                    'labels' => $labels,
                ],
                'series' => $series,
            ]) ?>

        </div>
    </div>

    <div class="custom-card card">
        <div class="card-body">
            <h5 class="card-title"> Prezzi Camere Medi</h5>


            <?php
            $categories = $analisitappe[0];

            // Array di serie (dati)
            $dataSeries = array_map(function ($item) {
                return (float)str_replace('.', ',', $item['data']);
            }, $analisitappe[1]);



            echo   \onmotion\apexcharts\ApexchartsWidget::widget([
                'type' => 'bar', // default area
                'id' => 'chart-bar',
                'chartOptions' => [
                    'chart' => [
                        'toolbar' => [
                            'show' => true,
                            'autoSelected' => 'zoom',
                        ],

                    ],
                    'xaxis' => [
                        'categories' => $categories,

                    ],
                    'plotOptions' => [
                        'bar' => [
                            'horizontal' => false,
                            'endingShape' => 'rounded',
                        ],
                    ],
                    'dataLabels' => [
                        'enabled' => false,
                    ],
                    'stroke' => [
                        'show' => true,
                        'colors' => ['transparent'],
                    ],
                    'legend' => [
                        'verticalAlign' => 'bottom',
                        'horizontalAlign' => 'left',
                        //'floating'=> 'true'
                    ],
                ],
                'series' =>
                [
                    [
                        'name' => 'Dati',
                        'data' => $dataSeries, // Assegna i dati trasformati
                    ],
                ],
            ]); ?>
        </div>
    </div>

</div>
<h3>Costo Medio Party/Camere</h3>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead>
        <tr>
            <th style="background-color: #f1eef6; color: #002c48;">Party</th>
            <?php foreach ($pivot['columns'] as $column): ?>
                <th style=" background-color: #f1eef6; color: #002c48;"><?= Html::encode($column) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pivot['results'] as $row): ?>
            <tr>
                <td><?= Html::encode($row['party']) ?></td>
                <?php foreach ($pivot['columns'] as $column): ?>
                    <td><?= isset($row[$column]) ? '€ ' . number_format($row[$column], 2) : '0' ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <!-- Riga delle medie -->
        <tr class="table-total bg-primary">
            <td style="font-size: 16px; background-color: #f1eef6; color: #002c48;"> <strong>Media</strong></td>
            <?php foreach ($pivot['columnAverages'] as $average): ?>
                <td style="font-size: 16px; background-color: #f1eef6; color: #002c48;"><strong><?= '€ ' . number_format($average, 2) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>

