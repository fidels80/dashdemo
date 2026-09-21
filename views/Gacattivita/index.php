<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\GacattivitaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attività';
$this->params['breadcrumbs'][] = $this->title;


if(!isset($id))
{
$id=0;

}


Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id'   => 'att' . $id,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
         $('#modalatt_$id').click(function (){
        $('#att$id').modal('show')
        .find('#modalContent')
        .load($(this).attr('value'));
        
        // Salva l'ID della tab corrente quando si fa clic sul pulsante Crea nella modal
    var currentTabId = $('.nav-tabs .active a').attr('href');
    $('#att' + $id).data('currentTabId', currentTabId);
        console.log(currentTabId);
       // $('#cli$id').data('currentTabId', currentTabId);
    });
    
    $('#modalContent').on('submit', 'form', function(event) {
        event.preventDefault();
        var form = $(this);
        var currentTabId = $('#cli$id').data('currentTabId'); // Recupera l'ID della tab corrente
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#cli$id').modal('hide');
                    // Esegui l'aggiornamento della tab corrente tramite AJAX
                    $('#' + currentTabId + ' a').tab('show'); // Mostra la tab corrente
                    $.pjax.reload({container: '#' + currentTabId + '-content'}); // Ricarica il contenuto della tab corrente
                } else {
                    // Se ci sono errori nella form, puoi gestirli qui se necessario
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                // Gestisci gli errori se necessario
            }
        });
    });
     "
);
$url = Url::to(['create', 'id' => $id,
//   'mod'->$model
]);



?>
<div class="gacattivita-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php  if ( ($id)<>0){

   /*       
echo Html::button('Crea', ['value' => $url,
    'class'                                           => 'btn btn-warning', 
    'id' => 'modalcli_' . $id]);
*/
echo Html::button('Crea', ['value' => $url, 'class' => 'btn btn-warning', 
'id' => 'modalatt_' . $id]);
        }else{
            echo Html::a('Create Gacattivita',
             ['create','id_sub_prv'=>$id], ['class' => 'btn btn-success']);
        }?>
    
</p>

    <?php Pjax::begin(['id' => 'pjax-gridview']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          // 'id_attivita',
            //'id_sub_prv',
          //  'sequenza',
            'attivita',
            'descrizione',
            'um',
            'tempo',
           // 'ore',
            'risorsa',
            'costo',
            'sconto',
            ['attribute'=>'costo_scontato','label'=>'Costo Sc.'],
            'ricarico',
            //'costo_ricarico',
            'sconto_vendita',
           // 'valore_costounitario',
           
            ['attribute'=> 'valore_costotot','label'=>'Costo Tot.'],
            'margine',
            ['attribute'=> 'margine_perc','label'=>'Margine %'],
            
           // 'note:ntext',
            'data_apertura',
            'data_chiusura',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
