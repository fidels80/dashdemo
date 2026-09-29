<?php

use yii\db\Migration;
use yii\db\Query;

class m260929_180000_popola_mg_commessa_e_sottocommessa extends Migration
{
    public function safeUp()
    {
        // 1. Prepara e inserisci le 20 commesse
        $commesse = [];
        for ($i = 1; $i <= 20; $i++) {
            $codice = sprintf('COMM%03d', $i);
            $commesse[] = [
                'codice' => $codice,
                'descrizione' => "Commessa numero {$i} - Tipo{$i}",
                'data_inizio' => null,
                'data_fine' => null,
                'id_anagrafica' => null,
                'attivo' => 1,
                'created_at' => new \yii\db\Expression('GETDATE()'),
            ];
        }

        $this->batchInsert('{{%mg_commessa}}', [
            'codice',
            'descrizione',
            'data_inizio',
            'data_fine',
            'id_anagrafica',
            'attivo',
            'created_at',
        ], $commesse);

        // 2. Recupera gli ID reali appena generati da SQL Server
        $commesseInserite = (new Query())
            ->select(['id', 'codice'])
            ->from('{{%mg_commessa}}')
            ->orderBy(['id' => SORT_DESC])
            ->limit(20)
            ->all();

        // 3. Genera le sottocommesse usando gli ID REALI
        $sottocommesse = [];
        $i = 1;
        foreach ($commesseInserite as $commessa) {
            // Crea 3 sottocommesse per ogni commessa (20 * 3 = 60 totale)
            for ($j = 1; $j <= 3; $j++) {
                $codice_sottocommessa = sprintf('SCOMM_%s_%02d', $commessa['codice'], $j);
                $sottocommesse[] = [
                    'id_commessa' => $commessa['id'], // <--- ID reale dal DB
                    'codice' => $codice_sottocommessa,
                    'descrizione' => "Sottocommessa {$j} per {$commessa['codice']}",
                    'data_inizio' => null,
                    'data_fine' => null,
                    'id_anagrafica' => null,
                    'attivo' => 1,
                    'created_at' => new \yii\db\Expression('GETDATE()'),
                ];
                $i++;
            }
        }

        // 4. Inserisci le sottocommesse
        $this->batchInsert('{{%mg_sottocommessa}}', [
            'id_commessa',
            'codice',
            'descrizione',
            'data_inizio',
            'data_fine',
            'id_anagrafica',
            'attivo',
            'created_at',
        ], $sottocommesse);
    }

    public function safeDown()
    {
        $this->delete('{{%mg_sottocommessa}}');
        $this->delete('{{%mg_commessa}}');
    }
}
