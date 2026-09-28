<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_documento_riga il campo "tessuto" (attributo variante),
 * così le righe generate dalla matrice taglie riportano tessuto/colore/taglia.
 */
class m260922_020000_add_tessuto_to_righe_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_documento_riga', 'tessuto')) {
            $this->addColumn('{{%mg_documento_riga}}', 'tessuto', $this->string(50)->null());
        }
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('mg_documento_riga', 'tessuto')) {
            $this->dropColumn('{{%mg_documento_riga}}', 'tessuto');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
