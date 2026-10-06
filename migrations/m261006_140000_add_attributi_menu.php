<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Aggiunge al menu (sotto Microgestionale > Prodotti) le voci
 * Taglie, Tessuti e Colori, che aprono gli attributi filtrati per tipo.
 */
class m261006_140000_add_attributi_menu extends Migration
{
    public function safeUp()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $prodotti = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro-prodotti'])->scalar();
        if (!$prodotti) {
            $prodotti = (new Query())->select('id')->from('{{%dash_menu}}')->where(['codice' => 'micro'])->scalar();
        }
        if (!$prodotti) {
            return;
        }

        $this->insertVoce('micro-taglie', 'Taglie', 'ruler', 'mgattributo/index?tipo=taglia', $prodotti, 2);
        $this->insertVoce('micro-tessuti', 'Tessuti', 'tshirt', 'mgattributo/index?tipo=tessuto', $prodotti, 3);
        $this->insertVoce('micro-colori', 'Colori', 'palette', 'mgattributo/index?tipo=colore', $prodotti, 4);

        // Le voci esistenti restano sotto le nuove.
        $this->sposta('micro-art', 1);
        $this->sposta('micro-attributi', 5);
        $this->sposta('micro-um', 6);
        $this->sposta('micro-modelli', 7);
        $this->sposta('micro-wizard', 8);
        $this->sposta('micro-magazzini', 9);
    }

    public function safeDown()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        $this->delete('{{%dash_menu}}', ['codice' => ['micro-taglie', 'micro-tessuti', 'micro-colori']]);

        $this->sposta('micro-attributi', 2);
        $this->sposta('micro-um', 3);
        $this->sposta('micro-modelli', 4);
        $this->sposta('micro-wizard', 5);
        $this->sposta('micro-magazzini', 6);
    }

    private function insertVoce($codice, $label, $icona, $url, $genitoreId, $ordine)
    {
        $esiste = (new Query())->from('{{%dash_menu}}')->where(['codice' => $codice])->exists();
        if ($esiste) {
            return;
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => $codice,
            'label' => $label,
            'icona' => $icona,
            'url' => $url,
            'genitore_id' => $genitoreId,
            'livello_min' => 0,
            'ordine' => $ordine,
            'per_tutti' => 1,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function sposta($codice, $ordine)
    {
        $this->update('{{%dash_menu}}', ['ordine' => $ordine], ['codice' => $codice]);
    }

    private function tableExists($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
