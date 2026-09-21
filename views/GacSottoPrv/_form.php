
<?php
use kartik\tabs\TabsX;
use yii\helpers\Url;
 use yii\widgets\Pjax;
use app\models\Teste2;
use app\models\Teste2Search;
use yii\grid\GridView;
use yii\bootstrap4\Modal;

use app\models\gacprv;
use app\models\sottocommessa;
use app\models\gacmateriali;
use app\models\gacattivita;
use app\models\gacspese;
use app\models\allfiles;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\testaj;
use app\models\TestajSearch;

$sottoc = sottocommessa::find()
    ->select(['CD_DOSottocommessa as ID', 'Descrizione'])
    ->asArray()
    ->all();
$listsottoc = ArrayHelper::map($sottoc, 'ID', 'Descrizione');

$prv = gacprv::find()
    ->select(['id_prv as ID', 'descrizione as Descrizione'])
    ->asArray()
    ->all();
$listprv = ArrayHelper::map($prv, 'ID', 'Descrizione');
//yii::warning($listprv);

/* @var $this yii\web\View */
/* @var $model app\models\Gacsottoprv */
/* @var $form yii\widgets\ActiveForm */
$tipo=[['id'=>1,
'Descrizione'=>'Commerciale'],
['id'=>2,'Descrizione'=>'Tecnico'],
['id'=>3,'Descrizione'=>'Escutivo']
];


 $listtipo = ArrayHelper::map($tipo, 'id', 'Descrizione');
//yii::warning($listtipo);
$stato=
[['id'        => 1,
    'Descrizione' => 'Preventivazione'],
    ['id' => 2, 'Descrizione' => 'Avviato'],
    ['id' => 3, 'Descrizione' => 'Completato'],
    ['id' => 4, 'Descrizione' => 'Rifiutato'],
    ['id' => 5, 'Descrizione' => 'Arrestato'],
    ['id' => 3, 'Descrizione' => 'Fatturato'],
    
];
$liststato = ArrayHelper::map($stato, 'id', 'Descrizione');
//yii::warning($listtipo);

$totmateriali=gacmateriali::find()->where(['id_sub_prv'=>$model->id_sub_prv])->count();
$totattivita = gacattivita::find()->where(['id_sub_prv' => $model->id_sub_prv])->count();
$totspese = gacspese::find()->where(['id_sub_prv' => $model->id_sub_prv])->count();
$totdocumenti = allfiles::find()->where(['id_padre' => $model->id_sub_prv])->
andwhere(['entita'=>'PRV'])->count();


$Limateriali=gacmateriali::find()->where(['id_sub_prv'=>$model->id_sub_prv])->asArray()->all();
$Liattivita = gacattivita::find()->where(['id_sub_prv' => $model->id_sub_prv])->asArray()->all();
$Lispese = gacspese::find()->where(['id_sub_prv' => $model->id_sub_prv])->asArray()->all();
$Lidocumenti = allfiles::find()->where(['id_padre' => $model->id_sub_prv])->
andwhere(['entita'=>'PRV'])->asArray()->all();
$mat='<tr>';
$att='<tr>';
$spe='<tr>';
$doc='<tr>';
$conta=0;
foreach ($Limateriali as  $value) {
if($conta==8){

    $mat=$mat.'</tr><tr>';
}    
    $mat=$mat.'  <td>'.$value['cd_ar'].'</td>';
$conta=$conta+1;

}
$conta=0;
foreach ($Liattivita  as $value) {
    if (8 == $conta) {

    $att = $att . '</tr><tr>';
}

$att = $att . ' <td>' . $value['attivita'] . '</td>';
    $conta = $conta + 1;

}
$conta = 0;

foreach ($Lispese as $value) {
    if (8 == $conta) {

    $spe = $spe . '</tr><tr>';
}

    $spe = $spe . ' <td>' . 
    $value['descrizione'].
    '  importo  €'.$value['costoricaricato'] . '</td>';
$conta = $conta + 1;

}
$conta=0;
foreach ($Lidocumenti as $value) {
    if (8 == $conta) {

    $doc = $doc . '</tr><tr>';
}

    $doc = $doc . ' <td>' . $value['nomefile'] . '</td>';
$conta=$conta+1;
}


$content1=<<<EOF

<table class="table">
  <thead>
    <tr>
      <th scope="col">Elenco Materiali($totmateriali)</th>
    
 </tr>
  </thead>
  <tbody>
$mat
 </tbody>
</table>


<table class="table">
  <thead>
    <tr>
     <th scope="col">
Elenco Attività($totattivita)
</tr>
  </thead>
  <tbody>
$att
 </tbody>
</table>

 
<table class="table">
  <thead>
    <tr>
     <th scope="col">
