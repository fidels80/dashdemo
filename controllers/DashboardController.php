<?php

namespace app\controllers;

use Yii;
use app\components\AccessControl;
use yii\web\Controller;

/**
 * DashboardController — pagina di ingresso dopo il login.
 *
 * Riepilogo in un colpo d'occhio: scadenze, ordini e giacenze del
 * microgestionale (tabelle mg_*), planning/presenze, to-do e Vtiger.
 * Sola lettura: ogni card rimanda alla pagina completa di dettaglio.
 *
 * Le sezioni visibili dipendono dai permessi dell'utente sulla pagina
 * di destinazione: chi non puo' aprire quella pagina non vede la card
 * (e non viene nemmeno eseguita la query relativa).
 */
class DashboardController extends Controller
{
    /**
     * Cruscotto riassuntivo.
     *
     * @return string
     */
    public function actionIndex()
    {
        $vis = $this->visibilitaSezioni();

        return $this->render('index', [
            'vis'  => $vis,
            'dati' => [
                'scadenze' => $vis['scadenze'] ? $this->datiScadenze() : null,
                'ordini'   => $vis['ordini'] ? $this->datiOrdini() : null,
                'giacenze' => $vis['giacenze'] ? $this->datiGiacenze() : null,
                'planning' => ($vis['planning'] || $vis['presenze'])
                    ? $this->datiPlanning($vis['planning'], $vis['presenze']) : null,
                'todo'     => $vis['todo'] ? $this->datiTodo() : null,
                'vtiger'   => $vis['vtiger'] ? $this->datiVtiger() : null,
            ],
        ]);
    }

    /**
     * Visibilita' di ogni sezione: coincide con il permesso di aprire
     * la pagina a cui la sezione rimanda.
     *
     * @return array
     */
    private function visibilitaSezioni()
    {
        $u = Yii::$app->user->identity;

        return [
            'scadenze' => AccessControl::allowed('mgdocumento', 'index', $u),
            'ordini'   => AccessControl::allowed('mgdocumento', 'index', $u),
            'giacenze' => AccessControl::allowed('mgarticolo', 'index', $u),
            'planning' => AccessControl::allowed('planning', 'index', $u),
            'presenze' => AccessControl::allowed('presenze', 'index', $u),
            'todo'     => AccessControl::allowed('todomain', 'board', $u),
            'vtiger'   => AccessControl::allowed('vtiger', 'ticket', $u),
        ];
    }

    /**
     * Scadenze aperte: scadute, prossime 30 giorni e prime 6 rate.
     */
    private function datiScadenze()
    {
        $oggi = date('Y-m-d');

        $r = Yii::$app->db->createCommand("
            SELECT
                SUM(CASE WHEN data_scadenza < :d1 THEN 1 ELSE 0 END) AS scadute,
                SUM(CASE WHEN data_scadenza >= :d2 AND data_scadenza < :d3 THEN 1 ELSE 0 END) AS prossime,
                SUM(CASE WHEN data_scadenza < :d4 THEN importo ELSE 0 END) AS importo_scadute,
                SUM(CASE WHEN data_scadenza >= :d5 AND data_scadenza < :d6 THEN importo ELSE 0 END) AS importo_prossime
            FROM mg_scadenza
            WHERE stato = 'aperta'
        ", [
            ':d1' => $oggi,
            ':d2' => $oggi,
            ':d3' => date('Y-m-d', strtotime('+30 days')),
            ':d4' => $oggi,
            ':d5' => $oggi,
            ':d6' => date('Y-m-d', strtotime('+30 days')),
        ])->queryOne();

        $lista = Yii::$app->db->createCommand("
            SELECT TOP 6
                s.data_scadenza, s.importo, s.progressivo,
                d.id AS id_documento, d.codice_tipo, d.anno, d.numero, d.suffisso,
                a.ragione_sociale
            FROM mg_scadenza s
            JOIN mg_documento d ON d.id = s.id_documento
            LEFT JOIN mg_anagrafica a ON a.id = d.id_anagrafica
            WHERE s.stato = 'aperta'
            ORDER BY s.data_scadenza ASC, s.id ASC
        ")->queryAll();

        return [
            'scadute'         => (int) ($r['scadute'] ?? 0),
            'prossime'        => (int) ($r['prossime'] ?? 0),
            'importo_scadute' => (float) ($r['importo_scadute'] ?? 0),
            'importo_prossime'=> (float) ($r['importo_prossime'] ?? 0),
            'lista'           => $lista,
        ];
    }

