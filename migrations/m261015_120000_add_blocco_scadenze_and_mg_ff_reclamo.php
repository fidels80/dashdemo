<?php

use yii\db\Migration;

/**
 * Fatture ricevute da fornitori (FAF):
 * - mg_scadenza: colonna bloccata (0/1) + tracciamento del blocco, per
 *   sospendere le scadenze di una fattura contestata.
 * - mg_ff_reclamo: storico dei reclami inviati per una fattura (piu' reclami
 *   per lo stesso documento).
 */
class m261015_120000_add_blocco_scadenze_and_mg_ff_reclamo extends Migration
{
    public function safeUp()
    {
        if ($this->checkTableExist('mg_scadenza')) {
            if (!$this->checkColumnExist('mg_scadenza', 'bloccata')) {
                $this->addColumn('{{%mg_scadenza}}', 'bloccata',
                    $this->boolean()->notNull()->defaultValue(0));
            }
            if (!$this->checkColumnExist('mg_scadenza', 'bloccata_at')) {
                $this->addColumn('{{%mg_scadenza}}', 'bloccata_at', $this->dateTime()->null());
            }
            if (!$this->checkColumnExist('mg_scadenza', 'bloccata_by')) {
                $this->addColumn('{{%mg_scadenza}}', 'bloccata_by', $this->integer()->null());
            }
        }

        if (!$this->checkTableExist('mg_ff_reclamo')) {
            $this->createTable('{{%mg_ff_reclamo}}', [
                'id' => $this->primaryKey(),
                'id_documento' => $this->integer()->notNull(),
                'id_utente' => $this->integer()->null(),
                'oggetto' => $this->string(200)->notNull(),
                'messaggio' => $this->text()->null(),
                'destinatari' => $this->string(400)->notNull(),
                'esito' => $this->boolean()->notNull()->defaultValue(0),
                'errore' => $this->string(500)->null(),
                'inviata_at' => $this->dateTime()->null(),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_ff_reclamo-documento', '{{%mg_ff_reclamo}}', 'id_documento');
            $this->addForeignKey('fk-mg_ff_reclamo-documento', '{{%mg_ff_reclamo}}', 'id_documento',
                '{{%mg_documento}}', 'id', 'CASCADE', 'CASCADE');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_ff_reclamo')) {
            $this->dropForeignKey('fk-mg_ff_reclamo-documento', '{{%mg_ff_reclamo}}');
            $this->dropTable('{{%mg_ff_reclamo}}');
        }

        if ($this->checkTableExist('mg_scadenza')) {
            foreach (['bloccata_by', 'bloccata_at', 'bloccata'] as $col) {
                if ($this->checkColumnExist('mg_scadenza', $col)) {
                    $this->execute(
                        "IF EXISTS (SELECT 1 FROM sys.default_constraints WHERE name = 'DF_mg_scadenza_" . $col . "')
                             ALTER TABLE [dbo].[mg_scadenza] DROP CONSTRAINT [DF_mg_scadenza_" . $col . "]"
                    );
                    $this->dropColumn('{{%mg_scadenza}}', $col);
                }
            }
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
}
