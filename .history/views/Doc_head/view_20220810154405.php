<?php

use yii\helpers\Html;

use  yii\bootstrap4\Modal;
use app\models\AnacliSearch;
use app\models\Anacli;
use app\models\CliDest;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use yii\widgets\DetailView; 
use app\models\allfiles;
use app\models\doc_rows;
$icon = new \thoulah\fontawesome\Icon();

$usrid=Yii::$app->user->Id;

if ($usrid !== null) {
    $usr_ris = (new \yii\db\Query())
        ->select(['email', 'gruppo'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
    //->AsArray();
}


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
if ($usr_ris['gruppo']<>'users') {
    echo Html::button('Cambia Sede', ['value' => $url,
    'class' => 'btn btn-success', 'id' => 'modalcli_' . $tmpid]);
}
echo '<br>';
echo '<br>';
if ($usr_ris['gruppo']<>'users') {
    echo Html::a(
        'Conferma',
        ['conferma', 'id' => $model->id],
        ['class' => 'btn  btn-success',
            'data' => [
                'confirm' => 'Sei sicuro di voler CONFERMARE il documento?',
                'method' => 'post',
            ]],
    );
}
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
if ($usr_ris['gruppo']<>'users') {
    echo Html::button('Rifiuta', ['value' => $url,
    'class' => 'btn btn-danger', 'id' => 'modalcli_' . $tmpid]);
}
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
        [
            'attribute' => 'altcli',
            'value' => function ($model) {
                
                $dm = anacli::find()
    ->select(['Desk as  Name'])->where(['cd_cli' => $model->altcli])
    //->andWhere(['cd_cli_dest'=>$model->dest])
    ->asArray()
    ->one();
//yii::warning($dm);
                
                return $dm['Name'];
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

if ($model->confermato<>1 and $model->rifiutato<>1)
{
if ($usr_ris['gruppo']<>'users') {
    echo Html::button('Carica File', ['value'=>$url,
   'class' => 'btn btn-success','id'=>'modalcli_'.$tmpid]);
}
}

   echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

//yii::error( ($model->xid_testa ));
//yii::warning($model->filesall);
foreach ($model->filesall as $value) {
    // echo '<tr>';

    if ($value['entita'] = 'DOTES') {
        echo '<tr>';
        echo '<td>';
        
        echo  Html::a($value['nomefile'],
        ['allfiles/genfile','id' => $value['id'],
        'file'=>str_replace(' ', '_',$value['nomefile']) ]);
        
        echo '</td>';
      

echo '<td>';





          if ($value['origine']=='S'){
//echo 'usrld';

echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
Yii::$app->fontawesome->name('user',
    'solid')->fill('#003865');

}else{

echo //Yii::$app->fontawesome->name('github', 'brands')->fill->('#003865');
Yii::$app->fontawesome->name('server',
    'solid')->fill('#003865');



        } 

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
echo '<th width=50px >Evaso</th>';

//echo '<th>Cod. Art.</th>';
echo '<th>Descrizione</th>';
echo '<th>Data Pubblicazione</th>';
echo '<th>Nr. Gazzetta</th>';
echo '<th>Nr. Inserzione</th>';

echo '<th>U.M.</th>';

echo '<th>Qta</th>';
echo '<th>Prezzo</th>';
//echo '<th>note</th>';
echo '<th>Al. Iva</th>';
echo '<th>File</th>';

        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        

 

//yii::warning(var_dump($model->getrowssall()));

     foreach ($model->rowsall  as $value) {
        echo '<tr>';
        //echo  '<td>';
        //echo $value['id_agenda'];
        //echo '</td>';
       
echo '<td>';
if (!is_null($value['f_row'])) {

    $eva = doc_rows::find()->where(['xid_riga' => $value['f_row']])->one();

    echo Html::a(
        Yii::$app->fontawesome->name(
            'arrow-left',
            'solid'
        )->fill('#003865'),
        ['doc_head/view', 'id' => $eva['doc_head_id']]
    );

} else {
    echo Yii::$app->fontawesome->name(
        'hourglass', 'solid')->fill('#003865');

}
echo '||';
//if (is_null($value['f_row'])){
    $eva=doc_rows::find()->where(['f_row'=>$value['xid_riga']])->one();
//}

    if (!is_null($eva['doc_head_id'])) {
   // echo $eva['doc_head_id'];
   //echo '<i class="fa-solid fa-file-arrow-up">sss</i>';
   //echo Yii::$app->fontawesome->name(
   // 'arrow-up','solid')->fill('#003865');
    echo Html::a(Yii::$app->fontawesome->name(
    'arrow-right','solid')->fill('#003865'),
    ['doc_head/view', 'id' =>  $eva['doc_head_id']]);
 
}else{
echo Yii::$app->fontawesome->name(
 'hourglass','solid')->fill('#003865');

}
//yii::warning($value['f_row']);





echo '</td>';

       // echo  '<td>';
       
        //echo  $value['cd_art'];
       
        
       
       
      //  echo '</td>';
        echo  '<td>';
         echo $value['descrizione'];
        echo '</td>';
        echo  '<td>';
         echo $value['datacons'];
        echo '</td>';
        echo '<td>';
echo $value['nrgazzetta'];
echo '</td>';
echo '<td>';
echo $value['nrinserzione'];
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
echo '<td>';

//yii::warning($value->xid_riga);

$subvalue=allfiles::find()->
where(['entita'=>'DORIG'])
->andWhere(['id_padre'=>$value->xid_riga])
->one();
echo Html::a($subvalue['nomefile'],
    ['allfiles/genfile', 'id' => $subvalue['id'],
        'file' => str_replace(' ', '_', $subvalue['nomefile'])]);



echo '</td>';

        echo '</tr>';
       
     }
      echo '<tr id="product-new-parcel-block" style="display: none;">';
      echo '</tr>';
      echo '</tbody>';
      echo '</table>';

?>
    
</div>
