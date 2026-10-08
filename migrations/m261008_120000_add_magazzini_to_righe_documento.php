<?php

use yii\db\Migration;

/**
 * Magazzini sulle righe documento:
 * - id_magazzino_partenza / id_magazzino_arrivo: magazzino da cui parte e
 *   a cui arriva la merce della singola riga. Proposti dal tipo documento,
 *   modificabili per riga.
 *
 * Le foreign key sono NO ACTION per evitare percorsi di propagazione
 * multipli: l'integrita' e' garantita dai controlli applicativi.
 */
class m261008_120000_add_magazzini_to_righe_documento extends Migration
{
    private $colonne = ['id_magazzino_partenza', 'id_magazzino_arrivo'];

    public function safeUp()
    {
        if (!$this->checkTableExist('mg_magazzino')) {
            return;
        }

        foreach ($this->colonne as $c) {
            if (!$this->checkColumnExist('mg_documento_riga', $c)) {
                $this->addColumn('{{%mg_documento_riga}}', $c, $this->integer()->null());
            }
            $indice = 'idx-mg_documento_riga-' . str_replace('id_magazzino_', '', $c);
            if (!$this->checkIndexExist('mg_documento_riga', $indice)) {
                $this->createIndex($indice, '{{%mg_documento_riga}}', $c);
            }
            $fk = 'fk-mg_documento_riga-' . str_replace('id_magazzino_', 'magazzino_', $c);
            if ($this->checkForeignKeyExist('mg_documento_riga', $fk)) {
                $this->dropForeignKey($fk, '{{%mg_documento_riga}}');
            }
            $this->addForeignKey($fk, '{{%mg_documento_riga}}', $c,
                '{{%mg_magazzino}}', 'id', 'NO ACTION', 'NO ACTION');
        }
    }

    public function safeDown()
    {
        foreach ($this->colonne as $c) {
            $fk = 'fk-mg_documento_riga-' . str_replace('id_magazzino_', 'magazzino_', $c);
            if ($this->checkForeignKeyExist('mg_documento_riga', $fk)) {
                $this->dropForeignKey($fk, '{{%mg_documento_riga}}');
            }
            if ($this->checkColumnExist('mg_documento_riga', $c)) {
                $this->dropColumn('{{%mg_documento_riga}}', $c);
            }
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
