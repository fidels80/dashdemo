<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Permessi per form/funzione (ACL) assegnati per utente.
 * - dash_permesso:        elenco risorse (una per controller/form)
 * - dash_permesso_utente: flag vista/crea/modifica/elimina per utente
 */
class m260921_200000_create_dash_permessi extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('dash_permesso')) {
            $this->createTable('{{%dash_permesso}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(50)->notNull(),
                'descrizione' => $this->string(150)->notNull(),
                'gruppo' => $this->string(50)->null(),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-dash_permesso-codice', '{{%dash_permesso}}', 'codice', true);
        }

        if (!$this->checkTableExist('dash_permesso_utente')) {
            $this->createTable('{{%dash_permesso_utente}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'permesso_id' => $this->integer()->notNull(),
                'can_view' => $this->boolean()->notNull()->defaultValue(0),
                'can_create' => $this->boolean()->notNull()->defaultValue(0),
                'can_update' => $this->boolean()->notNull()->defaultValue(0),
                'can_delete' => $this->boolean()->notNull()->defaultValue(0),
            ]);
            $this->createIndex('idx-dash_permesso_utente-unico', '{{%dash_permesso_utente}}', ['user_id', 'permesso_id'], true);
        }

        if ($this->checkTableExist('dash_permesso')) {
            $count = (new Query())->from('{{%dash_permesso}}')->count();
            if ((int) $count === 0) {
                $now = new \yii\db\Expression('GETDATE()');
                $this->batchInsert('{{%dash_permesso}}',
                    ['codice', 'descrizione', 'gruppo', 'ordine', 'attivo', 'created_at'], [
                        ['planning', 'Planning', 'Operativo', 10, 1, $now],
                        ['todomain', 'ToDo', 'Operativo', 20, 1, $now],
                        ['mgdocumento', 'Documenti', 'Microgestionale', 30, 1, $now],
                        ['mgtipodocumento', 'Tipi documento', 'Microgestionale', 40, 1, $now],
                        ['mganagrafica', 'Anagrafica', 'Microgestionale', 50, 1, $now],
                        ['mgarticolo', 'Articoli', 'Microgestionale', 60, 1, $now],
                        ['apitoken', 'Token API', 'Sicurezza', 70, 1, $now],
                        ['dashmenu', 'Gestione Menu', 'Sicurezza', 80, 1, $now],
                        ['dashpermesso', 'Gestione Permessi', 'Sicurezza', 90, 1, $now],
                    ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_permesso_utente')) {
            $this->dropTable('{{%dash_permesso_utente}}');
        }
        if ($this->checkTableExist('dash_permesso')) {
            $this->dropTable('{{%dash_permesso}}');
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
