<?php

use app\models\Testaj;

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\TestajSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

use yii\bootstrap4\Modal;

 





$this->title = 'Testajs';
$this->params['breadcrumbs'][] = $this->title;
$createUrl = Url::to(['create']);
$createUrl = str_replace('"', '\"', $createUrl);
$js        = <<<JS
    $(document).on('click', '[data-toggle="create-testaj-modal"]', function(event) {
        event.preventDefault();
        var url = "$createUrl";
        $.get(url, function(data) {
            $('#testaj-modal .modal-content').html(data); // Utilizza .modal-content per inserire il contenuto
            $('#testaj-modal').modal('show'); // Mostra la modale una volta caricata
        });
    });
JS;
$this->registerJs($js);
$id='nuovo';
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
$url = Url::to(['create', 'aj' =>'modal',
//   'mod'->$model
]);


?>

 

 

<div class="testaj-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
                <?php echo Html::button('Crea', ['value' => $url, 'class' => 'btn btn-warning',
    'id'                               => 'modalspe_' . $id]);
?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'testo',
            'testo2',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<script>
    // Codice per gestire il salvataggio del form nella modale
    $(document).on('submit', '#testaj-form', function(event) {
        console.log('inter');
        event.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            success: function(data) {
                // Hide the modal after successful submission
                $('#testaj-modal').modal('hide');
                $.pjax.reload({container: '#grid-pjax', timeout: false});
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX request failed: ' + textStatus, errorThrown);
            }
        });
    });
</script>