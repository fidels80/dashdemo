<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale) le voci:
 * - Commesse
 * - Sottocommesse
 */
class m260930_100100_add_commesse_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        $now = new \yii\db\Expression('GETDATE()');

        $voci = [
            ['micro-commesse', 'Commesse', 'diagram-project', 'mgcommessa/index', 10],
            ['micro-sottocommesse', 'Sottocommesse', 'diagram-project', 'mgsottocommessa/index', 11],
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

        // La nuova voce "Commesse" precede il wizard prodotti.
        $this->update('{{%dash_menu}}', ['ordine' => 12], ['codice' => 'micro-wizard']);
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-commesse']);
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-sottocommesse']);
            $this->update('{{%dash_menu}}', ['ordine' => 10], ['codice' => 'micro-wizard']);
        }
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
