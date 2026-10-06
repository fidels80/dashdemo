<?php

use yii\db\Migration;

/**
 * Flag di evasione del rapportino: quando viene prelevato in un documento
 * non compare più tra quelli disponibili.
 */
class m261001_100100_add_evaso_to_rapportini extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('rapportini', 'evaso')) {
            $this->addColumn('{{%rapportini}}', 'evaso', $this->boolean()->notNull()->defaultValue(0));
        }
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('rapportini', 'evaso')) {
            $this->dropColumn('{{%rapportini}}', 'evaso');
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
