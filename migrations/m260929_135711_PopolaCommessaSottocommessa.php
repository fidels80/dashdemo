<?php

use yii\db\Migration;

class m260929_135711_PopolaCommessaSottocommessa extends Migration
{
    public function safeUp()
    {
        // Popola mg_commessa con 20 righe
        $this->batchInsert('mg_commessa', ['codice', 'descrizione'], [
            ['C001', 'Commissa Base'],
            ['C002', 'Commissa Avanzata'],
            ['C003', 'Commissa Pro'],
            ['C004', 'Commissa Enterprise'],
            ['C005', 'Commissa Premium'],
            ['C006', 'Commissa Gold'],
            ['C007', 'Commissa Silver'],
            ['C008', 'Commissa Standard'],
            ['C009', 'Commissa Plus'],
            ['C010', 'Commissa Ultimate'],
            ['C011', 'Commissa Expert'],
            ['C012', 'Commissa Basic'],
            ['C013', 'Commissa Simple'],
            ['C014', 'Commissa Advanced'],
            ['C015', 'Commissa Deluxe'],
            ['C016', 'Commissa Executive'],
            ['C017', 'Commissa Supreme'],
            ['C018', 'Commissa Royal'],
            ['C019', 'Commissa Diamond'],
            ['C020', 'Commissa Platinum'],
        ]);

        // Recupera le commesse appena inserite
        $commesse = (new \app\models\MgCommessa())->find()->asArray()->all();

        $sottocommesse = [];
        $totaleCommesse = count($commesse);

        // Assegna esattamente 3 sottocommesse uniche per ogni commessa (3 * 20 = 60)
        foreach ($commesse as $commessa) {
            for ($i = 1; $i <= 3; $i++) {
                $numPaddato = str_pad($i, 2, '0', STR_PAD_LEFT);
                $sottocommesse[] = [
                    $commessa['id'],
                    "SC{$commessa['codice']}-{$numPaddato}", // Produce SC C001-01, SC C001-02, SC C001-03...
                    "Sottocommissa {$numPaddato} - {$commessa['descrizione']}"
                ];
            }
        }

        // Inserimento batch unico per massima efficienza
        $this->batchInsert('mg_sottocommessa', ['id_commessa', 'codice', 'descrizione'], $sottocommesse);
    }

    public function safeDown()
    {
        $this->delete('mg_sottocommessa');
        $this->delete('mg_commessa');
    }
}
