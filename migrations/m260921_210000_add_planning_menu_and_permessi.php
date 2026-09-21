<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Planning) i CRUD delle anagrafiche operative
 * e registra le relative risorse per i permessi.
 */
class m260921_210000_add_planning_menu_and_permessi extends Migration
{
    public function safeUp()
    {
        $now = new \yii\db\Expression('GETDATE()');

        // Voci di menu sotto Planning
        if ($this->tableExists('dash_menu')) {
            $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'planning'])->scalar();

            $items = [
                ['planning-personale', 'Personale', 'users', 'personale/index', 4],
                ['planning-veicoli', 'Veicoli', 'truck', 'veicoli/index', 5],
                ['planning-presenze', 'Presenze', 'user-clock', 'presenze/index', 6],
                ['planning-rapportini', 'Rapportini', 'clock', 'rapportini/index', 7],
                ['planning-mansioni', 'Mansioni', 'briefcase', 'mansioni/index', 8],
                ['planning-reparti', 'Reparti', 'sitemap', 'reparti/index', 9],
                ['planning-locazioni', 'Locazioni', 'map-marker-alt', 'locazioni/index', 10],
                ['planning-tipopres', 'Tipologie presenza', 'clipboard-list', 'tipologiapresenza/index', 11],
            ];

            foreach ($items as $it) {
                $exists = (new Query())->from('{{%dash_menu}}')->where(['codice' => $it[0]])->exists();
                if ($exists) {
                    continue;
                }
                $this->insert('{{%dash_menu}}', [
                    'codice' => $it[0],
                    'label' => $it[1],
                    'icona' => $it[2],
                    'url' => $it[3],
                    'genitore_id' => $parent ?: null,
                    'livello_min' => 0,
                    'ordine' => $it[4],
                    'per_tutti' => 1,
                    'attivo' => 1,
                    'created_at' => $now,
                ]);
            }
        }

        // Risorse permessi per gli stessi controller
        if ($this->tableExists('dash_permesso')) {
            $risorse = [
                ['personale', 'Personale', 'Operativo', 100],
                ['veicoli', 'Veicoli', 'Operativo', 110],
                ['presenze', 'Presenze', 'Operativo', 120],
                ['rapportini', 'Rapportini', 'Operativo', 130],
                ['mansioni', 'Mansioni', 'Operativo', 140],
                ['reparti', 'Reparti', 'Operativo', 150],
                ['locazioni', 'Locazioni', 'Operativo', 160],
                ['tipologiapresenza', 'Tipologie presenza', 'Operativo', 170],
            ];

            foreach ($risorse as $r) {
                $exists = (new Query())->from('{{%dash_permesso}}')->where(['codice' => $r[0]])->exists();
                if ($exists) {
                    continue;
                }
                $this->insert('{{%dash_permesso}}', [
                    'codice' => $r[0],
                    'descrizione' => $r[1],
                    'gruppo' => $r[2],
                    'ordine' => $r[3],
                    'attivo' => 1,
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => [
                'planning-personale', 'planning-veicoli', 'planning-presenze', 'planning-rapportini',
                'planning-mansioni', 'planning-reparti', 'planning-locazioni', 'planning-tipopres',
            ]]);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => [
                'personale', 'veicoli', 'presenze', 'rapportini',
                'mansioni', 'reparti', 'locazioni', 'tipologiapresenza',
            ]]);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
