

<style>
    .card-container {
    display: flex;
    flex-direction: column; /* Forza layout verticale */
    gap: 20px;
    align-items: stretch;
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    flex: 1 1 calc(33.33% - 20px);
    max-width: 400px;
    height: auto; /* Altezza dinamica */
    min-height: 200px; /* Altezza minima per il testo */
}

.card-content {
    flex: 2;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden; /* Evita che il contenuto ecceda la card */
    word-wrap: break-word; /* Permette di spezzare parole lunghe */
}

.card-content .card-text {
    flex-grow: 1; /* Espandi il testo per adattarlo allo spazio disponibile */
    overflow: hidden;
    text-overflow: ellipsis; /* Aggiunge "..." per i testi troppo lunghi */
    white-space: normal; /* Consente il wrapping del testo */
}

.card-image {
    width: 100%; /* L'immagine occuperà tutta la larghezza */
    height: 200px; /* Altezza fissa o come preferisci */
    object-fit: cover; /* contain  Questo farà in modo che l'immagine riempia lo spazio mantenendo le proporzioni */
    object-position: center;
    margin: 0; /* Rimuoviamo i margini */
    display: block;
    max-width: none; /* Rimuoviamo il max-width precedente */
    max-height: none; /* Rimuoviamo il max-height precedente */
}
.fatturato-banda {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background-color:rgb(123, 255, 0); /* Colore della banda */
    color: white;
    padding: 5px;
    text-align: center;
    font-weight: bold;
}

/* Adatta il layout per tablet in orizzontale */
@media (max-width: 1080px) {
    .card-container {
        flex: 1 1 calc(50% - 20px); /* Due card per riga */
    }

    .card-content {
        padding: 10px; /* Riduci il padding per dispositivi stretti */
    }

    .card-content .card-title {
        font-size: 1rem; /* Riduci la dimensione del titolo */
    }

    .card-content .card-text {
        font-size: 0.9rem; /* Riduci la dimensione del testo */
    }
}

/* Adatta il layout per dispositivi ancora più piccoli */
@media (max-width: 768px) {
    .card-container {
        flex: 1 1 calc(100% - 20px); /* Una card per riga */
    }

    .card-content .card-title {
        font-size: 0.85rem;
    }

    .card-content .card-text {
        font-size: 0.8rem;
    }
}
</style>
<style>
    /* Layout per schermi 1920x1080 */
    @media (min-width: 1920px) {
        .card-container {
            flex: 1 1 calc(25% - 20px); /* Quattro card per riga */
            max-width: 480px; /* Card più grandi */
            gap: 15px; /* Riduce lo spazio tra le card */
        }

        .card-content {
            padding: 30px; /* Maggiore padding per card più grandi */
        }

        .card-content .card-title {
            font-size: 1.25rem; /* Testo più grande per il titolo */
        }

        .card-content .card-text {
            font-size: 1rem; /* Testo più grande per il contenuto */
        }


    }
</style>



</style>

<?php yii::error($model->getUniqueSottocommesseAndDescriptions());?>
<div class="card-container-wrapper">
    <div class="card-container">
        <?php if ($model->fatturato == 1): ?>
            <div class="fatturato-banda">Fatturato</div>
        <?php endif; ?>
        <img src="<?= $model->imageFile ?? '/uploads/l_mancante.jpg' ?>" alt="Locandina" class="card-image">
        <div class="card-content">
            
            <p class="card-text">
                <b><?= $model->descrizione ?></b>
            <br>
             <div style="font-size:13px">  Sub  Commesse:<br> 
           <?= $model->getUniqueSottocommesseAndDescriptions() ?></div>
            </p>
            <a href="<?= \yii\helpers\Url::to(['masterhotel', 'id' => $model->th_id]) ?>" class="btn btn-primary">Dettagli</a>
        </div>
    </div>
</div>


<?php 
/*<h5 class="card-title">Num: <?= $model->numero ?></h5>
*/
?>