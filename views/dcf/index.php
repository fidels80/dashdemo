<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $documenti array */
/* @var $fornitori array */
/* @var $filtri array */
/* @var $batch array */

$this->title = 'Controllo documenti fornitore';
$this->params['breadcrumbs'][] = $this->title;

$batchSet = array_flip(array_map('intval', $batch));
?>
<div class="dcf-index">

    <form id="dcf-selezione-form" method="post" action="<?= Url::to(['selezione']) ?>">
        <?= Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->csrfToken) ?>
    </form>

    <div class="card p-3 shadow-sm mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h4 class="mb-1"><i class="fas fa-truck"></i> Controllo documenti fornitore</h4>
                <div class="text-muted small">
                    Passo 1 di 3 — scegli uno o piu' documenti (anche di fornitori diversi) e avvia il riscontro quantita'.
                </div>
            </div>
            <?php if (!empty($batch)): ?>
                <a href="<?= Url::to(['controlla']) ?>" class="btn btn-warning">
                    <i class="fas fa-play"></i> Riprendi controllo in corso (<?= count($batch) ?>)
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card p-3 shadow-sm mb-3">
        <form method="get" class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2">
                <label class="small mb-1">Fornitore</label>
                <?= Html::dropDownList('id_anagrafica', $filtri['id_anagrafica'], $fornitori, [
                    'class' => 'form-control',
                    'prompt' => 'Tutti i fornitori',
                ]) ?>
            </div>
            <div class="form-group col-md-5 mb-2">
                <label class="small mb-1">Cerca</label>
                <input type="text" name="q" class="form-control" value="<?= Html::encode($filtri['q']) ?>"
                       placeholder="Numero, data o descrizione...">
            </div>
            <div class="form-group col-md-3 mb-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtra</button>
                <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary">Azzera</a>
            </div>
        </form>
    </div>

    <div class="card p-3 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <div class="small text-muted">
                <i class="fas fa-info-circle"></i>
                Sono elencati solo i documenti <strong>ancora da controllare</strong>.
            </div>
            <button type="submit" form="dcf-selezione-form" class="btn btn-success">
                <i class="fas fa-play"></i> Avvia controllo (selezionati)
            </button>
        </div>

        <table id="dcf-index-table" class="table table-striped table-bordered" style="width:100%">
            <thead>
            <tr>
                <th class="no-export text-center" style="width:36px">
                    <input type="checkbox" id="dcf-check-all" title="Seleziona tutti">
                </th>
                <th>Documento</th>
                <th>Data</th>
                <th>Fornitore</th>
                <th class="text-right">Righe</th>
                <th class="text-right">Totale pezzi</th>
                <th class="text-right">Totale documento</th>
                <th>Stato</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($documenti as $row): ?>
                <?php $d = $row['model']; ?>
                <tr>
                    <td class="text-center no-export">
                        <input type="checkbox" class="dcf-check" form="dcf-selezione-form" name="docs[]"
                               value="<?= (int) $d->id ?>"
                            <?= isset($batchSet[(int) $d->id]) ? 'checked' : '' ?>>
                    </td>
                    <td><?= Html::encode($d->etichetta) ?></td>
                    <td><?= $d->data ? date('d/m/Y', strtotime($d->data)) : '' ?></td>
                    <td><?= Html::encode($row['fornitore']) ?></td>
                    <td class="text-right"><?= (int) $row['n_righe'] ?></td>
                    <td class="text-right"><?= number_format($row['tot_pezzi'], 2, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['tot_documento'], 2, ',', '.') ?></td>
                    <td><span class="badge badge-<?= $row['stato_badge'] ?>"><?= Html::encode($row['stato_label']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (empty($documenti)): ?>
            <div class="alert alert-info mb-0">
                Nessun documento da fornitore da controllare: tutti i documenti risultano gia' controllati.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var all = document.getElementById('dcf-check-all');
    if (all) {
        all.addEventListener('change', function () {
            document.querySelectorAll('input.dcf-check').forEach(function (c) { c.checked = all.checked; });
        });
    }
    var form = document.getElementById('dcf-selezione-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            var n = document.querySelectorAll('input.dcf-check:checked').length;
            if (n === 0) {
                e.preventDefault();
                alert('Seleziona almeno un documento da controllare.');
            }
        });
    }
})();
</script>
<?php DataTables::render('dcf-index-table', 1, 'asc'); ?>
