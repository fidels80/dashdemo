 
<?php

echo '<table id="product-parcels" class="table table-condensed table-bordered" >';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
echo '<th>cd_art</th>';
echo '<th>descrizione</th>';
echo '<th>U.m.</th>';

echo '<th>qta</th>';
echo '<th>prezzo</th>';
//echo '<th>note</th>';
echo '<th>iva</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

//yii::warning(var_dump($model->getrowssall()));

foreach ($model->rowsall as $value) {
    echo '<tr >';
    //echo  '<td>';
    //echo $value['id_agenda'];
    //echo '</td>';
    echo '<td>';
    echo $value['cd_art'];
    echo '</td>';
    echo '<td>';
    echo $value['descrizione'];
    echo '</td>';
    echo '<td>';
echo $value['um'];
echo '</td>';

    echo '<td>';
    echo $value['qta'];
    echo '</td>';
    echo '<td>';
    echo $value['prezzo'];
    echo '</td>';
    //echo '<td>';
    //echo $value['note'];

    //  echo  Html::a('scarica',['agenda/genfile','id' => $value['id'],'file'=>str_replace(' ', '_',$value['nome_file']) ]);
//    echo '</td>';
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


<?php
//   echo '<table id="product-parcels" class="table table-condensed table-bordered">';
//  echo '<thead>';
//  echo '<tr>';
//  //echo '<th>id_agenda</th>';
//  echo '<th>cdFile_art</th>';
echo '<table id="product-files" class="table table-condensed table-bordered">';
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
 