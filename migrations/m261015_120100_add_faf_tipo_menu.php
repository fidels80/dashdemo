<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tipo documento "FAF" (fattura ricevuta da fornitore), voce di menu
 * "Fatture fornitori" e relativo permesso.
 */
class m261015_120100_add_faf_tipo_menu extends Migration
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
            $this->delete('{{%dash_menu}}', ['codice' => 'micro-fatturefornitore']);
        }
        if ($this->tableExists('dash_permesso')) {
            $this->delete('{{%dash_permesso}}', ['codice' => 'fatturafornitore']);
        }
        if ($this->tableExists('mg_tipo_documento')) {
            $this->delete('{{%mg_tipo_documento}}', ['codice' => 'FAF']);
        }
    }

    private function creaTipoDocumento()
    {
        if (!$this->tableExists('mg_tipo_documento')) {
            return;
        }

        if ((new Query())->from('{{%mg_tipo_documento}}')->where(['codice' => 'FAF'])->exists()) {
            return;
        }

        $this->insert('{{%mg_tipo_documento}}', [
            'codice' => 'FAF',
            'descrizione' => 'Fattura fornitore',
            'anno' => (int) date('Y'),
            'contatore' => 0,
            'usa_progressivo' => 0,
            'congruita' => 0,
            'attivo' => 1,
            'destinazione' => 'fornitore',
            'crea_scadenze' => 1,
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

        if ((new Query())->from('{{%dash_permesso}}')->where(['codice' => 'fatturafornitore'])->exists()) {
            return;
        }

        $this->insert('{{%dash_permesso}}', [
            'codice' => 'fatturafornitore',
            'descrizione' => 'Fatture fornitori',
            'gruppo' => 'Microgestionale',
            'ordine' => 36,
            'attivo' => 1,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ]);
    }

    private function creaMenu()
    {
        if (!$this->tableExists('dash_menu')) {
            return;
        }

        if ((new Query())->from('{{%dash_menu}}')->where(['codice' => 'micro-fatturefornitore'])->exists()) {
            return;
        }

        $parent = (new Query())->select('id')->from('{{%dash_menu}}')
            ->where(['codice' => 'micro-documentale'])->scalar();
        if (!$parent) {
            $parent = (new Query())->select('id')->from('{{%dash_menu}}')
                ->where(['codice' => 'micro'])->scalar();
        }

        $this->insert('{{%dash_menu}}', [
            'codice' => 'micro-fatturefornitore',
            'label' => 'Fatture fornitori',
            'icona' => 'file-invoice',
            'url' => 'fatturafornitore/index',
            'genitore_id' => $parent ?: null,
            'livello_min' => 0,
            'ordine' => 8,
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
