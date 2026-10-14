<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tipo documento "DCF" (documento di trasporto a fornitore), voce di menu
 * "Controllo DCF" e relativo permesso.
 */
class m261014_120100_add_dcf_tipo_menu extends Migration
{
    public function safeUp()
    {
        $this->creaTipoDocumento();
        $this->creaPermesso();
        $this->creaMenu();
    }

    public function safeDown()
    {
        if ($this->tableExists('dash_menu')) {
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-dcf']);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => 'dcf']);
        }
        if ($this->tableExists('mg_tipo_documento')) {
            $this->delete('{{%mg_tipo_documento}}', ['codice' => 'DCF']);
        }
    }

    private function creaTipoDocumento()
    {
        if (!$this->tableExists('mg_tipo_documento')) {
            return;
        }

        if ((new Query())->from('{{%mg_tipo_documento}}')->where(['codice' => 'DCF'])->exists()) {
            return;
        }

        $this->insert('{{%mg_tipo_documento}}', [
            'codice' => 'DCF',
            'descrizione' => 'Documento di trasporto a fornitore',
            'anno' => (int) date('Y'),
            'contatore' => 0,
            'usa_progressivo' => 1,
            'congruita' => 0,
            'attivo' => 1,
            'destinazione' => 'fornitore',
            'crea_scadenze' => 0,
            'mostra_varianti' => 0,
            'preleva_rapportini' => 0,
            'crea_articoli' => 0,
            'crea_anagrafiche' => 0,
            'mostra_matrice' => 0,
            'gestione_seriali' => 0,
            'gestione_data_consegna' => 0,
            'gestione_lotti' => 0,
            'elettronico' => 0,
            'segno_movimento' => 'nessuno',
            'varia_impegnato' => 'nessuno',
            'varia_ordinato' => 'nessuno',
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function creaPermesso()
    {
        if (!$this->tableExists('dash_permesso')) {
            return;
        }

        if ((new Query())->from('{{%dash_permesso}}')->where(['codice' => 'dcf'])->exists()) {
            return;
        }

        $this->insert('{{%dash_permesso}}', [
            'codice' => 'dcf',
            'descrizione' => 'Controllo documenti fornitore',
            'gruppo' => 'Microgestionale',
            'ordine' => 35,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function creaMenu()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-dcf'])->exists()) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')
            ->where(['codice' => 'micro-documentale'])->scalar();
        if (!$parent) {
            $parent = (new Query())->select('id')->from('{{%dash_menu}}')
                ->where(['codice' => 'micro'])->scalar();
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'micro-dcf',
            'label' => 'Controllo documenti fornitore',
            'icona' => 'truck',
            'url' => 'dcf/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 0,
            'ordine' => 7,
            'per_tutti' => 1,
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
