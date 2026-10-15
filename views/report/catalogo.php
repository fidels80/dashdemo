<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Report';
?>

<div class="report-catalogo">
    <?php $puoProgettare = \app\components\AccessControl::allowed('reportdesigner', 'index', Yii::$app->user->identity); ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="m-0 text-dark"><i class="fas fa-file-alt text-secondary"></i> <?= Html::encode('Report disponibili') ?></h1>
        <?php if ($puoProgettare): ?>
            <?= Html::a('<i class="fas fa-tools"></i> Report Designer', ['reportdesigner/index'], ['class' => 'btn btn-outline-primary']) ?>
        <?php endif; ?>
    </div>

    <p class="text-muted pb-2 border-bottom">Seleziona il report che desideri eseguire.</p>

    <?php if (empty($reports)): ?>
        <div class="alert alert-info">Nessun report disponibile per il tuo profilo.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($reports as $r): ?>
                <div class="col-md-4 mb-4">
                    <a href="<?= Url::to(['report/esegui', 'codice' => $r->codice]) ?>" class="text-decoration-none text-dark">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-1">
                                    <?php if ($r->icona): ?><i class="fas fa-<?= Html::encode($r->icona) ?> text-primary mr-1"></i><?php endif; ?>
                                    <?= Html::encode($r->nome) ?>
                                </h5>
                                <?php if ($r->categoria): ?>
                                    <span class="badge badge-secondary mb-2"><?= Html::encode($r->categoria) ?></span>
                                <?php endif; ?>
                                <p class="card-text text-muted small mb-2"><?= Html::encode($r->descrizione) ?></p>
                                <span class="btn btn-outline-primary btn-sm"><i class="fas fa-play"></i> Esegui</span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
