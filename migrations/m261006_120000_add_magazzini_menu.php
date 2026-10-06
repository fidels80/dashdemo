<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) la voce "Magazzini".
 */
class m261006_120000_add_magazzini_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-magazzini'])->exists()) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'micro-magazzini',
            'label' => 'Magazzini',
            'icona' => 'warehouse',
            'url' => 'mgmagazzino/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 0,
            'ordine' => 16,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-magazzini']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
