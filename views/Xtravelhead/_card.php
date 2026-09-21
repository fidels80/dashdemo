<style>
    .card-stats-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
        width: 100%;
        font-family: 'Poppins', sans-serif;
        margin-top: 5px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .stat-text {
        font-size: 0.75rem;
        color: var(--text-color);
        white-space: nowrap;
    }

    .stat-text.bold {
        font-weight: 700;
    }

    .inner-card {
        width: 100%;
        height: 100%;
        /* Aggiunto per uniformare le altezze */
    }

    @media screen and (max-width: 1366px) {
        .stat-text {
            font-size: 0.65rem;
        }
    }

    @media screen and (max-width: 768px) {
        .stat-text {
            font-size: 0.6rem;
        }
    }
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $model app\models\Xtravelhead */
/** @var $stats array Array dei dati pre-calcolati dal controller */

// 1. Recupero dati pre-calcolati (evita le query N+1 lente)
$myStats = $stats[$model->th_id] ?? [];

// Importi calcolati tramite la funzione SQL pesante nel controller
$gppth = $myStats['imp_hotel'] ?? 0;
$gpptv = $myStats['imp_viaggi'] ?? 0;

// Query leggere (o da ottimizzare in seguito se necessario)
$gpdh  = $model->getPagamentiDocumentihotel();
$gpdv  = $model->getPagamentiDocumentiviaggi();
$gpd   = $model->getPagamentiDocumenti();

// 2. Formattazione valori
$fmt_imp_hotel  = number_format($gppth, 2, ',', '.');
$fmt_acc_hotel  = number_format($gpdh, 2, ',', '.');
$fmt_imp_viaggi = number_format($gpptv, 2, ',', '.');
$fmt_acc_viaggi = number_format($gpdv, 2, ',', '.');
$fmt_fatturato  = number_format((float)$gpd, 2, ',', '.');
$fmt_da_fatt    = number_format((float)($gppth + $gpptv) - $gpd, 2, ',', '.');

// Per le sub-commesse (se usate)
$guscad = $model->x_tiposhow == 2 ? "..." : ""; // Qui andrebbe la tua logica per guscad se definita
?>



<div class="card-container h-100" data-cliente="<?= Html::encode($model->x_cfdesk) ?>" style="width: 100%;">
    <div class="inner-card d-flex flex-column" style="background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden;">

        <div style="display: flex; flex-direction: column; height: 100%;">

            <?php if ($model->fatturato == 1): ?>
                <div class="fatturato-banda">Fatturato</div>
            <?php endif; ?>

            <div style="width: 100%; height: 200px; overflow: hidden;">
                <img src="<?= $model->imageFile ?? '/uploads/l_mancante.jpg' ?>" alt="Locandina" class="card-image" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div class="content d-flex flex-column flex-grow-1" style="padding: 15px;">
                <div class="flex-grow-1">
                    <b style="font-size: 1.1em; display: block; margin-bottom: 10px;"><?= Html::encode($model->descrizione) ?></b>

                    <div class="sub-commesse-label">
                        <?php if ((Yii::$app->user->identity->level ?? 0) >= 0): ?>

                            <div class="card-stats-container">
                                <div class="stat-row">
                                    <span class="stat-text">
                                        Ultima data <?= Yii::$app->formatter->asDate($model->x_maxdata, 'php:d/m/Y') ?>
                                    </span>
                                </div>

                                <div class="stat-row">
                                    <span class="stat-text">
                                        Importo Hotel: € <?= $fmt_imp_hotel ?>
                                    </span>
                                    <span class="stat-text bold">
                                        Acconti Hotel: € <?= $fmt_acc_hotel ?>
                                    </span>
                                </div>

                                <div class="stat-row">
                                    <span class="stat-text">
                                        Importo Viaggi: € <?= $fmt_imp_viaggi ?>
                                    </span>
                                    <span class="stat-text bold">
                                        Acconti Viaggi: € <?= $fmt_acc_viaggi ?>
                                    </span>
                                </div>

                                <div class="stat-row">
                                    <span class="stat-text bold">
                                        Fatturato: € <?= $fmt_fatturato ?>
                                    </span>
                                    <span class="stat-text bold">
                                        Da Fatturare: <span class="stat-text bold">€ <?= $fmt_da_fatt ?></span>
                                    </span>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>

                    <?php if ($model->x_tiposhow == 2 && !empty($guscad)): ?>
                        <p class="sub-commesse-label" style="margin-top: 10px;">
                        <div style="font-size:10px"> Sub Commesse:<br>
                            <?= $guscad ?>
                        </div>
                        </p>
                    <?php endif; ?>
                </div>

                <div style="margin-top: 15px;">
                    <a href="<?= Url::to(['masterhotel', 'id' => $model->th_id]) ?>"
                        class="button-base button-card-action button-lift"
                        style="background: linear-gradient(to right, #5c85b2, #002c48); width: 100%; display: block; text-align: center; color: white; padding: 8px; border-radius: 4px; text-decoration: none;">
                        Dettagli
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>