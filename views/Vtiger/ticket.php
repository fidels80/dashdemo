<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use onmotion\apexcharts\ApexchartsWidget;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ArrayDataProvider */
/* @var $dayFrom string */
/* @var $dayTo string */
/* @var $listac array */
/* @var $resultstipoass array */
/* @var $resultsprod array */
/* @var $resultscomprod array */
/* @var $resultsstato array */
/* @var $resultscf array */
/* @var $resultsutenti array */
/* @var $resultsutentiore array */

$this->registerCss('
    .custom-card {
        width: 100%;
        height: auto;
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
    }
    table.table-fit {
        width: 100%;
        table-layout: auto !important;
    }
    table.table-fit thead th,
    table.table-fit tbody td,
    table.table-fit tfoot th,
    table.table-fit tfoot td {
        width: auto !important;
    }
');

$_listacVal = Yii::$app->request->get('listac');
$_listacVal = is_array($_listacVal) ? $_listacVal : (($_listacVal !== null && $_listacVal !== '') ? [$_listacVal] : []);

// Solo i clienti già selezionati: vengono precaricati per renderizzare i tag
// (la ricerca completa avviene via AJAX, azione clienti-ajax)
$_listacData = [];
foreach ($_listacVal as $lv) {
    if (isset($listac[$lv])) {
        $_listacData[$lv] = $listac[$lv];
    }
}
?>

<div class="container-fluid">
    <?= $this->render('_nav', ['active' => 'ticket']) ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h3 class="panel-title m-0" style="font-size: 1.1rem;">
                <i class="fas fa-ticket-alt mr-2"></i> Filtri Ticket
            </h3>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'options' => ['class' => ''],
            ]); ?>

            <div class="row">
                <!-- Giorno Da -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <?= Html::label('Dal', 'dayFrom', ['class' => 'form-label']) ?>
                    <?= Html::input('date', 'dayFrom', $dayFrom, ['class' => 'form-control']) ?>
                </div>

                <!-- Giorno A -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <?= Html::label('Al', 'dayTo', ['class' => 'form-label']) ?>
                    <?= Html::input('date', 'dayTo', $dayTo, ['class' => 'form-control']) ?>
                </div>

                <!-- Clienti -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                    <?= Html::label('Clienti', 'listac', ['class' => 'form-label']) ?>
                    <?= Select2::widget([
                        'name' => 'listac[]',
                        'value' => $_listacVal,
                        'data' => $_listacData,
                        'options' => [
                            'placeholder' => 'Seleziona Cliente',
                            'class' => 'form-control select2',
                            'multiple' => true
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '100%',
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['vtiger/clienti-ajax']),
                                'dataType' => 'json',
                                'delay' => 300,
                                'data' => new \yii\web\JsExpression('function (params) { return { q: params.term }; }'),
                                'processResults' => new \yii\web\JsExpression('function (data, params) { return { results: data.results }; }'),
                                'cache' => true,
                            ],
                        ],
                    ]) ?>
                </div>

                <!-- Bottone di ricerca -->
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3 d-flex align-items-end">
                    <div class="d-flex w-100">
                        <?= Html::submitButton('<i class="fas fa-search mr-1"></i> Cerca', ['class' => 'btn btn-primary flex-fill mr-2']) ?>
                        <button type="button" class="btn btn-secondary flex-fill" id="resetFilters">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<script>
    document.getElementById('resetFilters').addEventListener('click', function() {
        document.querySelectorAll('.form-control').forEach(function(input) {
            input.value = '';
        });
        $('.select2').val(null).trigger('change');
    });
</script>

