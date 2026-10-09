<?php

use yii\db\Migration;

/**
 * Aggiunge a mg_anagrafica i dati per i documenti elettronici
 * (card "Documenti elettronici"): codice destinatario, PEC, soggetto
 * estero / persona fisica e relative anagrafiche.
 */
class m261009_200200_add_fe_to_mg_anagrafica extends Migration
{
    public function safeUp()
    {
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_codice_destinatario')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_codice_destinatario', $this->string(7)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_pec')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_pec', $this->string(100)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_id_paese')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_id_paese', $this->string(2)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_nazione')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_nazione', $this->string(2)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_tipo_soggetto')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_tipo_soggetto', $this->string(1)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_nome')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_nome', $this->string(100)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_cognome')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_cognome', $this->string(100)->null());
        }
        if (!$this->checkColumnExist('mg_anagrafica', 'fe_regime_fiscale')) {
            $this->addColumn('{{%mg_anagrafica}}', 'fe_regime_fiscale', $this->string(4)->null());
        }

        $this->execute("UPDATE {{%mg_anagrafica}} SET fe_id_paese = 'IT' WHERE fe_id_paese IS NULL");
        $this->execute("UPDATE {{%mg_anagrafica}} SET fe_nazione = 'IT' WHERE fe_nazione IS NULL");
        $this->execute("UPDATE {{%mg_anagrafica}} SET fe_tipo_soggetto = 'G' WHERE fe_tipo_soggetto IS NULL");
    }

    public function safeDown()
    {
        foreach (['fe_codice_destinatario', 'fe_pec', 'fe_id_paese', 'fe_nazione',
                  'fe_tipo_soggetto', 'fe_nome', 'fe_cognome', 'fe_regime_fiscale'] as $c) {
            if ($this->checkColumnExist('mg_anagrafica', $c)) {
                $this->dropColumn('{{%mg_anagrafica}}', $c);
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
