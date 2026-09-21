<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Todomain */

$attivita = $model->attivitas;
?>

<?php if (empty($attivita)): ?>
    <p class="text-muted small mb-0">Nessuna modifica registrata.</p>
<?php else: ?>
    <?php foreach ($attivita as $a): ?>
        <div class="activity-item">
            <strong><?= Html::encode($a->user ?: 'sistema') ?></strong>
            <?php if ($a->azione === 'creazione'): ?>
                ha creato il task
            <?php elseif ($a->azione === 'commento'): ?>
                ha commentato
            <?php elseif ($a->azione === 'spostamento'): ?>
                ha spostato lo stato
                <?php if ($a->valore_prima !== '' || $a->valore_dopo !== ''): ?>
                    da <em><?= Html::encode($a->valore_prima) ?></em> a <em><?= Html::encode($a->valore_dopo) ?></em>
                <?php endif; ?>
            <?php else: ?>
                ha modificato <em><?= Html::encode($a->campo) ?></em>
                <?php if ($a->valore_prima !== '' || $a->valore_dopo !== ''): ?>
                    : <?= Html::encode($a->valore_prima) ?> → <?= Html::encode($a->valore_dopo) ?>
                <?php endif; ?>
            <?php endif; ?>
            <span class="text-muted">· <?= $a->data ? date('d/m/Y H:i', strtotime($a->data)) : '' ?></span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
