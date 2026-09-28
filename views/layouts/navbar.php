<?php

use yii\helpers\Html;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
\hail812\adminlte3\assets\AdminLteAsset::register($this);

$assetDir = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');

$publishedRes = Yii::$app->assetManager->publish('@vendor/hail812/yii2-adminlte3/src/web/js');
$this->registerJsFile($publishedRes[1] . '/control_sidebar.js', ['depends' => '\hail812\adminlte3\assets\AdminLteAsset']);


$usrid = Yii::$app->user->Id;
$nuoviMessaggi = 0;

if ($usrid !== null) {
    // 1. Lettura utente per eventuale lastlogin (o last_activity)
    $ris = (new \yii\db\Query())
        ->select(['lastlogin'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();

    // --- NUOVA LOGICA: CONTEGGIO MESSAGGI CHAT NON LETTI ---
    $nuoviMessaggi = (new \yii\db\Query())
        ->from('sys_chat_messages')
        ->where(['to_user_id' => $usrid, 'is_read' => 0]) // Solo messaggi privati non letti
        ->count('*');
}

// --- CALCOLO NOTIFICHE ---
// Calcolo pratiche delle ultime 24 ore su db5
$ieri = date('Ymd H:i:s', strtotime('-24 hours')); // Formato sicuro per SQL Server
$praticheRecenti = (new \yii\db\Query())
    ->from('xtravelhead')
    ->where(['>=', 'datath', $ieri])
    ->count('*', Yii::$app->db5);

// Somma per il badge rosso della campanella
$totaleNotifiche = $praticheRecenti + $nuoviMessaggi;
// ------------------------------

?>

<script>
    function dm() {
        var isDark = !$('body').hasClass('dark-mode');
        $('body').toggleClass('dark-mode', isDark);
        try {
            localStorage.setItem('dashdemo-theme', isDark ? 'dark' : 'light');
        } catch (e) { }
        aggiornaIconaTema(isDark);
    }

    function aggiornaIconaTema(isDark) {
        $('#dm-icon').toggleClass('far fa-moon', !isDark).toggleClass('fas fa-sun', isDark);
    }

    $(function () {
        aggiornaIconaTema($('body').hasClass('dark-mode'));
        $(document).on('click', '#dm-toggle', function (e) {
            e.preventDefault();
            dm();
        });
    });
</script>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    <ul class="navbar-nav">
        </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" data-enable-remember="true">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Notifiche">
                <i class="far fa-bell"></i>
                <?php if ($totaleNotifiche > 0): ?>
                    <span class="badge badge-danger navbar-badge"><?= $totaleNotifiche ?></span>
                <?php endif; ?>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header"><?= $totaleNotifiche ?> Notifiche</span>
                <div class="dropdown-divider"></div>

<?php /* 
<a href="<?= \yii\helpers\Url::to(['/xtravelhead/index']) ?>" class="dropdown-item">
    <i class="fas fa-file-alt mr-2 text-info"></i> <?= $praticheRecenti ?>
     Pratiche recenti
    <span class="float-right text-muted text-sm">24h</span>
</a>
<div class="dropdown-divider"></div>
*/ ?>

                <a href="javascript:void(0);" onclick="toggleChat();" class="dropdown-item">
                    <i class="fas fa-comments mr-2 text-success"></i> <?= $nuoviMessaggi ?> Nuovi messaggi
                    <?php if ($nuoviMessaggi > 0): ?>
                        <span class="float-right badge badge-danger text-sm">Nuovi</span>
                    <?php else: ?>
                        <span class="float-right text-muted text-sm">Chat</span>
                    <?php endif; ?>
                </a>
                <div class="dropdown-divider"></div>

                <a href="<?= \yii\helpers\Url::to(['/planning']) ?>" class="dropdown-item">
                    <i class="fas fa-calendar-day mr-2 text-warning"></i> Vai al Calendario
                </a>
                <div class="dropdown-divider"></div>

                <a href="#" class="dropdown-item dropdown-footer">Vedi tutte le notifiche</a>
            </div>
        </li>

        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Microgestionale documentale">
                <i class="fas fa-file-invoice"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/mgdocumento/index']) ?>"><i class="fas fa-file-invoice mr-2"></i> Documenti</a>
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/mgtipodocumento/index']) ?>"><i class="fas fa-tags mr-2"></i> Tipi documento</a>
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/mganagrafica/index']) ?>"><i class="fas fa-users mr-2"></i> Anagrafica</a>
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/mgarticolo/index']) ?>"><i class="fas fa-boxes mr-2"></i> Articoli</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/apitoken/index']) ?>"><i class="fas fa-key mr-2"></i> Token API</a>
                <a class="dropdown-item" href="<?= \yii\helpers\Url::to(['/apitoken/test']) ?>"><i class="fas fa-flask mr-2"></i> Prova API</a>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0);" id="btn-segnalazione-global" title="Segnala Anomalia">
                <i class="fas fa-bug"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/site/manuale']) ?>" title="Apri il Manuale Utente">
                <i class="fas fa-book"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="<?= \yii\helpers\Url::to(['/two-factor/setup']) ?>" title="Autenticazione a Due Fattori (2FA)">
                <i class="fas fa-shield-alt"></i>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="javascript:void(0);" id="dm-toggle" title="Tema chiaro/scuro" role="button">
                <i class="far fa-moon" id="dm-icon"></i>
            </a>
        </li>

        <li class="nav-item">
            <?= Html::a('<i class="fas fa-sign-out-alt"></i>', ['/site/logout'], ['data-method' => 'post', 'class' => 'nav-link', 'title' => 'Logout']) ?>
        </li>
<?php /*
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button" title="Impostazioni">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
*/ ?>

    </ul>
</nav>