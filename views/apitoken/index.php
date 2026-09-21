<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Token API';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apitoken-index">

    <h1><?= Html::encode($this->title) ?></h1>

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

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Nuovo token', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'descrizione',
            [
                'attribute' => 'user_id',
                'value' => function ($m) {
                    return $m->user->username ?? '';
                },
            ],
            'scopes',
            [
                'attribute' => 'created_at',
                'value' => function ($m) {
                    return $m->created_at ? date('d/m/Y H:i', $m->created_at) : '';
                },
            ],
            [
                'attribute' => 'expires_at',
                'value' => function ($m) {
                    return $m->expires_at ? date('d/m/Y', $m->expires_at) : 'mai';
                },
            ],
            [
                'attribute' => 'last_used_at',
                'value' => function ($m) {
                    return $m->last_used_at ? date('d/m/Y H:i', $m->last_used_at) : '';
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
            ],
        ],
    ]); ?>
</div>
