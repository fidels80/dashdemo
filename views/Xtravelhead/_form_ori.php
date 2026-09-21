<?php

use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use unclead\multipleinput\Multipleinput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap4\Button;
 
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
 

use app\models\sottocommessa;
use app\models\Auxar;
 
 

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */
/* @var $form yii\widgets\ActiveForm */


$usrid = Yii::$app->user->Id;
if (null !== $usrid) {
    $usr_ris = (new \yii\db\Query ())
        ->select(['email', 'gruppo', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}


 
?>
<?php echo Html::a('Torna indietro', Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-secondary']);?>


<?php


$col = [
    ['name'   => 'sottocommessa',
        'title'   => 'Sottocommessa',
        'type'    => Select2::class,
        'options' => [
            'value'         => function ($data) {
                return $data['sottocommessa'];
            },
            //'data'=>$listcommessa,
            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 2,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'sottocommessa']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
    var result = data.text ;
    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return   data.text;
                    }'),
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ]]], 'columnOptions' => ['class' => 'col-lg-6']],
    ['name' => 'x_scdesc',
        'title' => 'Descrizione Commessa',
        'type'  => 'textInput'],
    ['name'   => 'guest',
        'title'   => 'Nominativo',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 180px;'],
    ],

    ['name'            => 'ruolo',
        'title'            => 'Ruolo',
        'type'             => kartik\select2\Select2::class,
        'options'          => [
            'value'         => function ($data) {
                return $data['ruolo'];
            }, 'value' => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'ruoli']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                                              return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {


    var result = data.text ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {

                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        'attributeOptions' => [
            'style' => 'width: 200px;',
        ],
    ],

    ['name'            => 'cd_cf_ft',
        'title'            => 'Cliente',
        'type'             => kartik\select2\Select2::class,
        'options'          => [
            'value'         => function ($data) {
                return $data['cd_cf_ft'];
            }, 'value' => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'cli']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                                              return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {


    var result = data.text ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {

                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        'attributeOptions' => [
            'style' => 'width: 200px;',
        ],
    ],
    ['name' => 'descli',
        'title' => 'Descrizione Cliente',
        'type'  => 'textInput', 'options' => ['style' => 'width: 200px;']],

    ['name'   => 'citta',
        'title'   => 'Città',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            //'size' => Select2::MEDIUM ,
            'value'         => function ($data) {
                return $data['citta'];
            },

            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'citta']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
                           if (data.loading) {
        return data.text;
    }

    var result = data.id ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], 'headerOptions' => ['style' => 'width: 200px;'],

    ],
    ['name'         => 'cd_Ar',
        'title'         => 'Articolo',
        'type'          => kartik\select2\Select2::class,
        'options'       => [
            'value'         => function ($data) {
                return $data['cd_Ar'];
            }, 'value' => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'art']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {

    console.log(data);
    var result = data.text ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {

                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        'columnOptions' => ['class' => 'col-lg-6'],

    ],
    ['name'   => 'descrizione',
        'title'   => 'Descrizione',
        'type'    => 'textInput',

        'options' => ['style' => 'width: 200px;'],

    ],
    ['name'            => 'fornitore',
        'title'            => 'Fornitore',
        'type'             => kartik\select2\Select2::class,
        'options'          => [
            'value'         => function ($data) {
                return $data['fornitore'];
            }, 'value' => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'for']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                                              return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {


    var result = data.text ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {

                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        'attributeOptions' => [
            'style' => 'width: 200px;',
        ],
    ],
    ['name' => 'desfor',
        'title' => 'Descrizione Fornitore',
        'type'  => 'textInput'],
    ['name'         => 'struttura',
        'title'         => 'Struttura',
        'type'          => kartik\select2\Select2::class,
        'options'       => [
            'value'         => function ($data) {
                return $data['struttura'];
            }, 'value' => 'Please select...',
            'pluginOptions' => [
                'width'              => '200px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'struttura']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {


    var result = data.text ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {

                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        'columnOptions' => ['class' => 'col-lg-6'],

    ],
    ['name'   => 'citta_da',
        'title'   => 'Da',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            //'size' => Select2::MEDIUM ,
            'value'         => function ($data) {
                return $data['citta_da'];
            },

            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '150px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'citta']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
                           if (data.loading) {
        return data.text;
    }

    var result = data.id ;

    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return data.text;
                    }'),

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], 'headerOptions' => ['style' => 'width: 200px;'],

    ],
    ['name'   => 'citta_a',
        'title'   => 'A',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            //'size' => Select2::MEDIUM ,
            'value'         => function ($data) {
                return $data['citta_a'];
            },
            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '250px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'citta']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
                           if (data.loading) {
        return data.text;
    }
    var result = data.id ;
    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return data.text;
                    }'),
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], 'headerOptions' => ['style' => 'width: 200px;'],

    ],

    ['name'   => 'check_in',
        'title'   => 'Check In',
        'type'    => kartik\datetime\DateTimePicker::class,
        'options' => [
            'type'          => DateTimePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'dd/mm/yyyy h:i',
            ],
        ],
    ],
    ['name'   => 'check_out',
        'title'   => 'Check Out',
        'type'    => kartik\datetime\DateTimePicker::class,
        'options' => [
            'type'          => DateTimePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'dd/mm/yyyy h:i',
            ],
        ],

    ],
    ['name'   => 'pnr',
        'title'   => 'Pnr',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 180px;']],
    ['name'   => 'nr_biglietto',
        'title'   => 'Biglietto Nr.',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 180px;']],

    ['name'   => 'qta',
        'title'   => 'Quantità',
//'type' => 'textInput',
        'options' => [
            'value' => function ($data) {
                return $data['qta'];
            },
            'style' => 'width: 80px;',
        ]],
    ['name'   => 'data_pg',
        'title'   => 'Data Pagamento',
        'type'    => kartik\date\DatePicker::class,
        'options' => [
            'type'          => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'dd/mm/yyyy',
            ],
        ],
    ],
    ['name'   => 'cd_pg',
        'title'   => 'Mezzo Pagamento',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            //'size' => Select2::MEDIUM ,
            'value'         => function ($data) {
                return $data['cd_pg'];
            },
            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '250px',
                'allowClear'         => true,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'credito']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
                           if (data.loading) {
        return data.text;
    }
    var result = data.id ;
    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return data.text;
                    }'),
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], 'headerOptions' => ['style' => 'width: 200px;'],

    ],
    ['name'   => 'prezzo',
        'title'   => '   Prezzo   ',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 80px;'],
    ],
    ['name'   => 'codiva',
        'title'   => 'Cod.Iva',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            //'size' => Select2::MEDIUM ,
            'value'         => function ($data) {
                return $data['codiva'];
            },
            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '250px',
                'allowClear'         => true,
                'minimumInputLength' => 2,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'iva']),
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                ],
                'escapeMarkup'       => new \yii\web\JsExpression ('function (markup) {
                        return markup;
                    }'),
                'templateResult'     => new \yii\web\JsExpression ('function(data) {
                           if (data.loading) {
        return data.text;
    }
    var result = data.id ;
    return result;
                    }'),
                'templateSelection'  => new \yii\web\JsExpression ('function (data) {
                        return data.text;
                    }'),
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], 'headerOptions' => ['style' => 'width: 200px;'],

    ],

    ['name' => 'totale',
        'title' => 'Totale',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'tax_unit',
        'title' => 'Tassa Unitaria',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'tax',
        'title' => 'Tax',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'fee_perc',
        'title' => 'Fee %',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'fee',
        'title' => 'Fee',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'totfattura',
        'title' => 'Totale Fattura',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'imponibile',
        'title' => 'Imponibie',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'iva',
        'title' => 'Iva',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'Totalegenerale',
        'title' => 'Totale Generale',
        'type'  => 'textInput', 'options' => ['style' => 'width: 80px;']],
    ['name' => 'stato',
        'title' => 'stato',
        'type'  => 'dropDownList',
        'items' => [
            'P'  => 'Prenotato',
            'DP' => 'DA Prenotare',

        ], 'options' => ['style' => 'width: 110px;'],
    ]
    ,

    ['name'   => 'orario',
        'title'   => 'Orario',
        'type'    => kartik\datetime\DateTimePicker::class,
        'options' => [
            'type'          => DateTimePicker::TYPE_INPUT,
            'pluginOptions' => [
                'autoclose' => true,
                'format'    => 'dd/mm/yyyy h:i',
            ],
        ],

    ],
    ['name' => 'contabile',
        'title' => 'Contabile',
        'type'  => 'textInput'],
    ['name' => 'evadi_A',
        'title' => 'Evasione A',
        'type'  => 'checkbox', 'columnOptions' => [
            'style' => 'width: 20px;',
        ]]
    ,
    ['name'   => 'evadi_p',
        'title'   => 'Evasione P',
        'type'    => 'checkbox',
        'options' => [
            'style' => 'width: 20px;',
        ],
    ],

    ['name' => 'note',
        'title' => 'Note',
        'type'  => 'textInput'],
    ['name' => 'descontab',
        'title' => 'Des.Contabile',
        'type'  => 'textInput'],
    ['name'         => 'pagato',
        'title'         => 'Pagato',
        'type'          => 'checkbox',
        'columnOptions' => [
            'style' => 'width: 20px;',
            //'class' => 'col col-lg-2',
            //'readonly' => true,
        ]],

    ['name' => 'x_pagato',
        'title' => 'Importo Pagato',
        'type'  => 'textInput'],

];

