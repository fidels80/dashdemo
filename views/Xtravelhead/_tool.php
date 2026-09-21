 <?php

    use app\models\XRoomlist;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\bootstrap4\Modal;
    use kartik\select2\Select2;
    use yii\helpers\Url;
    use yii\web\JsExpression;
    use app\models\XVenue;

    $rand = rand();
    $db = Yii::$app->db5;
    $command = $db->createCommand("select id, venue+'-'  +citta 
    from x_venue order by citta asc  ");
    $lcittà = $command->queryAll();
    $lcittà2 = ArrayHelper::map($lcittà, 'ID', 'Desk');

    echo '<br>';

    if (Yii::$app->user->identity->level  <= 70) {
        throw new \yii\web\ForbiddenHttpException('Utente non autorizzato.');
    }

    if (Yii::$app->user->identity->level  >= 80) {
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
            'data-target' => '#xroomlistModal',
        ]);

        echo '    ';

        echo Html::button('Crea Nuova Venue', [
            'class' => 'button-base button-lift',
            'data-toggle' => 'modal',
            'data-target' => '#venueModal',
        ]);
        echo '    ';


        $urlWizard = Url::to(['xtravelrow/wizardtour', 'th_id' => $mth_id]);

        // Definiamo il messaggio di avviso
        $testMessage = "ATTENZIONE: Questa procedura è attualmente in fase di TESTING. Vuoi procedere comunque?";

        echo Html::button('<i class="fas fa-magic"></i> Configura Tour Rapido', [
            'class' => 'button-base button-lift',
            'style' => 'background-color: #28a745 !important; color: white;',
            'onclick' => 'if(confirm("' . $testMessage . '")) { window.location.href = "' . $urlWizard . '"; }'
        ]);

        echo '    '; // Spazio tra i pulsanti


        $url = Url::to(['xtravelhead/wizardrighe', 'th_id' => $mth_id]);

        echo Html::button('<i class="fas fa-edit"></i>Modifica Dati', [
            'class' => 'button-base-support button-lift',
            'onclick' => 'window.location.href = "' . $url . '"'
        ]);

        echo '    ';

        $urlUpdatePrenotazione = Url::to(['xtravelhead/modpre', 'id' => $mth_id]);

        // 2. Aggiungiamo il pulsante Modifica Prenotazione
        echo Html::button('<i class="fas fa-edit"></i> Modifica Testata[TEST]', [
            'class' => 'button-base-support button-lift',
            //  'style' => 'background-color: #ffc107 !important; color: #212529;', // Giallo "warning" per distinguersi
            'onclick' => 'window.location.href = "' . $urlUpdatePrenotazione . '"'
        ]);

        echo '    '; // Spazio tra i pulsanti


        $url = Url::to(['xtravelhead/masterhotel', 'id' => $mth_id]);
        echo Html::button('<i class="fa fa-arrow-left"></i> Torna indietro', [
            'class' => 'button-base-support button-lift',
            'style' => 'background-color: #6c757d !important;',
            'onclick' => 'window.location.href = "' . $url . '"'
        ]);
    }

    Modal::begin([
        'id' => 'tappeModal',
        'title' => '<h4>Scegli le Tappe del Tour</h4>', // Imposta il titolo della modale qui
        'size' => Modal::SIZE_LARGE, // opzioni: SIZE_SMALL, SIZE_LARGE, SIZE_DEFAULT
        'options' => [
            'class' => 'custom-modal', // aggiungiamo classe custom
        ]
    ]);

    // echo '<div id="modalContent">';
    // echo '<div class="modal-body">';
    echo "<div class=\"scroll-container\" style=\"min-height: 400px;\">";
    echo Html::beginForm(['xtravelhead/savetappe'], 'post', ['id' => 'tappe-form']);

    echo '<div class="form-group">';
    echo Html::label('Numero di Tappe', 'num-tappe');
    echo Html::input('number', 'num-tappe', null, ['class' => 'form-control', 'id' => 'num-tappe']);
    echo '</div>';

    echo '<div id="tappe-fields">';
    // I campi per le tappe verranno aggiunti qui via JavaScript
    echo '</div>';
    //                    echo '</div>';
    //echo '</div>';

    echo Html::submitButton('Salva', ['class' => 'button-base button-lift']);
    echo Html::endForm();


    echo '<br>';



    echo '</div>';
    Modal::end();


    Modal::begin([
        'id' => 'venueModal',
        'title' => '<h4>Crea Nuova Venue</h4>',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<div id="modalContent2" class="scrollable-table-container">';
    $modelVenue = new XVenue();
    // Qui carichi direttamente il contenuto del form
    echo $this->render('/xvenue/createaj', [
        'model' => $modelVenue,   // passi il modello di create
    ]);
    echo '</div>';
    Modal::end();


    ?>



 <br>Dettaglio tappe<br>
 <div class="row mb-3">
     <div class="col-md-4">
         <div class="input-group">
             <div class="input-group-prepend">
                 <span class="input-group-text"><i class="fas fa-search"></i></span>
             </div>
             <input type="text" id="search-tappe" class="form-control" placeholder="Cerca città o data...">
         </div>
     </div>
 </div>
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
                    echo $value['evaso'] == 1 ? '<i class="fas fa-check"></i>'
                        : '<i class="fas fa-stop"></i>';

                    echo '</td>';
                    echo '<td>';
                    // Pulsante elimina solo se non è evaso
                    if (Yii::$app->user->identity->level  >= 80) {
                        $isEvaso = (isset($value['evaso']) && $value['evaso'] == 1);


                        //echo '<span class="text-muted">Non eliminabile</span>';
                        $url = Url::to(['xtappe/update', 'id' => $value['id_tappa']]);

                        echo Html::button('<i class="fas fa-pen"></i>', [
                            'title' => 'Modifica nominativo',
                            'class' => 'btn btn-warning btn-sm',
                            'onclick' => 'window.location.href = "' . $url . '"'
                        ]);
                        echo ' ';
                        if ($isEvaso) {
                            echo '<span class="badge badge-secondary">Evaso - Non Cancellabile</span>';
                        } else {
                            echo Html::button('<i class="fas fa-trash"></i>', [
                                'class' => 'btn btn-danger btn-sm elimina-tappa',
                                'data-id' => $value['id_tappa'],
                                'title' => 'Elimina tappa'
                            ]);
                        }
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
 </div>
 <script>
     // Funzione di ricerca in tempo reale sulla tabella Tappe
     $('#search-tappe').on('keyup', function() {
         var value = $(this).val().toLowerCase();
         $('#tabella-tappe tbody tr').filter(function() {
             // Toggle mostra la riga se il testo è trovato, altrimenti la nasconde
             $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
         });
     });
 </script>
 <?php
    // Pulsante per aprire la modale per l'inserimento dei nominativi


    Modal::begin([
        'id' => 'xroomlistModal',
        'title' => '<h4>Aggiungi Nominativo</h4>',
        'size' => Modal::SIZE_LARGE,
    ]);
    echo '<div id="modalContent2a" class="scrollable-table-container">';
    $modelxroomlist = new XRoomlist();
    $modelxroomlist->th_id = $mth_id;
    // Qui carichi direttamente il contenuto del form
    echo $this->render('/xroomlist/createaj', [
        'model' => $modelxroomlist,
        'modalId' => '#xroomlistModal',
        'th_id' => $mth_id,
        // passi il modello di create
    ]);
    echo '</div>';
    Modal::end();


    ?>

 <?php

    $this->registerJs("
// Reset campi quando si apre la modale
$('#xroomlistModal').on('show.bs.modal', function() {
    // Reset del form
    $('#xroomlist-form')[0].reset();
    
    // Reset delle Select2
    $('#cd_ar-select').val(null).trigger('change');
    $('#party-select').val(null).trigger('change');
    $('#ruolo-select').val(null).trigger('change');
    // Non resettiamo commessa-select come richiesto
    
    // Reset campi testo
    $('#xroomlist-nominativo').val('');
    $('#xroomlist-note').val('');
});

// Fix Select2 nelle modali (quando la modale è completamente visibile)
$('#xroomlistModal').on('shown.bs.modal', function() {
    $('.select2-container').remove();
    
    $('#cd_ar-select, #ruolo-select, #party-select, #commessa-select').each(function() {
        if ($(this).data('select2')) {
            $(this).select2('destroy');
        }
        $(this).select2({
            dropdownParent: $('#xroomlistModal'),
            allowClear: true,
            width: '100%'
        });
    });
});

// Submit AJAX (usa .off().on() per evitare duplicazione)
$('#xroomlist-form').off('submit').on('submit', function(e) {
    e.preventDefault();
    
    // Disabilita il pulsante submit per evitare doppi click
    var submitBtn = $(this).find('button[type=\"submit\"]');
    submitBtn.prop('disabled', true);
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response);
            
            if (response.success) {
                $('#xroomlistModal').modal('hide');
                submitBtn.prop('disabled', false);
if (response.data) {
    const data = response.data;
    
    // Prepariamo i pulsanti con i dati necessari per la duplicazione
    const nuovaRiga = `
        <tr class=\"nuova-tappa\">
            <td style=\"text-transform: uppercase;\">\${data.nominativo}</td>
            <td>\${data.cd_ar}</td>
            <td>\${data.ruolo}</td>
            <td>\${data.party}</td>
            <td>\${data.commessa}</td>
            <td>\${data.note}</td>
            <td><i class=\"fas fa-stop\"></i></td>
            <td>
               

                <button type=\"button\" class=\"btn btn-info btn-sm prepara-duplica\" 
                        data-id=\"\${data.id_guest}\" 
                        data-guest=\"\${data.nominativo}\" 
                        data-cd_ar=\"\${data.cd_ar}\" 
                        data-ruolo=\"\${data.ruolo}\" 
                        data-party=\"\${data.party}\" 
                        data-commessa=\"\${data.commessa}\" 
                        data-note=\"\${data.note}\" 
                        title=\"Duplica nominativo\">
                    <i class=\"fas fa-copy\"></i>
                </button>

                <button type=\"button\" class=\"btn btn-warning btn-sm\"
                        title=\"Modifica nominativo\" 
                        onclick=\"window.location.href='/index.php?r=xroomlist%2Fupdate&id=\${data.id_guest}'\">
                    <i class=\"fas fa-pen\"></i>
                </button>
                 <button type=\"button\" class=\"btn btn-danger btn-sm elimina-nominativo\" 
                        data-id=\"\${data.id_guest}\" title=\"Elimina nominativo\">
                    <i class=\"fas fa-trash\"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('table[name=\"roomlist\"] tbody').prepend(nuovaRiga);
    alert('Salvataggio effettuato');
} else {
                    alert('Ospite inserito con successo');
                    location.reload();
                }
            } else {
                // Gestione errori
                let errorMsg = 'Errore durante il salvataggio del nominativo.';
                
                console.log('Errori ricevuti:', response.errors);
                
                if (response.errors) {
                    errorMsg = Object.entries(response.errors)
                        .map(([field, errors]) => `\${field}: \${Array.isArray(errors) ? errors.join(', ') : errors}`)
                        .join('\\n');
                }
                
                if (response.html) {
                    $('#modalContent2a').html(response.html);
                    // Reinizializza le select2 dopo aver ricaricato l'HTML
                    setTimeout(function() {
                        $('#xroomlistModal').trigger('shown.bs.modal');
                    }, 100);
                }
                
                alert('ERRORE:\\n' + errorMsg);
                
                // Riabilita il pulsante
                submitBtn.prop('disabled', false);
            }
        },
        error: function(xhr, status, error) {
            console.log('Errore AJAX:', error);
            console.log('Response:', xhr.responseText);
            alert('Si è verificato un errore nella richiesta.');
            
            // Riabilita il pulsante
            submitBtn.prop('disabled', false);
        }
    });
    
    return false;
});
", \yii\web\View::POS_READY);

    ?>




 <br>Dettaglio RoomList<br>
 <div class="row mb-3">
     <div class="col-md-4">
         <div class="input-group">
             <div class="input-group-prepend">
                 <span class="input-group-text"><i class="fas fa-search"></i></span>
             </div>
             <input type="text" id="search-roomlist" class="form-control" placeholder="Cerca ospite, prodotto o commessa...">
         </div>
     </div>
 </div>


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

                    // Verifichiamo se è evaso (restituisce true se è 1, false altrimenti)
                    $isEvasoRoom = (isset($value['evaso']) && $value['evaso'] == 1);

                    //yii::error("Stato evaso: " . ($isEvasoRoom ? 'SI' : 'NO'));

                    echo '<td>';
                    //echo Html::button('<i class="fas fa-copy"></i>', [
                    //    'class' => 'btn btn-info btn-sm duplica-nominativo',
                    //    'data-id' => $value['id_guest'] ?? '',
                    //    'title' => 'Duplica nominativo'
                    // ]);
                    // All'interno del foreach della RoomList
                    if (Yii::$app->user->identity->level  >= 80) {
                        echo Html::button('<i class="fas fa-copy"></i>', [
                            'class' => 'btn btn-info btn-sm prepara-duplica', // Cambiata classe
                            'data-id' => $value['id_guest'] ?? '',
                            'data-guest' => $value['guest'] ?? '',
                            'data-cd_ar' => $value['cd_Ar'] ?? '',
                            'data-ruolo' => $value['ruolo'] ?? '',
                            'data-party' => $value['party'] ?? '',
                            'data-commessa' => $value['commessa'] ?? '',
                            'data-note' => $value['note'] ?? '',
                            'title' => 'Duplica e modifica'
                        ]);


                        // Pulsante elimina solo se non è evaso

                        echo ' '; // Un po' di spazio tra i bottoni 
                        //echo '<span class="text-muted">Non eliminabile</span>';
                        $url = Url::to(['xroomlist/update', 'id' => $value['id_guest']]);

                        echo Html::button('<i class="fas fa-pen"></i>', [
                            'title' => 'Modifica nominativo',
                            'class' => 'btn btn-warning btn-sm',
                            'onclick' => 'window.location.href = "' . $url . '"'
                        ]);


                        echo ' ';

                        if ($isEvasoRoom) {
                            echo '<span class="badge badge-secondary">Evaso - Non Cancellabile</span>';
                        } else {
                            echo Html::button('<i class="fas fa-trash"></i>', [
                                'class' => 'btn btn-danger btn-sm elimina-nominativo',
                                'data-id' => isset($value['id_guest']) ? $value['id_guest'] : '',
                                'title' => 'Elimina nominativo'
                            ]);
                        }
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
                 dropdownParent: $('#tappeModal'), // AGGIUNGI QUESTO
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
         setTimeout(function() {
             $('.scroll-container-tappe').trigger('scroll');
         }, 300);
     });

     $(document).ready(function() {
         $.fn.modal.Constructor.prototype.enforceFocus = $.noop;
         $('#xroomlistModal').removeAttr('tabindex');
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




         /*  $('#xroomlist-form').on('show.bs.modal', function() {
             // Reset tutti i campi tranne commessa
             $('#nominativo').val('');
             $('#cd_Ar').val(null).trigger('change');
             $('#party').val(null).trigger('change');
             $('#ruolo').val(null).trigger('change');
             $('#nota').val('');
             // Non resettiamo la commessa come richiesto
         });
*/

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
         /*    $('#xroomlist-form').on('submit', function(event) {
             event.preventDefault(); // Previene il normale submit del form

             $.ajax({
                 url: $(this).attr('action'),
                 type: 'post',
                 data: $(this).serialize(),
                 success: function(response) {
                     console.log(response);

                     if (response.success) {
                         $('#xroomlistModal').modal('hide');

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
*/
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
     $(document).on('click', '.duplica-nominativo', function() {
         var idNominativo = $(this).data('id');
         var btn = $(this);
         var $rowOriginale = btn.closest('tr');

         if (confirm('Vuoi duplicare questo nominativo?')) {
             $.ajax({
                 url: '<?php echo Url::to(['xtravelhead/duplicanominativo']); ?>',
                 type: 'POST',
                 data: {
                     id: idNominativo
                 },
                 beforeSend: function() {
                     btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                 },
                 success: function(response) {
                     if (response.success) {
                         const d = response.data;

                         // Costruiamo gli URL dinamicamente
                         const urlUpdate = '<?php echo Url::to(['xroomlist/update', 'id' => 'REPLACE_ID']); ?>'.replace('REPLACE_ID', d.id_guest);

                         // Creiamo l'HTML della nuova riga
                         const nuovaRiga = `
                        <tr class="table-info nuova-riga-animata">
                            <td style="text-transform: uppercase;">${d.nominativo || ''}</td>
                            <td>${d.cd_ar || ''}</td>
                            <td>${d.ruolo || ''}</td>
                            <td>${d.party || ''}</td>
                            <td>${d.commessa || ''}</td>
                            <td>${d.note || ''}</td>
                            <td><i class="fas fa-stop"></i></td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm duplica-nominativo" 
                                        data-id="${d.id_guest}" title="Duplica">
                                    <i class="fas fa-copy"></i>
                                </button>
                            <button type="button" class="btn btn-danger btn-sm elimina-nominativo" 
                                        data-id="${d.id_guest}" title="Elimina">
                                    <i class="fas fa-trash"></i>
                                </button>

                                <button type="button" class="btn btn-warning btn-sm" 
                                        onclick="window.location.href = '${urlUpdate}'" title="Modifica">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                         // Aggiunge la riga subito sotto quella duplicata
                         $rowOriginale.after(nuovaRiga);

                         // Ripristina il bottone originale
                         btn.prop('disabled', false).html('<i class="fas fa-copy"></i>');

                         alert('Nominativo duplicato!');
                     } else {
                         alert('Errore: ' + response.error);
                         btn.prop('disabled', false).html('<i class="fas fa-copy"></i>');
                     }
                 },
                 error: function() {
                     alert('Errore di comunicazione');
                     btn.prop('disabled', false).html('<i class="fas fa-copy"></i>');
                 }
             });
         }
     });

     $(document).on('click', '.prepara-duplica', function() {
         var btn = $(this);

         // 1. Apri la modale esistente
         $('#xroomlistModal').modal('show');

         // 2. Popola i campi (usa gli ID che hai nel tuo form xroomlist-form)
         // Nota: usiamo un piccolo timeout per essere sicuri che la modale sia pronta
         setTimeout(function() {
             $('#xroomlist-nominativo').val(btn.data('guest'));
             $('#xroomlist-note').val(btn.data('note'));

             // Per le Select2 bisogna impostare il valore e triggerare il 'change'
             $('#cd_ar-select').val(btn.data('cd_ar')).trigger('change');
             $('#ruolo-select').val(btn.data('ruolo')).trigger('change');
             $('#party-select').val(btn.data('party')).trigger('change');
             $('#commessa-select').val(btn.data('commessa')).trigger('change');

             // IMPORTANTE: Assicurati che l'ID del record sia vuoto 
             // così il controller capirà che è un NUOVO inserimento
             $('#xroomlist-id_guest').val('');
         }, 200);
     });


     // Funzione di ricerca in tempo reale sulla tabella RoomList
     $('#search-roomlist').on('keyup', function() {
         var value = $(this).val().toLowerCase();

         // Selezioniamo tutte le righe del corpo della tabella 'roomlist'
         $('table[name="roomlist"] tbody tr').filter(function() {
             // Mostra/nasconde la riga se il testo cercato è presente in una qualsiasi cella
             $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
         });
     });

     // Funzione di ricerca per la tabella riepilogo costi
 </script>

 <?php if ((Yii::$app->user->identity->level ?? 0) >= 80): ?>
     <hr>
     <br>
     <h4>Riepilogo Costi per Struttura[TEST]</h4><br>

     <div class="row mb-3">
         <div class="col-md-4">
             <div class="input-group">
                 <div class="input-group-prepend">
                     <span class="input-group-text"><i class="fas fa-search"></i></span>
                 </div>
                 <input type="text" id="search-xtravel" class="form-control" placeholder="Cerca struttura o città...">
             </div>
         </div>
     </div>
     <style>
         .scrollable-table-container {
             width: 100%;
             overflow-x: auto;
             display: block;

         }

         #tabella-riepilogo-costi {
             /* Rimuoviamo table-layout: fixed così le colonne si adattano al contenuto */
             table-layout: auto !important;
             width: 100%;
             border-collapse: collapse;
         }

         /* Gestione larghezza colonne */
         .col-struttura {
             min-width: 150px;
             /* Garantisce uno spazio minimo */
             max-width: 200px;
             white-space: normal;
             /* Permette il ritorno a capo se necessario, evitando sovrapposizioni */
             word-wrap: break-word;
         }

         .col-citta {
             min-width: 130px;
             white-space: normal;
         }

         .col-inout {
             min-width: 110px;
             white-space: nowrap;
             /* Mantiene le date su una riga sola */
             text-align: center;
         }

         /* Pulizia font e celle */
         #tabella-riepilogo-costi td,
         #tabella-riepilogo-costi th {
             font-size: 12px;
             /* Leggermente più piccolo per aiutare lo spazio */
             vertical-align: middle;
             padding: 8px 4px !important;
             /* Riduce il padding laterale per guadagnare spazio */
             line-height: 1.2;
         }

         /* Stile per i piccoli badge di data */
         .date-badge {
             display: block;
             font-weight: 600;
             color: #444;
         }
     </style>


     <div class="scrollable-table-container">
         <table class="table table-striped table-bordered" id="tabella-riepilogo-costi" width>
             <thead>
                 <tr>
                     <th style="background-color: #e3f2fd; color: #002c48;">Struttura</th>
                     <th style="background-color: #e3f2fd; color: #002c48;">Città</th>
                     <th style="background-color: #e3f2fd; color: #002c48; ">In / Out</th>
                     <th style="background-color: #f1eef6; color: #002c48;">Qta</th>
                     <th style="background-color: #f1eef6; color: #002c48;">Prezzo</th>
                     <th style="background-color: #f1eef6; color: #002c48;">Tax</th>
                     <th style="background-color: #f1eef6; color: #002c48;">Imponibile</th>
                     <th style="background-color: #f1eef6; color: #002c48;">IVA</th>
                     <th style="background-color: #d1e7dd; color: #002c48;">Totale Gen.</th>
                     <th style="background-color: #e3f2fd; color: #002c48;">Stato</th>
                     <th style="background-color: #d1e7dd; color: #002c48;">Pagato </th>
                     <th style="background-color: #e3f2fd; color: #002c48;">Residuo</th>
                     <th style="background-color: #e3f2fd; color: #002c48;">Paga</th>

                 </tr>
             </thead>
             <tbody>

                 <?php

                    // Con print_r (restituisce una stringa dell'array)
                    //              Yii::warning(print_r($test1, true), 'debug_xtravel');

                    // Oppure in formato JSON (molto pulito se i dati sono tanti)
                    // Yii::warning(json_encode($righeXtravel), 'debug_xtravel');




                    //Yii::warning($tabgrphot, 'debug_riga');

                    $granTotale = 0;

                    if (!empty($tabgrphot)):
                        foreach ($tabgrphot as $riga):
                            $granTotale += $riga['Totalegenerale'];
                    ?>
                         <?php if ($riga['qta'] <> 0 and $riga['struttura'] <> '0'): ?>
                             <tr>
                                 <td><strong><?= Html::encode($riga['struttura']) ?></strong></td>
                                 <td><?= Html::encode($riga['citta']) ?></td>
                                 <td class="col-inout">
                                     <span class="date-badge">
                                         <?= $riga['checkin'] ?> <i class="fas fa-long-arrow-alt-right" style="font-size: 10px; color: #999;"></i> <?= $riga['checkout'] ?>
                                     </span>
                                 </td>
                                 <td><?= number_format($riga['qta'], 2, ',', '.') ?></td>
                                 <td><?= number_format($riga['prezzo'], 2, ',', '.') ?> €</td>
                                 <td><?= number_format($riga['tax_unit'], 2, ',', '.') ?> €</td>
                                 <td><?= number_format($riga['imponibile'], 2, ',', '.') ?> €</td>
                                 <td><?= number_format($riga['iva'], 2, ',', '.') ?> €</td>
                                 <td class="table-success"><strong><?= number_format($riga['Totalegenerale'], 2, ',', '.') ?> €</strong></td>
                                 <td class="text-center">
                                     <?= $riga['saldato']
                                            ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> SALDATO</span>'
                                            : '<span class="badge badge-warning"><i class="fas fa-clock"></i> DA SALDARE</span>'
                                        ?>
                                 </td>
                                 <td class="text-info">
                                     <?= number_format($riga['all_pagato'], 2, ',', '.') ?> €
                                 </td>
                                 <td class="<?= $riga['Residuo'] > 0.2 ? 'text-danger' : 'text-success' ?>">
                                     <strong><?= number_format($riga['Residuo'], 2, ',', '.') ?> €</strong>
                                 </td>
                                 <td>
                                     <?php

                                        echo Html::a(
                                            '<i class="fa fa-piggy-bank"></i>Pagamenti',
                                            [
                                                'xtravelrow/paga',
                                                'th_id' => $mth_id,
                                                'id_struttura' => $riga['idStruttura'],
                                                'id_tappa' => $riga['id_tappa'],
                                                'prezzo' => $riga['Residuo'],
                                                'check_in' => $riga['checkin'],
                                                'check_out' => $riga['checkout']
                                            ],
                                            ['class' => 'btn btn-success btn-sm']
                                        );


                                        ?>
                                 </td>
                             </tr>
                         <?php endif; ?>
                     <?php endforeach;
                    else: ?>
                     <tr>
                         <td colspan="11" class="text-center">Nessun dato trovato per questo th_id</td>
                     </tr>
                 <?php endif; ?>
             </tbody>
             <tfoot>
                 <tr style="background-color: #f8f9fa; font-weight: bold;">
                     <td colspan="3" class="text-right">TOTALI RICALCOLATI:</td>
                     <td id="tot-qta">0</td>
                     <td></td>
                     <td id="avg-tax">0 €</td>
                     <td id="tot-imponibile">0 €</td>
                     <td id="tot-iva">0 €</td>
                     <td id="tot-generale" class="table-success">0 €</td>
                     <td></td>
                     <td></td>
                     <td id="tot-residuo" class="text-danger">0 €</td>
                     <td></td>
                 </tr>
             </tfoot>
         </table>
     </div>

     <script>
         $('#search-xtravel').on('keyup', function() {
             var value = $(this).val().toLowerCase();
             $('#tabella-riepilogo-costi tbody tr').filter(function() {
                 $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
             });
         });
     </script>

     <?php
        $this->registerJs("
function ricalcolaTotali() {
    let qta = 0, prezzo = 0, tax = 0, imp = 0, iva = 0, totale = 0, residuo = 0; // Aggiunto residuo
    let count = 0;

    $('#tabella-riepilogo-costi tbody tr:visible').each(function() {
        let row = $(this);
        // Funzione per pulire il testo e convertirlo in numero
        let parseNum = (text) => {
            if (!text) return 0;
            // Rimuove il simbolo €, gli spazi e converte la virgola decimale in punto
            let clean = text.replace('€', '').replace(/\./g, '').replace(',', '.').trim();
            return parseFloat(clean) || 0;
        };

        // Indici corretti basati sulla struttura <thead>/<tbody>
        qta     += parseNum(row.find('td:eq(3)').text()); // Colonna Qta
        prezzo  += parseNum(row.find('td:eq(4)').text()); // Colonna Prezzo
        tax     += parseNum(row.find('td:eq(5)').text()); // Colonna Tax
        imp     += parseNum(row.find('td:eq(6)').text()); // Colonna Imponibile
        iva     += parseNum(row.find('td:eq(7)').text()); // Colonna IVA
        totale  += parseNum(row.find('td:eq(8)').text()); // Colonna Totale Gen.
        residuo += parseNum(row.find('td:eq(11)').text()); // Colonna Residuo
        
        count++;
    });

    let avgP = count > 0 ? (prezzo / count) : 0;
    let avgT = count > 0 ? (tax / count) : 0;

    // Formattatore per valuta italiana
    let fmt = (num) => new Intl.NumberFormat('it-IT', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    }).format(num);

    // Aggiornamento dei campi nel tfoot
    $('#tot-qta').text(fmt(qta));
    $('#avg-prezzo').text(fmt(avgP) + ' €');
    $('#avg-tax').text(fmt(tax) + ' €');
    $('#tot-imponibile').text(fmt(imp) + ' €');
    $('#tot-iva').text(fmt(iva) + ' €');
    $('#tot-generale').text(fmt(totale) + ' €');
    $('#tot-residuo').text(fmt(residuo) + ' €'); // Aggiorna il Residuo
}

// Assicurati che il trigger di ricerca rimanga attivo
$(document).on('keyup', '#search-xtravel', function() {
    var value = $(this).val().toLowerCase();
    $('#tabella-riepilogo-costi tbody tr').filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
    ricalcolaTotali();
});

// Esegui al caricamento
ricalcolaTotali();
");
        ?>

 <?php endif; ?>