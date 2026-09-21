<?php
setlocale(LC_TIME, 'it_IT.UTF-8');

use yii\helpers\Url;
use app\models\Agente;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\models\Cf;

$session = Yii::$app->session;
$age = Agente::find()
    ->alias('a')
    ->select(['a.[Cd_Agente] as id', 'a.[Descrizione] as Name'])
    ->innerJoin('cf c', 'c.[Cd_CF] = a.[Cd_CF_For]')
    ->where(['c.[Obsoleto]' => 0])
    ->orderBy(['a.[Descrizione]' => SORT_ASC])
    ->asArray()
    ->all();

$lstage = ArrayHelper::map($age, 'id', 'Name');
/** @var array $rows */

// raggruppa per anno e ordina anni desc (opzionale)
$grouped = [];
foreach ($rows as $r) {
    $anno = date('Y', strtotime($r['mese'] . '-01'));
    $grouped[$anno][] = $r;
}
krsort($grouped); // anni dal più recente al più vecchio
$lvl = Yii::$app->user->identity->level; // livello utente
$cd_agente = Yii::$app->user->identity->cd_agente;
//echo Url::to(['statistiche/index']);
?>

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



<div class="container mt-4">
    <h3 class="mb-4">Riepilogo Mensile</h3>
    <?php if ($lvl == 80): ?>
        <form method="get" action="/index.php" class="mb-4">
            <input type="hidden" name="r" value="statistiche/index">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="agente">Filtra per Agente:</label>
                    <?= Html::dropDownList('agente', null, $lstage, [
                        'class' => 'form-control select2',
                        'prompt' => 'Seleziona Agente',
                        'onchange' => 'this.form.submit();'
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <!-- Bottone per resettare il filtro -->
                    <?= Html::button('Reset agente', [
                        'class' => 'button-base-support button-lift',
                        'onclick' => '
        if (confirm("Vuoi resettare il filtro agente?")) {
            window.location.href = "' . \yii\helpers\Url::to(['statistiche/resetagente']) . '";
        }
    ',
                    ]) ?>


                </div>
            </div>
        </form>

    <?php endif; ?>






    <!-- Tabs -->
    <ul class="nav nav-tabs" id="yearTabs" role="tablist">
        <!-- Tab File -->
        <li class="nav-item" role="presentation">
            <a class="nav-link"
                id="tab-file-tab"
                data-bs-toggle="tab"
                data-toggle="tab"
                data-bs-target="#tab-file"
                href="#tab-file"
                role="tab"
                aria-controls="tab-file"
                aria-selected="false">
                File
            </a>
        </li>

        <?php $first = true; ?>
        <?php foreach ($grouped as $anno => $mesi): ?>
            <?php $tabId = 'tab-' . $anno; ?>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?= $first ? 'active' : '' ?>"
                    id="<?= $tabId ?>-tab"
                    data-bs-toggle="tab" data-toggle="tab"
                    data-bs-target="#<?= $tabId ?>" href="#<?= $tabId ?>"
                    role="tab" aria-controls="<?= $tabId ?>"
                    aria-selected="<?= $first ? 'true' : 'false' ?>">
                    <?= $anno ?>
                </a>
            </li>
            <?php $first = false; ?>
        <?php endforeach; ?>
    </ul>


    <!-- Tab Contents -->
    <div class="tab-content mt-3" id="yearTabsContent">
        <!-- TAB FILE (inizialmente nascosto) -->
        <div class="tab-pane fade" id="tab-file" role="tabpanel" aria-labelledby="tab-file-tab">
            <?= $this->render('_file') ?>
        </div>

        <?php $first = true; ?>
        <?php foreach ($grouped as $anno => $mesi): ?>
            <?php $tabId = 'tab-' . $anno; ?>
            <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="<?= $tabId ?>" role="tabpanel"
                aria-labelledby="<?= $tabId ?>-tab">
                <div class="row">
                    <?php foreach ($mesi as $r): ?>


                        <?php
                        // Directory dei file
                        $basePath = Yii::getAlias('@webroot/uploads/cal/');
                        $baseUrl  = Yii::getAlias('@web/uploads/cal/');

                        // Nome base del file (es: 2025-11)
                        $fileBase = $r['mese'];

                        // Lista delle estensioni possibili
                        $extensions = ['mp4', 'gif', 'png', 'jpg'];

                        $videoPath = null;

                        // Trova il primo file esistente
                        foreach ($extensions as $ext) {
                            if (file_exists($basePath . $fileBase . '.' . $ext)) {
                                $videoPath = $baseUrl . $fileBase . '.' . $ext;
                                break;
                            }
                        }
                        ?>


                        <div class="col-md-4 mb-3"> <!-- col-md-4 = 3 cards per riga -->
                            <div class="inner-card">
                                <div class="card-container">
                                    <div class="card shadow-sm border-0 hover-shadow"
                                        style="cursor:pointer"
                                        onclick="location.href=
                                        '<?= Url::to(['statistiche/dettaglio', 'mese' => $r['mese']]) ?>'">
                                        <div class="card-body">
                                            <h5><?php
                                                $fmt = new \IntlDateFormatter(
                                                    'it_IT',
                                                    \IntlDateFormatter::NONE,
                                                    \IntlDateFormatter::NONE,
                                                    'Europe/Rome',
                                                    null,
                                                    'MMMM yyyy'
                                                );

                                                $data = $fmt->format(strtotime($r['mese'] . '-01'));

                                                // Prima lettera maiuscola (gestisce anche parole accentate)
                                                echo mb_convert_case($data, MB_CASE_TITLE, "UTF-8");
                                                ?>

                                            </h5>
                                            <!-- 🎬 Video in loop -->
                                            <?php if ($videoPath !== null): ?>
                                                <?php if (pathinfo($videoPath, PATHINFO_EXTENSION) === 'mp4'): ?>
                                                    <!-- 🎬 Video -->
                                                    <video autoplay loop muted playsinline
                                                        style="width:100%; border-radius:10px; margin-bottom:10px;">
                                                        <source src="<?= $videoPath ?>" type="video/mp4">
                                                        Il tuo browser non supporta il video HTML5.
                                                    </video>
                                                <?php else: ?>
                                                    <!-- 🖼️ Immagine -->
                                                    <img src="<?= $videoPath ?>"
                                                        alt="<?= $r['mese'] ?>"
                                                        style="width:100%; border-radius:10px; margin-bottom:10px;">
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- non trovato -->
                                                <video autoplay loop muted playsinline
                                                    style="width:100%; border-radius:10px; margin-bottom:10px;">
                                                    <source src="/uploads/cal/2025-01.mp4" type="video/mp4">
                                                    Il tuo browser non supporta il video HTML5.
                                                </video>

                                            <?php endif; ?>
                                            <p><strong>Totale Ft Imponibile:</strong> <?= number_format($r['TotImponibileE'], 2, ',', '.') ?></p>
                                            <p><strong>Avere Socio:</strong> <?= number_format($r['TotProvvigione_1E'], 2, ',', '.') ?></p>
                                            <p><strong>Totale Giorni:</strong> <?= number_format($r['x_ore'], 2, ',', '.') ?></p>
                                        </div>
                                    </div>
                                </div>
                                <a href="<?= Url::to(['statistiche/dettaglio', 'mese' => $r['mese']]) ?>"
                                    class="button-base button-card-action button-lift"
                                    style="background: linear-gradient(to right, #5c85b2, #002c48); 
                                width: 100%;">Dettaglio</a>
                            </div>


                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $first = false; ?>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .hover-shadow:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        transform: scale(1.02);
        transition: 0.2s;
    }
</style>

<!-- Fallback JS: se Bootstrap non è presente mostra i pannelli manualmente -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // se Bootstrap Tab è disponibile non fare nulla (gestito da bootstrap)
        if (typeof bootstrap !== 'undefined' || typeof jQuery !== 'undefined') return;

        // fallback: usa gli anchor con href="#tab-YYYY"
        var tabs = document.querySelectorAll('#yearTabs a.nav-link');
        var panes = document.querySelectorAll('.tab-pane');

        function hideAll() {
            panes.forEach(function(p) {
                p.classList.remove('show', 'active');
            });
            tabs.forEach(function(t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
        }

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                var target = tab.getAttribute('href') || tab.dataset.target;
                if (!target) return;
                hideAll();
                var pane = document.querySelector(target);
                if (pane) pane.classList.add('show', 'active');
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
                // scroll into view (optional)
                pane && pane.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });
    });
</script>