<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Doc_head */

$this->title = ' ';//$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Teste Documenti', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="doc-head-view">

    <h1><?= Html::encode($model->cd_doc.' Num: '.$model->numdoc.' del '. $model->data) ?></h1>

    <p>
        <?php if ($model->confermato<>1) {

        
        
       echo Html::a('Conferma', ['conferma', 'id' => $model->id],
         ['class' => 'btn btn-primary']);  }
    else{

     echo    \hail812\adminlte\widgets\Alert::widget([
            'type' => 'success',
            'body' => '<h3>Documento Confermato!</h3>',
        ])  ;

    }



?>
        <?php /*Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
       //     'id',
            'cd_doc',
            'data',
            'numdoc',
            'cd_cli',
            'cd_pg',
            'sconto',
            'note',
        ],
    ]) ?>

 <?php echo '<table id="product-files" class="table table-condensed table-bordered">';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
echo '<th>file</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';
foreach ($model->filesall as $value) {
    // echo '<tr>';
    
    if ($value['entita'] == 'DOTES') {
        echo '<tr>';
        echo '<td>';
        echo $value['nomefile'];
        echo '</td>';
          echo '</tr>';
    }

}
echo '<tr id="files-new-parcel-block" style="display: none;">';
echo '</tr>';
echo '</tbody>';
echo '</table>';
?>

<?php 
echo '<table id="product-parcels" class="table table-condensed table-bordered">';
        echo '<thead>';
        echo '<tr>';
        //echo '<th>id_agenda</th>';
        echo '<th>cd_art</th>';
        echo '<th>descrizione</th>';
        echo '<th>qta</th>';
        echo '<th>prezzo</th>';
        echo '<th>note</th>';
        echo '<th>iva</th>';
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
         echo $value['qta'];
        echo '</td>';
        echo  '<td>';
         echo $value['prezzo'];
        echo '</td>';
        echo  '<td>';
         echo $value['note'];
        
        //  echo  Html::a('scarica',['agenda/genfile','id' => $value['id'],'file'=>str_replace(' ', '_',$value['nome_file']) ]);
        echo '</td>';
        echo  '<td>';
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
