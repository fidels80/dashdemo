<style>
    .xdivclass {
        width: 150% !important;
        /* Increased from 80% to 95% */
        margin: auto;
        overflow-x: auto;
        padding: 10px;
        /* Add some padding */
    }

    .xdivclass table {
        min-width: 1200px;
        /* Reduced from 1500px to 1200px */
        width: 100%;
    }

    .dataTables_wrapper {
        width: 100%;
        overflow-x: auto;
        /* Ensure horizontal scroll */
    }

    /* Improve horizontal scrolling */
    .dataTables_scrollX {
        overflow-x: auto !important;
    }

    /* Fissa l'ultima colonna */
    .fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: white !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 10 !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
        min-width: 120px !important;
        /* Ensure minimum width */
    }

    /* Assicura che l'header sia anche fisso */
    .dataTables_scrollHead th.fixed-actions-column {
        position: sticky !important;
        right: 0 !important;
        background-color: #f8f9fa !important;
        border-left: 2px solid #dee2e6 !important;
        z-index: 11 !important;
        box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1) !important;
        min-width: 120px !important;
    }

    /* Stile per i bottoni nella colonna fissa */
    .fixed-actions-column .btn {
        margin: 1px 2px;
        font-size: 11px;
        padding: 3px 6px;
    }

    /* Ensure other columns have proper width */
    .dataTables_wrapper table td,
    .dataTables_wrapper table th {
        white-space: nowrap;
        min-width: 80px;
    }
</style>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Xruoli */

//$this->title =null;
//$this->params['breadcrumbs'][] = '';//['label' => 'Xruolis', 'url' => ['index']];
//$this->params['breadcrumbs'][] = '';//$this->title;
//\yii\web\YiiAsset::register($this);
?>
<div class="xruoli-view">

    <h1><?php //Html::encode($this->title)
     ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cd_ruolo' => $model->cd_ruolo], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cd_ruolo' => $model->cd_ruolo], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class='xdivclass'>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'cd_ruolo',
                'descrizione',
            ],
        ]) ?>
    </div>
</div>