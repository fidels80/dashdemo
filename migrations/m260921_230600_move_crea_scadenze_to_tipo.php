<?php

use yii\db\Migration;

/**
 * Sposta il flag "crea scadenze" dal documento al tipo documento.
 * - aggiunge mg_tipo_documento.crea_scadenze
 * - propaga ai tipi l'eventuale flag già attivo sui documenti
 * - rimuove mg_documento.crea_scadenze
 */
class m260921_230600_move_crea_scadenze_to_tipo extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'crea_scadenze')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'crea_scadenze',
                $this->boolean()->notNull()->defaultValue(0));
        }

        if ($this->checkColumnExist('mg_documento', 'crea_scadenze')) {
            $this->execute(
                "UPDATE t SET t.crea_scadenze = 1
                 FROM {{%mg_tipo_documento}} t
                 WHERE EXISTS (SELECT 1 FROM {{%mg_documento}} d
                               WHERE d.id_tipo = t.id AND d.crea_scadenze = 1)"
            );
            $this->dropDefaultConstraint('mg_documento', 'DF_mg_documento_crea_scadenze');
            $this->dropColumn('{{%mg_documento}}', 'crea_scadenze');
        }
    }

    public function safeDown()
    {
        if (!$this->checkColumnExist('mg_documento', 'crea_scadenze')) {
            $this->addColumn('{{%mg_documento}}', 'crea_scadenze',
                $this->boolean()->notNull()->defaultValue(0));
        }
        if ($this->checkColumnExist('mg_tipo_documento', 'crea_scadenze')) {
            $this->dropDefaultConstraint('mg_tipo_documento', 'DF_mg_tipo_documento_crea_scadenze');
            $this->dropColumn('{{%mg_tipo_documento}}', 'crea_scadenze');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    /**
     * Rimuove un default constraint se presente (evita errori sul DROP COLUMN in SQL Server).
     */
    private function dropDefaultConstraint($table, $constraintName)
    {
        $this->execute(
            "IF EXISTS (SELECT 1 FROM sys.default_constraints WHERE name = :n)
                 ALTER TABLE [dbo].[$table] DROP CONSTRAINT [$constraintName]",
            [':n' => $constraintName]
        );
    }
}
