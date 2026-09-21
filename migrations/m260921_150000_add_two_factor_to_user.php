<?php

use yii\db\Migration;

/**
 * Aggiunge i campi necessari alla 2FA alla tabella user.
 */
class m260921_150000_add_two_factor_to_user extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('user', 'two_factor_secret')) {
            $this->addColumn('{{%user}}', 'two_factor_secret', $this->string(255)->null());
        }
        if (!$this->checkColumnExist('user', 'two_factor_enabled')) {
            $this->addColumn('{{%user}}', 'two_factor_enabled', $this->boolean()->defaultValue(false));
        }
        if (!$this->checkColumnExist('user', 'two_factor_verified_at')) {
            $this->addColumn('{{%user}}', 'two_factor_verified_at', $this->integer()->null());
        }
    }

    public function safeDown()
    {
        if ($this->checkColumnExist('user', 'two_factor_verified_at')) {
            $this->dropColumn('{{%user}}', 'two_factor_verified_at');
        }
        if ($this->checkColumnExist('user', 'two_factor_enabled')) {
            $this->dropColumn('{{%user}}', 'two_factor_enabled');
        }
        if ($this->checkColumnExist('user', 'two_factor_secret')) {
            $this->dropColumn('{{%user}}', 'two_factor_secret');
        }
    }

    private function checkColumnExist($table, $column)
    {
        $command = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :table AND COLUMN_NAME = :column"
        );
        $command->bindValues([':table' => $table, ':column' => $column]);
        return $command->queryScalar() > 0;
    }
}