?>

<div class="xtravelhead-form">


<style>
 
.clearfix::after {
    content: "";
    display: table;
    clear: both;
    }
    .custom-wrapper-class > .mi-row:nth-child(4n+5) {
    clear: both;
}
input[name^="Xtravelhead[travelRows]"][name$="[check_in]"]
{
    width: 200px; // Imposta la larghezza desiderata
}
input[name^="Xtravelhead[travelRows]"][name$="[check_out]"]
{
    width: 200px; // Imposta la larghezza desiderata
}
input[name^="Xtravelhead[travelRows]"][name$="[orario]"]
{
    width: 200px; // Imposta la larghezza desiderata
}
input[name^="Xtravelhead[travelRows]"][name$="[data_pg]"]
{
    width: 200px; // Imposta la larghezza desiderata
}
.custom-multiple-input {
    width: 80%; /* Imposta la larghezza desiderata */
}
 
</style>
    <?php 

    
    
    $form = ActiveForm::begin(
 




    );?>
<div class="container">
    <div class="row">
    <div class="col">
    <?php echo $form->field($model, 'datath')->widget(DateTimePicker::classname(), [
    'options'       => ['placeholder' => 'data'],
    'removeButton'  => false,
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd/mm/yyyy hh:ii'],
    // yii::warning(date('d/m/y', (strtotime($model->data))));
    //            return date('d/m/y', (strtotime($model->data)));
    //}
]) ?>

    <?=$form->field($model, 'numero')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'descrizione')->textInput(['maxlength' => true])?>

    <?=$form->field($model, 'timeins')->textInput(['hidden' => true])->label(false)?>

