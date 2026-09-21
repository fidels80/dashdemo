<?php
 use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use app\models\Teste2;
use kartik\tabs\TabsX;

// Codice per la griglia dei dati

$t= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        'id',
        'testo',
        'testo2',
        // Altri campi, se necessario
    ],
]);


// Codice per il pulsante che apre la finestra modale
echo Html::button('Nuovo Record', ['class' => 'btn btn-success', 'id' => 'create-button']);

// Codice per la finestra modale
Modal::begin([
    'id'     => 'create-modal',
    'size'   => 'modal-lg',
 //   'header' => '<h4>Nuovo Record</h4>',
]);
Pjax::begin(['id' => 'modal-pjax']);
$model= new Teste2();
echo $this->render('_form', ['model' => $model]);
Pjax::end();
Modal::end();

// Javascript per gestire l'apertura della finestra modale e l'aggiornamento con Pjax
$this->registerJs("
    $('#create-button').click(function(){
        $('#create-modal').modal('show')
    });
$('#create-button').click(function(){
    $.ajax({
        url: 'index.php?r=teste2/create', // Controlla il percorso del controller
        type: 'get',
        success: function(data) {
            $('#create-modal .modal-body').html(data);
            $('#create-modal').modal('show');
        }
    });
});
    $('#modal-pjax').on('pjax:end', function() {
        $.pjax.reload({container:'#grid-pjax'});
        $('#create-modal').modal('hide');
    });



$('#create-modal').on('beforeSubmit', 'form#create-form', function(e) {
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function(data) {
            if (data.success) {
                // Chiudi la finestra modale e aggiorna la griglia con Pjax
                $('#create-modal').modal('hide');
                $.pjax.reload({container:'#grid-pjax'});
            } else {
                // In caso di errori, puoi visualizzarli o gestirli qui
                console.log('Errore nel salvataggio del record.');
            }
        }
    });
    return false;
});



");
$items = [
    [
        'label'=>'<i class="fas fa-home"></i> Home',
        'content'=>$t,
    ],
    [
        'label'=>'<i class="fas fa-home"></i> h',
        'content'=>'sadsad',
        
    ],

];
Pjax::begin(['id' => 'grid-pjax']);
echo TabsX::widget([
    'items'        => $items,
    'position'     => TabsX::POS_RIGHT,
    'encodeLabels' => false,
]);
Pjax::end();


?>