 

 
<?php
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\testaj;
/* @var $this yii\web\View */
/* @var $model app\models\Testaj */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Title';
$this->params['breadcrumbs'][] = $this->title;

$dataProvider = new \yii\data\ActiveDataProvider([
    'query' => app\models\testaj::find(),
]);

?>

<!-- Contenuto della griglia con l'elenco di tutti i record -->
<?=GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        'testo',
        'testo2',
        // Aggiungi altre colonne se necessario
    ],
])?>

<!-- Tasto per aprire il modale di creazione del record -->
<?=Html::button('Crea Record', [
    'class' => 'btn btn-primary',
    'id'    => 'modal-crea-record',
])?>

<!-- Modale per la creazione del record -->
<?php
Modal::begin([
    'id'    => 'modal-crea-record-modal',
    'title' => 'Crea Record',
    'size'  => Modal::SIZE_LARGE,
]);

// URL per l'azione di creazione del record
$url = Url::to(['testaj/create']);
$model=new testaj();
// Render the form in the modal content
echo $this->renderAjax('@app/views/testaj/_form', [
    'model' => $model,
]);

Modal::end();
?>

<?php
// Script per aprire il modale quando il tasto viene cliccato
$script = <<< JS
    $('#modal-crea-record').on('click', function() {
        var url = '{$url}'; // Ottieni l'URL dell'azione create dal controller
        console.log('URL:', url); // Aggiunto per debug
        $('#modal-crea-record-modal').modal('show')
            .find('#modal-crea-record-content')
            .load(url); // Usa l'URL ottenuto per caricare il contenuto del modale tramite AJAX
    });
$(document).ready(function() {
    // Gestisci l'invio del form tramite AJAX
    $(document).on('submit', '#testaj-form', function(e) {
        e.preventDefault(); // Disabilita l'invio del form in modo tradizionale
        console.log('Form submit intercettato');
        var form = $(this);
        console . log('Form dati:', form . serialize());

        // Controlla se il form è valido prima di inviare la richiesta AJAX
      //  if (form.yiiActiveForm('validate')) {
            $.ajax({
                url: form.attr('action'),
                type: 'post',//form.attr('method'),
                data: form.serialize(),
                success: function(data) {
                    console.log('Risposta AJAX:', data); // Aggiunto per debug
                  //  if (data.success) {
                        // Aggiorna la griglia dopo la chiusura del modale
                    //    $.pjax.reload({container: '#pjax-grid-view'});
                        $('#modal-crea-record-modal').modal('hide');
                    //} else {
                        console.log('Errore: ' + data.message);
                        console.log(data.errors);
                        //form.yiiActiveForm('updateMessages', data.errors, true);
                   // }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log('Errore AJAX:');
                    console.log('Status:', textStatus); // Mostra il tipo di errore
                    console.log('Error Thrown:', errorThrown); // Mostra l'eventuale messaggio di errore
                    console.log('Response Text:', xhr.responseText); // Mostra la risposta completa della richiesta AJAX
                }
            });
        //}
    });
});
    // Chiudi la modale dopo il completamento dell'evento hidden.bs.modal
    $('#modal-crea-record-modal').on('hidden.bs.modal', function () {
        $(this).find('#modal-crea-record-content').empty();
    });
JS;

$this->registerJs($script);
?>
<?php if (Yii::$app->request->get('debug')): ?>
    <?=\yii\debug\Toolbar::widget()?>
<?php endif;?>