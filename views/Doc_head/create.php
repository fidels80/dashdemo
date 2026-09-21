<style>
    th.list-cell__drag,
    th.list-cell__nriga,
    th.list-cell__cd_art,
    th.list-cell__descrizione,
    th.list-cell__note,
    th.list-cell__um,
    th.list-cell__iva,
    th.list-cell__qta,
    th.list-cell__prezzo,
    th.list-cell__totale,
    th.list-cell__sconto,
    th.list-cell__prz_tot,
    th.list-cell__button {
        padding: 0;
        /* Rimuove il padding da tutte le celle specificate */
    }

    td.list-cell__cd_art {
        padding: 1px;
        margin: 0px;
        width: 160px;
    }

    td.list-cell__qta {
        padding: 1px;
        margin: 0px;
        width: 70px;
    }

    td.list-cell__prezzo {
        padding: 1px;
        margin: 0px;
        width: 70px;

    }

    td.list-cell__totale {
        padding: 1px;
        margin: 0px;
        width: 100px;

    }

    td.list-cell__sconto {
        padding: 1px;
        margin: 0px;
        width: 100px;

    }

    td.list-cell__prz_tot {
        padding: 1px;
        margin: 0px;
        width: 100px;

    }

    td.list-cell__nriga {

        padding: 1px;
        margin: 0px;
        width: 35px;
    }

    td.list-cell__um {
        padding: 1px;
        margin: 0px;
        width: 50px;
    }

    td.list-cell__iva {
        padding: 1px;
        margin: 0px;
        width: 50px;
    }

    td.list-cell__drag,


    td.list-cell__descrizione,
    td.list-cell__note,








    td.list-cell__button {
        padding: 1px;
        margin: 0px;
        /* Rimuove il padding da tutte le celle specificate */
    }

    td {
        padding: 1px;
        margin: 0px;
    }

    classea {
        width: 160px;
    }
</style>

<?php

use app\models\Anacli;
use app\models\AR;
use app\models\DocType;
use app\models\User;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use unclead\multipleinput\Multipleinput;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

