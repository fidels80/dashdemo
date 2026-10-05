<?php

use yii\db\Migration;

/**
 * Registro delle entita' esposte dal servizio REST e delle regole di integrita'
 * referenziale applicate in cancellazione (solo tabelle mg_*).
 *
 * - dash_api_entita : elenco tabelle/modelli raggiungibili via API
 * - dash_api_rel    : vincoli referenziali (figli / riferimenti) per entita'
 *
 * Le regole riportano i vincoli gia' imposti dalle chiavi esterne del database:
 * ogni riga "figlio" blocca la cancellazione della testata se esistono righe
 * figlie, ogni riga "riferimento" blocca la cancellazione se il record e'
 * referenziato da un altro ente.
 *
 * Per aggiungere una nuova tabella mg_* basta eseguire:
 *   php yii api/sync
 * che registra l'entita' e deriva le regole dalle foreign key.
 */
class m261005_140000_create_dash_api_entita extends Migration
{
    /**
     * codice, alias, classe, tabella, descrizione, chiave_upsert,
     * sola_lettura, cancellabile, attiva, ordinamento
     */
    private function entita()
    {
        return [
            ['aliquote-iva', 'aliquotaiva,aliquote', 'app\models\MgAliquotaIva', 'mg_aliquota_iva', 'Aliquote IVA', 'codice', 0, 1, 1, 10],
            ['anagrafiche', 'anagrafica,anagrafico,clienti,fornitori', 'app\models\MgAnagrafica', 'mg_anagrafica', 'Anagrafiche (clienti/fornitori)', 'codice', 0, 1, 1, 20],
            ['tipi-contatto', 'tipocontatto,tipi-contatti', 'app\models\MgTipoContatto', 'mg_tipo_contatto', 'Tipi di contatto', 'codice', 0, 1, 1, 30],
            ['anagrafiche-contatti', 'contatti,anagrafica-contatti', 'app\models\MgAnagraficaContatto', 'mg_anagrafica_contatto', 'Contatti anagrafiche', null, 0, 1, 1, 40],
            ['articoli', 'articolo,prodotti', 'app\models\MgArticolo', 'mg_articolo', 'Articoli', 'codice', 0, 1, 1, 50],
            ['articoli-um', 'articoloum,unita-articolo', 'app\models\MgArticoloUm', 'mg_articolo_um', 'Unita di misura per articolo', null, 0, 1, 1, 60],
            ['attributi-articolo', 'attributi,varianti', 'app\models\MgAttributoArticolo', 'mg_attributo_articolo', 'Attributi articolo (marca/modello/taglia/colore/tessuto)', 'tipo,descrizione', 0, 1, 1, 70],
            ['modelli-tessuti', 'modellotessuto,modelli-tessuto', 'app\models\MgModelloTessuto', 'mg_modello_tessuto', 'Abbinamenti modello/tessuto', null, 0, 1, 1, 80],
            ['unita-misura', 'unita-misure,um', 'app\models\MgUnitaMisura', 'mg_unita_misura', 'Unita di misura', 'codice', 0, 1, 1, 90],
            ['tipi-pagamento', 'tipopagamento,tipi-pagamenti', 'app\models\MgTipoPagamento', 'mg_tipo_pagamento', 'Tipi di pagamento', 'codice', 0, 1, 1, 100],
            ['metodi-pagamento', 'metodopagamento,metodi-pagamento', 'app\models\MgMetodoPagamento', 'mg_metodo_pagamento', 'Metodi di pagamento', 'codice', 0, 1, 1, 110],
            ['metodi-pagamento-rate', 'rate,scadenzario-metodo', 'app\models\MgMetodoPagamentoRata', 'mg_metodo_pagamento_rata', 'Rate dei metodi di pagamento', null, 0, 1, 1, 120],
            ['tipi-documento', 'tipodocumento,tipi-documento', 'app\models\MgTipoDocumento', 'mg_tipo_documento', 'Tipi di documento', 'codice', 0, 1, 1, 130],
            ['documenti', 'documento,testate', 'app\models\MgDocumento', 'mg_documento', 'Documenti (testate)', 'id_tipo,anno,numero,suffisso', 0, 1, 1, 140],
            ['righe', 'riga,righe-documento,documentirighe', 'app\models\MgDocumentoRiga', 'mg_documento_riga', 'Righe documento', null, 0, 1, 1, 150],
            ['scadenze', 'scadenza,scadenze-documento', 'app\models\MgScadenza', 'mg_scadenza', 'Scadenze documento', null, 0, 0, 1, 160],
            ['commesse', 'commessa', 'app\models\MgCommessa', 'mg_commessa', 'Commesse', 'codice', 0, 1, 1, 170],
            ['sottocommesse', 'sottocommessa', 'app\models\MgSottocommessa', 'mg_sottocommessa', 'Sottocommesse', 'codice', 0, 1, 1, 180],
        ];
    }

