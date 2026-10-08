<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge la sottocommessa alla testata documento (mg_documento) e alle
 * righe documento (mg_documento_riga). La sottocommessa scelta in testata
 * viene proposta alle nuove righe, con modifica manuale per riga.
 *
 * Le foreign key sono NO ACTION: SQL Server rifiuta più percorsi di
 * propagazione (mg_sottocommessa -> mg_documento -> mg_documento_riga con
 * CASCADE, più i percorsi diretti). L'integrità è garantita dai controlli
 * applicativi prima della cancellazione.
 */
class m261008_100000_add_sottocommessa_to_documenti extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_sottocommessa')) {
            return;
        }

        if (!$this->checkColumnExist('mg_documento', 'id_sottocommessa')) {
            $this->addColumn('{{%mg_documento}}', 'id_sottocommessa', $this->integer()->null());
        }
        if (!$this->checkIndexExist('mg_documento', 'idx-mg_documento-sottocommessa')) {
            $this->createIndex('idx-mg_documento-sottocommessa', '{{%mg_documento}}', 'id_sottocommessa');
        }
        if ($this->checkForeignKeyExist('mg_documento', 'fk-mg_documento-sottocommessa')) {
            $this->dropForeignKey('fk-mg_documento-sottocommessa', '{{%mg_documento}}');
        }
        $this->addForeignKey('fk-mg_documento-sottocommessa', '{{%mg_documento}}', 'id_sottocommessa',
            '{{%mg_sottocommessa}}', 'id', 'NO ACTION', 'NO ACTION');

        if (!$this->checkColumnExist('mg_documento_riga', 'id_sottocommessa')) {
            $this->addColumn('{{%mg_documento_riga}}', 'id_sottocommessa', $this->integer()->null());
        }
        if (!$this->checkIndexExist('mg_documento_riga', 'idx-mg_documento_riga-sottocommessa')) {
            $this->createIndex('idx-mg_documento_riga-sottocommessa', '{{%mg_documento_riga}}', 'id_sottocommessa');
        }
        if ($this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-sottocommessa')) {
            $this->dropForeignKey('fk-mg_documento_riga-sottocommessa', '{{%mg_documento_riga}}');
        }
        $this->addForeignKey('fk-mg_documento_riga-sottocommessa', '{{%mg_documento_riga}}', 'id_sottocommessa',
            '{{%mg_sottocommessa}}', 'id', 'NO ACTION', 'NO ACTION');

        $this->aggiungiRegoleApi();
    }

    public function safeDown()
    {
        $this->rimuoviRegoleApi();

        if ($this->checkForeignKeyExist('mg_documento_riga', 'fk-mg_documento_riga-sottocommessa')) {
            $this->dropForeignKey('fk-mg_documento_riga-sottocommessa', '{{%mg_documento_riga}}');
        }
        if ($this->checkColumnExist('mg_documento_riga', 'id_sottocommessa')) {
            $this->dropColumn('{{%mg_documento_riga}}', 'id_sottocommessa');
        }
        if ($this->checkForeignKeyExist('mg_documento', 'fk-mg_documento-sottocommessa')) {
            $this->dropForeignKey('fk-mg_documento-sottocommessa', '{{%mg_documento}}');
        }
        if ($this->checkColumnExist('mg_documento', 'id_sottocommessa')) {
            $this->dropColumn('{{%mg_documento}}', 'id_sottocommessa');
        }
    }

    /**
     * Regole di integrità referenziale per l'API: una sottocommessa usata in
     * documenti o righe documento non è cancellabile.
     */
    private function aggiungiRegoleApi()
    {
        if (!$this->checkTableExist('dash_api_rel')) {
            return;
        }

        $regole = [
            ['sottocommesse', 'riferimento', 'mg_documento', 'id_sottocommessa', 'documenti con questa sottocommessa', 10],
            ['sottocommesse', 'riferimento', 'mg_documento_riga', 'id_sottocommessa', 'righe documento con questa sottocommessa', 20],
        ];

        foreach ($regole as $regola) {
            $esiste = (new Query())
                ->from('{{%dash_api_rel}}')
                ->where([
                    'entita' => $regola[0],
                    'tipo' => $regola[1],
                    'tabella' => $regola[2],
                    'colonna' => $regola[3],
                ])
                ->exists();
            if (!$esiste) {
                $this->insert('{{%dash_api_rel}}', [
                    'entita' => $regola[0],
                    'tipo' => $regola[1],
                    'tabella' => $regola[2],
                    'colonna' => $regola[3],
                    'etichetta' => $regola[4],
                    'cascade' => 0,
                    'attiva' => 1,
                    'ordinamento' => $regola[5],
                ]);
            }
        }
    }

    private function rimuoviRegoleApi()
    {
        if (!$this->checkTableExist('dash_api_rel')) {
            return;
        }
        foreach (['mg_documento', 'mg_documento_riga'] as $tabella) {
            $this->delete('{{%dash_api_rel}}', [
                'entita' => 'sottocommesse',
                'tipo' => 'riferimento',
                'tabella' => $tabella,
                'colonna' => 'id_sottocommessa',
            ]);
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        )->bindValue(':table', $table)->queryScalar() > 0;
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }

    private function checkIndexExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM sys.indexes WHERE object_id = OBJECT_ID(:t) AND name = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }

    private function checkForeignKeyExist($table, $name)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = :t AND CONSTRAINT_NAME = :n"
        )->bindValue(':t', $table)->bindValue(':n', $name)->queryScalar() > 0;
    }
}
