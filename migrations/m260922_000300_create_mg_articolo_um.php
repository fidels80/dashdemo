<?php

use yii\db\Migration;

/**
 * Unità di misura per articolo: collega mg_articolo a mg_unita_misura
 * con il fattore di conversione verso l'unità base e il flag di predefinita.
 */
class m260922_000300_create_mg_articolo_um extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_articolo_um')) {
            $this->createTable('{{%mg_articolo_um}}', [
                'id' => $this->primaryKey(),
                'id_articolo' => $this->integer()->notNull(),
                'id_unita_misura' => $this->integer()->notNull(),
                'fattore' => $this->decimal(18, 4)->notNull()->defaultValue(1),
                'predefinita' => $this->boolean()->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);

            $this->createIndex('idx-mg_articolo_um-articolo', '{{%mg_articolo_um}}', 'id_articolo');
            $this->createIndex('idx-mg_articolo_um-unita', '{{%mg_articolo_um}}', 'id_unita_misura');
            $this->createIndex('idx-mg_articolo_um-unico', '{{%mg_articolo_um}}',
                ['id_articolo', 'id_unita_misura'], true);

            $this->addForeignKey('fk-mg_articolo_um-articolo', '{{%mg_articolo_um}}', 'id_articolo',
                '{{%mg_articolo}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk-mg_articolo_um-unita', '{{%mg_articolo_um}}', 'id_unita_misura',
                '{{%mg_unita_misura}}', 'id', 'NO ACTION', 'NO ACTION');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_articolo_um')) {
            $this->dropForeignKey('fk-mg_articolo_um-articolo', '{{%mg_articolo_um}}');
            $this->dropForeignKey('fk-mg_articolo_um-unita', '{{%mg_articolo_um}}');
            $this->dropTable('{{%mg_articolo_um}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
