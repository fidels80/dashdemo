<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_aliquota_iva la natura IVA SDI (N1..N7) usata nella
 * fattura elettronica per le righe con aliquota a zero.
 */
class m261009_200600_add_fe_to_mg_aliquota_iva extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_aliquota_iva', 'fe_natura')) {
            $this->addColumn('{{%mg_aliquota_iva}}', 'fe_natura', $this->string(4)->null());
        }

        // Aliquota a zero: natura "esente" di default (personalizzabile)
        $this->execute("UPDATE {{%mg_aliquota_iva}} SET fe_natura='N4'
            WHERE percentuale = 0 AND (fe_natura IS NULL OR fe_natura='')");
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_aliquota_iva', 'fe_natura')) {
            $this->dropColumn('{{%mg_aliquota_iva}}', 'fe_natura');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
