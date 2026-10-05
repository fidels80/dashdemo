<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) la voce "Contatti".
 */
class m261001_120300_add_contatti_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-contatti'])->exists()) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'micro-contatti',
            'label' => 'Contatti',
            'icona' => 'address-card',
            'url' => 'mgcontatto/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 0,
            'ordine' => 15,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-contatti']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
