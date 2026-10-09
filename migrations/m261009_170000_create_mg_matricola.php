<?php

use yii\db\Migration;

/**
 * Anagrafica matricole / numeri di serie.
 *
 * Una matricola e' legata a un articolo (per id e/o codice) e riporta:
 * - codice_articolo / id_articolo: articolo di appartenenza;
 * - matricola: numero di serie / matricola;
 * - descrizione: descrizione libera;
 * - nota: note aggiuntive;
 * - attivo: se la matricola e' utilizzabile.
 *
 * Nella form documento, con il flag "gestione_seriali" attivo, si possono
 * selezionare solo le matricole dell'articolo della riga e crearne di nuove
 * automaticamente associate all'articolo.
 */
class m261009_170000_create_mg_matricola extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_matricola')) {
            $this->createTable('{{%mg_matricola}}', [
                'id' => $this->primaryKey(),
                'id_articolo' => $this->integer()->null(),
                'codice_articolo' => $this->string(25)->null(),
                'matricola' => $this->string(100)->notNull(),
                'descrizione' => $this->string(200)->null(),
                'nota' => $this->string(500)->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_matricola-articolo', '{{%mg_matricola}}', 'id_articolo');
            $this->createIndex('idx-mg_matricola-codice_articolo', '{{%mg_matricola}}', 'codice_articolo');
        }

        $fk = 'fk-mg_matricola-articolo';
        if ($this->checkForeignKeyExist('mg_matricola', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_matricola}}');
        }
        $this->addForeignKey($fk, '{{%mg_matricola}}', 'id_articolo',
            '{{%mg_articolo}}', 'id', 'NO ACTION', 'NO ACTION');
    }

    public function safeDown()
    {
        $fk = 'fk-mg_matricola-articolo';
        if ($this->checkForeignKeyExist('mg_matricola', $fk)) {
            $this->dropForeignKey($fk, '{{%mg_matricola}}');
        }
        if ($this->checkTableExist('mg_matricola')) {
            $this->dropTable('{{%mg_matricola}}');
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
