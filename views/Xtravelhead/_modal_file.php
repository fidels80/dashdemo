<style>
    /* CSS da aggiungere al tuo file CSS principale o in una sezione <style> */

    .file-upload-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }

    .file-upload-section h5 {
        margin-bottom: 15px;
        color: #002c48;
        font-weight: 600;
    }

    #file-input {
        border: 2px dashed #dee2e6;
        padding: 10px;
        transition: border-color 0.3s ease;
    }

    #file-input:hover {
        border-color: #007bff;
    }

    #upload-btn {
        background-color: #002c48;
        border-color: #002c48;
        transition: all 0.3s ease;
    }

    #upload-btn:hover {
        background-color: #001a2e;
        border-color: #001a2e;
    }

    #upload-btn:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
    }

    #product-files {
        margin-top: 20px;
    }

    #product-files th {
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #product-files tbody tr:hover {
        background-color: #f5f5f5;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        font-size: 12px;
        padding: 4px 8px;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Stili per i messaggi di status */
    .alert {
        margin-bottom: 10px;
        padding: 8px 12px;
        border-radius: 4px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-warning {
        background-color: #fff3cd;
        border-color: #ffeaa7;
        color: #856404;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #bee5eb;
        color: #0c5460;
    }

    /* Icone dei file */
    .file-icon {
        margin-right: 8px;
        width: 16px;
        height: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {

        .file-upload-section .row .col-md-8,
        .file-upload-section .row .col-md-4 {
            margin-bottom: 10px;
        }

        #product-files {
            font-size: 14px;
        }

        #product-files th,
        #product-files td {
            padding: 8px 4px;
        }
    }

    /* Loading spinner */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid #002c48;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\web\JsExpression;

$userId = \Yii::$app->user->id;
$user = \app\models\User::findOne($userId);
// Prima parte: Modifica del file _modal_file.php con debug
$uploadForm = new \app\models\AllFiles();
?>