Spese($totspese)
   </tr>
  </thead>
  <tbody>
$spe
 </tbody>
</table>

<table class="table">
  <thead>
    <tr>
     <th scope="col">
Documenti($totdocumenti)
  </tr>
  </thead>
  <tbody>
$doc
 </tbody>
</table>



EOF;





$content2='VvAsdasdsadasdasdas';
$content3='sadsadasdasdas';
$content4='sadklnadhjklsjhlfakhdsalkfhlaskj';
$content0=<<<EOF
<table class="table">
  <thead>
    <tr>
      <th scope="col">Azioni</th>
    </tr>
  </thead>
  <tbody>
  <tr>
  <td><a  href=""  target="_blank">
  Chiudi</a></td>
  <td>
  <td><a  href=""  target="_blank">
  Evadi in ordine a Fornitore</a></td>
  <td><a  href=""  target="_blank">
  Evadi in ordine a Cliente</a></td>

  </tr>
 </tbody>
</table>
EOF;
;


$attivita='';
$asearchModel  = new TestajSearch();
$adataProvider = $asearchModel->search(Yii::$app->request->queryParams);
$tmpurl=Url::to(['/testaj/index']);
$content44=<<<FF
<div class="iframe-tab">
    <iframe src="$tmpurl" style="width: 100%; height: 500px; border: 0;"></iframe>
</div>
FF;



$searchModel  = new Teste2Search();
$dataProvider = $searchModel->search(Yii::$app->request->queryParams);





$t = GridView::widget([
    'dataProvider' => $dataProvider,
    'columns'      => [
        'id',
        'testo',
        'testo2',
        // Altri campi, se necessario
    ],
]);




$items = [
   [
       'label'   => '<i class="fa-solid fa-location-crosshairs fa-spin"></i>Azioni',
        'content' => $content0,
        
    ],
   
    [
       'label'   => '<i class="fa-solid fa-list"></i>Generale',
        'content' => $content1,
        
    ],

    [
        'label'   => '<i class="fa-solid fa-person-circle-exclamation"></i> Attività',
  //      'options' => ['id'=>'attivita'],
        'content' => $attivita,
        'linkOptions'=>['data-url'=>Url::to(['/gacattivita/tabsdata', 'id' => $model['id_sub_prv']]),
    'data-pjax' => 1,
    ],'id' => 'tab1',
        //'active'  => true,
    ],
    [
        'label'   => '<i class="fa-solid fa-microchip"></i> Materiali',
      //'//content' => '$content2',
    //'options' => [  'id'=>'materiali'],
          'linkOptions'=>['data-url'=>Url::to(['/gacmateriali/tabsdata', 
         'id' => $model['id_sub_prv']]),
   'data-pjax' => 1,
   ], 'id' => 'tab2',
      // 'active'  => true,
    ],
  //  [
  //      'label'   => '<i class="fas fa-wrench"></i>/<i class="fas fa-bell"></i> Materiali/Attività',
    //    'content' => $content3,
        //'active'  => true,
   // ],
    [
        'label'   => '<i class="fa-solid fa-euro-sign"></i> Spese',
      //  'content' => $content4,
        //'active'  => true,
                 'linkOptions'=>['data-url'=>Url::to(['/gacspese/tabsdata', 
         'id' => $model['id_sub_prv']]),
    'data-pjax' => 1,
    ],'id' => 'tab3',
    ],
    [
        'label'   => '<i class="fas fa-book"></i> DMS',
        //'content' => $content1,
        //'active'  => true,
         'linkOptions'=>['data-url'=>Url::to(['/allfiles/tabsdata', 
         'id' => $model['id_sub_prv'],'entita'=>'PRV']),
    'data-pjax' => 1,
    ],'id' => 'tab4',
    ],
 [
       'label' => 'tesstag',
       'content' =>''
       /*$content44, $this->render('//testaj/index',[
           'dataProvider'=>$adataProvider,
           'searchModel'=>$asearchModel]), */
            // Renderizza la form direttamente all'interno del tab
       // 'active' => true,
   ],

    [
        'label' => 'ajaxcalls',
        'id'=>'ajcall',
        'content' =>$t, /*$this->render('//testaj/index',[
            'dataProvider'=>$adataProvider,
            'searchModel'=>$asearchModel]), */
            // Renderizza la form direttamente all'interno del tab
       // 'active' => true,
    ],
    
];


?>
 
<div class="gacsottoprv-form">

    <?php $form = ActiveForm::begin();?>
