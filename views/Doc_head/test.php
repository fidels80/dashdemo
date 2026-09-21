<?php

use app\models\Anagrafica;
use app\models\Contact;
use app\models\Tablibero1;
use app\models\Tabmodelli;
use app\models\Tabtessuti;
use app\models\Tabtipidocumento;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\Pjax;
/* $tdM=Tabmodelli::find()->all();
$db = Yii::$app->db;
$listdataM= ArrayHelper::map($tdM,'ID','Descrizione');
 */
$tdA = Anagrafica::find()->all();
$db = Yii::$app->db;
$listdataA = ArrayHelper::map($tdA, 'ID', 'name');

$tdd = Tabtipidocumento::find()->all();
$listdataD = ArrayHelper::map($tdd, 'ID', 'Descrizione');
$datam = Tabmodelli::find()
    ->select(['id as value', 'descrizione as  label', 'id as id'])
    ->asArray()
    ->all();
$datad = Tabmodelli::find()
    ->select(['descrizione as value', 'id as  label', 'id as id'])
    ->asArray()
    ->all();
//$tdc = Contact::find()->all();
//$listdataC = ArrayHelper::map($tdc, 'id_contact', 'Name');
$datac = Contact::find()
    ->select(['id_contact as value', 'Name as label', 'id_contact as id'])
    ->asArray()
    ->all();
$dataT = Tabtessuti::find()
    ->select(['id as value', 'descrizione as label', 'id as id'])
    ->asArray()
    ->all();
$this->registerJs(<<<JS
$(".dynamic-form").on('afterInsert', function(e, item) {
var datePickers = $(this).find('[data-krajee-kvdatepicker]');
datePickers.each(function(index, el) {
$(this).parent().removeData().kvDatepicker('remove');
$(this).parent().kvDatepicker(eval($(this).attr('data-krajee-kvdatepicker')));
});

    var last_index = -1;
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        $(this).html("Produit: " + (index + 1));
        last_index= index;
    });

   // kvListenEvent("select2:select", $("#invoiceitem-"+last_index+"-item").kvSelector(),
   // function() {
   //     alert('test');
   // });
});

JS
);
/* for ($t = 1; $t <= 999; $t++) {
$this->registerJs("$('#x[{$t}]itcode').xchange(function() {
var str=e.params.data.text;
str=str.replace(e.params.data.id,'');
document.getElementById('invoiceitem-{$t}-item').value=str;
});");
} */
?>
//invoiceitem-{$i}-item
<div class="customer-form">



    <?php
$form = \yii\widgets\ActiveForm::begin([
    'id' => 'dynamic-form',
]);
?>




            <?php echo $form->errorSummary($items); ?>

    <div class="row">
        <div class="col-md-5">
            <?php /* $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'Name'])->label(false)
 */?>
            <?=
