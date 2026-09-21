<?php

use yii\db\Migration;

/**
 * Crea le tabelle di supporto per il ToDo stile Jira:
 * - to_do_tipo      (tipi issue: Task, Bug, Story, Epic, Subtask)
 * - to_do_sprint    (sprint / iterazioni)
 * - to_do_attivita  (cronologia delle modifiche)
 */
class m260921_160100_create_todo_jira_tables extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('to_do_tipo')) {
            $this->createTable('{{%to_do_tipo}}', [
                'id' => $this->primaryKey(),
                'tipo' => $this->string(50)->notNull(),
                'icona' => $this->string(50)->null(),
                'colore' => $this->string(20)->null(),
                'ordine' => $this->integer()->null(),
            ]);

            $this->batchInsert('{{%to_do_tipo}}', ['tipo', 'icona', 'colore', 'ordine'], [
                ['Task', 'fa-tasks', '#0d6efd', 1],
                ['Bug', 'fa-bug', '#dc3545', 2],
                ['Story', 'fa-bookmark', '#198754', 3],
                ['Epic', 'fa-bolt', '#6f42c1', 4],
                ['Subtask', 'fa-sitemap', '#6c757d', 5],
            ]);
        }

        if (!$this->checkTableExist('to_do_sprint')) {
            $this->createTable('{{%to_do_sprint}}', [
                'id' => $this->primaryKey(),
                'nome' => $this->string(100)->notNull(),
                'obiettivo' => $this->string(500)->null(),
                'data_inizio' => $this->date()->null(),
                'data_fine' => $this->date()->null(),
                'stato' => $this->string(20)->null()->defaultValue('pianificato'),
                'created_at' => $this->dateTime()->null(),
            ]);
        }

        if (!$this->checkTableExist('to_do_attivita')) {
            $this->createTable('{{%to_do_attivita}}', [
                'id' => $this->primaryKey(),
                'id_todo' => $this->string(50)->notNull(),
                'user' => $this->string(50)->null(),
                'azione' => $this->string(50)->null(),
                'campo' => $this->string(50)->null(),
                'valore_prima' => $this->string(500)->null(),
                'valore_dopo' => $this->string(500)->null(),
                'data' => $this->dateTime()->null(),
            ]);

            $this->createIndex('idx-to_do_attivita-id_todo', '{{%to_do_attivita}}', 'id_todo');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('to_do_attivita')) {
            $this->dropTable('{{%to_do_attivita}}');
        }
        if ($this->checkTableExist('to_do_sprint')) {
            $this->dropTable('{{%to_do_sprint}}');
        }
        if ($this->checkTableExist('to_do_tipo')) {
            $this->dropTable('{{%to_do_tipo}}');
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
