<?php
$this->title = '';
//$this->params['breadcrumbs'] = [['label' => $this->title]];
use app\models\doc_head;
use app\models\payments;
use app\models\User;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use yii\helpers\Html;

try {
    $cf = Yii::$app->user->identity->cd_cli;
} catch (Exception $e) {
    $usrid= '';
    //Yii::$app->user->identity->id;
    $ris = (new \yii\db\Query())
        ->select(['cd_cli', 'email', 'username', 'piva'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();

    $cf = $ris['cd_cli'] ?? '';
}
//yii::error($cf);

$altteste =
/*
doc_head::find()->select(['xid_testa','id'])->where(['altcli'=>$cd_cf])//->andwhere('is not',['altcli'=>null])
->limit(5)->all();
 */
(new \yii\db\Query())
    ->select(['xid_testa'])
    ->from('doc_head')
    ->where(['=', 'altcli', $cf])
    ->andwhere(['IS NOT', 'altcli', null])
    ->all();

//yii::warning($altteste);
$filtro = [];
foreach ($altteste as $value) {
    $filtro[] = $value['xid_testa'];
}
//yii::warning($filtro);

$totdoc = doc_head::find()->where(
    "(cd_cli='" . $cf . "' or altcli='" . $cf . "')"

    //['cd_cli' => $cf]
    //,['or','altcli'=>$cf]
)
//->andwhere(['not like','cd_doc','F%',false])
    ->count();
$totdocC = doc_head::find()->where(['confermato' => 1, 'cd_cli' => $cf])->count();
$toddocConf = doc_head::find()->where(
    "(cd_cli='" . $cf . "' or altcli='" . $cf . "')")
    ->andwhere(['not like', 'cd_doc', 'F%', false])
    ->count();
$totsc = payments::find()->where(['cd_cli' => $cf])->orwhere(['IN', 'xid_testa', $filtro])->count();
$totsc_pagata = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '1'])->orwhere(['and', ['IN', 'xid_testa', $filtro], ['Pagata' => '1']])->count();
$connection = Yii::$app->getDb();

$command = $connection->createCommand("
select isnull(sum(importov),0) as imp from payments where Pagata<>1 and datascadenza<=getdate() and cd_cli=:id_cli
", [':id_cli' => $cf]);


$imppagare = $command->queryAll();
//yii::warning($imppagare);
$importodapagare= $imppagare[0]['imp'];
//yii::warning($importodapagare);

$dapagare = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '0'])->orwhere(['and', ['IN', 'xid_testa', $filtro], ['Pagata' => '0']])->sum('ImportoV');
$pagato = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '1'])->orwhere(['and', ['IN', 'xid_testa', $filtro], ['Pagata' => '1']])->sum('ImportoV');
//$connection = Yii::$app->getDb();
$command = $connection->createCommand("select mese,sum(tot)as tot from (
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where cd_cli=:id_cli /*and year(DataScadenza)=2022 */
and pagata=1 --or (xid_testa in (select xid_testa from doc_head where altcli=:id_cli) and pagata=1)

group by year(DataScadenza),month(DataScadenza)
union
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where (payments.xid_testa in (select xid_testa from doc_head where altcli=:id_cli2) and payments.pagata=1)
group by year(DataScadenza),month(DataScadenza)

union
select 1 as mese,0
union
select 2 as mese,0
union
select 3 as mese,0
union
select 4 as mese,0
union
select 5 as mese,0
union
select 6 as mese,0
union
select 7 as mese,0
union
select 8 as mese,0
union
select 9 as mese,0
union
select 10 as mese,0
union
select 11 as mese,0
union
select 12 as mese,0
) as t
group by mese

", [':id_cli' => $cf,':id_cli2'=>$cf]);

$result = $command->queryAll();

$command2 = $connection->createCommand("select mese,sum(tot)as tot from (
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where (cd_cli=:id_cli /*and year(DataScadenza)=2022*/ and pagata<>1)
--or (payments.xid_testa in (select xid_testa from doc_head where altcli=:id_cli) and payments.pagata<>1)
group by year(DataScadenza),month(DataScadenza)
union
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where (payments.xid_testa in (select xid_testa from doc_head where altcli=:id_cli2) and payments.pagata<>1)
group by year(DataScadenza),month(DataScadenza)

union
select 1 as mese,0
union
select 2 as mese,0
union
select 3 as mese,0
union
select 4 as mese,0
union
select 5 as mese,0
union
select 6 as mese,0
union
select 7 as mese,0
union
select 8 as mese,0
union
select 9 as mese,0
union
select 10 as mese,0
union
select 11 as mese,0
union
select 12 as mese,0
) as t
group by mese

