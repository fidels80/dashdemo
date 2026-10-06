<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Contatti delle anagrafiche:
 * - mg_tipo_contatto       (tipi: email, PEC, cellulare, telefono, Discord, ...)
 * - mg_anagrafica_contatto (N contatti di diverso tipo per ogni anagrafica)
 */
class m261001_120000_create_mg_contatti extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_tipo_contatto')) {
            $this->createTable('{{%mg_tipo_contatto}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(30)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'icona' => $this->string(50)->null(),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_tipo_contatto-codice', '{{%mg_tipo_contatto}}', 'codice', true);
        }

        if (!$this->checkTableExist('mg_anagrafica_contatto')) {
            $this->createTable('{{%mg_anagrafica_contatto}}', [
                'id' => $this->primaryKey(),
                'id_anagrafica' => $this->integer()->notNull(),
                'id_tipo_contatto' => $this->integer()->notNull(),
                'valore' => $this->string(200)->notNull(),
                'etichetta' => $this->string(100)->null(),
                'predefinito' => $this->boolean()->notNull()->defaultValue(0),
                'note' => $this->string(500)->null(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_anagrafica_contatto-anagrafica', '{{%mg_anagrafica_contatto}}', 'id_anagrafica');
            $this->createIndex('idx-mg_anagrafica_contatto-tipo', '{{%mg_anagrafica_contatto}}', 'id_tipo_contatto');
            $this->createIndex('idx-mg_anagrafica_contatto-unico', '{{%mg_anagrafica_contatto}}',
                ['id_anagrafica', 'id_tipo_contatto', 'valore'], true);
            $this->addForeignKey('fk-mg_anagrafica_contatto-anagrafica', '{{%mg_anagrafica_contatto}}', 'id_anagrafica',
                '{{%mg_anagrafica}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk-mg_anagrafica_contatto-tipo', '{{%mg_anagrafica_contatto}}', 'id_tipo_contatto',
                '{{%mg_tipo_contatto}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        // Seed dei tipi contatto (idempotente: solo se la tabella è vuota)
        if ($this->checkTableExist('mg_tipo_contatto')) {
            $count = (new Query())->from('{{%mg_tipo_contatto}}')->count();
            if ((int) $count === 0) {
                $this->batchInsert('{{%mg_tipo_contatto}}',
                    ['codice', 'descrizione', 'icona', 'ordine', 'attivo'], [
                        ['email', 'Email', 'fas fa-envelope', 10, 1],
                        ['pec', 'PEC', 'fas fa-certificate', 20, 1],
                        ['cellulare', 'Cellulare', 'fas fa-mobile-alt', 30, 1],
                        ['telefono_fisso', 'Telefono fisso', 'fas fa-phone', 40, 1],
                        ['telefono_personale', 'Telefono personale', 'fas fa-phone-alt', 50, 1],
                        ['fax', 'Fax', 'fas fa-fax', 60, 1],
                        ['sito_web', 'Sito web', 'fas fa-globe', 70, 1],
                        ['linkedin', 'LinkedIn', 'fab fa-linkedin', 80, 1],
                        ['skype', 'Skype', 'fab fa-skype', 90, 1],
                        ['discord', 'Discord', 'fab fa-discord', 100, 1],
                        ['whatsapp', 'WhatsApp', 'fab fa-whatsapp', 110, 1],
                        ['telegram', 'Telegram', 'fab fa-telegram', 120, 1],
                    ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_anagrafica_contatto')) {
            $this->dropForeignKey('fk-mg_anagrafica_contatto-anagrafica', '{{%mg_anagrafica_contatto}}');
            $this->dropForeignKey('fk-mg_anagrafica_contatto-tipo', '{{%mg_anagrafica_contatto}}');
            $this->dropTable('{{%mg_anagrafica_contatto}}');
        }
        if ($this->checkTableExist('mg_tipo_contatto')) {
            $this->dropTable('{{%mg_tipo_contatto}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        )->bindValue(':table', $table)->queryScalar() > 0;
    }
}
