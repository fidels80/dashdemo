<?php
$this->title = 'Home Page';
//$this->params['breadcrumbs'] = [['label' => $this->title]];
use app\models\doc_head;
use app\models\payments;
use practically\chartjs\Chart;
use yii\helpers\Url;
use app\models\User;
try {
$cf =Yii::$app->user->identity->cd_cli;
} catch (Exception $e) {
    $ris = (new \yii\db\Query())
    ->select(['cd_cli'])
    ->from('user')
    ->where(['id' => $usrid])
    ->one();
   
   
    $cf=$ris['cd_cli'];
}
yii::error($cf);

$altteste=
/*
doc_head::find()->select(['xid_testa','id'])->where(['altcli'=>$cd_cf])//->andwhere('is not',['altcli'=>null])
->limit(5)->all();
*/
  (new \yii\db\Query())
->select(['xid_testa'])
->from('doc_head')
->where(['=','altcli',$cd_cf])
->andwhere(['IS NOT', 'altcli', null])
->all();
yii::warning($altteste);

$totdoc = doc_head::find()->where(['cd_cli' => $cf])->count();
$totdocC = doc_head::find()->where(['confermato' => 1, 'cd_cli' => $cf])->count();
$totsc = payments::find()->where(['cd_cli' => $cf])->count();
$totsc_pagata = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '1'])->count();
$dapagare = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '0'])->sum('ImportoV');
$pagato = payments::find()->where(['cd_cli' => $cf, 'Pagata' => '1'])->sum('ImportoV');
$connection = Yii::$app->getDb();
$command = $connection->createCommand("select mese,sum(tot)as tot from (
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where cd_cli=:id_cli /*and year(DataScadenza)=2022 */and pagata=1
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

", [':id_cli' => $cf]);

$result = $command->queryAll();

$command2 = $connection->createCommand("select mese,sum(tot)as tot from (
select month(DataScadenza) as mese ,sum(importov) as tot from payments
where cd_cli=:id_cli /*and year(DataScadenza)=2022*/ and pagata<>1
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

", [':id_cli' => $cf]);
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

]
;

 
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
            <?=\hail812\adminlte\widgets\SmallBox::widget([
    'title' => $totdoc,
    'text' => 'Documenti Presenti in Archivio',
    'icon' => 'far fa-copy',
        'linkText'=>'Apri',
    'linkUrl'=> Url::to(['doc_head/index'])
])?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">


         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totdocC,
    'text' => 'Documenti Confermati',
    'icon' => 'far fa-copy',
    'theme' => 'success',
      'linkText'=>'Apri',
     'linkUrl'=> Url::to(['doc_head/index', 'filtra_confermato' => true]),
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Confermati',
    'theme' => 'warning',
    'size' => 'lg',
   
    'textSize' => 'lg',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totdoc - $totdocC,
    'text' => 'Documenti da Confermare',
    'icon' => 'far fa-copy',
    'theme' => 'Info',
    'linkText'=>'Apri',
    'linkUrl'=> Url::to(['doc_head/index', 'filtra_confermato' => false])

])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Non Confermati',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'sm',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
    </div>
   <!-- ///////////////////////////////////////////////////-->
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <?=\hail812\adminlte\widgets\SmallBox::widget([
    'title' => $totsc,
    'text' => 'Totali scadenze',
    'icon' => 'far fa-calendar',
            'linkText'=>'Apri',
    'linkUrl'=> Url::to(['payments/index'])
])?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">


         <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totsc_pagata,
    'text' => 'Scadenze Pagate',
    'icon' => 'far fa-calendar',
    'theme' => 'success',
                'linkText'=>'Apri',
    'linkUrl'=> Url::to(['payments/index', 'filtra_pagato' => true])
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Pagate',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'lg',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <?php $smallBox = \hail812\adminlte\widgets\SmallBox::begin([
    'title' => $totsc - $totsc_pagata,
    'text' => 'Scadenze da Pagare',
    'icon' => 'far fa-calendar',
    'theme' => 'Info',
                    'linkText'=>'Apri',
    'linkUrl'=> Url::to(['payments/index', 'filtra_pagato' => false])
])?>
            <?=\hail812\adminlte\widgets\Ribbon::widget([
    'id' => $smallBox->id . '-ribbon',
    'text' => 'Non Pagate',
    'theme' => 'warning',
    'size' => 'lg',
    'textSize' => 'sm',
])?>
            <?php \hail812\adminlte\widgets\SmallBox::end()?>
        </div>
    </div>
    <div class="row " >
    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card card-succes">
    <div class="card-header">
    <h3 class="card-title">Top Articoli</h3>
   
   <?php 
   $usrid = Yii::$app->user->Id;
$ris = user::find()
    ->select(['level', 'cd_cli', 'id'])
    ->where(['id' => $usrid])
    ->asArray()
    ->one();
