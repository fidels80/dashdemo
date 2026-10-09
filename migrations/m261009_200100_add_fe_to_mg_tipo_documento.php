<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_tipo_documento i flag e i valori di default per la
 * fatturazione elettronica (card "Documento elettronico").
 *
 * Condizioni/modalità di pagamento e natura IVA NON stanno qui: sono
 * configurate rispettivamente su mg_tipo_pagamento, mg_metodo_pagamento
 * e mg_aliquota_iva, così da riflettere quanto indicato nel documento.
 */
class m261009_200100_add_fe_to_mg_tipo_documento extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_tipo_documento', 'elettronico')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'elettronico', $this->boolean()->notNull()->defaultValue(0));
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_tipo_documento')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_tipo_documento', $this->string(4)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_regime_fiscale')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_regime_fiscale', $this->string(4)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_divisa')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_divisa', $this->string(3)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_causale')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_causale', $this->string(200)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_esigibilita_iva')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_esigibilita_iva', $this->string(1)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_riferimento_normativo')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_riferimento_normativo', $this->string(100)->null());
        }
        if (!$this->checkColumnExist('mg_tipo_documento', 'fe_codice_destinatario')) {
            $this->addColumn('{{%mg_tipo_documento}}', 'fe_codice_destinatario', $this->string(7)->null());
        }

        // Valori di default per i tipi documento esistenti
        $this->execute("UPDATE {{%mg_tipo_documento}} SET fe_tipo_documento='TD01', fe_regime_fiscale='RF01',
            fe_divisa='EUR', fe_esigibilita_iva='I' WHERE codice='FTT'");
    }

    public function safeDown()
    {
        foreach (['elettronico', 'fe_tipo_documento', 'fe_regime_fiscale', 'fe_divisa', 'fe_causale',
                  'fe_esigibilita_iva', 'fe_riferimento_normativo', 'fe_codice_destinatario'] as $c) {
            if ($this->checkColumnExist('mg_tipo_documento', $c)) {
                $this->dropColumn('{{%mg_tipo_documento}}', $c);
            }
        }
    }

    private function checkColumnExist($table, $column)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :t AND COLUMN_NAME = :c"
        )->bindValue(':t', $table)->bindValue(':c', $column)->queryScalar() > 0;
    }
}
