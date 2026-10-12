<?php

use yii\db\Migration;

/**
 * Tabelle di stato della licenza e degli aggiornamenti dell'applicazione.
 *
 * - dash_licenza     : riga singola con lo stato corrente della licenza
 *                      (chiave, machine id, tier, feature, scadenze, cache).
 * - dash_licenza_log : storico degli eventi (attivazione, heartbeat, update).
 */
class m261012_100000_create_dash_licenza extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('dash_licenza')) {
            $this->createTable('{{%dash_licenza}}', [
                'id' => $this->primaryKey(),
                'license_key' => $this->string(64)->null(),
                'machine_id' => $this->string(64)->null(),
                'product' => $this->string(50)->null(),
                'tier' => $this->string(20)->null(),
                'features' => $this->text()->null(),
                'expires_at' => $this->dateTime()->null(),
                'status' => $this->string(20)->null(),
                'signature' => $this->string(255)->null(),
                'first_run_at' => $this->dateTime()->null(),
                'activated_at' => $this->dateTime()->null(),
                'last_check_at' => $this->dateTime()->null(),
                'last_check_ok' => $this->dateTime()->null(),
                'last_error' => $this->string(500)->null(),
                'update_version' => $this->string(20)->null(),
                'updated_at' => $this->dateTime()->null(),
            ]);
        }

        if (!$this->checkTableExist('dash_licenza_log')) {
            $this->createTable('{{%dash_licenza_log}}', [
                'id' => $this->primaryKey(),
                'event_type' => $this->string(50)->notNull(),
                'detail' => $this->string(1000)->null(),
                'created_at' => $this->dateTime()->null(),
            ]);
            $this->createIndex('idx-dash_licenza_log-created', '{{%dash_licenza_log}}', 'created_at');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_licenza_log')) {
            $this->dropTable('{{%dash_licenza_log}}');
        }
        if ($this->checkTableExist('dash_licenza')) {
            $this->dropTable('{{%dash_licenza}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