$command2 = $connection->createCommand("select top 5  cd_art,descrizione,sum(qta)
 from doc_rows
where cd_cli=:id and cd_art is not null
and cd_art in
 (select  cd_ar from  adb_vivendasrl.dbo.ar where cd_argruppo1 is not null)
group by cd_art,descrizione
order by sum(qta) desc ")
->bindParam(':id', $ris['cd_cli']);
$resultart = $command2->queryAll();
//yii::error($resultart);
?>
   
   
   
    <div class="card-tools">
      <!-- Buttons, labels, and many other things can be placed here! -->
      <!-- Here is a label for example -->
      <span class="badge badge-primary">Articoli</span>
    </div>
    <!-- /.card-tools -->
  </div>
  <!-- /.card-header -->
  <?php 
  foreach($resultart as $value){
  echo '<div class="card-body">'.
 $value['descrizione'].
    '</div>';

  }
   ?> 
   

            </div>
            </div>
  <!-- /.card-body -->
  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
    <div class="card card-succes">
    <div class="card-header">
    <h3 class="card-title">Documenti</h3>
  <?php /*$series = [
    [
        'name' => 'Entity 1',
        'data' => [
            ['2018-10-04', 4.66],
            ['2018-10-05', 5.0],
        ],
    ],
    [
        'name' => 'Entity 2',
        'data' => [
            ['2018-10-04', 3.88],
            ['2018-10-05', 3.77],
        ],
    ],
    [
        'name' => 'Entity 3',
        'data' => [
            ['2018-10-04', 4.40],
            ['2018-10-05', 5.0],
        ],
    ],
    [
        'name' => 'Entity 4',
        'data' => [
            ['2018-10-04', 4.5],
            ['2018-10-05', 4.18],
        ],
    ],
];
 
echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'bar', // default area
    'height' => '400', // default 350
    'width' => '500', // default 100%
    'chartOptions' => [
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],
        ],
        'xaxis' => [
            'type' => 'datetime',
            // 'categories' => $categories,
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
        ],
    ],
    'series' => $series,
]);


*/
//$pie=['Confermato'=>5,'In attesa'=>2,'Rifutato'=>8];

$command3 = $connection->createCommand("select count(*) as tot
 from doc_head
where cd_cli=:id and confermato=1 ")
    ->bindParam(':id', $ris['cd_cli']);
$resulconf = $command3->queryAll();
$command4 = $connection->createCommand("select count(*) as tot
 from doc_head
where cd_cli=:id and (confermato is null or confermato=0)  ")
    ->bindParam(':id', $ris['cd_cli']);
$resulwait = $command4->queryAll();
$command5 = $connection->createCommand("select count(*) as tot
 from doc_head
where cd_cli=:id and rifiutato=1 ")
    ->bindParam(':id', $ris['cd_cli']);
$resulcanc = $command5->queryAll();
 
$command6 = $connection->createCommand("select count(*) as tot from (
select distinct  (xid_testa)
 from doc_rows
where cd_cli=:id and f_row is not null
) as t")
    ->bindParam(':id', $ris['cd_cli']);
$resuleva = $command6->queryAll();



$pie=[];
/*$pie[]=array('Confermati'=>intval($resulconf[0]['tot']));
$pie[]=array('In attesa'=>intval($resulwait[0]['tot']));
$pie[]=array('Rifiutati'=>intval($resulcanc[0]['tot']));
$pie[]=array('Evasi'=>intval($resuleva[0]['tot']));
*/

//$pie[]='pagate'=>500;

$pie[]=intval($resulwait[0]['tot']);
$pie[] = intval($resulconf[0]['tot']);
$pie[] = intval($resuleva[0]['tot']);

$pie[]=intval($resulcanc[0]['tot']);


$conf=$resulconf[0]['tot'];

//$pie=[$conf,6,7,8,9];
yii::warning($pie);

//yii::warning($resulconf);
//yii::warning($resulwait);
//yii::warning($resulcanc);
//yii::warning($resuleva);


echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'pie', // default area
    //'height' => '400', // default 350
   // 'width' => '500', // default 100%
 
    'chartOptions' => [
           'labels'=>['In attesa','Confermato','Evaso','Rifiutato'],
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

  <!-- /.card-footer -->
<div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
    <h3 class="card-title">Scadenze</h3>
<?php
/*
$series= [
    'data'=> [
    [  'x'=> strtotime('2018-02-12'),
      'y'=> 76
    ],[
      'x'=> strtotime('2018-02-12') ,
      'y'=> 76
    ]
  ], 
  'xaxis'=>[
    'type'=> 'datetime'
  ],
  ];*/
echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'bar', // default area
    //'height' => '400', // default 350
    //'width' => '500', // default 100%
    'chartOptions' => [
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],
        ],
        'xaxis' => [
            //'type' => 'datetime',
            // 'categories' => $categories,
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
        ],
    ],
    'series' => $series,
]);

?>


</div>
      </div>
            </div>
    </div>
</div>
