<?php

namespace app\controllers;

use Yii;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgDcfControllo;
use app\models\MgDcfControlloRiga;
use app\models\MgTipoDocumento;
use app\models\MgAnagrafica;
use app\models\MgMatricola;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * Wizard di controllo (riscontro) delle quantita' dei documenti da fornitore.
 *
 * Flusso in tre passi:
 *  1. Index: elenco dei documenti da fornitore ancora da controllare, con
 *     selezione multipla (anche di fornitori diversi).
 *  2. Controlla: riscontro della q.ta reale per ogni riga dei documenti scelti,
 *     con totali sempre visibili e import della q.ta da file di testo.
 *  3. Riepilogo: sintesi delle differenze e invio email di segnalazione al
 *     fornitore.
 *
 * La selezione corrente e' mantenuta in sessione; i riscontri sono persistiti
 * nelle tabelle mg_dcf_controllo / mg_dcf_controllo_riga.
 */
class DcfController extends Controller
{
    const SESSION_KEY = 'dcf_batch';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'selezione' => ['POST'],
                    'salva' => ['POST'],
                    'invia-email' => ['POST'],
                    'annulla' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Passo 1: elenco documenti da fornitore da controllare (selezione multipla).
     */
    public function actionIndex()
    {
        $idFornitore = Yii::$app->request->get('id_anagrafica');
        $q = trim((string) Yii::$app->request->get('q'));

        return $this->render('index', [
            'documenti' => $this->documentiFornitore($idFornitore, $q),
            'fornitori' => MgAnagrafica::mapForDestinazione(MgTipoDocumento::DEST_FORNITORE),
            'filtri' => ['id_anagrafica' => $idFornitore, 'q' => $q],
            'batch' => $this->batchIds(),
        ]);
    }

    /**
     * Passo 1 -> 2: salva in sessione i documenti scelti e apre il riscontro.
     */
    public function actionSelezione()
    {
        $ids = array_values(array_unique(array_map('intval', array_filter((array) Yii::$app->request->post('docs', [])))));
        $ids = $this->documentiValidi($ids);

        if (empty($ids)) {
            Yii::$app->session->setFlash('error', 'Seleziona almeno un documento da controllare.');
            return $this->redirect(['index']);
        }

        $this->setBatch($ids);
        return $this->redirect(['controlla']);
    }

    /**
     * Passo 2: riscontro delle quantita' dei documenti selezionati.
     */
    public function actionControlla()
    {
        $ids = $this->batchIds();
        if (empty($ids)) {
            Yii::$app->session->setFlash('info', 'Seleziona i documenti da controllare.');
            return $this->redirect(['index']);
        }

        $documenti = $this->caricaDocumenti($ids);
        if (empty($documenti)) {
            $this->clearBatch();
            Yii::$app->session->setFlash('error', "I documenti selezionati non sono piu' disponibili.");
            return $this->redirect(['index']);
        }

        $codici = [];
        foreach ($documenti as $doc) {
            foreach ($doc->righe as $r) {
                if ($r->codice_articolo !== null && $r->codice_articolo !== '') {
                    $codici[] = $r->codice_articolo;
                }
            }
        }
        $barcode = $this->barcodePerCodice($codici);

        return $this->render('controlla', [
            'documenti' => $documenti,
            'riscontri' => $this->riscontriPerDocumenti($ids),
            'barcode' => $barcode,
        ]);
    }

    /**
     * Passo 2 -> 3: salva i riscontri dei documenti selezionati.
     */
    public function actionSalva()
    {
        $ids = $this->batchIds();
        if (empty($ids)) {
            return $this->redirect(['index']);
        }

        $documenti = $this->caricaDocumenti($ids);
        $qta = (array) Yii::$app->request->post('qta', []);
        $azione = Yii::$app->request->post('azione', 'controlla');

        foreach ($documenti as $doc) {
            $controllo = MgDcfControllo::perDocumento($doc->id);
            if ($controllo->isNewRecord) {
                $controllo->created_at = new \yii\db\Expression('GETDATE()');
                $controllo->save(false);
            }

            $perDoc = (array) ($qta[$doc->id] ?? []);
            foreach ($doc->righe as $r) {
                $cr = MgDcfControlloRiga::findOne([
                    'id_controllo' => $controllo->id,
                    'id_riga' => $r->id,
                ]);
                if ($cr === null) {
                    $cr = new MgDcfControlloRiga();
                    $cr->id_controllo = $controllo->id;
                    $cr->id_riga = $r->id;
                    $cr->created_at = new \yii\db\Expression('GETDATE()');
                }
                $cr->qta_documento = (float) $r->qta;

                $valore = $perDoc[$r->id] ?? null;
                if (is_string($valore)) {
                    $valore = trim(str_replace(',', '.', $valore));
                }
                $cr->qta_riscontrata = ($valore === null || $valore === '') ? null : (float) $valore;
                $cr->save(false);
            }

            $controllo->id_utente = Yii::$app->user->id;
            $controllo->aggiornaTotali();
        }

        if ($azione === 'riepilogo' || $this->batchCompleto($ids)) {
            return $this->redirect(['riepilogo']);
        }

        Yii::$app->session->setFlash('warning', 'Riscontro salvato: mancano ancora alcune righe da controllare.');
        return $this->redirect(['controlla']);
    }

