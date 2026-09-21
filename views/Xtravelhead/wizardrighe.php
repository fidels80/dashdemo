<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Url;
use onmotion\apexcharts\ApexchartsWidget;

$this->registerCssFile(
    '@web/css/custom-styles.css',
    ['depends' => [\yii\web\YiiAsset::class]]
);


use yii\helpers\ArrayHelper;
use kartik\nav\NavX;
use kartik\select2\Select2;

use yii\helpers\Json;
use kartik\dialog\Dialog;
use yii\web\JsExpression;
use yii\data\ArrayDataProvider;
use kartik\export\ExportMenu;
use yii\bootstrap4\Modal;
use kartik\editable\Editable;
//use kartik\dynagrid\DynaGrid;
use kartik\grid\GridView;
use app\models\XVenue;
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */
use app\models\Xtappe;
//$this->title = $model->descrizione;
//$this->params['breadcrumbs'][] = ['label' => 'Xtravelheads', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
//yii::warning($model);
//yii::warning($tappe);
//yii::warning($roomlist);


$this->title = 'Wizard Righe di Viaggio';

?>
<br>
<br>
<br>


<h1><?= Html::encode($this->title) ?></h1>
<h5 class="card-title" style="color: #4a6fa5;">Righe da Elaborare</h5>
<br>

<?php

$url = Url::to(['xtravelhead/masterhotel', 'id' => $xth]);

echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
    'class' => 'button-base-support button-lift',
    'onclick' => 'window.location.href = "' . $url . '"'
]);

yii::error($tappe);
?>


<br>
<br><br>

<?php
// Estraggo i dati necessari dai DataProvider0
$tappeData = [];
$roomlistData = [];

// Per i modelli nel DataProvider delle tappe

// Per i modelli nel DataProvider della roomlist


// Converto gli array in JSON
$tappeJson = json_encode($tappe->allModels);
$roomlistJson = json_encode($roomlist->allModels);
if ($tappe->totalCount > 0 && $roomlist->totalCount > 0) {
    // Creo il form con gli input hidden
    echo Html::beginForm(['xtravelhead/wizardrighesalva'], 'post');
    echo Html::hiddenInput('th_id', $model->th_id);
    echo Html::hiddenInput('tappe', $tappeJson);
    echo Html::hiddenInput('roomlist', $roomlistJson);

    echo Html::submitButton('Applica Righe', ['class' => 'button-base button-lift']);

    echo Html::endForm();
}
if ($tappe->totalCount == 0 && $roomlist->totalCount > 0) {
    // Creo il form con gli input hidden

    $db = Yii::$app->db5;

    // 1. Estrai le città uniche
    $command = $db->createCommand("select id_tappa,th_id,data,citta,evaso	
        	 from x_tappe
where th_id=$model->th_id
order by data asc 
");

    $tappe_arr = $command->queryAll();
    $tappe = new ArrayDataProvider([
        'allModels' => $tappe_arr,
        'pagination' => false,

    ]);

    $tappeJson = json_encode($tappe->allModels);


    echo Html::beginForm(['xtravelhead/wizardrighesalvaospiti'], 'post');
    echo Html::hiddenInput('th_id', $model->th_id);
    echo Html::hiddenInput('tappe', $tappeJson);
    echo Html::hiddenInput('roomlist', $roomlistJson);

    echo Html::submitButton('Applica Righe', ['class' => 'button-base button-lift']);

    echo Html::endForm();
}



?>

<?= GridView::widget([
    'dataProvider' => $tappe,
    'columns' => [
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function () {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) use ($roomlist, $tappe) {
                //_ddetail_wiz


                return   $this->render('_ddetail_wiz', ['dwroomlist' => $roomlist, 'dwteappe' => $tappe]);
            },
            'expandOneOnly' => true,
        ],
        [
            'label' => 'Tappa',
            'attribute' => 'id_tappa',
            'value' => function ($model, $key, $index, $column) {
                $ris = Xtappe::find()->where(['id_tappa' => $model['id_tappa']])
                    ->One();
                //=Xvenue::find()->where(['id' => $citta])->one();
                return   $ris['citta'];
            }


        ],
        // ['attribute' => 'th_id'],
        [
            'attribute' => 'data',
            'value' => function ($model, $key, $index, $column) {
                return !empty($model['data'])
                    ? date('d/m/y', strtotime($model['data']))
                    : '';
            }
        ],
        [
            'label' => 'Città',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {

                if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $model['citta'])) {
                    // È un UUID valido
                    $tras = Xvenue::find()->where(['id' => $model['citta']])->one();
                    if ($tras) {
                        return  $tras->venue . '-' . $tras->citta;
                    } else {
                        return "Città non trovata per ID " . $model['citta'];
                    }
                } else {
                    // È una stringa normale
                    return  $model['citta'];
                }
            }
        ],
        [
            'attribute' => 'evaso',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {

                return    '<i class="fas fa-stop"></i>';
            }


        ],
    ],
]);
?>
<br>
<br>
<h5 class="card-title" style="color: #4a6fa5;">Righe presenti</h5>
<br><br>



<?= GridView::widget([
    'dataProvider' => $wzrighe_g,
    'columns' => [
        [
            'class' => 'kartik\grid\ExpandRowColumn',
            'value' => function () {
                return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) use ($wzrighe, $roomlist, $tappe) {
                // Filtro righe con citta o citta_da uguale alla riga espansa e stessa data
                $wzrighe_filtro = array_filter($wzrighe, function ($riga) use ($model) {
                    $citta_effettiva = $riga['citta'] ?? $riga['citta_da'];
                    return (
                        $citta_effettiva === $model['citta']
                        //&&             $riga['check_in'] === $model['check_in']
                    );
                });

                return Yii::$app->controller->renderPartial('_ddetail_wizrighe', [
                    'righe' => $wzrighe_filtro,
                    'dwroomlist' => $roomlist,
                    'dwteappe' => $tappe
                ]);
            },

            'expandOneOnly' => true,
        ],
        [
            'attribute' => 'citta',
            'header' => 'Tappa',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $widget) {
                if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $model['citta'])) {
                    // È un UUID valido
                    $tras = Xvenue::find()->where(['id' => $model['citta']])->one();
                    if ($tras) {
                        $tcitta =  $tras->citta .  ' - ' . $tras->venue;
                    } else {
                        $tcitta =  "Città non trovata per ID " . $model['citta'];
                    }
                } else {
                    // È una stringa normale

                    $tcitta =   $model['citta'];
                }





                return Html::a(
                    Html::encode($tcitta),
                    [
                        'xtravelhead/wizartappe',
                        'th_id' => $model['th_id'],
                        'tappa' => $model['citta'],
                        // 'data' => $model['check_in']
                    ],
                    ['title' => 'Vai alla tappa']
                );
            },
        ],
        [
            'attribute' => 'check_in',
            'header' => 'Data',
            'value' => function ($model, $key, $index, $column) {
                return !empty($model['check_in'])
                    ? date('d/m/y', strtotime($model['check_in']))
                    : '';
            }

        ],

        // ['attribute' => 'check_out'],
        ['attribute' => 'totale'],

    ],
]);
?>



<?php
// Nella view wizardrighe.php
$debugInfo = Yii::$app->session->get('debug_wizard');
if ($debugInfo): ?>
    <div class="debug-info" style="background: #f8f9fa; padding: 15px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px;">
        <h3>Debug Info</h3>
        <pre><?= print_r($debugInfo, true) ?></pre>
    </div>
<?php
    // Rimuovo le info di debug dopo averle mostrate
    Yii::$app->session->remove('debug_wizard');
endif;

?>