<!-- Form per l'upload di nuovi file -->
<div class="file-upload-section mb-4">
    <h5>Carica nuovo file</h5>

    <?php $form = ActiveForm::begin([
        'id' => 'file-upload-form',
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'form-inline'
        ],
        'action' => ['upload-file'],
        'method' => 'post'
    ]); ?>

    <div class="row">
        <div class="col-md-8">
            <?= Html::fileInput('upload_file', null, [
                'id' => 'file-input',
                'class' => 'form-control',
                'accept' => '*/*'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= Html::button('Carica File', [
                'id' => 'upload-btn',
                'class' => 'btn btn-primary',
                'onclick' => 'uploadFile()'
            ]) ?>
        </div>
    </div>

    <!-- Campo nascosto per l'ID del modello padre -->
    <?= Html::hiddenInput('id_padre', $model->th_id) ?>
    <?= Html::hiddenInput('entita', 'Xtravelrow') ?>

    <?php ActiveForm::end(); ?>

    <!-- Area per mostrare i messaggi di stato -->
    <div id="upload-status" class="mt-2"></div>

    <!-- Debug console (rimuovi in produzione) -->
    <div id="debug-console" class="mt-2" style="background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;"></div>
</div>

<hr>

<!-- Tabella dei file esistenti -->
<?php echo '<table id="product-files" class="table">';
echo '<thead>';
echo '<tr>';
echo '<th style="font-size: 16px; background-color: #f1eef6; color: #002c48;" width="60%">File</th>';
echo '<th style="font-size: 16px; background-color: #f1eef6; color: #002c48;" width="20%">Origine</th>';
echo '<th style="font-size: 16px; background-color: #f1eef6; color: #002c48;" width="20%">Azioni</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody id="files-table-body">';

foreach ($model->filesall as $value) {
    if ($value['entita'] == 'Xtravelrow') {
        echo '<tr>';
        echo '<td>';

        echo Html::a(
            $value['nomefile'],
            [
                'allfiles/genfile',
                'id' => $value['id'],
                'file' => str_replace(' ', '_', $value['nomefile'])
            ],
            ['target' => '_blank']
        );

        echo '</td>';

        echo '<td>';
        if ($value['origine'] == 'S') {
            echo Yii::$app->fontawesome->name('user', 'solid')->fill('#003865');
            echo ' Utente';
        } else {
            echo Yii::$app->fontawesome->name('server', 'solid')->fill('#003865');
            echo ' TourMe';
        }
        echo '</td>';

        echo '<td>';
        if ($user->level >= 80) {
            echo Html::button('Elimina', [
                'class' => 'btn btn-sm btn-danger',
                'onclick' => 'deleteFile(' . $value['id'] . ', this)'
            ]);
        }
        echo '</td>';

        echo '</tr>';
    }
}

echo '</tbody>';
echo '</table>';
?>

<script>
    // Funzione di debug
    function debugLog(message) {
        var debugConsole = document.getElementById('debug-console');
        var timestamp = new Date().toLocaleTimeString();
        // debugConsole.innerHTML += '[' + timestamp + '] ' + message + '<br>';
        // debugConsole.scrollTop = debugConsole.scrollHeight;
        // console.log('[DEBUG] ' + message);
    }

    function uploadFile() {
        // debugLog('Inizio upload file...');

        var fileInput = document.getElementById('file-input');
        var statusDiv = document.getElementById('upload-status');

        if (!fileInput.files.length) {
            //       debugLog('Nessun file selezionato');
            statusDiv.innerHTML = '<div class="alert alert-warning">Seleziona un file da caricare</div>';
            return;
        }

        var selectedFile = fileInput.files[0];
        //     debugLog('File selezionato: ' + selectedFile.name + ' (dimensione: ' + selectedFile.size + ' bytes)');

        var formData = new FormData();
        formData.append('upload_file', selectedFile);
        formData.append('id_padre', <?= $model->th_id ?>);
        formData.append('entita', 'Xtravelrow');
        formData.append('<?= Yii::$app->request->csrfParam ?>', '<?= Yii::$app->request->csrfToken ?>');

        //  debugLog('FormData preparato');
        debugLog('URL target: <?= yii\helpers\Url::to(['upload-file']) ?>');
        //
        // Mostra loading
        statusDiv.innerHTML = '<div class="alert alert-info"><span class="loading-spinner"></span>Caricamento in corso...</div>';
        document.getElementById('upload-btn').disabled = true;

        //    debugLog('Invio richiesta AJAX...');

        fetch('<?= yii\helpers\Url::to(['upload-file']) ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                //      debugLog('Risposta ricevuta - Status: ' + response.status + ' (' + response.statusText + ')');
                //        debugLog('Content-Type: ' + response.headers.get('Content-Type'));

                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }

                return response.text(); // Prima prendiamo il testo grezzo
            })
            .then(rawText => {
                debugLog('Risposta grezza: ' + rawText.substring(0, 200) + (rawText.length > 200 ? '...' : ''));

                try {
                    var data = JSON.parse(rawText);
                    //            debugLog('JSON parsato correttamente');

                    if (data.success) {
                        //                 debugLog('Upload completato con successo');
                        statusDiv.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';

                        if (data.file) {
                            //                     debugLog('Aggiornamento tabella con nuovo file: ' + data.file.nomefile);
                            addFileToTable(data.file);
                        } else {
                            //                      debugLog('Dati file non presenti nella risposta, ricarico la pagina...');
                            setTimeout(() => location.reload(), 1500);
                        }

                        // Reset del form
                        fileInput.value = '';
                        //                   debugLog('Form resettato');
                    } else {
                        //                    debugLog('Errore dal server: ' + data.message);
                        statusDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                    }
                } catch (parseError) {
                    //                 debugLog('Errore parsing JSON: ' + parseError.message);
                    //                debugLog('Probabilmente la risposta non è JSON valido. Ricarico la pagina...');
                    statusDiv.innerHTML = '<div class="alert alert-warning">File caricato, ricaricamento pagina...</div>';
                    setTimeout(() => location.reload(), 2000);
                }
            })
            .catch(error => {
                //          debugLog('Errore AJAX: ' + error.message);
                statusDiv.innerHTML = '<div class="alert alert-danger">Errore durante il caricamento. Il file potrebbe essere stato salvato.</div>';

                // Aggiungi pulsante per ricaricare manualmente
                statusDiv.innerHTML += '<br><button class="btn btn-sm btn-info" onclick="location.reload()">Ricarica pagina</button>';
            })
            .finally(() => {
                document.getElementById('upload-btn').disabled = false;
                //          debugLog('Upload processo completato');
            });
    }

    function addFileToTable(fileData) {
        var tbody = document.getElementById('files-table-body');
        if (!tbody) {
            console.error('Errore: tbody non trovato');
            return;
        }

        var newRow = tbody.insertRow();
        console.log(fileData.origine);
        // Determina se è server o utente
        let isServer = fileData.origine == 'U';
        let iconHtml = isServer ?
            '<i class="fas fa-server" style="color: #003865;"></i> TourMe' :
            '<i class="fas fa-user" style="color: #003865;"></i> Utente';

        newRow.innerHTML = `
        <td>
            <a href="${fileData.downloadUrl}" target="_blank">
                ${fileData.nomefile}
            </a>
        </td>
        <td>
            ${iconHtml}
        </td>
        <td>
            <button class="btn btn-sm btn-danger" onclick="deleteFile(${fileData.id}, this)">Elimina</button>
        </td>
    `;
    }


    function old_deleteFile(fileId, button) {
        //      debugLog('Inizio eliminazione file ID: ' + fileId);

        if (!confirm('Sei sicuro di voler eliminare questo file?')) {
            debugLog('Eliminazione annullata dall\'utente');
            return;
        }

        var requestData = {
            id: fileId,
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
        };

        debugLog('Dati richiesta eliminazione: ' + JSON.stringify(requestData));

        fetch('<?= yii\helpers\Url::to(['delete-file']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                debugLog('Risposta eliminazione - Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                debugLog('Risposta eliminazione: ' + JSON.stringify(data));

                if (data.success) {
                    // Rimuovi la riga dalla tabella
                    event.target.closest('tr').remove();
                    debugLog('File eliminato e riga rimossa dalla tabella');
                } else {
                    debugLog('Errore eliminazione: ' + data.message);
                    alert('Errore durante l\'eliminazione del file: ' + data.message);
                }
            })
            .catch(error => {
                debugLog('Errore AJAX eliminazione: ' + error.message);
                // alert('Errore durante l\'eliminazione del file');
            });
    }


    function deleteFile(fileId, button) {
        debugLog('Inizio eliminazione file ID: ' + fileId);

        if (!confirm('Sei sicuro di voler eliminare questo file?')) {
            debugLog('Eliminazione annullata dall\'utente');
            return;
        }

        var requestData = {
            id: fileId,
            '<?= Yii::$app->request->csrfParam ?>': '<?= Yii::$app->request->csrfToken ?>'
        };

        debugLog('Dati richiesta eliminazione: ' + JSON.stringify(requestData));

        fetch('<?= yii\helpers\Url::to(['delete-file']) ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                debugLog('Risposta eliminazione - Status: ' + response.status);
                return response.json();
            })
            .then(data => {
                debugLog('Risposta eliminazione: ' + JSON.stringify(data));

                if (data.success) {
                    // Rimuovi la riga dalla tabella usando il bottone come riferimento
                    button.closest('tr').remove();
                    debugLog('File eliminato e riga rimossa dalla tabella');
                } else {
                    debugLog('Errore eliminazione: ' + data.message);
                    alert('Errore durante l\'eliminazione del file: ' + data.message);
                }
            })
            .catch(error => {
                debugLog('Errore AJAX eliminazione: ' + error.message);
                alert('Errore durante l\'eliminazione del file');
            });
    }

    // Log iniziale
    //   debugLog('Script caricato - ID Padre: <?= $model->th_id ?>');
    //    debugLog('CSRF Token: <?= Yii::$app->request->csrfToken ?>');
</script>