<div class="row">
    <div class="col-sm">
       <?=$form->field($model, 'id_prv')->widget(Select2::classname(),
    ['data'         => $listprv,
        'options'       => ['placeholder' => 'Seleziona Preventivo ...',
            'id'                              => 'id_prv'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Preventivo Padre')?>
</div>
<div class="col-sm">
    <?=$form->field($model, 'descrizione')->textInput(['maxlength' => true])?>
</div>
 <div class="col-sm">
        <?=$form->field($model, 'note')->textarea(['rows' => 6])?>
</div>

<div class="col-sm">
    <?=$form->field($model, 'tipologia')->widget(Select2::classname(),
    ['data'         => $listtipo,
        'options'       => ['placeholder' => 'Seleziona Tipologia ...',
            'id'                              => 'tipo'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
</div>
</div>
<div class="row">
    <div class="col-sm">
    <?php echo $form->field($model, 'sottocommessa')->widget(Select2::classname(),
    ['data'         => $listsottoc,
        'options'       => ['placeholder' => 'Seleziona sottocommessa ...',
            'id'                              => 'sottocommessa'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])->label('Commessa') ?>
</div>
<div class="col-sm">
    <?=$form->field($model, 'datacreazione')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])?>
</div>
<div class="col-sm">
    <?=$form->field($model, 'inizioval')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])->label('Inizio Val')?>
</div>
<div class="col-sm">
    <?=$form->field($model, 'fineval')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])->label('Fine Val')?>
</div>
    </div>
    <div class="row">
        <div class="col-sm">
    <?=$form->field($model, 'probacq')->textInput()->label('% di Acquisizione')?>
    </div>
    <div class="col-sm">
    <?=$form->field($model, 'provvigione')->textInput()?>
    </div>
    <div class="col-sm">
<?=$form->field($model, 'apertura')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])?>
</div>
    <div class="col-sm">

    <?=$form->field($model, 'chiusura')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])?>
</div>
    <div class="col-sm">

    <?=$form->field($model, 'apertura_pianificata')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])?>

    </div>

        <div class="col-sm">

    <?=$form->field($model, 'chiusura_pianificata')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])?>
</div>
    </div>
    <div class="row">
    <div class="col-sm">

    <?=$form->field($model, 'stato')->widget(Select2::classname(),
    ['data'         => $liststato,
        'options'       => ['placeholder' => 'Seleziona Stato ...',
            'id'                              => 'stato'],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ])?>
    </div>
        <div class="col-sm">

    <?=$form->field($model, 'datastato')->widget(DatePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
])->label('Data Stato')?>
</div>
    </div>
 <div>




    </div>   


    <div class="form-group">
        <?=Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>

    <?php ActiveForm::end();?>

</div>



<?php
echo Html::button('Nuovo ajax', ['class' => 'btn btn-success', 'id' => 'create-button']);
Modal::begin([
    'id'   => 'create-modal',
    'size' => 'modal-lg',
    //   'header' => '<h4>Nuovo Record</h4>',
]);
//Pjax::begin(['id' => 'modal-pjax']);
$model = new Teste2();
echo $this->render('/teste2/_form', ['model' => $model]);
//Pjax::end();
Modal::end();

// Javascript per gestire l'apertura della finestra modale e l'aggiornamento con Pjax
$this->registerJs("
    $('#create-button').click(function(){
        $('#create-modal').modal('show')
    });
$('#create-button').click(function(){
    $.ajax({
        url: 'index.php?r=teste2/create', // Controlla il percorso del controller
        type: 'get',
        success: function(data) {
            $('#create-modal .modal-body').html(data);
            $('#create-modal').modal('show');
        }
    });
});
$('#modal-pjax').on('pjax:end', function() {
    $.pjax.reload({container:'#grid-pjax'});
    $('#create-modal').modal('hide');
    $('a[data-toggle=\"tab\"]').eq(7).trigger('click');
    console.log('fine');
});



$('#create-modal').on('beforeSubmit', 'form#create-form', function(e) {
    var form = $(this);
    $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: form.serialize(),
        success: function(data) {
            if (data.success) {
                // Chiudi la finestra modale e aggiorna la griglia con Pjax
                $('#create-modal').modal('hide');
                $.pjax.reload({container:'#grid-pjax',async: false});
  $('a[data-toggle=\"tab\"]').eq(7).trigger('click');
                // Esegui il clic sulla tab dopo l'aggiornamento Pjax
            } else {
                // In caso di errori, puoi visualizzarli o gestirli qui
                console.log('Errore nel salvataggio del record.');
            }
        }
    });
            $('a[data-toggle=\"tab\"]').eq(7).trigger('click');

    return false;
});



");

?>

<?php

Pjax::begin(['id' => 'grid-pjax']);

echo TabsX::widget([
    'items'        => $items,
    'position'     => TabsX::POS_ABOVE,
    'encodeLabels' => false,

]);
Pjax::end();
?>