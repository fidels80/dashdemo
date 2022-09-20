<?php

use yii\helpers\Html;

use  yii\bootstrap4\Modal;
use app\models\AnacliSearch;
use app\models\Anacli;
use app\models\CliDest;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
 use kartik\detail\DetailView;
 


/* @var $this yii\web\View 
use yii\helpers\ArrayHelper;
*/
/* @var $model app\models\Doc_head */
use yii\helpers\Url;
$this->title = ' ';//$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Elenco Documenti', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);


$annmodel=CliDest::find()
->select(['id', 'descrizione as  Name'])->where(['cd_cli'=>$model->cd_cli])->asArray()
    ->all();

$listdest = ArrayHelper::map($annmodel, 'id', 'Name');

 
?>
<div class="doc-head-view">

    <h1><?= Html::encode($model->cd_doc.' Num: '.$model->numdoc.' del '. $model->data) ?></h1>

    <p>
        <?php /*if ($model->confermato<>1 ) {

        
        
       echo Html::a('Conferma', ['conferma', 'id' => $model->id],
         ['class' => 'btn btn-primary']);  }
    else{

     echo    \hail812\adminlte\widgets\Alert::widget([
            'type' => 'success',
            'body' => '<h3>Documento Confermato!</h3>',
        ])  ;

    }
*/
if ($model->confermato<>1  && $model->rifiutato<>1) {

    echo '              ';
$tmpid = 2;
Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id' => 'cli' . $tmpid,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
  $('#modalcli_$tmpid').click(function (){
  $('#cli$tmpid').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
);
$url = Url::to(['sede', 'id' => $model->id
//   'mod'->$model
]);

echo Html::button('Cambia Sede', ['value' => $url,
    'class' => 'btn btn-success', 'id' => 'modalcli_' . $tmpid]);

echo '<br>';
echo '<br>';

echo Html::a(
    'Conferma',
    ['conferma', 'id' => $model->id],
    ['class' => 'btn  btn-success',
        'data' => [
            'confirm' => 'Sei sicuro di voler CONFERMARE il documento?',
            'method' => 'post',
        ]],
);

$tmpid=1;
Modal::begin([
    //'header'=>'<h4>Clienti</h4>',
    'id' => 'cli' . $tmpid,
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
$this->registerJs("
  $('#modalcli_$tmpid').click(function (){
  $('#cli$tmpid').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
);
$url = Url::to(['nrifiuta', 'id' => $model->id,
//   'mod'->$model
]);
echo '<br>';
echo '<br>';

echo Html::button('Rifiuta', ['value' => $url,
    'class' => 'btn btn-danger', 'id' => 'modalcli_' . $tmpid]);


}
elseif ( $model->confermato==1){           
echo \hail812\adminlte\widgets\Alert::widget([
    'type' => 'success',
    'body' => '<h3>Documento Confermato!</h3>',
]);}
elseif ($model->rifiutato==1 ){           
echo \hail812\adminlte\widgets\Alert::widget([
    'type' => 'danger',
    'body' => '<h3>Documento Rifiutato!</h3>',
]);
}
 
?>
        <?php
        



        /*Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>
    <table id="product-files" class="table table-condensed table-bordered">
<tr><td width="70%">
    <?php echo DetailView::widget([
        'model' => $model,
        'attributes' => [
       //     'id',
            'cd_doc',
            'data',
            'numdoc',
            'cd_cli',
            'dest',
        [
            'attribute' => 'dest',
            'value' => function ($model) {
                /*
                $dm = CliDest::find()
    ->select(['id', 'descrizione as  Name'])->where(['cd_cli' => $model->cd_cli])
    ->andWhere(['cd_cli_dest'=>$model->dest])
    ->asArray()
    ->one();
*/
                
                return $model;
                //$dm->Name;
            },
         
        ],

    'cd_pg',
            'sconto',
            'note',
        ],
    ]) ?></td><td width="30%">

 <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
echo '<th width="70%">file</th>';
echo '<th width="30%">';
$tmpid=0;
Modal::begin([
  //'header'=>'<h4>Clienti</h4>',
  'id' => 'cli'.$tmpid,
  'size'=>'modal-lg', //classe bootstrap
  ]);
  echo "<div id='modalContent'></div>";
  Modal::end();               
  $this->registerJs( "
  $('#modalcli_$tmpid').click(function (){
  $('#cli$tmpid').modal('show')
  .find('#modalContent')
  .load($(this).attr('value'));
  });"
   );
   $url=Url::to(['allfiles/ajupd','xid_testa' => $model->xid_testa,'tab'=>'DOTES',
//   'mod'->$model
]);
   echo Html::button('Carica File',['value'=>$url,
   'class' => 'btn btn-success','id'=>'modalcli_'.$tmpid]);
   echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

yii::error( ($model->xid_testa ));

foreach ($model->filesall as $value) {
    // echo '<tr>';

    if ($value['entita'] == 'DOTES') {
        echo '<tr>';
        echo '<td>';
        
        echo  Html::a($value['nomefile'],
        ['allfiles/genfile','id' => $value['id'],
        'file'=>str_replace(' ', '_',$value['nomefile']) ]);
        
        echo '</td>';
          echo '</tr>';
    }

}
echo '<tr id="files-new-parcel-block" style="display: none;">';
echo '</tr>';
echo '</tbody>';
echo '</table>';
?>
</td>
<?php 
echo '<table id="product-parcels" class="table table-condensed table-bordered">';
        echo '<thead>';
        echo '<tr>';
        //echo '<th>id_agenda</th>';
echo '<th>Cod. Art.</th>';
echo '<th>Descrizione</th>';
echo '<th>Data Consegna</th>';

echo '<th>U.M.</th>';

echo '<th>Qta</th>';
echo '<th>Prezzo</th>';
//echo '<th>note</th>';
echo '<th>Al. Iva</th>';

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        

 

//yii::warning(var_dump($model->getrowssall()));

     foreach ($model->rowsall  as $value) {
        echo '<tr>';
        //echo  '<td>';
        //echo $value['id_agenda'];
        //echo '</td>';
       
        echo  '<td>';
        echo  $value['cd_art'];
        echo '</td>';
        echo  '<td>';
         echo $value['descrizione'];
        echo '</td>';
        echo  '<td>';
         echo $value['note'];
        echo '</td>';
        echo  '<td>';
         echo $value['um'];
        echo '</td>';
        echo  '<td>';
         echo number_format($value['qta'],2);
        
        //  echo  Html::a('scarica',['agenda/genfile','id' => $value['id'],'file'=>str_replace(' ', '_',$value['nome_file']) ]);
        echo '</td>';
        echo  '<td>';
        echo number_format($value['prezzo'],2);
        echo '</td>';
        echo '<td>';
echo $value['iva'];
echo '</td>';

        echo '</tr>';
       
     }
      echo '<tr id="product-new-parcel-block" style="display: none;">';
      echo '</tr>';
      echo '</tbody>';
      echo '</table>';

?>
    
</div>
