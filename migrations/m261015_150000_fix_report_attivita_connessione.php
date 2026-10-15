<?php

use yii\db\Migration;

/**
 * Corregge la connessione della fonte cross-db di esempio: i login "sa"
 * (db2/db3/db4) non sono piu' utilizzabili, mentre l'utente "yii" usato da
 * "db" e "db5" ha accesso ai vari database della stessa istanza SQL Server.
 */
class m261015_150000_fix_report_attivita_connessione extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_report_fonte')) {
            return;
        }
        $this->update('{{%dash_report_fonte}}',
            ['connessione' => 'db', 'updated_at' => new \yii\db\Expression('GETDATE()')],
            ['codice' => 'flt_attivita']
        );
    }

    public function safeDown()
    {
        if (!$this->tableExists('dash_report_fonte')) {
            return;
        }
        $this->update('{{%dash_report_fonte}}',
            ['connessione' => 'db3'],
            ['codice' => 'flt_attivita']
        );
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
