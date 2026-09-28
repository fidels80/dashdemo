<?php

use yii\db\Migration;

/**
 * Tabella del changelog applicativo (pagina di log riservata ai livelli elevati).
 *
 * Registra, per ogni miglioria rilasciata, cosa e' stato modificato e in che modo.
 * Le righe vengono create automaticamente dall'hook git post-commit
 * (comando `yii changelog/git`) e possono essere arricchite dall'agente
 * documentale con il dettaglio funzionale.
 */
class m260922_030000_create_dash_changelog extends Migration
{
    public function safeUp()
    {
        if ($this->checkTableExist('dash_changelog')) {
            return;
        }

        $this->createTable('{{%dash_changelog}}', [
            'id' => $this->primaryKey(),
            'versione' => $this->string(50)->null(),
            'commit_hash' => $this->string(64)->null(),
            'tipo' => $this->string(20)->null(),
            'titolo' => $this->string(300)->notNull(),
            'dettaglio' => $this->text()->null(),
            'file_modificati' => $this->text()->null(),
            'autore' => $this->string(150)->null(),
            'data_commit' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->null(),
        ]);

        $this->createIndex('idx-dash_changelog-commit', '{{%dash_changelog}}', 'commit_hash', true);
        $this->createIndex('idx-dash_changelog-data', '{{%dash_changelog}}', 'data_commit');
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_changelog')) {
            $this->dropTable('{{%dash_changelog}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
