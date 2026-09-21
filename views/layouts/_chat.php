<?php

use yii\helpers\Html;
use yii\helpers\Url;

// Recuperiamo la cartella degli assets di AdminLTE per le immagini di default (avatar)
$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>

<style>
    /* Posizionamento del bottone rotondo */
    #chat-circle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--primary-color, #007bff);
        color: white;
        text-align: center;
        line-height: 60px;
        font-size: 24px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        z-index: 9999;
        transition: transform 0.3s;
    }

    #chat-circle:hover {
        transform: scale(1.1);
    }

    /* Posizionamento del box della chat (inizialmente nascosto) */
    #chat-box {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        display: none;
        /* Nascosto di default */
        z-index: 9999;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>

<div id="chat-circle" onclick="toggleChat()">
    <i class="fas fa-comments"></i>
</div>

<div id="chat-box" class="card direct-chat direct-chat-primary">

<div class="card-header" style="background-color: var(--primary-color, #007bff); color: white;">
        <h3 class="card-title">Chat <i class="fas fa-angle-right mx-1" style="font-size: 0.8rem;"></i></h3>

        <div class="dropdown d-inline-block">
            <button class="btn btn-sm dropdown-toggle text-white font-weight-bold border-0" type="button" id="chatContactDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: transparent; box-shadow: none;">
                <img id="chat-current-avatar" src="" style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%; display: none;" class="mr-1">
                <span id="chat-current-name">Chat Globale</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="chatContactDropdown" id="chat-contact-list" style="max-height: 300px; overflow-y: auto;">
                <a class="dropdown-item d-flex align-items-center" href="#" onclick="changeChatContact('', 'Chat Globale', '')">
                    <i class="fas fa-globe mr-2 text-muted" style="width: 24px; text-align: center;"></i> Chat Globale
                </a>
                </div>
        </div>
        
        <input type="hidden" id="chat-contact-select" value="">

        <div class="card-tools">
            <button type="button" class="btn btn-tool text-white" onclick="toggleChat()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="direct-chat-messages" id="chat-messages-container">
        </div>
    </div>

    <div class="card-footer">
        <form action="#" method="post" id="chat-form" onsubmit="inviaMessaggio(event)">
            <div class="input-group">
                <input type="text" name="message" id="chat-input-text" placeholder="Scrivi un messaggio..." class="form-control" autocomplete="off">
                <span class="input-group-append">
                    <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #007bff); border-color: var(--primary-color, #007bff);">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
</div>
<script>
    var urlSend = '<?= Url::to(['staff-chat/send']) ?>';
    var urlFetch = '<?= Url::to(['staff-chat/fetch']) ?>';
    var urlUserList = '<?= Url::to(['staff-chat/user-list']) ?>';
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var defaultAvatarUrl = '<?= $assetDir ?>/img/user1-128x128.jpg'; // Avatar grigio di default

    function toggleChat() {
        var chatBox = document.getElementById('chat-box');
        if (chatBox.style.display === 'none' || chatBox.style.display === '') {
            chatBox.style.display = 'block';
            scrollChatToBottom();
        } else {
            chatBox.style.display = 'none';
        }
    }

    function scrollChatToBottom() {
        var container = $('#chat-messages-container');
        container.scrollTop(container[0].scrollHeight);
    }

    // --- NUOVA GESTIONE DELLA LISTA UTENTI CON IMMAGINI ---
function loadUserList() {
        $.get(urlUserList, function(users) {
            var list = $('#chat-contact-list');
            list.find('.user-item').remove(); 

            if (users && users.length > 0) {
                users.forEach(function(u) {
                    var avatarUrl = defaultAvatarUrl;
                    if (u.file) {
                        avatarUrl = '/uploads/' + u.id + '_' + u.file;
                    }
                    
                    var safeName = u.username.replace(/'/g, "\\'"); 

                    // --- LOGICA DEL BADGE ---
                    var badge = '';
                    if (parseInt(u.unread_count) > 0) {
                        badge = '<span class="badge badge-danger ml-auto">' + u.unread_count + '</span>';
                    }

                    // Aggiungiamo il badge alla fine della riga
                    var html = '<a class="dropdown-item user-item d-flex align-items-center" href="#" onclick="changeChatContact(\'' + u.id + '\', \'' + safeName + '\', \'' + avatarUrl + '\')">' +
                               '<img src="' + avatarUrl + '" style="width: 24px; height: 24px; object-fit: cover; border-radius: 50%;" class="mr-2 border">' +
                               '<span>' + u.username + '</span>' + 
                               badge + 
                               '</a>';
                    list.append(html);
                });
            }
        });
    }
    // Funzione che scatta quando clicchi su un utente nel nuovo menu
    function changeChatContact(id, name, avatarUrl) {
        $('#chat-contact-select').val(id); // Aggiorna l'input nascosto
        $('#chat-current-name').text(name); // Cambia il nome in alto

        if (avatarUrl !== '') {
            $('#chat-current-avatar').attr('src', avatarUrl).show();
        } else {
            $('#chat-current-avatar').hide(); // Nasconde la foto per la Chat Globale
        }

        loadMessages(true); // Ricarica subito i messaggi
    }
    // --------------------------------------------------------

    function loadMessages(forceScroll = false) {
        if ($('#chat-box').is(':hidden')) return; 

        var contactId = $('#chat-contact-select').val();

        $.ajax({
            url: urlFetch,
            type: 'GET',
            data: { contact_id: contactId },
            success: function(html) {
                var container = $('#chat-messages-container');
                var isScrolledToBottom = container[0].scrollHeight - container[0].clientHeight <= container[0].scrollTop + 50;

                container.html(html);

                if (isScrolledToBottom || forceScroll) {
                    scrollChatToBottom();
                }
            }
        });
    }

    function inviaMessaggio(e) {
        e.preventDefault();
        var input = $('#chat-input-text');
        var messaggio = input.val().trim();
        var toUser = $('#chat-contact-select').val();

        if (messaggio !== '') {
            $.ajax({
                url: urlSend,
                type: 'POST',
                data: {
                    message: messaggio,
                    to_user_id: toUser,
                    _csrf: csrfToken
                },
                success: function(response) {
                    if (response.status === 'success') {
                        input.val(''); 
                        loadMessages(true); 
                    } else {
                        alert('Errore: ' + response.message);
                    }
                },
                error: function() {
                    alert('Errore di connessione al server.');
                }
            });
        }
    }

    $(document).ready(function() {
        loadUserList(); 
        setInterval(function() { loadMessages(false); }, 5000);
        loadMessages(true); 
    });
</script>