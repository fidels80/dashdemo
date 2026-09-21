<script type="text/javascript" src="https://unpkg.com/default-passive-events"></script>

<style>
div.scroll {
            margin: 4px, 4px;
            padding: 4px;
            background-color: white;
            width:100%;
            height: 400px;
            overflow-x: auto;
            overflow-y: auto;
            white-space: nowrap;
        }

</style>

<?php
ini_set('memory_limit', '8G');
use kartik\date\DatePicker;
use kartik\datetime\DateTimePicker;
use unclead\multipleinput\Multipleinput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
//use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap4\Button;
 
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
 
use unclead\multipleinput\TabularInput;
use app\models\sottocommessa;
use app\models\Auxar;
 
use yii\db\Query;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Xtravelhead */
/* @var $form yii\widgets\ActiveForm */

use app\models\User; // Assicurati che il percorso sia corretto
 

// Recupera gli utenti per la dropdown
$usersList = ArrayHelper::map(User::find()->orderBy('username')->all(), 'id', 'username');

$usrid = Yii::$app->user->Id;
if (null !== $usrid) {
    $usr_ris = (new \yii\db\Query ())
        ->select(['email', 'gruppo', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
}

function formatEuro($number) {
    return number_format($number, 3, ',', '.'); // 2 decimali, ',' come separatore decimali, '.' come separatore migliaia
}
 
// Add custom CSS
$css = <<<CSS
.multiple-input-compact {
    font-size: 12px;
    line-height: 1.2;
}

.multiple-input-compact .row-compact {
    margin: 0;
    padding: 2px;
}

.multiple-input-compact .col-compact {
    padding: 2px;
}

.multiple-input-compact .form-control {
    padding: 4px;
    height: auto;
}

.multiple-input-compact .select2-container--krajee .select2-selection--single {
    height: 28px;
}

.multiple-input-compact .select2-container--krajee .select2-selection--single .select2-selection__rendered {
    padding: 2px 8px;
    line-height: 24px;
}

/* Make numeric columns more compact */
.multiple-input-compact input[type="number"] {
    width: 80px;
}

/* Make date/time columns more compact */
.multiple-input-compact .krajee-datepicker {
    width: 120px;
}

.select2-auto-width {
    min-width: 200px !important;  /* Larghezza minima */
    width: auto !important;        /* Adatta la larghezza al contenuto */
    max-width: 100%;               /* Evita che si allarghi troppo */
    white-space: nowrap;           /* Impedisce di andare a capo */
}
.select2-container {
    min-width: 200px !important; /* Imposta una larghezza minima */
    width: auto !important; /* Adatta la larghezza al contenuto */
    max-width: 100% !important; /* Impedisce di allargarsi oltre il contenitore */
}
.select2-selection {
    min-width: 200px !important;
}


CSS;

$this->registerCss($css);

?>
<?php echo Html::a('Torna indietro', Yii::$app->request->referrer ?: ['index'], ['class' => 'btn btn-secondary']);?>

<script>
$(document).ready(function() {
    setTimeout(function() {
        $('.select2-container').each(function() {
            $(this).css('min-width', '200px');
            $(this).css('width', 'auto');
        });
    }, 500);
});

    </script>
<?php



$col = [
    ['name'   => 'sottocommessa',
        'title'   => 'SottC',
        'type'    => kartik\select2\Select2::class,
        
        'options' => [
         // 'size' => Select2::SMALL,
         //  'data'          => $zmapcomme,
          //  'value'         => function ($data) {
          //      return $data['sottocommessa'];
         //   },
           //'data'=>$listcommessa,
            'value'         => 'Please select...',
            'pluginOptions' => [
           //  'width' => '100px',
                 'allowClear'         => false,
                'minimumInputLength' => 2,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'sottocommessa']),
                        'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                ],
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ]]
            
            
            ],     
],
      ['name'   => 'guest',
        'title'   => 'Nominativo',
        'type'    => 'textInput',
'options' => ['style' => 'width: 100px;  ']
    ],

    ['name'            => 'ruolo',
        'title'            => 'Ruolo',
        'type'             => kartik\select2\Select2::class,
        'options'          => [
          //    'size' => Select2::SMALL,
         //   'value'         => function ($data) {
         //       return $data['ruolo'];
         //   }, 
          //  'data'          => $zmapruoli,
            
            'value' => 'Please select...',
            'pluginOptions' => [
     // 'width' => '100px',
                 'allowClear'         => false,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'ruoli']),
                    'dataType' => 'json',
                        'delay' => 500,
                        'cache' => true, // Abilita la cache

                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
 
    ],
  ['name'            => 'party',
        'title'            => 'Party',
        'type'             => kartik\select2\Select2::class,
        'options'          => [
            //  'size' => Select2::SMALL,
            //    'data'          => $zmapparty,
            //'value'         => function ($data) {
            //    return $data['party'];
            //},
             'value' => 'Please select...',
            'pluginOptions' => [
        //'width' => '80px',
                 'allowClear'         => false,
                'minimumInputLength' => 1,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',

                        'tab' => 'party']),
                                                'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],


                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
 
    ],
     ['name'   => 'citta',
        'title'   => 'Città',
        'type'    => kartik\select2\Select2::class,

        'options' => [
             // 'size' => Select2::SMALL,
            //'size' => Select2::MEDIUM ,
      //      'value'         => function ($data) {
      //          return $data['citta'];
      //      },
 // 'data'          => $zmapcitta,
            'value'         => 'Please select...',
            'pluginOptions' => [
          //  'width' => '120px',
                 'allowClear'         => false,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'citta']),
                                                'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
                
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],  

    ],
    ['name'         => 'cd_Ar',
        'title'         => 'Articolo',
        'type'          => kartik\select2\Select2::class,
        'options'       => [
         //     'size' => Select2::SMALL,
     //       'value'         => function ($data) {
     //           return $data['cd_Ar'];
     //       }, 
           // 'data'          => $zmapart,
            
            'value' => 'Please select...',
            'pluginOptions' => [
        //    'width' => '180px',
                 'allowClear'         => false,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'art']),
                                                'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
               

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
 

    ],
   ['name'         => 'struttura',
        'title'         => 'Struttura',
        'type'          => kartik\select2\Select2::class,
        'options'       => [
            //  'size' => Select2::SMALL,
       //     'value'         => function ($data) {
       //         return $data['struttura'];
       //     }, 'value' => 'Please select...',
      //        'data'          => $zmapstruttura,
            'pluginOptions' => [
             //   'width' => '180px',
                 'allowClear'         => false,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'struttura']),
                                                'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'dataType' => 'json',
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return {
                                 q: params.term

                             };
                        }'),
                ],
              

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ],
        //'columnOptions' => ['class' => 'col-lg-6'],
 
    ],
    ['name'   => 'citta_da',
        'title'   => 'Da',
        'type'    => kartik\select2\Select2::class,
        'options' => [
           //   'size' => Select2::SMALL,
            //'size' => Select2::MEDIUM ,
       //     'value'         => function ($data) {
       //         return $data['citta_da'];
       //     },
//  'data'          => $zmapcitta,
            'value'         => 'Please select...',
            'pluginOptions' => [
          // 'width' => '120px',
                 'allowClear'         => false,
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
               
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], //'headerOptions' => ['style' => 'width: 200px;'],
 
    ],
    ['name'   => 'citta_a',
        'title'   => 'A',
        'type'    => kartik\select2\Select2::class,
        'options' => [
            'value'         => 'Please select...',
            'pluginOptions' => [
               //   'theme' => 'bootstrap',
    //'width' => 'resolve', // Select2 calcola la larghezza automaticamente
                'allowClear'         => false,
                'minimumInputLength' => 3,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu', 'tab' => 'citta']),
                    'dataType' => 'json',
                    'delay' => 250, // Aggiunto delay per ridurre le chiamate
                    'cache' => true, // Aggiunto caching
                    'data'     => new \yii\web\JsExpression ('function(params) {
                        return { q: params.term };
                    }'),
                ],
               
                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                        return 'error caricamento dati...'; 
                    }"),
                ],
                 //'containerCssStyle' => 'select2-custom-width',
                 'width' => 'auto', 
            ],
            // 'containerCssClass' => 'select2-custom-width',
                    'options' => [
           'class' => 'select2-auto-width',
        ],
 
        ],
 
    ],