    /**
     * Ordini aperti (tipi con "varia ordinato" attivo, non chiusi),
     * quantità ancora da evadere e ultimi 6 ordini.
     */
    private function datiOrdini()
    {
        $sintesi = Yii::$app->db->createCommand("
            SELECT t.destinazione, COUNT(*) AS n, ISNULL(SUM(d.totale), 0) AS totale
            FROM mg_documento d
            JOIN mg_tipo_documento t ON t.id = d.id_tipo
            WHERE t.varia_ordinato <> 'nessuno'
              AND d.stato <> 'chiuso'
            GROUP BY t.destinazione
        ")->queryAll();

        $cliente = ['n' => 0, 'totale' => 0];
        $fornitore = ['n' => 0, 'totale' => 0];
        foreach ($sintesi as $row) {
            $voce = ['n' => (int) $row['n'], 'totale' => (float) $row['totale']];
            if ($row['destinazione'] === 'fornitore') {
                $fornitore = $voce;
            } else {
                $cliente = $voce;
            }
        }

        $qta = Yii::$app->db->createCommand("
            SELECT
                ISNULL(SUM(CASE WHEN qta_ordinato > 0 THEN qta_ordinato END), 0) AS da_evadere,
                ISNULL(SUM(CASE WHEN qta_ordinato < 0 THEN -qta_ordinato END), 0) AS verso_fornitore,
                ISNULL(SUM(CASE WHEN qta_impegnato > 0 THEN qta_impegnato END), 0) AS impegnato
            FROM mg_movimentimagazzino
        ")->queryOne();

        $righe = Yii::$app->db->createCommand("
            SELECT TOP 6
                d.id, d.data, d.anno, d.numero, d.suffisso, d.codice_tipo, d.stato, d.totale,
                a.ragione_sociale, t.destinazione
            FROM mg_documento d
            JOIN mg_tipo_documento t ON t.id = d.id_tipo
            LEFT JOIN mg_anagrafica a ON a.id = d.id_anagrafica
            WHERE t.varia_ordinato <> 'nessuno'
              AND d.stato <> 'chiuso'
            ORDER BY d.data DESC, d.id DESC
        ")->queryAll();

        return [
            'cliente'          => $cliente,
            'fornitore'        => $fornitore,
            'da_evadere'       => (float) ($qta['da_evadere'] ?? 0),
            'verso_fornitore'  => (float) ($qta['verso_fornitore'] ?? 0),
            'impegnato'        => (float) ($qta['impegnato'] ?? 0),
            'lista'            => $righe,
        ];
    }

    /**
     * Giacenze derivate dai movimenti: articoli sotto zero e peggiori 6.
     */
    private function datiGiacenze()
    {
        $sintesi = Yii::$app->db->createCommand("
            SELECT
                COUNT(*) AS sotto_zero,
                (SELECT COUNT(DISTINCT codice_articolo) FROM mg_movimentimagazzino) AS movimentati,
                (SELECT COUNT(*) FROM mg_articolo) AS catalogo
            FROM (
                SELECT codice_articolo
                FROM mg_movimentimagazzino
                GROUP BY codice_articolo
                HAVING ISNULL(SUM(qta_movimento), 0) < 0
            ) x
        ")->queryOne();

        $lista = Yii::$app->db->createCommand("
            SELECT TOP 6
                m.codice_articolo,
                a.descrizione,
                MIN(m.um) AS um,
                ISNULL(SUM(m.qta_movimento), 0) AS giacenza
            FROM mg_movimentimagazzino m
            LEFT JOIN mg_articolo a ON a.codice = m.codice_articolo
            GROUP BY m.codice_articolo, a.descrizione
            HAVING ISNULL(SUM(m.qta_movimento), 0) < 0
            ORDER BY ISNULL(SUM(m.qta_movimento), 0) ASC
        ")->queryAll();

        return [
            'sotto_zero' => (int) ($sintesi['sotto_zero'] ?? 0),
            'movimentati'=> (int) ($sintesi['movimentati'] ?? 0),
            'catalogo'   => (int) ($sintesi['catalogo'] ?? 0),
            'lista'      => $lista,
        ];
    }

    /**
     * Attività di planning di oggi e della settimana, presenze del giorno.
     *
     * I due sotto-blocchi vengono calcolati solo se l'utente li puo' leggere.
     *
     * @param bool $conPlanning attività di planning leggibili
     * @param bool $conPresenze presenze leggibili
     */
    private function datiPlanning($conPlanning, $conPresenze)
    {
        $oggi = date('Y-m-d');

        $oggi_n = $conPlanning
            ? (int) Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM planning WHERE data_attivita = :d1",
                [':d1' => $oggi]
            )->queryScalar()
            : null;

        $settimana = $conPlanning
            ? (int) Yii::$app->db->createCommand(
                "SELECT COUNT(*) FROM planning WHERE data_attivita BETWEEN :d1 AND :d2",
                [':d1' => $oggi, ':d2' => date('Y-m-d', strtotime('+7 days'))]
            )->queryScalar()
            : null;

        $presenze = null;
        $ore = null;
        $persone = null;
        if ($conPresenze) {
            $r = Yii::$app->db->createCommand("
                SELECT
                    COUNT(*) AS n,
                    ISNULL(SUM(ore_lavorate), 0) AS ore,
                    COUNT(DISTINCT personale_id) AS persone
                FROM presenze
                WHERE data_presenza = :d1
            ", [':d1' => $oggi])->queryOne();
            $presenze = (int) ($r['n'] ?? 0);
            $ore = (float) ($r['ore'] ?? 0);
            $persone = (int) ($r['persone'] ?? 0);
        }

        $lista = $conPlanning ? Yii::$app->db->createCommand("
            SELECT TOP 6
                p.ora_inizio, p.ora_fine, p.stato_completamento, p.descrizione,
                LTRIM(RTRIM(ISNULL(pe.nome, ''))) + ' ' + LTRIM(RTRIM(ISNULL(pe.cognome, ''))) AS persona,
                v.targa
            FROM planning p
            LEFT JOIN personale pe ON pe.id = p.personale_id
            LEFT JOIN veicoli v ON v.id = p.veicolo_id
            WHERE p.data_attivita = :oggi
            ORDER BY p.ora_inizio ASC, p.id ASC
        ", [':oggi' => $oggi])->queryAll() : [];

        return [
            'oggi'          => $oggi_n,
            'settimana'     => $settimana,
            'presenze'      => $presenze,
            'persone'       => $persone,
            'ore'           => $ore,
            'con_planning'  => $conPlanning,
            'con_presenze'  => $conPresenze,
            'lista'         => $lista,
        ];
    }

    /**
     * To-do per stato (join su to_do_stato) e prime 5 scadenze aperte.
     */
    private function datiTodo()
    {
        $stati = [];
        foreach (Yii::$app->db->createCommand("
            SELECT stato, COUNT(*) AS n FROM to_do_main GROUP BY stato
        ")->queryAll() as $row) {
            $stati[trim((string) $row['stato'])] = (int) $row['n'];
        }

        $etichette = [];
        foreach (\app\models\Todostato::find()->all() as $s) {
            $etichette[(string) $s->id] = $s->stato;
        }

        $scaduti = Yii::$app->db->createCommand("
            SELECT COUNT(*) FROM to_do_main
            WHERE data_scadenza < GETDATE()
              AND stato IN ('1', '3')
        ")->queryScalar();

        $lista = Yii::$app->db->createCommand("
            SELECT TOP 5
                descrizione, data_scadenza, stato, priorita, progresso
            FROM to_do_main
            WHERE stato IN ('1', '3')
            ORDER BY
                CASE WHEN TRY_CAST(LTRIM(RTRIM(priorita)) AS INT) IS NULL
                     THEN 9 ELSE TRY_CAST(LTRIM(RTRIM(priorita)) AS INT) END ASC,
                CASE WHEN data_scadenza IS NULL THEN 1 ELSE 0 END ASC,
                data_scadenza ASC
        ")->queryAll();

        $conteggi = [];
        foreach ($stati as $id => $n) {
            $conteggi[] = [
                'label' => $etichette[$id] ?? ('Stato ' . $id),
                'n'     => $n,
            ];
        }

        return [
            'stati'   => $conteggi,
            'aperti'  => (int) ($stati['1'] ?? 0),
            'attesa'  => (int) ($stati['3'] ?? 0),
            'scaduti' => (int) $scaduti,
            'lista'   => $lista,
        ];
    }

    /**
     * Vtiger (db6, MySQL): ticket non chiusi e progetti in corso.
     * Se il database esterno non è raggiungibile la card segnala l'indisponibilità.
     */
    private function datiVtiger()
    {
        try {
            $ticket = Yii::$app->db6->createCommand("
                SELECT COUNT(*) FROM vtiger_troubletickets WHERE status <> 'Closed'
            ")->queryScalar();

            $progetti = Yii::$app->db6->createCommand("
                SELECT COUNT(*) FROM vtiger_project
                WHERE projectstatus IN ('in progress', 'initiated')
            ")->queryScalar();

            $utenti = Yii::$app->db6->createCommand("
                SELECT COUNT(*) FROM vtiger_users WHERE status = 'Active'
            ")->queryScalar();

            return [
                'disponibile' => true,
                'ticket'      => (int) $ticket,
                'progetti'    => (int) $progetti,
                'utenti'      => (int) $utenti,
            ];
        } catch (\Exception $e) {
            return [
                'disponibile' => false,
                'ticket'      => 0,
                'progetti'    => 0,
                'utenti'      => 0,
            ];
        }
    }
}
