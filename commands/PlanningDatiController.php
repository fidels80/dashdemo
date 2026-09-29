<?php

namespace app\commands;

use Yii;
use app\models\Planning;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Popolamento dati di prova del Planning.
 *
 * Genera attivita' realistiche (cantieri, descrizioni, orari, dipendenti, mezzi,
 * ditta esterna, stato di avanzamento) riprendendo come riferimento i dati
 * gia' presenti in tabella, e le salva passando dal model Planning: cosi'
 * vengono scritti anche planning_personale, planning_veicoli e le Presenze
 * automatiche da 8 ore, esattamente come facendo la form.
 *
 * Uso:
 *   php yii planning-dati/seed --da=2026-09-01 --a=2026-12-31 --min=5 --max=8
 *   php yii planning-dati/seed --da=2026-09-01 --a=2026-12-31 --dry=1
 *   php yii planning-dati/pulisci --da=2026-09-01 --a=2026-12-31 --esegui=1
 */
class PlanningDatiController extends Controller
{
    /** @var string data iniziale (Y-m-d) */
    public $da = '2026-09-01';
    /** @var string data finale (Y-m-d) */
    public $a = '2026-12-31';
    /** @var int numero minimo di attivita' per giorno lavorativo */
    public $min = 5;
    /** @var int numero massimo di attivita' per giorno lavorativo */
    public $max = 8;
    /** @var int 1 = non scrive nulla, mostra solo cosa verrebbe inserito */
    public $dry = 0;
    /** @var int 1 = cancella prima le attivita' gia' presenti nel periodo */
    public $pulisci = 0;
    /** @var int seed del generatore casuale (riproducibile) */
    public $seed = 20260901;

    /**
     * Opzioni riconosciute da questo comando.
     * {@inheritdoc}
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['da', 'a', 'min', 'max', 'dry', 'pulisci', 'seed']);
    }

    /** Tipologie di intervento ricorrenti nel planning aziendale. */
    const TIPI = [
        'FACCHINAGGIO',
        'SCARICO + FACCHINAGGIO PORTE',
        'MONTAGGIO PORTE',
        'MONTAGGIO SEDUTE',
        'MONTAGGIO BOX - GIA\' DEL CLIENTE',
        'CONSEGNA E MONTAGGIO ARREDI',
        'consegna 1 seduta',
        'SMONTAGGIO ARREDI',
        'TRASLOCHI AMBIENTI',
        'TRASLOCHI + MONTAGGIO',
        'ARRIVO MATERIALE',
        'RITIRO MATERIALE',
        'RITIRO ATTREZZATURE',
        'POSIZIONAMENTO BANCHI',
        'PULIZIA FINE LAVORO',
        'SOPRALLUOGO TECNICO',
        'VERIFICA A REGIME',
        'RIPRESA LAVORI SOSPESI',
        'ASSISTENZA AL MONTAGGIO',
        'TRASLOCHI NEGOZIO CHIUSO',
    ];

    /** Suffissi aggiunti a circa un quarto delle descrizioni. */
    const SUFFISSI = [
        '',
        '',
        '',
        '',
        ' - FINITO',
        ' - FERMO',
        ' - DA COMPLETARE',
        ' - 2a SEDUTA',
        ' - DA CONFERMARE',
    ];

    /** Orari di inizio usati per le attivita' (minuti dalla mezzanotte). */
    const SLOT_INIZIO = [420, 480, 480, 540, 540, 600, 660, 720, 780, 840, 900];

    /** Corrispondenza stato_completamento <-> stato_completamento_id. */
    const STATI = [
        1 => 'Da Iniziare',
        2 => 'In Corso',
        3 => 'Completato',
        4 => 'Annullato',
    ];

    /** Indirizzo del magazzino, usato per l'attivita' di carico/scarico */
    const MAGAZZINO = 'MAGAZZINO - VIA HONDURAS 8 - POMEZIA  ';

