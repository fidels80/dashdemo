 
<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use unclead\multipleinput\Multipleinput;
 
use yii\bootstrap4\Button;
 
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
 
use unclead\multipleinput\TabularInput;
use app\models\sottocommessa;
use app\models\Auxar;
?>
 
 
<div class="xtravelrow-form">

<?php $form = ActiveForm::begin(); ?>

<div class="card">
    <div class="card-header bg-primary text-white">Dati Generali</div>
    <div class="card-body row">
 <div class="col-md-3"><?= $form->field($model, 'guest')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-3"><?= $form->field($model, 'ruolo')->
        widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'ruoli']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],
        'dropdownParent' => new JsExpression("$('#yii-modaljs')"),
    ],

])  ?></div>
            <div class="col-md-2"><?= $form->field($model, 'stato')->
  widget(Select2::class, [
    'data' => [
        'DP' => 'Da Prenotare',
        'P'  => 'Prenotato',
        'A'  => 'Annullato',
    ],
    'options' => [
        'placeholder' => 'Seleziona stato...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
])?></div>
</div>
<div class="card-body row">

<div class="col-md-3">
   <?= $form->field($model, 'sottocommessa')->widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'dropdownParent' => new JsExpression("$('#yii-modaljs')"),
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'sottocommessa']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],
    ],
                      'pluginEvents' => [
'select2:select' => new JsExpression(<<<JS
function (e) {
let data = e.params.data;
let descrizionePura = data.text.replace(data.id, "").trim().replace(/^[-–—]\s*/, "");
$("#xtravelrow-x_scdesc").val(descrizionePura);
}
JS

),
],
]) ?>
</div>
<div class="col-md-3"><?= $form->field($model, 'x_scdesc')
->textInput(['maxlength' => true])->label('Descrizione Commessa') ?>
</div>
</div>


<div class="card-body row">
        <div class="col-md-3"><?= $form->field($model, 'cd_Ar')->
       widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'dropdownParent' => new JsExpression("$('#yii-modaljs')"),
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'art']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],
                'pluginEvents' => [
'select2:select' => new JsExpression(<<<JS
function (e) {
let data = e.params.data;
let descrizionePura = data.text.replace(data.id, "").trim().replace(/^[-–—]\s*/, "");
$("#xtravelrow-descrizione").val(descrizionePura);
}
JS

),
],
])?></div>
        <div class="col-md-6"><?= $form->field($model, 'descrizione')->
        textInput(['maxlength' => true]) ?></div>
        </div>
        <div class="card-body row">
        <div class="col-md-3"><?= $form->field($model, 'qta')->textInput() ?></div>
        <div class="col-md-3"><?= $form->field($model, 'prezzo')->textInput() ?></div>
</diV>
    


<div class="card">
    <div class="card-header bg-secondary text-white">Cliente e Fornitore</div>
    <div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'cd_cf_ft')->
       widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'cli']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],
                'pluginEvents' => [
'select2:select' => new JsExpression(<<<JS
function (e) {
let data = e.params.data;
let descrizionePura = data.text.replace(data.id, "").trim().replace(/^[-–—]\s*/, "");
$("#xtravelrow-descli").val(descrizionePura);
}
JS

),
],
])?></div>
        <div class="col-md-4"><?= $form->field($model, 'descli')->
        textInput(['maxlength' => true])->label('Descrizione Cliente') ?></div>
</div>
<div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'citta')->
widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'citta']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],

])  ?></div>
        <div class="col-md-4"><?= $form->field($model, 'struttura')->
          widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'struttura']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],

]) ?></div>
</div>
        <div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'fornitore')->
               widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'for']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],
                'pluginEvents' => [
'select2:select' => new JsExpression(<<<JS
function (e) {
let data = e.params.data;
let descrizionePura = data.text.replace(data.id, "").trim().replace(/^[-–—]\s*/, "");
$("#xtravelrow-desfor").val(descrizionePura);
}
JS

),
],
]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'desfor')->
        textInput(['maxlength' => true])->label('Descrizione Fornitore') ?></div>
