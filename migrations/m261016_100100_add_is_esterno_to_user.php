<?php

use yii\db\Migration;

/**
 * Marca gli utenti "esterni" (contatti dei clienti) che accedono al portale CRM.
 * Un utente esterno e' collegato a una o piu' anagrafiche tramite
 * mg_crm_utente_anagrafica.
 */
class m261016_100100_add_is_esterno_to_user extends Migration
{
    public function safeUp()
    {
        if (!$this->columnExists('user', 'is_esterno')) {
            $this->addColumn('{{%user}}', 'is_esterno', $this->boolean()->notNull()->defaultValue(0));
        }
    }

    public function safeDown()
    {
        if ($this->columnExists('user', 'is_esterno')) {
            $this->dropColumn('{{%user}}', 'is_esterno');
        }
    }

    private function columnExists($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
