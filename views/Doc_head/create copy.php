
<?php

//use app\models\Anagrafica;
//use app\models\Contact;
//use app\models\Tablibero1;
//use kidzen\dynamicform\DynamicFormWidget;
//use kartik\widgets\DynaGrid;
//use app\models\Tabmodelli;
//use app\models\Tabtessuti;
//use app\models\Tabtipidocumento;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\models\Anacli;
use app\models\Doc_head;
use app\models\Doc_rows;
use app\models\AR;
use app\models\DocType;
use app\models\User;
 


 


//$icon = new \thoulah\fontawesome\Icon();

/* $tdM=Tabmodelli::find()->all();
$db = Yii::$app->db;
$listdataM= ArrayHelper::map($tdM,'ID','Descrizione');
 */
$tdA = Anacli::find()
->select(['[cd_cli] as ID', 'desk as name'])
 ->asArray()
->all();
$db = Yii::$app->db;
$listdataA = ArrayHelper::map($tdA, 'ID', 'name');

$tdd = DocType::find()
 ->select(['[cd_doc] as ID', 'descrizione'])
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
?><link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <?php echo $form->errorSummary($items); ?>
    <div class="row">
        <div class="col-md-2">
            <?php echo 
$form->field($model, 'cd_doc')->
widget(Select2::classname(), ['data' => $listdataD,
    'options' => ['placeholder' => 'Seleziona documento ...', 'id' => 'cd_doc'],
    'pluginOptions' => [
        'allowClear' => true,
    ],
]);
?>
            <?=$form->field($model, 'numdoc')->textInput(['maxlength' => true, 'readonly' => true, 'placeholder' => 'Number'])->label(false)?>

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
        <div class="col-md-5">
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
            <?=$form->field($model, 'altcli')->textInput(['maxlength' => true, 'placeholder' => 'ATTN', 'onclick' => 'magsearch()', 'id' => 'piva'])->label(false)?>
            <?=$form->field($model, 'note')->textarea(['rows' => 6, 'placeholder' => 'Address', 'id' => 'indi'])->label(false)?>
            <?php
// $url = \yii\helpers\Url::to(['index.php?r=contact/list']);
echo $form->field($model, 'xnota')->widget(DepDrop::classname(), [
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
    <?=Html::button('cerca prodotto', ['value' => Url::to('index.php?r=ar/barcode'), 'class' => 'btn btn-success', 'id' => 'modalButton'])?>
    <?php
Modal::begin([
    'header' => '<h4>articoli</h4>',
    'id' => "modal",
    'size' => 'modal-lg', //classe bootstrap
]);
echo "<div id='modalContent'></div>";
Modal::end();
?>
    <?php
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
        'prezzo', 'qta',
    ],
]);
?>

    <div class="panel panel-default">
     
    <div class="panel-heading">
            <h4>
                <i class="glyphicon glyphicon-briefcase"></i> Items
                <button type="button" class="add-item btn btn-success btn-sm pull-right">
                    <i class="glyphicon glyphicon-plus"></i> Add</button>
            </h4>
        </div>
        <div class="panel-body">
            <div class="container-items"><!-- widgetBody -->
                <?php foreach ($items as $i => $m): ?>
                    <div class="item"><!-- widgetItem -->
                        <div class="rows justify-content-md-center">
                            <?php
// necessary for update action.
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
                            <table width="50%">
                                <tr>
                                    <td valign="top" width="5%"><button type="button"
                                     class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus">

                                     </i></button></td>
                                    <td width="80%">
                                        <div >
                                            <table width="80%">
                                                <tr> <td width="20%">
                                                        <?php
$url = Url::to('index.php?r=ar/list');
echo $form->field($m, "[{$i}]cd_art")->widget(Select2::classname(), [
    'options' => ['multiple' => false, 'placeholder' => 'cerca articolo ...'],
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
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression("function(md) { return md.text; }"),
        'templateSelection' => new JsExpression('function (md) { return md.text;}'),
    ],
]);
?></td><td width="5%"><td width="20%">
</td><td width="5%"><td width="20%">
                                       </td><td width="5%"><td width="20%"></td><td>
                                            </table><table width="100%">

                                            </tr><tr>
                                            <td  width="15%">

                                                <?php $h = new JsExpression("//alert(this.name );
                                                            if(!$(this).val() ){
                                                var conta=this.name;
                                                                         conta=conta.replace('itcode','');
                                                                        conta=conta.replace('InvoiceItem','');
                                                                        conta=conta.replace('[','');
                                                                        conta=conta.replace(']','');
                                                                         conta=conta.replace('[]','');
                                                                    modello=$(\"#invoiceitem-\"+conta+\"-modello\").val();
                                                                    tessuto=$(\"#invoiceitem-\"+conta+\"-tessuto\").val();
                                                                    taglia= $(\"#invoiceitem-\"+conta+\"-taglia\").val();
                                                                    colore= $(\"#invoiceitem-\"+conta+\"-colore\").val();
                                                             $.get(\"index.php?r=articoli/bc\", {modello: modello ,tessuto: tessuto, taglia: taglia , colore: colore}, function (data) {
                                                            if (data !== null ) {
                                                             $(\"#invoiceitem-\"+conta+\"-itcode\").val(data.IDArticolo);
                                                              $(\"#invoiceitem-\"+conta+\"-total\").val(data.Prezzo2);
                                                                // tmpdesk= new String();
                                                                $.get(\"index.php?r=articoli/dsm\", {modello: modello}, function (datam) {
                                                                tmpdesk=datam.Descrizione;
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk);
                                                                })
                                                                $.get(\"index.php?r=articoli/dst\", {tessuto: tessuto}, function (datat) {
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datat.Descrizione);
                                                                })
                                                                 $.get(\"index.php?r=articoli/dsc\", {colore: colore}, function (datac) {
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datac.Descrizione);
                                                                })
                                                                $.get(\"index.php?r=articoli/dsta\", {taglia: taglia}, function (datata) {
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datata.Descrizione);
                                                                })

                                                                } else {
                                                                $(this).val('non ho trovatonulla');
                                                                //passa anche se il campo è vuoto ce vole na fix
                                                                alert('Attenzione non  esistono articoli con le combinazioni selezionate!');
                                                            }
                                                        });  } else{}  ");