    /**
     * Passo 3: riepilogo delle differenze e invio segnalazione al fornitore.
     */
    public function actionRiepilogo()
    {
        $ids = $this->batchIds();
        if (empty($ids)) {
            return $this->redirect(['index']);
        }

        $documenti = $this->caricaDocumenti($ids);
        if (empty($documenti)) {
            $this->clearBatch();
            return $this->redirect(['index']);
        }

        $riscontri = $this->riscontriPerDocumenti($ids);

        // Ricalcola e raggruppa per fornitore.
        $gruppi = [];
        foreach ($documenti as $doc) {
            $controllo = MgDcfControllo::perDocumento($doc->id);
            if ($controllo->isNewRecord) {
                $controllo->created_at = new \yii\db\Expression('GETDATE()');
                $controllo->save(false);
            }
            $controllo->aggiornaTotali();

            $chiave = (int) $doc->id_anagrafica;
            if (!isset($gruppi[$chiave])) {
                $gruppi[$chiave] = [
                    'fornitore' => $doc->anagrafica,
                    'documenti' => [],
                    'completo' => true,
                    'differenze' => 0,
                    'email_inviata' => true,
                ];
            }

            $righeDiff = [];
            foreach ($doc->righe as $r) {
                $cr = $riscontri[$doc->id][$r->id] ?? null;
                $qr = ($cr && $cr->qta_riscontrata !== null && $cr->qta_riscontrata !== '')
                    ? (float) $cr->qta_riscontrata : null;
                $diff = $qr === null ? null : round($qr - (float) $r->qta, 4);
                if ($diff !== null && abs($diff) > 0.0001) {
                    $righeDiff[] = [
                        'riga' => $r,
                        'qta_documento' => (float) $r->qta,
                        'qta_riscontrata' => $qr,
                        'differenza' => $diff,
                    ];
                }
            }

            $gruppi[$chiave]['documenti'][] = [
                'model' => $doc,
                'controllo' => $controllo,
                'righe_differenza' => $righeDiff,
            ];
            $gruppi[$chiave]['completo'] = $gruppi[$chiave]['completo'] && $controllo->isCompleto();
            $gruppi[$chiave]['differenze'] += count($righeDiff);
            $gruppi[$chiave]['email_inviata'] = $gruppi[$chiave]['email_inviata'] && (bool) $controllo->email_inviata;
        }

        return $this->render('riepilogo', [
            'gruppi' => $gruppi,
        ]);
    }

