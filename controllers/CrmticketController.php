<?php

namespace app\controllers;

use Yii;
use app\models\MgCrmTicket;
use app\models\MgCrmTicketMessaggio;
use app\models\MgCrmAllegato;
use app\models\MgCrmTipo;
use app\models\MgCrmStato;
use app\models\MgCrmPriorita;
use app\models\MgCrmFaq;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgMatricola;
use app\models\MgSottocommessa;
use app\models\Rapportini;
use app\components\AllegatiCrm;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * CRM interno: gestione dei ticket/richieste dei clienti.
 * Gli operatori assegnano, cambiano stato, rispondono, allegano file,
 * indicano il tempo impiegato, chiudono il ticket, lo evadono in un
 * rapportino e lo trasformano in FAQ.
 */
class CrmticketController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'assegna' => ['POST'],
                    'cambia-stato' => ['POST'],
                    'rispondi' => ['POST'],
                    'prendi' => ['POST'],
                    'allega' => ['POST'],
                    'elimina-allegato' => ['POST'],
                    'soluzione' => ['POST'],
                    'chiudi' => ['POST'],
                    'evadi' => ['POST'],
                    'crea-faq' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $get = Yii::$app->request->get();

        $query = MgCrmTicket::find()
            ->with(['tipo', 'stato', 'priorita', 'anagrafica', 'utente', 'assegnato'])
            ->orderBy(['id' => SORT_DESC]);

        if (!empty($get['id_stato'])) {
            $query->andWhere(['id_stato' => (int) $get['id_stato']]);
        }
        if (!empty($get['id_tipo'])) {
            $query->andWhere(['id_tipo' => (int) $get['id_tipo']]);
        }
        if (!empty($get['id_priorita'])) {
            $query->andWhere(['id_priorita' => (int) $get['id_priorita']]);
        }
        if (!empty($get['assegnato_a'])) {
            $query->andWhere(['assegnato_a' => (int) $get['assegnato_a']]);
        }
        if (!empty($get['id_anagrafica'])) {
            $query->andWhere(['id_anagrafica' => (int) $get['id_anagrafica']]);
        }
        if (isset($get['aperto']) && $get['aperto'] !== '') {
            if ($get['aperto'] === '1') {
                $query->andWhere(['id_stato' => $this->statiAperti()]);
            } else {
                $query->andWhere(['id_stato' => $this->statiChiusi()]);
            }
        }
        if (!empty($get['q'])) {
            $q = trim($get['q']);
            $query->andWhere(['or',
                ['like', 'oggetto', $q],
                ['like', 'descrizione', $q],
            ]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'filtri' => $get,
            'tipi' => MgCrmTipo::mapAttivi(),
            'stati' => MgCrmStato::mapAttivi(),
            'priorita' => MgCrmPriorita::mapAttivi(),
            'operatori' => MgCrmTicket::mapOperatori(),
            'anagrafiche' => MgAnagrafica::mapForDestinazione('cliente'),
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
            'stati' => MgCrmStato::mapAttivi(),
            'operatori' => MgCrmTicket::mapOperatori(),
            'messaggio' => new MgCrmTicketMessaggio(),
            'articoli' => MgArticolo::map(),
            'sottocommesse' => $this->mappaSottocommesse(),
        ]);
    }

    public function actionCreate($id_anagrafica = null)
    {
        $model = new MgCrmTicket();
        $model->id_tipo = $this->idByCodice(MgCrmTipo::className(), 'assistenza');
        $model->id_stato = $this->idByCodice(MgCrmStato::className(), 'nuovo');
        $model->id_priorita = $this->idByCodice(MgCrmPriorita::className(), 'normale');
        if ($id_anagrafica) {
            $model->id_anagrafica = (int) $id_anagrafica;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AllegatiCrm::salvaMultipli($model->id, null, Yii::$app->user->id);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'tipi' => MgCrmTipo::mapAttivi(),
            'stati' => MgCrmStato::mapAttivi(),
            'priorita' => MgCrmPriorita::mapAttivi(),
            'anagrafiche' => MgAnagrafica::mapForDestinazione('cliente'),
            'articoli' => MgArticolo::map(),
            'matricole' => MgMatricola::mapAttivi(),
            'operatori' => MgCrmTicket::mapOperatori(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'tipi' => MgCrmTipo::mapAttivi(),
            'stati' => MgCrmStato::mapAttivi(),
            'priorita' => MgCrmPriorita::mapAttivi(),
            'anagrafiche' => MgAnagrafica::mapForDestinazione('cliente'),
            'articoli' => MgArticolo::map(),
            'matricole' => MgMatricola::mapAttivi(),
            'operatori' => MgCrmTicket::mapOperatori(),
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $motivo = null;
        if (!$model->puoEliminare($motivo)) {
            Yii::$app->session->setFlash('error', $motivo);
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Assegna il ticket a un operatore.
     */
    public function actionAssegna($id)
    {
        $model = $this->findModel($id);
        $model->assegnato_a = (int) Yii::$app->request->post('assegnato_a') ?: null;

        if ($model->assegnato_a && !$model->data_presa_carico
            && in_array($model->stato->codice ?? '', ['nuovo', 'aperto'], true)) {
            $model->data_presa_carico = new \yii\db\Expression('GETDATE()');
            $stato = $this->idByCodice(MgCrmStato::className(), 'preso_in_carico');
            if ($stato) {
                $model->id_stato = $stato;
            }
        }
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Ticket assegnato.');

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Reclama il ticket (lo assegna all'utente corrente).
     */
    public function actionPrendi($id)
    {
        $model = $this->findModel($id);
        $model->assegnato_a = Yii::$app->user->id;
        if (!$model->data_presa_carico) {
            $model->data_presa_carico = new \yii\db\Expression('GETDATE()');
        }
        if (in_array($model->stato->codice ?? '', ['nuovo', 'aperto'], true)) {
            $stato = $this->idByCodice(MgCrmStato::className(), 'preso_in_carico');
            if ($stato) {
                $model->id_stato = $stato;
            }
        }
        $model->save(false);
        Yii::$app->session->setFlash('success', 'Ticket preso in carico.');

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Cambia lo stato del ticket (senza richiedere il tempo; per la chiusura
     * usare l'apposita azione "chiudi").
     */
    public function actionCambiaStato($id)
    {
        $model = $this->findModel($id);
        $idStato = (int) Yii::$app->request->post('id_stato');
        $stato = MgCrmStato::findOne($idStato);
        if ($stato) {
            if ($stato->is_chiuso) {
                Yii::$app->session->setFlash('error', 'Per chiudere il ticket usa il pannello di chiusura (indica il tempo impiegato).');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            $model->id_stato = $stato->id;
            $model->data_chiusura = null;
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Stato aggiornato a "' . $stato->descrizione . '".');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Aggiunge un messaggio o una nota interna al ticket, con eventuali allegati.
     */
    public function actionRispondi($id)
    {
        $model = $this->findModel($id);

        $messaggio = new MgCrmTicketMessaggio();
        $messaggio->id_ticket = $model->id;

        if ($messaggio->load(Yii::$app->request->post()) && $messaggio->save()) {
            AllegatiCrm::salvaMultipli($model->id, $messaggio->id, Yii::$app->user->id);
            if ($messaggio->is_soluzione) {
                $this->applicaSoluzione($model, $messaggio);
            }
            Yii::$app->session->setFlash('success', 'Risposta registrata.');
        } else {
            Yii::$app->session->setFlash('error', 'Impossibile salvare il messaggio.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Marca un commento come soluzione (visibile solo agli operatori interni).
     */
    public function actionSoluzione($id)
    {
        $model = $this->findModel($id);
        $messaggio = MgCrmTicketMessaggio::findOne([
            'id' => (int) Yii::$app->request->post('messaggio_id'),
            'id_ticket' => $model->id,
        ]);
        if ($messaggio === null) {
            Yii::$app->session->setFlash('error', 'Commento non trovato.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $this->applicaSoluzione($model, $messaggio);
        Yii::$app->session->setFlash('success', 'Commento marcato come soluzione.');

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Carica allegati di testata sul ticket.
     */
    public function actionAllega($id)
    {
        $model = $this->findModel($id);
        $n = AllegatiCrm::salvaMultipli($model->id, null, Yii::$app->user->id);
        Yii::$app->session->setFlash($n ? 'success' : 'error',
            $n ? $n . ' allegato/i caricato/i.' : 'Nessun file caricato.');

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Scarica / visualizza un allegato.
     */
    public function actionAllegato($id, $download = 0)
    {
        $allegato = MgCrmAllegato::findOne($id);
        if ($allegato === null) {
            throw new NotFoundHttpException('Allegato non trovato.');
        }
        AllegatiCrm::invia($allegato, (bool) $download);
    }

    public function actionEliminaAllegato($id)
    {
        $allegato = MgCrmAllegato::findOne($id);
        if ($allegato === null) {
            throw new NotFoundHttpException('Allegato non trovato.');
        }
        $idTicket = $allegato->id_ticket;
        $allegato->delete();
        Yii::$app->session->setFlash('success', 'Allegato eliminato.');

        return $this->redirect(['view', 'id' => $idTicket]);
    }

    /**
     * Chiude il ticket indicando il tempo impiegato e la soluzione.
     * Se richiesto crea anche la FAQ.
     */
    public function actionChiudi($id)
    {
        $model = $this->findModel($id);
        $tempo = Yii::$app->request->post('tempo_impiegato_ore');
        $soluzione = trim((string) Yii::$app->request->post('soluzione'));

        if ($tempo === null || $tempo === '' || (float) $tempo < 0) {
            Yii::$app->session->setFlash('error', 'Indica il tempo impiegato (in ore) per chiudere il ticket.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->tempo_impiegato_ore = (float) $tempo;
        if ($soluzione !== '') {
            $model->soluzione = $soluzione;
        } elseif ($model->messaggioSoluzione) {
            $model->soluzione = $model->messaggioSoluzione->messaggio;
        }
        $statoChiuso = MgCrmStato::findOne(['codice' => 'chiuso']);
        if ($statoChiuso) {
            $model->id_stato = $statoChiuso->id;
        }
        $model->data_chiusura = new \yii\db\Expression('GETDATE()');
        $model->save(false);

        if (Yii::$app->request->post('crea_faq')) {
            $this->creaFaqDaTicket($model);
            Yii::$app->session->setFlash('success', 'Ticket chiuso e FAQ creata.');
        } else {
            Yii::$app->session->setFlash('success', 'Ticket chiuso. Puoi generare la FAQ o evadere il rapportino.');
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Evade il ticket in un rapportino (articolo + tempo per risolvere).
     */
    public function actionEvadi($id)
    {
        $model = $this->findModel($id);

        $codiceArticolo = Yii::$app->request->post('cd_art');
        $articolo = null;
        if (Yii::$app->request->post('id_articolo')) {
            $articolo = MgArticolo::findOne((int) Yii::$app->request->post('id_articolo'));
        } elseif ($model->articolo) {
            $articolo = $model->articolo;
        }
        if ($articolo) {
            $codiceArticolo = $articolo->codice;
        }

        $tempo = Yii::$app->request->post('tempo_impiegato_ore', $model->tempo_impiegato_ore);
        if ($tempo === null || $tempo === '' || (float) $tempo <= 0) {
            Yii::$app->session->setFlash('error', 'Indica il tempo impiegato (in ore) per evadere il rapportino.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        if (empty($codiceArticolo)) {
            Yii::$app->session->setFlash('error', 'Indica l\'articolo per il rapportino.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $rapportino = $this->creaRapportino(
            $model,
            $articolo,
            $codiceArticolo,
            (float) $tempo,
            Yii::$app->request->post('commessa'),
            Yii::$app->request->post('data')
        );

        if ($rapportino === null) {
            Yii::$app->session->setFlash('error', 'Impossibile creare il rapportino.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->id_rapportino = $rapportino->id;
        $model->evaso = 1;
        if (empty($model->tempo_impiegato_ore)) {
            $model->tempo_impiegato_ore = (float) $tempo;
        }
        $model->save(false);

        Yii::$app->session->setFlash('success', 'Rapportino ' . $rapportino->id . ' creato dal ticket.');
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Trasforma il ticket in FAQ.
     */
    public function actionCreaFaq($id)
    {
        $model = $this->findModel($id);
        $faq = $this->creaFaqDaTicket($model);
        if ($faq === null) {
            Yii::$app->session->setFlash('error', 'Impossibile creare la FAQ.');
            return $this->redirect(['view', 'id' => $model->id]);
        }
        Yii::$app->session->setFlash('success', 'FAQ creata dal ticket.');
        return $this->redirect(['crmfaq/view', 'id' => $faq->id]);
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    /**
     * Imposta un commento come unica soluzione del ticket e ne copia il testo.
     */
    private function applicaSoluzione(MgCrmTicket $ticket, MgCrmTicketMessaggio $messaggio)
    {
        MgCrmTicketMessaggio::updateAll(['is_soluzione' => 0], ['id_ticket' => $ticket->id]);
        $messaggio->is_soluzione = 1;
        $messaggio->save(false);

        $ticket->soluzione = $messaggio->messaggio;
        $ticket->save(false, ['soluzione', 'updated_at']);
    }

    /**
     * Crea (o restituisce) la FAQ del ticket.
     *
     * @return MgCrmFaq|null
     */
    private function creaFaqDaTicket(MgCrmTicket $ticket)
    {
        if ($ticket->faq !== null) {
            return $ticket->faq;
        }
        $faq = MgCrmFaq::daTicket($ticket);
        return $faq->save() ? $faq : null;
    }

    /**
     * Crea il rapportino a partire dal ticket.
     *
     * @return Rapportini|null
     */
    private function creaRapportino(MgCrmTicket $ticket, $articolo, $codiceArticolo, $tempoOre, $commessa, $data)
    {
        $data = $data ?: date('Y-m-d');
        $ts = strtotime($data) ?: time();

        $rapportino = new Rapportini();
        $rapportino->id = $this->guid();
        $rapportino->cd_cli = $ticket->anagrafica ? $ticket->anagrafica->codice : null;
        $rapportino->altcli = $ticket->anagrafica ? $ticket->anagrafica->codice : null;
        $rapportino->commessa = $commessa ?: $this->primaSottocommessa();
        $rapportino->cd_art = $codiceArticolo;
        $rapportino->des_art = $articolo ? $articolo->descrizione : null;
        $rapportino->qta = 1;
        $rapportino->data = date('Y-m-d\TH:i:s', $ts);
        $rapportino->ora_in = '08:00:00';
        $rapportino->ora_out = $this->oraFine($tempoOre);
        $rapportino->userid = Yii::$app->user->id;
        $rapportino->note = 'Ticket ' . $ticket->etichetta . ' - ' . $ticket->oggetto
            . ($ticket->soluzione ? "\nSoluzione: " . $ticket->soluzione : '');
        $rapportino->evaso = 0;
        // La colonna "numero" e' IDENTITY: viene assegnata da SQL Server.

        return $rapportino->save(false) ? $rapportino : null;
    }

    private function oraFine($ore)
    {
        $minuti = (int) round(((float) $ore) * 60) + 480; // 08:00
        $h = intdiv($minuti, 60) % 24;
        $m = $minuti % 60;
        return sprintf('%02d:%02d:00', $h, $m);
    }

    private function mappaSottocommesse()
    {
        return ArrayHelper::map(
            MgSottocommessa::find()->orderBy(['codice' => SORT_ASC])->all(),
            'codice',
            'codice'
        );
    }

    private function primaSottocommessa()
    {
        $codice = MgSottocommessa::find()->orderBy(['codice' => SORT_ASC])->select('codice')->scalar();
        return $codice ?: 'CRM';
    }

    private function guid()
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4)));
    }

    private function statiAperti()
    {
        return array_map('intval', MgCrmStato::find()->select('id')->where(['is_chiuso' => 0])->column());
    }

    private function statiChiusi()
    {
        return array_map('intval', MgCrmStato::find()->select('id')->where(['is_chiuso' => 1])->column());
    }

    private function idByCodice($class, $codice)
    {
        $row = $class::find()->select('id')->where(['codice' => $codice])->scalar();
        return $row ? (int) $row : null;
    }

    protected function findModel($id)
    {
        if (($model = MgCrmTicket::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Ticket non trovato.');
    }
}
