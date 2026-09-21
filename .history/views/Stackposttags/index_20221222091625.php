<?php

use yii\grid\GridView;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Modal;
use yii\helpers\Url;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\StackposttagsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stackposttags';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stackposttags-index">

    <h1><?=Html::encode($this->title)?></h1>

    <p>
         <div class="row">

  <div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
<h3 class="card-title">Ripartizione Domande</h3>
<?php
$connection = Yii::$app->getDb();

$command = $connection->createCommand("select  sum(cast(somma as int)) as somma
,'yii' as fw
from stack_post_tags
where tagname like '%yii%'
union
select sum(cast(somma as int)) as somma
,'node.js' as fw
from stack_post_tags
where tagname like '%node.js%'
union
select sum(cast(somma as int)) as somma
,'react-native' as fw
from stack_post_tags
where tagname like '%react-native%'
 and tagname like '%native%'
union
select sum(cast(somma as int)) as somma
,'vue' as fw
from stack_post_tags
where tagname like '%vue%'
union
select sum(cast(somma as int)) as somma
,'flutter' as fw
from stack_post_tags
where tagname like '%flutter%'
union
select sum(cast(somma as int)) as somma
,'react.js' as fw
from stack_post_tags
where tagname like '%react-%'
and tagname not like '%native%'
 union
select sum(cast(somma as int)) as somma
,'ionic' as fw
from stack_post_tags
where tagname like '%ionic%'
union
select sum(cast(somma as int)) as somma
,'Xamarin' as fw
from stack_post_tags
where tagname like '%Xamarin%'
union
select sum(cast(somma as int)) as somma
,'Onsen' as fw
from stack_post_tags
where tagname like '%Onsen%'
union
select sum(cast(somma as int)) as somma
,'Cordova' as fw
from stack_post_tags
where tagname like '%Cordova%'


");
$sof = $command->queryAll();
//yii::warning($sof);

$pie = [];
$labels = [];
foreach ($sof as $value) {
    $pie[] = intval($value['somma']);
    $labels[] = $value['fw'];
}
//yii::warning($pie);

echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'pie', // default area
    //'height' => '400', // default 350
    // 'width' => '500', // default 100%

    'chartOptions' => [
        'labels' => $labels,
        // ['In attesa', 'Confermato', 'Evaso', 'Rifiutato'],
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
</div></div></div>
 <div class="col-lg-8 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
        <h3 class="card-title">Domande 2022</h3>
        <?php
$series = [];
$command = $connection->createCommand("select

--mese ,
count(tagname) as SOMMA--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%node.js%'
group by mese
order by cast(mese as int ) asc
");
$node = $command->queryAll();
$xnode = [];
foreach ($node as $value) {
    $xnode[] = $value['SOMMA'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as SOMMA--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%yii%'
group by mese
order by cast(mese as int ) asc
");
$xyii = $command->queryAll();
$xxyii = [];
foreach ($xyii as $value) {
    $xxyii[] = $value['SOMMA'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%react-native%'
group by mese
order by cast(mese as int ) asc
");
$rn = $command->queryAll();

$crn = [];
foreach ($rn as $value) {
    $crn[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%vue%'
group by mese
order by cast(mese as int ) asc
");
$vue = $command->queryAll();

$xvue = [];
foreach ($vue as $value) {
    $xvue[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%flutter%'
group by mese
order by cast(mese as int ) asc
");
$flutter = $command->queryAll();

$xflutter = [];
foreach ($flutter as $value) {
    $xflutter[] = $value['somma'];
}
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%react-%'
AND  tagname not like '%native%'
group by mese
order by cast(mese as int ) asc
");
$react_ = $command->queryAll();

$xreact_ = [];
foreach ($react_ as $value) {
    $xreact[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%ionic%'
group by mese
order by cast(mese as int ) asc
");
$ionic = $command->queryAll();

$xionic = [];
foreach ($ionic as $value) {
    $xionic[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%xamarin%'
group by mese
order by cast(mese as int ) asc
");
$xamarin = $command->queryAll();

$xxamarin = [];
foreach ($xamarin as $value) {
    $xxamarin[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%onsen%'
group by mese
order by cast(mese as int ) asc
");
$onsen = $command->queryAll();

$xonsen = [];
foreach ($onsen as $value) {
    $xonsen[] = $value['somma'];
}
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022
AND  tagname like '%cordova%'
group by mese
order by cast(mese as int ) asc
");
$cordova = $command->queryAll();

$xcordova = [];
//$xcordova[]=0;
foreach ($cordova as $value) {
    $xcordova[] = $value['somma'];
}
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno=2022 and mese <=11
AND  tagname like '%angular%' 
--and 1=2
group by mese
order by cast(mese as int ) asc
");
$angular = $command->queryAll();

$xangular = [];
//$xangular[] = 0;
foreach ($angular as $value) {
    $xangular[] = $value['somma'];
}

yii::warning($xangular);
$series = [


    [
        'name' => 'php_yii',
        'data' => $xxyii,

    ],
        [
        'name' => "node.js",
        'data' => $xnode, //
        // [45,46,1,3,0,0,0,0,0,1,1,1]
    ],
    [
        'name' => 'react-native',
        'data' => $crn,

    ],
    [
        'name' => 'vue',
        'data' => $xvue,

    ],
    ['name' => 'flutter',
        'data' => $xflutter,
    ],
    [
        'name' => 'react',
        'data' => $xreact,

    ],
    [
        'name'=>'ionic',
        'data'=>$xionic,
    ],
    [
        'name'=>'xamarin',
        'data'=>$xxamarin
    ],
    [
        'name'=>'onsen',
        'data'=>$xonsen
    ],
    [
        'name'=>'cordova',
        'data'=>$xcordova
    ],
    [
        'name'=>'angular',
        'data'=>$xangular
    ],


]
;
//yii::warning($series);

echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'line', // default area
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
    /*    'plotOptions' => [
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
        ],*/
    ],
    'series' => $series,
]);

?>
 
</div></div></div></div>
</row>
<?php 
$series = [];
$command = $connection->createCommand("select

--mese ,
count(tagname) as SOMMA--//,tagname

from

post_tag_timeline
where
-- anno=2022
--AND 
 tagname like '%node.js%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$node = $command->queryAll();
$xnode = [];
foreach ($node as $value) {
    $xnode[] = $value['SOMMA'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as SOMMA--//,tagname

from

post_tag_timeline
where
-- anno=2022
--AND 
 tagname like '%yii%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$xyii = $command->queryAll();
$xxyii = [];
foreach ($xyii as $value) {
    $xxyii[] = $value['SOMMA'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%react-native%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$rn = $command->queryAll();

$crn = [];
 $crn[]=0;
  $crn[]=0;
foreach ($rn as $value) {
    $crn[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%vue%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$vue = $command->queryAll();

$xvue = [];
foreach ($vue as $value) {
    $xvue[] = $value['somma'];
}

$command = $connection->createCommand("

select
--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where anno>=2017
AND  
tagname like '%flutter%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$flutter = $command->queryAll();


$xflutter = [];
$xflutter[]=0;
$xflutter[]=0;
$xflutter[] = 0;
$xflutter[]=1;
$xflutter[] = 0;
$xflutter[] = 0;
$xflutter[] = 0;
$xflutter[] = 1;
$xflutter[] = 0;
$xflutter[] = 0;
$xflutter[] = 1;
$xflutter[] = 0;
$xflutter[] = 2;
$xflutter[] = 2;
$xflutter[] = 0;
$xflutter[] = 0;
$xflutter[] = 0;
$xflutter[] = 2;
$xflutter[] = 2;
$xflutter[] = 0;
$xflutter[] = 4;
$xflutter[] = 0;
$xflutter[] = 2;
$xflutter[] = 4;

foreach ($flutter as $value) {
    $xflutter[] = $value['somma'];
}
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where 
-- anno=2022
--AND 
 tagname like '%react-%'
 and tagname not like '%native%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$react_ = $command->queryAll();

$xreact_ = [];
//yii::error($xreact_);
 $xreact=[];
foreach ($react_ as $value) {
    $xreact[] = $value['somma'];
}
//yii::error($xreact);
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%ionic%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$ionic = $command->queryAll();

$xionic = [];
foreach ($ionic as $value) {
    $xionic[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%xamarin%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$xamarin = $command->queryAll();

$xxamarin = [];
foreach ($xamarin as $value) {
    $xxamarin[] = $value['somma'];
}

$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%onsen%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$onsen = $command->queryAll();

$xonsen = [];
foreach ($onsen as $value) {
    $xonsen[] = $value['somma'];
}
$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND  
tagname like '%cordova%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$cordova = $command->queryAll();

$xcordova = [];
//$xcordova[] = 0;
foreach ($cordova as $value) {
    $xcordova[] = $value['somma'];
}



$command = $connection->createCommand("select

--mese ,
count(tagname) as somma--//,tagname

from

post_tag_timeline
where --anno=2022
--AND
-- (anno<>2022 and mese<>12) and 
tagname like '%angular%'
group by anno ,mese
order by cast(anno as int) asc ,cast(mese as int ) asc");
$angular = $command->queryAll();

$xangular = [];
//$xcordova[] = 0;
foreach ($angular as $value) {
    $xangular[] = $value['somma'];
}




//yii::warning($rn);
$series = [

    [
        'name' => 'php_yii',
        'data' => $xxyii,

    ],
    [
        'name' => "node.js",
        'data' => $xnode, //
        // [45,46,1,3,0,0,0,0,0,1,1,1]
    ],
    [
        'name' => 'react-native',
        'data' => $crn,

    ],
    [
        'name' => 'vue',
        'data' => $xvue,

    ],
    ['name' => 'flutter',
        'data' => $xflutter,
    ],
    [
        'name' => 'react',
        'data' => $xreact,

    ],
    [
        'name' => 'ionic',
        'data' => $xionic,
    ],
    [
        'name' => 'xamarin',
        'data' => $xxamarin,
    ],
    [
        'name' => 'onsen',
        'data' => $xonsen,
    ],
    [
        'name' => 'cordova',
        'data' => $xcordova,
    ],
        [
        'name' => 'angular',
        'data' => $xangular,
    ],

]
;
//yii::error($series);
?>
 <div class="col-lg-12 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
<h3 class="card-title">Ripartizione Domande</h3>
<?php
echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'line', // default area
    'chartOptions' => [
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],
        ],
        'xaxis' => [
            'categories' => [
'2015	1',
'2015	2',
'2015	3',
'2015	4',
'2015	5',
'2015	6',
'2015	7',
'2015	8',
'2015	9',
'2015	10',
'2015	11',
'2015	12',
'2016	1',
'2016	2',
'2016	3',
'2016	4',
'2016	5',
'2016	6',
'2016	7',
'2016	8',
'2016	9',
'2016	10',
'2016	11',
'2016	12',
'2017	1',
'2017	2',
'2017	3',
'2017	4',
'2017	5',
'2017	6',
'2017	7',
'2017	8',
'2017	9',
'2017	10',
'2017	11',
'2017	12',
'2018	1',
'2018	2',
'2018	3',
'2018	4',
'2018	5',
'2018	6',
'2018	7',
'2018	8',
'2018	9',
'2018	10',
'2018	11',
'2018	12',
'2019	1',
'2019	2',
'2019	3',
'2019	4',
'2019	5',
'2019	6',
'2019	7',
'2019	8',
'2019	9',
'2019	10',
'2019	11',
'2019	12',
'2020	1',
'2020	2',
'2020	3',
'2020	4',
'2020	5',
'2020	6',
'2020	7',
'2020	8',
'2020	9',
'2020	10',
'2020	11',
'2020	12',
'2021	1',
'2021	2',
'2021	3',
'2021	4',
'2021	5',
'2021	6',
'2021	7',
'2021	8',
'2021	9',
'2021	10',
'2021	11',
'2021	12',
'2022	1',
'2022	2',
'2022	3',
'2022	4',
'2022	5',
'2022	6',
'2022	7',
'2022	8',
'2022	9',
'2022	10',
'2022	11'],

        ],
        /*    'plotOptions' => [
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
    ],*/
    ],
    'series' => $series,
]);


?></div></div></div>
<row></row><row>

<row>
<?php 
$command = $connection->createCommand("select sum(somma) as somma,
'yii' as [name] from badgeusers
where name like '%yii%'
union
select sum(somma)as somma,
'vue' as [name] from badgeusers
where name like '%vue%'
union
select sum(somma)as somma,
'flutter' as [name] from badgeusers
where name like '%flutter%'
union
select sum(somma)as somma,
'react' as [name] from badgeusers
where name like '%react%' and name not like '%native%'
union
select sum(somma)as somma,
'react-native' as [name] from badgeusers
where name like '%react%' and name   like '%native%'
union
select sum(somma)as somma,
'node' as [name] from badgeusers
where name like '%node%' 
union
select sum(somma)as somma,
'Ionic' as [name] from badgeusers
where name like '%ionic%'
union
select sum(somma)as somma,
'xamarin' as [name] from badgeusers
where name like '%xamarin%'
union
select sum(somma)as somma,
'cordova' as [name] from badgeusers
where name like '%cordova%'
union
select sum(somma)as somma,
'onsen' as [name] from badgeusers
where name like '%onsen%'
union 
select sum(somma)as somma,
'angular' as [name] from badgeusers
where name like '%angular%'


"
);
$alluser=$command->queryAll();

?>
<div class="col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card card-succes">
    <div class="card-header">
<h2>Bagde utenti</h2></div>
<?php

$pie2 = [];
$labels2 = [];
foreach ($alluser as $value) {
    $pie2[] = intval($value['somma']);
    $labels2[] = $value['name'];
}
//yii::warning($pie);

echo \onmotion\apexcharts\ApexchartsWidget::widget([
    'type' => 'pie', // default area
    //'height' => '400', // default 350
    // 'width' => '500', // default 100%

    'chartOptions' => [
        'labels' => $labels2,
        // ['In attesa', 'Confermato', 'Evaso', 'Rifiutato'],
        'chart' => [
            'toolbar' => [
                'show' => true,
                'autoSelected' => 'zoom',
            ],

        ],

    ],
    'series' => $pie2,

]);



?></div>
 </div>

</row>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>




    <?=GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'somma',
        'tagname',
        'id',

        ['class' => 'yii\grid\ActionColumn'],
    ],
]);?>
</row>

</div>
