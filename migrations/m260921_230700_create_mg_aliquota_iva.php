<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tabella delle aliquote IVA (codice, descrizione, percentuale).
 */
class m260921_230700_create_mg_aliquota_iva extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_aliquota_iva')) {
            $this->createTable('{{%mg_aliquota_iva}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(20)->notNull(),
                'descrizione' => $this->string(100)->notNull(),
                'percentuale' => $this->decimal(9, 2)->notNull()->defaultValue(0),
                'attivo' => $this->boolean()->notNull()->defaultValue(1),
            ]);
            $this->createIndex('idx-mg_aliquota_iva-codice', '{{%mg_aliquota_iva}}', 'codice', true);
        }

        if ($this->checkTableExist('mg_aliquota_iva')) {
            $count = (new Query())->from('{{%mg_aliquota_iva}}')->count();
            if ((int) $count === 0) {
                $this->batchInsert('{{%mg_aliquota_iva}}', ['codice', 'descrizione', 'percentuale', 'attivo'], [
                    ['22', 'IVA 22%', 22.00, 1],
                    ['10', 'IVA 10%', 10.00, 1],
                    ['05', 'IVA 5%', 5.00, 1],
                    ['04', 'IVA 4%', 4.00, 1],
                    ['00', 'Esente / Non imponibile', 0.00, 1],
                ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_aliquota_iva')) {
            $this->dropTable('{{%mg_aliquota_iva}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
