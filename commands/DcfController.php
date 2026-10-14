<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgTipoDocumento;

/**
 * Carica documenti DCF (documento di trasporto a fornitore) di prova.
 *
 * Ogni documento ha un numero casuale di righe (default 35-40) con articoli
 * diversi, utile per provare il wizard di controllo documenti fornitore.
 *
 * Uso:
 *   php yii dcf/seed
 *   php yii dcf/seed --count=50 --min=35 --max=40
 */
class DcfController extends Controller
{
    /** Numero totale di documenti DCF da avere in archivio. */
    public $count = 50;

    /** Numero minimo di righe per documento. */
    public $min = 35;

    /** Numero massimo di righe per documento. */
    public $max = 40;

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
        $tipo = MgTipoDocumento::findOne(['codice' => 'DCF']);
        if ($tipo === null) {
            $this->stderr("Tipo documento 'DCF' non trovato: eseguire prima le migrazioni.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $fornitori = (new \yii\db\Query())->select('id')->from('mg_anagrafica')
            ->where(['is_fornitore' => 1])->column();
        $articoli = (new \yii\db\Query())
            ->select(['id', 'codice', 'descrizione', 'prezzo', 'iva', 'um'])
            ->from('mg_articolo')
            ->where(['not', ['codice' => 'API-TEST']])
            ->all();

        if (empty($fornitori) || empty($articoli)) {
            $this->stderr("Dati di base mancanti (fornitori/articoli).\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $min = max(1, (int) $this->min);
        $max = max($min, (int) $this->max);
        $count = max(1, (int) $this->count);

        $esistenti = (int) MgDocumento::find()->where(['id_tipo' => $tipo->id])->count();
        $daCreare = $count - $esistenti;
        if ($daCreare <= 0) {
            $this->stdout("Documenti DCF gia' presenti: $esistenti (richiesti $count). Nessuna azione.\n");
            return ExitCode::OK;
        }

        $this->stdout("Documenti DCF presenti: $esistenti. Ne creo $daCreare.\n");

        $anno = (int) date('Y');
        $ultimo = (int) MgDocumento::find()
            ->where(['id_tipo' => $tipo->id, 'anno' => $anno])
            ->max('numero');
        $numero = $ultimo;

        $inizio = strtotime('-120 days');
        $oggi = time();
        $creati = 0;
        $totRighe = 0;

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
            $doc->descrizione = 'Documento di trasporto a fornitore n. ' . $numero;
            $doc->stato = 'confermato';
            $doc->created_by = 'seed';
            $doc->note = 'Documento generato automaticamente per il controllo quantita.';

            if (!$doc->save(false)) {
                $this->stderr("Errore nella creazione del documento n.$numero.\n");
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
            $creati++;
        }

        $this->stdout("Creati $creati documenti DCF con $totRighe righe totali.\n");
        $this->stdout("Totale documenti DCF in archivio: " . MgDocumento::find()->where(['id_tipo' => $tipo->id])->count() . "\n");

        return ExitCode::OK;
    }
}
