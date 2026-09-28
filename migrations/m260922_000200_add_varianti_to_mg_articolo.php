<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_articolo:
 * - guid (uniqueidentifier, generato, univoco) che coesiste con l'id progressivo
 * - id_marca / id_modello / id_taglia / id_colore (FK verso mg_attributo_articolo)
 */
class m260922_000200_add_varianti_to_mg_articolo extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_articolo', 'guid')) {
            // Colonna nullable, backfill con NEWID(), poi NOT NULL + default.
            $this->addColumn('{{%mg_articolo}}', 'guid', 'uniqueidentifier NULL');
            $this->execute("UPDATE {{%mg_articolo}} SET [guid] = NEWID() WHERE [guid] IS NULL");
            $this->execute("ALTER TABLE {{%mg_articolo}} ALTER COLUMN [guid] UNIQUEIDENTIFIER NOT NULL");
            $this->execute("ALTER TABLE {{%mg_articolo}} ADD CONSTRAINT [DF_mg_articolo_guid] DEFAULT NEWID() FOR [guid]");
            $this->createIndex('idx-mg_articolo-guid', '{{%mg_articolo}}', 'guid', true);
        }

        $cols = [
            'id_marca' => 'fk-mg_articolo-marca',
            'id_modello' => 'fk-mg_articolo-modello',
            'id_taglia' => 'fk-mg_articolo-taglia',
            'id_colore' => 'fk-mg_articolo-colore',
        ];
        foreach ($cols as $col => $fk) {
            if (!$this->checkColumnExist('mg_articolo', $col)) {
                $this->addColumn('{{%mg_articolo}}', $col, $this->integer()->null());
                $this->createIndex('idx-mg_articolo-' . $col, '{{%mg_articolo}}', $col);
            }
            if (!$this->checkForeignKeyExist('mg_articolo', $fk)) {
                $this->addForeignKey($fk, '{{%mg_articolo}}', $col,
                    '{{%mg_attributo_articolo}}', 'id', 'NO ACTION', 'NO ACTION');
            }
        }
    }

    public function safeDown()
    {
        foreach (['fk-mg_articolo-marca' => 'id_marca', 'fk-mg_articolo-modello' => 'id_modello',
                  'fk-mg_articolo-taglia' => 'id_taglia', 'fk-mg_articolo-colore' => 'id_colore'] as $fk => $col) {
            if ($this->checkForeignKeyExist('mg_articolo', $fk)) {
                $this->dropForeignKey($fk, '{{%mg_articolo}}');
            }
            if ($this->checkColumnExist('mg_articolo', $col)) {
                $this->dropColumn('{{%mg_articolo}}', $col);
            }
        }
        if ($this->checkColumnExist('mg_articolo', 'guid')) {
            $this->execute("ALTER TABLE {{%mg_articolo}} DROP CONSTRAINT [DF_mg_articolo_guid]");
            $this->dropColumn('{{%mg_articolo}}', 'guid');
        }
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