", [':id_cli' => $cf,':id_cli2'=>$cf]);
$result2 = $command2->queryAll();
$righe = [];
$righe2 = [];
foreach ($result as $value) {
    $righe[] = $value['tot'];
}
foreach ($result2 as $value) {
    $righe2[] = $value['tot'];
}

$series = [

    [
        'name' => "Pagate",
        'data' => $righe, //[45,46,1,3,0,0,0,0,0,1,1,1]
    ],
    [
        'name' => 'non Pagate',
        'data' => $righe2,

    ],
    [
        'name' => 'ciroPagate',
        'data' => $righe2,

    ],

]
;
//yii::warning($series);
?>
<div class="container-fluid">
<!--
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <?=\hail812\adminlte\widgets\InfoBox::widget([
    'text' => 'File Presenti',
    'number' => '50 <small></small>',
    'icon' => 'fas fa-copy',
])?>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <?=\hail812\adminlte\widgets\InfoBox::widget([
    'text' => 'Saldo Da Pagare',
    'number' => round($dapagare, 2) . ' <small></small>',
    'icon' => 'fas fa-calendar',
])?>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <?=\hail812\adminlte\widgets\InfoBox::widget([
    'text' => 'Saldato',
    'number' => round($pagato, 2) . ' <small></small>',
    'icon' => 'fas fa-calendar',
])?>
        </div>
    </div>

            -->


    <div class="row">

  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card card-succes">
    <div class="card-header">
    <h3 class="card-title">Documenti</h3>
  <?php

  $usrid = Yii::$app->user->Id;
$ris = user::find()
    ->select(['level', 'cd_cli', 'id'])
    ->where(['id' => $usrid])
    ->asArray()
    ->one();

$pie_command3 = $connection->createCommand("select count(*) as tot
 from doc_head
where cd_cli=:id and confermato=1 ")
    ->bindParam(':id', $ris['cd_cli']);
$pie_resulconf = $pie_command3->queryAll();
$pie_command4 = $connection->createCommand("select count(*) as tot
 from doc_head
where (cd_cli=:id or altcli=:id2) and (confermato is null or confermato=0)  ")
    ->bindParam(':id', $ris['cd_cli'])
    ->bindParam(':id2', $ris['cd_cli'])

;
$pie_resulwait = $pie_command4->queryAll();
$pie_command5 = $connection->createCommand("select count(*) as tot
 from doc_head
where cd_cli=:id and rifiutato=1 ")
    ->bindParam(':id', $ris['cd_cli']);
$pie_resulcanc = $pie_command5->queryAll();

$pie_command6 = $connection->createCommand("select count(*) as tot from (
select distinct  (xid_testa)
 from doc_rows
where cd_cli=:id and f_row is not null
) as t")
    ->bindParam(':id', $ris['cd_cli']);
$pie_resuleva = $pie_command6->queryAll();

$pie = [];
/*$pie[]=array('Confermati'=>intval($resulconf[0]['tot']));
$pie[]=array('In attesa'=>intval($resulwait[0]['tot']));
$pie[]=array('Rifiutati'=>intval($resulcanc[0]['tot']));
$pie[]=array('Evasi'=>intval($resuleva[0]['tot']));
 */

//$pie[]='pagate'=>500;

$pie[] = intval($pie_resulwait[0]['tot']);
$pie[] = intval($pie_resulconf[0]['tot']);
$pie[] = intval($pie_resuleva[0]['tot']);

$pie[] = intval($pie_resulcanc[0]['tot']);

$conf = $pie_resulconf[0]['tot'];

//$pie=[$conf,6,7,8,9];
//yii::warning($pie);

//yii::warning($resulconf);
//yii::warning($resulwait);
//yii::warning($resulcanc);
//yii::warning($resuleva);

echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'pie', // default area
    //'height' => '400', // default 350
    // 'width' => '500', // default 100%

    'chartOptions' => [
        'labels' => ['In attesa', 'Confermato', 'Evaso', 'Rifiutato'],
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],

        ],

    ],
    'series' => $pie,

]);

