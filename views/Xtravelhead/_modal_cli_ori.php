<?php
if (!function_exists('formatEuro')) {
    function formatEuro($number, $decimals = 2)
    {
        return number_format((float)$number, $decimals, ',', '.');
    }
}
 
if (!function_exists('closeTable')) {
    function closeTable($totals)
    {
        echo '<tr style="font-weight: bold; background-color: #eee;">';
        echo '<td colspan="2">Totali</td>';
        echo '<td>€ ' . formatEuro($totals['mprezzo']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['imponibile']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['ctax']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['iva']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['totale']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['fee']) . '</td>';
        echo '</tr>';
        echo '</table>';
    }
}
 
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
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (!is_array($roomlist['tappetourcli'])) {
    die('roomlist["tappetourcli"] non è un array!');
}

if (!isset($roomlist['tappetourcli']) || !is_array($roomlist['tappetourcli'])) {
    echo "<div class='alert alert-danger'>Errore: tappetourcli mancante o non valido</div>";
    return;
}
Yii::warning('Contenuto tappetourcli: ' . print_r($roomlist['tappetourcli'], true));

// Funzione per chiudere la tabella e mostrare i totali
try {
    foreach ($roomlist['tappetourcli'] as $value) {
        yii::warning($roomlist['tappetourcli']);
        if ($previousClient !== $value['descli']) {
            // Se non è il primo cliente, chiudi la tabella precedente
            if ($previousClient !== null) {
                closeTable($totals);
            }

            // Inizia una nuova tabella per il nuovo cliente
            echo '<h3>Elenco Clienti</h3>
         <table class="table">
         <thead >
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Cliente</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">TAPPA</th>    
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Imponibile Hotel</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">CityTax</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">IVA Hotel</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Totale Pagato Hotel</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">FEE Agenzia</th>
         <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">FT da Emettere Imponibile</th>
       
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
} catch (\Throwable $e) {
    echo '<div class="alert alert-danger"><strong>Errore nella modale:</strong><br>' .
        $e->getMessage() . '<br><pre>' . $e->getTraceAsString() . '</pre></div>';
}
?>
 
<hr><strong>Fine contenuto modale</strong>';