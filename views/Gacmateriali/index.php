<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GacmaterialiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Materiali';
$this->params['breadcrumbs'][] = $this->title;

$id = 1;


if (!isset($id)) {
    $id = 0;

}
 
Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id'   => 'mat' . $id,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
 $('#modalmat_$id').click(function (){
        $('#mat$id').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));
        
        // Salva l'ID della tab corrente quando si fa clic sul pulsante Crea nella modal
    var currentTabId = $('.nav-tabs .active a').attr('href');
    $('#mat' + $id).data('currentTabId', currentTabId);
        console.log(currentTabId);
       // $('#mat$id').data('currentTabId', currentTabId);
    });
    
  "
); 
$url = Url::to(['create', 'id' => $id,
//   'mod'->$model
]);



?>
<div class="gacmateriali-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 

        if (($id) != 0) {

    /*
    echo Html::button('Crea', ['value' => $url,
    'class'                                           => 'btn btn-warning',
    'id' => 'modalcli_' . $id]);
     */
    echo Html::button('Aggiungi', ['value' => $url, 'class' => 'btn btn-warning',
        'id'                               => 'modalmat_' . $id]);
} else {
    echo Html::a('Aggiungi Materiali',
        ['create', 'id_sub_prv' => $id], ['class' => 'btn btn-success']);
}?>



    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'id_sub_prv',
            'listino',
            ['attribute'=>'cd_ar','label'=>'Art'],
            'descrizione',
           'qta',
           'um',
           'costounitario',
           'scontoacq',
          // 'costounitscontato',
           'ricarico',
           'costounitarioric',
           'sconto_vendita',
           //'valvendita',
           //'margine',
           'margineperc',
           'prezzounitarionetto',
          // 'note:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