?>
</div>

            </div>
            </div>

        <div class="col-lg-2 col-md-6 col-sm-6 col-12">
            <?=\hail812\adminlte\widgets\SmallBox::widget([
    'title' => $totdoc,
    'text' => 'Documenti  in Archivio',
    'icon' => 'far fa-copy',
    'theme' => 'success',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['doc_head/index']),
])?><br><br><br>
        <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $toddocConf,
    'text' => 'Documenti Confermabili',
    'icon' => 'far fa-copy',
    //'theme' => 'Info',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['doc_head/index', 'filtra_confermato' => false]),

])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Confermabili',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'sm',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>

        <div class="col-lg-2 col-md-6 col-sm-6 col-12">


         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totdocC,
    'text' => 'Documenti Confermati',
    'icon' => 'far fa-copy',
    'theme' => 'success',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['doc_head/index', 'filtra_confermato' => true]),
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => '   Confermati   ',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'sm',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
            <br><br><br>
         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $toddocConf - $totdocC,
    'text' => 'Documenti da Confermare',
    'icon' => 'far fa-copy',
    'theme' => 'warning',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['doc_head/index', 'filtra_confermato' => false]),

])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Non Confermati',
    'theme' => 'info',
    'size' => 'lg',
    'textSize' => 'sm',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
 <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card card-succes" >
        <!--style="background-color:#28a745 !important"-->
    <div class="card-header">
    
    <table style="border-collapse: collapse; width: 100%;" border="0"><colgroup>
    <col style="width: 50%;"><col style="width: 50%;"></colgroup>
 <tbody>
 <tr>
 <td style="width: 80%;">
    <h3 class="card-title">Top Articoli</h3>
    </td>
 <td style="width: 20%;">  <h3 class="card-title">Qtà con Um</h3>
 </td>
 </tr>
 </tbody>
 </table>
   <?php
$usrid = Yii::$app->user->Id;
$ris = user::find()
    ->select(['level', 'cd_cli', 'id'])
    ->where(['id' => $usrid])
    ->asArray()
    ->one();
    if ($ris['cd_cli']<>'C001443'){  
$command2 = $connection->createCommand("select top 5
doc_rows.cd_art,doc_rows.descrizione,sum(doc_rows.qta) as t,um
 from doc_rows
where cd_cli=:id and cd_art is not null
and cd_art in
 (select  cd_ar from  adb_vivendasrl.dbo.ar where cd_argruppo1 is not null)
group by cd_art,descrizione,um
order by sum(qta) desc ")
    ->bindParam(':id', $ris['cd_cli']);
$resultart = $command2->queryAll();
    }else{
$command2 = $connection->createCommand("select top 5
doc_rows.cd_art,doc_rows.descrizione,count(doc_rows.cd_art) as t,um
 from doc_rows
where cd_cli=:id and cd_art is not null
and cd_art in
 (select  cd_ar from  adb_vivendasrl.dbo.ar where cd_argruppo1 is not null)
group by cd_art,descrizione,um
order by sum(qta) desc ")
    ->bindParam(':id', $ris['cd_cli']);
$resultart = $command2->queryAll();



    }
//yii::error($resultart);
?>



    <div class="card-tools">
      <!-- Buttons, labels, and many other things can be placed here! -->
      <!-- Here is a label for example -->
      <!-- <span class="badge badge-primary">Articoli</span>-->
    </div>
    <!-- /.card-tools -->
  </div>
  <!-- /.card-header -->

  <?php

foreach ($resultart as $value) {
    echo '<div class="card-body">' .
    '<table style="border-collapse: collapse; width: 100%;" border="0"><colgroup><col style="width: 50%;"><col style="width: 50%;"></colgroup>
 <tbody >
 <tr>
 <td  style="width: 80%;">' . $value['descrizione'] . '</td>
 <td style="width: 20%;">' . number_format($value['t'], 0) . ' ' . $value['um'] . '</td>
 </tr>
 </tbody>
 </table>' .
        '</div>';
}
?>
            </div>
            </div>
    </div>
   <!-- ///////////////////////////////////////////////////-->
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
    <h3 class="card-title">Scadenze</h3>
<?php

echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'bar', // default area
    'chartOptions' => [
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],
        ],
        'xaxis' => [
            'categories' => ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set'
                , 'Ott', 'Nov', 'Dic'],

        ],
        'plotOptions' => [
            'bar' => [
                'horizontal' => false,
                'endingShape' => 'rounded',
            ],
        ],
        'dataLabels' => [
            'enabled' => false,
        ],
        'stroke' => [
            'show' => true,
            'colors' => ['transparent'],
        ],
        'legend' => [
            'verticalAlign' => 'bottom',
            'horizontalAlign' => 'left',
            //'floating'=> 'true'
        ],
    ],
    'series' => $series,
]);
 
