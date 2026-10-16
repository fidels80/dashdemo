<?php

use yii\db\Migration;

/**
 * Estensioni CRM:
 * - mg_crm_allegato:            file allegati al ticket e ai singoli commenti
 * - mg_crm_faq:                 FAQ generate dalla chiusura dei ticket
 * - mg_crm_ticket:              tempo impiegato, soluzione, rapportino, evaso
 * - mg_crm_ticket_messaggio:    flag "soluzione"
 */
class m261016_110000_add_crm_allegati_tempo_faq extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_crm_allegato')) {
            $this->createTable('{{%mg_crm_allegato}}', [
                'id' => $this->primaryKey(),
                'id_ticket' => $this->integer()->null(),
                'id_messaggio' => $this->integer()->null(),
                'user_id' => $this->integer()->null(),
                'nome_file' => $this->string(255)->notNull(),
                'nome_originale' => $this->string(255)->notNull(),
                'mime' => $this->string(100)->null(),
                'dimensione' => $this->integer()->null(),
                'percorso' => $this->string(500)->notNull(),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_crm_allegato-ticket', '{{%mg_crm_allegato}}', 'id_ticket');
            $this->createIndex('idx-mg_crm_allegato-messaggio', '{{%mg_crm_allegato}}', 'id_messaggio');
        }

        if (!$this->checkTableExist('mg_crm_faq')) {
            $this->createTable('{{%mg_crm_faq}}', [
                'id' => $this->primaryKey(),
                'id_ticket' => $this->integer()->null(),
                'id_articolo' => $this->integer()->null(),
                'codice_articolo' => $this->string(25)->null(),
                'domanda' => $this->string(500)->notNull(),
                'soluzione' => $this->string(4000)->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_by' => $this->string(50)->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_crm_faq-articolo', '{{%mg_crm_faq}}', 'id_articolo');
            $this->createIndex('idx-mg_crm_faq-codice', '{{%mg_crm_faq}}', 'codice_articolo');
        }

        $this->addColumnIfMissing('mg_crm_ticket', 'tempo_impiegato_ore',
            $this->decimal(9, 2)->null(), 'Tempo impiegato in ore');
        $this->addColumnIfMissing('mg_crm_ticket', 'soluzione',
            $this->string(4000)->null(), 'Soluzione');
        $this->addColumnIfMissing('mg_crm_ticket', 'id_rapportino',
            $this->string(36)->null(), 'Rapportino');
        $this->addColumnIfMissing('mg_crm_ticket', 'evaso',
            $this->boolean()->notNull()->defaultValue(0), 'Evaso in rapportino');

        $this->addColumnIfMissing('mg_crm_ticket_messaggio', 'is_soluzione',
            $this->boolean()->notNull()->defaultValue(0), 'Soluzione');
    }

    public function safeDown()
    {
        foreach (['is_soluzione'] as $col) {
            $this->dropColumnIfExists('mg_crm_ticket_messaggio', $col);
        }
        foreach (['tempo_impiegato_ore', 'soluzione', 'id_rapportino', 'evaso'] as $col) {
            $this->dropColumnIfExists('mg_crm_ticket', $col);
        }
        if ($this->checkTableExist('mg_crm_faq')) {
            $this->dropTable('{{%mg_crm_faq}}');
        }
        if ($this->checkTableExist('mg_crm_allegato')) {
            $this->dropTable('{{%mg_crm_allegato}}');
        }
    }

    private function addColumnIfMissing($table, $column, $type, $comment = null)
    {
        if (!$this->checkTableExist($table) || $this->columnExists($table, $column)) {
            return;
        }
        $this->addColumn('{{%' . $table . '}}', $column, $type);
    }

    private function dropColumnIfExists($table, $column)
    {
        if ($this->checkTableExist($table) && $this->columnExists($table, $column)) {
            $this->dropColumn('{{%' . $table . '}}', $column);
        }
    }

    private function columnExists($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
