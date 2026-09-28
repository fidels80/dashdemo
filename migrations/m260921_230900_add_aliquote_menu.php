<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) la voce Aliquote IVA.
 */
class m260921_230900_add_aliquote_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        $now = new \yii\db\Expression('GETDATE()');

        if (!(new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-aliquote'])->exists()) {
            $this->insert('{{%dash_menu}}', [
                'codice' => 'micro-aliquote',
                'label' => 'Aliquote IVA',
                'icona' => 'percent',
                'url' => 'mgaliquotaiva/index',
                'genitore_id' => $parent ?: null,
                'livello_min' => 0,
                'ordine' => 7,
                'per_tutti' => 1,
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-aliquote']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