    /**
     * entita, tipo, tabella, colonna, etichetta, cascade, ordinamento
     * cascade = 1 solo sui figli: l'API puo' rimuoverli solo su richiesta esplicita.
     */
    private function relazioni()
    {
        return [
            // Testata documento: non si cancella se ha righe o scadenze
            ['documenti', 'figlio', 'mg_documento_riga', 'id_documento', 'righe del documento', 1, 10],
            ['documenti', 'figlio', 'mg_scadenza', 'id_documento', 'scadenze del documento', 1, 20],

            ['tipi-documento', 'riferimento', 'mg_documento', 'id_tipo', 'documenti di questo tipo', 0, 10],

            ['anagrafiche', 'riferimento', 'mg_documento', 'id_anagrafica', 'documenti di questa anagrafica', 0, 10],
            ['anagrafiche', 'riferimento', 'mg_commessa', 'id_anagrafica', 'commesse di questa anagrafica', 0, 20],
            ['anagrafiche', 'riferimento', 'mg_sottocommessa', 'id_anagrafica', 'sottocommesse di questa anagrafica', 0, 30],
            ['anagrafiche', 'figlio', 'mg_anagrafica_contatto', 'id_anagrafica', 'contatti di questa anagrafica', 1, 40],

            ['articoli', 'figlio', 'mg_articolo_um', 'id_articolo', 'conversioni di unita di misura', 1, 20],
            ['articoli', 'riferimento', 'mg_documento_riga', 'id_articolo', 'righe documento con questo articolo', 0, 10],

            ['unita-misura', 'riferimento', 'mg_articolo_um', 'id_unita_misura', 'conversioni che la usano', 0, 10],
            ['unita-misura', 'riferimento', 'mg_documento_riga', 'id_unita_misura', 'righe documento con questa unita', 0, 20],

            ['aliquote-iva', 'riferimento', 'mg_articolo', 'id_iva_vendita', 'articoli con IVA di vendita', 0, 10],
            ['aliquote-iva', 'riferimento', 'mg_articolo', 'id_iva_acquisto', 'articoli con IVA di acquisto', 0, 20],
            ['aliquote-iva', 'riferimento', 'mg_anagrafica', 'id_aliquota_iva', 'anagrafiche con questa aliquota', 0, 30],

            ['attributi-articolo', 'riferimento', 'mg_articolo', 'id_marca', 'articoli con questa marca', 0, 10],
            ['attributi-articolo', 'riferimento', 'mg_articolo', 'id_modello', 'articoli con questo modello', 0, 20],
            ['attributi-articolo', 'riferimento', 'mg_articolo', 'id_tessuto', 'articoli con questo tessuto', 0, 30],
            ['attributi-articolo', 'riferimento', 'mg_articolo', 'id_taglia', 'articoli con questa taglia', 0, 40],
            ['attributi-articolo', 'riferimento', 'mg_articolo', 'id_colore', 'articoli con questo colore', 0, 50],
            ['attributi-articolo', 'figlio', 'mg_modello_tessuto', 'id_modello', 'abbinamenti con questo modello', 1, 60],
            ['attributi-articolo', 'riferimento', 'mg_modello_tessuto', 'id_tessuto', 'abbinamenti con questo tessuto', 0, 70],

            ['tipi-pagamento', 'riferimento', 'mg_metodo_pagamento', 'id_tipo_pagamento', 'metodi di pagamento di questo tipo', 0, 10],

            ['metodi-pagamento', 'figlio', 'mg_metodo_pagamento_rata', 'id_metodo', 'rate di questo metodo', 1, 10],
            ['metodi-pagamento', 'riferimento', 'mg_documento', 'id_metodo_pagamento', 'documenti con questo metodo', 0, 20],
            ['metodi-pagamento', 'riferimento', 'mg_scadenza', 'id_metodo_pagamento', 'scadenze con questo metodo', 0, 30],
            ['metodi-pagamento', 'riferimento', 'mg_anagrafica', 'id_metodo_pagamento', 'anagrafiche con questo metodo', 0, 40],

            ['commesse', 'figlio', 'mg_sottocommessa', 'id_commessa', 'sottocommesse della commessa', 1, 10],

            ['tipi-contatto', 'riferimento', 'mg_anagrafica_contatto', 'id_tipo_contatto', 'contatti di questo tipo', 0, 10],
        ];
    }

