<style>
.card-container-wrapper {
    display: flex;
    flex-wrap: wrap; /* Permette alle card di andare a capo */
    gap: 20px; /* Spazio tra le card */
    justify-content: center; /* Centra le card */
}

.card-container {
    display: flex;
    gap: 20px;
    align-items: stretch; /* Assicura che tutte le card abbiano la stessa altezza */
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    flex: 1 1 calc(33.33% - 20px); /* Card flessibile: occupa un terzo dello spazio disponibile meno il gap */
    max-width: 400px; /* Imposta una larghezza massima */
    height: auto; /* Permette di adattarsi al contenuto */
}

.card-image {
    flex: 1;
    max-width: 150px;
    object-fit: scale-down;
    height: auto;
    max-height: 150px;
}

.card-content {
    flex: 2;
    padding: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.fatturato-banda {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    background-color: rgb(123, 255, 0);
    color: white;
    padding: 5px;
    text-align: center;
    font-weight: bold;
}

/* Media query per schermi più piccoli */
@media (max-width: 1200px) {
    .card-container {
        flex: 1 1 calc(50% - 20px); /* Due card per riga */
    }
    .card-image {
        max-width: 120px;
        max-height: 120px;
    }
}

@media (max-width: 768px) {
    .card-container {
        flex: 1 1 calc(100% - 20px); /* Una card per riga */
    }
    .card-image {
        max-width: 100px;
        max-height: 100px;
    }
}

</style>


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
    flex: 1;
    max-width: 150px;
    max-height: 150px;
    object-fit: contain; /* Mantiene le proporzioni dell'immagine */
    object-position: center; /* Centra l'immagine */
    align-self: center; /* Centra l'immagine nel contenitore */
    margin: auto; /* Centra ulteriormente se necessario */
    display: block; /* Assicura che l'immagine sia trattata come blocco */
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

<?php yii::error($model->getUniqueSottocommesseAndDescriptions());?>
<div class="card-container-wrapper">
    <div class="card-container">
        <?php if ($model->fatturato == 1): ?>
            <div class="fatturato-banda">Fatturato</div>
        <?php endif; ?>
        <img src="<?= $model->imageFile ?? '/uploads/l_mancante.jpg' ?>" alt="Locandina" class="card-image">
        <div class="card-content">
            <h5 class="card-title">Num: <?= $model->numero ?></h5>
            <p class="card-text">Comessa: <?= $model->getUniqueSottocommesseAndDescriptions() ?><br>
            Descrizione: <?= $model->descrizione ?></p>
            <a href="<?= \yii\helpers\Url::to(['masterhotel', 'id' => $model->th_id]) ?>" class="btn btn-primary">Dettagli</a>
        </div>
    </div>
</div>
