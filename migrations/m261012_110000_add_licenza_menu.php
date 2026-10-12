<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu la voce "Licenza e aggiornamenti" (solo supervisori).
 */
class m261012_110000_add_licenza_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'sistema-licenza'])->exists()) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'sistema-licenza',
            'label' => 'Licenza e aggiornamenti',
            'icona' => 'key',
            'url' => 'licenza/index',
            'genitore_id' => null,
            'livello_min' => 100,
            'ordine' => 90,
            'per_tutti' => 0,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'sistema-licenza']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
