<?php
 $previousClient = null;
 $totals = [
     'mprezzo' => 0,
     'imponibile' => 0,
     'ctax' => 0,
     'iva' => 0,
     'totale' => 0,
     'fee' => 0,
     'totaleimpfatt' => 0
 ];

 // Funzione per chiudere la tabella e mostrare i totali

 foreach ($roomlist['tappetourcli'] as $value) {
     if ($previousClient !== $value['descli']) {
         // Se non è il primo cliente, chiudi la tabella precedente
         if ($previousClient !== null) {
             closeTable($totals);
         }

         // Inizia una nuova tabella per il nuovo cliente
         echo '<h3>Elenco Clienti</h3>
         <table class="table table-sm table-hover table-responsive-sm table-fit">
         <thead class="thead-dark">
         <th>Cliente</th>
         <th>TAPPA</th>    
         <th>Imponibile Hotel</th>
         <th>CityTax</th>
         <th>IVA Hotel</th>
         <th>Totale Pagato Hotel</th>
         <th>FEE Agenzia</th>
         <th>FT da Emettere Imponibile</th>
       
     </thead>';

         // Resetta i totali
         $totals = [
             'mprezzo' => 0,
             'imponibile' => 0,
             'ctax' => 0,
             'iva' => 0,
             'totale' => 0,
             'fee' => 0,
             'totaleimpfatt' => 0
         ];

         $previousClient = $value['descli'];
     }

     // Aggiungi i dati alla tabella
     echo '<tr>';
     echo '<td>' . $value['descli'] . '</td>';
     echo '<td>' . $value['citta'] . '</td>';
     echo '<td>€ ' . formatEuro($value['mprezzo'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['imponibile'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['ctax'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['iva'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['totale'], 2) . '</td>';
     echo '<td>€ ' . formatEuro($value['fee'], 2) . '</td>';
    // echo '<td>€ ' . formatEuro($value['totaleimpfatt'], 2) . '</td>';
    // echo '<td>€ ' . formatEuro($value['totaleimpfatt']  + $value['fee'] ) . '</td>';
     echo '</tr>';

     // Aggiorna i totali
     $totals['mprezzo'] += $value['mprezzo'];
     $totals['imponibile'] += $value['imponibile'];
     $totals['ctax'] += $value['ctax'];
     $totals['iva'] += $value['iva'];
     $totals['totale'] += $value['totale'];
     $totals['fee'] += $value['fee'];
     $totals['totaleimpfatt'] += $value['totaleimpfatt'];
 }

 // Chiudi l'ultima tabella
 closeTable($totals);




?>