[
    'name'   => 'check_in',
    'title'  => 'Check In',
    'type'   => kartik\datetime\DateTimePicker::class,
    'options' => [
        'type'          => \kartik\datetime\DateTimePicker::TYPE_INPUT,
                'options'       => [
           // 'class' => 'form-control form-control-lg', // Aggiunge una classe di Bootstrap per ingrandire l'input
            'style' => 'width: 150px;', // Imposta una larghezza personalizzata (modifica a piacere)
        ],
        'pluginOptions' => [
            'autoclose'     => true,
            'format'        => 'yyyy-mm-dd hh:ii', // formato data e ora
            'todayHighlight'=> true,
            'todayBtn'      => true,
            'minuteStep'    => 5, // Opzionale: step per i minuti
            'showMeridian'  => false, // Imposta su true per formato 12h con AM/PM
        ],
    ],
],
[
    'name'   => 'check_out',
    'title'  => 'Check Out',
    'type'   => kartik\datetime\DateTimePicker::class,
    'options' => [
        'type'          => \kartik\datetime\DateTimePicker::TYPE_INPUT,
                'options'       => [
           // 'class' => 'form-control form-control-lg', // Aggiunge una classe di Bootstrap per ingrandire l'input
            'style' => 'width: 150px;', // Imposta una larghezza personalizzata (modifica a piacere)
        ],
        'pluginOptions' => [
            'autoclose'     => true,
            'format'        => 'yyyy-mm-dd hh:ii', // formato data e ora
            'todayHighlight'=> true,
            'todayBtn'      => true,
            'minuteStep'    => 5, // Opzionale: step per i minuti
            'showMeridian'  => false, // Imposta su true per formato 12h con AM/PM
        ],
    ],
]
,
 
    ['name'   => 'pnr',
        'title'   => 'Pnr',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 100px;  ']
  ],
    ['name'   => 'nr_biglietto',
        'title'   => 'Biglietto Nr.',
        'type'    => 'textInput',
        'options' => ['style' => 'width: 100px;  ']
     /*   'options' => ['style' => 'width: 60px; font-size: 12px; padding: 0px; height: 32px;'],
'headerOptions' => ['style' => 'width:60px; font-size:11px; padding:0px;'],
        'columnOptions' => ['style' => 'width:60px; padding:0px;'] 
*/],
    ['name'   => 'qta',
        'title'   => 'Qta.',
//'type' => 'textInput',
'options' => ['style' => 'width: 100px;  ']  
],
    ['name'   => 'prezzo',
        'title'   => 'Prezzo',
        'type'    => 'textInput',
 'value' => function($data) {
        return formatEuro($data['prezzo']??0);
    },
'options' => ['style' => 'width: 100px;  ']
  ],
    ['name'   => 'codiva',
        'title'   => 'Cod.Iva',
         'type'    => kartik\select2\Select2::class,
        'options' => [
            //  'size' => Select2::SMALL,
            //'size' => Select2::MEDIUM ,
          //  'value'         => function ($data) {
          //      return $data['codiva'];
          //  },
      //'data'          => $zmapiva,
            'value'         => 'Please select...',
            'pluginOptions' => [
                'width'              => '100px',
                'allowClear'         => false,
                'minimumInputLength' => 2,
                'initValueText'      => '',
                'ajax'               => [
                    'url'      => Url::to(['xtravelhead/loadmenu',
                        'tab' => 'iva']),
                    'dataType' => 'json',
                                            'delay' => 500,
                        'cache' => true, // Abilita la cache
                    'data'     => new \yii\web\JsExpression ('function(params) {
                             return { q: params.term};
                        }'),
                ],

                'language'           => [
                    'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                ],
            ],
        ], //'headerOptions' => ['style' => 'width: 50px;'],
         //  'headerOptions' => ['style' => 'width: 80px;']
 
    ],

    ['name' => 'totale',
        'title' => 'Totale',
        'type'  => 'textInput',  
        'value' => function($data) {
        return formatEuro($data['totale']??0);
    },
            'options' => ['style' => 'width: 100px;  '],    
  ],
    ['name' => 'tax_unit',
        'title' => 'Tassa Unitaria',
        'type'  => 'textInput',   
        'value' => function($data) {
        return formatEuro($data['tax_unit']??0);
    },
    'options' => ['style' => 'width: 100px;  ']
    ],
  /*  ['name' => 'tax',
        'title' => 'Tax',
        'type'  => 'textInput', 
        'value' => function($data) {
        return formatEuro($data['tax']??0);
    },
        
        'options' => ['style' => 'width: 60px; font-size: 12px; padding: 2px; height: 32px;'],
'headerOptions' => ['style' => 'width:80px; font-size:11px; padding:2px;'],
        'columnOptions' => ['style' => 'width:80px; padding:1px;'] ],

*/
    ['name' => 'fee_perc',
        'title' => 'Fee %',
        'type'  => 'textInput',  
        'value' => function($data) {
        return formatEuro($data['fee_perc']??0);
    },
    'options' => ['style' => 'width: 100px;  ']
 ],
   /* ['name' => 'fee',
        'title' => 'Fee',
        'type'  => 'textInput',       
        'value' => function($data) {
        return formatEuro($data['fee']??0);
    },
        'options' => ['style' => 'width: 60px; font-size: 12px; padding: 2px; height: 32px;'],
'headerOptions' => ['style' => 'width:80px; font-size:11px; padding:2px;'],
        'columnOptions' => ['style' => 'width:80px; padding:1px;'] ],

        */
    ['name' => 'totfattura',
        'title' => 'Totale Fattura',
        'type'  => 'textInput',       
        'value' => function($data) {
        return formatEuro($data['totfattura']??0);
    },
    'options' => ['style' => 'width: 100px;  ']
  ],
    ['name' => 'imponibile',
        'title' => 'Imponibie',
        'type'  => 'textInput',       
        'value' => function($data) {
        return formatEuro($data['imponibile']??0);
    },
    'options' => ['style' => 'width: 100px;  ']
     ],
    ['name' => 'iva',
        'title' => 'Iva',
        'type'  => 'textInput',        
        'value' => function($data) {
        return formatEuro($data['iva']??0);
    },
    'options' => ['style' => 'width: 100px;  ']
  ],
   /* ['name' => 'Totalegenerale',
        'title' => 'Totale Generale',
        'type'  => 'textInput',       
        'value' => function($data) {
        return formatEuro($data['Totalegenerale']??0);
    },
        
        'options' => ['style' => 'width: 60px; font-size: 12px; padding: 2px; height: 32px;'],
'headerOptions' => ['style' => 'width:80px; font-size:11px; padding:2px;'],
        'columnOptions' => ['style' => 'width:80px; padding:1px;'] ],*/
    ['name' => 'stato',
        'title' => 'stato',
        'type'  => 'dropDownList',
        'items' => [
            'P'  => 'Prenotato',
            'DP' => 'Da Prenotare',
        ],
 'options' => ['style' => 'width: 100px;  ']
    ]
    ,
   
];

