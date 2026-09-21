 <div class="table-responsive-sm">
<?php
use app\models\Xtravelrow;
echo '<table    class="table table-sm"   >';
echo '<thead class="thead-dark" >';
echo '<tr>';
//echo '<th>id_agenda</th>';
//echo '<th width=50px >Evaso</th>';

//echo '<th>Cod. Art.</th>';
echo '<th  scope="col">';
echo 'Commessa';
echo '</th>';
echo '<th scope="col"> ';
echo 'Guest';
echo '</th>';
echo '<th scope="col">';
echo 'Cliente';
echo '</th>';
echo '<th scope="col">';
echo 'Descrizione';
echo '</th>';
echo '<th scope="col">';
echo 'Citta';
echo '</th>';
echo '<th scope="col">';
echo 'Cd.Ar';
echo '</th>';
echo '<th scope="col">';
echo 'Descrizione';
echo '</th>';
echo '<th scope="col">';
echo 'fornitore';
echo '</th>';
echo '<th scope="col">';
echo 'Descrizione';
echo '</th>';
echo '<th  scope="col">';
echo 'Struttura';
echo '</th>';
echo '<th scope="col">';
echo 'Da';
echo '</th>';
echo '<th  scope="col">';
echo 'A';
echo '</th>';
echo '<th scope="col">';
echo 'Check In';
echo '</th>';
echo '<th scope="col">';
echo 'Check Out';
echo '</th>';
echo '<th scope="col">';
echo 'Qta';
echo '</th>';
echo '<th scope="col">';
echo 'Pnr';
echo '</th>';
echo '<th scope="col">';
echo 'Nr Big.';
echo '</th>';
echo '<th scope="col">';
echo 'Prezzo';
echo '</th>';
echo '<th scope="col">';
echo 'Iva';
echo '</th>';
echo '<th scope="col">';
echo 'Tax';
echo '</th>';
echo '<th scope="col">';
echo 'Fee';
echo '</th>';
echo '<th scope="col">';
echo 'Imp.';
echo '</th>';
echo '<th scope="col">';
echo 'Tot.Generale';
echo '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

//yii::warning(var_dump($model->getrowssall()));
$t=Xtravelrow::find()
->select(['sottocommessa'
      ,'cd_Ar'
      ,'descrizione'
      ,'qta'
      ,'prezzo'
      ,'stato'
      ,'guest'
      ,'ruolo'
      ,'cd_cf_ft'
      ,'descli'
      ,'citta'
      ,'fornitore'
      ,'desfor'
      ,'struttura'
      ,'check_in'
      ,'check_out'
      ,'citta_da'
      ,'citta_a'
      ,'orario'
      ,'pnr'
      ,'nr_biglietto'
      ,'data_pg'
      ,'cd_pg'
      ,'contabile'
      ,'totale'
      ,'tax'
      ,'fee'
      ,'fee_perc'
      ,'imponibile'
      ,'iva'
      ,'Totalegenerale'
     // ,[evadi_A
     // ,[evadi_p]
      ,'tax_unit'
      ,'note'
      ,'descontab'
      ,'totfattura'
      ,'codiva'
      ,'pagato'
      //,[xid]
      //,[timeins]
     // ,[numero]
     // ,[datah]
      ,'x_scdesc'
      ,'x_pagato'])
->where(['th_id'=>$model->th_id
//$model->id
])
->asArray()
->all();
//yii::warning($t);
//$p=$model->rowsall;
foreach (//$model->rowsall
 $t as $value) {
    echo '<tr >';
echo '<td>';
echo $value['sottocommessa'];
echo '</td>';
echo '<td>';
echo $value['guest'];
echo '</td>';
echo '<td>';
echo $value['cd_cf_ft'];
echo '</td>';
echo '<td>';
echo $value['descli'];
echo '</td>';
echo '<td>';
echo $value['citta'];
echo '</td>';
echo '<td>';
echo $value['cd_Ar'];
echo '</td>';
echo '<td>';
echo $value['descrizione'];
echo '</td>';
echo '<td>';
echo $value['fornitore'];
echo '</td>';
echo '<td>';
echo $value['desfor'];
echo '</td>';
echo '<td>';
echo $value['struttura'];
echo '</td>';
echo '<td>';
echo $value['citta_da'];
echo '</td>';
echo '<td>';
echo $value['citta_a'];
echo '</td>';
echo '<td>';
echo $value['check_in'];
echo '</td>';
echo '<td>';
echo $value['check_out'];
echo '</td>';
echo '<td>';
echo $value['qta'];
echo '</td>';
echo '<td>';
echo $value['pnr'];
echo '</td>';
echo '<td>';
echo $value['nr_biglietto'];
echo '</td>';
echo '<td>';
echo $value['prezzo'];
echo '</td>';
echo '<td>';
echo $value['iva'];
echo '</td>';
echo '<td>';
echo $value['tax'];
echo '</td>';
echo '<td>';
echo $value['fee'];
echo '</td>';
echo '<td>';
echo $value['imponibile'];
echo '</td>';
echo '<td>';
echo $value['Totalegenerale'];
echo '</td>';
echo '</tr>';


}
echo '<tr  style="display: none;">';
echo '</tr>';
?>


</tbody>
</table>



 </div> 
 <div class="table-responsive-sm">
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
 </div>