<script src="https://kit.fontawesome.com/a5ce0dfadd.js" crossorigin="anonymous"></script>

<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\select2\Select2;
use yii\widgets\ListView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\XtravelheadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$x = Yii::$app->runAction('site/getexp');
$icon = new \thoulah\fontawesome\Icon();
//use kartik\icons\Icon;
//Icon::map($this, Icon::EL);
$usrid = Yii::$app->user->Id;
 
$this->title = "Commesse"; // Oppure una stringa vuota
 
?>


<style>
.row {
  margin-right: 15px;
  margin-left: 15px;
}
    </style>

<div  class="row d-flex" style="gap: 10px;">
            <?php echo Html::a('<i class="fa-solid fa-magnifying-glass">
            </i> Cerca Commessa', ['xtravelhead/index2'], [
    'class' => 'btn btn-primary  me-2', // puoi personalizzare la classe per lo stile
]);?>        
            <?php echo Html::a('<i class="fa-solid fa-thumbtack"></i>
            Attivi', ['xtravelhead/index'], [
    'class' => 'btn btn-primary', // puoi personalizzare la classe per lo stile
]);?>
</div>

<br>

<br>

<div class="row">
<?= \yii\widgets\ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView' => '_card',
    'layout' => "{items}\n{pager}", // Contenitore personalizzato
    'options' => ['class' => 'row'],
    'itemOptions' => ['class' => 'col-lg-4 col-md-6 mb-4'],
    'pager' => [
        'options' => ['class' => 'pagination'],
        'linkOptions' => ['style' => 'font-size: 20px; font-weight: bold;'],
        'activePageCssClass' => 'active',
    ],
]); ?>



</div>
