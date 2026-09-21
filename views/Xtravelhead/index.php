<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>

<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XtravelheadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$x = Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
//use kartik\icons\Icon;
//Icon::map($this, Icon::EL);
$usrid = Yii::$app->user->Id;

$this->title = "Commesse"; // Oppure una stringa vuota

// Estraiamo tutti i modelli dal dataProvider
$models = $dataProvider->getModels();
yii::warning($dataProvider);
// Creiamo un array con i valori unici di x_cfdesk presenti nei modelli attuali
$clientiPresenti = \yii\helpers\ArrayHelper::getColumn($models, 'x_cfdesk');
$clientiUnici = array_unique(array_filter($clientiPresenti)); // Rimuove duplicati e valori vuoti
sort($clientiUnici); // Ordina alfabeticamente

// Prepariamo i dati per la Select2 (formato id => nome)
$dataSelect2 = array_combine($clientiUnici, $clientiUnici);


?>


<style>
    .row {
        margin-right: 15px;
        margin-left: 15px;
    }
</style>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle"></i> <?= Yii::$app->session->getFlash('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            
        </button>
    </div>
<?php endif; ?>
<div class="row d-flex align-items-center" style="gap: 10px; margin-bottom: 15px;">
    <?php echo Html::a('<i class="fa-solid fa-magnifying-glass"></i> Cerca Commessa', ['xtravelhead/index2'], ['class' => 'button-base']); ?>
    <?php //echo Html::a('<i class="fa-solid fa-box-archive"></i> Archivio', ['xtravelhead/index3'], ['class' => 'button-base']); ?>
    <?php if ((Yii::$app->user->identity->level ?? 0) >= 0): ?>
        <div style="flex-grow: 1; max-width: 400px;">
            <?php
            echo Select2::widget([
                'name' => 'filtro_cliente_rapido',
                'data' => $dataSelect2,
                'options' => [
                    'placeholder' => 'Filtra per cliente...',
                    'id' => 'select-cliente-local'
                ],
                'pluginOptions' => ['allowClear' => true],
                'pluginEvents' => [
                    "change" => "function() {
        var clienteScelto = $(this).val();
        var visibili = 0;

        // 1. Prima mostriamo/nascondiamo le card
        $('.row [data-cliente]').each(function() {
            var clienteCard = $(this).attr('data-cliente');
            var container = $(this).parent(); // Il div generato da ListView con col-lg-4...

            if (clienteScelto === '' || clienteCard === clienteScelto) {
                container.show();
                visibili++;
            } else {
                container.hide();
            }
        });

        // 2. Gestione della larghezza dinamica
        $('.row [data-cliente]').each(function() {
            var container = $(this).parent();
            
            if (visibili === 1) {
                // Se ce n'è solo una, la allarghiamo (es. col-lg-10) e la centriamo
                container.removeClass('col-lg-4').addClass('col-lg-10 offset-lg-1');
            } else {
                // Altrimenti torniamo al layout standard a 3 colonne
                container.removeClass('col-lg-10 offset-lg-1').addClass('col-lg-4');
            }
        });

        // Nascondiamo il pager se stiamo filtrando
        $('.pagination').toggle(clienteScelto === '');
    }",
                ]
            ]);
            ?>
        </div>
        <?php
            if (Yii::$app->user->identity->level  >= 80) {
        echo Html::a(
            '<i class="fa-regular fa-file"></i> Crea Prenotazione',
            ['xtravelhead/crea'],
            ['class' => 'button-base']
        ); 
            }
        ?>


    <?php endif; ?>
</div>

<br>

<br>
<style>
    #rotate-notice {
        display: none;
        position: fixed;
        inset: 0;
        background: #0009;
        color: white;
        font-size: 1.5em;
        text-align: center;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    @media screen and (orientation: portrait) {
        #rotate-notice {
            display: flex;
        }
    }
</style>

<div id="rotate-notice">
    Ruota il dispositivo in orizzontale per continuare 🔄
</div>
<div class="row">
    <?php


    $itemCount = $dataProvider->getTotalCount();

    // 2. Scegliamo la classe CSS in base alla quantità
    if ($itemCount == 1) {
        // Un solo record: card larga (col-lg-10) e centrata (mx-auto)
        $itemClass = 'col-lg-10 col-md-6 mb-4';
    } else {
        // Più record: layout a griglia standard (3 colonne per riga su schermi larghi)
        $itemClass = 'col-lg-4 col-md-6 mb-4';
    }
    echo \yii\widgets\ListView::widget([
        'dataProvider' => $dataProvider,
        'itemView' => '_card',
        'viewParams' => [
            'stats' => $stats, // <--- Aggiungi questo 24-02-26
        ],
        'layout' => "{items}\n{pager}",
        'options' => ['class' => 'row w-100'], // w-100 assicura che la riga occupi tutto lo spazio
        'itemOptions' => ['class' =>  $itemClass], // Sempre definito!
        'pager' => [
            'options' => ['class' => 'pagination'],
            'activePageCssClass' => 'active',
        ],
    ]);
    ?>
</div>