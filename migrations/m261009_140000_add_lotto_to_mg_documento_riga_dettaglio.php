<?php

use yii\db\Migration;

/**
 * Lotto collegato alla riga di dettaglio documento.
 * - id_lotto: lotto selezionato o creato per il dettaglio (opzionale).
 *
 * La foreign key e' NO ACTION per non creare percorsi di propagazione
 * multipli con la riga documento.
 */
class m261009_140000_add_lotto_to_mg_documento_riga_dettaglio extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_documento_riga_dettaglio') || !$this->checkTableExist('mg_lotto')) {
            return;
        }

        if (!$this->checkColumnExist('mg_documento_riga_dettaglio', 'id_lotto')) {
            $this->addColumn('{{%mg_documento_riga_dettaglio}}', 'id_lotto', $this->integer()->null());
        }
        if (!$this->checkIndexExist('mg_documento_riga_dettaglio', 'idx-mg_documento_riga_dettaglio-lotto')) {
            $this->createIndex('idx-mg_documento_riga_dettaglio-lotto', '{{%mg_documento_riga_dettaglio}}', 'id_lotto');
        }

        $fk = 'fk-mg_documento_riga_dettaglio-lotto';
        if ($this->checkForeignKeyExist('mg_documento_riga_dettaglio', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_documento_riga_dettaglio}}');
        }
        $this->addForeignKey($fk, '{{%mg_documento_riga_dettaglio}}', 'id_lotto',
            '{{%mg_lotto}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function safeDown()
    {
        $fk = 'fk-mg_documento_riga_dettaglio-lotto';
        if ($this->checkForeignKeyExist('mg_documento_riga_dettaglio', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_documento_riga_dettaglio}}');
        }
        if ($this->checkColumnExist('mg_documento_riga_dettaglio', 'id_lotto')) {
            $this->dropColumn('{{%mg_documento_riga_dettaglio}}', 'id_lotto');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkIndexExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM sys.indexes WHERE object_id = OBJECT_ID(:t) AND name = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