</div>
  
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">Dati Viaggio</div>
    <div class="card-body row">
        <div class="col-md-4">

  <?= $form->field($model, 'check_in')->widget(DateTimePicker::class, [
    'removeButton' => false,
    'options' => [
        'placeholder' => 'Seleziona data e ora...',
        'value' => $model->check_in ? Yii::$app->formatter->asDatetime($model->check_in, 'php:Y-m-d H:i:s') : null,
    ],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayHighlight' => true,
        'showMeridian' => false, // 24h
        'todayBtn' => true,
        'initialDate' => date('Y-m-d')
    ]
])  ?>

        </div>
        <div class="col-md-4"><?= $form->field($model, 'check_out')->widget(DateTimePicker::class, [
    'removeButton' => false,
    'options' => [
        'placeholder' => 'Seleziona data e ora...',
        'value' => $model->check_in ? Yii::$app->formatter->asDatetime($model->check_in, 'php:Y-m-d H:i:s') : null,
    ],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayHighlight' => true,
        'showMeridian' => false, // 24h
        'todayBtn' => true,
        'initialDate' => date('Y-m-d')
    ]
])   ?></div>
</div>
<div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'citta_da')->
         widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'citta']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],

])?></div>
        <div class="col-md-4"><?= $form->field($model, 'citta_a')->
         widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'citta']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],

]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'orario')->textInput() ?></div>
</div>
<div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'pnr')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'nr_biglietto')->textInput(['maxlength' => true]) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning text-dark">Fatturazione</div>
    <div class="card-body row">
        <div class="col-md-4"><?= $form->field($model, 'totale')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'tax')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'fee')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'fee_perc')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'imponibile')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'iva')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'Totalegenerale')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'tax_unit')->textInput() ?></div>
        <div class="col-md-4"><?= $form->field($model, 'codiva')->           widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'iva']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],
                'pluginEvents' => [
'select2:select' => new JsExpression(<<<JS
function (e) {
let data = e.params.data;

}
JS

),
],
]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'totfattura')->textInput() ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">Altro</div>
    <div class="card-body row">

        <div class="col-md-2"><?= $form->field($model, 'data_pg')->widget(DateTimePicker::class, [
    'removeButton' => false,
    'options' => [
        'placeholder' => 'Seleziona data e ora...',
        'value' => $model->check_in ? Yii::$app->formatter->asDatetime($model->check_in, 'php:Y-m-d H:i:s') : null,
    ],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayHighlight' => true,
        'showMeridian' => false, // 24h
        'todayBtn' => true,
        'initialDate' => date('Y-m-d')
    ]
])  ?></div>
        <div class="col-md-2"><?= $form->field($model, 'cd_pg')->
         widget(Select2::class, [
    'options' => [
        'placeholder' => 'Please select...',
    ],
    'pluginOptions' => [
        'allowClear' => false,
        'minimumInputLength' => 2,
        'ajax' => [
            'url' => Url::to(['xtravelhead/loadmenu', 'tab' => 'credito']),
            'dataType' => 'json',
            'delay' => 500,
            'cache' => true,
            'data' => new JsExpression('function(params) { return {q: params.term}; }'),
            'processResults' => new JsExpression('function(data) {
                return {
                    results: $.map(data.results, function (item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    })
                };
            }'),
        ],
        'language' => [
            'errorLoading' => new JsExpression("function () {
                return 'Errore nel caricamento dei dati...'; 
            }"),
        ],

    ],

])
          ?>
          </div>
           <div class="col-md-4"><?= $form->field($model, 'descontab')->
           textInput(['maxlength' => true]) ?></div>
            <div class="col-md-2"><?= $form->field($model, 'pagato')->checkbox() ?></div>

        </div>
        <div class="col-md-10"><?= $form->field($model, 'note')->
        textarea(['rows' => 4])?></div>
          <div class="col-md-4"><?= $form->field($model, 'numero')->textInput(['maxlength' => true]) ?></div>
        <div class="col-md-4"><?= $form->field($model, 'datah')->widget(DateTimePicker::class, [
    'removeButton' => false,
    'options' => [
        'placeholder' => 'Seleziona data e ora...',
        'value' => $model->check_in ? Yii::$app->formatter->asDatetime($model->check_in, 'php:Y-m-d H:i:s') : null,
    ],
    'pluginOptions' => [
        'autoclose' => true,
        'format' => 'yyyy-mm-dd hh:ii:ss',
        'todayHighlight' => true,
        'showMeridian' => false, // 24h
        'todayBtn' => true,
        'initialDate' => date('Y-m-d')
    ]
])  ?></div>
               <div class="col-md-4"><?= $form->field($model, 'x_pagato')->textInput() ?></div>
    </div>


<div class="form-group mt-3">
    <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>
</div>
</div>
    </div>
    </div>

 
