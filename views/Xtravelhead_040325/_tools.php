<?php

use onmotion\apexcharts\ApexchartsWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\grid\GridView;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;


$this->registerCssFile('@web/css/custom-styles.css', ['depends' => [\yii\web\YiiAsset::class]]);

?>
<?php
echo rand();
$db = Yii::$app->db5;

$command = $db->createCommand("select cd_citta as ID,descrizione as Desk from x_citta order by cd_citta asc ");
$lcittà = $command->queryAll();
$lcittà2 = ArrayHelper::map($lcittà, 'ID', 'Desk');



// Aggiungi qui il pulsante per aprire la modale
echo Html::button('Aggiungi Tappe', [
    'class' => 'btn btn-primary mt-3',
    'data-toggle' => 'modal',
    'data-target' => '#tappeModal',
]);

Modal::begin([
    'id' => 'tappeModal',
    'title' => '<h4>Scegli le Tappe del Tour</h4>', // Imposta il titolo della modale qui
]);

echo '<div id="modalContent">';
echo Html::beginForm(['xtravelhead/savetappe'], 'post', ['id' => 'tappe-form']);

echo '<div class="form-group">';
echo Html::label('Numero di Tappe', 'num-tappe');
echo Html::input('number', 'num-tappe', null, ['class' => 'form-control', 'id' => 'num-tappe']);
echo '</div>';

echo '<div id="tappe-fields">';
// I campi per le tappe verranno aggiunti qui via JavaScript
echo '</div>';

echo Html::submitButton('Salva', ['class' => 'btn btn-success']);
echo Html::endForm();

echo '</div>';

Modal::end();



?>


<br>Dettaglio tappe<br>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Citta</th>
        <th>data</th>
        <th>evaso</th>
        <th>id</th>

    </thead>
    <?php
    foreach ($listatappetool as $value) {
        echo '<tr>';
        echo '<td>';
        echo $value['citta'];
        echo '</td>';
        echo '<td>';
        echo $value['data'];
        echo '</td>';
        echo '<td>';
        echo $value['evaso'] == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
        echo '</td>';
        echo '<td>';
        echo $value['id_tappa'];
        echo '</td>';

        echo '</tr>';
    }

    ?>
</table>

<?php
// Pulsante per aprire la modale per l'inserimento dei nominativi
echo Html::button('Aggiungi Nominativo', [
    'class' => 'btn btn-primary mt-3',
    'data-toggle' => 'modal',
    'data-target' => '#nominativoModal',
]);

Modal::begin([
    'id' => 'nominativoModal',
    'title' => '<h4>Aggiungi Nominativo</h4>',
]);

echo '<div id="modalNominativoContent">';
echo Html::beginForm(['xtravelhead/savenominativo'], 'post', ['id' => 'nominativo-form']);

echo '<div class="form-group">';
echo Html::label('Nominativo', 'nominativo');
echo Html::input('text', 'nominativo', null, ['class' => 'form-control', 'id' => 'nominativo']);
echo '</div>';

echo '<div class="form-group">';
echo Html::label('cd_Ar', 'cd_ar');

$listaart2 = ArrayHelper::map($listaart, 'ID', 'Desk');
yii::error($listaart2);
echo Select2::widget([
    'name' => 'cd_Ar',
    'data' => $listaart2, // Array contenente le opzioni per il cd_Ar
    'options' => [
        'placeholder' => 'Seleziona cd_Ar...',
        'id' => 'cd_Ar',
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 0
    ],
]);



echo '</div>';
echo '<div class="form-group">';
echo Html::label('Nota', 'Nota');
echo Html::textarea('nota', null, ['class' => 'form-control', 'id' => 'nota', 'rows' => 5]); // Specifica il numero di righe
echo '</div>';
echo '<div class="form-group">';
echo Html::label('th_id', 'th_id');
echo Html::input('text', 'th_id', $th_id, ['class' => 'form-control', 'id' => 'th_id', 'readonly' => true]); // Campo non modificabile
echo '</div>';
echo Html::submitButton('Salva', ['class' => 'btn btn-success']);
echo Html::endForm();

echo '</div>';

Modal::end();


?>


<br>Dettaglio RoomList<br>
<table class="table table-sm table-hover table-responsive-sm table-fit">
    <thead class="thead-dark">
        <th>Citta</th>
        <th>data</th>
        <th>nota</th>
        <th>evaso</th>

    </thead>
    <?php
    foreach ($roomlist as $value) {
        echo '<tr>';
        echo '<td>';
        echo isset($value['guest'])? $value['guest']:'' ;
        echo '</td>';
        echo '<td>';
        echo isset($value['cd_Ar'])?$value['cd_Ar']:'';
        echo '</td>';
        echo '<td>';
        echo isset($value['note'])?$value['note']:'';
        echo '</td>';
        echo '<td>';
        echo(isset($value['evaso'])?$value['evaso']:'' )== 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
        echo '</td>';

        echo '</tr>';
    }

    ?>
</table>