<div class="row">
     <div class="col">
    <?=$form->field($model, 'evaso_A')->checkBox(['disabled' => true])?>
    </div>
 <div class="col">
    <?=$form->field($model, 'evaso_p')->checkBox(['disabled' => true])?>
</div> <div class="col">
<?=$form->field($model, 'fatturato')->checkBox(['disabled' => true])?>
</div>
</div>
    </div>
    <div class="col">
     <?=$form->field($model, 'totaleservizi')->textInput(['maxlength' => true,
    'readonly'                                                            => true])->label('Totale Servizi')?>
          <?=$form->field($model, 'tax')->textInput(['maxlength' => true,
    'readonly'                                                       => true])->label('Totale Tasse')?>
     <?=$form->field($model, 'fee')->textInput(['maxlength' => true,
    'readonly'                                                  => true])->label('Totale Fee')?>
          <?=$form->field($model, 'totft')->textInput(['maxlength' => true,
    'readonly'                                                         => true])->label('Totale Fatturato')?>
          <?=$form->field($model, 'righe')->textInput(['maxlength' => true,
    'readonly'                                                         => true])->label('Totale Righe')?>
</div>
    </div>
    </div>
<div>
<?php
 

echo $form->field($model, 'travelRows')->widget(MultipleInput::className(), [
    'max'                 => 999,
    'min'                 => 1,
    'allowEmptyList'      => false,
    'enableGuessTitle'    => true,
     'sortable' =>true,
    'addButtonPosition'   => [

        MultipleInput::POS_ROW, MultipleInput::POS_FOOTER],
    'cloneButton'         => true, 'cloneButtonOptions' => [
        'class' => 'btn btn-warning',
        'label' => 'Clona', // also you can use html code
    ],
    'addButtonOptions'    => [
        'class' => 'btn btn-success',
        'label' => 'Aggiungi', // also you can use html code
    ],
    'removeButtonOptions' => [
        'label' => 'remove',
        'class' => 'btn btn-danger',

    ],

    'showGeneralError'    => true,
 'layoutConfig' => [
        'offsetClass' => 'col-md-auto',
        'labelClass' => 'col-md-auto',
        'wrapperClass' => 'col-md-auto my-custom-class', 
        'errorClass' => 'col-md-auto',
        'buttonActionClass' => 'col-md-auto',
        'options' => [
        'style' => 'width: 80%;', // Imposta la larghezza del 20% per tutti gli elementi
    ],
       ], 
       'options' => [
        'class' => 'custom-multiple-input',
    ],
    'columns'             => $col,
    
     //'rendererClass' => \unclead\multipleinput\renderers\ListRenderer::class,
    
     
])->label(false);;
?>
</div>
  <div class="form-group">
        <?=Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>

</div>


<?php 
$js = <<<JS


/*
$(document).ready(function() {
  // $(document).on('mouseenter', '.select2-selection__choice', function() {
      /*  var \$choice = $(this);
        var title = \$choice.attr('title');
        \$choice.attr('title', '');

        if (title) {
            var description = \$choice.find('.description').text();
            \$choice.attr('title', title + (description ? ' - ' + description : ''));
        }*/
      // console.log('cane');
   // });
/*
$(document).on('afterInit', '.multiple-input', function() {
    $('.select2').each(function() {
        var \$select2 = $(this);
        if (!\$select2.data('select2')) {
            \$select2.select2({
                templateSelection: function(data) {
                    console.log(data)
                    var description = data.description || "";
                    return data.id + "<span class=\"description\">" 
                    + description + "</span>";
                }
            });
        }
    });
});
*/
JS;

$this->registerJs($js,\yii\web\View::POS_READY);

?>