    /**
     * Invia l'email di segnalazione delle quantita' mancanti a un fornitore.
     */
    public function actionInviaEmail()
    {
        $ids = $this->batchIds();
        $idFornitore = (int) Yii::$app->request->post('id_anagrafica');
        if (empty($ids) || !$idFornitore) {
            return $this->redirect(['index']);
        }

        $documenti = array_filter($this->caricaDocumenti($ids), function ($d) use ($idFornitore) {
            return (int) $d->id_anagrafica === $idFornitore;
        });

        if (empty($documenti)) {
            Yii::$app->session->setFlash('error', 'Nessun documento per il fornitore selezionato.');
            return $this->redirect(['riepilogo']);
        }

        $fornitore = MgAnagrafica::findOne($idFornitore);
        $riscontri = $this->riscontriPerDocumenti($ids);

        $blocchi = [];
        $totDifferenza = 0.0;
        foreach ($documenti as $doc) {
            $controllo = MgDcfControllo::perDocumento($doc->id);
            if (!$controllo->isCompleto()) {
                Yii::$app->session->setFlash('error',
                    "Il controllo non e' completo per il documento " . $doc->etichetta . ': riscontrare tutte le righe prima di inviare.');
                return $this->redirect(['controlla']);
            }
            $righe = [];
            foreach ($doc->righe as $r) {
                $cr = $riscontri[$doc->id][$r->id] ?? null;
                $qr = ($cr && $cr->qta_riscontrata !== null && $cr->qta_riscontrata !== '')
                    ? (float) $cr->qta_riscontrata : null;
                $diff = $qr === null ? null : round($qr - (float) $r->qta, 4);
                if ($diff !== null && abs($diff) > 0.0001) {
                    $righe[] = [
                        'riga' => $r,
                        'qta_documento' => (float) $r->qta,
                        'qta_riscontrata' => $qr,
                        'differenza' => $diff,
                    ];
                    $totDifferenza += $diff;
                }
            }
            if (!empty($righe)) {
                $blocchi[] = ['documento' => $doc, 'righe' => $righe];
            }
        }

        if (empty($blocchi)) {
            Yii::$app->session->setFlash('info', 'Nessuna differenza da segnalare per questo fornitore.');
            return $this->redirect(['riepilogo']);
        }

        $destinatari = $this->parseDestinatari(Yii::$app->request->post('destinatari'));
        if (empty($destinatari) && $fornitore && !empty($fornitore->email)) {
            $destinatari = [$fornitore->email];
        }
        if (empty($destinatari)) {
            Yii::$app->session->setFlash('error', 'Indica almeno un indirizzo email valido a cui inviare la segnalazione.');
            return $this->redirect(['riepilogo']);
        }

        $oggetto = trim((string) Yii::$app->request->post('oggetto'));
        if ($oggetto === '') {
            $oggetto = "Segnalazione quantita' mancanti - documenti fornitore";
        }
        $messaggio = trim((string) Yii::$app->request->post('messaggio'));

        try {
            Yii::$app->mailer->compose('dcf/_email_mancanti', [
                'fornitore' => $fornitore,
                'blocchi' => $blocchi,
                'messaggio' => $messaggio,
                'mittente' => Yii::$app->user->identity->username ?? '',
            ])
                ->setTo($destinatari)
                ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                ->setSubject($oggetto)
                ->send();
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', "Errore nell'invio dell'email: " . $e->getMessage());
            return $this->redirect(['riepilogo']);
        }

        foreach ($documenti as $doc) {
            $controllo = MgDcfControllo::perDocumento($doc->id);
            $controllo->email_inviata = 1;
            $controllo->email_destinatari = implode('; ', $destinatari);
            $controllo->email_oggetto = $oggetto;
            $controllo->email_inviata_at = new \yii\db\Expression('GETDATE()');
            $controllo->stato = MgDcfControllo::STATO_INVIATO;
            $controllo->save(false);
        }

        Yii::$app->session->setFlash('success', 'Segnalazione inviata a ' . implode(', ', $destinatari) . '.');
        return $this->redirect(['riepilogo']);
    }

    /**
     * Annulla la selezione corrente.
     */
    public function actionAnnulla()
    {
        $this->clearBatch();
        return $this->redirect(['index']);
    }

    // ---------------------------------------------------------------
    //  Helper
    // ---------------------------------------------------------------

    /**
     * Elenco dei documenti da fornitore ancora da controllare (o completati,
     * se $includiControllati). Filtrabile per fornitore e testo libero.
     */
    private function documentiFornitore($idFornitore = null, $q = '', $includiControllati = false)
    {
        $tipoIds = MgTipoDocumento::find()->select('id')
            ->where(['destinazione' => MgTipoDocumento::DEST_FORNITORE])->column();
        if (empty($tipoIds)) {
            return [];
        }

        $query = MgDocumento::find()
            ->with(['anagrafica', 'dcfControllo'])
            ->where(['id_tipo' => $tipoIds]);

        if (!empty($idFornitore)) {
            $query->andWhere(['id_anagrafica' => (int) $idFornitore]);
        }

        $docs = $query->orderBy(['data' => SORT_DESC, 'numero' => SORT_DESC])->all();
        if (empty($docs)) {
            return [];
        }

        $ids = [];
        foreach ($docs as $d) {
            $ids[] = (int) $d->id;
        }
        $agg = MgDocumentoRiga::find()
            ->select(['id_documento', 'n' => 'COUNT(*)', 'pz' => 'SUM(qta)'])
            ->where(['id_documento' => $ids])
            ->groupBy('id_documento')
            ->asArray()
            ->indexBy('id_documento')
            ->all();

        $q = strtolower(trim((string) $q));
        $out = [];
        foreach ($docs as $d) {
            $c = $d->dcfControllo;
            $stato = $c ? $c->stato : MgDcfControllo::STATO_DA_CONTROLLARE;
            if (!$includiControllati
                && in_array($stato, [MgDcfControllo::STATO_COMPLETATO, MgDcfControllo::STATO_INVIATO], true)) {
                continue;
            }

            $fornitore = $d->anagrafica ? $d->anagrafica->ragione_sociale : '';
            if ($q !== '') {
                $haystack = strtolower($d->etichetta . ' ' . $fornitore . ' ' . (string) $d->descrizione);
                if (strpos($haystack, $q) === false) {
                    continue;
                }
            }

            $righe = $agg[(int) $d->id] ?? null;
            $out[] = [
                'model' => $d,
                'fornitore' => $fornitore,
                'n_righe' => $righe ? (int) $righe['n'] : 0,
                'tot_pezzi' => $righe ? (float) $righe['pz'] : 0.0,
                'tot_documento' => (float) $d->totale,
                'stato' => $stato,
                'stato_label' => MgDcfControllo::opzioniStato()[$stato] ?? $stato,
                'stato_badge' => MgDcfControllo::badgeStato($stato),
                'in_corso' => ($stato === MgDcfControllo::STATO_IN_CORSO),
            ];
        }

        return $out;
    }

