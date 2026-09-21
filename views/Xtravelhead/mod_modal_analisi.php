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
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead>
        <tr>
            <th>Party</th>
            <th>Totale</th>
            <th>Pagato Hotel</th>
            <th>Imponibile</th>
            <th>City Tax</th>
            <th>Iva</th>
            <th>Fee</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totale = $imponibile = $tassa = $iva = $fee = 0;

        foreach ($dettaglio as $v) {
            echo '<tr>';
            echo '<td>' . Html::encode($v['party']) . '</td>';
            echo '<td>€ ' . number_format($v['ttotale'] + $v['ttassa'], 2) . '</td>';
            echo '<td>€ ' . number_format($v['ttotale'], 2) . '</td>';
            echo '<td>€ ' . number_format($v['timponibile'], 2) . '</td>';
            echo '<td>€ ' . number_format($v['ttassa'], 2) . '</td>';
            echo '<td>€ ' . number_format($v['tiva'], 2) . '</td>';
            echo '<td>€ ' . number_format($v['tfee'], 2) . '</td>';
            echo '</tr>';

            $totale += $v['ttotale'];
            $imponibile += $v['timponibile'];
            $tassa += $v['ttassa'];
            $iva += $v['tiva'];
            $fee += $v['tfee'];
        }
        ?>
        <tr class="table-total bg-primary">
            <td><strong>Totale</strong></td>
            <td><strong>€ <?= number_format($totale + $tassa, 2) ?></strong></td>
            <td><strong>€ <?= number_format($totale, 2) ?></strong></td>
            <td><strong>€ <?= number_format($imponibile, 2) ?></strong></td>
            <td><strong>€ <?= number_format($tassa, 2) ?></strong></td>
            <td><strong>€ <?= number_format($iva, 2) ?></strong></td>
            <td><strong>€ <?= number_format($fee, 2) ?></strong></td>
        </tr>
    </tbody>
</table>

<hr>

<div class="row">
    <div class="col-md-6">
        <h5>Party Group Cost (Pie Chart)</h5>
        <div id="pieChart" style="width:100%;height:400px;"></div>
    </div>

    <div class="col-md-6">
        <h5>Prezzi Camere Medi (Bar Chart)</h5>
        <div id="barChart" style="width:100%;height:400px;"></div>
    </div>
</div>

<hr>

<h3>Costo Medio Party/Camere</h3>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead>
        <tr>
            <th>Party</th>
            <?php foreach ($pivot['columns'] as $col): ?>
                <th><?= Html::encode($col) ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pivot['results'] as $row): ?>
            <tr>
                <td><?= Html::encode($row['party']) ?></td>
                <?php foreach ($pivot['columns'] as $col): ?>
                    <td><?= isset($row[$col]) ? '€ ' . number_format($row[$col], 2) : '0' ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        <tr class="table-total bg-primary">
            <td><strong>Media</strong></td>
            <?php foreach ($pivot['columnAverages'] as $avg): ?>
                <td><strong>€ <?= number_format($avg, 2) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>

<hr>

<h3>PowerBI Style - Analisi Interattiva</h3>

<div class="row mb-3">
    <div class="col-md-4">
        <?= Select2::widget([
            'name' => 'filter_party',
            'data' => array_column($dettaglio, 'party', 'party'),
            'options' => ['placeholder' => 'Filtra Party...'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= Select2::widget([
            'name' => 'filter_month',
            'data' => [
                '01' => 'Gen',
                '02' => 'Feb',
                '03' => 'Mar',
                '04' => 'Apr',
                '05' => 'Mag',
                '06' => 'Giu',
                '07' => 'Lug',
                '08' => 'Ago',
                '09' => 'Set',
                '10' => 'Ott',
                '11' => 'Nov',
                '12' => 'Dic'
            ],
            'options' => ['placeholder' => 'Filtra mese...'],
            'pluginOptions' => ['allowClear' => true],
        ]) ?>
    </div>
</div>

<div id="echartsInteractive" style="width:100%;height:400px;"></div>

<?php
$labels = isset($labels) && is_array($labels) ? $labels : [];
$series = isset($series) && is_array($series) ? $series : [];
$labelsJson = json_encode($labels);
$seriesJson = json_encode($series);

$js = <<<JS
var myChart = echarts.init(document.getElementById('echartsInteractive'));

// Dati iniziali
var level1 = {
    title: { text: 'Analisi Generale' },
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: $labelsJson },
    yAxis: { type: 'value' },
    series: [{ type:'bar', name:'Valore', data:$seriesJson }]
};

// Drilldown simulato per ogni party
var level2 = {
    'Party1': { xAxis:{type:'category',data:['Imponibile','IVA','Tassa','Fee']}, series:[{type:'bar',data:[10,4,3,2]}]},
    'Party2': { xAxis:{type:'category',data:['Imponibile','IVA','Tassa','Fee']}, series:[{type:'bar',data:[15,6,5,3]}]}
};

myChart.setOption(level1);

// Drill-down on click
myChart.on('click', function(params){
    if(level2[params.name]){
        myChart.setOption({
            title:{text:'Dettaglio '+params.name},
            xAxis: level2[params.name].xAxis,
            series: level2[params.name].series
        });
    }
});

// Filtri dinamici
$("[name='filter_party'], [name='filter_month']").on('change', function(){
    var party = $("[name='filter_party']").val();
    if(level2[party]){
        myChart.setOption({
            title:{text:'Dettaglio '+party},
            xAxis: level2[party].xAxis,
            series: level2[party].series
        });
    } else {
        myChart.setOption(level1);
    }
});

// Grafici secondari (Pie e Bar) con dati PHP
var pieChart = echarts.init(document.getElementById('pieChart'));
pieChart.setOption({
    tooltip: {trigger:'item'},
    series:[{
        type:'pie',
        radius:'50%',
        data: series.map((v,i)=>({value:v, name: labels[i]}));
    }]
});

var barChart = echarts.init(document.getElementById('barChart'));
barChart.setOption({
    xAxis:{type:'category', data:$labelsJson},
    yAxis:{type:'value'},
    series:[{type:'bar', data:$seriesJson}]
});
JS;

$this->registerJs($js);
?>