<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<?php
use app\models\Anacli;
use app\models\AR;
use app\models\Doc_head;
use app\models\DocType;
use app\models\User;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
//use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap4\Button;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use unclead\multipleinput\Multipleinput;

use yii\web\View;


\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
$js = '';
$usrid = Yii::$app->user->Id;
if ($usrid !== null) {
    $usr_ris = (new \yii\db\Query())
        ->select(['email', 'gruppo', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}
$tdA = Anacli::find()
    ->select(['[cd_cli] as ID', 'desk as name'])
    ->where(['cd_cli' => $usr_ris['cd_cli']])
    ->asArray()
    ->all();
$db = Yii::$app->db;
$listdataA = ArrayHelper::map($tdA, 'ID', 'name');
$annmodel = (new \yii\db\Query())
    ->select(['altcli as id', 'Desk as Name'])
    ->from('relcli')
    ->leftJoin('ana_cli', 'relcli.altcli = ana_cli.cd_cli')
    ->where(['relcli.cd_cli' => $usr_ris['cd_cli']])
    ->all();
$listdest = ArrayHelper::map($annmodel, 'id', 'Name');
$tdd = DocType::find()
    ->select(['[cd_doc] as ID', 'descrizione'])
    ->where(['cd_doc' => 'PRV'])
    ->asArray()
    ->all();
$listdataD = ArrayHelper::map($tdd, 'ID', 'descrizione');
$datam = AR::find()
    ->select(['Cd_AR as value', 'descrizione as  label', 'Cd_AR as id'])
    ->asArray()
    ->all();
$datad = AR::find()
    ->select(['descrizione as value', 'Cd_AR as  label', 'Cd_AR as id'])
    ->asArray()
    ->all();
//$tdc = Contact::find()->all();
//$listdataC = ArrayHelper::map($tdc, 'id_contact', 'Name');
$datac = user::find()
    ->select(['id value', 'username  as label', 'id as id'])
    ->asArray()
    ->all();
$dataT = AR::find()
    ->select(['Cd_AR as value', 'descrizione as label', 'Cd_AR as id'])
    ->asArray()
    ->all();
?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissable">
         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
         <h4><i class="icon fa fa-check"></i>Saved!</h4>
         <?=Yii::$app->session->getFlash('error')?>
    </div>
<?php endif;?>
<div class="customer-form">
 <?php
$form = \yii\widgets\ActiveForm::begin([
    'id' => 'dynamic-form',
]);
?><link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <?php  
   // echo $form->errorSummary($items); ?>
    <div class="row">
        <div class="col-md-4">
            <?php echo
$form->field($model, 'cd_doc')->
    widget(Select2::classname(), ['data' => $listdataD,
    'options' => ['placeholder' => 'Seleziona documento ...', 'id' => 'cd_doc'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            <?=$form->field($model, 'numdoc', ['enableClientValidation' => false])->textInput(['maxlength' => true,
    'readonly' => true, 'placeholder' => 'Number'])->label("numero doc")?>
            <?php
echo $form->field($model, 'data')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'data'],
    'removeButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
    ],
])->label(false);
?>
        </div>
        <div class="col-md-7">
            <?=
$form->field($model, 'cd_cli')->
widget(Select2::classname(), ['data' => $listdataA,
    'id' => 'invoice-name',
    'options' => ['placeholder' => 'Seleziona anagrafica ...', 'id' => 'lvl-0'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            <?php //echo $form->field($model, 'altcli')->textInput(['maxlength' => true,
//'placeholder' => 'ATTN', 'onclick' => 'magsearch()', 'id' => 'piva'])->label(false)
?>
            <?=$form->field($model, 'note')->textarea(['rows' => 6, 'placeholder' => 'Note', 'id' => 'note'])->label(false)?>
            <?php
// $url = \yii\helpers\Url::to(['index.php?r=contact/list']);
echo $form->field($model, 'altcli')->widget(DepDrop::classname(), [
    'data' => $datac,
    'options' => ['placeholder' => 'carico ...'],
    'type' => DepDrop::TYPE_SELECT2,
    'select2Options' => ['pluginOptions' => ['allowClear' => true]],
    'pluginOptions' => [
        'depends' => ['lvl-0'],
        'url' => Url::to(['/anacli/list']),
        //   'params' => ['lvl-0'],
        'loadingText' => 'caricamento dati ...',
    ],
]);
?>
        </div>
    </div>
<div class="cerca">
</div>
<?php
/*
DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper_1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    'limit' => 999, // the maximum times, an element can be added (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $items[0],
    'formId' => 'dynamic-form',
    'formFields' => ['cd_art',
        'descrizione',
        'prezzo', 'qta', 'um',
    ],
]);
?>
    <div class="panel panel-default">
<br>
    <div class="panel-heading">
         <div class="col-md-4  align-self-end">
      <input type="text" id="search-input" placeholder="Cerca..." class="form-control">
<button id="search-button" type="button" class="btn-success btn-sm pull-right"> <i class="nav-icon fas fa-filter"></i> Cerca</button>
<button id="reset-button" type="button" class="btn-success btn-sm pull-right"> <i class="nav-icon fas fa-light fa-filter fa-flip-vertical"></i>Mostra tutto</button>
</div>
        </div>
        <br><br><br>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <?php foreach ($items as $i => $m): ?>
                    <div class="item"><!-- widgetItem -->
                        <div class="rows justify-content-md-center">
                            <?php
$datam[$i] = AR::find()
    ->select(['Cd_AR as value', 'descrizione as  label', 'Cd_AR as id'])
    ->asArray()
    ->all();
$datal1[$i] = AR::find()
    ->select(['Cd_AR as value', 'descrizione as  label', 'Cd_AR as id'])
    ->asArray()
    ->all();
if (!$m->isNewRecord) {
    echo Html::activeHiddenInput($m, "[{$i}]id");
}
?>
<div class="row 1"> <div class="col-md-1"><br>
                  <button type="button" class="add-item btn btn-success btn-xl pull-right">
                   <i class="nav-icon fas fa-plus"></i> Add</button>
</div>
 
    <?=$form->field($m, "[{$i}]nriga")->textInput(['maxlength' => true, 
    'placeholder' => 'Riga','readonly'=>true,'hidden'=>true])->label(false)?>
 
<div class="col-md-2">

<?php
$url = Url::to('index.php?r=ar/list');
echo $form->field($m, "[{$i}]cd_art")->widget(Select2::classname(), [
    'options' => ['multiple' => false, 'placeholder' => 'cerca articolo ...', 'class' => 'reqItem form-control'],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'error caricamento dati...'; }"),
        ],
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
        ],
    ],
]);
?></div>  <div class="col-md-7"><?=
$form->field($m, "[{$i}]descrizione")->
textInput(['maxlength' => true, 'placeholder' => 'Descrizione']
)
?></div>
<div class="col-md-2">
<br>
 </div>
</div>
<div class="row 2">     <div class="col-md-1">
         <span></span><br>
<button type="button" class="remove-item btn btn-danger btn-XL navbar-btn navbar-right">
                                     <i class="nav-icon fas fa-minus"> </i>  Rimuovi
                                     </button>

                                    </div> <div class="col-md-2">
<?=$form->field($m, "[{$i}]qta")->textInput(['maxlength' => true, 'placeholder' => 'qta'])?></div>
<div class="col-md-2"><?php echo $form->field($m, "[{$i}]um")->textInput(['placeholder' => 'U.m.']); ?></div>
<div class="col-md-2"><?php echo $form->field($m, "[{$i}]prezzo")->textInput(['placeholder' => 'price']); ?></div>
<div class="col-md-1"><?php echo $form->field($m, "[{$i}]iva")->textInput(['placeholder' => 'price']); ?></div>
<div class="col-md-2"><?=$form->field($m, "[{$i}]totale")->textInput(['readonly' => true, 'placeholder' => 'totale'])?></div>
<div class="col-md-1"><button type="button" class=" duplicate-button  btn btn-danger btn-XL navbar-btn navbar-right">
                                     <i class="nav-icon fas fa-minus"> </i>  duplica
                                     </button></div>

</div><!-- .row2 -->
                </div><!-- .row -->
            </div>
        <?php endforeach;?>
    </div>
</div>
</div><!-- .panel -->
<?php DynamicFormWidget::end();?>
*/ ?>


<?php $url = Url::to('index.php?r=ar/list');
//$t=new Doc_head();
//$items=$model->getrows($model->id); 
 
$zdata = AR::find()
        ->select(['Cd_Ar', "CONCAT(Cd_Ar, '-', Descrizione) as Descrizione"])
        ->orderBy(['Descrizione' => SORT_ASC])
        ->asArray()
        ->all();

$zmap = array_column($zdata, 'Descrizione', 'Cd_Ar');


//yii::error( ($model->getDocRows()));
$model->items=json_encode($model->getRowsall())  ;
//yii::warning($items);





echo $form->field($model,'docRows')->widget(MultipleInput::className(), [
'max' => 999,
    'min' => 1,
    
    'allowEmptyList' => false,
    'enableGuessTitle' => false,
    'sortable'=>true,


    'addButtonPosition' => [ 
 
    MultipleInput::POS_ROW , MultipleInput::POS_FOOTER],
   'cloneButton' => true,'cloneButtonOptions' => [
            'class' => 'btn btn-warning',
            'label' => 'Clona' // also you can use html code
           ],
           'addButtonOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Aggiungi' // also you can use html code
           ],
           'removeButtonOptions' => [
            'label' => 'remove',
            'class' => 'btn btn-danger',

           ],
           
  'showGeneralError' => true,
//'layoutConfig' => [
//        'offsetClass' => 'col-md-offset-1',
//        'labelClass' => 'col-md-1',
//        'wrapperClass' => 'col-md-5',
//        'errorClass' => 'col-md-offset-1 col-md-5',
//        'buttonActionClass' => 'col-md-offset-1 col-md-1',
//    ],
    'columns' => [
        ['name'=>'nriga',
        'title'=>'Riga',
         // 'reaDonly' => true,
         'options'=>[
         'readonly' => true,
         ],
        'columnOptions' => [
                'style' => 'width: 20px;',
                //'class' => 'col col-lg-2',
                //'readonly' => true,
            ]
    //    'class'=>'col col-lg-2'
    ],
        [
'name'  => 'cd_art',
                    'type' =>  kartik\select2\Select2::class,
                   //  'dropDownList',

                    'title' => 'Articolo <font color="red">*</font>',
                   // 'class'=>'Req-items',
                    'options' => [
                                          'value' => function ($data) {
        return $data['cd_art'];
    },
                    'data' =>$zmap
                    // ArrayHelper::map(AR::find()->
                    //orderBy(['Descrizione'=>SORT_ASC])->all(),
                    //'Cd_AR','Descrizione')
                    ,
                        'value'=>'Please select...',
                         'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'language' => [
            'errorLoading' => new JsExpression("function () { 
                return 'error caricamento dati...'; }"),
        ],
        
        //'ajax' => [
        //    'url' => $url,
        //    'dataType' => 'json',
        //    'data' => new JsExpression('function(params) { return {q:params.term}; }'),
        //],
    
    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function(data) { return data.text; }'),
            'templateSelection' => new JsExpression('function (data) { return data.id; }'),
],

],'columnOptions' => [
                'style' => 'width: 150px;',
                'class' => 'col col-lg-2'
            ]
                    ]
                    ,['name'=>'descrizione','title' =>'Descrizione',
                'columnOptions' => [
                'style' => 'width: 400px;',
                'class' => 'col col-lg-8'
            ]],
                    ['name'=>'um','title' =>'Um',
                      'columnOptions' => [
                'style' => 'width: 70px;',
                'class' => 'col col-lg-2'
            ]],
                    ['name'=>'iva','title' =>' IVA ',
                    'columnOptions' => [
                'style' => 'width: 70px;',
                'class' => 'col col-lg-2'
            ]]
                    ,['name'=>'qta','title' =>'Qta.',
                'columnOptions' => [
                'style' => 'width: 100px;',
                'class' => 'col col-lg-1',
                
                ],       
                        'options'=>[ 'oninput' => 'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);',
            ],
              'value' => function ($data) {
                    return number_format($data['prz_unit']??0, 3,',','.'); // Formatta il numero con tre decimali
                },
        ],

['name'=>'prezzo','title' =>'Prezzo.Unit',
                'columnOptions' => [
                'style' => 'width: 100px;',
                'class' => 'col col-lg-1'
                ],
                             'options'=>[ 'oninput' => 'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);',
         ],
              'value' => function ($data) {
                    return number_format($data['prz_unit']??0, 3,',','.'); // Formatta il numero con tre decimali
                },
                
                ]
                
                ,
                    ['name'=>'totale','title' =>'Totale',
                'columnOptions' => [
                'style' => 'width: 100px;',
                'class' => 'col col-lg-1' 
                ],
        
                           'options'=>[ 'oninput' => 'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);',
         ],
              'value' => function ($data) {
                    return number_format($data['prezzo']??0, 3,',','.'); // Formatta il numero con tre decimali
                },
        ],
            ['name'=>'sconto','title' =>'Sconto.Unit',
                'columnOptions' => [
                'style' => 'width: 100px;',
                'class' => 'col col-lg-1'
            ]],
                    ['name'=>'prz_tot','title' =>'Totale Scontato',
                    'columnOptions' => [
                'style' => 'width: 100px;',
                'class' => 'col col-lg-1','readonly' => true,
            ],
        
                             'options'=>[ 'oninput' => 'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);',
         ],
              'value' => function ($data) {
                    return number_format($data['prz_tot']??0, 3,',','.'); // Formatta il numero con tre decimali
                },]
                    
                    


                    


    ],
    'data' => $model->docRows,
]);


 


$js = <<<JS
function validateNumber(input) {
    if (event . keyCode === 9) {
    // Tasto Tab premuto, non eseguire la validazione
    return;
}

    var value = input.value;
    var parts = value.split('.');
    if (parts.length > 1 && parts[1].length > 3) {
        input.value = parseFloat(parts[0] + '.' + parts[1].substr(0, 3));
    }

    var form = input.closest('form');
    if (form) {
        var validateResult = form.validate().element(input);
        if (validateResult === false) {
            // Annulla la conferma del form
            return false;
        }
    }
}
JS;

// Registra il codice JavaScript
$this->registerJs($js, View::POS_END);


?>























<div class="form-group">
    <?php
    echo Html::submitButton($model->isNewRecord ? 'Salva' : 'Aggiorna', ['class' => 
    'btn btn-primary'])
    ?>
</div>
<?php \yii\widgets\ActiveForm::end();?>
<script type="text/javascript">
    //the dropdown list id; This doesn't have to be a dropdown it can be any field type.
    function magsearch()
    {
        id = (document.getElementById('lvl-0').value);
        $.get("index.php?r=invoice/get-location-address", {id: id}, function (data) {
            if (data !== null) {
                document.getElementById('piva').value = data.PIVA;
                document.getElementById('indi').value = data.Indirizzo;
            } else {
                //if data wasn't found the alert.
                alert('We\'re sorry but we couldn\'t load the the location data!');
            }
        });
    }
    ;
</script>
<script type="text/javascript">
    //the dropdown list id; This doesn't have to be a dropdown it can be any field type.
    function itemsearch(id, desc)
    {
        //  id=(document.getElementById('rg').value);
        $.get("index.php?r=invoice/Itcode-Search", {id: id}, function (data) {
            if (data !== null) {
                document.getElementById('desc').value = data.Descrizione;
                //document.getElementById('indi').value=data.Indirizzo;
            } else {
                //if data wasn't found the alert.
                alert('We\'re sorry but we couldn\'t load the the location data!');
            }
        });
    }
    ;
</script>
</div>



<script>

$(document).ready(function() {

$(document).on("change", "[id*=-qta]", function() {
//$this.val(99);
 var itemVal=$(this).attr("name");
console.log(itemVal);
var conta=$(this).attr("name");
  conta=conta.replace('qta','');
              conta=conta.replace('Doc_head','');
            conta=conta.replace('docRows','');
            conta=conta.replace('[','');
            conta=conta.replace(']','');
           conta=conta.replace('[]','');
                      conta=conta.replace('[','');
            conta=conta.replace(']','');
    console.log(conta);
    var prezzo=parseFloat($("#doc_head-docrows"+conta+"-prezzo").val());
    console.log(prezzo);
    qta=
    console . log(prezzo);

var totale=parseFloat($(this).val())*prezzo;
console.log(totale);
 $("#doc_head-docrows-"+conta+"-totale").val( totale.toFixed(2));
});

$(document).on("change", "[id*=-prezzo]", function() {
//$this.val(99);
 var itemVal=$(this).attr("name");
//console.log(itemVal);
var conta=$(this).attr("name");
  conta=conta.replace('prezzo','');
               conta=conta.replace('Doc_head','');
            conta=conta.replace('docRows','');
            conta=conta.replace('[','');
            conta=conta.replace(']','');
           conta=conta.replace('[]','');
              conta=conta.replace('[','');
                 conta=conta.replace(']','');
                 conta=conta.replace('[','');
                 conta=conta.replace(']','');
    console.log(conta);
    var qta=$("#doc_head-docrows-"+conta+"-qta").val();
    var totale=parseFloat($(this).val())*qta;
 $("#doc_head-docrows-"+conta+"-totale").val(totale.toFixed(2) );
});

//});
//$(document).ready(function() {
$(document).on("change", "[id*=-cd_art]", function() {
    var itemVal=$(this).val();
    console.log("cane");
//console.log( itemVal);
//console.log($(this).attr("name"));
var conta=$(this).attr("name");
 // console.log(conta);
  conta=conta.replace('cd_art','');
            conta=conta.replace('Doc_head','');
            conta=conta.replace('docRows','');
            conta=conta.replace('[','');
            conta=conta.replace(']','');
           conta=conta.replace('[]','');
           conta=conta.replace('[','');
            conta=conta.replace(']','');
           conta=conta.replace('[]','');
              conta=conta.replace('[','');
                 conta=conta.replace(']','');
                               conta=conta.replace('[','');
                 conta=conta.replace(']','');
     console.log(conta);
$.get("index.php?r=ar/getardet", {ar: this.value}, function (datam) {
console . log(datam . Descrizione);
 $("#doc_head-docrows-"+conta+"-descrizione").val(datam.Descrizione);
 $("#doc_head-docrows-"+conta+"-um").val(datam.Cd_ARMisura);
 $("#doc_head-docrows-"+conta+"-iva").val(datam.Cd_Aliquota_V);
 })
});

/*
$(".dynamicform_wrapper_1").on("beforeInsert", function(e, item) {

 // var confirmDuplicate = confirm("Vuoi duplicare la riga?");
 // if (confirmDuplicate) {
 // var curItem = $(item);
 // $(this).trigger("afterInsert",curItem,t='xauto');
//}

});
*/
});
  $('#search-button').on('click', function() {
    //console.log("as");
    var searchText = $('#search-input').val();
     console.log(searchText);
    $('div.rows').hide();
    $("div.rows:contains('" + searchText + "')").show();
  });

  $('#reset-button').on('click', function() {
    $('div.rows').show();
  });
</script>

