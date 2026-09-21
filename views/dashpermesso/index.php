<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Gestione Permessi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashpermesso-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-user-shield"></i> Assegna all\'utente', ['assegna'], ['class' => 'btn btn-outline-primary']) ?>
            <?= Html::a('<i class="fas fa-plus"></i> Nuova risorsa', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="alert alert-secondary mb-3">
        <i class="fas fa-info-circle"></i>
        Una <strong>risorsa</strong> corrisponde a un form/funzione. Per ogni utente si scelgono i permessi di
        <strong>vista / crea / modifica / elimina</strong>. Senza configurazione l'accesso è negato;
        il livello <strong>100</strong> e gli utenti super hanno sempre accesso completo.
    </div>

    <table id="dashpermesso-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Gruppo</th>
            <th>Ordine</th>
            <th>Attivo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode($m->gruppo) ?></td>
                <td><?= (int) $m->ordine ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questa risorsa?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('dashpermesso-table', 4, 'asc'); ?>