\hail812\adminlte3\assets\FontAwesomeAsset::register($this);
$js    = '';
$usrid = Yii::$app->user->Id;
if (null !== $usrid) {
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
$db        = Yii::$app->db;
$listdataA = ArrayHelper::map($tdA, 'ID', 'name');
$annmodel  = (new \yii\db\Query())
    ->select(['altcli as id', 'Desk as Name'])
    ->from('relcli')
    ->leftJoin('ana_cli', 'relcli.altcli = ana_cli.cd_cli')
    ->where(['relcli.cd_cli' => $usr_ris['cd_cli']])
    ->all();
$listdest = ArrayHelper::map($annmodel, 'id', 'Name');
$tdd      = DocType::find()
    ->select(['[cd_doc] as ID', 'descrizione'])
    ->where(['cd_doc' => 'PRV'])
    ->asArray()
    ->all();
$listdataD = ArrayHelper::map($tdd, 'ID', 'descrizione');
$datam     = AR::find()
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
//yii::warning($dataT);
?>
<?php if (Yii::$app->session->hasFlash('error')) : ?>
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <h4><i class="icon fa fa-check"></i>Saved!</h4>
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>
<div class="customer-form">
    <?php
    $form = \yii\widgets\ActiveForm::begin([
        'id' => 'dynamic-form',
    ]);
    ?>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <?php
    // echo $form->errorSummary($items); 
    ?>
    <div class="row">
        <div class="col-md-4">
            <?php echo
            $form->field($model, 'cd_doc')->widget(Select2::classname(), [
                'data' => $listdataD,
                'options'                            => ['placeholder' => 'Seleziona documento ...', 'id' => 'cd_doc'],
                'pluginOptions'                      => [
                    'allowClear' => true,
                ],
            ]);
            ?>
            <?= $form->field($model, 'numdoc', ['enableClientValidation' => false])->textInput([
                'maxlength' => true,
                'readonly'                                                                                                 => true, 'placeholder' => 'Number'
            ])->label("numero doc") ?>
            <?php

            echo $form->field($model, 'data')->widget(DatePicker::classname(), [
                'options'       => ['placeholder' => 'data'],
                'removeButton'  => false,
                'pluginOptions' => [
                    'autoclose' => true,
                    'format'    => 'dd/mm/yyyy'
                ],
                // yii::warning(date('d/m/y', (strtotime($model->data))));
                //            return date('d/m/y', (strtotime($model->data)));
                //}
            ])->label(false);
            ?>
        </div>
        <div class="col-md-7">
            <?=
            $form->field($model, 'cd_cli')->widget(Select2::classname(), [
                'data' => $listdataA,
                'id'                                 => 'invoice-name',
                'options'                            => ['placeholder' => 'Seleziona anagrafica ...', 'id' => 'lvl-0'],
                'pluginOptions'                      => [
                    'allowClear' => true,
                ],
            ]);
            ?>
            <div>
                <?php //echo $form->field($model, 'altcli')->textInput(['maxlength' => true,
                //'placeholder' => 'ATTN', 'onclick' => 'magsearch()', 'id' => 'piva'])->label(false)
                ?>
                <?= $form->field($model, 'note')->textarea([
                    'rows' => 6,
                    'placeholder' => 'Note',
                    'id' => 'note'
                ])->label(false)
                ?>
                <?php
                // $url = \yii\helpers\Url::to(['index.php?r=contact/list']);
                echo $form->field($model, 'altcli')->widget(DepDrop::classname(), [
                    'data'           => $listdest,
                    'options'        => ['placeholder' => 'carico ...'],
                    'type'           => DepDrop::TYPE_SELECT2,
                    'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                    'pluginOptions'  => [
                        'depends'     => ['lvl-0'],
                        'url'         => Url::to(['/anacli/list']),
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
        ?>


        <?php $url = Url::to('index.php?r=ar/list');
        //$t=new Doc_head();
        //$items=$model->getrows($model->id);

        $zdata = AR::find()
            ->select(['Cd_Ar', "CONCAT(Cd_Ar, '-', Descrizione) as Descrizione"])
            ->orderBy(['Descrizione' => SORT_ASC])
            ->asArray()
            ->all();

        $zmap = array_column($zdata, 'Descrizione', 'Cd_Ar');

        $zmisura = AR::find()
            ->select(['Cd_ARMisura', 'Cd_ARMisura as Descrizione'])
            ->where(['is not', 'Cd_ARMisura', null])
            ->asArray()
            ->all();
        $zmisuramap = array_column($zmisura, 'Cd_ARMisura', 'Cd_ARMisura');

        $q2 = (new \yii\db\Query())
            ->select(['iva as Cd_Aliquota_v', 'iva as Cd_Aliquota_v'])
            ->distinct()
            ->from('web_frontier.dbo.doc_rows');
        //->limit(10);
        $ziva = AR::find()
            ->select(['Cd_Aliquota_v', 'Cd_Aliquota_v'])
            ->where(['is not', 'Cd_Aliquota_v', null])
            ->union($q2)
            ->asArray()
            ->all();

        $zivamap = array_column($ziva, 'Cd_Aliquota_v', 'Cd_Aliquota_v');

        //yii::error( ($model->getDocRows()));
        $model->items = json_encode($model->getRowsall());
        //yii::warning($items);
        ?>
    </div>
    <div class="custom-multiple-input-container">
        <?= $form->field($model, 'docRows')->widget(MultipleInput::className(), [
            'max'                 => 999,
            'min'                 => 1,
            'allowEmptyList'      => false,
            'enableGuessTitle'    => true,
            'sortable'            => true,
            'addButtonPosition'   => [

                MultipleInput::POS_ROW, MultipleInput::POS_FOOTER
            ],
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
            'columns'             => [
                [
                    'name'         => 'nriga',
                    'title'         => 'Riga',
                    'options'       => [
                        'readonly' => true,
                        // 'class' => 'custom-table',
                        'style' => 'width: 30px;'
                        //'style' => 'width: 10%;'
                    ],

                ],
                [
                    'name'         => 'cd_art', 'title' => 'Articolo',
                    'type'          => kartik\select2\Select2::class,
                    //'width' => '160px',

                    'options'       => [
                        // 'style' => 'width: 160px;',
                        //  // 'class' => 'custom-table',
                        'value'         => function ($data) {
                            return $data['cd_art'];
                        },
                        'data'          => $zmap,
                        'value'         => 'Please select...',
                        'pluginOptions' => [
                            'ajax'               => [
                                'url'      => Url::to([
                                    'ar/list',
                                ]),
                                'dataType' => 'json',
                                'data'     => new \yii\web\JsExpression('function(params) {
                             return {
                                 q: params.term
                             };
                        }'),
                            ],
                            //     'class' => 'classea',
                            'size' => Select2::SIZE_X_SMALL,
                            'allowClear'        => true,
                            'language'          => [
                                'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                            ],

                            'escapeMarkup'      => new JsExpression('function (markup) { return markup; }'),
                            'templateResult'    => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.id; }'),
                        ],

                    ],
                ], [
                    'name' => 'descrizione', 'title' => 'Descrizione',
                    'options' => [
                        // 'class' => 'custom-table',
                        'style' => 'width: 100%;'

                    ]
                ],
                [
                    'name'         => 'note', 'title' => 'Note',
                    'type' => 'textarea',
                    'options' => [
                        // 'class' => 'custom-table',
                        'style' => 'width: 100%;'

                    ]
                ],
                [
                    'name'         => 'um', 'title' => 'Um',
                    'type'          => kartik\select2\Select2::class,
                    'options'       => [
                        // 'class' => 'custom-table',
                        'value'         => function ($data) {
                            return $data['um'];
                        },
                        'data'          => $zmisuramap,
                        'value'         => 'Please select...',
                        'pluginOptions' => [
                            // 'width' => '80px',
                            'style' => 'width: 100%;',
                            'allowClear'        => true,
                            'language'          => [
                                'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                            ],
                            'escapeMarkup'      => new JsExpression('function (markup) { return markup; }'),
                            'templateResult'    => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.id; }'),
                        ],

                    ],
                ],

                [
                    'name'         => 'iva', 'title' => 'IVA',
                    'type'          => kartik\select2\Select2::class,
                    'options'       => [
                        // 'class' => 'custom-table',
                        'value'         => function ($data) {
                            return $data['iva'];
                        },
                        'data'          => $zivamap,
                        'value'         => 'Please select...',
                        'pluginOptions' => [
                            'style' => 'width: 100%;',
                            //   'width' => '80px',
                            'allowClear'        => true,
                            'language'          => [
                                'errorLoading' => new JsExpression("function () {
                return 'error caricamento dati...'; }"),
                            ],

                            'escapeMarkup'      => new JsExpression('function (markup) { return markup; }'),
                            'templateResult'    => new JsExpression('function(data) { return data.text; }'),
                            'templateSelection' => new JsExpression('function (data) { return data.id; }'),
                        ],

                    ],
                ], [
                    'name'       => 'qta', 'title' => 'Qta.',

                    'options'       => [
                        // 'class' => 'custom-table',
                        //  'style' => 'width: 100%;',
                        'oninput' =>
                        'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);', 'style' => 'width: 70px;'
                    ],
                    'value'         => function ($data) {
                        return number_format($data['qta'] ?? 0, 3, '.', ','); // Formatta il numero con tre decimali
                    },
                ],
                [
                    'name'         => 'prezzo', 'title' => 'Prezzo',

                    'options'       => [
                        // 'class' => 'custom-table',
                        'style' => 'width: 100%;',
                        'oninput' =>
                        'this.value = this.value.replace(/[^0-9\.]/g, "");validateNumber(this);', 'style' => 'width: 70px;'
                    ],
                    'value' => function ($data) {
                        if ($data !== null && $data instanceof \app\models\Doc_rows) {
                            $dataArray = $data->toArray(); // Converte l'oggetto in un array
                            if (array_key_exists('prezzo', $dataArray) && $dataArray['prz_unit'] == null) {
                                $qta = $dataArray['qta'];
                                if ($qta == 0) {
                                    $qta = 1;
                                };
                                return number_format($dataArray['prezzo'] / $qta, 3, '.', ',');
                            }
                            return $dataArray['prezzo'] ?? 0; // Utilizza 'prz_unit' se 'prezzo' non esiste o è null
                        }
                        return 0; // Valore predefinito se $data è null o non è un'istanza di \app\models\Doc_rows
                    }
                ],
                [
                    'name'         => 'totale',
                    'title' => 'Totale',
                    'options'       => [
                        'style' => 'width: 100px;',
                        // 'class' => 'custom-table',
                        'oninput' =>
                        'this.value = this.value.replace(/[^0-9\.]/g, "");
validateNumber(this);
;',
                        // 'style' => 'width: 80px;',
                        'readonly' => true,

                    ],
                    'value' => function ($data) {
                        if ($data !== null && $data instanceof \app\models\Doc_rows) {
                            $dataArray = $data->toArray(); // Converte l'oggetto in un array
                            if (
                                array_key_exists('prezzo', $dataArray) && $dataArray['prezzo'] <> null
                                && $dataArray['prz_unit'] == null && $dataArray['totale'] == 0
                            ) {
                                return number_format($dataArray['prezzo'], 3, '.', ',');
                            }
                            return $dataArray['totale'] ?? 0; // Utilizza 'prz_unit' se 'prezzo' non esiste o è null
                        }
                        return 0; // Valore predefinito se $data è null o non è un'istanza di \app\models\Doc_rows
                    },

                ],
                [
                    'name'         => 'sconto', 'title' => 'Sconto',
                    'options' => [
                        // 'class' => 'custom-table',
                        // 'style' => 'width: 80px;'
                        'style' => 'width: 100%;',
                    ]
                ],
                [
                    'name'         => 'prz_tot',
                    'title' => 'Totale Scontato',
                    'options'       => [
                        // 'class' => 'custom-table',
                        'oninput' =>
                        'this.value = this.value.replace(/[^0-9\.]/g, "");
validateNumber(this);
;',
                        //  'style' => 'width: 80px;',
                        'style' => 'width: 100%;',
                        'readonly' => true,

                    ],
                    'value' => function ($data) {
                        if ($data !== null && $data instanceof \app\models\Doc_rows) {
                            $dataArray = $data->toArray(); // Converte l'oggetto in un array
                            if (
                                array_key_exists('prezzo', $dataArray) && $dataArray['prezzo'] <> null
                                && $dataArray['prz_unit'] == null && $dataArray['totale'] == 0
                            ) {
                                return number_format($dataArray['prezzo'], 3, '.', ',');
                            }
                            return $dataArray['prz_tot'] ?? 0; // Utilizza 'prz_unit' se 'prezzo' non esiste o è null
                        }
                        return 0; // Valore predefinito se $data è null o non è un'istanza di \app\models\Doc_rows
                    },

                ],

            ],

            // 'data' => $model->docRows,
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




    </div>


















    <div class="form-group">
        <?php
        echo Html::submitButton($model->isNewRecord ? 'Salva' : 'Aggiorna', ['class' =>
        'btn btn-primary'])
        ?>
    </div>
    <?php \yii\widgets\ActiveForm::end(); ?>
    <script type="text/javascript">
        //the dropdown list id; This doesn't have to be a dropdown it can be any field type.
        function magsearch() {
            id = (document.getElementById('lvl-0').value);
            $.get("index.php?r=invoice/get-location-address", {
                id: id
            }, function(data) {
                if (data !== null) {
                    document.getElementById('piva').value = data.PIVA;
                    document.getElementById('indi').value = data.Indirizzo;
                } else {
                    //if data wasn't found the alert.
                    alert('We\'re sorry but we couldn\'t load the the location data!');
                }
            });
        };
    </script>
    <script type="text/javascript">
        //the dropdown list id; This doesn't have to be a dropdown it can be any field type.
        function itemsearch(id, desc) {
            //  id=(document.getElementById('rg').value);
            $.get("index.php?r=invoice/Itcode-Search", {
                id: id
            }, function(data) {
                if (data !== null) {
                    document.getElementById('desc').value = data.Descrizione;
                    //document.getElementById('indi').value=data.Indirizzo;
                } else {
                    //if data wasn't found the alert.
                    alert('We\'re sorry but we couldn\'t load the the location data!');
                }
            });
        };
    </script>
</div>



<script>
    $(document).ready(function() {

        $(document).on("change", "[id*=-qta]", function() {
            //$this.val(99);
            var itemVal = $(this).attr("name");
            console.log(itemVal);
            var conta = $(this).attr("name");
            conta = conta.replace('qta', '');
            conta = conta.replace('Doc_head', '');
            conta = conta.replace('docRows', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[]', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            console.log(conta);
            var prezzo = parseFloat($("#doc_head-docrows-" + conta + "-prezzo").val());
            // doc_head-docrows-7-prezzo
            console.log(prezzo);
            // qta=console . log(prezzo);

            var totale = parseFloat($(this).val()) * prezzo;
            console.log(totale);
            $("#doc_head-docrows-" + conta + "-totale").val(totale.toFixed(3));
        });

        $(document).on("change", "[id*=-prezzo]", function() {
            //$this.val(99);
            var itemVal = $(this).attr("name");
            //console.log(itemVal);
            var conta = $(this).attr("name");
            conta = conta.replace('prezzo', '');
            conta = conta.replace('Doc_head', '');
            conta = conta.replace('docRows', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[]', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            console.log(conta);
            var qta = $("#doc_head-docrows-" + conta + "-qta").val();
            var totale = parseFloat($(this).val()) * qta;
            $("#doc_head-docrows-" + conta + "-totale").val(totale.toFixed(3));
        });

        $(document).on("change", "[id*=-sconto]", function() {
            //$this.val(99);
            var itemVal = $(this).attr("name");
            console.log(itemVal);
            var conta = $(this).attr("name");
            conta = conta.replace('sconto', '');
            conta = conta.replace('Doc_head', '');
            conta = conta.replace('docRows', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[]', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            console.log(conta);
            var totale = $("#doc_head-docrows-" + conta + "-totale").val();
            var totale_sc = totale - ((totale / 100) * $(this).val())

            //parseFloat($(this).val())*qta;
            $("#doc_head-docrows-" + conta + "-prz_tot").val(totale_sc.toFixed(3));
        });
        //});
        //$(document).ready(function() {
        $(document).on("change", "[id*=-cd_art]", function() {
            var itemVal = $(this).val();
            console.log("cane");
            //console.log( itemVal);
            //console.log($(this).attr("name"));
            var conta = $(this).attr("name");
            // console.log(conta);
            conta = conta.replace('cd_art', '');
            conta = conta.replace('Doc_head', '');
            conta = conta.replace('docRows', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[]', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[]', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            conta = conta.replace('[', '');
            conta = conta.replace(']', '');
            console.log(conta);
            $.get("index.php?r=ar/getardet", {
                ar: this.value
            }, function(datam) {
                console.log(datam.Cd_Aliquota_V);
                $("#doc_head-docrows-" + conta + "-descrizione").val(datam.Descrizione);
                $("#doc_head-docrows-" + conta + "-um").val(datam.Cd_ARMisura).trigger('change');
                $("#doc_head-docrows-" + conta + "-iva").val(datam.Cd_Aliquota_V).trigger('change');
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