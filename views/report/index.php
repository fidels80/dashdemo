<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '';

$this->registerCss("
    .report-card { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; border: none; border-top: 4px solid; }
    .report-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; cursor: pointer; }
    .report-icon { font-size: 3rem; opacity: 0.8; }
    .card-presenze { border-top-color: #0d6efd; }
    .card-mezzi { border-top-color: #198754; }
");
?>

<div class="report-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 text-dark"><i class="fa fa-chart-pie text-secondary"></i> <?= Html::encode('Centro Reportistica') ?></h1>
    </div>

    <p class="text-muted mb-4 pb-2 border-bottom">Seleziona il report che desideri generare e analizzare.</p>

    <div class="row">
        <div class="col-md-4 mb-4">
            <a href="<?= Url::to(['report/presenze']) ?>" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100 report-card card-presenze bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="fa fa-user-clock text-primary report-icon mb-3"></i>
                        <h4 class="card-title fw-bold">Report Presenze e Costi</h4>
                        <p class="card-text text-muted">
                            Analisi dettagliata delle ore lavorate, assenze, ferie e calcolo dei costi generati per ogni dipendente.
                        </p>
                        <span class="btn btn-outline-primary mt-2"><i class="fa fa-arrow-right"></i> Vai al Report</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-4">
<a href="<?= Url::to(['report/planning']) ?>" class="text-decoration-none text-dark">
                <div class="card shadow-sm h-100 report-card card-mezzi bg-light">
                    <div class="card-body p-4 text-center">
                        <i class="fa fa-truck text-success report-icon mb-3"></i>
                        <h4 class="card-title fw-bold">Report Attività Flotta</h4>
                        <p class="card-text text-muted">
                            Statistiche di utilizzo dei veicoli, incroci con i conducenti e dettaglio delle pianificazioni.
                        </p>
                        <span class="btn btn-outline-success mt-2"><i class="fa fa-arrow-right"></i> Vai al Report</span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>