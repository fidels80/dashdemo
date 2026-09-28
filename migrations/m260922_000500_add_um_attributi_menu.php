<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) le voci:
 * - Unità di misura
 * - Attributi articolo (marche, modelli, taglie, colori)
 */
class m260922_000500_add_um_attributi_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        $now = new \yii\db\Expression('GETDATE()');

        $voci = [
            ['micro-um', 'Unità di misura', 'ruler', 'mgunitamisura/index', 8],
            ['micro-attributi', 'Attributi articolo', 'tags', 'mgattributo/index', 9],
        ];
        foreach ($voci as $v) {
            if (!(new Query())->from('{{%dash_menu}}')->where(['codice' => $v[0]])->exists()) {
                $this->insert('{{%dash_menu}}', [
                    'codice' => $v[0],
                    'label' => $v[1],
                    'icona' => $v[2],
                    'url' => $v[3],
                    'genitore_id' => $parent ?: null,
                    'livello_min' => 0,
                    'ordine' => $v[4],
                    'per_tutti' => 1,
                    'attivo' => 1,
                    'created_at' => $now,
                ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-um']);
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-attributi']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
