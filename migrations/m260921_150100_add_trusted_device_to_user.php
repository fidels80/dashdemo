<?php

use yii\db\Migration;

/**
 * Crea la tabella user_trusted_device per memorizzare i dispositivi fidati
 * che permettono di saltare la verifica 2FA per un periodo di tempo.
 */
class m260921_150100_add_trusted_device_to_user extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('user_trusted_device')) {
            $this->createTable('{{%user_trusted_device}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->notNull(),
                'token_hash' => $this->string(64)->notNull(),
                'expires_at' => $this->bigInteger()->notNull(),
                'user_agent' => $this->string(500)->null(),
                'created_at' => $this->bigInteger()->notNull(),
                'last_used_at' => $this->bigInteger()->null(),
            ]);

            $this->createIndex(
                'idx-user_trusted_device-token_hash',
                '{{%user_trusted_device}}',
                'token_hash',
                true
            );

            $this->createIndex(
                'idx-user_trusted_device-user_id',
                '{{%user_trusted_device}}',
                'user_id'
            );

            $this->addForeignKey(
                'fk-user_trusted_device-user_id',
                '{{%user_trusted_device}}',
                'user_id',
                '{{%user}}',
                'id',
                'CASCADE',
                'CASCADE'
            );
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('user_trusted_device')) {
            $this->dropForeignKey('fk-user_trusted_device-user_id', '{{%user_trusted_device}}');
            $this->dropTable('{{%user_trusted_device}}');
        }
    }

    private function checkTableExist($table)
    {
        $command = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        );
        $command->bindValues([':table' => $table]);
        return $command->queryScalar() > 0;
    }
}