$c = new JsExpression("
                                                var conta=this.name;
                                                                         conta=conta.replace('itcode','');
                                                                        conta=conta.replace('InvoiceItem','');
                                                                        conta=conta.replace('[','');
                                                                        conta=conta.replace(']','');
                                                                         conta=conta.replace('[]','');
                                                                    bc=$(\"#invoiceitem-\"+conta+\"-itcode\").val();

                                                  $.get(\"index.php?r=articoli/getar\", {bc: bc}, function (data) {
                                                           if (data !== null ) {
                                                                 modello=   data.Modello;
                                                                   tessuto= data.Tessuto;
                                                                  taglia=  data.Taglia;
                                                                    colore= data.Colore;
                                                                  //  alert(modello);
                                                                $(\"#invoiceitem-\"+conta+\"-total\").val(data.Prezzo2);
                                                                // tmpdesk= new String();
                                                                $.get(\"index.php?r=articoli/dsm\", {modello: modello}, function (datam) {
                                                                  tmpdesk=datam.Descrizione;
                                                                  $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk);
                                                                })
                                                                  $.get(\"index.php?r=articoli/dst\", {tessuto: tessuto}, function (datat) {
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datat.Descrizione);
                                                                })
                                                                 $.get(\"index.php?r=articoli/dsc\", {colore: colore}, function (datac) {
                                                                 alert (colore);
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datac.Descrizione);
                                                                })
                                                                $.get(\"index.php?r=articoli/dsta\", {taglia: taglia}, function (datata) {
                                                                tmpdesk=$(\"#invoiceitem-\"+conta+\"-descrizione\").val();
                                                                $(\"#invoiceitem-\"+conta+\"-descrizione\").val(tmpdesk+' ' +datata.Descrizione);
                                                                })
                                                         } else {
                                                                $(this).val('non ho trovatonulla');
                                                                //if data wasn't found the alert.
                                                                alert('Attenzione non  esistono articoli con il barcode inserito!');
                                                            }
                                                          });
                                                         ");
?>
                                                <?=
$form->field($m, "[{$i}]cd_art")->
textInput(['maxlength' => true, 'placeholder' => 'idarticolo', 'onclick' => $h, 'onchange' => $c]
)->label(false)
?></td>
                                            <td width="65%"><?=
$form->field($m, "[{$i}]descrizione")->
textInput(['maxlength' => true, 'placeholder' => 'description']
)->label(false)
?></td>
                                            </div>
                                            <td width="10%"><?=$form->field($m, "[{$i}]qta")->textInput(['maxlength' => true, 'placeholder' => 'qta'])->label(false)?></td>
                                            <td width="15%">
                                                <?php echo $form->field($m, "[{$i}]prezzo")->textInput(['placeholder' => 'price'])->label(false); ?>
                                            </td>
                                    <td>                        
              
                      
                      <button type="button" class="duplicate-item  btn btn-primary">
                        
                        <i class="glyphicon glyphicon-duplicate">doppia
                        </i></button>
</td>
                                        </tr>
                                    
                                    </table>
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


 
<script>
//$('.duplicate-item').click(function(){
 // var panelBody = $(this).closest('.panel-body');
 // var clonedPanelBody = panelBody.clone(); // Clona il div panel-body
  //clonedPanelBody.find('input').val(''); // Resetta i valori degli input clonati
 // panelBody.after(clonedPanelBody); // Aggiunge il clone dopo il div originale
//});
$(document).on('click', '.duplicate-item', function() {
    console.log('pino');
    var panelBody = $(this).closest('.container-items');
    var clonedPanelBody = panelBody.clone();
panelBody . after(clonedPanelBody);
});
</script>