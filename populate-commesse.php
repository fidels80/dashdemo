<?php

use yii\db\Query;

// Bootstrap Yii2 - approccio semplice per script standalone
require __DIR__ . '/vendor/autoload.php';
$config = require __DIR__ . '/config/web.php';
$app = new \yii\web\Application($config);
$db = $app->db;

// Inizializza i componenti necessari
\Yii::$app->controllerBehavior = null;

// Genera 20 commesse
$commesse = [];
for ($i = 1; $i <= 20; $i++) {
    $codice = sprintf('COM%03d', $i);
    $descrizione = "Commessa principale numero {$i} - Progetto dimostrativo";
    
    $commesse[] = [
        'codice' => $codice,
        'descrizione' => $descrizione,
        'data_inizio' => null,
        'data_fine' => null,
        'id_anagrafica' => null,
        'attivo' => 1,
    ];
}

// Inserisce le commesse
foreach ($commesse as $commessa) {
    $db->createCommand()->insert('mg_commessa', $commessa)->execute();
}

echo "Inserite {$commesse['codice']} commesse\n";

// Genera 60 sottocommesse (ognuna con un padre tra le 20 commesse)
$sottocommesse = [];
$parenti = range(1, 20); // Ogni parente usato più volte per distribuire le sottocommesse

for ($i = 1; $i <= 60; $i++) {
    $codice = sprintf('SCM%03d', $i);
    $descrizione = "Sottocommessa numero {$i} - Attività specifica";
    
    // Assegna un padre in modo distribuito (ogni commessa avrà circa 3 sottocommesse)
    $parente_idx = ($i - 1) % 20 + 1;
    
    $sottocommesse[] = [
        'id_commessa' => $parente_idx,
        'codice' => $codice,
        'descrizione' => $descrizione,
        'data_inizio' => null,
        'data_fine' => null,
        'id_anagrafica' => null,
        'attivo' => 1,
    ];
}

// Inserisce le sottocommesse
foreach ($sottocommesse as $sottocommessa) {
    $db->createCommand()->insert('mg_sottocommessa', $sottocommessa)->execute();
}

echo "Inserite {$sottocommesse['codice']} sottocommesse\n";

// Verifica i risultati
$countCommesse = (new Query($db))->select(['count(*) as total'])->from('mg_commessa')->scalar();
$countSottocommesse = (new Query($db))->select(['count(*) as total'])->from('mg_sottocommessa')->scalar();

echo "\nTotale commesse: {$countCommesse}\n";
echo "Totale sottocommesse: {$countSottocommesse}\n";

// Mostra distribuzione per commessa
$parenti = (new Query($db))
    ->select(['id_commessa', 'COUNT(*) as cnt'])
    ->from('mg_sottocommessa')
    ->groupBy('id_commessa')
    ->orderBy(['cnt' => SORT_DESC])
    ->all();

echo "\nDistribuzione sottocommesse per commessa:\n";
foreach ($parenti as $p) {
    echo "  Commessa {$p['id_commessa']}: {$p['cnt']} sottocommesse\n";
}
