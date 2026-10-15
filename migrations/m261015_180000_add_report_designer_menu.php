<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Report designer: permesso dedicato "reportdesigner" e voce di menu
 * "Report Designer" (assegnabile ai singoli operatori tramite dash_menu_utente;
 * visibile sempre ai supervisori di livello 100).
 */
class m261015_180000_add_report_designer_menu extends Migration
{
    public function safeUp()
    {
        $this->creaPermesso();
        $this->creaMenu();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'report-designer']);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => 'reportdesigner']);
        }
    }

    private function creaPermesso()
    {
        if (!$this->tableExists('dash_permesso')) {
            return;
        }
        if ((new Query())->from('{{%dash_permesso}}')->where(['codice' => 'reportdesigner'])->exists()) {
            return;
        }
        $this->insert('{{%dash_permesso}}', [
            'codice' => 'reportdesigner',
            'descrizione' => 'Report Designer',
            'gruppo' => 'Reportistica',
            'ordine' => 110,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function creaMenu()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }
        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'report-designer'])->exists()) {
            return;
        }
        // livello_min 0 + per_tutti 0: nascosta di default, assegnabile per
        // operatore tramite dash_menu_utente; i supervisori la vedono sempre.
        $this->insert('{{%dash_menu}}', [
            'codice' => 'report-designer',
            'label' => 'Report Designer',
            'icona' => 'tools',
            'url' => 'reportdesigner/index',
            'genitore_id' => null,
            'livello_min' => 0,
            'ordine' => 86,
            'per_tutti' => 0,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