<div class="row">
    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Tipi eventi assistenza</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'pie',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'labels' => array_column($resultstipoass, 'name')
                    ],
                    'series' => array_column($resultstipoass, 'data'),
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Prodotti</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'pie',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'labels' => array_column($resultsprod, 'name')
                    ],
                    'series' => array_column($resultsprod, 'data'),
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Prodotti Complementari</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'pie',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'dataLabels' => [
                            'enabled' => true,
                            'style' => ['fontSize' => '16px']
                        ],
                        'legend' => ['fontSize' => '10px'],
                        'labels' => array_column($resultscomprod, 'name')
                    ],
                    'series' => array_column($resultscomprod, 'data'),
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Stato Assistenze</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'pie',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'labels' => array_column($resultsstato, 'name')
                    ],
                    'series' => array_column($resultsstato, 'data'),
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Operatori per Ore ticket</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'bar',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'plotOptions' => [
                            'bar' => [
                                'borderRadius' => 2,
                                'borderRadiusApplication' => 'end',
                                'horizontal' => true
                            ]
                        ],
                        'dataLabels' => ['enabled' => false],
                        'xaxis' => [
                            'categories' => array_column($resultsutentiore, 'name')
                        ]
                    ],
                    'series' => [
                        ['name' => 'Ore', 'data' => array_column($resultsutentiore, 'data')]
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Ticket per Operatori</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'bar',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'plotOptions' => [
                            'bar' => [
                                'borderRadius' => 2,
                                'borderRadiusApplication' => 'end',
                                'horizontal' => true
                            ]
                        ],
                        'dataLabels' => ['enabled' => false],
                        'xaxis' => [
                            'categories' => array_column($resultsutenti, 'name')
                        ]
                    ],
                    'series' => [
                        ['name' => 'Ticket', 'data' => array_column($resultsutenti, 'data')]
                    ],
                ]) ?>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="custom-card card h-100">
            <div class="card-body">
                <h5 class="card-title">
                    Ticket per Cliente</h5>
                <?= ApexchartsWidget::widget([
                    'type' => 'pie',
                    'height' => '270px',
                    'width' => '100%',
                    'chartOptions' => [
                        'chart' => [
                            'toolbar' => ['show' => true]
                        ],
                        'labels' => array_column($resultscf, 'name')
                    ],
                    'series' => array_column($resultscf, 'data'),
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php
$modelsT = $dataProvider->getModels();
?>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h3 class="panel-title m-0" style="font-size: 1.1rem;">
            <i class="fas fa-list-alt mr-2"></i> Risultati Ticket
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="table-ticket-final" class="table table-hover table-striped mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th>Soggetto</th>
                        <th>Ticket</th>
                        <th>Title</th>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Inizio</th>
                        <th>Fine</th>
                        <th>Delta</th>
                        <th>Oggetto</th>
                        <th>Prodotti</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>STATUS</th>
                        <th>TID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modelsT as $mt): ?>
                        <tr>
                            <td><?= Html::encode($mt['soggetto'] ?? '') ?></td>
                            <td><?= Html::encode($mt['ticket_no'] ?? $mt['ticketid'] ?? '') ?></td>
                            <td><?= Html::encode($mt['title'] ?? '') ?></td>
                            <td><?= !empty($mt['dataevento']) ? date('d/m/Y', strtotime($mt['dataevento'])) : '' ?></td>
                            <td><?= Html::encode($mt['tipo'] ?? '') ?></td>
                            <td><?= Html::encode($mt['orainizioevento'] ?? '') ?></td>
                            <td><?= Html::encode($mt['orafineevento'] ?? '') ?></td>
                            <td><?= number_format($mt['oredelta'] ?? 0, 2) ?></td>
                            <td title="<?= Html::encode($mt['oggetto'] ?? '') ?>">
                                <?= mb_strimwidth(Html::encode($mt['oggetto'] ?? ''), 0, 40, "...") ?>
                            </td>
                            <td><?= Html::encode($mt['custom2'] ?? '') ?></td>
                            <td><?= Html::encode($mt['priority'] ?? '') ?></td>
                            <td><?= Html::encode($mt['status'] ?? '') ?></td>
                            <td>
                                <span class="badge <?= ($mt['STATUS'] == 'Chiuso') ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= Html::encode($mt['STATUS'] ?? '') ?>
                                </span>
                            </td>
                            <td><?= Html::encode($mt['tid'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// DataTables centralizzato via componente del progetto
\app\components\DataTables::render('table-ticket-final', 1, 'desc');
?>