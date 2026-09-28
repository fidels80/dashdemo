<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) le voci per i pagamenti:
 * metodi di pagamento e tipi di pagamento.
 */
class m260921_230400_add_pagamento_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        $now = new \yii\db\Expression('GETDATE()');

        $items = [
            ['micro-metodipag', 'Metodi pagamento', 'credit-card', 'mgmetodopagamento/index', 5],
            ['micro-tipipag', 'Tipi pagamento', 'money-bill', 'mgtipopagamento/index', 6],
        ];

        foreach ($items as $it) {
            if ((new Query())->from('{{%dash_menu}}')->where(['codice' => $it[0]])->exists()) {
                continue;
            }
            $this->insert('{{%dash_menu}}', [
                'codice' => $it[0],
                'label' => $it[1],
                'icona' => $it[2],
                'url' => $it[3],
                'genitore_id' => $parent ?: null,
                'livello_min' => 0,
                'ordine' => $it[4],
                'per_tutti' => 1,
                'attivo' => 1,
                'created_at' => $now,
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => ['micro-metodipag', 'micro-tipipag']]);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
