<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) le voci "Lotti" e "Matricole".
 */
class m261009_180000_add_lotti_matricole_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();

        $voci = [
            ['codice' => 'micro-lotti', 'label' => 'Lotti', 'icona' => 'boxes', 'url' => 'mglotto/index', 'ordine' => 17],
            ['codice' => 'micro-matricole', 'label' => 'Matricole', 'icona' => 'barcode', 'url' => 'mgmatricola/index', 'ordine' => 18],
        ];

        foreach ($voci as $voce) {
            if ((new Query())->from('{{%dash_menu}}')->where(['codice' => $voce['codice']])->exists()) {
                continue;
            }
            $this->insert('{{%dash_menu}}', [
                'codice' => $voce['codice'],
                'label' => $voce['label'],
                'icona' => $voce['icona'],
                'url' => $voce['url'],
                'genitore_id' => $parent ?: null,
                'livello_min' => 0,
                'ordine' => $voce['ordine'],
                'per_tutti' => 1,
                'attivo' => 1,
                'created_at' => new \yii\db\Expression('GETDATE()'),
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-lotti']);
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-matricole']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
