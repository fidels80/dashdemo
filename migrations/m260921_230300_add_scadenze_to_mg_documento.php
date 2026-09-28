<?php

use yii\db\Migration;

/**
 * Scadenze documento:
 * - mg_documento: flag crea_scadenze + metodo di pagamento (proposto dall'anagrafica,
 *   modificabile sul singolo documento)
 * - mg_scadenza: rate generate dal metodo di pagamento (data + importo)
 */
class m260921_230300_add_scadenze_to_mg_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento', 'crea_scadenze')) {
            $this->addColumn('{{%mg_documento}}', 'crea_scadenze',
                $this->boolean()->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_documento', 'id_metodo_pagamento')) {
            $this->addColumn('{{%mg_documento}}', 'id_metodo_pagamento', $this->integer()->null());
            $this->createIndex('idx-mg_documento-metodo', '{{%mg_documento}}', 'id_metodo_pagamento');
        }
        if (!$this->checkForeignKeyExist('mg_documento', 'fk-mg_documento-metodo')) {
            $this->addForeignKey('fk-mg_documento-metodo', '{{%mg_documento}}', 'id_metodo_pagamento',
                '{{%mg_metodo_pagamento}}', 'id', 'NO ACTION', 'NO ACTION');
        }

        if (!$this->checkTableExist('mg_scadenza')) {
            $this->createTable('{{%mg_scadenza}}', [
                'id' => $this->primaryKey(),
                'id_documento' => $this->integer()->notNull(),
                'id_metodo_pagamento' => $this->integer()->null(),
                'progressivo' => $this->integer()->notNull()->defaultValue(1),
                'data_scadenza' => $this->date()->notNull(),
                'percentuale' => $this->decimal(9, 4)->notNull()->defaultValue(0),
                'importo' => $this->decimal(18, 2)->notNull()->defaultValue(0),
                'stato' => $this->string(20)->notNull()->defaultValue('aperta'),
                'created_at' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-mg_scadenza-doc', '{{%mg_scadenza}}', 'id_documento');
            $this->createIndex('idx-mg_scadenza-unico', '{{%mg_scadenza}}', ['id_documento', 'progressivo'], true);

            $this->addForeignKey('fk-mg_scadenza-doc', '{{%mg_scadenza}}', 'id_documento',
                '{{%mg_documento}}', 'id', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk-mg_scadenza-metodo', '{{%mg_scadenza}}', 'id_metodo_pagamento',
                '{{%mg_metodo_pagamento}}', 'id', 'NO ACTION', 'NO ACTION');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_scadenza')) {
            $this->dropForeignKey('fk-mg_scadenza-doc', '{{%mg_scadenza}}');
            $this->dropForeignKey('fk-mg_scadenza-metodo', '{{%mg_scadenza}}');
            $this->dropTable('{{%mg_scadenza}}');
        }
        if ($this->checkForeignKeyExist('mg_documento', 'fk-mg_documento-metodo')) {
            $this->dropForeignKey('fk-mg_documento-metodo', '{{%mg_documento}}');
        }
        if ($this->checkColumnExist('mg_documento', 'id_metodo_pagamento')) {
            $this->dropColumn('{{%mg_documento}}', 'id_metodo_pagamento');
        }
        if ($this->checkColumnExist('mg_documento', 'crea_scadenze')) {
            $this->execute(
                "IF EXISTS (SELECT 1 FROM sys.default_constraints WHERE name = 'DF_mg_documento_crea_scadenze')
                     ALTER TABLE [dbo].[mg_documento] DROP CONSTRAINT [DF_mg_documento_crea_scadenze]"
            );
            $this->dropColumn('{{%mg_documento}}', 'crea_scadenze');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
