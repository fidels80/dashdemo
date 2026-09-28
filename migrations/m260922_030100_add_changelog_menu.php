<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu laterale (sotto "Sicurezza") la voce "Changelog",
 * visibile esclusivamente agli operatori di livello elevato (livello >= 100).
 */
class m260922_030100_add_changelog_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'sic-changelog'])->exists()) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'sicurezza'])->scalar();

        $this->insert('{{%dash_menu}}', [
            'codice' => 'sic-changelog',
            'label' => 'Changelog',
            'icona' => 'clipboard-list',
            'url' => 'dashchangelog/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 100,
            'ordine' => 10,
            'per_tutti' => 0,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'sic-changelog']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
