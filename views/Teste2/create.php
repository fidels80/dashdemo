<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Teste2 */

$this->title = 'Create Teste2';
$this->params['breadcrumbs'][] = ['label' => 'Teste2s', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teste2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
