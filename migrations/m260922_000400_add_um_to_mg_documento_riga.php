<?php

use yii\db\Migration;

/**
 * Aggiunge alle righe documento l'unità di misura scelta e il fattore di
 * conversione memorizzato, usato per il ricalcolo del prezzo.
 */
class m260922_000400_add_um_to_mg_documento_riga extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento_riga', 'id_unita_misura')) {
            $this->addColumn('{{%mg_documento_riga}}', 'id_unita_misura', $this->integer()->null());
            $this->createIndex('idx-mg_documento_riga-um', '{{%mg_documento_riga}}', 'id_unita_misura');
            $this->addForeignKey('fk-mg_documento_riga-um', '{{%mg_documento_riga}}', 'id_unita_misura',
                '{{%mg_unita_misura}}', 'id', 'NO ACTION', 'NO ACTION');
        }
        if (!$this->checkColumnExist('mg_documento_riga', 'um')) {
            $this->addColumn('{{%mg_documento_riga}}', 'um', $this->string(10)->null());
        }
        if (!$this->checkColumnExist('mg_documento_riga', 'fattore')) {
            $this->addColumn('{{%mg_documento_riga}}', 'fattore', $this->decimal(18, 4)->notNull()->defaultValue(1));
        }
    }

    public function safeDown()
    {
        if ($this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-um')) {
            $this->dropForeignKey('fk-mg_documento_riga-um', '{{%mg_documento_riga}}');
        }
        foreach (['id_unita_misura', 'um', 'fattore'] as $col) {
            if ($this->checkColumnExist('mg_documento_riga', $col)) {
                $this->dropColumn('{{%mg_documento_riga}}', $col);
            }
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