    /**
     * Genera e salva le attivita' nel periodo indicato.
     * @return int
     */
    public function actionSeed()
    {
        $da = $this->dataIniziale();
        $a = $this->dataFinale();
        if ($da > $a) {
            $this->stderr("Intervallo non valido: --da deve precedere --a.\n", Console::FG_RED);
            return ExitCode::USAGE;
        }

        $this->preparaAmbienteConsole();

        mt_srand((int) $this->seed);

        $giorni = $this->giorniLavorativi($da, $a);
        if (empty($giorni)) {
            $this->stdout("Nessun giorno lavorativo nell'intervallo.\n");
            return ExitCode::OK;
        }

        $cantieri = $this->poolCantieri();
        $ditte = $this->poolDitte();
        if (empty($cantieri) || empty($ditte)) {
            $this->stderr("Dati di riferimento insufficienti (cantieri/ditte esterne).\n", Console::FG_RED);
            return ExitCode::USAGE;
        }

        $personale = $this->poolPersonale();
        $veicoli = $this->poolVeicoli();
        $this->stdout(sprintf(
            "Periodo %s / %s - %d giorni lavorativi\nCantieri: %d - Ditte: %d - Dipendenti: %d - Mezzi: %d\n\n",
            $da,
            $a,
            count($giorni),
            count($cantieri),
            count($ditte),
            count($personale),
            count($veicoli)
        ));

        $db = Yii::$app->db;
        if (!$this->dry && $this->pulisci) {
            $this->pulisciPeriodo($da, $a);
        }
        $transazione = $this->dry ? null : $db->beginTransaction();

        try {
        // Occupazione del giorno: id => intervalli [inizio, fine] in minuti.
        $occupati = [];
        $salvate = 0;
        $saltate = 0;
        $presenze = 0;
        $totale = count($giorni);
        $elaborati = 0;

        foreach ($giorni as $giorno) {
            $occupati = [];
            $n = mt_rand((int) $this->min, (int) $this->max);
            $conMagazzino = mt_rand(1, 100) <= 30;

            for ($i = 0; $i < $n; $i++) {
                $attivita = $this->componiAttivita(
                    $giorno,
                    $cantieri,
                    $ditte,
                    $personale,
                    $veicoli,
                    $occupati
                );

                if ($attivita === null) {
                    $saltate++;
                    continue;
                }

                if ($this->dry) {
                    $salvate++;
                    $presenze += count($attivita['personale_ids']);
                    continue;
                }

                $model = $this->salvaAttivita($attivita);
                if ($model === null) {
                    $saltate++;
                    continue;
                }
                $salvate++;
                $presenze += count($attivita['personale_ids']);
                $this->occupa($occupati, $attivita, $model);
            }

            if ($conMagazzino) {
                $this->aggiungiMagazzino($giorno, $salvate, $this->dry);
            }

            $elaborati++;
            if ($elaborati % 10 === 0 || $elaborati === $totale) {
                $this->stdout(sprintf("\r  giorno %d/%d (%s)  attivita': %d", $elaborati, $totale, $giorno, $salvate));
            }
        }
        } catch (\Throwable $e) {
            if ($transazione !== null) {
                $transazione->rollBack();
            }
            $this->stderr("\nOperazione annullata: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "\n";

        if ($transazione !== null) {
            $transazione->commit();
        }

        $prefisso = $this->dry ? '[DRY-RUN] ' : '';
        $this->stdout(sprintf(
            "%sAttivita' pianificate: %d (saltate %d) - presenze previste: %d\n",
            $prefisso,
            $salvate,
            $saltate,
            $presenze
        ));

        if ($this->dry) {
            $this->stdout("Nessuna scrittura eseguita: togli --dry=1 per salvare.\n", Console::FG_YELLOW);
        }

        return ExitCode::OK;
    }

    /**
     * Rimuove le attivita' (e le presenze auto-generate) del periodo indicato.
     * @return int
     */
    public function actionPulisci()
    {
        $da = $this->dataIniziale();
        $a = $this->dataFinale();

        $db = Yii::$app->db;
        $ids = array_column(
            $db->createCommand('SELECT id FROM planning WHERE data_attivita BETWEEN :da AND :a', [':da' => $da, ':a' => $a])->queryAll(),
            'id'
        );

        $this->stdout(sprintf("Attivita' nel periodo %s / %s: %d\n", $da, $a, count($ids)));
        if (empty($ids)) {
            $this->stdout("Niente da cancellare.\n");
            return ExitCode::OK;
        }

        $collegati = $db->createCommand(
            'SELECT COUNT(*) FROM planning_personale WHERE planning_id IN (' . $this->listaIn($ids) . ')'
        )->queryScalar();
        $mezzi = $db->createCommand(
            'SELECT COUNT(*) FROM planning_veicoli WHERE planning_id IN (' . $this->listaIn($ids) . ')'
        )->queryScalar();

        $this->stdout(sprintf("  legami dipendenti: %d - legami mezzi: %d\n", $collegati, $mezzi));

        if (!$this->dry) {
            $this->pulisciPeriodo($da, $a);
            $this->stdout("Cancellazione eseguita.\n", Console::FG_GREEN);
        } else {
            $this->stdout("[DRY-RUN] usa --dry=0 per procedere.\n", Console::FG_YELLOW);
        }

        return ExitCode::OK;
    }

    // ------------------------------------------------------------------
    // Composizione dell'attivita'
    // ------------------------------------------------------------------

    /**
     * Costruisce un'attivita' coerente per il giorno indicato, evitando
     * sovrapposizioni di orario per gli stessi dipendenti/mezzi.
     *
     * @return array|null null se non esiste una fascia libera
     */
    private function componiAttivita($giorno, array $cantieri, array $ditte, array $personale, array $veicoli, array &$occupati)
    {
        $oggi = date('Y-m-d');

        for ($tentativo = 0; $tentativo < 25; $tentativo++) {
            $cantiere = $cantieri[mt_rand(0, count($cantieri) - 1)];
            $inizioMin = self::SLOT_INIZIO[mt_rand(0, count(self::SLOT_INIZIO) - 1)];
            $durata = [90, 120, 120, 150, 180, 210][mt_rand(0, 5)];
            $fineMin = min($inizioMin + $durata, 19 * 60 + 30);
            if ($fineMin - $inizioMin < 60) {
                continue;
            }

            // Distribuzione dei mezzi uguale a quella dei dati reali (mag-lug):
            // 55% attivita' senza mezzo, 40% con un mezzo, 5% con due.
            $estrazione = mt_rand(1, 100);
            $nVeicoli = $estrazione <= 55 ? 0 : ($estrazione <= 95 ? 1 : 2);
            $nPersone = [1, 1, 2, 2, 2, 3, 3, 4][mt_rand(0, 7)];

            $sceltiVeicoli = $this->scegliLiberi($veicoli, 'v', $nVeicoli, $occupati, $inizioMin, $fineMin);
            if ($sceltiVeicoli === null) {
                continue;
            }
            $sceltiPersone = $this->scegliLiberi($personale, 'p', $nPersone, $occupati, $inizioMin, $fineMin);
            if ($sceltiPersone === null) {
                continue;
            }

            // Le attivita' in appalto non hanno dipendenti propri.
            $ditta = null;
            if (mt_rand(1, 100) <= 30) {
                $ditta = $ditte[mt_rand(0, count($ditte) - 1)];
                if (mt_rand(1, 100) <= 75) {
                    $sceltiPersone = [];
                }
            }

            $descrizione = self::TIPI[mt_rand(0, count(self::TIPI) - 1)]
                . self::SUFFISSI[mt_rand(0, count(self::SUFFISSI) - 1)];

            $qtaOperai = null;
            if (mt_rand(1, 100) <= 45) {
                $qtaOperai = empty($sceltiPersone) ? 0 : count($sceltiPersone) + mt_rand(-1, 1);
                $qtaOperai = max(0, $qtaOperai);
            }

            $statoId = $this->statoCasuale($giorno, $oggi);

            return [
                'data_attivita' => $giorno,
                'descrizione' => trim($descrizione),
                'indirizzo' => $cantiere['indirizzo'],
                'cd_cf' => $cantiere['cd_cf'],
                'cd_dosottocommessa' => $this->sottocommessa($giorno),
                'ditta_esterna' => $ditta,
                'ora_inizio' => $this->ora($inizioMin),
                'ora_fine' => $this->ora($fineMin),
                'giro' => $this->giro(),
                'qta_operai' => $qtaOperai,
                'stato_id' => $statoId,
                'personale_ids' => $sceltiPersone,
                'veicoli_ids' => $sceltiVeicoli,
            ];
        }

        return null;
    }

    /**
     * Aggiunge l'attivita' di carico/scarico al magazzino delle 06:00.
     */
    private function aggiungiMagazzino($giorno, &$salvate, $dry)
    {
        // Come nei dati reali il giro al magazzino non ha mezzo assegnato.
        $attivita = [
            'data_attivita' => $giorno,
            'descrizione' => 'MAGAZZINO',
            'indirizzo' => self::MAGAZZINO,
            'cd_cf' => null,
            'cd_dosottocommessa' => $this->sottocommessa($giorno),
            'ditta_esterna' => null,
            'ora_inizio' => $this->ora(360),
            'ora_fine' => $this->ora(420),
            'giro' => 1,
            'qta_operai' => null,
            'stato_id' => $this->statoCasuale($giorno, date('Y-m-d')),
            'personale_ids' => [],
            'veicoli_ids' => [],
        ];

        if ($dry) {
            $salvate++;
            return;
        }

        if ($this->salvaAttivita($attivita) !== null) {
            $salvate++;
        }
    }

    /**
     * Salva l'attivita' usando il model Planning (scrive anche relazioni e presenze).
     * @return Planning|null
     */
    private function salvaAttivita(array $attivita)
    {
        $model = new Planning();
        $model->data_attivita = $attivita['data_attivita'];
        $model->descrizione = $attivita['descrizione'];
        $model->indirizzo = $attivita['indirizzo'];
        $model->cd_cf = $attivita['cd_cf'];
        $model->cd_dosottocommessa = $attivita['cd_dosottocommessa'];
        $model->ditta_esterna = $attivita['ditta_esterna'];
        $model->ora_inizio = $attivita['ora_inizio'];
        $model->ora_fine = $attivita['ora_fine'];
        $model->giro = $attivita['giro'];
        $model->qta_operai = $attivita['qta_operai'];
        $model->stato_completamento = self::STATI[$attivita['stato_id']];
        $model->stato_completamento_id = $attivita['stato_id'];
        $model->updated_at = date('Y-m-d H:i:s');
        $model->personale_ids = $attivita['personale_ids'];
        $model->veicoli_ids = $attivita['veicoli_ids'];

        if (!$model->save()) {
            $this->stderr(
                'Salvataggio fallito: ' . implode(', ', $model->getFirstErrors()) . "\n",
                Console::FG_RED
            );
            return null;
        }

        return $model;
    }

    // ------------------------------------------------------------------
    // Occupazione e filtri di conflitto
    // ------------------------------------------------------------------

    /**
     * Sceglie fino a $quanti elementi della pool liberi nella fascia oraria.
     * @return array|null null se non c'e' posto per almeno uno
     */
    private function scegliLiberi(array $pool, $tipo, $quanti, array $occupati, $inizio, $fine)
    {
        if ($quanti <= 0) {
            return [];
        }

        $scelti = [];
        $candidati = $pool;
        // Mescola i candidati, cosi' si evitano sempre gli stessi dipendenti.
        for ($i = count($candidati) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$candidati[$i], $candidati[$j]] = [$candidati[$j], $candidati[$i]];
        }

        foreach ($candidati as $id) {
            if (count($scelti) >= $quanti) {
                break;
            }
            $chiave = $tipo . $id;
            if ($this->eOccupato($occupati, $chiave, $inizio, $fine)) {
                continue;
            }
            $scelti[] = $id;
        }

        if (empty($scelti)) {
            return null;
        }

        return $scelti;
    }

    private function eOccupato(array $occupati, $chiave, $inizio, $fine)
    {
        if (empty($occupati[$chiave])) {
            return false;
        }
        foreach ($occupati[$chiave] as $intervallo) {
            if ($inizio < $intervallo[1] && $fine > $intervallo[0]) {
                return true;
            }
        }
        return false;
    }

    private function occupa(array &$occupati, array $attivita, Planning $model)
    {
        if (!$model->ora_inizio || !$model->ora_fine) {
            return;
        }
        $inizio = $this->minuti($model->ora_inizio);
        $fine = $this->minuti($model->ora_fine);

        foreach ($attivita['personale_ids'] as $id) {
            $occupati['p' . $id][] = [$inizio, $fine];
        }
        foreach ($attivita['veicoli_ids'] as $id) {
            $occupati['v' . $id][] = [$inizio, $fine];
        }
    }

    // ------------------------------------------------------------------
    // Dati di riferimento
    // ------------------------------------------------------------------

    /**
     * Pool di cantieri: prima le coppie cliente/indirizzo gia' usate nel
     * planning, poi le anagrafiche clienti (db5) come fonte alternativa.
     * @return array
     */
    private function poolCantieri()
    {
        $db = Yii::$app->db;
        $righe = $db->createCommand(
            "SELECT DISTINCT cd_cf, indirizzo FROM planning
             WHERE indirizzo IS NOT NULL AND LEN(indirizzo) BETWEEN 12 AND 200
               AND indirizzo NOT LIKE '%asdas%' AND indirizzo NOT LIKE '%jhh%'"
        )->queryAll();

        $cantieri = [];
        $visti = [];
        foreach ($righe as $r) {
            $indirizzo = $this->pulisceTesto($r['indirizzo']);
            $chiave = mb_strtolower($indirizzo);
            if (isset($visti[$chiave])) {
                continue;
            }
            $visti[$chiave] = true;
            $cantieri[] = [
                'cd_cf' => ($r['cd_cf'] !== null && $r['cd_cf'] !== '') ? $r['cd_cf'] : null,
                'indirizzo' => $indirizzo,
            ];
        }

        try {
            $anagrafiche = Yii::$app->db5->createCommand(
                "SELECT TOP 120 Cd_CF, Descrizione, Indirizzo, Localita FROM cf
                 WHERE Cliente = 1 AND ISNULL(Indirizzo, '') <> '' AND ISNULL(Localita, '') <> ''"
            )->queryAll();
            foreach ($anagrafiche as $r) {
                $indirizzo = $this->pulisceTesto($r['Descrizione'] . ' - ' . $r['Indirizzo'] . ' - ' . $r['Localita']);
                $indirizzo = mb_substr($indirizzo, 0, 255);
                $chiave = mb_strtolower($indirizzo);
                if (isset($visti[$chiave])) {
                    continue;
                }
                $visti[$chiave] = true;
                $cantieri[] = ['cd_cf' => $r['Cd_CF'], 'indirizzo' => $indirizzo];
            }
        } catch (\Throwable $e) {
            $this->stderr("Anagrafiche clienti non disponibili: " . $e->getMessage() . "\n", Console::FG_YELLOW);
        }

        return $cantieri;
    }

    /**
     * Le anagrafiche contengono spazi e ritorni a capo: vengono ridotti a uno
     * solo perche' l'indirizzo in planning e' un campo monolinea.
     * @return string
     */
    private function pulisceTesto($testo)
    {
        return trim(preg_replace('/\s+/u', ' ', (string) $testo));
    }

    /** @return string[] lista pesata di codici ditta esterna (TMG la piu' usata anche nei dati reali) */
    private function poolDitte()
    {
        $codici = Yii::$app->db->createCommand('SELECT codice FROM dittaesterna ORDER BY codice')->queryColumn();
        $pesi = ['TMG SERVICES' => 30, 'SAMBO MONTAGGI' => 20, 'DIT02' => 10, 'SUBIOLI ALLESTIMENTI' => 10, 'TRASLOCHI CD REMOVAL' => 10];
        $pool = [];
        foreach ($codici as $codice) {
            $peso = isset($pesi[$codice]) ? $pesi[$codice] : 10;
            for ($i = 0; $i < $peso; $i++) {
                $pool[] = $codice;
            }
        }
        return $pool;
    }

    /** @return int[] id dei dipendenti attivi */
    private function poolPersonale()
    {
        static $cache = null;
        if ($cache === null) {
            $cache = array_map('intval', Yii::$app->db
                ->createCommand('SELECT id FROM Personale WHERE stato_attivo = 1 ORDER BY id')
                ->queryColumn());
        }
        return $cache;
    }

    /** @return int[] id dei mezzi utilizzabili */
    private function poolVeicoli()
    {
        static $cache = null;
        if ($cache === null) {
            $cache = array_map('intval', Yii::$app->db
                ->createCommand("SELECT id FROM Veicoli WHERE ISNULL(stato_veicolo, 'Disponibile') <> 'In Riparazione' ORDER BY id")
                ->queryColumn());
        }
        return $cache;
    }

    /**
     * Sottocommessa del periodo: la piu' recente presente a db5.
     * @return string|null
     */
    private function sottocommessa($giorno)
    {
        static $perAnno = [];
        $anno = substr($giorno, 0, 4);
        if (!isset($perAnno[$anno])) {
            try {
                $perAnno[$anno] = Yii::$app->db5->createCommand(
                    "SELECT TOP 1 Cd_DOSottoCommessa FROM DOSottoCommessa
                     WHERE Cd_DOSottoCommessa LIKE :pref ORDER BY Cd_DOSottoCommessa DESC",
                    [':pref' => $anno . '-%']
                )->queryScalar();
            } catch (\Throwable $e) {
                $perAnno[$anno] = false;
            }
        }
        return $perAnno[$anno] ?: null;
    }

    /**
     * Stato di avanzamento plausibile rispetto alla data odierna.
     * @return int id in stato_completamento
     */
    private function statoCasuale($giorno, $oggi)
    {
        $estratto = mt_rand(1, 100);
        if ($giorno < $oggi) {
            if ($estratto <= 70) {
                return 3;
            }
            if ($estratto <= 92) {
                return 2;
            }
            return 1;
        }
        if ($giorno === $oggi) {
            if ($estratto <= 40) {
                return 2;
            }
            return 1;
        }
        if ($estratto <= 85) {
            return 1;
        }
        if ($estratto <= 98) {
            return 2;
        }
        return 4;
    }

    /** @return int giro/ordine (1-24), con i giri bassi piu' frequenti */
    private function giro()
    {
        $pesi = [1, 1, 1, 1, 2, 2, 2, 3, 3, 4, 5, 6, 7, 8, 10, 12, 14, 16, 18, 20, 22, 24];
        return $pesi[mt_rand(0, count($pesi) - 1)];
    }

    // ------------------------------------------------------------------
    // Date e utilita'
    // ------------------------------------------------------------------

    /**
     * Elenco dei giorni lavorativi (lun-ven, esclusi festivi) dell'intervallo.
     * @return string[]
     */
    private function giorniLavorativi($da, $a)
    {
        $giorni = [];
        $cur = new \DateTime($da);
        $fine = new \DateTime($a);
        while ($cur <= $fine) {
            $n = (int) $cur->format('N');
            $chiave = $cur->format('Y-m-d');
            if ($n <= 5 && !in_array($chiave, $this->festivi((int) $cur->format('Y')), true)) {
                $giorni[] = $chiave;
            }
            $cur->modify('+1 day');
        }
        return $giorni;
    }

    /**
     * Festivita' nazionali italiane per l'anno indicato.
     * @return string[]
     */
    private function festivi($anno)
    {
        $pasqua = date('Y-m-d', easter_date($anno, true));
        $lunedi = date('Y-m-d', strtotime($pasqua . ' +1 day'));

        $date = [
            "$anno-01-01", "$anno-01-06", $pasqua, $lunedi, "$anno-04-25", "$anno-05-01",
            "$anno-06-02", "$anno-06-24", "$anno-08-15", "$anno-11-01", "$anno-11-04",
            "$anno-12-08", "$anno-12-24", "$anno-12-25", "$anno-12-26", "$anno-12-31",
        ];

        return $date;
    }

    /** Cancella attivita', relazioni, presenze auto-generate e log del periodo. */
    private function pulisciPeriodo($da, $a)
    {
        $db = Yii::$app->db;
        $ids = array_map('intval', $db->createCommand('SELECT id FROM planning WHERE data_attivita BETWEEN :da AND :a', [':da' => $da, ':a' => $a])->queryColumn());
        if (empty($ids)) {
            return;
        }
        $lista = $this->listaIn($ids);

        $db->createCommand('DELETE FROM planning_personale WHERE planning_id IN (' . $lista . ')')->execute();
        $db->createCommand('DELETE FROM planning_veicoli WHERE planning_id IN (' . $lista . ')')->execute();

        // Presenze generate dal planning: si rimuovono solo se marcate come
        // automatiche e non piu' riferite ad attivita' rimaste.
        $db->createCommand(
            "DELETE FROM presenze
             WHERE data_presenza BETWEEN :da AND :a
               AND note LIKE 'Generata automaticamente da Planning ID:%'
               AND NOT EXISTS (
                   SELECT 1 FROM planning_personale pp
                   INNER JOIN planning p ON p.id = pp.planning_id
                   WHERE p.data_attivita = presenze.data_presenza
                     AND pp.personale_id = presenze.personale_id
               )",
            [':da' => $da, ':a' => $a]
        )->execute();

        $db->createCommand('DELETE FROM planning WHERE data_attivita BETWEEN :da AND :a', [':da' => $da, ':a' => $a])->execute();

        $this->pulisciLog($da, $a);
        $this->stdout(sprintf("Cancellate %d attivita' preesistenti nel periodo.\n", count($ids)));
    }

    /**
     * Rimuove dal log le voci di INSERT su planning/presenze del periodo.
     * Le date sono cercate come prefisso mese dentro il JSON dei valori.
     */
    private function pulisciLog($da, $a)
    {
        $mesi = [];
        $cur = new \DateTime($da);
        $fine = new \DateTime($a);
        while ($cur <= $fine) {
            $mesi[] = $cur->format('Y-m');
            $cur->modify('first day of next month');
        }

        $condizioni = [];
        $params = [];
        foreach ($mesi as $i => $mese) {
            $condizioni[] = "(operazione = 'INSERT on planning' AND valore LIKE :p{$i})";
            $condizioni[] = "(operazione = 'INSERT on presenze' AND valore LIKE :s{$i})";
            $params[":p{$i}"] = '%"data_attivita":"' . $mese . '%';
            $params[":s{$i}"] = '%"data_presenza":"' . $mese . '%';
        }

        $n = Yii::$app->db->createCommand(
            'DELETE FROM log WHERE ' . implode(' OR ', $condizioni),
            $params
        )->execute();

        $this->stdout(sprintf("Rimosse %d voci di log.\n", $n));
    }

    /**
     * In console non esiste il componente 'user', usato da LogBehavior:
     * viene registrato uno stub con id 0 per non bloccare i salvataggi.
     */
    private function preparaAmbienteConsole()
    {
        if (!Yii::$app->has('user')) {
            Yii::$app->set('user', ['class' => ConsoleUser::class]);
        }
    }

    private function dataIniziale()
    {
        $v = strtotime($this->da);
        return $v ? date('Y-m-d', $v) : date('Y-m-01');
    }

    private function dataFinale()
    {
        $v = strtotime($this->a);
        return $v ? date('Y-m-d', $v) : date('Y-m-t');
    }

    /** @return string 'H:i:s' */
    private function ora($minuti)
    {
        return sprintf('%02d:%02d:00', intdiv($minuti, 60), $minuti % 60);
    }

    /** @return int minuti dalla mezzanotte */
    private function minuti($ora)
    {
        $parti = explode(':', (string) $ora);
        return ((int) $parti[0]) * 60 + (int) ($parti[1] ?? 0);
    }

    /** @return string lista di interi separati da virgola, per le query IN */
    private function listaIn(array $ids)
    {
        return implode(',', array_map('intval', $ids));
    }
}

/**
 * Stub del componente 'user' per i comandi console (usato da LogBehavior).
 */
class ConsoleUser extends \yii\base\Component
{
    /** @var int id fittizio dello script di riempimento */
    public $id = 0;
}
