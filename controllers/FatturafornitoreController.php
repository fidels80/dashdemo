<?php

namespace app\controllers;

use Yii;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;
use app\models\MgScadenza;
use app\models\MgFfReclamo;
use app\models\MgTipoDocumento;
use app\models\MgAnagrafica;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * Elenco delle fatture ricevute dai fornitori (tipo documento FAF) con due
 * funzioni per riga:
 *  - blocco delle scadenze (le rate vengono contrassegnate come bloccate);
 *  - invio di un reclamo al fornitore via email, con storico dei reclami.
 *
 * Per la stessa fattura si possono inviare e memorizzare piu' reclami.
 */
class FatturafornitoreController extends Controller
{
    const TIPO_CODICE = 'FAF';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'toggle-blocco' => ['POST'],
                    'invia-reclamo' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Elenco fatture fornitore, con filtri e stato di blocco/reclami.
     */
    public function actionIndex()
    {
        $idFornitore = Yii::$app->request->get('id_anagrafica');
        $q = trim((string) Yii::$app->request->get('q'));
        $soloBloccate = (int) Yii::$app->request->get('solo_bloccate', 0) === 1;

        return $this->render('index', [
            'fatture' => $this->elencoFatture($idFornitore, $q, $soloBloccate),
            'fornitori' => MgAnagrafica::mapForDestinazione(MgTipoDocumento::DEST_FORNITORE),
            'filtri' => ['id_anagrafica' => $idFornitore, 'q' => $q, 'solo_bloccate' => $soloBloccate],
        ]);
    }

