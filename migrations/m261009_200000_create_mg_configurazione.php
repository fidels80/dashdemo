<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Tabella di configurazione generica del gestionale: coppie codice/valore
 * con descrizione, usata per i parametri standard (es. dati della fattura
 * elettronica) e in futuro per altre impostazioni.
 */
class m261009_200000_create_mg_configurazione extends Migration
{
    public function safeUp()
    {
        if (!$this->checkTableExist('mg_configurazione')) {
            $this->createTable('{{%mg_configurazione}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(100)->notNull(),
                'descrizione' => $this->string(255)->notNull(),
                'valore' => $this->string(500)->null(),
            ]);
            $this->createIndex('idx-mg_configurazione-codice', '{{%mg_configurazione}}', 'codice', true);
        }

        if ($this->checkTableExist('mg_configurazione')) {
            $count = (new Query())->from('{{%mg_configurazione}}')->count();
            if ((int) $count === 0) {
                $this->batchInsert('{{%mg_configurazione}}',
                    ['codice', 'descrizione', 'valore'], [
                        // Dati del cedente / prestatore (intestazione azienda)
                        ['fe.cedente.id_paese', 'Cedente - Paese (ISO)', 'IT'],
                        ['fe.cedente.denominazione', 'Cedente - Denominazione / Ragione sociale', ''],
                        ['fe.cedente.partita_iva', 'Cedente - Partita IVA', ''],
                        ['fe.cedente.codice_fiscale', 'Cedente - Codice fiscale', ''],
                        ['fe.cedente.regime_fiscale', 'Cedente - Regime fiscale (RF01...)', 'RF01'],
                        ['fe.cedente.indirizzo', 'Cedente - Indirizzo (via/piazza)', ''],
                        ['fe.cedente.numero_civico', 'Cedente - Numero civico', ''],
                        ['fe.cedente.cap', 'Cedente - CAP', ''],
                        ['fe.cedente.comune', 'Cedente - Comune', ''],
                        ['fe.cedente.provincia', 'Cedente - Provincia (2 lettere)', ''],
                        ['fe.cedente.nazione', 'Cedente - Nazione (ISO)', 'IT'],
                        ['fe.cedente.telefono', 'Cedente - Telefono', ''],
                        ['fe.cedente.email', 'Cedente - Email', ''],
                        // Dati di trasmissione al Sistema di Interscambio
                        ['fe.trasmissione.id_paese', 'Trasmissione - IdPaese del trasmittente', 'IT'],
                        ['fe.trasmissione.id_codice', 'Trasmissione - IdCodice (P.IVA/CF) del trasmittente', ''],
                        ['fe.trasmissione.formato', 'Trasmissione - Formato (FPR12/FPA12)', 'FPR12'],
                        ['fe.trasmissione.progressivo_invio', 'Trasmissione - Prefisso progressivo invio (seguito dall\'ID documento)', ''],
                        ['fe.trasmissione.codice_destinatario', 'Trasmissione - Codice destinatario di default', '0000000'],
                        // Dati generali documento
                        ['fe.divisa', 'Documento - Divisa di default', 'EUR'],
                        ['fe.condizioni_pagamento', 'Documento - Condizioni pagamento di default (TP01/TP02/TP03)', 'TP02'],
                        ['fe.modalita_pagamento', 'Documento - Modalità pagamento di default (MP01..MP23)', 'MP05'],
                        ['fe.natura', 'Documento - Natura IVA di default per aliquota 0 (N1..N7)', 'N1'],
                    ]);
            }
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('mg_configurazione')) {
            $this->dropTable('{{%mg_configurazione}}');
        }
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
