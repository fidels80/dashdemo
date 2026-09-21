<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Menu laterale dinamico dell'applicazione demo.
 * - dash_menu:        voci di menu (con gerarchia padre/figlio)
 * - dash_menu_utente: assegnazione delle voci agli utenti
 */
class m260921_190000_create_dash_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('dash_menu')) {
            $this->createTable('{{%dash_menu}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(50)->notNull(),
                'label' => $this->string(100)->notNull(),
                'icona' => $this->string(50)->null(),
                'url' => $this->string(200)->null(),
                'genitore_id' => $this->integer()->null(),
                'livello_min' => $this->integer()->notNull()->defaultValue(0),
                'ordine' => $this->integer()->notNull()->defaultValue(0),
                'per_tutti' => $this->boolean()->notNull()->defaultValue(1),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-dash_menu-codice', '{{%dash_menu}}', 'codice', true);
            $this->createIndex('idx-dash_menu-genitore', '{{%dash_menu}}', 'genitore_id');
        }

        if (!$this->checkTableExist('dash_menu_utente')) {
            $this->createTable('{{%dash_menu_utente}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'menu_id' => $this->integer()->notNull(),
            ]);

            $this->createIndex('idx-dash_menu_utente-unico', '{{%dash_menu_utente}}', ['user_id', 'menu_id'], true);
        }

        // Seed iniziale (solo se la tabella e' vuota)
        if ($this->checkTableExist('dash_menu')) {
            $count = (new Query())->from('{{%dash_menu}}')->count();
            if ((int) $count === 0) {
                $this->seedMenu();
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_menu_utente')) {
            $this->dropTable('{{%dash_menu_utente}}');
        }
        if ($this->checkTableExist('dash_menu')) {
            $this->dropTable('{{%dash_menu}}');
        }
    }

    private function seedMenu()
    {
        $now = new \yii\db\Expression('GETDATE()');

        $parents = [
            ['planning', 'Planning', 'calendar-days', null, null, 0, 10, 1, 1, $now],
            ['todo', 'ToDo', 'check-square', null, null, 0, 20, 1, 1, $now],
            ['micro', 'Microgestionale', 'file-invoice', null, null, 0, 30, 1, 1, $now],
            ['sicurezza', 'Sicurezza', 'shield-alt', null, null, 0, 40, 1, 1, $now],
            ['manuale', 'Manuale', 'book', 'site/manuale', null, 0, 50, 1, 1, $now],
        ];

        $this->batchInsert('{{%dash_menu}}',
            ['codice', 'label', 'icona', 'url', 'genitore_id', 'livello_min', 'ordine', 'per_tutti', 'attivo', 'created_at'],
            $parents);

        $map = (new Query())->select(['id', 'codice'])->from('{{%dash_menu}}')->indexBy('codice')->all();

        $children = [
            ['planning-index', 'Calendario', 'calendar-alt', 'planning/index', $map['planning']['id'], 0, 1, 1, 1, $now],
            ['planning-list', 'Lista Attivita', 'list', 'planning/list', $map['planning']['id'], 0, 2, 1, 1, $now],
            ['planning-create', 'Nuova Attivita', 'plus', 'planning/create', $map['planning']['id'], 0, 3, 1, 1, $now],
            ['todo-index', 'Lista', 'list', 'todomain/index', $map['todo']['id'], 0, 1, 1, 1, $now],
            ['todo-board', 'Board', 'columns', 'todomain/board', $map['todo']['id'], 0, 2, 1, 1, $now],
            ['todo-backlog', 'Backlog', 'list-ol', 'todomain/backlog', $map['todo']['id'], 0, 3, 1, 1, $now],
            ['micro-doc', 'Documenti', 'file-invoice', 'mgdocumento/index', $map['micro']['id'], 0, 1, 1, 1, $now],
            ['micro-tipi', 'Tipi documento', 'tags', 'mgtipodocumento/index', $map['micro']['id'], 0, 2, 1, 1, $now],
            ['micro-anag', 'Anagrafica', 'users', 'mganagrafica/index', $map['micro']['id'], 0, 3, 1, 1, $now],
            ['micro-art', 'Articoli', 'boxes', 'mgarticolo/index', $map['micro']['id'], 0, 4, 1, 1, $now],
            ['sic-2fa', 'Autenticazione 2FA', 'shield-alt', 'two-factor/setup', $map['sicurezza']['id'], 0, 1, 1, 1, $now],
            ['sic-token', 'Token API', 'key', 'apitoken/index', $map['sicurezza']['id'], 0, 2, 1, 1, $now],
            ['sic-menu', 'Gestione Menu', 'bars', 'dashmenu/index', $map['sicurezza']['id'], 0, 3, 1, 1, $now],
        ];

        $this->batchInsert('{{%dash_menu}}',
            ['codice', 'label', 'icona', 'url', 'genitore_id', 'livello_min', 'ordine', 'per_tutti', 'attivo', 'created_at'],
            $children);
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
