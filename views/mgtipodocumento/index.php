<?php

use yii\helpers\Html;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipi documento';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgtipodocumento-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo tipo documento', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <table id="mgtipodocumento-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Codice</th>
            <th>Descrizione</th>
            <th>Destinazione</th>
            <th>Magazzino partenza</th>
            <th>Magazzino arrivo</th>
            <th>Movimento</th>
            <th>Anno</th>
            <th>Contatore</th>
            <th>Numerazione auto</th>
            <th>Congruità numeri</th>
            <th>Crea scadenze</th>
            <th>Taglia/colore</th>
            <th>Preleva rapportini</th>
            <th>Crea articoli</th>
            <th>Crea anagrafiche</th>
            <th>Matrice taglie</th>
            <th>Gestione seriali</th>
            <th>Gestione data consegna</th>
            <th>Gestione lotti</th>
            <th>Varia impegnato</th>
            <th>Varia ordinato</th>
            <th>Attivo</th>
            <th>Creato il</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->codice) ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode($m->destinazioneLabel) ?></td>
                <td><?= Html::encode($m->magazzinoPartenzaLabel) ?></td>
                <td><?= Html::encode($m->magazzinoArrivoLabel) ?></td>
                <td><?= Html::encode($m->segnoMovimentoLabel) ?></td>
                <td><?= (int) $m->anno ?></td>
                <td><?= (int) $m->contatore ?></td>
                <td><?= $m->usa_progressivo ? 'Sì' : 'No' ?></td>
                <td><?= $m->congruita ? 'Sì' : 'No' ?></td>
                <td><?= $m->crea_scadenze ? 'Sì' : 'No' ?></td>
                <td><?= $m->mostra_varianti ? 'Sì' : 'No' ?></td>
                <td><?= $m->preleva_rapportini ? 'Sì' : 'No' ?></td>
                <td><?= $m->crea_articoli ? 'Sì' : 'No' ?></td>
                <td><?= $m->crea_anagrafiche ? 'Sì' : 'No' ?></td>
                <td><?= $m->mostra_matrice ? 'Sì' : 'No' ?></td>
                <td><?= $m->gestione_seriali ? 'Sì' : 'No' ?></td>
                <td><?= $m->gestione_data_consegna ? 'Sì' : 'No' ?></td>
                <td><?= $m->gestione_lotti ? 'Sì' : 'No' ?></td>
                <td><?= Html::encode($m->variaImpegnatoLabel) ?></td>
                <td><?= Html::encode($m->variaOrdinatoLabel) ?></td>
                <td><?= $m->attivo ? 'Sì' : 'No' ?></td>
                <td><?= $m->created_at ? date('d/m/Y H:i', strtotime($m->created_at)) : '' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    <?= Html::a('<i class="fas fa-pen"></i>', ['update', 'id' => $m->id], ['class' => 'btn btn-sm btn-warning', 'title' => 'Modifica']) ?>
                    <?= Html::a('<i class="fas fa-copy"></i>', ['create', 'from' => $m->id], ['class' => 'btn btn-sm btn-secondary', 'title' => 'Duplica']) ?>
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Elimina',
                        'data' => ['confirm' => 'Eliminare questo tipo documento?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('mgtipodocumento-table', 1, 'asc'); ?>
