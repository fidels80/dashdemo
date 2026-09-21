 
<?php
use app\models\doc_rows;
use app\models\Anacli;
use app\models\User;
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select([ 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$usrcfid = $ris['cd_cli'];
$cfprice=Anacli::find()->select(['showprices','show_ins_nrgaz'])->where(['cd_cli'=>$usrcfid])->one();
 
yii::warning($cfprice['show_ins_nrgaz']);
echo '<table id="product-parcels" class="table table-condensed table-bordered" >';
echo '<thead>';
echo '<tr>';
//echo '<th>id_agenda</th>';
//echo '<th width=50px >Evaso</th>';

//echo '<th>Cod. Art.</th>';
echo '<th>Descrizione</th>';
echo '<th>Data Pubblicazione</th>';
if($cfprice['show_ins_nrgaz']==1){
echo '<th>Nr. Gazzetta</th>';
echo '<th>Nr. Inserzione</th>';
}
echo '<th>U.M.</th>';

echo '<th>Qta</th>';
if ($cfprice['showprices']==1){
echo '<th>Prz.Unit</th>';

echo '<th>Prezzo Tot </th>';
echo '<th>Sconto</th>';
echo '<th>Prezzo Scont.</th>';
//echo '<th>note</th>';
echo '<th>Al. Iva</th>';
}
echo '<th>File</th>';

echo '</tr>';
echo '</thead>';
echo '<tbody>';

//yii::warning(var_dump($model->getrowssall()));
$t=doc_rows::find()
->select(['descrizione','datacons','nrgazzetta','nrinserzione','um','qta','prezzo','iva','cd_doc','xid_riga',
'totale','prz_tot','sconto'//,'nriga'
])
->where(['doc_head_id'=>$model->id])
->orderBY('nriga',SORT_DESC)
->asArray()
->all();
//yii::warning($t);
//$p=$model->rowsall;
foreach (//$model->rowsall
 $t as $value) {
    echo '<tr >';

// echo  '<td>';

//echo  $value['cd_art'];

//  echo '</td>';
echo '<td>';
echo $value['descrizione'];
echo '</td>';
echo '<td>';
if ($value['cd_doc']<>'FTV'){
if (date("d/m/Y",strtotime($value['datacons']))<>'01/01/1970'){
    echo date("d/m/Y",strtotime($value['datacons']));
}
}
echo '</td>';
if($cfprice['show_ins_nrgaz']==1){
echo '<td>';
echo $value['nrgazzetta'];
echo '</td>';
echo '<td>';
echo $value['nrinserzione'];
echo '</td>';
}
echo '<td>';
echo $value['um'];
echo '</td>';
echo '<td>';
echo number_format($value['qta'], 2);

//  echo  Html::a('scarica',['agenda/genfile','id' => $value['id'],'file'=>str_replace(' ', '_',$value['nome_file']) ]);
echo '</td>';

//echo number_format($value['prezzo']/($value['qta']==0?1:$value['qta']), 2,',','.') .' €';

    $ints = yii::$app->db2
    ->createCommand('select * from ADB_VIVENDASRL.dbo.dorig where  id_dorig=:id_dorig'
    )->bindValues([':id_dorig' =>$value['xid_riga']]);
$ints->execute();
$prz = $ints->queryAll();
if ($cfprice['showprices'] == 1) {
    echo '<td>';
    if (isset($prz[0]['PrezzoUnitarioV']) || is_null($value['prezzo'])) {
        echo number_format($prz[0]['PrezzoUnitarioV'] ?? 0, 2, ',', '.') . ' €';
    } else {
        echo number_format($value['prezzo'], 2, '.', ',') . ' €' ;
    }
    echo '</td>';
    echo '<td>';
    if (isset($prz[0]['PrezzoUnitarioV']) || is_null($value['totale'])) {
        echo number_format(($value['qta']?? 0) * ($prz[0]['PrezzoUnitarioV']?? 0), 2, ',', '.') . ' €';
    } else {
        echo number_format($value['totale'], 2, ',', '.') . ' €';

    }
    echo '</td>';
    echo '<td>';
    if (isset($prz[0]['ScontoRiga']) || is_null($value['sconto'])) {
        echo $prz[0]['ScontoRiga']?? null;
    } else {
        echo $value['sconto'];
    }
    echo '</td>';
    echo '<td>';

    echo number_format($value['prz_tot'], 2, ',', '.') . ' €';

    echo '</td>';
    echo '<td>';
    echo $value['iva'];
    echo '</td>';
}

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
 