$form->field($model, 'tipodoc')->
//     dropDownList(
// $listdataD,
// ['prompt'=>'Seleziona il documento' ]
//    );
widget(Select2::classname(), ['data' => $listdataD,
    'options' => ['placeholder' => 'Seleziona documento ...', 'id' => 'tipodoc'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            <?=$form->field($model, 'invoice_number')->textInput(['maxlength' => true, 'readonly' => true, 'placeholder' => 'Number'])->label(false)?>

            <?php
echo $form->field($model, 'due_date')->widget(DatePicker::classname(), [
    'options' => ['placeholder' => 'Due date'],
    'removeButton' => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd',
    ],
])->label(false);
?>
        </div>
        <div class="col-md-7">
            <?php /* $form->field($model, 'name')->textInput(['maxlength' => true,'placeholder'=>'Name'])->label(false)
 */?>
<?php //Pjax::begin(['id' => 'pjax-grid-cont']); ?>
  <?=
$form->field($model, 'name')->
widget(Select2::classname(), ['data' => $listdataA,
    'id' => 'invoice-name',
    'options' => ['placeholder' => 'Seleziona anagrafica ...', 'id' => 'lvl-0'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            <?=$form->field($model, 'attn')->textInput(['maxlength' => true, 'placeholder' => 'ATTN', 'onclick' => 'magsearch()', 'id' => 'piva'])->label(false)?>
            <?=$form->field($model, 'address')->textarea(['rows' => 6, 'placeholder' => 'Address', 'id' => 'indi'])->label(false)?>
            <?php
// $url = \yii\helpers\Url::to(['index.php?r=contact/list']);
echo $form->field($model, 'cd_contact')->widget(DepDrop::classname(), [
    'data' => $datac,
    'options' => ['placeholder' => 'carico ...'],
    'type' => DepDrop::TYPE_SELECT2,
    'select2Options' => ['pluginOptions' => ['allowClear' => true]],
    'pluginOptions' => [
        'depends' => ['lvl-0'],
        'url' => Url::to(['/contact/list']),
        //   'params' => ['lvl-0'],
        'loadingText' => 'caricamento dati ...',
    ],
]);
?>
        </div>
    </div>

 <?php //Pjax::end(); ?>
    <?php //$form->field($model,'attributeName')->widget(DatePicker::className(),['clientOptions' => ['defaultDate' => '2014-01-01']])
?>

    <?php
DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    'limit' => 999, // the maximum times, an element can be added (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => $items[0],
    'formId' => 'dynamic-form',
    'formFields' => ['itcode',
        'desciption',
        'price', 'total',
    ],
]);
?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-briefcase"></i> Items
                <button type="button" class="add-item btn btn-success btn-sm pull-right"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                        <?php foreach ($items as $i => $m): ?>
                    <div class="item"><!-- widgetItem -->
                        <div class="rows">
                            <?php
// necessary for update action.
$datam[$i] = Tabmodelli::find()
    ->select(['id as value', 'descrizione as  label', 'id as id'])
    ->asArray()
    ->all();
$datal1[$i] = Tablibero1::find()
    ->select(['id as value', 'descrizione as  label', 'id as id'])
    ->asArray()
    ->all();
if (!$m->isNewRecord) {
    echo Html::activeHiddenInput($m, "[{$i}]id");
}
?>
                            <table width="80%">
                                <tr>
                                    <td valign="top" width="5%"><button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button></td>
                                    <td width="25%">
                                        <div class=”col-sm-6">
                                            <?php
/* echo $form->field($m, "[{$i}]itcode")->
//textInput(['maxlength' => true,'placeholder'=>'codice'])
widget(\yii\jui\AutoComplete::classname(),
[
'clientOptions' => [
'source' => $datam[$i],
'autoFill'=>true,
//    'id'=>"[{$i}]itcode",
'select' => new JsExpression("function( event, ui ) {
$('#invoice-[{$i}]itcode').val(ui.item.id);
document.getElementById('invoiceitem-{$i}-item').value=ui.item.label;
}")
],
]
)->label(false) */
$url = Url::to('index.php?r=tabmodelli/list');
echo $url;
$tmpjava = new JsExpression("function(e) {  var str=e.params.data.text;
                                str=str.replace(e.params.data.id,'');
                                document.getElementById('invoiceitem-{$i}-item').value=str; }");
echo $form->field($m, "[{$i}]itcode")->widget(Select2::classname(), [
    'options' => ['multiple' => false, 'placeholder' => 'cerca modello ...'],
    'pluginEvents' => [
        'select2:select' => $tmpjava,
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 3,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'caricamento dati...'; }"),
        ],
        'ajax' => [
            'url' => $url,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {q:params.term}; }'),
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression("function(md) { return md.text; }"),
        'templateSelection' => new JsExpression('function (md) { return md.text;}'),
    ],
]);
?>


<?php
/*/* $url = \yii\helpers\Url::to(['index.php?r=contact/list']);
echo $form->field($model, 'tessuto')->widget(DepDrop::classname(), [
'data' => $dataT,
'options' => ['placeholder' => 'carico ...'],
'type' => DepDrop::TYPE_SELECT2,
'select2Options' => ['pluginOptions' => ['allowClear' => true]],
'pluginOptions' => [
'depends' => ['[{$i}]itcode'],
'url' => Url::to(['/articoli/tessuti']),
//   'params' => ['lvl-0'],
'loadingText' => 'caricamento dati ...',
]
]);
 */?>


                                    </td>

                                    <td width="15%"><?=
$form->field($m, "[{$i}]item")->
textInput(['maxlength' => true, 'placeholder' => 'description']
    /*      widget(AutoComplete::className(),
[
'clientOptions' => [
'source' => $datad,
'autoFill'=>true,
//    'id'=>"[{$i}]itcode",
'select' => new JsExpression("function( event, ui ) {
$('#invoice-[{$i}]item').val(ui.item.id);
document.getElementById('invoiceitem-{$i}-itcode').value=ui.item.label;
}")

]
] */
)->label(false)
?></td>
                                    </div>
                                    <td width="10%"><?=$form->field($m, "[{$i}]qty")->textInput(['maxlength' => true, 'placeholder' => 'qta'])->label(false)?></td>
                                    <td width="15%">
                                        <?php echo $form->field($m, "[{$i}]total")->textInput(['placeholder' => 'price'])->label(false); ?>
                                        <?=$form->field($m, "[{$i}]tablibero1")->dropDownList($datal1[$i], ['prompt' => ''])?>
                                    </td>
                                </tr>
                            </table>
                        </div><!-- .row -->
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div><!-- .panel -->
    <?php DynamicFormWidget::end();?>

    <div class="form-group">
        <?=Html::submitButton($m->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary'])?>
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


