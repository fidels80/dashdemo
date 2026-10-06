<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\MgArticolo */
/* @var $aliquote array */
/* @var $unita array */
/* @var $marche array */
/* @var $modelli array */
/* @var $tessuti array */
/* @var $taglie array */
/* @var $colori array */
/* @var $unitaArticolo app\models\MgArticoloUm[] */

$this->title = 'Modifica articolo: ' . $model->codice;
$this->params['breadcrumbs'][] = ['label' => 'Articoli', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->codice, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Modifica';
?>
<div class="mgarticolo-update">
    <?= $this->render('_form', [
        'model' => $model,
        'aliquote' => $aliquote,
        'unita' => $unita,
        'marche' => $marche,
        'modelli' => $modelli,
        'tessuti' => $tessuti,
        'taglie' => $taglie,
        'colori' => $colori,
        'unitaArticolo' => $unitaArticolo,
    ]) ?>
</div>
