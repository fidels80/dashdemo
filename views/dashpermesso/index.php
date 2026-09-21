<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestione Permessi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashpermesso-index">

    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <?= Html::a('<i class="fas fa-user-shield"></i> Assegna all\'utente', ['assegna'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuova risorsa', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="alert alert-secondary">
        <i class="fas fa-info-circle"></i>
        Una <strong>risorsa</strong> corrisponde a un form/controller (es. <code>mgdocumento</code>).
        Per ogni utente si scelgono i permessi di <strong>vista / crea / modifica / elimina</strong>.
        Senza configurazione l'accesso è negato; il livello <strong>100</strong> ha sempre accesso completo.
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'codice',
            'descrizione',
            'gruppo',
            'ordine',
            ['attribute' => 'attivo', 'format' => 'boolean'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]); ?>
</div>
