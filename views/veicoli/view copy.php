<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use app\models\SubEntitaLookup;
$categorie = SubEntitaLookup::find()->where(['riferimento' => 'VEICOLI'])->orderBy('descrizione')->all();
$this->title = '';

$this->registerCss("
    .section-title { border-left: 5px solid #007bff; padding-left: 15px; margin-bottom: 20px; margin-top: 30px; font-weight: bold; }
   /* Rimuovi o commenta se presente questa riga fissa: */
/* .info-table th:nth-child(1) { color: red; } */

/* Usa invece classi dinamiche: */
.text-danger { color: #d9534f !important; }
.text-warning { color: #f0ad4e !important; }
    .scadenza-alert { color: #d9534f; font-weight: bold; }
");
?>

 
<div class="veicoli-view card p-4 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode('Veicolo: ' . $model->targa) ?></h1>
        <div>
<!-- PULSANTE ATTIVAZIONE MODALE -->
<?php // 1. IL PULSANTE
echo Html::button('<i class="fa fa-upload"></i> Carica Documento', [
    'class' => 'btn btn-success btn-lg apri-upload-btn', // Usiamo questa classe per il JS
    'data-bs-toggle' => 'modal',
    'data-bs-target' => '#modal-caricamento-veicolo',
    'data-url' => Url::to(['upload-file', 'id' => $model->id]),
]);
?>
            <?= Html::a('Modifica', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-lg']) ?>
            <?= Html::a('Lista Veicoli', ['index'], ['class' => 'btn btn-secondary btn-lg']) ?>
        </div>
    </div>

    </div>
 

<?php
    $oggi = time();
    $soglia_preavviso = strtotime('+30 days'); // Calcoliamo la data di oggi + 30 giorni

    // Funzione rapida per determinare la classe colore a 3 livelli
    $getClasseScadenza = function ($dataStr) use ($oggi, $soglia_preavviso) {
        if (!$dataStr) return 'text-muted'; // Se la data non è inserita, usiamo il grigio
        
        $timestamp = strtotime($dataStr);
        
        // 1. Controllo se è già scaduta
        if ($timestamp < $oggi) {
            return 'text-danger font-weight-bold'; // Rosso
        } 
        // 2. Controllo se scade entro i prossimi 30 giorni
        elseif ($timestamp <= $soglia_preavviso) {
            return 'text-warning font-weight-bold'; // Giallo/Arancione
        } 
        // 3. Altrimenti è tutto regolare
        else {
            return 'text-success font-weight-bold'; // Verde
        }
    };

    $classeAss = $getClasseScadenza($model->scadenza_assicurazione);
    $classeRev = $getClasseScadenza($model->scadenza_revisione);
    $classeZtl = $getClasseScadenza($model->scadenza_ztl);
    ?>

    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="section-title text-primary">Dati Tecnici e Scadenze</h3>
            <table class="table table-bordered info-table" style="font-size: 1.1rem;">
                <tr>
                    <th>Targa</th>
                    <td><strong><?= Html::encode($model->targa) ?></strong></td>
                    <th>Marca e Modello</th>
                    <td><?= Html::encode($model->marca_modello) ?></td>
                </tr>
                <tr>
                    <th>Stato attuale</th>
                    <td><?= $model->getStatoBadge() ?></td>
                    <th>Ultimo KM rilevato</th>
                    <td><span class="badge bg-dark"><?= number_format($model->ultimo_km, 0, '.', '.') ?> km</span></td>
                </tr>
                <tr>
                    <th class="<?= $classeAss ?>">Scadenza Assicurazione</th>
                    <td class="<?= $classeAss ?>">
                        <?= $model->scadenza_assicurazione ? Yii::$app->formatter->asDate($model->scadenza_assicurazione, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                    <th class="<?= $classeRev ?>">Scadenza Revisione</th>
                    <td class="<?= $classeRev ?>">
                        <?= $model->scadenza_revisione ? Yii::$app->formatter->asDate($model->scadenza_revisione, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                </tr>
                <tr>
                    <th class="<?= $classeZtl ?>">Scadenza ZTL</th>
                    <td class="<?= $classeZtl ?>">
                        <?= $model->scadenza_ztl ? Yii::$app->formatter->asDate($model->scadenza_ztl, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                    <th>Documento Unico</th>
                    <td><?= $model->documento_path ? 'Presente' : 'Mancante' ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

 
 

<?php 
 


// MODALE
Modal::begin([
    'id' => 'modal-caricamento-veicolo',
    'title' => '<h4 class="text-primary m-0">Nuovo Caricamento</h4>',
    'size' => 'modal-lg',
    'options' => ['tabindex' => false],
]);

echo '<div id="area-form-upload" class="text-center p-5">
        <div class="spinner-border text-primary"></div>
        <p class="mt-2">Inizializzazione modulo...</p>
      </div>';

Modal::end();
?>

<?php
$js = <<<JS
$(document).ready(function() {
    console.log('1. Script caricato correttamente');

    // Usiamo il listener sulla modale tramite jQuery
    $(document).on('show.bs.modal', '#modal-caricamento-veicolo', function (event) {
        console.log('2. Evento apertura modale intercettato!');
        
        var pulsante = $(event.relatedTarget); 
        var urlAzione = pulsante.data('url');
        
        console.log('3. URL da chiamare: ' + urlAzione);
        
        // Puliamo l'area e mettiamo lo spinner
        $('#area-form-upload').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div><p>Caricamento...</p></div>');

        // Chiamata AJAX
        $.ajax({
            url: urlAzione,
            type: 'GET',
            success: function(risposta) {
                console.log('4. Dati ricevuti dal server con successo');
                $('#area-form-upload').html(risposta);
            },
            error: function(errore) {
                console.log('Errore nella chiamata AJAX');
                $('#area-form-upload').html('<div class="alert alert-danger">Impossibile caricare il modulo.</div>');
            }
        });
    });
});
JS;

// POS_END inserisce lo script in fondo al body, dopo tutte le librerie
$this->registerJs($js, \yii\web\View::POS_END);
?>
<div class="row">
    <div class="col-md-12">
        <h3 class="section-title text-success">Archivio Documenti Digitali (Libretti, Assicurazioni, Multe)</h3>
        <table id="table-files" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th style="width: 150px;">Nome File</th>
                    <th style="width: 50px;">Estensione</th>
                    <th style="width: 350px;">Nota</th>
                    <th style="width: 150px;">Azioni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->files as $file): ?>
                    <tr>
                        <td><?= Html::encode($file->nomefile) ?></td>
                        <td class="text-center"><span class="badge bg-secondary"><?= strtoupper($file->estensione) ?></span></td>
                        <td><?= Html::encode($file->nota) ?></td>
                        <td class="text-center">
                            <a href="<?= Url::to(['/allfiles/download', 'id' => $file->id]) ?>" class="btn btn-xs btn-info">
                                <i class="fa fa-download"></i> Scarica
                            </a>
                            <?= Html::a('<i class="fa fa-trash"></i>', ['/allfiles/delete', 'id' => $file->id], [
                                'class' => 'btn btn-xs btn-danger',
                                'data-method' => 'post',
                                'data-confirm' => 'Eliminare il file?'
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php
 

// JAVASCRIPT DI GESTIONE
$js = <<<JS
$(document).ready(function() {
 
    // DataTable
    if (!$.fn.DataTable.isDataTable('#table-files')) {
        $('#table-files').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' },
            pageLength: 5
        });
    }
});
JS;
$this->registerJs($js);


?>

<div class="row mt-4">
    <div class="col-md-12">
        <h3 class="section-title text-success">Archivio Documenti Digitali</h3>
        
        <ul class="nav nav-tabs" id="docTabs" role="tablist">
            <?php foreach ($categorie as $index => $cat): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                            id="tab-<?= $cat->codice ?>" data-bs-toggle="tab" 
                            data-bs-target="#content-<?= $cat->codice ?>" 
                            type="button" role="tab"><?= Html::encode($cat->descrizione) ?></button>
                </li>
            <?php endforeach; ?>
            
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-generico" data-bs-toggle="tab" 
                        data-bs-target="#content-generico" 
                        type="button" role="tab">Senza Categoria / Altro</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-3 bg-white shadow-sm" id="docTabsContent">
            
            <?php foreach ($categorie as $index => $cat): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                     id="content-<?= $cat->codice ?>" role="tabpanel">
                    
                    <?= $this->render('_tab_documenti_table', ['files' => $model->files, 'sub_entita' => $cat->codice]) ?>
                </div>
            <?php endforeach; ?>

            <div class="tab-pane fade" id="content-generico" role="tabpanel">
                <?= $this->render('_tab_documenti_table', ['files' => $model->files, 'sub_entita' => null]) ?>
            </div>

        </div>
    </div>
</div>