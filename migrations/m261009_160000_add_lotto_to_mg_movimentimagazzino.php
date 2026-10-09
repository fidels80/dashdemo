<?php

use yii\db\Migration;

/**
 * Lotto sul movimento di magazzino.
 * - id_lotto: lotto a cui si riferisce il movimento (opzionale).
 *
 * Con la gestione lotti attiva sul tipo documento, le righe con lotti
 * generano un movimento di magazzino per ogni lotto; per le righe senza
 * lotti resta un unico movimento con id_lotto nullo.
 */
class m261009_160000_add_lotto_to_mg_movimentimagazzino extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_movimentimagazzino') || !$this->checkTableExist('mg_lotto')) {
            return;
        }

        if (!$this->checkColumnExist('mg_movimentimagazzino', 'id_lotto')) {
            $this->addColumn('{{%mg_movimentimagazzino}}', 'id_lotto', $this->integer()->null());
        }
        if (!$this->checkIndexExist('mg_movimentimagazzino', 'idx-mg_movimentimagazzino-lotto')) {
            $this->createIndex('idx-mg_movimentimagazzino-lotto', '{{%mg_movimentimagazzino}}', 'id_lotto');
        }

        $fk = 'fk-mg_movimentimagazzino-lotto';
        if ($this->checkForeignKeyExist('mg_movimentimagazzino', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_movimentimagazzino}}');
        }
        $this->addForeignKey($fk, '{{%mg_movimentimagazzino}}', 'id_lotto',
            '{{%mg_lotto}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function safeDown()
    {
        $fk = 'fk-mg_movimentimagazzino-lotto';
        if ($this->checkForeignKeyExist('mg_movimentimagazzino', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_movimentimagazzino}}');
        }
        if ($this->checkColumnExist('mg_movimentimagazzino', 'id_lotto')) {
            $this->dropColumn('{{%mg_movimentimagazzino}}', 'id_lotto');
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