    /**
     * Blocca o sblocca le scadenze di una fattura (AJAX).
     */
    public function actionToggleBlocco()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int) Yii::$app->request->post('id_documento');
        $documento = $this->findFattura($id);

        $scadenze = MgScadenza::find()->where(['id_documento' => $documento->id])->all();
        if (empty($scadenze)) {
            return ['success' => false, 'error' => 'Questa fattura non ha scadenze da bloccare.'];
        }

        $richiesto = Yii::$app->request->post('blocca');
        if ($richiesto === null || $richiesto === '') {
            $tutteBloccate = true;
            foreach ($scadenze as $s) {
                if (!$s->bloccata) {
                    $tutteBloccate = false;
                    break;
                }
            }
            $blocca = !$tutteBloccate;
        } else {
            $blocca = (int) $richiesto === 1;
        }

        $attributes = [
            'bloccata' => $blocca ? 1 : 0,
            'bloccata_at' => $blocca ? new \yii\db\Expression('GETDATE()') : null,
            'bloccata_by' => $blocca ? Yii::$app->user->id : null,
        ];
        MgScadenza::updateAll($attributes, ['id_documento' => $documento->id]);

        return [
            'success' => true,
            'bloccata' => $blocca,
            'n_bloccate' => $blocca ? count($scadenze) : 0,
            'n_scadenze' => count($scadenze),
            'message' => $blocca
                ? count($scadenze) . ' scadenze bloccate.'
                : count($scadenze) . ' scadenze sbloccate.',
        ];
    }

    /**
     * Invia un reclamo al fornitore e lo memorizza nello storico (AJAX).
     */
    public function actionInviaReclamo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int) Yii::$app->request->post('id_documento');
        $documento = $this->findFattura($id);
        $fornitore = $documento->anagrafica;

        $destinatari = $this->parseDestinatari(Yii::$app->request->post('destinatari'));
        if (empty($destinatari) && $fornitore && !empty($fornitore->email)) {
            $destinatari = [$fornitore->email];
        }
        if (empty($destinatari)) {
            return ['success' => false, 'error' => 'Indica almeno un indirizzo email valido.'];
        }

        $oggetto = trim((string) Yii::$app->request->post('oggetto'));
        if ($oggetto === '') {
            $oggetto = $this->oggettoPredefinito($documento);
        }

        $messaggio = trim((string) Yii::$app->request->post('messaggio'));
        if ($messaggio === '') {
            return ['success' => false, 'error' => 'Scrivi il testo del reclamo.'];
        }

        $reclamo = new MgFfReclamo();
        $reclamo->id_documento = $documento->id;
        $reclamo->id_utente = Yii::$app->user->id;
        $reclamo->oggetto = $oggetto;
        $reclamo->messaggio = $messaggio;
        $reclamo->destinatari = implode('; ', $destinatari);
        $reclamo->created_at = new \yii\db\Expression('GETDATE()');

        $inviata = false;
        $errore = null;
        try {
            $inviata = $this->inviaMail('fatturafornitore/_email_reclamo', [
                'documento' => $documento,
                'fornitore' => $fornitore,
                'messaggio' => $messaggio,
                'mittente' => Yii::$app->user->identity->username ?? '',
            ], $destinatari, [Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']], $oggetto);
            if (!$inviata) {
                $errore = 'Invio non riuscito.';
            }
        } catch (\Throwable $e) {
            $errore = $e->getMessage();
        }

        $reclamo->esito = $inviata ? 1 : 0;
        $reclamo->errore = $errore !== null ? mb_substr($errore, 0, 500) : null;
        $reclamo->inviata_at = $inviata ? new \yii\db\Expression('GETDATE()') : null;
        $reclamo->save(false);

        if (!$inviata) {
            return ['success' => false, 'error' => "Errore nell'invio dell'email: " . ($errore ?: 'invio non riuscito')];
        }

        return [
            'success' => true,
            'reclamo_id' => (int) $reclamo->id,
            'message' => 'Reclamo inviato a ' . implode(', ', $destinatari) . '.',
        ];
    }

    /**
     * Elenco dei reclami registrati per una fattura (AJAX).
     */
    public function actionReclami()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = (int) Yii::$app->request->get('id_documento');
        $documento = $this->findFattura($id);

        $out = [];
        foreach ($documento->ffReclami as $r) {
            $out[] = [
                'id' => (int) $r->id,
                'oggetto' => $r->oggetto,
                'messaggio' => (string) $r->messaggio,
                'destinatari' => $r->destinatari,
                'esito' => (bool) $r->esito,
                'errore' => $r->errore,
                'utente' => $r->getUtenteLabel(),
                'inviata_at' => $r->inviata_at ? date('d/m/Y H:i', strtotime($r->inviata_at)) : '',
                'created_at' => $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : '',
            ];
        }

        return [
            'success' => true,
            'documento' => $documento->etichetta,
            'fornitore' => $documento->anagrafica ? $documento->anagrafica->ragione_sociale : '',
            'reclami' => $out,
        ];
    }

    // ---------------------------------------------------------------
    //  Helper
    // ---------------------------------------------------------------

    /**
     * Elenco delle fatture fornitore con dati aggregati di righe, scadenze
     * e reclami.
     */
    private function elencoFatture($idFornitore = null, $q = '', $soloBloccate = false)
    {
        $tipo = MgTipoDocumento::findOne(['codice' => self::TIPO_CODICE]);
        if ($tipo === null) {
            return [];
        }

        $query = MgDocumento::find()
            ->with(['anagrafica'])
            ->where(['id_tipo' => $tipo->id]);

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

        $righeAgg = MgDocumentoRiga::find()
            ->select(['id_documento', 'n' => 'COUNT(*)', 'pz' => 'SUM(qta)'])
            ->where(['id_documento' => $ids])
            ->groupBy('id_documento')
            ->asArray()
            ->indexBy('id_documento')
            ->all();

        $scadAgg = MgScadenza::find()
            ->select([
                'id_documento',
                'n' => 'COUNT(*)',
                'bloccate' => 'SUM(CASE WHEN bloccata = 1 THEN 1 ELSE 0 END)',
                'prossima' => "MIN(CASE WHEN stato = 'aperta' THEN data_scadenza END)",
            ])
            ->where(['id_documento' => $ids])
            ->groupBy('id_documento')
            ->asArray()
            ->indexBy('id_documento')
            ->all();

        $reclamiAgg = MgFfReclamo::find()
            ->select(['id_documento', 'n' => 'COUNT(*)', 'ultimo' => 'MAX(created_at)'])
            ->where(['id_documento' => $ids])
            ->groupBy('id_documento')
            ->asArray()
            ->indexBy('id_documento')
            ->all();

        $reclamiPerDoc = [];
        foreach (MgFfReclamo::find()
            ->where(['id_documento' => $ids])
            ->orderBy(['created_at' => SORT_ASC, 'id' => SORT_ASC])
            ->all() as $r) {
            $reclamiPerDoc[(int) $r->id_documento][] = [
                'data' => $r->created_at ? date('d/m/Y H:i', strtotime($r->created_at)) : '',
                'esito' => (bool) $r->esito,
                'utente' => $r->getUtenteLabel(),
                'oggetto' => (string) $r->oggetto,
                'messaggio' => (string) $r->messaggio,
            ];
        }

        $q = strtolower(trim((string) $q));
        $out = [];
        foreach ($docs as $d) {
            $fornitore = $d->anagrafica ? $d->anagrafica->ragione_sociale : '';
            if ($q !== '') {
                $haystack = strtolower($d->etichetta . ' ' . $fornitore . ' ' . (string) $d->descrizione);
                if (strpos($haystack, $q) === false) {
                    continue;
                }
            }

            $righe = $righeAgg[(int) $d->id] ?? null;
            $scad = $scadAgg[(int) $d->id] ?? null;
            $nScadenze = $scad ? (int) $scad['n'] : 0;
            $nBloccate = $scad ? (int) $scad['bloccate'] : 0;
            $bloccata = $nScadenze > 0 && $nBloccate >= $nScadenze;
            if ($soloBloccate && !$bloccata) {
                continue;
            }

            $reclami = $reclamiAgg[(int) $d->id] ?? null;

            $out[] = [
                'model' => $d,
                'fornitore' => $fornitore,
                'fornitore_email' => $d->anagrafica ? $d->anagrafica->email : '',
                'n_righe' => $righe ? (int) $righe['n'] : 0,
                'tot_pezzi' => $righe ? (float) $righe['pz'] : 0.0,
                'totale' => (float) $d->totale,
                'n_scadenze' => $nScadenze,
                'n_bloccate' => $nBloccate,
                'bloccata' => $bloccata,
                'prossima_scadenza' => $scad && !empty($scad['prossima']) ? $scad['prossima'] : null,
                'n_reclami' => $reclami ? (int) $reclami['n'] : 0,
                'ultimo_reclamo' => $reclami && !empty($reclami['ultimo']) ? $reclami['ultimo'] : null,
                'reclami' => $reclamiPerDoc[(int) $d->id] ?? [],
            ];
        }

        return $out;
    }

    /**
     * Oggetto predefinito del reclamo: "Reclamo per vostro documento <num> <data>".
     */
    private function oggettoPredefinito(MgDocumento $documento)
    {
        $data = $documento->data ? date('d/m/Y', strtotime($documento->data)) : '';
        return trim('Reclamo per vostro documento ' . $documento->numero . ' ' . $data);
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

    /**
     * Invia un'email usando il mailer applicativo; ignora le deprecazioni di
     * Swift Mailer su PHP 8.2 per non far fallire l'invio in debug.
     */
    private function inviaMail($view, array $params, array $to, array $from, $subject)
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if ($errno === E_DEPRECATED || $errno === E_USER_DEPRECATED) {
                return true;
            }
            if (Yii::$app->errorHandler !== null) {
                Yii::$app->errorHandler->handleError($errno, $errstr, $errfile, $errline);
            }
            return true;
        });

        try {
            return Yii::$app->mailer->compose($view, $params)
                ->setTo($to)
                ->setFrom($from)
                ->setSubject($subject)
                ->send();
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Trova una fattura fornitore esistente, altrimenti 404.
     */
    private function findFattura($id)
    {
        $tipo = MgTipoDocumento::findOne(['codice' => self::TIPO_CODICE]);
        $model = ($tipo === null) ? null : MgDocumento::findOne([
            'id' => (int) $id,
            'id_tipo' => $tipo->id,
        ]);
        if ($model === null) {
            throw new NotFoundHttpException('Fattura fornitore non trovata.');
        }
        return $model;
    }
}