?>

<div class="xtravelhead-form">



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
    'readonly'=> true,
    'value' => isset($model->totaleservizi) ? formatEuro($model->totaleservizi) : '',
    
    ])->label('Totale Servizi')?>
          <?=$form->field($model, 'tax')->textInput(['maxlength' => true,
    'readonly'=> true,
    'value' => isset($model->tax) ? formatEuro($model->tax) : '',])->label('Totale Tasse')?>
     <?=$form->field($model, 'fee')->textInput(['maxlength' => true,
    'readonly'  => true,
    'value' => isset($model->fee) ? formatEuro($model->fee) : '',])->label('Totale Fee')?>
          <?=$form->field($model, 'totft')->textInput(['maxlength' => true,
    'readonly' => true,'value' => isset($model->totft) ? formatEuro($model->totft) : '',])->label('Totale Fatturato')?>
          <?=$form->field($model, 'righe')->textInput(['maxlength' => true,
    'readonly' => true])->label('Totale Righe')?>
</div>
    </div>
    </div>
<div class="scroll">
<?php
 

echo $form->field($model, 'travelRows')->widget(MultipleInput::className(), [
    'max'                 => 999,
    'min'                 => 1,
    'allowEmptyList'      => false,
    'enableGuessTitle'    => true,
     'sortable' =>true,
    'addButtonPosition'   => [
    MultipleInput::POS_ROW, MultipleInput::POS_FOOTER],
    'cloneButton'         => true, 
    'cloneButtonOptions' => [
     'label' => '<i class="fas fa-clone"></i>', // Icona per il clone
        'encodeLabel' => false, // Importante per permettere HTML nel label

    ],
    'addButtonOptions'    => [
       // 'class' => 'btn btn-success',
       // 'label' => 'Aggiungi', // also you can use html code
       'label' => '<i class="fas fa-plus"></i>', // Icona per aggiungere
        'encodeLabel' => false,
    ],
    'removeButtonOptions' => [
       // 'label' => 'remove',
       // 'class' => 'btn btn-danger',
       'label' => '<i class="fas fa-trash-alt"></i>', // Icona per rimuovere
        'encodeLabel' => false,
    ],
    'showGeneralError'    => true,
    'columns'             => $col,
       'id' => 'tabl_pre',
           'rowOptions' => [
               'id' => 'row{multiple_index_tabl_pre}',
               'num' => '{multiple_index_tabl_pre}',
              // 'class' =>  'adr_str',
           ],
     //'rendererClass' => \unclead\multipleinput\renderers\ListRenderer::class,
    
     
])->label(false);;


?>
</div>
  <div class="form-group">
        <?=Html::submitButton('Save', ['class' => 'btn btn-success'])?>
    </div>
    <?php ActiveForm::end();?>

</div>
<script>

    </script>

<?php 














?>