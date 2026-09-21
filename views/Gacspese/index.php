<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GacspeseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Spese';
$this->params['breadcrumbs'][] = $this->title;


if (!isset($id)) {
    $id = 0;

}




Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id'   => 'spe' . $id,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
         $('#modalspe_$id').click(function (){
        $('#spe$id').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));

        // Salva l'ID della tab corrente quando si fa clic sul pulsante Crea nella modal
   // var currentTabId = $('.nav-tabs .active a').attr('href');
   // $('#cli' + $id).data('currentTabId', currentTabId);
    //    console.log(currentTabId);
       // $('#cli$id').data('currentTabId', currentTabId);
    });

   
 
     "
);
$url = Url::to(['create', 'id' => $id,
//   'mod'->$model
]);
?>
<div class="gacspese-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
              <?php if (($id) != 0) {

    /*
    echo Html::button('Crea', ['value' => $url,
    'class'                                           => 'btn btn-warning',
    'id' => 'modalcli_' . $id]);
     */
    echo Html::button('Crea', ['value' => $url, 'class' => 'btn btn-warning',
        'id'                               => 'modalspe_' . $id]);
} else {
    echo Html::a('Create GAcspese',
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
            'spesa',
            'descrizione',
            'qta',
            //'um',
            
            ['attribute'=>'costounitario','label'=>'Costo Uni.'],
            //'sconto',
            //'costonetto',
            'ricarico',
            //'costoricaricato',
            //'scontovendita',
            ['attribute'=>'valorenettounitario','label'=>'Val.Net.Unit.'],
        
            'valorenetto',
            //'margine',
            ['attribute'=> 'margineperc','label'=>'Margine %'],
        
           
            //'note:ntext',
            ['attribute'=>     'cd_ar', 'label'=>'Art'],
        
            ['attribute'=>     'descrizionear', 'label'=>'Des.Art'],
                   ['attribute'=>     'prezzoar', 'label'=>'Prz.Art'],
            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
