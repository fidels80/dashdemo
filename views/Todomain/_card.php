<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Todomain */

$tipo = $model->tipo ? \app\models\Todotipo::getById($model->tipo) : null;
$prio = $model->priorita ? \app\models\Todopriorita::findOne($model->priorita) : null;
$assignee = $model->userdett->username ?? null;
$initial = $assignee ? strtoupper(substr($assignee, 0, 1)) : '?';
$scaduta = $model->data_scadenza && strtotime($model->data_scadenza) < strtotime(date('Y-m-d'));
?>
<div class="jira-card" data-id="<?= Html::encode($model->id) ?>"
     data-url="<?= Url::to(['todomain/issue', 'id' => $model->id]) ?>">

    <div class="jira-card-top">
        <?php if ($tipo): ?>
            <span class="jira-type" style="color:<?= Html::encode($tipo->colore ?: '#6c757d') ?>"
                  title="<?= Html::encode($tipo->tipo) ?>">
                <i class="fas <?= Html::encode($tipo->icona ?: 'fa-tasks') ?>"></i>
            </span>
        <?php endif; ?>
        <span class="jira-key">#<?= Html::encode($model->id) ?></span>
        <?php if ($prio): ?>
            <span class="jira-prio" title="Priorità: <?= Html::encode($prio->priorita) ?>">
                <?= Html::encode($prio->priorita) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="jira-card-title"><?= Html::encode(mb_strimwidth((string) $model->descrizione, 0, 140, '…')) ?></div>

    <?php
    $tagList = array_filter(array_map('trim', explode(',', (string) $model->tags)));
    if (!empty($tagList)):
        ?>
        <div class="jira-tags">
            <?php foreach ($tagList as $tag): ?>
                <span class="jira-tag"><?= Html::encode($tag) ?></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="jira-card-bottom">
        <div class="jira-meta">
            <?php if ($model->story_points !== null && $model->story_points !== ''): ?>
                <span class="jira-sp" title="Story points"><i class="fas fa-gem"></i> <?= (int) $model->story_points ?></span>
            <?php endif; ?>
            <?php if ($model->data_scadenza): ?>
                <span class="jira-due <?= $scaduta ? 'text-danger' : '' ?>" title="Scadenza">
                    <i class="fas fa-calendar-day"></i> <?= date('d/m', strtotime($model->data_scadenza)) ?>
                </span>
            <?php endif; ?>
            <?php if ($model->progresso !== null && $model->progresso !== '' && (int) $model->progresso > 0): ?>
                <span class="jira-prog" title="Progresso"><i class="fas fa-tachometer-alt"></i> <?= (int) $model->progresso ?>%</span>
            <?php endif; ?>
        </div>
        <div class="jira-avatar" title="<?= Html::encode($assignee ?: 'Non assegnato') ?>">
            <?= Html::encode($initial) ?>
        </div>
    </div>
</div>
