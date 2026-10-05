<?php

use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\web\View;
use app\components\DataTables;
use app\models\MgAnagrafica;
use app\models\MgSottocommessa;
use app\models\Rapportini;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RapportiniSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Rapportini';
$this->params['breadcrumbs'][] = $this->title;

// Registrato per primo: gli asset DataTables (che includono una Select2 da CDN)
// devono precedere quelli di kartik Select2 usati nella modale, altrimenti
// quest'ultima sovrascrive il tema krajee-bs4.
DataTables::render('rapportini-table', 1, 'desc');

$models = $dataProvider->getModels();

$codiciCli = [];
$codiciCommessa = [];
foreach ($models as $m) {
    if ($m->cd_cli !== null && $m->cd_cli !== '') {
        $codiciCli[$m->cd_cli] = $m->cd_cli;
    }
    if ($m->commessa !== null && $m->commessa !== '') {
        $codiciCommessa[$m->commessa] = $m->commessa;
    }
}

$clienti = MgAnagrafica::find()
    ->select(['codice', 'ragione_sociale'])
    ->where(['codice' => array_values($codiciCli)])
    ->indexBy('codice')
    ->column();
$commesse = MgSottocommessa::find()
    ->select(['codice', 'descrizione'])
    ->where(['codice' => array_values($codiciCommessa)])
    ->indexBy('codice')
    ->column();
?>

<div class="rapportini-index card p-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::button('<i class="fas fa-plus"></i> Inserisci rapportino', [
                'id' => 'btn-inserisci-rapportino',
                'class' => 'btn btn-success',
                'data-toggle' => 'modal',
                'data-target' => '#rapportini-modal',
            ]) ?>
        </div>
    </div>

    <table id="rapportini-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>Numero</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Sottocommessa</th>
                <th>Articolo</th>
                <th>Qta</th>
                <th>Ora in</th>
                <th>Ora out</th>
                <th>Note</th>
                <th class="no-export">Azioni</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($models as $m): ?>
                <tr>
                    <td><?= (int) $m->numero ?></td>
                    <td><?= Html::encode(Yii::$app->formatter->asDate($m->data, 'php:d/m/Y')) ?></td>

                    <td><?= Html::encode($clienti[$m->cd_cli] ?? $m->cd_cli) ?></td>
                    <td><?= Html::encode($commesse[$m->commessa] ?? $m->commessa) ?></td>
                    <td><?= Html::encode(trim(($m->cd_art ?? '') . ' ' . ($m->des_art ?? ''))) ?></td>
                    <td><?= Html::encode($m->qta) ?></td>
                    <td><?= $m->ora_in ? Html::encode(Yii::$app->formatter->asTime((string) $m->ora_in, 'php:H:i')) : '' ?></td>
                    <td><?= $m->ora_out ? Html::encode(Yii::$app->formatter->asTime((string) $m->ora_out, 'php:H:i')) : '' ?></td>
                    <td><?= Html::encode(mb_strimwidth((string) $m->note, 0, 60, '…', 'UTF-8')) ?></td>
                    <td class="text-center text-nowrap no-export">
                        <?= Html::a('<i class="fas fa-eye"></i>', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-info', 'title' => 'Vedi']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
Modal::begin([
    'id' => 'rapportini-modal',
    'title' => 'Inserisci rapportino',
    'size' => 'modal-lg',
    'options' => ['tabindex' => false],
]);
echo $this->render('createaj', ['model' => new Rapportini()]);
Modal::end();
?>
