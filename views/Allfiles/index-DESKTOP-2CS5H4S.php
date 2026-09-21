<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\AllfilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Files';
$this->params['breadcrumbs'][] = $this->title;


if (!isset($id)) {
    $id = 0;

}

Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id'   => 'doc' . $id,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
  $('#modaldoc_$id').click(function (){
  $('#doc$id').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
);
$url = Url::to(['allfiles/ajupd', 'xid_testa' => $id, 'tab' => 'PRV'
//   'mod'->$model
]);



?>
<div class="allfiles-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
<?php
  if ( ($id)<>0){
echo Html::button('Crea', ['value' => $url, 'class' => 'btn btn-warning',
    'id'                               => 'modaldoc_' . $id]);

  }else{
        echo Html::a('Create Allfiles', ['create'], ['class' => 'btn btn-success']); }?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'id_padre',
           // 'f_content',
            'entita',
            'nomefile',
            'estensione',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
