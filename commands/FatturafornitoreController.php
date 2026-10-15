<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgTipoDocumento;
use app\models\MgMetodoPagamento;

/**
 * Carica fatture fornitore (tipo FAF) di prova con scadenze generate.
 *
 * Ogni fattura ha un numero casuale di righe (default 20-30) con articoli
 * diversi, un metodo di pagamento casuale e le relative scadenze.
 *
 * Uso:
 *   php yii fatturafornitore/seed
 *   php yii fatturafornitore/seed --count=50 --min=20 --max=30
 */
class FatturafornitoreController extends Controller
{
    /** Numero totale di fatture FAF da avere in archivio. */
    public $count = 50;

    /** Numero minimo di righe per fattura. */
    public $min = 20;

    /** Numero massimo di righe per fattura. */
    public $max = 30;

    public function options($actionID)
    {
        return ['count', 'min', 'max'];
    }

    public function optionAliases()
    {
        return ['c' => 'count', 'm' => 'min', 'M' => 'max'];
    }

    public function actionSeed()
    {
        $tipo = MgTipoDocumento::findOne(['codice' => 'FAF']);
        if ($tipo === null) {
            $this->stderr("Tipo documento 'FAF' non trovato: eseguire prima le migrazioni.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $fornitori = (new \yii\db\Query())->select('id')->from('mg_anagrafica')
            ->where(['is_fornitore' => 1])->column();
        $articoli = (new \yii\db\Query())
            ->select(['id', 'codice', 'descrizione', 'prezzo', 'iva', 'um'])
            ->from('mg_articolo')
            ->where(['not', ['codice' => 'API-TEST']])
            ->all();
        $metodi = (new \yii\db\Query())->select('id')->from('mg_metodo_pagamento')
            ->where(['attivo' => 1])->column();

        if (empty($fornitori) || empty($articoli) || empty($metodi)) {
            $this->stderr("Dati di base mancanti (fornitori/articoli/metodi di pagamento).\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $min = max(1, (int) $this->min);
        $max = max($min, (int) $this->max);
        $count = max(1, (int) $this->count);

        $esistenti = (int) MgDocumento::find()->where(['id_tipo' => $tipo->id])->count();
        $daCreare = $count - $esistenti;
        if ($daCreare <= 0) {
            $this->stdout("Fatture FAF gia' presenti: $esistenti (richieste $count). Nessuna azione.\n");
            return ExitCode::OK;
        }

        $this->stdout("Fatture FAF presenti: $esistenti. Ne creo $daCreare.\n");

        $anno = (int) date('Y');
        $numero = (int) MgDocumento::find()
            ->where(['id_tipo' => $tipo->id, 'anno' => $anno])
            ->max('numero');

        $inizio = strtotime('-120 days');
        $oggi = time();
        $creati = 0;
        $totRighe = 0;
        $totScadenze = 0;

        for ($i = 0; $i < $daCreare; $i++) {
            $numero++;

            $doc = new MgDocumento();
            $doc->id_tipo = $tipo->id;
            $doc->codice_tipo = $tipo->codice;
            $doc->anno = $anno;
            $doc->numero = $numero;
            $doc->suffisso = '';
            $doc->data = date('Y-m-d', mt_rand($inizio, $oggi));
            $doc->id_anagrafica = $fornitori[array_rand($fornitori)];
            $doc->id_metodo_pagamento = $metodi[array_rand($metodi)];
            $doc->descrizione = 'Fattura fornitore n. ' . $numero . '/' . $anno;
            $doc->stato = 'confermato';
            $doc->created_by = 'seed';
            $doc->note = 'Fattura generata automaticamente.';

            if (!$doc->save(false)) {
                $this->stderr("Errore nella creazione della fattura n.$numero.\n");
                continue;
            }

            $nRighe = mt_rand($min, $max);
            $nRighe = min($nRighe, count($articoli));
            $scelti = (array) array_rand($articoli, $nRighe);

            $ord = 0;
            foreach ($scelti as $idx) {
                $art = $articoli[$idx];
                $riga = new MgDocumentoRiga();
                $riga->id_documento = $doc->id;
                $riga->id_articolo = $art['id'];
                $riga->codice_articolo = $art['codice'];
                $riga->codice_tipo = $tipo->codice;
                $riga->descrizione = $art['descrizione'];
                $riga->um = $art['um'];
                $riga->qta = mt_rand(1, 60);
                $riga->prezzo = $art['prezzo'];
                $riga->sconto = [0, 0, 0, 5, 10][array_rand([0, 0, 0, 5, 10])];
                $riga->iva = $art['iva'];
                $riga->fattore = 1;
                $riga->ordine = $ord++;
                $riga->save(false);
                $totRighe++;
            }

            $doc->calcolaTotale();
            $totScadenze += $doc->generaScadenze();
            $creati++;
        }

        $this->stdout("Create $creati fatture FAF con $totRighe righe e $totScadenze scadenze.\n");
        $this->stdout("Totale fatture FAF in archivio: " . MgDocumento::find()->where(['id_tipo' => $tipo->id])->count() . "\n");

        return ExitCode::OK;
    }
}
