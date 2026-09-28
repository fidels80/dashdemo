<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\DashChangelog */

$this->title = $model->titolo;
$this->params['breadcrumbs'][] = ['label' => 'Changelog', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashchangelog-view">

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h1 class="h4 mb-1"><?= Html::encode($model->titolo) ?></h1>
                    <p class="text-muted mb-0">
                        <?php if ($model->versione): ?>
                            <span class="badge badge-primary">v<?= Html::encode(ltrim($model->versione, 'v')) ?></span>
                        <?php endif; ?>
                        <span class="badge badge-info"><?= Html::encode($model->tipoLabel) ?></span>
                        <?php if ($model->data_commit): ?>
                            <span class="ml-2"><i class="fas fa-calendar"></i> <?= Html::encode(date('d-m-Y H:i', strtotime($model->data_commit))) ?></span>
                        <?php endif; ?>
                        <?php if ($model->autore): ?>
                            <span class="ml-2"><i class="fas fa-user"></i> <?= Html::encode($model->autore) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <?= Html::a('<i class="fas fa-arrow-left"></i> Torna all\'elenco', ['index'], ['class' => 'btn btn-secondary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header"><strong>Cosa è stato modificato e in che modo</strong></div>
        <div class="card-body">
            <?php if (!empty($model->dettaglio)): ?>
                <div><?= nl2br(Html::encode($model->dettaglio)) ?></div>
            <?php else: ?>
                <p class="text-muted mb-0">Nessun dettaglio descritto.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header"><strong>File modificati</strong></div>
                <div class="card-body">
                    <?php $files = $model->getFileList(); ?>
                    <?php if (!empty($files)): ?>
                        <ul class="mb-0" style="font-family:'Courier New',monospace;font-size:.85rem;">
                            <?php foreach ($files as $f): ?>
                                <li><?= Html::encode($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nessun file registrato.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header"><strong>Riferimenti</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Commit</dt>
                        <dd class="col-7">
                            <?php if ($model->commit_hash): ?>
                                <code><?= Html::encode(substr($model->commit_hash, 0, 10)) ?></code>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </dd>
                        <dt class="col-5">Registrato il</dt>
                        <dd class="col-7"><?= $model->created_at ? Html::encode(date('d-m-Y H:i', strtotime($model->created_at))) : '—' ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
