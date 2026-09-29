<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Commesse e sottocommesse del microgestionale:
 * - mg_commessa       (codice, descrizione, date inizio/fine, anagrafica opzionale)
 * - mg_sottocommessa  (stessa struttura + commessa padre obbligatoria)
 *
 * La colonna rapportini.cd_cli viene allargata: era CHAR(7) dimensionata sui
 * vecchi codici anacli, mentre mg_anagrafica.codice arriva a 20 caratteri.
 */
class m260930_100000_create_mg_commesse extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_commessa')) {
            $this->createTable('{{%mg_commessa}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(200)->notNull(),
                'data_inizio' => $this->date()->null(),
                'data_fine' => $this->date()->null(),
                'id_anagrafica' => $this->integer()->null(),
                'attivo' => $this->boolean()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_commessa-codice', '{{%mg_commessa}}', 'codice', true);
            $this->createIndex('idx-mg_commessa-anagrafica', '{{%mg_commessa}}', 'id_anagrafica');
            if ($this->checkTableExist('mg_anagrafica')) {
                // NO ACTION: SQL Server rifiuta più percorsi di propagazione
                // (anagrafica -> commessa -> sottocommessa e anagrafica -> sottocommessa).
                $this->addForeignKey('fk-mg_commessa-anagrafica', '{{%mg_commessa}}', 'id_anagrafica',
                    '{{%mg_anagrafica}}', 'id', 'NO ACTION', 'NO ACTION');
            }
        }

        if (!$this->checkTableExist('mg_sottocommessa')) {
            $this->createTable('{{%mg_sottocommessa}}', [
                'id' => $this->primaryKey(),
                'id_commessa' => $this->integer()->notNull(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(200)->notNull(),
                'data_inizio' => $this->date()->null(),
                'data_fine' => $this->date()->null(),
                'id_anagrafica' => $this->integer()->null(),
                'attivo' => $this->boolean()->defaultValue(1),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_sottocommessa-codice', '{{%mg_sottocommessa}}', 'codice', true);
            $this->createIndex('idx-mg_sottocommessa-commessa', '{{%mg_sottocommessa}}', 'id_commessa');
            $this->createIndex('idx-mg_sottocommessa-anagrafica', '{{%mg_sottocommessa}}', 'id_anagrafica');
            $this->addForeignKey('fk-mg_sottocommessa-commessa', '{{%mg_sottocommessa}}', 'id_commessa',
                '{{%mg_commessa}}', 'id', 'CASCADE', 'CASCADE');
            if ($this->checkTableExist('mg_anagrafica')) {
                $this->addForeignKey('fk-mg_sottocommessa-anagrafica', '{{%mg_sottocommessa}}', 'id_anagrafica',
                    '{{%mg_anagrafica}}', 'id', 'NO ACTION', 'NO ACTION');
            }
        }

        $this->allargaRapportiniCdCli();
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_sottocommessa')) {
            $this->dropForeignKey('fk-mg_sottocommessa-commessa', '{{%mg_sottocommessa}}');
            if ($this->checkTableExist('mg_anagrafica')) {
                $this->dropForeignKey('fk-mg_sottocommessa-anagrafica', '{{%mg_sottocommessa}}');
            }
            $this->dropTable('{{%mg_sottocommessa}}');
        }
        if ($this->checkTableExist('mg_commessa')) {
            if ($this->checkTableExist('mg_anagrafica')) {
                $this->dropForeignKey('fk-mg_commessa-anagrafica', '{{%mg_commessa}}');
            }
            $this->dropTable('{{%mg_commessa}}');
        }
    }

    /**
     * CHAR(7) -> NVARCHAR(20), per contenere i codici anagrafica del microgestionale.
     */
    private function allargaRapportiniCdCli()
    {
        if (!$this->checkTableExist('rapportini')) {
            return;
        }
        $tipo = Yii::$app->db->createCommand(
            "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'rapportini' AND COLUMN_NAME = 'cd_cli'"
        )->queryScalar();
        if (strtoupper((string) $tipo) !== 'CHAR') {
            return;
        }
        Yii::$app->db->createCommand("ALTER TABLE rapportini ALTER COLUMN cd_cli NVARCHAR(20) NOT NULL")->execute();
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        )->bindValue(':table', $table)->queryScalar() > 0;
    }
}
