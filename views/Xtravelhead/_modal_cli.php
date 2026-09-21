
 
<?php
if (!function_exists('formatEuro')) {
    function formatEuro($number, $decimals = 2)
    {
        return number_format((float)$number, $decimals, ',', '.');
    }
}

if (!function_exists('closeTableModal')) {
    function closeTableModal($totals)
    {
        echo '<tr style="font-weight: bold; background-color: #eee;">';
        echo '<td colspan="2">Totali</td>';
        echo '<td>€ ' . formatEuro($totals['imponibile']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['ctax']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['iva']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['totale']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['fee']) . '</td>';
        echo '<td>€ ' . formatEuro($totals['totaleimpfatt']) . '</td>';
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

if (!is_array($roomlist['tappetourcli'])) {
    die('roomlist["tappetourcli"] non è un array!');
}

if (!isset($roomlist['tappetourcli']) || !is_array($roomlist['tappetourcli'])) {
    echo "<div class='alert alert-danger'>Errore: tappetourcli mancante o non valido</div>";
    return;
}

try {
    foreach ($roomlist['tappetourcli'] as $value) {
        // Verifica se il cliente è cambiato
        if ($previousClient !== $value['descli']) {
            // Se non è il primo cliente, chiudi la tabella precedente con i suoi totali
            if ($previousClient !== null) {
                closeTableModal($totals);
            }

            // Inizia una nuova tabella per il nuovo cliente
            echo '<h3>Cliente: ' . htmlspecialchars($value['descli']) . '</h3>
            <table class="table">
            <thead>
             <tr>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Cliente</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">TAPPA</th>    
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Imponibile</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">CityTax</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">IVA </th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">Totale Pagato</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">FEE Agenzia</th>
                <th style="font-size: 16px; background-color: #f1eef6; color: #002c48;">FT da Emettere Imponibile</th>
           </tr>
                </thead>
            <tbody>';

            // IMPORTANTE: Resetta i totali per il nuovo cliente
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

        // Aggiungi i dati alla tabella (solo se i valori non sono vuoti)
        $imponibile = (float)($value['imponibile'] ?? 0);
        $ctax = (float)($value['ctax'] ?? 0);
        $iva = (float)($value['iva'] ?? 0);
        $totale = (float)($value['totale'] ?? 0);
        $fee = (float)($value['fee'] ?? 0);
        $totaleimpfatt = (float)($value['totaleimpfatt'] ?? 0);
        $mprezzo = (float)($value['mprezzo'] ?? 0);
        if (!empty($value['descli'])){
        echo '<tr>';
        echo '<td>' . htmlspecialchars($value['descli']) . '</td>';
        echo '<td>' . htmlspecialchars($value['citta']) . '</td>';
        echo '<td>€ ' . formatEuro($imponibile, 2) . '</td>';
        echo '<td>€ ' . formatEuro($ctax, 2) . '</td>';
        echo '<td>€ ' . formatEuro($iva, 2) . '</td>';
        echo '<td>€ ' . formatEuro($totale, 2) . '</td>';
        echo '<td>€ ' . formatEuro($fee, 2) . '</td>';
        echo '<td>€ ' . formatEuro($totaleimpfatt, 2) . '</td>';
        echo '</tr>';
        }
        // Aggiorna i totali SOLO per il cliente corrente
        $totals['mprezzo'] += $mprezzo;
        $totals['imponibile'] += $imponibile;
        $totals['ctax'] += $ctax;
        $totals['iva'] += $iva;
        $totals['totale'] += $totale;
        $totals['fee'] += $fee;
        $totals['totaleimpfatt'] += $totaleimpfatt;
    }

    // Chiudi l'ultima tabella con i suoi totali
    if ($previousClient !== null) {
      echo '</tbody>';
        closeTableModal($totals);
    }
} catch (\Throwable $e) {
    echo '<div class="alert alert-danger"><strong>Errore nella modale:</strong><br>' .
        htmlspecialchars($e->getMessage()) . '<br><pre>' . 
        htmlspecialchars($e->getTraceAsString()) . '</pre></div>';
}
