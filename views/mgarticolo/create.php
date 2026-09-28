<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */
/* @var $aliquote array */
/* @var $unita array */
/* @var $marche array */
/* @var $modelli array */
/* @var $taglie array */
/* @var $colori array */
/* @var $unitaArticolo app\models\MgArticoloUm[] */

$this->title = 'Nuovo articolo';
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mgarticolo-create">
    <?= $this->render('_form', [
        'model' => $model,
        'aliquote' => $aliquote,
        'unita' => $unita,
        'marche' => $marche,
        'modelli' => $modelli,
        'taglie' => $taglie,
        'colori' => $colori,
        'unitaArticolo' => $unitaArticolo,
    ]) ?>
</div>
