<?php

use yii\db\Migration;

/**
 * Crea la tabella api_token per l'autenticazione Bearer del servizio REST.
 */
class m260921_180000_create_api_token extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('api_token')) {
            $this->createTable('{{%api_token}}', [
                'id' => $this->primaryKey(),
                'user_id' => $this->integer()->null(),
                'descrizione' => $this->string(200)->null(),
                'token_hash' => $this->string(64)->notNull(),
                'scopes' => $this->string(500)->null(),
                'expires_at' => $this->bigInteger()->null(),
                'created_at' => $this->bigInteger()->notNull(),
                'last_used_at' => $this->bigInteger()->null(),
            ]);

            $this->createIndex('idx-api_token-token_hash', '{{%api_token}}', 'token_hash', true);
            $this->createIndex('idx-api_token-user_id', '{{%api_token}}', 'user_id');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('api_token')) {
            $this->dropTable('{{%api_token}}');
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
