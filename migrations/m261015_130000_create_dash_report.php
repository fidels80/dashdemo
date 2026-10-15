<?php

use yii\db\Migration;

/**
 * Report designer: definizione dei report, delle fonti dati, attivazione
 * per singolo operatore e log delle esecuzioni.
 *
 * - dash_report_fonte     : dataset (tabella/vista o SQL tecnico, anche con
 *                           join cross-database tra le connessioni di db.php).
 * - dash_report           : definizione del report (colonne, filtri, parametri,
 *                           raggruppamenti, aggregazioni, calcolati, layout).
 * - dash_report_utente    : abilitazione del singolo report a un operatore.
 * - dash_report_esecuzione: storico delle esecuzioni (audit e statistiche).
 */
class m261015_130000_create_dash_report extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('dash_report_fonte')) {
            $this->createTable('{{%dash_report_fonte}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(50)->notNull(),
                'nome' => $this->string(150)->notNull(),
                'descrizione' => $this->string(500)->null(),
                'tipo' => $this->string(20)->notNull()->defaultValue('strutturata'),
                'connessione' => $this->string(20)->notNull()->defaultValue('db'),
                'base_tabella' => $this->string(128)->null(),
                'definizione' => $this->text()->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'creato_da' => $this->integer()->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('uidx-dash_report_fonte-codice', '{{%dash_report_fonte}}', 'codice', true);
        }

        if (!$this->checkTableExist('dash_report')) {
            $this->createTable('{{%dash_report}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(50)->notNull(),
                'nome' => $this->string(150)->notNull(),
                'descrizione' => $this->string(500)->null(),
                'categoria' => $this->string(50)->null(),
                'fonte_id' => $this->integer()->null(),
                'tipo' => $this->string(20)->notNull()->defaultValue('tabella'),
                'definizione' => $this->text()->null(),
                'layout' => $this->text()->null(),
                'licenza_feature' => $this->string(50)->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'creato_da' => $this->integer()->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('uidx-dash_report-codice', '{{%dash_report}}', 'codice', true);
            $this->createIndex('idx-dash_report-fonte', '{{%dash_report}}', 'fonte_id');
        }

        if (!$this->checkTableExist('dash_report_utente')) {
            $this->createTable('{{%dash_report_utente}}', [
                'id' => $this->primaryKey(),
                'report_id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->notNull(),
                'can_run' => $this->boolean()->notNull()->defaultValue(1),
                'can_edit' => $this->boolean()->notNull()->defaultValue(0),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('uidx-dash_report_utente-unico', '{{%dash_report_utente}}', ['report_id', 'user_id'], true);
            $this->createIndex('idx-dash_report_utente-user', '{{%dash_report_utente}}', 'user_id');
        }

        if (!$this->checkTableExist('dash_report_esecuzione')) {
            $this->createTable('{{%dash_report_esecuzione}}', [
                'id' => $this->primaryKey(),
                'report_id' => $this->integer()->notNull(),
                'user_id' => $this->integer()->null(),
                'parametri' => $this->text()->null(),
                'esito' => $this->string(20)->null(),
                'messaggio' => $this->string(500)->null(),
                'righe' => $this->integer()->null(),
                'durata_ms' => $this->integer()->null(),
                'formato' => $this->string(20)->null(),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-dash_report_esecuzione-report', '{{%dash_report_esecuzione}}', 'report_id');
            $this->createIndex('idx-dash_report_esecuzione-created', '{{%dash_report_esecuzione}}', 'created_at');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_report_esecuzione')) {
            $this->dropTable('{{%dash_report_esecuzione}}');
        }
        if ($this->checkTableExist('dash_report_utente')) {
            $this->dropTable('{{%dash_report_utente}}');
        }
        if ($this->checkTableExist('dash_report')) {
            $this->dropTable('{{%dash_report}}');
        }
        if ($this->checkTableExist('dash_report_fonte')) {
            $this->dropTable('{{%dash_report_fonte}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