    public function safeUp()
    {
        if (!$this->checkTableExist('dash_api_entita')) {
            $this->createTable('{{%dash_api_entita}}', [
                'id' => $this->primaryKey(),
                'codice' => $this->string(30)->notNull(),
                'alias' => $this->string(200)->null(),
                'classe' => $this->string(200)->notNull(),
                'tabella' => $this->string(60)->notNull(),
                'descrizione' => $this->string(200)->notNull(),
                'chiave_upsert' => $this->string(200)->null(),
                'sola_lettura' => $this->boolean()->notNull()->defaultValue(0),
                'cancellabile' => $this->boolean()->notNull()->defaultValue(1),
                'attiva' => $this->boolean()->notNull()->defaultValue(1),
                'ordinamento' => $this->integer()->notNull()->defaultValue(0),
            ]);
            $this->createIndex('idx-dash_api_entita-codice', '{{%dash_api_entita}}', 'codice', true);
            $this->createIndex('idx-dash_api_entita-tabella', '{{%dash_api_entita}}', 'tabella');
        }

        if (!$this->checkTableExist('dash_api_rel')) {
            $this->createTable('{{%dash_api_rel}}', [
                'id' => $this->primaryKey(),
                'entita' => $this->string(30)->notNull(),
                'tipo' => $this->string(20)->notNull(),
                'tabella' => $this->string(60)->notNull(),
                'colonna' => $this->string(60)->notNull(),
                'etichetta' => $this->string(100)->notNull(),
                'cascade' => $this->boolean()->notNull()->defaultValue(0),
                'attiva' => $this->boolean()->notNull()->defaultValue(1),
                'ordinamento' => $this->integer()->notNull()->defaultValue(0),
            ]);
            $this->createIndex('idx-dash_api_rel-entita', '{{%dash_api_rel}}', 'entita');
            $this->createIndex('idx-dash_api_rel-unica', '{{%dash_api_rel}}',
                ['entita', 'tipo', 'tabella', 'colonna'], true);
        }

        // "riferimento" richiede 11 caratteri: allarga la colonna se una versione
        // precedente della tabella l'aveva creata troppo corta.
        if ($this->checkTableExist('dash_api_rel') && $this->columnLength('dash_api_rel', 'tipo') < 20) {
            $this->execute('ALTER TABLE [dash_api_rel] ALTER COLUMN [tipo] NVARCHAR(20) NOT NULL');
        }

        $this->seedEntita();
        $this->seedRelazioni();

        // I token precedenti non avevano permessi applicati: si portano a "*"
        if ($this->checkTableExist('api_token')) {
            $this->update('{{%api_token}}', ['scopes' => '*'],
                ['or', ['scopes' => null], ['scopes' => '']]);
        }
    }

    public function safeDown()
    {
        if ($this->checkTableExist('dash_api_rel')) {
            $this->dropTable('{{%dash_api_rel}}');
        }
        if ($this->checkTableExist('dash_api_entita')) {
            $this->dropTable('{{%dash_api_entita}}');
        }
    }

    private function seedEntita()
    {
        foreach ($this->entita() as $r) {
            list($codice, $alias, $classe, $tabella, $descrizione, $chiave,
                $soloLettura, $cancellabile, $attiva, $ordine) = $r;

            $exists = (new \yii\db\Query())
                ->from('{{%dash_api_entita}}')
                ->where(['codice' => $codice])
                ->exists();
            if ($exists) {
                continue;
            }

            $this->insert('{{%dash_api_entita}}', [
                'codice' => $codice,
                'alias' => $alias,
                'classe' => $classe,
                'tabella' => $tabella,
                'descrizione' => $descrizione,
                'chiave_upsert' => $chiave,
                'sola_lettura' => $soloLettura,
                'cancellabile' => $cancellabile,
                'attiva' => $attiva,
                'ordinamento' => $ordine,
            ]);
        }
    }

    private function seedRelazioni()
    {
        $valide = [];
        foreach ($this->entita() as $r) {
            $valide[] = $r[0];
        }

        foreach ($this->relazioni() as $r) {
            list($entita, $tipo, $tabella, $colonna, $etichetta, $cascade, $ordine) = $r;

            if (!in_array($entita, $valide, true)) {
                continue;
            }

            $exists = (new \yii\db\Query())
                ->from('{{%dash_api_rel}}')
                ->where([
                    'entita' => $entita,
                    'tipo' => $tipo,
                    'tabella' => $tabella,
                    'colonna' => $colonna,
                ])
                ->exists();
            if ($exists) {
                continue;
            }

            $this->insert('{{%dash_api_rel}}', [
                'entita' => $entita,
                'tipo' => $tipo,
                'tabella' => $tabella,
                'colonna' => $colonna,
                'etichetta' => $etichetta,
                'cascade' => $cascade,
                'attiva' => 1,
                'ordinamento' => $ordine,
            ]);
        }
    }

    private function checkTableExist($table)
    {
        $command = Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :table"
        );
        $command->bindValues([':table' => $table]);
        return $command->queryScalar() > 0;
    }

    /**
     * Lunghezza dichiarata di una colonna (0 se assente).
     */
    private function columnLength($table, $column)
    {
        $command = Yii::$app->db->createCommand(
            "SELECT ISNULL(CHARACTER_MAXIMUM_LENGTH, 0) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_NAME = :table AND COLUMN_NAME = :column"
        );
        $command->bindValues([':table' => $table, ':column' => $column]);
        return (int) $command->queryScalar();
    }
}
