<?php

use yii\db\Migration;

/**
 * Aggiunge a to_do_main i campi per la gestione stile Jira:
 * tipo issue, story points, sprint, posizione nella board, reporter e timestamp.
 */
class m260921_160000_add_jira_fields_to_todo extends Migration
{
    public function safeUp()
    {
        $table = '{{%to_do_main}}';

        if (!$this->checkColumnExist('to_do_main', 'tipo')) {
            $this->addColumn($table, 'tipo', $this->integer()->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'story_points')) {
            $this->addColumn($table, 'story_points', $this->integer()->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'sprint_id')) {
            $this->addColumn($table, 'sprint_id', $this->integer()->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'posizione')) {
            $this->addColumn($table, 'posizione', $this->integer()->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'reporter')) {
            $this->addColumn($table, 'reporter', $this->string(30)->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'created_at')) {
            $this->addColumn($table, 'created_at', $this->dateTime()->null());
        }
        if (!$this->checkColumnExist('to_do_main', 'updated_at')) {
            $this->addColumn($table, 'updated_at', $this->dateTime()->null());
        }
    }

    public function safeDown()
    {
        $table = '{{%to_do_main}}';
        foreach (['tipo', 'story_points', 'sprint_id', 'posizione', 'reporter', 'created_at', 'updated_at'] as $col) {
            if ($this->checkColumnExist('to_do_main', $col)) {
                $this->dropColumn($table, $col);
            }
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
