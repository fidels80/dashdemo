<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
use app\models\XVenue;

$rand = rand();
$db = Yii::$app->db5;
$command = $db->createCommand("select id, venue+'-'  +citta from x_venue order by citta asc  ");
$lcittà = $command->queryAll();
$lcittà2 = ArrayHelper::map($lcittà, 'ID', 'Desk');



// Aggiungi qui il pulsante per aprire la modale
echo Html::button('Aggiungi Tappe', [
    'class' => 'button-base button-lift',
    'data-toggle' => 'modal',
    'data-target' => '#tappeModal',
]);
echo '    ';
                        echo Html::button('Aggiungi Nominativo', [
                            'class' => 'button-base button-lift',
                            'data-toggle' => 'modal',
                            'data-target' => '#nominativoModal',
                        ]);

echo '    ';


    $url = Url::to(['xtravelhead/wizardrighe', 'th_id' => $mth_id]);

    echo Html::button('Modifica Dati', [
        'class' => 'button-base-support button-lift',
        'onclick' => 'window.location.href = "' . $url . '"'
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

echo Html::submitButton('Salva', ['class' => 'button-base button-lift']);
echo Html::endForm();


echo '<br>';
echo Html::button('Crea Nuova Venue', [
    'class' => 'button-base button-lift',
    'data-toggle' => 'modal',
    'data-target' => '#venueModal',
]);




echo '</div>';
Modal::end();


Modal::begin([
    'id' => 'venueModal',
    'title' => '<h4>Crea Nuova Venue</h4>',
    'size' => Modal::SIZE_LARGE,
]);
echo '<div id="modalContent2">';
$modelVenue = new XVenue();
// Qui carichi direttamente il contenuto del form
echo $this->render('/xvenue/createaj', [
    'model' => $modelVenue,   // passi il modello di create
]);
echo '</div>';
Modal::end();
?>



<br>Dettaglio tappe<br>
<div class="scrollable-table-container">
    <table class="table " id="tabella-tappe">
        <thead>
            <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Venue</th>
            <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Data</th>
            <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Evaso</th>
            <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Azioni</th>
            <th style=" display:none; font-size: 16px;
         background-color: #f1eef6; color: #002c48;"></th>

        </thead>
        <tbody>
            <?php
            foreach ($mlistatappetool as $value) {
                echo '<tr>';
                echo '<td>';
                $citta = $value['citta'];

                if (preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $citta)) {
                    // È un UUID valido
                    $tras = Xvenue::find()->where(['id' => $citta])->one();
                    if ($tras) {
                        echo $tras->citta . ' - ' . $tras->venue;
                    } else {
                        echo "Città non trovata per ID $citta";
                    }
                } else {
                    // È una stringa normale
                    echo $citta;
                }
                echo '</td>';
                echo '<td>';
                echo         date('d/m/y', strtotime($value['data']));
                echo '</td>';
                echo '<td>';
                echo $value['evaso'] == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
                echo '</td>';
                echo '<td>';
                // Pulsante elimina solo se non è evaso
                $isEvaso = $value['evaso'] == 1;
                if (!$isEvaso) {
                    echo Html::button('<i class="fas fa-trash"></i>', [
                        'class' => 'btn btn-danger btn-sm elimina-tappa',
                        'data-id' => $value['id_tappa'],
                        'title' => 'Elimina tappa'
                    ]);
                } else {
                    //echo '<span class="text-muted">Non eliminabile</span>';
                    $url = Url::to(['xtappe/update', 'id' => $value['id_tappa']]);

                    echo Html::button('<i class="fas fa-pen"></i>', [
                        'title' => 'Modifica nominativo',
                        'class' => 'btn btn-warning btn-sm',
                        'onclick' => 'window.location.href = "' . $url . '"'
                    ]);
                }
                echo '</td>';
                echo '<td style = "display:none">';
                echo $value['id_tappa'];
                echo '</td>';

                echo '</tr>';
            }

            ?>
        </tbody>
    </table>

    <?php
    // Pulsante per aprire la modale per l'inserimento dei nominativi
  

    Modal::begin([
        'id' => 'nominativoModal',
        'title' => '<h4>Aggiungi Nominativo</h4>',
    ]);
    $partyData = (new \yii\db\Query())
        ->select(['party as cd_party', 'party as dparty'])
        ->from('xtravelrow')
        ->where(['not', ['party' => null]])
        ->distinct()
        ->createCommand(Yii::$app->db5)
        ->queryAll();

    $ruoloData = (new \yii\db\Query())
        ->select(['cd_ruolo as cd_ruolo', 'descrizione  as druolo'])
        ->from('xruoli')
        ->where(['not', ['cd_ruolo' => null]])
        ->distinct()
        ->createCommand(Yii::$app->db5)
        ->queryAll();
    $commessaData = (new \yii\db\Query())
        ->select(['Cd_DOSottoCommessa as cd_commessa', 'Descrizione as dcommessa'])
        ->from('DOSottoCommessa')
        ->where(['not', ['Cd_DOSottoCommessa' => null]])
        ->distinct()
        ->createCommand(Yii::$app->db5)
        ->queryAll();

    $partyOptions = ArrayHelper::map($partyData, 'cd_party', 'dparty');
    $ruoloOptions = ArrayHelper::map($ruoloData, 'cd_ruolo', 'druolo');
    $commessaOptions = ArrayHelper::map($commessaData, 'cd_commessa', 'dcommessa');
    echo '<div id="modalNominativoContent">';
    echo Html::beginForm(['xtravelhead/savenominativo'], 'post', ['id' => 'nominativo-form']);

    echo '<div class="form-group">';
    echo Html::label('Nominativo', 'nominativo');
    echo Html::input('text', 'nominativo', null, ['class' => 'form-control', 'id' => 'nominativo']);
    echo '</div>';

    echo '<div class="form-group">';
    echo Html::label('cd_Ar', 'cd_ar');

    $listaart2 = ArrayHelper::map($listaart, 'ID', 'Desk');
    //yii::error($listaart2);
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
    echo Html::label('party', 'party');
    echo Select2::widget([
        'name' => 'party',
        'data' => $partyOptions, // Array contenente le opzioni per il cd_Ar
        'options' => [
            'placeholder' => 'Seleziona party...',
            'id' => 'party',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0
        ],
    ]);

    echo '</div>';
    echo '<div class="form-group">';
    echo Html::label('Ruolo', 'ruolo');
    echo Select2::widget([
        'name' => 'ruolo',
        'data' => $ruoloOptions, // Array contenente le opzioni per il cd_Ar
        'options' => [
            'placeholder' => 'Seleziona ruolo...',
            'id' => 'ruolo',
        ],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 0
        ],
    ]);

    echo '</div>';
    echo '<div class="form-group">';
    echo Html::label('Commessa', 'commessa');
    echo Select2::widget([
        'name' => 'commessa',
        'data' => $commessaOptions, // Array contenente le opzioni per il cd_Ar
        'options' => [
            'placeholder' => 'Seleziona commessa...',
            'id' => 'commessa',
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
    echo Html::input('text', 'th_id', $mth_id, ['class' => 'form-control', 'id' => 'th_id', 'readonly' => true]); // Campo non modificabile
    echo '</div>';
    echo Html::submitButton('Salva', ['class' => 'button-base button-lift']);
    echo Html::endForm();

    echo '</div>';

    Modal::end();


    ?>



    <br>Dettaglio RoomList<br>
    <div class="scrollable-table-container">
        <table class="table" name="roomlist">
            <thead>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Ospite</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Prodotto</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Ruolo</th>

                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Party</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Commessa</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">nota</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">evaso</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Azioni</th>


            </thead>
            <tbody>
                <?php
                foreach ($mroomlist as $value) {
                    echo '<tr>';
                    echo '<td>';
                    echo isset($value['guest']) ? $value['guest'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo isset($value['cd_Ar']) ? $value['cd_Ar'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo isset($value['ruolo']) ? $value['ruolo'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo isset($value['party']) ? $value['party'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo isset($value['commessa']) ? $value['commessa'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo isset($value['note']) ? $value['note'] : '';
                    echo '</td>';
                    echo '<td>';
                    echo (isset($value['evaso']) ? $value['evaso'] : '') == 1 ? '<i class="fas fa-check"></i>' : '<i class="fas fa-stop"></i>';
                    echo '</td>';

                    $isEvasoRoom = (isset($value['evaso']) ? $value['evaso'] : '') == 1;

                    echo '<td>';
                    // Pulsante elimina solo se non è evaso
                    if (!$isEvasoRoom) {
                        echo Html::button('<i class="fas fa-trash"></i>', [
                            'class' => 'btn btn-danger btn-sm elimina-nominativo',
                            'data-id' => isset($value['id_guest']) ? $value['id_guest'] : '',
                            'title' => 'Elimina nominativo'
                        ]);
                    } else {
                        //echo '<span class="text-muted">Non eliminabile</span>';
                        $url = Url::to(['xroomlist/update', 'id' => $value['id_guest']]);

                        echo Html::button('<i class="fas fa-pen"></i>', [
                            'title' => 'Modifica nominativo',
                            'class' => 'btn btn-warning btn-sm',
                            'onclick' => 'window.location.href = "' . $url . '"'
                        ]);
                    }
                    echo '</td>';
                    echo '</tr>';
                }

                ?>
            </tbody>
        </table>
        <br>
    </div>
    <br>
    

</div>

<script>
    $('#num-tappe').on('change', function() {
        var numTappe = $(this).val();
        var tappeFields = $('#tappe-fields');
        tappeFields.empty();
        console.log("Generazione campi tappe...");

        // Serializza l'array delle città per utilizzarlo nel JavaScript
        var cittaOptions = <?php echo json_encode($lcittà2); ?>;
        var cittaOptionsUrl = "<?php echo \Yii::$app->urlManager->createUrl(['xtravelhead/xcaricatappa']); ?>";

        for (var i = 1; i <= numTappe; i++) {
            var select2Id = 'citta-tappa-' + i;

            tappeFields.append(
                '<div class="form-group">' +
                '<label for="data-tappa-' + i + '">Data Tappa ' + i + '</label>' +
                '<input type="date" name="Tappe[' + i + '][data]" class="form-control datepicker" id="data-tappa-' + i + '">' +

                '</div>' +
                '<div class="form-group">' +
                '<label for="' + select2Id + '">Città Tappa ' + i + '</label>' +
                '<select name="Tappe[' + i + '][citta]" class="form-control select2" id="' + select2Id + '"></select>' +
                '</div>' +
                '<div class="form-group">' +
                '<label for="id-tappa-' + i + '">id Tappa ' + i + '</label>' +
                '<input type="text" readonly name="Tappe[' + i + '][id]" class="form-control" id="id-tappa-' +
                i + '"  placeholder="<?php echo $mth_id; ?>" value="<?php echo $mth_id; ?>">' +
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


        $(document).on('focus', 'input.datepicker', function() {
            $(this).datepicker({
                dateFormat: 'dd/mm/yy' // formato giorno/mese/anno (ultimi 2 cifre)
            });
        });


        // Event handler per eliminazione tappe

        $(document).on('click', '.elimina-tappa', function() {
            var idTappa = $(this).data('id');
            var $row = $(this).closest('tr');

            if (confermaEliminazioneTappa(idTappa, $row)) {
                eliminaTappa(idTappa, $row);
            }
        });

        // Event handler per eliminazione nominativi
        $(document).on('click', '.elimina-nominativo', function() {
            var idNominativo = $(this).data('id');
            var $row = $(this).closest('tr');

            if (confermaEliminazioneNominativo(idNominativo, $row)) {
                eliminaNominativo(idNominativo, $row);
            }
        });




        $('#nominativoModal').on('show.bs.modal', function() {
            // Reset tutti i campi tranne commessa
            $('#nominativo').val('');
            $('#cd_Ar').val(null).trigger('change');
            $('#party').val(null).trigger('change');
            $('#ruolo').val(null).trigger('change');
            $('#nota').val('');
            // Non resettiamo la commessa come richiesto
        });


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
                        alert('Salvataggio effettuato');
                        aggiungiTappeAllaTabella(response.tappe);
                        // Ricarica solo il tab con l'ID 'tools'


                    } else {
                        alert('Errore durante il salvataggio delle tappe .');
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

                        // Dati appena inseriti
                        const data = response.data;
                        const nuovaRiga = `
            <tr class="nuova-tappa">
                <td>${data.nominativo}</td>
                <td>${data.cd_ar}</td>
                <td>${data.ruolo}</td>
                <td>${data.party}</td>
                <td>${data.commessa}</td>
                <td>${data.note}</td>
                <td><i class="fas fa-stop"></i></td>
               <td> <button type="button" class="btn btn-warning btn-sm" 
                title="Modifica nominativo" onclick="window.location.href = &quot;/index.php?r=xroomlist%2Fupdate&amp;id=${response.id_guest}&quot;"><i class="fas fa-pen"></i></button>
            </td>
                </tr>
        `;

                        // Aggiunge la riga all'inizio del tbody (subito dopo thead)
                        $('table[name="roomlist"] tbody').prepend(nuovaRiga);

                        alert('Salvataggio effettuato');
                    } else {
                        let errorMsg = 'Errore durante il salvataggio del nominativo.';

                        if (response.error) {
                            if (typeof response.error === 'string') {
                                errorMsg = response.error;
                            } else if (typeof response.error === 'object') {
                                // Se è un oggetto (es. Yii::$model->getErrors()), trasformalo in stringa
                                errorMsg = Object.entries(response.error)
                                    .map(([field, errors]) => `${field}: ${errors.join(', ')}`)
                                    .join('\n');
                            }
                        }

                        alert("ERRORE:\n" + errorMsg);
                    }
                },
                error: function() {
                    alert('Si è verificato un errore nella richiesta.');
                }
            });

            return false; // Impedisce il submit normale
        });

        // Funzione per la doppia conferma eliminazione tappa
        function confermaEliminazioneTappa(idTappa, $row) {
            // Prima conferma
            if (!confirm('Sei sicuro di voler eliminare questa tappa?\n\nATTENZIONE: Questa azione non può essere annullata!')) {
                return false;
            }

            // Seconda conferma
            //   if (!confirm('ULTIMA CONFERMA\n\nStai per eliminare definitivamente la tappa.\nConfermi l\'eliminazione?')) {
            //       return false;
            //   }

            return true;
        }

        // Funzione per la doppia conferma eliminazione nominativo
        function confermaEliminazioneNominativo(idNominativo, $row) {
            var nomeOspite = $row.find('td:first').text().trim();

            // Prima conferma
            if (!confirm('Sei sicuro di voler eliminare il nominativo: ' + nomeOspite + '?\n\nATTENZIONE: Questa azione non può essere annullata!')) {
                return false;
            }

            // Seconda conferma
            //  if (!confirm('ULTIMA CONFERMA\n\nStai per eliminare definitivamente il nominativo: ' + nomeOspite + '\nConfermi l\'eliminazione?')) {
            //     return false;
            // }

            return true;
        }

        // Funzione per eliminare la tappa via AJAX
        function eliminaTappa(idTappa, $row) {
            $.ajax({
                url: '<?php echo Url::to(['xtravelhead/eliminatappa']); ?>',
                type: 'POST',
                data: {
                    id_tappa: idTappa
                },
                beforeSend: function() {
                    $row.addClass('eliminazione-in-corso');
                    $row.find('.elimina-tappa').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        alert('Tappa eliminata con successo!');
                    } else {
                        alert('Errore durante l\'eliminazione della tappa: ' + (response.error || 'Errore sconosciuto'));
                        $row.removeClass('eliminazione-in-corso');
                        $row.find('.elimina-tappa').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Errore di comunicazione con il server: ' + error);
                    $row.removeClass('eliminazione-in-corso');
                    $row.find('.elimina-tappa').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }

        // Funzione per eliminare il nominativo via AJAX
        function eliminaNominativo(idNominativo, $row) {
            $.ajax({
                url: '<?php echo Url::to(['xtravelhead/eliminanominativo']); ?>',
                type: 'POST',
                data: {
                    id_nominativo: idNominativo
                },
                beforeSend: function() {
                    $row.addClass('eliminazione-in-corso');
                    $row.find('.elimina-nominativo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    console.log(response);

                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                        alert('Nominativo eliminato con successo!');
                    } else {
                        alert('Errore durante l\'eliminazione del nominativo: ' + (response.error || 'Errore sconosciuto'));
                        $row.removeClass('eliminazione-in-corso');
                        $row.find('.elimina-nominativo').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Errore di comunicazione con il server: ' + error);
                    $row.removeClass('eliminazione-in-corso');
                    $row.find('.elimina-nominativo').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }


    });
</script>



<script>
    function aggiungiTappeAllaTabella(tappe) {
        const tbody = document.querySelector('#tabella-tappe tbody');

        tappe.forEach(tappa => {
            const row = document.createElement('tr');
            row.classList.add('nuova-tappa'); // per lo sfondo verde

            // Trasforma la data in formato dd/mm/yyyy
            let formattedDate = '';
            if (tappa.data) {
                const d = new Date(tappa.data);
                const day = String(d.getDate()).padStart(2, '0');
                const month = String(d.getMonth() + 1).padStart(2, '0'); // i mesi partono da 0
                const year = String(d.getFullYear()).slice(-2); // ultimi 2 numer
                formattedDate = `${day}/${month}/${year}`;
            }

            row.innerHTML = `
    
            <td>${tappa.citta}</td>
           <td>${formattedDate}</td>
            <td>${tappa.evaso ? 'Sì' : 'No'}</td>
             <td style = "display:none">${tappa.id_tappa}</td>
          <button type="button" class="btn btn-warning btn-sm" 
          title="Modifica Tappa" 
          onclick="window.location.href = &quot;/index.php?r=xtappe%2Fupdate&amp;id=${tappa.id_tappa}&quot;"><i class="fas fa-pen"></i></button>
        `;

            // Inserisci la riga all'inizio della tabella
            if (tbody.firstChild) {
                tbody.insertBefore(row, tbody.firstChild);
            } else {
                tbody.appendChild(row);
            }

            // Rimuovi lo sfondo verde dopo qualche secondo

        });
    }
</script>