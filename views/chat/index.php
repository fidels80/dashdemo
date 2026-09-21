<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Analista Dashboard AI';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/marked/marked.min.js');

$this->registerCss("
    .ai-chat-wrapper { display: flex; height: 82vh; background: #fff; border: 1px solid #ddd; border-radius: 8px; overflow: hidden; margin-top:10px; }
    .ai-chat-sidebar { width: 280px; border-right: 1px solid #eee; background: #f4f6f9 !important; display: flex; flex-direction: column; }
    .ai-chat-main { flex: 1; display: flex; flex-direction: column; background: #fff; }
    
    .ai-chat-header { padding: 10px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fdfdfd; min-height: 85px; }
    
    #ai-chat-box { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background: #fdfdfd; }
    .ai-msg { max-width: 85%; padding: 12px 18px; border-radius: 15px; line-height: 1.6; font-size: 14px; }
    .ai-msg-user { align-self: flex-end; background: #007bff !important; color: white !important; }
    .ai-msg-bot { align-self: flex-start; background: #f1f3f5; color: #333; border: 1px solid #dee2e6; }
    
    .model-select-container { display: flex; flex-direction: column; align-items: flex-end; min-width: 320px; }
    .model-selector { padding: 6px 10px; border-radius: 8px; border: 1px solid #ddd; background-color: #f9f9f9; font-size: 13px; font-weight: 600; cursor: pointer; width: 230px; }
    #model-description { font-size: 11px; color: #0056b3; font-style: italic; margin-top: 5px; height: 15px; }

    .ai-msg-bot table { width: 100%; border-collapse: collapse; margin: 10px 0; background: white; border: 1px solid #ccc; }
    .ai-msg-bot th, .ai-msg-bot td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    .ai-msg-bot th { background: #f8f9fa; }

    .chat-input-wrapper {
        padding: 15px;
        background: #f9f9f9;
        border-top: 1px solid #eee;
    }

    .chat-input-container { 
        display: flex; 
        align-items: flex-end; 
        gap: 8px; 
        background: #fff; 
        padding: 8px 12px; 
        border: 1px solid #ddd; 
        border-radius: 20px; 
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        position: relative;
    }

    #user-input { 
        flex: 1; 
        border: none; 
        padding: 8px 10px; 
        padding-left: 45px;
        max-height: 150px; 
        overflow-y: auto; 
        resize: none; 
        font-size: 14px; 
        line-height: 1.5;
        outline: none !important;
        box-shadow: none !important;
        background: transparent;
        min-height: 38px;
    }

    #send-btn { 
        position: absolute; 
        left: 8px; 
        bottom: 7px;
        border-radius: 50%; 
        width: 36px; 
        height: 36px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        padding: 0;
        z-index: 10;
    }

    .ai-typing-dots { display: inline-flex; gap: 3px; margin-left: 10px; }
    .ai-dot { width: 6px; height: 6px; background: #007bff; border-radius: 50%; animation: ai-blink 1.4s infinite both; }
    .ai-dot:nth-child(2) { animation-delay: 0.2s; }
    .ai-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes ai-blink { 0%, 80%, 100% { opacity: 0; } 40% { opacity: 1; } }
    
    #status-log { font-size: 10px; color: #999; padding: 5px 20px; font-family: monospace; max-height: 100px; overflow-y: auto; background: #fdfdfd; border-top: 1px solid #eee; }
");
?>

<div class="ai-chat-wrapper">
    <div class="ai-chat-sidebar">
        <div style="padding: 15px; border-bottom: 1px solid #eee;">
            <?= Html::a('<i class="fas fa-plus"></i> Nuova Chat', ['index'], ['class' => 'btn btn-primary btn-sm btn-block']) ?>
        </div>
        <div style="flex:1; overflow-y:auto;">
            <div class="list-group list-group-flush">
                <?php foreach ($conversations as $c): ?>
                    <a href="<?= Url::to(['index', 'id' => $c->id]) ?>" class="list-group-item list-group-item-action <?= ($current->id == $c->id) ? 'active' : '' ?>" style="font-size: 12px;">
                        <i class="fas fa-comments"></i> <?= Html::encode($c->title) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="ai-chat-main">
        <div class="ai-chat-header">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div id="ai-header-icon" style="font-size: 26px; color: #007bff;"><i class="fas fa-robot"></i></div>
                <div>
                    <b style="font-size:16px; display: block;">Analista Dashboard AI</b>
                    <div id="ai-sub-status" style="font-size:11px; color:green;"><i class="fas fa-circle"></i> Online</div>
                </div>
            </div>
            <div class="model-select-container">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="font-size:11px; color:#666; font-weight:bold;">AGENTE:</span>
                    <select id="model-select" class="model-selector"><option value="">Caricamento...</option></select>
                </div>
                <div id="model-description">Inizializzazione...</div>
            </div>
        </div>

        <div id="ai-chat-box">
            <?php if (!$current->isNewRecord && !empty($current->aiMessages)): ?>
                <?php foreach ($current->aiMessages as $m): ?>
                    <div class="ai-msg <?= $m->role == 'user' ? 'ai-msg-user' : 'ai-msg-bot' ?>">
                        <div class="ai-content-raw"><?= Html::encode($m->content) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="ai-msg ai-msg-bot">
                    <b>Ciao! Sono il tuo Analista Dashboard AI. 🤖</b><br>
                    Posso aiutarti ad analizzare il codice del progetto, cercare dati nel CRM o spiegarti il funzionamento dei tuoi controller.<br><br>
                    <i>Cosa vuoi analizzare oggi?</i>
                </div>
            <?php endif; ?>
        </div>

        <div id="status-log"></div>

        <div class="chat-input-wrapper">
            <div class="chat-input-container">
                <button class="btn btn-primary" id="send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
                <textarea id="user-input" rows="1" placeholder="Scrivi un messaggio... (Ctrl+Invio)"></textarea>
            </div>
        </div>
    </div>
</div>

<?php
$sendUrl = Url::to(['chat/send-message']);
$getModelsUrl = Url::to(['chat/get-models']);
$convIdValue = $current->id ?: 'null';
$baseUrl = Url::to(['chat/index']);

$js = <<<JS
let allModelsData = [];

function updateLog(text, isImportant = false) {
    let time = new Date().toLocaleTimeString();
    let style = isImportant ? 'font-weight:bold; color:#007bff;' : '';
    $('#status-log').append('<div style="' + style + '">[' + time + '] ' + text + '</div>');
    if ($('#status-log').length) {
        $('#status-log').scrollTop($('#status-log')[0].scrollHeight);
    }
}

function scrollToBottom() { 
    if ($('#ai-chat-box').length) {
        $('#ai-chat-box').scrollTop($('#ai-chat-box')[0].scrollHeight); 
    }
}

// Inizializzazione markdown
$('.ai-msg-bot .ai-content-raw').each(function() { $(this).html(marked.parse($(this).text())); });
scrollToBottom();

function loadModels() {
    $.get('$getModelsUrl', function(data) {
        if(data.success) {
            allModelsData = data.models;
            let select = $('#model-select');
            select.empty();
            let savedModel = new URLSearchParams(window.location.search).get('model');
            allModelsData.forEach(m => {
                let isSelected = (savedModel ? (m.id === savedModel) : m.id.includes('dashboard-ai')) ? 'selected' : '';
                select.append('<option value="'+m.id+'" '+isSelected+'>'+m.name+'</option>');
            });
            updateModelUI(select.val());
        }
    });
}

function updateModelUI(modelId) {
    let model = allModelsData.find(m => m.id === modelId);
    if(model) {
        $('#model-description').html('<i class="fas ' + model.icon + '"></i> ' + model.desc);
        $('#ai-header-icon').html('<i class="fas ' + model.icon + '"></i>');
    }
}

$('#model-select').on('change', function() { updateModelUI($(this).val()); });

$('#send-btn').click(function() {
    let msg = $('#user-input').val();
    let model = $('#model-select').val();
    if (!msg.trim()) return;

    $('#ai-chat-box').append('<div class="ai-msg ai-msg-user">' + msg.replace(/\\n/g, '<br>') + '</div>');
    $('#user-input').val('').css('height', '38px').prop('disabled', true);
    $('#send-btn').prop('disabled', true);
    
    let loaderId = 'loader_' + Date.now();
    let loaderHtml = '<div id="' + loaderId + '" class="ai-msg ai-msg-bot">' +
            '<i class="fas fa-robot"></i> <b id="text_' + loaderId + '">Analisi in corso...</b>' +
            '<span class="ai-typing-dots">' +
                '<div class="ai-dot"></div><div class="ai-dot"></div><div class="ai-dot"></div>' +
            '</span>' +
        '</div>';
    
    $('#ai-chat-box').append(loaderHtml);
    updateLog('Richiesta inviata a ' + model, true);
    scrollToBottom();

    let steps = [
        "Accensione motori Vtiger...", "Verifica connessione neurale...", "Accesso alle tabelle xestrazione...",
        "Recupero dati da Vtiger CRM...", "Filtrazione parole chiave...", "Calcolo delle ore totali lavorate...",
        "Analisi dello stato dei ticket...", "Spostamento bit a nullo ...", "Consultazione dell'oracolo digitale...",
        "Rimozione duplicati fantasma...", "Sintesi dei risultati in corso...", "Verifica coerenza dei dati...",
        "Generazione tabella Markdown...", "Traduzione binario -> Italiano...", "Quasi pronto, sto impacchettando i dati...",
        "Verifica finale anti-allucinazione...", "Spostamento dei bit concluso...", "Generazione Report Finale..."
    ];

    let crazySteps = [
        "L'IA sta discutendo con il database...", "Contando le pecorelle elettriche...", "Il server sta chiedendo più RAM...",
        "Sto cercando di convincere i bit a collaborare...", "Ancora un attimo, Stefano dice che ci siamo...",
        "Ricarica caffeina per i processori...", "Riordinando i dati per colore...", "Speriamo che il cliente non se ne accorga...",
        "Inseguendo un bug molto veloce...", "Stefano sta aprendo un ticket", "Simone sta inveendo contro una qualche divinità",
        "Franscesco è in amministrazione a chiaccherare come al solito", "Rimozione bug timidi nel codice...",
        "Ricerca Stefano (non risponde, faccio io)...", "Lucidatura pixel per la tabella..."
    ];

    let stepIdx = 0;
    let stepTimer = setInterval(() => {
        let currentText = "";
        if(stepIdx < steps.length) {
            currentText = steps[stepIdx];
            stepIdx++;
        } else {
            let randomIdx = Math.floor(Math.random() * crazySteps.length);
            currentText = crazySteps[randomIdx];
        }
        $('#text_' + loaderId).text(currentText);
        updateLog(currentText);
        scrollToBottom();
    }, 2500);

    let startTime = Date.now();

    $.post('$sendUrl', {message: msg, conversation_id: "$convIdValue", model: model}, function(data) {
        clearInterval(stepTimer);
        $('#' + loaderId).remove();

        if (data.success && data.reply) {
            let elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
            updateLog('Risposta ricevuta in ' + elapsed + 's', true);
            
            let responseHtml = $('<div class="ai-msg ai-msg-bot"></div>').html(marked.parse(data.reply));
            $('#ai-chat-box').append(responseHtml);
            
            if ("$convIdValue" === 'null') {
                window.location.href = '$baseUrl' + (('$baseUrl'.indexOf('?') !== -1) ? '&' : '?') + 'id=' + data.conversation_id + '&model=' + model;
            } else {
                $('#user-input').prop('disabled', false).focus();
                $('#send-btn').prop('disabled', false);
            }
        } else {
             let errorMsg = data.error ? data.error : "Errore sconosciuto nella risposta.";
             $('#ai-chat-box').append('<div class="ai-msg ai-msg-bot text-danger"><b>Errore:</b> ' + errorMsg + '</div>');
             $('#user-input').prop('disabled', false);
             $('#send-btn').prop('disabled', false);
        }
        scrollToBottom();
    });
});

loadModels();

$('#user-input').on('keydown', function(e) {
    if (e.ctrlKey && e.keyCode == 13) { e.preventDefault(); $('#send-btn').click(); }
});
JS;
$this->registerJs($js);
?>