<script>
    /*$('#num-tappe').on('change', function() {
        var numTappe = $(this).val();
        var tappeFields = $('#tappe-fields');
        tappeFields.empty();
        console.log("cacca");
        for (var i = 1; i <= numTappe; i++) {
            tappeFields.append(
                '<div class="form-group">' +
                '<label for="data-tappa-' + i + '">Data Tappa ' + i + '</label>' +
                '<input type="date" name="Tappe[' + i + '][data]" class="form-control" id="data-tappa-' + i + '">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="citta-tappa-' + i + '">Città Tappa ' + i + '</label>' +
                '<input type="text" name="Tappe[' + i + '][citta]" class="form-control" id="citta-tappa-' + i + '">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="id-tappa-' + i + '">id Tappa ' + i + '</label>' +
                '<input type="text" name="Tappe[' + i + '][id]" class="form-control" id="citta-tappa-' + i + '"  placeholder="<?php echo $th_id; ?>" value="<?php echo $th_id; ?>">' +
                '</div>'


            );
        }
    });



    */



    $('#num-tappe').on('change', function() {
        var numTappe = $(this).val();
        var tappeFields = $('#tappe-fields');
        tappeFields.empty();
        console.log("Generazione campi tappe...");

        // Serializza l'array delle città per utilizzarlo nel JavaScript
        var cittaOptions = <?php echo json_encode($lcittà2); ?>;
        var cittaOptionsUrl = "<?php echo \Yii::$app->urlManager->createUrl(['xtravelhead/xcaricacitta']); ?>";

        for (var i = 1; i <= numTappe; i++) {
            var select2Id = 'citta-tappa-' + i;

            tappeFields.append(
                '<div class="form-group">' +
                '<label for="data-tappa-' + i + '">Data Tappa ' + i + '</label>' +
                '<input type="date" name="Tappe[' + i + '][data]" class="form-control" id="data-tappa-' + i + '">' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="' + select2Id + '">Città Tappa ' + i + '</label>' +
                '<select name="Tappe[' + i + '][citta]" class="form-control select2" id="' + select2Id + '"></select>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="id-tappa-' + i + '">id Tappa ' + i + '</label>' +
                '<input type="text" name="Tappe[' + i + '][id]" class="form-control" id="id-tappa-' + i + '"  placeholder="<?php echo $th_id; ?>" value="<?php echo $th_id; ?>">' +
                '</div>'
            );
            console.log($.fn.select2);

            // Inizializza Select2 con dati statici
            /*    $('#' + select2Id).select2({
                    placeholder: 'Seleziona una città...',
                    allowClear: true,
                    minimumInputLength: 3,
                    data: $.map(cittaOptions, function(value, key) {
                        return {
                            id: key,
                            text: value
                        };
                    })
                });*/

            $('#' + select2Id).select2({
                placeholder: 'Seleziona una città...',
                allowClear: true,
                minimumInputLength: 3,
                ajax: {
                    url: cittaOptionsUrl, // Questo è l'URL dell'AJAX
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // Invia il termine di ricerca al server
                        };
                    },
                    processResults: function(data) {
                        console.log(data);
                        return {

                            results: data.items
                        };
                    },
                    cache: true
                }
            });

        }
    });

    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = $.noop;
        $('#nominativoModal').removeAttr('tabindex');
        $('#tappeModal').removeAttr('tabindex');


        $('#tappe-form').on('submit', function(event) {
            event.preventDefault(); // Previene il normale submit del form

            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $('#tappeModal').modal('hide');

                        // Ricarica solo il tab con l'ID 'tools'
                        $.ajax({
                            url: location.href, // Usa l'URL corrente per ricaricare il tab
                            type: 'get',
                            success: function(html) {
                                var newTabContent = $(html).find('#w3-tab0').html(); // Trova il contenuto del tab
                                console.log('dentro success');
                                console.log(location.href);
                                $('#tools').html(newTabContent); // Aggiorna il contenuto del tab
                            },
                            error: function() {
                                alert('Si è verificato un errore nella richiesta. ajax');
                            }
                        });
                    } else {
                        alert('Errore durante il salvataggio del nominativo.');
                    }
                },
                error: function() {
                    alert('Si è verificato un errore nella richiesta. post ');
                }
            });

            return false; // Impedisce il submit normale
        });
        console.log('JS Loaded2');
        // Collega nuovamente gli eventi
        $('#nominativo-form').on('submit', function(event) {
            event.preventDefault(); // Previene il normale submit del form

            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $('#nominativoModal').modal('hide');

                        // Ricarica solo il tab con l'ID 'tools'
                        $.ajax({
                            url: location.href, // Usa l'URL corrente per ricaricare il tab
                            type: 'get',
                            success: function(html) {
                                var newTabContent = $(html).find('#tools').html(); // Trova il contenuto del tab
                                $('#tools').html(newTabContent); // Aggiorna il contenuto del tab
                            },
                            error: function() {
                                alert('Si è verificato un errore nella richiesta.');
                            }
                        });
                    } else {
                        alert('Errore durante il salvataggio del nominativo.');
                    }
                },
                error: function() {
                    alert('Si è verificato un errore nella richiesta.');
                }
            });

            return false; // Impedisce il submit normale
        });




    });
</script>

<?php /*$this->registerJs("
    $('#tappe-form').on('beforeSubmit', function(event) {
        event.preventDefault(); // Previene il normale submit del form

        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#tappeModal').modal('hide'); // Nasconde la modale
                    $.pjax.reload({container: '#tab-content'}); // Ricarica solo il tab specifico
                } else {
                    alert('Errore durante il salvataggio delle tappe.');
                }
            },
            error: function() {
                alert('Si è verificato un errore nella richiesta.');
            }
        });

        return false;
    });

    $('#nominativo-form').on('beforeSubmit', function(event) {
        event.preventDefault(); // Previene il normale submit del form

        $.ajax({
            url: $(this).attr('action'),
            type: 'post',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#nominativoModal').modal('hide'); // Nasconde la modale
                    $.pjax.reload({container: '#tab-content'}); // Ricarica solo il tab specifico
                } else {
                    alert('Errore durante il salvataggio del nominativo.');
                }
            },
            error: function() {
                alert('Si è verificato un errore nella richiesta.');
            }
        });

        return false;
    });
");*/
?>