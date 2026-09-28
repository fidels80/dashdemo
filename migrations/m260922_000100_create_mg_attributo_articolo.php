<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tabella unica per gli attributi variante dell'articolo:
 * marche, modelli, taglie e colori (discriminati dal campo "tipo").
 * Riutilizzabile e con possibilità di creazione inline.
 */
class m260922_000100_create_mg_attributo_articolo extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_attributo_articolo')) {
            $this->createTable('{{%mg_attributo_articolo}}', [
                'id' => $this->primaryKey(),
                'tipo' => $this->string(20)->notNull(),
                'codice' => $this->string(30)->null(),
                'descrizione' => $this->string(100)->notNull(),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_attributo-tipo', '{{%mg_attributo_articolo}}', ['tipo', 'descrizione'], true);
        }

        if ($this->checkTableExist('mg_attributo_articolo')) {
            $count = (new Query())->from('{{%mg_attributo_articolo}}')->count();
            if ((int) $count === 0) {
                $rows = [
                    ['marca', 'GEN', 'Generico'],
                    ['modello', 'STD', 'Standard'],
                    ['taglia', 'XS', 'Extra Small'],
                    ['taglia', 'S', 'Small'],
                    ['taglia', 'M', 'Medium'],
                    ['taglia', 'L', 'Large'],
                    ['taglia', 'XL', 'Extra Large'],
                    ['taglia', 'XXL', 'Double Extra Large'],
                    ['colore', 'NER', 'Nero'],
                    ['colore', 'BIA', 'Bianco'],
                    ['colore', 'ROS', 'Rosso'],
                    ['colore', 'BLU', 'Blu'],
                    ['colore', 'VER', 'Verde'],
                    ['colore', 'GIA', 'Giallo'],
                ];
                $insert = [];
                foreach ($rows as $r) {
                    $insert[] = ['tipo' => $r[0], 'codice' => $r[1], 'descrizione' => $r[2], 'attivo' => 1];
                }
                $this->batchInsert('{{%mg_attributo_articolo}}', ['tipo', 'codice', 'descrizione', 'attivo'], $insert);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_attributo_articolo')) {
            $this->dropTable('{{%mg_attributo_articolo}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
