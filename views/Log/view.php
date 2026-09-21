<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Log */

$this->title =null;// "Dettaglio Log #" . $model->id;
//$this->params['breadcrumbs'][] =null; ['label' => 'Logs', 'url' => ['index']];
//$this->params['breadcrumbs'][] =null;
//= $this->title;

// Funzione helper per formattare il JSON o Serialized Data
function formatData($data) {
    if (empty($data)) return '<i class="text-muted">Nessun dato</i>';
    
    // Proviamo a decodificare se è JSON
    $decoded = json_decode($data, true);
    
    // Se non è JSON, proviamo a vedere se è Serializzato PHP (visto che nel controller avevi serialize)
    if ($decoded === null && @unserialize($data) !== false) {
        $decoded = unserialize($data);
    }

    if (is_array($decoded) || is_object($decoded)) {
        return '<pre class="json-view">' . Html::encode(json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
    }
    
    return Html::encode($data);
}

$this->registerCss("
    .json-view {
        background-color: #272822;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 8px;
        border-left: 5px solid #66d9ef;
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 0.9rem;
        max-height: 500px;
        overflow: auto;
    }
    .log-header-info {
        background: #f4f7f6;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 5px solid #3498db;
    }
    .label-log { font-weight: bold; color: #555; text-transform: uppercase; font-size: 0.8rem; }
");
?>

<div class="log-view">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode("Dettaglio Log #" . $model->id) ?></h1>
        <p>
            <?= Html::a('<i class="fa fa-arrow-left"></i> Torna ai Logs', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="fa fa-trash"></i> Elimina', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Sei sicuro di voler eliminare questo log?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>

    <div class="log-header-info shadow-sm">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="label-log">Operatore</div>
                <div class="h5 text-primary">
                    <i class="fa fa-user-circle"></i> 
                    <?= Html::encode($model->operatoreNome) ?>
                </div>
                <small class="text-muted">ID Sistema: <?= $model->userid ?></small>
            </div>
            <div class="col-md-6 border-start border-end">
                <div class="label-log">Operazione Eseguita</div>
                <div class="h5"><strong><?= Html::encode($model->operazione) ?></strong></div>
            </div>
            <div class="col-md-3">
                <div class="label-log">Data e Ora</div>
                <div class="h5"><i class="fa fa-clock"></i> <?= date('d/m/Y H:i:s', strtotime($model->timeins)) ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between">
                    <span><i class="fa fa-database"></i> DATI INSERITI / FINALI</span>
                    <span class="badge bg-light text-dark">Nuovo</span>
                </div>
                <div class="card-body">
                    <?= formatData($model->valore) ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between">
                    <span><i class="fa fa-history"></i> DATI PRECEDENTI</span>
                    <span class="badge bg-dark">Old</span>
                </div>
                <div class="card-body">
                    <?= formatData($model->old_valore) ?>
                </div>
            </div>
        </div>
    </div>
</div>