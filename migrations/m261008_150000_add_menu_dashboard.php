<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu laterale la voce radice "Dashboard" (cruscotto di
 * riepilogo), visibile a tutti e posizionata in cima al menu.
 * Apre dashboard/index, che e' anche la pagina di destinazione dopo il login.
 */
class m261008_150000_add_menu_dashboard extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $esiste = (new Query())->from('{{%dash_menu}}')->where(['codice' => 'dashboard'])->exists();
        if ($esiste) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'dashboard',
            'label' => 'Dashboard',
            'icona' => 'tachometer-alt',
            'url' => 'dashboard/index',
            'genitore_id' => null,
            'livello_min' => 0,
            'ordine' => 0,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    public function safeDown()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $this->delete('{{%dash_menu}}', ['codice' => 'dashboard']);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
