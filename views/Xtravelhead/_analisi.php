<?php

use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;

$this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);


?>
<h3>Costo Party</h3>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Party</th>
        <th>Totale</th>
        <th>Pagato Hotel</th>
        <th>Imponibile</th>
        <th>City Tax</th>
        <th>Iva</th>
        <th>Fee</th>
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
            echo '<td scope="col"> € ' . round($value['ttotale'], 2) + round($value['ttassa'], 2) . '</td>';
            echo '<td scope="col"> € ' . round($value['ttotale'], 2) . '</td>';
            echo '<td scope="col"> € ' . round($value['timponibile'], 2) . '</td>';
            echo '<td scope="col"> € ' . round($value['ttassa'], 2) . '</td>';
            echo '<td scope="col"> € ' . round($value['tiva'], 2) . '</td>';
            echo '<td scope="col"> € ' . round($value['tfee'], 2) . '</td>';
            echo '</tR>';
            $tttotale = $tttotale + round($value['ttotale'], 2);
            $ttimponibile =  $ttimponibile + round($value['timponibile'], 2);
            $tttassa = $tttassa + round($value['ttassa'], 2);
            $ttiva = $ttiva + round($value['tiva'], 2);
            $ttfee = $ttfee + round($value['tfee'], 2);
        }
    }
    echo '<tr  class="table-total bg-primary">';
    echo '<td><strong>Totale  ' . '</strong></td>';
    echo '<td><strong>€ ' . round($tttotale + $tttassa, 2) . '</strong></td>';
    echo '<td><strong>€ ' . round($tttotale, 2) . '</strong></td>';
    echo '<td><strong>€ ' . round($ttimponibile, 2)  . '</strong></td>';
    echo '<td><strong>€ ' . round($tttassa, 2)  . '</strong></td>';
    echo '<td><strong>€ ' . round($ttiva, 2) . '</strong></td>';
    echo '<td><strong>€ ' . round($ttfee, 2) . '</strong></td>';
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
            <th>Party</th>
            <?php foreach ($pivot['columns'] as $column): ?>
                <th><?= Html::encode($column) ?></th>
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
            <td><strong>Media</strong></td>
            <?php foreach ($pivot['columnAverages'] as $average): ?>
                <td><strong><?= '€ ' . number_format($average, 2) ?></strong></td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>


<div class="custom-card card mt-4">
    <div class="card-body">
        <h5 class="card-title">PowerBI Style - Analisi Interattiva</h5>

        <!-- FILTRI -->
        <div class="row mb-3">
            <div class="col-md-4">
                <?= Select2::widget([
                    'name' => 'pb_party',
                    'data' => array_column($dettaglio, 'party', 'party'),
                    'options' => ['placeholder' => 'Filtra Party...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>

            <div class="col-md-4">
                <?= Select2::widget([
                    'name' => 'pb_mese',
                    'data' => [
                        '01' => 'Gennaio',
                        '02' => 'Febbraio',
                        '03' => 'Marzo',
                        '04' => 'Aprile',
                        '05' => 'Maggio',
                        '06' => 'Giugno',
                        '07' => 'Luglio',
                        '08' => 'Agosto',
                        '09' => 'Settembre',
                        '10' => 'Ottobre',
                        '11' => 'Novembre',
                        '12' => 'Dicembre'
                    ],
                    'options' => ['placeholder' => 'Filtra mese...'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>
            </div>
        </div>

        <!-- GRAFICO -->
        <div id="powerbi-chart"></div>
    </div>
</div>

<?php
$initialLabels = json_encode($labels);
$initialSeries = json_encode($series);

$js = <<<JS

function loadPowerBiChart(filters = {}) {
    // AJAX simulato per ora (useremo i tuoi dati locali)
    let labels = $initialLabels;
    let series = $initialSeries;

    // Se è stato scelto un Party → drill-down simulato
    if (filters.party) {
        labels = ['Imponibile', 'IVA', 'Tassa', 'Fee'];
        series = [10, 4, 3, 2]; // fai tu la query vera dopo
    }

    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: true }
        },
        series: [{
            name: 'Valore',
            data: series
        }],
        xaxis: { categories: labels },
        title: {
            text: filters.party ? 'Dettaglio Party: ' + filters.party : 'Analisi Generale'
        }
    };

    var chart = new ApexCharts(document.querySelector("#powerbi-chart"), options);
    chart.render();
}

// Primo caricamento
loadPowerBiChart();

// Filtri dinamici
$("[name='pb_party'], [name='pb_mese']").on('change', function() {
    let filters = {
        party: $("[name='pb_party']").val(),
        mese: $("[name='pb_mese']").val(),
    };
    $("#powerbi-chart").html(""); // reset chart
    loadPowerBiChart(filters);
});

JS;

$this->registerJs($js);
?>