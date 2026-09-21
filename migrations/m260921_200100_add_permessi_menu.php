<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge la voce di menu "Gestione Permessi" sotto "Sicurezza".
 */
class m260921_200100_add_permessi_menu extends Migration
{
    public function safeUp()
    {
        $tableExists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'dash_menu'"
        )->queryScalar() > 0;

        if (!$tableExists) {
            return;
        }

        $exists = (new Query())->from('{{%dash_menu}}')->where(['codice' => 'sic-perm'])->exists();
        if ($exists) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'sicurezza'])->scalar();

        $this->insert('{{%dash_menu}}', [
            'codice' => 'sic-perm',
            'label' => 'Gestione Permessi',
            'icona' => 'user-shield',
            'url' => 'dashpermesso/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 0,
            'ordine' => 4,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        $tableExists = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'dash_menu'"
        )->queryScalar() > 0;
        if ($tableExists) {
            $this->delete('{{%dash_menu}}', ['codice' => 'sic-perm']);
        }
    }
}
