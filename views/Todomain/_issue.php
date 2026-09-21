<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Todomain */
/* @var $comments app\models\Todocommenti[] */
/* @var $newComment app\models\Todocommenti */
/* @var $tipi array */
/* @var $stati array */
/* @var $priorita array */
/* @var $sprints array */
/* @var $users array */

$tipo = $model->tipo ? \app\models\Todotipo::getById($model->tipo) : null;
?>

<div class="issue-detail" data-id="<?= Html::encode($model->id) ?>">

    <div class="d-flex align-items-center mb-2" style="gap:10px;">
        <?php if ($tipo): ?>
            <span style="color:<?= Html::encode($tipo->colore ?: '#6c757d') ?>">
                <i class="fas <?= Html::encode($tipo->icona ?: 'fa-tasks') ?>"></i>
                <strong><?= Html::encode($tipo->tipo) ?></strong>
            </span>
        <?php endif; ?>
        <span class="text-muted">#<?= Html::encode($model->id) ?></span>
    </div>

    <h4><?= Html::encode($model->descrizione) ?></h4>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Stato</label>
                <select class="form-control issue-quick" data-field="stato">
                    <?php foreach ($stati as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) $model->stato === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Assegnatario</label>
                <select class="form-control issue-quick" data-field="user">
                    <option value="">Non assegnato</option>
                    <?php foreach ($users as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) $model->user === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Priorità</label>
                <select class="form-control issue-quick" data-field="priorita">
                    <option value="">-</option>
                    <?php foreach ($priorita as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) $model->priorita === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Tipo</label>
                <select class="form-control issue-quick" data-field="tipo">
                    <option value="">-</option>
                    <?php foreach ($tipi as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) $model->tipo === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Sprint</label>
                <select class="form-control issue-quick" data-field="sprint_id">
                    <option value="">Backlog</option>
                    <?php foreach ($sprints as $id => $nome): ?>
                        <option value="<?= Html::encode($id) ?>" <?= ((string) $model->sprint_id === (string) $id) ? 'selected' : '' ?>>
                            <?= Html::encode($nome) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Story points</label>
                <input type="number" class="form-control issue-quick" data-field="story_points"
                       value="<?= Html::encode($model->story_points) ?>" min="0">
            </div>
            <div class="form-group">
                <label>Progresso (%)</label>
                <input type="number" class="form-control issue-quick" data-field="progresso"
                       value="<?= Html::encode($model->progresso) ?>" min="0" max="100">
            </div>
            <div class="form-group">
                <label>Data scadenza</label>
                <input type="text" class="form-control issue-quick" data-field="data_scadenza"
                       value="<?= Html::encode($model->data_scadenza) ?>" placeholder="AAAA-MM-GG">
            </div>
        </div>
    </div>

    <div class="text-muted small mb-3">
        Segnalato da <strong><?= Html::encode($model->reporter ?: 'N/A') ?></strong>
        <?php if ($model->created_at): ?> · creato il <?= date('d/m/Y H:i', strtotime($model->created_at)) ?><?php endif; ?>
        <?php if ($model->updated_at): ?> · aggiornato il <?= date('d/m/Y H:i', strtotime($model->updated_at)) ?><?php endif; ?>
        <?php if ($model->data_scadenza): ?> · scadenza <?= date('d/m/Y', strtotime($model->data_scadenza)) ?><?php endif; ?>
    </div>

    <hr>

    <h6><i class="fas fa-comments mr-1"></i> Commenti</h6>
    <div id="issue-comments">
        <?php if (empty($comments)): ?>
            <p class="text-muted small">Nessun commento.</p>
        <?php else: ?>
            <?php foreach ($comments as $c): ?>
                <div class="issue-comment">
                    <div class="issue-comment-head">
                        <strong><?= Html::encode($c->user) ?></strong>
                        <small class="text-muted"><?= $c->data ? date('d/m/Y H:i', strtotime($c->data)) : '' ?></small>
                    </div>
                    <div class="issue-comment-body"><?= $c->commento ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="input-group mt-2">
        <textarea class="form-control" id="issue-comment-text" rows="2" placeholder="Scrivi un commento..."></textarea>
        <div class="input-group-append">
            <button class="btn btn-primary" id="issue-comment-btn" type="button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <hr>

    <h6><i class="fas fa-history mr-1"></i> Cronologia</h6>
    <div id="issue-activity">
        <?= $this->render('_activity', ['model' => $model]) ?>
    </div>

    <div class="mt-3 text-right">
        <?= Html::a('<i class="fas fa-pen"></i> Modifica completa', ['update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
    </div>
</div>

<style>
    .issue-comment { border-left: 3px solid #dfe1e6; padding: 6px 10px; margin-bottom: 8px; background: #fafbfc; border-radius: 4px; }
    .issue-comment-head { display: flex; justify-content: space-between; font-size: .82rem; }
    .issue-comment-body { font-size: .9rem; margin-top: 3px; }
    .activity-item { font-size: .82rem; color: #44546f; padding: 3px 0; border-bottom: 1px dashed #eef0f2; }
</style>

<script>
(function () {
    var root = document.querySelector('.issue-detail');
    if (!root) { return; }
    var id = root.getAttribute('data-id');
    var quickUrl = '<?= Url::to(['todomain/quick-update']) ?>';
    var commentUrl = '<?= Url::to(['todomain/comment', 'id' => $model->id]) ?>';
    var csrfParam = '<?= Yii::$app->request->csrfParam ?>';
    var csrfToken = '<?= Yii::$app->request->csrfToken ?>';

    $(document).off('change', '.issue-quick').on('change', '.issue-quick', function () {
        var field = $(this).data('field');
        var value = $(this).val();
        var data = { id: id };
        data[field] = value;
        data[csrfParam] = csrfToken;
        $.post(quickUrl, data).done(function (res) {
            if (!res.success) { alert(res.error || 'Errore di aggiornamento'); }
        });
    });

    $(document).off('click', '#issue-comment-btn').on('click', '#issue-comment-btn', function () {
        var text = $('#issue-comment-text').val().trim();
        if (!text) { return; }
        var data = { commento: text };
        data[csrfParam] = csrfToken;
        $.post(commentUrl, data).done(function (res) {
            if (res.success) {
                $('#issue-comment-text').val('');
                var url = '<?= Url::to(['todomain/issue', 'id' => $model->id]) ?>';
                $('#issue-modal-body').load(url);
            } else {
                alert(res.error || 'Errore');
            }
        });
    });
})();
</script>
