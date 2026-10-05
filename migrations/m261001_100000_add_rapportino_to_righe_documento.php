<?php

use yii\db\Migration;

/**
 * Collega una riga documento al rapportino da cui è stata prelevata.
 * rapportini.id è un UNIQUEIDENTIFIER, quindi la colonna usa lo stesso tipo.
 */
class m261001_100000_add_rapportino_to_righe_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento_riga', 'id_rapportino')) {
            $this->addColumn('{{%mg_documento_riga}}', 'id_rapportino', 'uniqueidentifier NULL');
            $this->createIndex('idx-mg_documento_riga-rapportino', '{{%mg_documento_riga}}', 'id_rapportino');
            $this->addForeignKey('fk-mg_documento_riga-rapportino', '{{%mg_documento_riga}}', 'id_rapportino',
                '{{%rapportini}}', 'id', 'NO ACTION', 'NO ACTION');
        }
    }

    public function safeDown()
    {
        if ($this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-rapportino')) {
            $this->dropForeignKey('fk-mg_documento_riga-rapportino', '{{%mg_documento_riga}}');
        }
        if ($this->checkColumnExist('mg_documento_riga', 'id_rapportino')) {
            $this->dropColumn('{{%mg_documento_riga}}', 'id_rapportino');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
