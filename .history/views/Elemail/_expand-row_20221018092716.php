 
<?php
use app\models\elemail;

/*
function recurSearch($array, $depth = 0)
{
    echo ('Current depth : ' . $depth);
    foreach ($array as $item) {
        echo ('id : ' . $item['id']);
        echo ('question title : ' . $item['question_title']);
        echo ('step : ' . $item['step']);

        if (count($item['Children']) > 0) {
            echo 'child : ';
            echo '{';
            /*
             * If $item['Children'] isn't empty,
             * the function will be called again
             * using $item['Children'] as main array
             * That function will keep being called until $item['Children'] is empty
             
            recurSearch($item['Children'], $depth + 1);
            echo '}';
        } else {
            echo ('child : no child');
        }
    }
}

*/

function print_children($id)
{
    $children =array('email'=>'a','id'=>5);// elemail::find()->where(['id_padre'=>$id])->all();
    //query("SELECT * FROM `table` WHERE `parentId` = " . (int) $id);
    if (!empty($children)) {
        echo '<ul>';
        foreach ($children as $child) {
            echo '<li>' . 'pino';//$child['email'];
            print_children(4);
                //$child['id']);
            echo '</li>';
        }
        echo '</ul>';
    }
}

print_children(0);














echo '<table id="product-parcels" class="table table-condensed table-bordered" >';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
//echo '<th width=50px >Evaso</th>';

//echo '<th>Cod. Art.</th>';
echo '<th>Descrizione</th>';
echo '<th>Data Consegna</th>';
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

foreach ($model->child as $value) {
    echo '<tr >';

// echo  '<td>';

//echo  $value['cd_art'];

//  echo '</td>';
echo '<td>';
echo $value['nome'];
echo '</td>';
echo '<td>';
echo $value['email'];
echo '</td>';
echo '<td>';
echo $value['Soggetto'];
echo '</td>';
echo '<td>';
echo $value['Corpo'];
echo '</td>';

echo '<td>';
echo $value['data'];
echo '</td>';
echo '<td>';
echo number_format($value['status'], 2);

//  echo  Html::a('scarica',['agenda/genfile','id' => $value['id'],'file'=>str_replace(' ', '_',$value['nome_file']) ]);
echo '</td>';
echo '<td>';
echo number_format($value['letto'], 2);
echo '</td>';
echo '<td>';
echo $value['id_padre'];
echo '</td>';
echo '<td>';
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
/*foreach ($model->filesall as $value) {
    // echo '<tr>';
    
    if ($value['entita'] == 'DOTES') {
        echo '<tr>';
        echo '<td>';
        echo $value['nomefile'];
        echo '</td>';
          echo '</tr>';
    }

}*/
echo '<tr id="files-new-parcel-block" style="display: none;">';
echo '</tr>';
echo '</tbody>';
echo '</table>';

?>
 