<?php
use app\models\Testaj;
use yii\bootstrap4\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TestajSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Testajs';
$this->params['breadcrumbs'][] = $this->title;
$this->registerAssetBundle(yii\web\YiiAsset::class);
$this->registerAssetBundle(yii\widgets\PjaxAsset::class);
$this->registerAssetBundle(yii\bootstrap4\BootstrapAsset::class);
$this->registerAssetBundle(yii\bootstrap4\BootstrapPluginAsset::class);
//$this->registerAssetBundle(yii\bootstrap4\BootstrapThemeAsset::class);
$this->registerJsFile('https://code.jquery.com/jquery-3.3.1.min.js', ['position' => \yii\web\View::POS_HEAD]);

// Codice per aprire la modale al click sul pulsante "Create Testaj"
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
?>

<div class="testaj-index">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
        <?=Html::a('Create Testaj', ['create'], [
    'class'       => 'btn btn-success',
    'data-toggle' => 'create-testaj-modal', // Usa l'attributo 'data-toggle' per aprire la modale
])?>
    </p>

    <!-- PJAX Container -->
    <?php Pjax::begin(['id' => 'grid-pjax', 'timeout' => false, 'enablePushState' => false]);?>

    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'      => [
        ['class' => 'yii\grid\SerialColumn'],
        'id',
        'testo',
        'testo2',
        ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>

    <?php Pjax::end();?>
    <!-- End PJAX Container -->

</div>

<?php
Modal::begin([
    'id'    => 'testaj-modal',
    'size'  => Modal::SIZE_LARGE,
    'title' => 'Create Testaj',
]);

// Render the form directly in the modal content
echo $this->renderAjax('_form', [
    'model' => new Testaj(),
]);

Modal::end();
?>

<script>
    // Codice per gestire il salvataggio del form nella modale
    $(document).on('submit', '#testaj-form', function(event) {
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
