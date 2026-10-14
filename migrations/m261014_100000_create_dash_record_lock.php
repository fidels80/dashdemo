<?php

use yii\db\Migration;

/**
 * Tabella dei blocchi temporanei dei record in modifica.
 *
 * Una riga per (tabella, id_record): indica quale utente sta modificando
 * quel record e fino a quando il blocco e' valido (scadenza). Serve a
 * impedire che due utenti modifichino contemporaneamente lo stesso record:
 * il secondo utente viene reindirizzato alla vista in sola lettura.
 */
class m261014_100000_create_dash_record_lock extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('dash_record_lock')) {
            $this->createTable('{{%dash_record_lock}}', [
                'id' => $this->primaryKey(),
                'tabella' => $this->string(128)->notNull(),
                'id_record' => $this->string(100)->notNull(),
                'utente' => $this->string(125)->notNull(),
                'utente_id' => $this->integer()->null(),
                'scadenza' => $this->dateTime()->notNull(),
                'creato_il' => $this->dateTime()->notNull(),
            ]);
            $this->createIndex('uidx-dash_record_lock-record', '{{%dash_record_lock}}', ['tabella', 'id_record'], true);
            $this->createIndex('idx-dash_record_lock-scadenza', '{{%dash_record_lock}}', 'scadenza');
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_record_lock')) {
            $this->dropTable('{{%dash_record_lock}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
