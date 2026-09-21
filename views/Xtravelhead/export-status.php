<?php

/**
 * Vista per monitorare lo status dell'export XTravel
 * File: views/your-controller/export-status.php
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '';
//$this->title = 'Status Export XTravel';
//$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="export-status">
    <h1><?= Html::encode('Status Export Prenotazione') ?></h1>
    <div class="alert alert-warning">
        <strong>Puoi anche chiudere la schermata riceverai una email con il link per scaricare</strong>
        <span class="pull-right">
        </span>
    </div>
    <?php

    $url = Url::to(['xtravelhead/masterhotel', 'id' => $job['data_id']]);

    echo Html::button('Torna indietro', [
        'class' => 'button-base-support button-lift',
        'onclick' => 'window.location.href = "' . $url . '"'
    ]);


    $tipoexp = Yii::$app->request->get('tipoexp');
    $jobs = (new \yii\db\Query())
        ->from('export_jobs')
        ->where(['progress' => 0])
        ->andwhere(['job_id' => $jobId])

        ->orderBy(['id' => SORT_ASC])
        ->all();



    $yiiPath = 'C:\xampp\htdocs\dashdemo\yii';
    $jdataid = $job['data_id'];
    $jumail =           $job['username'];
    // Costruisco il comando diretto
    $command = "php {$yiiPath} background-export/process-excel \"$jobId\" \"$jdataid\" \"$jumail\" \"$jumail\"";
    $userEmail = $job['user_email'];

    $jobIdEscaped = escapeshellarg($jobId);
    $dataIdEscaped = escapeshellarg($jdataid);
    $usernameEscaped = escapeshellarg($jumail);
    $emailEscaped = escapeshellarg($userEmail);
    $tipoexescaped = escapeshellarg($tipoexp);
    $command = "php \"{$yiiPath}\" background-export/process-excel {$jobIdEscaped} {$dataIdEscaped} {$usernameEscaped} {$emailEscaped} {$tipoexescaped}";
    //$commad= "php yii export-job-runner/run";
    if (PHP_OS_FAMILY === 'Windows') {
        // Windows: lancia in background
        pclose(popen('start "" /B ' . $command, "r"));
    } else {
        // Linux/Unix
        exec($command . " > /dev/null 2>&1 &");
    }

    ?>
    <br>
    <br>





    <div class="alert alert-info">
        <strong>Job ID:</strong> <?= Html::encode($jobId) ?>
        <span class="pull-right">
            <strong>Tipo:</strong> <?= strtoupper($job['export_type'] ?? 'XTravel') ?>
        </span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Progresso Export
                        <span class="pull-right">
                            <small id="records-info">
                                <?= $job['records_count'] ? number_format($job['records_count']) . ' record' : 'Calcolando...' ?>
                            </small>
                        </span>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="progress" style="height: 30px;">
                        <div id="progress-bar"
                            class="progress-bar progress-bar-striped active"
                            role="progressbar"
                            style="width: <?= $job['progress'] ?? 0 ?>%; min-width: 2em;">
                            <span id="progress-text"><?= $job['progress'] ?? 0 ?>%</span>
                        </div>
                    </div>
                    <?php if (Yii::$app->user->identity->level >= 80): ?>
                        <div id="status-info" class="mt-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> <span id="current-status" class="label label-info"><?= ucfirst($job['status'] ?? 'unknown') ?></span></p>
                                    <p><strong>Messaggio:</strong> <span id="current-message"><?= Html::encode($job['message'] ?? 'In attesa...') ?></span></p>
                                    <p><strong>Record da elaborare:</strong> <span id="record-count"><?= $job['records_count'] ? number_format($job['records_count']) : 'N/A' ?></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Avviato:</strong> <?= $job['created_at'] ?? 'N/A' ?></p>
                                    <p><strong>Ultimo aggiornamento:</strong> <span id="last-update"><?= $job['updated_at'] ?? 'N/A' ?></span></p>
                                    <p><strong>Tempo trascorso:</strong> <span id="elapsed-time">Calcolando...</span></p>
                                </div>
                            </div>
                        </div>

                        <div id="performance-info" class="mt-3" style="display: none;">
                            <div class="well well-sm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Velocità:</strong> <span id="processing-speed">-</span> rec/sec
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Tempo rimanente:</strong> <span id="eta">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Dimensione stimata:</strong> <span id="estimated-size">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div id="download-section" style="display: none;" class="mt-4">
                        <div class="alert alert-success">
                            <h4><i class="glyphicon glyphicon-ok-circle"></i> Export Completato!</h4>
                            <div id="completion-details"></div>
                            <div class="mt-3">
                                <button id="download-link" data-url="" class="btn btn-success btn-lg">
                                    <i class="glyphicon glyphicon-download"></i> Scarica File Excel
                                </button>
                                <button class="btn btn-default" onclick="copyDownloadLink()">
                                    <i class="glyphicon glyphicon-copy"></i> Copia Link
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="error-section" style="display: none;" class="mt-4">
                        <div class="alert alert-danger">
                            <h4><i class="glyphicon glyphicon-exclamation-sign"></i> Export Fallito</h4>
                            <p id="error-message"></p>
                            <div class="mt-3">
                                <a href="<?= Url::to(['export-to-excel', 'id' => $job['data_id']]) ?>" class="btn btn-warning">
                                    <i class="glyphicon glyphicon-repeat"></i> Riprova Export
                                </a>
                                <a href="<?= Url::to(['index']) ?>" class="btn btn-default">
                                    <i class="glyphicon glyphicon-arrow-left"></i> Torna Indietro
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <?php if (Yii::$app->user->identity->level >= 80): ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Informazioni Job</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <li><strong>Data ID:</strong> <?= Html::encode($job['data_id'] ?? 'N/A') ?></li>
                            <li><strong>Utente:</strong> <?= Html::encode($job['username'] ?? 'N/A') ?></li>
                            <li><strong>Email:</strong> <?= Html::encode($job['user_email'] ?? 'N/A') ?></li>
                            <li><strong>Tipo Export:</strong> <?= strtoupper($job['export_type'] ?? 'XTravel') ?></li>
                        </ul>

                        <div class="mt-3">
                            <h5>Performance:</h5>
                            <ul class="small">
                                <li id="perf-processing-time">Tempo elaborazione: <span class="text-muted">In corso...</span></li>
                                <li id="perf-file-size">Dimensione file: <span class="text-muted">-</span></li>
                                <li id="perf-records-per-sec">Record/sec: <span class="text-muted">-</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <?php if (!empty($stats)): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Statistiche Sistema</h3>
                        </div>
                        <div class="panel-body">
                            <ul class="small list-unstyled">
                                <li><strong>Tempo medio elaborazione:</strong> <?= round($stats['avg_processing_time'], 1) ?>s</li>
                                <li><strong>Export XTravel oggi:</strong> <?= $stats['similar_exports_today'] ?></li>
                                <li><strong>I tuoi export oggi:</strong> <?= $stats['user_exports_today'] ?></li>
                                <li><strong>Carico sistema:</strong> <span class="label label-<?= $stats['system_load'] === 'alto' ? 'danger' : ($stats['system_load'] === 'medio' ? 'warning' : 'success') ?>"><?= ucfirst($stats['system_load']) ?></span></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Azioni</h3>
                </div>
                <div class="panel-body">
                    <button id="refresh-btn" class="btn btn-default btn-block" onclick="checkStatus()">
                        <i class="glyphicon glyphicon-refresh"></i> Aggiorna Status
                    </button>

                    <a href="<?= Url::to(['index']) ?>" class="btn btn-default btn-block mt-2">
                        <i class="glyphicon glyphicon-arrow-left"></i> Torna alla Lista
                    </a>

                    <?php if (Yii::$app->user->can('admin')): ?>
                        <button class="btn btn-danger btn-block mt-2" onclick="cancelJob()"
                            id="cancel-btn" <?= in_array($job['status'], ['completed', 'failed']) ? 'disabled' : '' ?>>
                            <i class="glyphicon glyphicon-remove"></i> Cancella Job
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    let statusCheckInterval;
    let lastStatus = '<?= $job['status'] ?? 'unknown' ?>';
    let startTime = new Date('<?= $job['created_at'] ?>').getTime();

    // Avvia controllo automatico status
    $(document).ready(function() {
        updateElapsedTime();
        checkStatus();

        // Se non è completato, avvia polling ogni 3 secondi
        if (lastStatus !== 'completed' && lastStatus !== 'failed') {
            statusCheckInterval = setInterval(checkStatus, 3000);

            // Aggiorna tempo trascorso ogni secondo
            setInterval(updateElapsedTime, 1000);
        }
    });

    function updateElapsedTime() {
        const now = new Date().getTime();
        const elapsed = Math.floor((now - startTime) / 1000);
        const minutes = Math.floor(elapsed / 60);
        const seconds = elapsed % 60;

        $('#elapsed-time').text(minutes + 'm ' + seconds + 's');
    }

    function checkStatus() {
        const refreshBtn = $('#refresh-btn');
        refreshBtn.prop('disabled', true);
        refreshBtn.html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> Controllo...');

        $.ajax({
            url: '<?= Url::to(['check-status']) ?>',
            data: {
                jobId: '<?= $jobId ?>'
            },
            dataType: 'json',
            success: function(response) {
                updateUI(response);
            },
            error: function() {
                console.error('Errore nel controllo status');
                $('#current-message').text('Errore nella comunicazione con il server');
            },
            complete: function() {
                refreshBtn.prop('disabled', false);
                refreshBtn.html('<i class="glyphicon glyphicon-refresh"></i> Aggiorna Status');
            }
        });
    }

    function updateUI(data) {
        // Aggiorna progress bar
        const progressBar = $('#progress-bar');
        const progressText = $('#progress-text');
        const progress = data.progress || 0;

        progressBar.css('width', progress + '%');
        progressText.text(progress + '%');

        // Aggiorna contatori
        if (data.records_count) {
            $('#record-count').text(data.records_count.toLocaleString());
            $('#records-info').text(data.records_count.toLocaleString() + ' record');
        }

        // Aggiorna status label
        const statusLabel = $('#current-status');
        statusLabel.removeClass('label-info label-success label-danger label-warning');

        switch (data.status) {
            case 'completed':
                statusLabel.addClass('label-success');
                progressBar.removeClass('progress-bar-striped active');
                progressBar.addClass('progress-bar-success');
                break;
            case 'failed':
                statusLabel.addClass('label-danger');
                progressBar.removeClass('progress-bar-striped active');
                progressBar.addClass('progress-bar-danger');
                break;
            case 'processing':
                statusLabel.addClass('label-warning');
                $('#performance-info').show();
                break;
            default:
                statusLabel.addClass('label-info');
        }

        statusLabel.text(data.status ? data.status.charAt(0).toUpperCase() + data.status.slice(1) : 'Unknown');

        // Aggiorna messaggio e timestamp
        $('#current-message').text(data.message || 'Nessun messaggio');
        $('#last-update').text(data.updated_at || 'N/A');

        // Calcola e mostra performance durante processing
        if (data.status === 'processing' && data.records_count && data.elapsed_minutes) {
            const recordsPerMinute = Math.round(data.records_count * (data.progress / 100) / data.elapsed_minutes);
            const recordsPerSecond = Math.round(recordsPerMinute / 60);

            $('#processing-speed').text(recordsPerSecond);

            if (recordsPerMinute > 0 && data.progress > 10) {
                const remainingRecords = data.records_count * ((100 - data.progress) / 100);
                const etaMinutes = Math.round(remainingRecords / recordsPerMinute);
                $('#eta').text(etaMinutes + ' min');
            }

            // Stima dimensione file (circa 100 byte per record)
            const estimatedSizeMB = Math.round((data.records_count * 100) / 1024 / 1024 * 100) / 100;
            $('#estimated-size').text(estimatedSizeMB + ' MB');
        }

        // Aggiorna info performance
        if (data.processing_time) {
            $('#perf-processing-time').html('Tempo elaborazione: <span class="text-success">' + data.processing_time + 's</span>');

            if (data.records_count && data.processing_time > 0) {
                const rps = Math.round(data.records_count / data.processing_time);
                $('#perf-records-per-sec').html('Record/sec: <span class="text-info">' + rps + '</span>');
            }
        }

        if (data.file_size_mb) {
            $('#perf-file-size').html('Dimensione file: <span class="text-primary">' + data.file_size_mb + ' MB</span>');
        }

        // Gestisci completamento
        if (data.status === 'completed') {
            clearInterval(statusCheckInterval);

            if (data.download_url) {
                $('#download-link').attr('data-url', data.download_url); // <-- NUO

                const details = `
                <p><strong>Export completato con successo!</strong></p>
                <ul class="list-unstyled">
                    <li><i class="glyphicon glyphicon-ok text-success"></i> Record elaborati: <strong>${data.records_count.toLocaleString()}</strong></li>
                    <li><i class="glyphicon glyphicon-time text-info"></i> Tempo totale: <strong>${data.processing_time}s</strong></li>
                    <li><i class="glyphicon glyphicon-file text-primary"></i> Dimensione file: <strong>${data.file_size_mb} MB</strong></li>
                </ul>
                <p><small class="text-muted">Una email di conferma è stata inviata al tuo indirizzo.</small></p>
            `;

                $('#completion-details').html(details);
                $('#download-section').show();
            }

            $('#cancel-btn').prop('disabled', true);
        }

        // Gestisci errore
        if (data.status === 'failed') {
            clearInterval(statusCheckInterval);
            $('#error-message').text(data.message || 'Errore sconosciuto durante l\'export');
            $('#error-section').show();
            $('#cancel-btn').prop('disabled', true);
        }

        lastStatus = data.status;
    }

    // --- GESTIONE DOWNLOAD CON IFRAME E SWEETALERT ---
    $(document).off('click', '#download-link').on('click', '#download-link', function(e) {
        // Blocca qualsiasi comportamento standard del browser/tema
        e.preventDefault();
        e.stopPropagation();

        const url = $(this).attr('data-url');
        if (!url) return;

        // 1. Crea un iframe invisibile per scaricare il file senza cambiare pagina
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = url;
        document.body.appendChild(iframe);

        // 2. Mostra la SweetAlert (supporta sia SweetAlert 1 che 2)
        if (typeof Swal !== 'undefined') {
            // Sintassi SweetAlert 2
            Swal.fire({
                title: 'Download in corso!',
                text: 'Il file Excel è in fase di scaricamento. Controlla la cartella download.',
                icon: 'success',
                confirmButtonText: 'Torna alla Lista',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= Url::to(['index']) ?>';
                }
            });
        } else if (typeof swal !== 'undefined') {
            // Sintassi SweetAlert 1
            swal({
                title: 'Download in corso!',
                text: 'Il file Excel è in fase di scaricamento. Controlla la cartella download.',
                type: 'success',
                confirmButtonText: 'Torna alla Lista',
                closeOnConfirm: true
            }, function() {
                window.location.href = '<?= Url::to(['index']) ?>';
            });
        } else {
            // Fallback nel caso SweetAlert non fosse caricato
            alert('Download avviato con successo!');
            window.location.href = '<?= Url::to(['index']) ?>';
        }
    });

    function copyDownloadLink() {
        // Ora leggiamo da data-url invece che da href
        const downloadUrl = $('#download-link').attr('data-url');
        if (downloadUrl) {
            navigator.clipboard.writeText(window.location.origin + downloadUrl).then(function() {
                alert('Link copiato negli appunti!');
            }).catch(function() {
                const textArea = document.createElement('textarea');
                textArea.value = window.location.origin + downloadUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Link copiato negli appunti!');
            });
        }
    }

    function cancelJob() {
        if (confirm('Sei sicuro di voler cancellare questo job? L\'operazione non può essere annullata.')) {
            $.ajax({
                url: '<?= Url::to(['cancel-job']) ?>',
                data: {
                    jobId: '<?= $jobId ?>'
                },
                method: 'POST',
                success: function(response) {
                    location.reload();
                },
                error: function() {
                    alert('Errore nella cancellazione del job');
                }
            });
        }
    }

    // Pulisci interval quando si lascia la pagina
    $(window).on('beforeunload', function() {
        if (statusCheckInterval) {
            clearInterval(statusCheckInterval);
        }
    });

    // Notifica desktop quando completato (se supportata)
    function showDesktopNotification(status, message) {
        if ("Notification" in window && Notification.permission === "granted") {
            const title = status === 'completed' ? '✅ Export Completato' : '❌ Export Fallito';
            const notification = new Notification(title, {
                body: message,
                icon: '/favicon.ico'
            });

            setTimeout(() => notification.close(), 5000);
        }
    }

    // Richiedi permesso notifiche se non dato
    if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
        Notification.requestPermission();
    }
</script>

<style>
    .progress {
        margin-bottom: 20px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, .1);
    }

    .progress-bar {
        transition: width 0.3s ease;
    }

    .panel-title {
        font-size: 16px;
        font-weight: bold;
    }

    .mt-2 {
        margin-top: 10px;
    }

    .mt-3 {
        margin-top: 15px;
    }

    .mt-4 {
        margin-top: 20px;
    }

    #download-section,
    #error-section {
        border-radius: 5px;
        padding: 15px;
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .small {
        font-size: 12px;
        color: #666;
    }

    .label {
        font-size: 12px;
        padding: 4px 8px;
    }

    .glyphicon-spin {
        animation: spin 1s infinite linear;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .well-sm {
        background-color: #f8f9fa;
        border: 1px solid #e3e6ea;
        border-radius: 4px;
        padding: 10px;
    }

    .text-success {
        color: #5cb85c !important;
    }

    .text-info {
        color: #5bc0de !important;
    }

    .text-primary {
        color: #337ab7 !important;
    }

    .text-muted {
        color: #777 !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {

        .col-md-4,
        .col-md-6,
        .col-md-8 {
            margin-bottom: 15px;
        }

        .btn-block {
            margin-bottom: 10px;
        }

        .progress {
            height: 25px;
        }

        #progress-text {
            font-size: 12px;
        }
    }

    /* Status indicators */
    .label-success {
        background-color: #5cb85c;
    }

    .label-info {
        background-color: #5bc0de;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .label-danger {
        background-color: #d9534f;
    }

    /* Performance info styling */
    #performance-info {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
        border-left: 4px solid #007bff;
    }

    #performance-info .row>div {
        text-align: center;
        padding: 10px;
    }

    #performance-info .row>div:not(:last-child) {
        border-right: 1px solid #dee2e6;
    }

    /* Enhanced buttons */
    .btn {
        transition: all 0.2s ease-in-out;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-success {
        background: linear-gradient(135deg, #5cb85c 0%, #449d44 100%);
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #d9534f 0%, #c9302c 100%);
        border: none;
    }

    /* Alert styling */
    .alert-success {
        background: linear-gradient(135deg, #dff0d8 0%, #d0e9c6 100%);
        border-color: #5cb85c;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f2dede 0%, #e7c3c3 100%);
        border-color: #d9534f;
    }

    /* Loading animation for progress bar */
    .progress-bar-striped {
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        background-size: 40px 40px;
    }

    .progress-bar-striped.active {
        animation: progress-bar-stripes 2s linear infinite;
    }

    @keyframes progress-bar-stripes {
        from {
            background-position: 40px 0;
        }

        to {
            background-position: 0 0;
        }
    }
</style>