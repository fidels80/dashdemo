<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $sprints app\models\Todosprint[] */
/* @var $sprintIssues array */
/* @var $backlog app\models\Todomain[] */
/* @var $tipi array */
/* @var $priorita array */
/* @var $sprintsMap array */

$this->title = 'Backlog ToDo';
$this->params['breadcrumbs'][] = ['label' => 'ToDo', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Backlog';

$renderRow = function ($issue) use ($sprintsMap) {
    $tipo = $issue->tipo ? \app\models\Todotipo::getById($issue->tipo) : null;
    $assignee = $issue->userdett->username ?? '';
    ob_start();
    ?>
    <div class="backlog-row d-flex align-items-center" data-id="<?= Html::encode($issue->id) ?>">
        <div class="backlog-type" style="color:<?= Html::encode($tipo ? ($tipo->colore ?: '#6c757d') : '#6c757d') ?>">
            <i class="fas <?= Html::encode($tipo ? ($tipo->icona ?: 'fa-tasks') : 'fa-tasks') ?>"></i>
        </div>
        <div class="backlog-key">#<?= Html::encode($issue->id) ?></div>
        <div class="backlog-title text-truncate">
            <a href="javascript:void(0);" class="backlog-open" data-url="<?= Url::to(['todomain/issue', 'id' => $issue->id]) ?>">
                <?= Html::encode($issue->descrizione) ?>
            </a>
        </div>
        <div class="backlog-meta">
            <?php if ($issue->story_points !== null && $issue->story_points !== ''): ?>
                <span class="badge badge-light" title="Story points"><?= (int) $issue->story_points ?></span>
            <?php endif; ?>
            <?php if ($issue->data_scadenza): ?>
                <span class="text-muted small"><?= date('d/m', strtotime($issue->data_scadenza)) ?></span>
            <?php endif; ?>
            <span class="badge badge-secondary"><?= Html::encode($assignee ?: '-') ?></span>
        </div>
        <div class="backlog-sprint">
            <select class="form-control form-control-sm sprint-select" data-id="<?= Html::encode($issue->id) ?>">
                <option value="">Backlog</option>
                <?php foreach ($sprintsMap as $sid => $snome): ?>
                    <option value="<?= Html::encode($sid) ?>" <?= ((string) $issue->sprint_id === (string) $sid) ? 'selected' : '' ?>>
                        <?= Html::encode($snome) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php
    return ob_get_clean();
};
?>

<div class="todomain-backlog">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h1 class="mb-2"><i class="fas fa-list-ol mr-2"></i>Backlog</h1>
        <div class="mb-2">
            <a href="<?= Url::to(['board']) ?>" class="btn btn-outline-primary"><i class="fas fa-columns"></i> Board</a>
            <a href="<?= Url::to(['index']) ?>" class="btn btn-outline-secondary"><i class="fas fa-table"></i> Lista</a>
            <button class="btn btn-success" id="btn-create-sprint"><i class="fas fa-plus"></i> Crea Sprint</button>
            <a href="<?= Url::to(['create']) ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Nuovo Task</a>
        </div>
    </div>

    <?php foreach ($sprints as $sprint): ?>
        <?php $issues = $sprintIssues[$sprint->id] ?? []; $sp = 0; foreach ($issues as $it) { $sp += (int) $it->story_points; } ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= Html::encode($sprint->nome) ?></strong>
                    <?php
                    $badge = ['attivo' => 'success', 'chiuso' => 'secondary', 'pianificato' => 'info'][$sprint->stato] ?? 'info';
                    ?>
                    <span class="badge badge-<?= $badge ?> ml-2"><?= Html::encode($sprint->stato) ?></span>
                    <?php if ($sprint->data_inizio || $sprint->data_fine): ?>
                        <small class="text-muted ml-2">
                            <?= $sprint->data_inizio ? date('d/m/Y', strtotime($sprint->data_inizio)) : '?' ?>
                            –
                            <?= $sprint->data_fine ? date('d/m/Y', strtotime($sprint->data_fine)) : '?' ?>
                        </small>
                    <?php endif; ?>
                    <small class="text-muted ml-2"><?= count($issues) ?> task · <?= $sp ?> SP</small>
                </div>
                <div>
                    <?php if ($sprint->stato !== 'attivo' && $sprint->stato !== 'chiuso'): ?>
                        <button class="btn btn-sm btn-success sprint-start" data-id="<?= $sprint->id ?>"><i class="fas fa-play"></i> Avvia</button>
                    <?php endif; ?>
                    <?php if ($sprint->stato === 'attivo'): ?>
                        <button class="btn btn-sm btn-outline-secondary sprint-close" data-id="<?= $sprint->id ?>"><i class="fas fa-check"></i> Chiudi</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-2">
                <?php if (empty($issues)): ?>
                    <p class="text-muted small mb-0 pl-2">Nessun task nello sprint.</p>
                <?php else: ?>
                    <?php foreach ($issues as $issue): ?>
                        <?= $renderRow($issue) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="card mb-3">
        <div class="card-header">
            <strong>Backlog</strong>
            <small class="text-muted ml-2"><?= count($backlog) ?> task</small>
        </div>
        <div class="card-body p-2">
            <?php if (empty($backlog)): ?>
                <p class="text-muted small mb-0 pl-2">Nessun task nel backlog.</p>
            <?php else: ?>
                <?php foreach ($backlog as $issue): ?>
                    <?= $renderRow($issue) ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
            <div class="modal-body" id="issue-modal-body"></div>
        </div>
    </div>
</div>

<style>
    .backlog-row { border-bottom: 1px solid #eef0f2; padding: 6px 8px; gap: 10px; }
    .backlog-row:hover { background: #f7f8fa; }
    .backlog-type { width: 20px; text-align: center; }
    .backlog-key { width: 70px; color: #6b778c; font-size: .8rem; font-weight: 600; }
    .backlog-title { flex: 1; min-width: 0; }
    .backlog-meta { display: flex; gap: 8px; align-items: center; }
    .backlog-sprint { width: 180px; }
</style>

<script>
(function () {
    var csrfParam = '<?= Yii::$app->request->csrfParam ?>';
    var csrfToken = '<?= Yii::$app->request->csrfToken ?>';
    var setSprintUrl = '<?= Url::to(['todomain/set-sprint']) ?>';
    var sprintCreateUrl = '<?= Url::to(['todomain/sprint-create']) ?>';
    var sprintStartUrl = '<?= Url::to(['todomain/sprint-start']) ?>';
    var sprintCloseUrl = '<?= Url::to(['todomain/sprint-close']) ?>';

    function post(url, data, done) {
        data[csrfParam] = csrfToken;
        $.post(url, data).done(done);
    }

    $(document).on('change', '.sprint-select', function () {
        post(setSprintUrl, { id: $(this).data('id'), sprint_id: $(this).val() }, function () {
            location.reload();
        });
    });

    $('#btn-create-sprint').on('click', function () {
        var nome = prompt('Nome dello sprint:');
        if (!nome) { return; }
        post(sprintCreateUrl, { nome: nome }, function (res) {
            if (res.success) { location.reload(); } else { alert(res.error || 'Errore'); }
        });
    });

    $(document).on('click', '.sprint-start', function () {
        post(sprintStartUrl + '?id=' + $(this).data('id'), {}, function () { location.reload(); });
    });

    $(document).on('click', '.sprint-close', function () {
        post(sprintCloseUrl + '?id=' + $(this).data('id'), {}, function () { location.reload(); });
    });

    $(document).on('click', '.backlog-open', function () {
        var url = $(this).data('url');
        $('#issue-modal-body').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x"></i></div>');
        $('#issue-modal').modal('show');
        $('#issue-modal-body').load(url);
    });
})();
</script>
