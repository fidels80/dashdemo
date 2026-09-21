<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use app\models\SubEntitaLookup;
use app\assets\DataTablesAsset;
DataTablesAsset::register($this);
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
    $classeContratto = $model->data_scadenza_contratto ? $getClasseScadenza($model->data_scadenza_contratto) : '';
    ?>

    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="section-title text-primary">Dati Tecnici e Scadenze</h3>
            <table class="table table-bordered info-table" style="font-size: 1.1rem;">
                <tr>
    <th>Tipologia Possesso</th>
    <td>
        <span class="badge bg-outline-dark text-dark border">
            <?= Html::encode($model->tipologia ?: 'Non definita') ?>
        </span>
    </td>
    <th class="<?= $classeContratto ?>">Scadenza Contratto</th>
    <td class="<?= $classeContratto ?>">
        <?php if ($model->tipologia === 'Leasing' || $model->tipologia === 'Noleggio'): ?>
            <?= $model->data_scadenza_contratto ? Yii::$app->formatter->asDate($model->data_scadenza_contratto, 'php:d/m/Y') : 'N.D.' ?>
        <?php else: ?>
            <span class="text-muted">N/A (Proprietà)</span>
        <?php endif; ?>
    </td>
</tr>
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
                <th>Peso</th> <td> <?= $model->peso_complessivo	?></td>
                <th>Classe Euro</th> <td> <?= $model->classe_euro	?></td>
               
                <tr>
                 <th>Data Immatricolazione</th> <td> <?= $model->data_immatricolazione	?></td>     
                <th class="<?= $classeAss ?>">Scadenza Assicurazione</th>
                    <td class="<?= $classeAss ?>">
                        <?= $model->scadenza_assicurazione ? Yii::$app->formatter->asDate($model->scadenza_assicurazione, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                  
                </tr>
                <tr>  <th class="<?= $classeRev ?>">Scadenza Revisione</th>
                    <td class="<?= $classeRev ?>">
                        <?= $model->scadenza_revisione ? Yii::$app->formatter->asDate($model->scadenza_revisione, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                    <th class="<?= $classeZtl ?>">Scadenza ZTL</th>
                    <td class="<?= $classeZtl ?>">
                        <?= $model->scadenza_ztl ? Yii::$app->formatter->asDate($model->scadenza_ztl, 'php:d/m/Y') : 'N.A.' ?>
                    </td>
                    
                </tr>
                <tr>
 
                    <th>Documento Unico</th>
                    <td><?= $model->documento_path ? 'Presente' : 'Mancante' ?></td>
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
<?php
$js = <<<JS
$(document).ready(function() {
    // Inizializziamo tutte le tabelle dei documenti
    $('.table-documenti-auto').each(function() {
        if (!$.fn.DataTable.isDataTable(this)) {
            $(this).DataTable({
                // dom: f = filtro/ricerca, t = tabella, p = paginazione
                // 'l' = lunghezza pagina, 'i' = info
                dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"l>>t<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                language: { 
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/it-IT.json' 
                },
                pageLength: 10,
                responsive: true,
                autoWidth: false
            });
        }
    });

    // Correzione larghezza quando si cambia Tab
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
    });
});
JS;

// Usiamo POS_END per caricarlo dopo le librerie di base
$this->registerJs($js, \yii\web\View::POS_END);
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
