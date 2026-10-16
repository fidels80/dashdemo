<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Modulo CRM del microgestionale (tabelle proprie, prefisso mg_crm_):
 * - mg_crm_tipo              (tipo richiesta: assistenza, amministrativa, preventivo, ...)
 * - mg_crm_stato             (workflow del ticket, con flag "chiuso")
 * - mg_crm_priorita          (bassa/normale/alta/urgente)
 * - mg_crm_ticket            (richiesta/ticket del cliente)
 * - mg_crm_ticket_messaggio  (thread di conversazione, con note interne)
 * - mg_crm_utente_anagrafica (associazione utente esterno <-> anagrafica mg)
 *
 * Il collegamento N:N tra utenti esterni e anagrafiche permette a un
 * contatto di vedere i dati di piu' aziende.
 */
class m261016_100000_create_mg_crm extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_crm_tipo')) {
            $this->createTable('{{%mg_crm_tipo}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(30)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'icona' => $this->string(50)->null(),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_crm_tipo-codice', '{{%mg_crm_tipo}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_crm_stato')) {
            $this->createTable('{{%mg_crm_stato}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(30)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'colore' => $this->string(20)->null(),
                'is_chiuso' => $this->boolean()->notNull()->defaultValue(0),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_crm_stato-codice', '{{%mg_crm_stato}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_crm_priorita')) {
            $this->createTable('{{%mg_crm_priorita}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(30)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'colore' => $this->string(20)->null(),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_crm_priorita-codice', '{{%mg_crm_priorita}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_crm_ticket')) {
            $this->createTable('{{%mg_crm_ticket}}', [
                'id' => $this->primaryKey(),
                'anno' => $this->integer()->notNull(),
                'numero' => $this->integer()->notNull(),
                'id_tipo' => $this->integer()->notNull(),
                'id_stato' => $this->integer()->notNull(),
                'id_priorita' => $this->integer()->null(),
                'id_anagrafica' => $this->integer()->null(),
                'id_utente' => $this->integer()->null(),
                'id_articolo' => $this->integer()->null(),
                'id_matricola' => $this->integer()->null(),
                'id_documento' => $this->integer()->null(),
                'oggetto' => $this->string(250)->notNull(),
                'descrizione' => $this->string(2000)->null(),
                'assegnato_a' => $this->integer()->null(),
                'data_apertura' => $this->dateTime()->notNull(),
                'data_presa_carico' => $this->dateTime()->null(),
                'data_chiusura' => $this->dateTime()->null(),
                'data_scadenza' => $this->dateTime()->null(),
                'created_by' => $this->string(50)->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-mg_crm_ticket-unico', '{{%mg_crm_ticket}}', ['anno', 'numero'], true);
            $this->createIndex('idx-mg_crm_ticket-anagrafica', '{{%mg_crm_ticket}}', 'id_anagrafica');
            $this->createIndex('idx-mg_crm_ticket-stato', '{{%mg_crm_ticket}}', 'id_stato');
            $this->createIndex('idx-mg_crm_ticket-utente', '{{%mg_crm_ticket}}', 'id_utente');
            $this->createIndex('idx-mg_crm_ticket-assegnato', '{{%mg_crm_ticket}}', 'assegnato_a');
            $this->createIndex('idx-mg_crm_ticket-documento', '{{%mg_crm_ticket}}', 'id_documento');

            $this->addForeignKey('fk-mg_crm_ticket-tipo', '{{%mg_crm_ticket}}', 'id_tipo',
                '{{%mg_crm_tipo}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-stato', '{{%mg_crm_ticket}}', 'id_stato',
                '{{%mg_crm_stato}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-priorita', '{{%mg_crm_ticket}}', 'id_priorita',
                '{{%mg_crm_priorita}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-anagrafica', '{{%mg_crm_ticket}}', 'id_anagrafica',
                '{{%mg_anagrafica}}', 'id', 'SET NULL', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-articolo', '{{%mg_crm_ticket}}', 'id_articolo',
                '{{%mg_articolo}}', 'id', 'SET NULL', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-matricola', '{{%mg_crm_ticket}}', 'id_matricola',
                '{{%mg_matricola}}', 'id', 'SET NULL', 'NO ACTION');
            $this->addForeignKey('fk-mg_crm_ticket-documento', '{{%mg_crm_ticket}}', 'id_documento',
                '{{%mg_documento}}', 'id', 'SET NULL', 'NO ACTION');
        }

        if (!$this->checkTableExist('mg_crm_ticket_messaggio')) {
            $this->createTable('{{%mg_crm_ticket_messaggio}}', [
                'id' => $this->primaryKey(),
                'id_ticket' => $this->integer()->notNull(),
                'user_id' => $this->integer()->null(),
                'autore' => $this->string(100)->null(),
                'is_nota_interna' => $this->boolean()->notNull()->defaultValue(0),
                'messaggio' => $this->string(2000)->notNull(),
                'created_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-mg_crm_ticket_messaggio-ticket', '{{%mg_crm_ticket_messaggio}}', 'id_ticket');
            $this->addForeignKey('fk-mg_crm_ticket_messaggio-ticket', '{{%mg_crm_ticket_messaggio}}', 'id_ticket',
                '{{%mg_crm_ticket}}', 'id', 'CASCADE', 'CASCADE');
        }

        if (!$this->checkTableExist('mg_crm_utente_anagrafica')) {
            $this->createTable('{{%mg_crm_utente_anagrafica}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'id_anagrafica' => $this->integer()->notNull(),
                'created_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-mg_crm_utente_anagrafica-unico', '{{%mg_crm_utente_anagrafica}}',
                ['user_id', 'id_anagrafica'], true);
            $this->addForeignKey('fk-mg_crm_utente_anagrafica-anagrafica', '{{%mg_crm_utente_anagrafica}}',
                'id_anagrafica', '{{%mg_anagrafica}}', 'id', 'CASCADE', 'NO ACTION');
        }

        $this->seedLookup();
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_crm_utente_anagrafica')) {
            $this->dropForeignKey('fk-mg_crm_utente_anagrafica-anagrafica', '{{%mg_crm_utente_anagrafica}}');
            $this->dropTable('{{%mg_crm_utente_anagrafica}}');
        }
        if ($this->checkTableExist('mg_crm_ticket_messaggio')) {
            $this->dropForeignKey('fk-mg_crm_ticket_messaggio-ticket', '{{%mg_crm_ticket_messaggio}}');
            $this->dropTable('{{%mg_crm_ticket_messaggio}}');
        }
        if ($this->checkTableExist('mg_crm_ticket')) {
            foreach (['tipo', 'stato', 'priorita', 'anagrafica', 'articolo', 'matricola', 'documento'] as $fk) {
                $this->dropForeignKey('fk-mg_crm_ticket-' . $fk, '{{%mg_crm_ticket}}');
            }
            $this->dropTable('{{%mg_crm_ticket}}');
        }
        if ($this->checkTableExist('mg_crm_priorita')) {
            $this->dropTable('{{%mg_crm_priorita}}');
        }
        if ($this->checkTableExist('mg_crm_stato')) {
            $this->dropTable('{{%mg_crm_stato}}');
        }
        if ($this->checkTableExist('mg_crm_tipo')) {
            $this->dropTable('{{%mg_crm_tipo}}');
        }
    }

    private function seedLookup()
    {
        if ($this->checkTableExist('mg_crm_tipo')) {
            if ((int) (new Query())->from('{{%mg_crm_tipo}}')->count() === 0) {
                $this->batchInsert('{{%mg_crm_tipo}}', ['codice', 'descrizione', 'icona', 'ordine', 'attivo'], [
                    ['assistenza', 'Assistenza tecnica', 'fas fa-tools', 10, 1],
                    ['amministrativa', 'Richiesta amministrativa', 'fas fa-file-invoice', 20, 1],
                    ['preventivo', 'Richiesta preventivo', 'fas fa-file-signature', 30, 1],
                    ['commerciale', 'Informazione commerciale', 'fas fa-comments', 40, 1],
                    ['reclamo', 'Reclamo', 'fas fa-exclamation-triangle', 50, 1],
                ]);
            }
        }

        if ($this->checkTableExist('mg_crm_stato')) {
            if ((int) (new Query())->from('{{%mg_crm_stato}}')->count() === 0) {
                $this->batchInsert('{{%mg_crm_stato}}',
                    ['codice', 'descrizione', 'colore', 'is_chiuso', 'ordine', 'attivo'], [
                        ['nuovo', 'Nuovo', 'secondary', 0, 10, 1],
                        ['aperto', 'Aperto', 'primary', 0, 20, 1],
                        ['preso_in_carico', 'Preso in carico', 'info', 0, 30, 1],
                        ['in_lavorazione', 'In lavorazione', 'warning', 0, 40, 1],
                        ['attesa_cliente', 'In attesa del cliente', 'dark', 0, 50, 1],
                        ['risolto', 'Risolto', 'success', 0, 60, 1],
                        ['chiuso', 'Chiuso', 'success', 1, 70, 1],
                        ['annullato', 'Annullato', 'danger', 1, 80, 1],
                    ]);
            }
        }

        if ($this->checkTableExist('mg_crm_priorita')) {
            if ((int) (new Query())->from('{{%mg_crm_priorita}}')->count() === 0) {
                $this->batchInsert('{{%mg_crm_priorita}}', ['codice', 'descrizione', 'colore', 'ordine', 'attivo'], [
                    ['bassa', 'Bassa', 'secondary', 10, 1],
                    ['normale', 'Normale', 'primary', 20, 1],
                    ['alta', 'Alta', 'warning', 30, 1],
                    ['urgente', 'Urgente', 'danger', 40, 1],
                ]);
            }
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
