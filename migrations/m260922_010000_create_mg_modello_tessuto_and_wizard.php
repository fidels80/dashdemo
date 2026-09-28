<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Wizard prodotti (modelli, tessuti, colori, taglie):
 * - mg_modello_tessuto: tessuti disponibili per un modello
 * - mg_articolo.id_tessuto: attributo variante tessuto
 * - seed attributi "tessuto"
 * - voce di menu "Wizard prodotti"
 */
class m260922_010000_create_mg_modello_tessuto_and_wizard extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_modello_tessuto')) {
            $this->createTable('{{%mg_modello_tessuto}}', [
                'id' => $this->primaryKey(),
                'id_modello' => $this->integer()->notNull(),
                'id_tessuto' => $this->integer()->notNull(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);

            $this->createIndex('idx-mg_modello_tessuto-modello', '{{%mg_modello_tessuto}}', 'id_modello');
            $this->createIndex('idx-mg_modello_tessuto-tessuto', '{{%mg_modello_tessuto}}', 'id_tessuto');
            $this->createIndex('idx-mg_modello_tessuto-unico', '{{%mg_modello_tessuto}}',
                ['id_modello', 'id_tessuto'], true);

            $this->addForeignKey('fk-mg_modello_tessuto-modello', '{{%mg_modello_tessuto}}', 'id_modello',
                '{{%mg_attributo_articolo}}', 'id', 'CASCADE', 'NO ACTION');
            $this->addForeignKey('fk-mg_modello_tessuto-tessuto', '{{%mg_modello_tessuto}}', 'id_tessuto',
                '{{%mg_attributo_articolo}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        if (!$this->checkColumnExist('mg_articolo', 'id_tessuto')) {
            $this->addColumn('{{%mg_articolo}}', 'id_tessuto', $this->integer()->null());
            $this->createIndex('idx-mg_articolo-id_tessuto', '{{%mg_articolo}}', 'id_tessuto');
        }
        if (!$this->checkForeignKeyExist('mg_articolo', 'fk-mg_articolo-tessuto')) {
            $this->addForeignKey('fk-mg_articolo-tessuto', '{{%mg_articolo}}', 'id_tessuto',
                '{{%mg_attributo_articolo}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        if ($this->checkTableExist('mg_attributo_articolo')) {
            $esiste = (new Query())->from('{{%mg_attributo_articolo}}')
                ->where(['tipo' => 'tessuto'])->exists();
            if (!$esiste) {
                $this->batchInsert('{{%mg_attributo_articolo}}',
                    ['tipo', 'codice', 'descrizione', 'attivo'], [
                        ['tessuto', 'COT', 'Cotone', 1],
                        ['tessuto', 'LIN', 'Lino', 1],
                        ['tessuto', 'LAN', 'Lana', 1],
                        ['tessuto', 'POL', 'Poliestere', 1],
                        ['tessuto', 'SET', 'Seta', 1],
                    ]);
            }
        }

        if ($this->checkTableExist('dash_menu')) {
            $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
            if (!(new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-wizard'])->exists()) {
                $this->insert('{{%dash_menu}}', [
                    'codice' => 'micro-wizard',
                    'label' => 'Wizard prodotti',
                    'icona' => 'magic',
                    'url' => 'mgwizard/index',
                    'genitore_id' => $parent ?: null,
                    'livello_min' => 0,
                    'ordine' => 10,
                    'per_tutti' => 1,
                    'attivo' => 1,
                    'created_at' => new \yii\db\Expression('GETDATE()'),
                ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-wizard']);
        }

        if ($this->checkForeignKeyExist('mg_articolo', 'fk-mg_articolo-tessuto')) {
            $this->dropForeignKey('fk-mg_articolo-tessuto', '{{%mg_articolo}}');
        }
        if ($this->checkColumnExist('mg_articolo', 'id_tessuto')) {
            $this->dropColumn('{{%mg_articolo}}', 'id_tessuto');
        }

        if ($this->checkTableExist('mg_modello_tessuto')) {
            $this->dropForeignKey('fk-mg_modello_tessuto-modello', '{{%mg_modello_tessuto}}');
            $this->dropForeignKey('fk-mg_modello_tessuto-tessuto', '{{%mg_modello_tessuto}}');
            $this->dropTable('{{%mg_modello_tessuto}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
