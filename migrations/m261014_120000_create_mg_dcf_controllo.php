<?php

use yii\db\Migration;

/**
 * Tabelle del controllo (riscontro) dei documenti di trasporto a fornitore (DCF).
 *
 * - mg_dcf_controllo: testata del controllo, una riga per documento (stato,
 *   totali, esito email).
 * - mg_dcf_controllo_riga: quantita' riscontrata per ogni riga del documento.
 */
class m261014_120000_create_mg_dcf_controllo extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_dcf_controllo')) {
            $this->createTable('{{%mg_dcf_controllo}}', [
                'id' => $this->primaryKey(),
                'id_documento' => $this->integer()->notNull(),
                'stato' => $this->string(20)->notNull()->defaultValue('da_controllare'),
                'totale_pezzi_documento' => $this->decimal(18, 4)->notNull()->defaultValue(0),
                'totale_documento' => $this->decimal(18, 2)->notNull()->defaultValue(0),
                'totale_pezzi_riscontrati' => $this->decimal(18, 4)->notNull()->defaultValue(0),
                'righe_totali' => $this->integer()->notNull()->defaultValue(0),
                'righe_riscontrate' => $this->integer()->notNull()->defaultValue(0),
                'righe_differenza' => $this->integer()->notNull()->defaultValue(0),
                'email_inviata' => $this->boolean()->notNull()->defaultValue(0),
                'email_destinatari' => $this->string(400)->null(),
                'email_oggetto' => $this->string(200)->null(),
                'email_inviata_at' => $this->dateTime()->null(),
                'id_utente' => $this->integer()->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('uidx-mg_dcf_controllo-documento', '{{%mg_dcf_controllo}}', 'id_documento', true);
        }

        if (!$this->checkTableExist('mg_dcf_controllo_riga')) {
            $this->createTable('{{%mg_dcf_controllo_riga}}', [
                'id' => $this->primaryKey(),
                'id_controllo' => $this->integer()->notNull(),
                'id_riga' => $this->integer()->notNull(),
                'qta_documento' => $this->decimal(18, 4)->notNull()->defaultValue(0),
                'qta_riscontrata' => $this->decimal(18, 4)->null(),
                'differenza' => $this->decimal(18, 4)->notNull()->defaultValue(0),
                'note' => $this->string(500)->null(),
                'created_at' => $this->dateTime()->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-mg_dcf_controllo_riga-controllo', '{{%mg_dcf_controllo_riga}}', 'id_controllo');
            $this->createIndex('idx-mg_dcf_controllo_riga-riga', '{{%mg_dcf_controllo_riga}}', 'id_riga');
            $this->addForeignKey(
                'fk-mg_dcf_controllo_riga-controllo',
                '{{%mg_dcf_controllo_riga}}',
                'id_controllo',
                '{{%mg_dcf_controllo}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_dcf_controllo_riga')) {
            $this->dropTable('{{%mg_dcf_controllo_riga}}');
        }
        if ($this->checkTableExist('mg_dcf_controllo')) {
            $this->dropTable('{{%mg_dcf_controllo}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
