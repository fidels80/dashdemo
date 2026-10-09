<?php

use yii\db\Migration;

/**
 * Collega le righe documento alle aliquote IVA tramite id_aliquota_iva:
 * la percentuale (mg_documento_riga.iva) resta denormalizzata per stampa e
 * calcoli, mentre la natura SDI e gli altri dati arrivano dall'aliquota.
 * Le righe esistenti vengono agganciate per corrispondenza di percentuale.
 */
class m261009_200700_add_aliquota_iva_to_mg_documento_riga extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento_riga', 'id_aliquota_iva')) {
            $this->addColumn('{{%mg_documento_riga}}', 'id_aliquota_iva', $this->integer()->null());
        }

        // Aggancia le righe esistenti all'aliquota con la stessa percentuale
        $this->execute("UPDATE d SET d.id_aliquota_iva = (
                SELECT TOP 1 a.id FROM {{%mg_aliquota_iva}} a
                WHERE a.percentuale = d.iva
                ORDER BY a.attivo DESC, a.id ASC)
            FROM {{%mg_documento_riga}} d");

        if (!$this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-iva')) {
            $this->addForeignKey('fk-mg_documento_riga-iva', '{{%mg_documento_riga}}', 'id_aliquota_iva',
                '{{%mg_aliquota_iva}}', 'id', 'SET NULL', 'SET NULL');
        }
    }

    public function safeDown()
    {
        if ($this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-iva')) {
            $this->dropForeignKey('fk-mg_documento_riga-iva', '{{%mg_documento_riga}}');
        }
        if ($this->checkColumnExist('mg_documento_riga', 'id_aliquota_iva')) {
            $this->dropColumn('{{%mg_documento_riga}}', 'id_aliquota_iva');
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
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
             WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n AND CONSTRAINT_TYPE = 'FOREIGN KEY'"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
