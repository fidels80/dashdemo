<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
/*
uec_testa.id as id',
            'uec_testa.data',
            'uec_righe.prezzo as dare',
            'uec_righe.prezzo as avere',
            new Expression("'D' as dareavere"),
            new Expression("'Ricevuta' as tipomov"),
            'uec_righe.articolo as desConto'



*/
$this->title = 'Esporta Dati';
$this->params['breadcrumbs'][] = $this->title;

?>

<h1><?= Html::encode($this->title) ?></h1>

<?php if (empty($toexp)): ?>
    <p>Nessun dato disponibile per l'esportazione.</p>
<?php else: ?>
    <?= GridView::widget([
        'dataProvider' => new ArrayDataProvider([
            'allModels' => $toexp,
            'pagination' => false,
        ]),
        'columns' => [
            'id', // esempio di colonna
            'data', // sostituisci con i nomi effettivi delle colonne
            'dare',
            'avere',
            'dareavere',
            'tipomov',
            'desConto'
            // Aggiungi tutte le colonne necessarie
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::a('Esporta e Scarica Excel', ['getta'], ['class' => 'btn btn-success']) ?>
    </div>
<?php endif; ?>