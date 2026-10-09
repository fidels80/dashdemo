<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Inserisce 20 magazzini aggiuntivi oltre al magazzino principale esistente
 * (codici MAG02..MAG21). L'operazione e' idempotente: vengono creati solo i
 * codici non ancora presenti.
 */
class m261009_150000_seed_mg_magazzino extends Migration
{
    private $da = 2;
    private $a = 21;

    public function safeUp()
    {
        if (!$this->checkTableExist('mg_magazzino')) {
            return;
        }

        $rows = [];
        for ($i = $this->da; $i <= $this->a; $i++) {
            $codice = 'MAG' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $descrizione = 'Magazzino ' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $esiste = (new Query())->from('{{%mg_magazzino}}')->where(['codice' => $codice])->exists();
            if (!$esiste) {
                $rows[] = [$codice, $descrizione, 1];
            }
        }

        if (!empty($rows)) {
            $this->batchInsert('{{%mg_magazzino}}', ['codice', 'descrizione', 'attivo'], $rows);
        }
    }

    public function safeDown()
    {
        if (!$this->checkTableExist('mg_magazzino')) {
            return;
        }

        for ($i = $this->da; $i <= $this->a; $i++) {
            $codice = 'MAG' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $this->delete('{{%mg_magazzino}}', ['codice' => $codice]);
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
