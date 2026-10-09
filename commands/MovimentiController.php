<?php

namespace app\commands;

use Yii;
use app\models\MgDocumentoRiga;
use app\models\MgMovimentoMagazzino;
use app\models\MgTipoDocumento;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

/**
 * Rigenerazione dei movimenti di magazzino (procedura solo console).
 *
 * Uso:
 *   php yii movimenti/ricrea
 *
 * La procedura:
 * 1. allinea le righe documento incomplete: codice tipo (dalla testata),
 *    codice articolo e unità di misura (default dell'articolo) e magazzini
 *    (dal tipo documento della riga);
 * 2. esegue un backup della tabella mg_movimentimagazzino;
 * 3. svuota la tabella e ricrea i movimenti per ogni riga documento:
 *    se la riga ha lotti genera un movimento per lotto (id_lotto), altrimenti
 *    un unico movimento per riga.
 */
class MovimentiController extends Controller
{
    public function actionRicrea()
    {
        if (!$this->checkTableExist('mg_movimentimagazzino')) {
            Console::output(Console::ansiFormat(
                'Tabella mg_movimentimagazzino non presente: eseguire prima le migrazioni.',
                [Console::FG_RED]
            ));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $inizio = microtime(true);
        $tipi = MgTipoDocumento::find()->all();
        $tipiByCodice = ArrayHelper::index($tipi, 'codice');
        $tipiById = ArrayHelper::index($tipi, 'id');

        $allineate = $this->allineaRighe($tipiByCodice, $tipiById);
        Console::output("Righe allineate (codice tipo, articolo/UM, magazzini): {$allineate}");

        $backup = $this->backupTabella();
        Console::output("Backup movimenti creato in {$backup}");

        $transaction = Yii::$app->db->beginTransaction();
        try {
            MgMovimentoMagazzino::deleteAll();

            $creati = 0;
            foreach (MgDocumentoRiga::find()->with('documento', 'dettagli')->orderBy(['id' => SORT_ASC])->batch(500) as $righe) {
                foreach ($righe as $riga) {
                    $movimenti = MgMovimentoMagazzino::creaDaRiga($riga, $this->tipoRiga($riga, $tipiByCodice, $tipiById));
                    $creati += count($movimenti);
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Console::output(Console::ansiFormat(
                'Errore: ' . $e->getMessage() . '. Movimenti originali ripristinati; backup in ' . $backup,
                [Console::FG_RED]
            ));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        Console::output("Movimenti ricreati: {$creati} (" . round(microtime(true) - $inizio, 2) . 's)');
        return ExitCode::OK;
    }

    /**
     * Allinea le righe documento incomplete: codice tipo (copiato dalla
     * testata), codice articolo e unità di misura (default dell'articolo),
     * magazzini (dal tipo documento). Vengono considerate solo le righe
     * a cui manca almeno uno di questi valori.
     *
     * @param MgTipoDocumento[] $tipiByCodice
     * @param MgTipoDocumento[] $tipiById
     * @return int numero di righe modificate
     */
    private function allineaRighe($tipiByCodice, $tipiById)
    {
        $query = MgDocumentoRiga::find()
            ->with('documento')
            ->where(['or',
                ['codice_tipo' => null],
                ['codice_tipo' => ''],
                ['codice_articolo' => null],
                ['codice_articolo' => ''],
                ['id_unita_misura' => null],
                ['um' => null],
                ['um' => ''],
                ['id_magazzino_partenza' => null],
                ['id_magazzino_arrivo' => null],
            ])
            ->orderBy(['id' => SORT_ASC]);

        $allineate = 0;
        foreach ($query->batch(500) as $righe) {
            foreach ($righe as $riga) {
                $daSalvare = false;

                if ($riga->codice_tipo === null || $riga->codice_tipo === '') {
                    $testata = $riga->documento;
                    if ($testata && $testata->codice_tipo !== null && $testata->codice_tipo !== '') {
                        $riga->codice_tipo = $testata->codice_tipo;
                        $daSalvare = true;
                    }
                }

                $dati = MgMovimentoMagazzino::datiArticolo($riga);
                if (($riga->codice_articolo === null || $riga->codice_articolo === '')
                    && $dati['codice_articolo'] !== null) {
                    $riga->codice_articolo = $dati['codice_articolo'];
                    $daSalvare = true;
                }
                if (!$riga->id_unita_misura && $dati['id_unita_misura']) {
                    $riga->id_unita_misura = $dati['id_unita_misura'];
                    $daSalvare = true;
                }
                if (($riga->um === null || $riga->um === '') && $dati['um'] !== null) {
                    $riga->um = $dati['um'];
                    $daSalvare = true;
                }

                $tipo = $this->tipoRiga($riga, $tipiByCodice, $tipiById);
                if ($tipo) {
                    if (!$riga->id_magazzino_partenza && $tipo->id_magazzino_partenza) {
                        $riga->id_magazzino_partenza = $tipo->id_magazzino_partenza;
                        $daSalvare = true;
                    }
                    if (!$riga->id_magazzino_arrivo && $tipo->id_magazzino_arrivo) {
                        $riga->id_magazzino_arrivo = $tipo->id_magazzino_arrivo;
                        $daSalvare = true;
                    }
                }

                if ($daSalvare) {
                    $riga->save(false, [
                        'codice_tipo', 'codice_articolo', 'id_unita_misura', 'um',
                        'id_magazzino_partenza', 'id_magazzino_arrivo',
                    ]);
                    $allineate++;
                }
            }
        }

        return $allineate;
    }

    /**
     * Tipo documento della riga: dal codice tipo della riga, altrimenti dalla
     * testata del documento.
     *
     * @param MgDocumentoRiga $riga
     * @param MgTipoDocumento[] $tipiByCodice
     * @param MgTipoDocumento[] $tipiById
     * @return MgTipoDocumento|null
     */
    private function tipoRiga($riga, $tipiByCodice, $tipiById)
    {
        if ($riga->codice_tipo && isset($tipiByCodice[$riga->codice_tipo])) {
            return $tipiByCodice[$riga->codice_tipo];
        }

        $testata = $riga->documento;
        if ($testata && isset($tipiById[$testata->id_tipo])) {
            return $tipiById[$testata->id_tipo];
        }

        return null;
    }

    /**
     * Copia la tabella dei movimenti in una tabella di backup con timestamp.
     *
     * @return string nome della tabella di backup
     */
    private function backupTabella()
    {
        $base = 'mg_movimentimagazzino_backup_' . date('Ymd_His');
        $nome = $base;
        $contatore = 1;
        while ($this->checkTableExist($nome)) {
            $contatore++;
            $nome = $base . '_' . $contatore;
        }

        Yii::$app->db->createCommand(
            'SELECT * INTO [dbo].[' . $nome . '] FROM [dbo].[mg_movimentimagazzino]'
        )->execute();

        return $nome;
    }

    private function checkTableExist($table)
    {
        return Yii::$app->db->createCommand(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :t"
        )->bindValue(':t', $table)->queryScalar() > 0;
    }
}
