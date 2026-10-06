<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Anagrafica dei magazzini: codice, descrizione e facoltativa
 * associazione a un'anagrafica generica (mg_anagrafica).
 */
class m261006_100000_create_mg_magazzino extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_magazzino')) {
            $this->createTable('{{%mg_magazzino}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(10)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'id_anagrafica' => $this->integer()->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_magazzino-codice', '{{%mg_magazzino}}', 'codice', true);
            $this->createIndex('idx-mg_magazzino-id_anagrafica', '{{%mg_magazzino}}', 'id_anagrafica');
        }

        if ($this->checkTableExist('mg_magazzino')) {
            $count = (new Query())->from('{{%mg_magazzino}}')->count();
            if ((int) $count === 0 && $this->checkTableExist('mg_anagrafica')) {
                $principale = (new Query())->select('id')->from('{{%mg_anagrafica}}')
                    ->orderBy(['id' => SORT_ASC])->limit(1)->scalar();
                if ($principale) {
                    $this->batchInsert('{{%mg_magazzino}}', ['codice', 'descrizione', 'id_anagrafica', 'attivo'], [
                        ['MAG01', 'Magazzino principale', $principale, 1],
                    ]);
                }
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_magazzino')) {
            $this->dropTable('{{%mg_magazzino}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