    /**
     * Filtra gli id tenendo solo documenti da fornitore esistenti.
     */
    private function documentiValidi(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $tipoIds = MgTipoDocumento::find()->select('id')
            ->where(['destinazione' => MgTipoDocumento::DEST_FORNITORE])->column();
        if (empty($tipoIds)) {
            return [];
        }
        return MgDocumento::find()
            ->select('id')
            ->where(['id' => $ids, 'id_tipo' => $tipoIds])
            ->column();
    }

    /**
     * Carica i documenti nell'ordine degli id indicati, con righe e anagrafica.
     */
    private function caricaDocumenti(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $docs = MgDocumento::find()
            ->with(['righe', 'anagrafica'])
            ->where(['id' => $ids])
            ->all();
        $byId = [];
        foreach ($docs as $d) {
            $byId[(int) $d->id] = $d;
        }
        $out = [];
        foreach ($ids as $id) {
            if (isset($byId[(int) $id])) {
                $out[] = $byId[(int) $id];
            }
        }
        return $out;
    }

    /**
     * Mappa id_documento => [id_riga => MgDcfControlloRiga] per i documenti dati.
     */
    private function riscontriPerDocumenti(array $ids)
    {
        if (empty($ids)) {
            return [];
        }
        $controlli = MgDcfControllo::find()->where(['id_documento' => $ids])->all();
        if (empty($controlli)) {
            return [];
        }
        $idControlli = [];
        $docPerControllo = [];
        foreach ($controlli as $c) {
            $idControlli[] = (int) $c->id;
            $docPerControllo[(int) $c->id] = (int) $c->id_documento;
        }

        $out = [];
        foreach (MgDcfControlloRiga::find()->where(['id_controllo' => $idControlli])->all() as $cr) {
            $idDoc = $docPerControllo[(int) $cr->id_controllo] ?? null;
            if ($idDoc !== null) {
                $out[$idDoc][(int) $cr->id_riga] = $cr;
            }
        }
        return $out;
    }

    /**
     * True se tutti i documenti indicati hanno il controllo completo.
     */
    private function batchCompleto(array $ids)
    {
        $documenti = $this->caricaDocumenti($ids);
        foreach ($documenti as $doc) {
            $controllo = MgDcfControllo::perDocumento($doc->id);
            if ($controllo->isNewRecord) {
                return false;
            }
            $controllo->aggiornaTotali();
            if (!$controllo->isCompleto()) {
                return false;
            }
        }
        return !empty($documenti);
    }

    /**
     * Mappa codice_articolo => [matricole] (i barcode utili all'import).
     */
    private function barcodePerCodice(array $codici)
    {
        $codici = array_values(array_unique(array_filter($codici)));
        if (empty($codici)) {
            return [];
        }
        $out = [];
        foreach (MgMatricola::find()->where(['codice_articolo' => $codici])->all() as $m) {
            if ($m->matricola === null || $m->matricola === '') {
                continue;
            }
            $out[$m->codice_articolo][] = (string) $m->matricola;
        }
        return $out;
    }

    /**
     * Estrae gli indirizzi email validi da una stringa (separatori , ; spazio).
     */
    private function parseDestinatari($raw)
    {
        $parti = preg_split('/[\s,;]+/', (string) $raw);
        $out = [];
        foreach ((array) $parti as $p) {
            $p = trim($p);
            if ($p !== '' && filter_var($p, FILTER_VALIDATE_EMAIL)) {
                $out[] = $p;
            }
        }
        return array_values(array_unique($out));
    }

    private function batchIds()
    {
        $ids = (array) Yii::$app->session->get(self::SESSION_KEY, []);
        return array_values(array_unique(array_map('intval', array_filter($ids))));
    }

    private function setBatch(array $ids)
    {
        Yii::$app->session->set(self::SESSION_KEY, array_values($ids));
    }

    private function clearBatch()
    {
        Yii::$app->session->remove(self::SESSION_KEY);
    }

    protected function findModel($id)
    {
        if (($model = MgDocumento::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Documento non trovato.');
    }
}
