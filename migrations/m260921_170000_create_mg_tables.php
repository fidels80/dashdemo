<?php

use yii\db\Migration;

/**
 * Microgestionale documentale (tabelle proprie, indipendenti da uec_*):
 * - mg_anagrafica       (clienti/fornitori)
 * - mg_articolo         (articoli)
 * - mg_tipo_documento   (tipi documento con contatore/anno/flag congruità)
 * - mg_documento        (testata documento con numero/anno/suffisso)
 * - mg_documento_riga   (righe documento)
 */
class m260921_170000_create_mg_tables extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_anagrafica')) {
            $this->createTable('{{%mg_anagrafica}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'ragione_sociale' => $this->string(200)->notNull(),
                'partita_iva' => $this->string(20)->null(),
                'codice_fiscale' => $this->string(20)->null(),
                'indirizzo' => $this->string(200)->null(),
                'cap' => $this->string(10)->null(),
                'citta' => $this->string(100)->null(),
                'provincia' => $this->string(3)->null(),
                'telefono' => $this->string(50)->null(),
                'email' => $this->string(100)->null(),
                'tipo' => $this->string(20)->null(),
                'attivo' => $this->boolean()->defaultValue(true),
            ]);
            $this->createIndex('idx-mg_anagrafica-codice', '{{%mg_anagrafica}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_articolo')) {
            $this->createTable('{{%mg_articolo}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(25)->notNull(),
                'descrizione' => $this->string(250)->notNull(),
                'um' => $this->string(10)->null(),
                'prezzo' => $this->decimal(18, 4)->defaultValue(0),
                'iva' => $this->decimal(9, 2)->defaultValue(0),
                'attivo' => $this->boolean()->defaultValue(true),
            ]);
            $this->createIndex('idx-mg_articolo-codice', '{{%mg_articolo}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_tipo_documento')) {
            $this->createTable('{{%mg_tipo_documento}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(200)->notNull(),
                'anno' => $this->integer()->notNull()->defaultValue(0),
                'contatore' => $this->integer()->notNull()->defaultValue(0),
                'usa_progressivo' => $this->boolean()->defaultValue(true),
                'congruita' => $this->boolean()->defaultValue(false),
                'attivo' => $this->boolean()->defaultValue(true),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_tipo_documento-codice', '{{%mg_tipo_documento}}', 'codice', true);

            $anno = (int) date('Y');
            $this->batchInsert('{{%mg_tipo_documento}}',
                ['codice', 'descrizione', 'anno', 'contatore', 'usa_progressivo', 'congruita', 'attivo', 'created_at'], [
                    ['ORD', 'Ordine cliente', $anno, 0, 1, 1, 1, date('Y-m-d H:i:s')],
                    ['PRE', 'Preventivo', $anno, 0, 1, 1, 1, date('Y-m-d H:i:s')],
                    ['DDT', 'Documento di trasporto', $anno, 0, 1, 1, 1, date('Y-m-d H:i:s')],
                    ['FTT', 'Fattura', $anno, 0, 1, 1, 1, date('Y-m-d H:i:s')],
                ]);
        }

        if (!$this->checkTableExist('mg_documento')) {
            $this->createTable('{{%mg_documento}}', [
                'id' => $this->primaryKey(),
                'id_tipo' => $this->integer()->notNull(),
                'codice_tipo' => $this->string(20)->notNull(),
                'anno' => $this->integer()->notNull(),
                'numero' => $this->integer()->notNull(),
                'suffisso' => $this->string(10)->notNull()->defaultValue(''),
                'data' => $this->date()->notNull(),
                'id_anagrafica' => $this->integer()->null(),
                'descrizione' => $this->string(500)->null(),
                'stato' => $this->string(20)->null()->defaultValue('bozza'),
                'totale' => $this->decimal(18, 2)->defaultValue(0),
                'note' => $this->string(2000)->null(),
                'created_by' => $this->string(50)->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-mg_documento-unico', '{{%mg_documento}}',
                ['id_tipo', 'anno', 'numero', 'suffisso'], true);
            $this->createIndex('idx-mg_documento-data', '{{%mg_documento}}', 'data');
            $this->createIndex('idx-mg_documento-anagrafica', '{{%mg_documento}}', 'id_anagrafica');

            $this->addForeignKey('fk-mg_documento-tipo', '{{%mg_documento}}', 'id_tipo',
                '{{%mg_tipo_documento}}', 'id', 'NO ACTION', 'NO ACTION');
            $this->addForeignKey('fk-mg_documento-anagrafica', '{{%mg_documento}}', 'id_anagrafica',
                '{{%mg_anagrafica}}', 'id', 'SET NULL', 'SET NULL');
        }

        if (!$this->checkTableExist('mg_documento_riga')) {
            $this->createTable('{{%mg_documento_riga}}', [
                'id' => $this->primaryKey(),
                'id_documento' => $this->integer()->notNull(),
                'id_articolo' => $this->integer()->null(),
                'codice_articolo' => $this->string(25)->null(),
                'descrizione' => $this->string(500)->null(),
                'qta' => $this->decimal(18, 4)->defaultValue(0),
                'prezzo' => $this->decimal(18, 4)->defaultValue(0),
                'sconto' => $this->decimal(9, 2)->defaultValue(0),
                'iva' => $this->decimal(9, 2)->defaultValue(0),
                'totale' => $this->decimal(18, 2)->defaultValue(0),
                'ordine' => $this->integer()->defaultValue(0),
            ]);

            $this->createIndex('idx-mg_documento_riga-doc', '{{%mg_documento_riga}}', 'id_documento');

            $this->addForeignKey('fk-mg_documento_riga-doc', '{{%mg_documento_riga}}', 'id_documento',
                '{{%mg_documento}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk-mg_documento_riga-art', '{{%mg_documento_riga}}', 'id_articolo',
                '{{%mg_articolo}}', 'id', 'SET NULL', 'SET NULL');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_documento_riga')) {
            $this->dropForeignKey('fk-mg_documento_riga-doc', '{{%mg_documento_riga}}');
            $this->dropForeignKey('fk-mg_documento_riga-art', '{{%mg_documento_riga}}');
            $this->dropTable('{{%mg_documento_riga}}');
        }
        if ($this->checkTableExist('mg_documento')) {
            $this->dropForeignKey('fk-mg_documento-tipo', '{{%mg_documento}}');
            $this->dropForeignKey('fk-mg_documento-anagrafica', '{{%mg_documento}}');
            $this->dropTable('{{%mg_documento}}');
        }
        if ($this->checkTableExist('mg_tipo_documento')) {
            $this->dropTable('{{%mg_tipo_documento}}');
        }
        if ($this->checkTableExist('mg_articolo')) {
            $this->dropTable('{{%mg_articolo}}');
        }
        if ($this->checkTableExist('mg_anagrafica')) {
            $this->dropTable('{{%mg_anagrafica}}');
        }
    }

    private function checkTableExist($table)
    {
        $command = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        );
        $command->bindValues([':table' => $table]);
        return $command->queryScalar() > 0;
    }
}
