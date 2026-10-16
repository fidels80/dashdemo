<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Voce di menu e permesso per il catalogo FAQ del CRM.
 */
class m261016_120000_add_crm_faq_menu extends Migration
{
    public function safeUp()
    {
        if ($this->tableExists('dash_permesso')
            && !(new Query())->from('{{%dash_permesso}}')->where(['codice' => 'crmfaq'])->exists()) {
            $this->insert('{{%dash_permesso}}', [
                'codice' => 'crmfaq',
                'descrizione' => 'FAQ CRM',
                'gruppo' => 'CRM',
                'ordine' => 102,
                'attivo' => 1,
                'created_at' => new \yii\db\Expression('GETDATE()'),
            ]);
        }

        if ($this->tableExists('dash_menu')
            && !(new Query())->from('{{%dash_menu}}')->where(['codice' => 'crm-faq'])->exists()) {
            $crm = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'crm'])->scalar();
            $this->insert('{{%dash_menu}}', [
                'codice' => 'crm-faq',
                'label' => 'FAQ',
                'icona' => 'question-circle',
                'url' => 'crmfaq/index',
                'genitore_id' => $crm ?: null,
                'livello_min' => 0,
                'ordine' => 4,
                'per_tutti' => 0,
                'attivo' => 1,
                'created_at' => new \yii\db\Expression('GETDATE()'),
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'crm-faq']);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => 'crmfaq']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
