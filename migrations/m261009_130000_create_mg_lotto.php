<?php

use yii\db\Migration;

/**
 * Anagrafica lotti.
 *
 * Un lotto e' legato a un articolo (per codice o per id) e riporta:
 * - codice_articolo / id_articolo: articolo di appartenenza;
 * - codice_lotto: codice identificativo del lotto;
 * - descrizione: descrizione libera del lotto;
 * - data_scadenza: scadenza del lotto;
 * - nota: note aggiuntive.
 *
 * Nella form documento, con il flag "gestione_lotti" attivo, si possono
 * selezionare solo i lotti dell'articolo della riga e crearne di nuovi
 * automaticamente associati all'articolo.
 */
class m261009_130000_create_mg_lotto extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_lotto')) {
            $this->createTable('{{%mg_lotto}}', [
                'id' => $this->primaryKey(),
                'id_articolo' => $this->integer()->null(),
                'codice_articolo' => $this->string(25)->null(),
                'codice_lotto' => $this->string(50)->notNull(),
                'descrizione' => $this->string(200)->null(),
                'data_scadenza' => $this->date()->null(),
                'nota' => $this->string(500)->null(),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_lotto-articolo', '{{%mg_lotto}}', 'id_articolo');
            $this->createIndex('idx-mg_lotto-codice_articolo', '{{%mg_lotto}}', 'codice_articolo');
        }

        $fk = 'fk-mg_lotto-articolo';
        if ($this->checkForeignKeyExist('mg_lotto', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_lotto}}');
        }
        $this->addForeignKey($fk, '{{%mg_lotto}}', 'id_articolo',
            '{{%mg_articolo}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function safeDown()
    {
        $fk = 'fk-mg_lotto-articolo';
        if ($this->checkForeignKeyExist('mg_lotto', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_lotto}}');
        }
        if ($this->checkTableExist('mg_lotto')) {
            $this->dropTable('{{%mg_lotto}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
