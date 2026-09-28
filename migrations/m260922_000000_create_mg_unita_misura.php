<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tabella delle unità di misura (codice, descrizione), condivisa tra gli articoli.
 * Ogni articolo potrà associare più unità con il relativo fattore di conversione.
 */
class m260922_000000_create_mg_unita_misura extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_unita_misura')) {
            $this->createTable('{{%mg_unita_misura}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(10)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_unita_misura-codice', '{{%mg_unita_misura}}', 'codice', true);
        }

        if ($this->checkTableExist('mg_unita_misura')) {
            $count = (new Query())->from('{{%mg_unita_misura}}')->count();
            if ((int) $count === 0) {
                $this->batchInsert('{{%mg_unita_misura}}', ['codice', 'descrizione', 'attivo'], [
                    ['PZ', 'Pezzo', 1],
                    ['NR', 'Numero', 1],
                    ['CF', 'Confezione', 1],
                    ['CT', 'Cartone', 1],
                    ['PLT', 'Pallet', 1],
                    ['KG', 'Chilogrammo', 1],
                    ['GR', 'Grammo', 1],
                    ['LT', 'Litro', 1],
                    ['MT', 'Metro', 1],
                    ['MQ', 'Metro quadro', 1],
                ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_unita_misura')) {
            $this->dropTable('{{%mg_unita_misura}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
