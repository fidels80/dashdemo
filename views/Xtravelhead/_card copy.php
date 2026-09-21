    <style>
        /* Container principale */
        .card-stats-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
            /* Spazio verticale tra le righe */
            width: 100%;
            font-family: 'Poppins', sans-serif;
            margin-top: 5px;
        }

        /* La singola riga che contiene Sinistra e Destra */
        .stat-row {
            display: flex;
            justify-content: space-between;
            /* Spinge il primo elemento a SX e il secondo a DX */
            align-items: center;
            width: 100%;
        }

        /* Stile del testo (Etichetta + Valore insieme) */
        .stat-text {
            font-size: 0.75rem;
            /* Base piccola */
            color: var(--text-color);
            white-space: nowrap;
            /* IMPEDISCE DI ANDARE A CAPO */
        }

        /* Grassetto per acconti e fatturato */
        .stat-text.bold {
            font-weight: 700;
        }

        /* Colore evidenza per Da Fatturare */
        .warning-text {
            color: #d63384;
            /* O usa un colore rosso/arancio */
        }

        .stat-divider {
            border: 0;
            border-top: 1px solid #eee;
            margin: 2px 0;
            width: 100%;
        }

        .inner-card {
            width: 100%;
        }

        /* Assicurati che l'interno occupi tutto il wrapper */
        /* ==============================================
   FIX PER RISOLUZIONE 1080 (Tablet/Card Strette)
   ============================================== */
        /* Quando lo schermo è stretto o la card si riduce, riduciamo il font 
   invece di mandare a capo il testo */
        @media screen and (max-width: 1366px) {
            .stat-text {
                font-size: 0.65rem;
                /* Font più piccolo per farli stare su una riga */
            }
        }

        /* Se molto piccolo */
        @media screen and (max-width: 768px) {
            .stat-text {
                font-size: 0.6rem;
            }
        }

        .inner-card-box {
            width: 100%
        }
    </style>
    <div class="card-container" data-cliente="<?= ($model->x_cfdesk) ?>" style="width: 100%;">

        <div class="inner-card">
            <div class="card-container">
                <?php if ($model->fatturato == 1): ?>
                    <div class="fatturato-banda">Fatturato</div>
                <?php endif; ?>
                <img src="<?= $model->imageFile ?? '/uploads/l_mancante.jpg' ?>" alt="Locandina" class="card-image">
                <div class="content">


                    <b><?= $model->descrizione . ' '  ?></b>
                    <br>
                    <div class="sub-commesse-label">
                        <?php if ((Yii::$app->user->identity->level ?? 0) >= 0): ?>

                            <?php
                            // Calcoli dati
                            $gppth  = $model->getPagamentiPerTipohotel();
                            $gpdh   = $model->getPagamentiDocumentihotel();
                            $gpptv  = $model->getPagamentiPerTipoviaggi();
                            $gpdv   = $model->getPagamentiDocumentiviaggi();
                            $gpd    = $model->getPagamentiDocumenti();

                            // Formattazione
                            $fmt_imp_hotel  = number_format($gppth, 2, ',', '.');
                            $fmt_acc_hotel  = number_format($gpdh, 2, ',', '.');
                            $fmt_imp_viaggi = number_format($gpptv, 2, ',', '.');
                            $fmt_acc_viaggi = number_format($gpdv, 2, ',', '.');
                            $fmt_fatturato  = number_format((float)$gpd, 2, ',', '.');
                            $fmt_da_fatt    = number_format((float)($gppth + $gpptv) - $gpd, 2, ',', '.');
                            ?>

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
                    <p class="sub-commesse-label">
                        <?php if ($model->x_tiposhow == 2 and !empty($guscad)): ?>
                    <div style="font-size:10px"> Sub Commesse:<br>
                        <?= $guscad ?></div>
                <?php endif; ?>
                </p>
                <a href="<?= \yii\helpers\Url::to(['masterhotel', 'id' => $model->th_id]) ?>" class="button-base button-card-action button-lift"
                    style="background: linear-gradient(to right, #5c85b2, #002c48); width: 100%;">Dettagli</a>
                </div>
            </div>
        </div>


 

    </div>