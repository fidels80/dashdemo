<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Command per popolare commesse e sottocommesse.
 */
class PopolaCommesseController extends Controller
{
    /**
     * Popola 20 commesse e 60 sottocommesse.
     * @return int Exit code
     */
    public function actionIndex()
    {
        $db = \Yii::$app->db;

        // Cancella dati esistenti (evita duplicati)
        $db->createCommand("DELETE FROM mg_commessa")->execute();
        $db->createCommand("DELETE FROM mg_sottocommessa")->execute();

        echo "Dati precedenti cancellati\n";

        // Genera 20 commesse
        $commesse = [];
        $tipologia = ['Ristrutturazione', 'Edilizia', 'Manutenzione', 'Nuova Costruzione', 'Interni', 'Esterni', 'Infrastrutture', 'Impianti', 'Arredo', 'Giardini'];
        for ($i = 1; $i <= 20; $i++) {
            $codice = sprintf('COM-%d', $i);
            $descrizione = "Commessa {$codice} - Ristrutturazione";

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

        echo "Inserite 20 commesse\n";

        // Genera 60 sottocommesse distribuite uniformemente tra le 20 commesse
        $sottocommesse = [];
        $sottoPerCommessa = []; // Mappa per garantire almeno una per ogni commessa
        
        // Assegna prima 1-3 sottocommesse a ciascuna commessa (garantisce copertura)
        for ($i = 1; $i <= 60; $i++) {
            $codice = sprintf('SCM%03d', $i);
            $descrizione = "Sottocommessa numero {$i} - Attività specifica";

            // Distribuzione uniforme: round($i / (60/20)) + resto per variare
            $parente_idx = (int) ceil($i * 20 / 60); 
            // Aggiorna la mappa dei conteggi
            if (!isset($sottoPerCommessa[$parente_idx])) {
                $sottoPerCommessa[$parente_idx] = [];
            }
            
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

        echo "Inserite 60 sottocommesse\n";

        // Verifica i risultati
        $countCommesse = (new \yii\db\Query($db))->select(['count(*) as total'])->from('mg_commessa')->scalar();
        $countSottocommesse = (new \yii\db\Query($db))->select(['count(*) as total'])->from('mg_sottocommessa')->scalar();

        echo "\nTotale commesse: {$countCommesse}\n";
        echo "Totale sottocommesse: {$countSottocommesse}\n";

        // Mostra distribuzione per commessa
        $parenti = (new \yii\db\Query($db))
            ->select(['id_commessa', 'COUNT(*) as cnt'])
            ->from('mg_sottocommessa')
            ->groupBy('id_commessa')
            ->orderBy(['cnt' => SORT_DESC])
            ->all();

        echo "\nDistribuzione sottocommesse per commessa:\n";
        foreach ($parenti as $p) {
            echo "  Commessa {$p['id_commessa']}: {$p['cnt']} sottocommesse\n";
        }

        return ExitCode::OK;
    }
}
