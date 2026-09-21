<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DataTables;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Token API';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apitoken-index card p-3 shadow-sm">

    <?php if (Yii::$app->session->hasFlash('token_generato')): ?>
        <div class="alert alert-success">
            <h5><i class="fas fa-key"></i> Token generato (copialo ora, non sarà più visibile):</h5>
            <code style="word-break:break-all; font-size:1rem;"><?= Html::encode(Yii::$app->session->getFlash('token_generato')) ?></code>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-info"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
    <?php endif; ?>

    <div class="alert alert-secondary">
        <i class="fas fa-info-circle"></i>
        Esempio di chiamata:
        <code>curl -H "Authorization: Bearer &lt;token&gt;" "<?= Url::to(['/api/export', 'entity' => 'documenti'], true) ?>"</code>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div class="mb-2">
            <?= Html::a('<i class="fas fa-plus"></i> Nuovo token', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <table id="apitoken-table" class="table table-striped table-bordered" style="width:100%">
        <thead>
        <tr>
            <th>ID</th>
            <th>Descrizione</th>
            <th>Utente</th>
            <th>Permessi</th>
            <th>Creato il</th>
            <th>Scadenza</th>
            <th>Ultimo utilizzo</th>
            <th class="no-export">Azioni</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dataProvider->getModels() as $m): ?>
            <tr>
                <td><?= (int) $m->id ?></td>
                <td><?= Html::encode($m->descrizione) ?></td>
                <td><?= Html::encode($m->user->username ?? '') ?></td>
                <td><?= Html::encode($m->scopes) ?></td>
                <td><?= $m->created_at ? date('d/m/Y H:i', $m->created_at) : '' ?></td>
                <td><?= $m->expires_at ? date('d/m/Y', $m->expires_at) : 'mai' ?></td>
                <td><?= $m->last_used_at ? date('d/m/Y H:i', $m->last_used_at) : '' ?></td>
                <td class="text-center text-nowrap no-export">
                    <?= Html::a('<i class="fas fa-trash"></i>', ['delete', 'id' => $m->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Revoca',
                        'data' => ['confirm' => 'Revocare questo token?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php DataTables::render('apitoken-table', 0, 'desc'); ?>
