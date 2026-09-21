<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $stati app\models\Todostato[] */
/* @var $grouped array */
/* @var $senzaStato array */
/* @var $filters array */
/* @var $users array */
/* @var $gruppi array */
/* @var $tipi array */
/* @var $priorita array */
/* @var $sprints array */

$this->title = 'Board ToDo';
$this->params['breadcrumbs'][] = ['label' => 'ToDo', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Board';

$palette = ['#0d6efd', '#fd7e14', '#6f42c1', '#20c997', '#dc3545', '#198754', '#0dcaf0', '#ffc107'];
?>

<div class="todomain-board">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mb-2"><i class="fas fa-columns mr-2"></i>Board ToDo</h1>
        <div class="mb-2">
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary"><i class="fas fa-table"></i> Lista</a>
            <a href="<?= Url::to(['backlog']) ?>" class="btn btn-outline-primary"><i class="fas fa-list-ol"></i> Backlog</a>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-success"><i class="fas fa-plus"></i> Nuovo Task</a>
        </div>
    </div>

    <form method="get" action="<?= Url::to(['board']) ?>" class="card card-body mb-3">
        <div class="row">
            <div class="col-md-3 mb-2">
                <input type="text" name="q" class="form-control" placeholder="Cerca (id, descrizione, tag)..."
                       value="<?= Html::encode($filters['q'] ?? '') ?>">
            </div>
            <div class="col-md-2 mb-2">
                <select name="user" class="form-control">
                    <option value="">Assegnatario...</option>
                    <?php foreach ($users as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) ($filters['user'] ?? '') === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="sprint_id" class="form-control">
                    <option value="">Sprint...</option>
                    <option value="0" <?= ((string) ($filters['sprint_id'] ?? '') === '0') ? 'selected' : '' ?>>Backlog (nessuno)</option>
                    <?php foreach ($sprints as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) ($filters['sprint_id'] ?? '') === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="tipo" class="form-control">
                    <option value="">Tipo...</option>
                    <?php foreach ($tipi as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) ($filters['tipo'] ?? '') === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="priorita" class="form-control">
                    <option value="">Priorità...</option>
                    <?php foreach ($priorita as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) ($filters['priorita'] ?? '') === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 mb-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i></button>
            </div>
        </div>
        <?php if (!empty(array_filter($filters))): ?>
            <div class="mt-1">
                <a href="<?= Url::to(['board']) ?>" class="text-muted small"><i class="fas fa-times"></i> Svuota filtri</a>
            </div>
        <?php endif; ?>
    </form>

    <div class="jira-board" id="jira-board">
        <?php $i = 0; foreach ($stati as $stato): ?>
            <?php $sid = (string) $stato->id; ?>
            <div class="jira-col" data-stato="<?= Html::encode($sid) ?>">
                <div class="jira-col-header" style="border-top:3px solid <?= $palette[$i % count($palette)] ?>">
                    <span class="jira-col-title"><?= Html::encode($stato->stato) ?></span>
                    <span class="jira-col-count"><?= count($grouped[$sid] ?? []) ?></span>
                </div>
                <div class="jira-col-body" data-stato="<?= Html::encode($sid) ?>">
                    <?php foreach (($grouped[$sid] ?? []) as $issue): ?>
                        <?= $this->render('_card', ['model' => $issue]) ?>
                    <?php endforeach; ?>
                </div>
                <a href="<?= Url::to(['create', 'stato' => $sid]) ?>" class="jira-add">
                    <i class="fas fa-plus"></i> Aggiungi
                </a>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>

        <?php if (!empty($senzaStato)): ?>
            <div class="jira-col" data-stato="0">
                <div class="jira-col-header" style="border-top:3px solid #adb5bd">
                    <span class="jira-col-title">Senza stato</span>
                    <span class="jira-col-count"><?= count($senzaStato) ?></span>
                </div>
                <div class="jira-col-body" data-stato="0">
                    <?php foreach ($senzaStato as $issue): ?>
                        <?= $this->render('_card', ['model' => $issue]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modale dettaglio issue -->
<div class="modal fade" id="issue-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clipboard-list mr-2"></i>Dettaglio Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body" id="issue-modal-body">
                <div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>
            </div>
        </div>
    </div>
</div>

<style>
    .jira-board { display: flex; gap: 14px; overflow-x: auto; align-items: flex-start; padding-bottom: 16px; }
    .jira-col { min-width: 280px; width: 280px; background: #f1f2f4; border-radius: 10px; padding: 8px; }
    .jira-col-header { display: flex; justify-content: space-between; align-items: center; padding: 6px 8px 10px; }
    .jira-col-title { font-weight: 600; color: #44546f; text-transform: uppercase; font-size: .78rem; letter-spacing: .04em; }
    .jira-col-count { background: #dfe1e6; color: #44546f; border-radius: 10px; padding: 1px 8px; font-size: .75rem; }
    .jira-col-body { min-height: 50px; }
    .jira-card { background: #fff; border-radius: 8px; padding: 10px; margin-bottom: 8px; box-shadow: 0 1px 2px rgba(9,30,66,.25); cursor: pointer; border-left: 4px solid #dfe1e6; transition: box-shadow .15s; }
    .jira-card:hover { box-shadow: 0 3px 8px rgba(9,30,66,.3); }
    .jira-card-top { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: .78rem; }
    .jira-key { color: #6b778c; font-weight: 600; }
    .jira-prio { background: #fff3cd; color: #856404; border-radius: 4px; padding: 0 6px; font-size: .7rem; }
    .jira-card-title { font-size: .9rem; color: #172b4d; margin-bottom: 8px; line-height: 1.35; }
    .jira-tags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 8px; }
    .jira-tag { background: #e9f2ff; color: #0052cc; border-radius: 4px; padding: 1px 7px; font-size: .68rem; }
    .jira-card-bottom { display: flex; justify-content: space-between; align-items: center; }
    .jira-meta { display: flex; gap: 10px; color: #6b778c; font-size: .75rem; align-items: center; }
    .jira-avatar { width: 26px; height: 26px; border-radius: 50%; background: #0052cc; color: #fff; display: flex; align-items: center; justify-content: center; font-size: .75rem; font-weight: 600; }
    .jira-add { display: block; text-align: center; color: #6b778c; font-size: .82rem; padding: 6px; text-decoration: none; }
    .jira-add:hover { background: #e2e4e9; border-radius: 6px; color: #172b4d; text-decoration: none; }
    .jira-sortable-ghost { opacity: .4; }
</style>

<?php
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js', ['position' => \yii\web\View::POS_END]);

$moveUrl = Url::to(['todomain/move']);
$csrf = Yii::$app->request->csrfParam;
$token = Yii::$app->request->csrfToken;

$js = <<<JS
(function () {
    if (typeof Sortable === 'undefined') { return; }
    var moveUrl = '{$moveUrl}';
    var csrfParam = '{$csrf}';
    var csrfToken = '{$token}';

    document.querySelectorAll('.jira-col-body').forEach(function (el) {
        Sortable.create(el, {
            group: 'jira-board',
            animation: 150,
            ghostClass: 'jira-sortable-ghost',
            onEnd: function (evt) {
                var card = evt.item;
                var id = card.getAttribute('data-id');
                var stato = evt.to.getAttribute('data-stato');
                var ids = Array.prototype.map.call(evt.to.querySelectorAll('.jira-card'), function (c) {
                    return c.getAttribute('data-id');
                }).join(',');

                var data = { id: id, stato: stato, ordine: ids };
                data[csrfParam] = csrfToken;

                $.ajax({
                    url: moveUrl,
                    type: 'POST',
                    data: data,
                    success: function (res) {
                        if (!res.success) { alert(res.error || 'Errore durante lo spostamento'); }
                    },
                    error: function () { alert('Errore di rete durante lo spostamento'); }
                });
            }
        });
    });

    // Apertura modale dettaglio
    $(document).on('click', '.jira-card', function () {
        var url = $(this).data('url');
        $('#issue-modal-body').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $('#issue-modal').modal('show');
        $('#issue-modal-body').load(url);
    });
})();
JS;

$this->registerJs($js);
?>