//yii::warning($dapagare);
?>

</div>
      </div>
            </div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-12">
            <?=\hail812\adminlte\widgets\SmallBox::widget([
    'title' => $totsc,
    'text' => 'Totali scadenze',
    'icon' => 'far fa-calendar',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['payments/index']),
])?>
  <br><br><br>
        <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totsc - $totsc_pagata,
    'text' => 'Scadenze da Pagare',
    'icon' => 'far fa-calendar',
    'theme' => 'Info',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['payments/index', 'filtra_pagato' => false]),
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Non Pagate',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'lg',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
</div>
        <div class="col-lg-2 col-md-6 col-sm-6 col-12">


         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totsc_pagata,
    'text' => 'Scadenze Pagate',
    'icon' => 'far fa-calendar',
    'theme' => 'success',
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['payments/index', 'filtra_pagato' => true]),
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Pagate',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'lg',
])?>
</div><br><br><br><div>
         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' =>$dapagare,
    'text' => 'Importo da Pagare',
    'icon' => 'far fa-calendar',
    'theme' => 'warning',
 
    'linkText' => 'Apri',
    'linkUrl' => Url::to(['payments/index', 'filtra_pagato' => false]),
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'A Pagare',
    'theme' => 'info',
    'size' => 'lg',
    'textSize' => 'lg',
])?>

            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-succes">
    <div class="card-header">
    <table style="border-collapse: collapse; width: 100%;" border="0"><colgroup>
    <col style="width: 50%;"><col style="width: 50%;"></colgroup>
 <tbody>
 <tr>
 <td style="width: 80%;">
    <h3 class="card-title">Prossime pubblicazioni</h3>
    </td>
 <td style="width: 20%;">  <h3 class="card-title">data</h3>
 </td>
 </tr>
 </tbody>
 </table>
 <?php
$usrid = Yii::$app->user->Id;
$ris = user::find()
    ->select(['level', 'cd_cli', 'id','ischief'])
    ->where(['id' => $usrid])
    ->asArray()
    ->one();
$command2 = $connection->createCommand("select top 5
doc_rows.cd_art,doc_rows.descrizione, FORMAT (datacons , 'dd/MM/yyyy ') as datacons from doc_rows
where cd_cli=:id and cd_art is not null and datacons is not null
and cd_art in
 (select  cd_ar from  adb_vivendasrl.dbo.ar where cd_argruppo1 is not null)
 
order by datacons desc ")
    ->bindParam(':id', $ris['cd_cli']);
$resultart = $command2->queryAll();
//yii::error($resultart);
?>



    <div class="card-tools">
      <!-- Buttons, labels, and many other things can be placed here! -->
      <!-- Here is a label for example -->
      <!-- <span class="badge badge-primary">Articoli</span>-->
    </div>
    <!-- /.card-tools -->
  </div>
  <!-- /.card-header -->

  <?php

foreach ($resultart as $value) {
    echo '<div class="card-body">' .
    '<table style="border-collapse: collapse; width: 100%;" border="0"><colgroup><col style="width: 50%;"><col style="width: 50%;"></colgroup>
 <tbody>
 <tr>
 <td style="width: 80%;">' . $value['descrizione'] . '</td>
 <td style="width: 20%;">' .  $value['datacons'] . '</td>
 </tr>
 </tbody>
 </table>' .
        '</div>';
 
    }
?>

      <?php //  yii::$app->runAction('user/cf', ['id' => Yii::$app->user->identity->Id]);
    //'op' => $mmodel->className() . '-->' . $this->action->id]);
?>

            </div>
                      </div>
    </div>
  <?php
if (($ris['ischief'] ?? 0) == 1) {
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
    $url = Url::to(['cf', 'id' => Yii::$app->user->identity->id,
//   'mod'->$model
    ]);

    echo Html::button('Cambia Azienda ('.$ris['cd_cli'].')', ['value' => $url,
        'class' => 'btn btn-warning', 'id' => 'modalcli_' . $tmpid]);
    // echo'</td><td width="30%">';
}
?>
            </div>
          
  
</div>
