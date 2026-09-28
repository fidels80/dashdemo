<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu laterale la sezione "Vtiger",
 * visibile agli operatori con livello >= 70.
 */
class m260923_000100_add_vtiger_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'vtiger'])->exists()) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'vtiger',
            'label' => 'Vtiger',
            'icona' => 'chart-line',
            'url' => 'vtiger/search',
            'genitore_id' => null,
            'livello_min' => 70,
            'ordine' => 25,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'vtiger']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}