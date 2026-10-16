<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;
use app\models\User;
use app\models\MgAnagrafica;
use app\models\MgCrmTipo;
use app\models\MgCrmStato;
use app\models\MgCrmPriorita;
use app\models\MgCrmTicket;
use app\models\MgCrmTicketMessaggio;
use app\models\MgCrmUtenteAnagrafica;
use app\models\MgArticolo;
use app\models\MgMatricola;
use app\models\MgDocumento;

/**
 * Dati dimostrativi del CRM: utenti esterni (portale clienti) collegati alle
 * anagrafiche mg e ticket di vario tipo.
 *
 * Uso:
 *   php yii crmdemo                      (20 utenti + 300 ticket)
 *   php yii crmdemo --utenti=20 --ticket=300
 *   php yii crmdemo --reset=1            (ricrea i dati demo)
 */
class CrmdemoController extends Controller
{
    /** Numero di utenti esterni da creare. */
    public $utenti = 20;

    /** Numero di ticket da creare. */
    public $ticket = 300;

    /** Password assegnata agli utenti demo. */
    public $password = 'Cliente2026!';

    /** Se 1, elimina utenti demo e ticket esistenti prima di generare. */
    public $reset = 0;

    public function options($actionID)
    {
        return ['utenti', 'ticket', 'password', 'reset'];
    }

    public function optionAliases()
    {
        return ['u' => 'utenti', 't' => 'ticket', 'p' => 'password', 'r' => 'reset'];
    }

    public function actionIndex()
    {
        $this->actionUtenti();
        $this->actionTicket();
        return ExitCode::OK;
    }

    /**
     * Crea (o aggiorna) gli utenti esterni e li collega alle anagrafiche.
     */
    public function actionUtenti()
    {
        $clienti = (new Query())
            ->select(['id', 'codice', 'ragione_sociale', 'email'])
            ->from('{{%mg_anagrafica}}')
            ->where(['is_cliente' => 1])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        if (empty($clienti)) {
            $this->stderr("Nessuna anagrafica cliente trovata in mg_anagrafica.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->reset) {
            $userIds = $this->utentiDemoIds();
            if (!empty($userIds)) {
                MgCrmTicket::deleteAll(['id_utente' => $userIds]);
                MgCrmUtenteAnagrafica::deleteAll(['user_id' => $userIds]);
                foreach (User::find()->where(['id' => $userIds])->all() as $u) {
                    $u->delete();
                }
                $this->stdout('Rimossi ' . count($userIds) . " utenti demo esistenti.\n");
            }
        }

        $nomi = ['Marco', 'Luca', 'Giuseppe', 'Anna', 'Paola', 'Stefano', 'Francesca', 'Andrea',
            'Chiara', 'Roberto', 'Elena', 'Matteo', 'Silvia', 'Alessandro', 'Laura', 'Davide',
            'Marta', 'Simone', 'Valentina', 'Fabio'];
        $cognomi = ['Rossi', 'Bianchi', 'Ferrari', 'Russo', 'Romano', 'Colombo', 'Ricci', 'Marino',
            'Greco', 'Bruno', 'Gallo', 'Conti', 'Costa', 'Giordano', 'Mancini', 'Rizzo',
            'Lombardi', 'Barbieri', 'Moretti', 'Fontana'];

        // Pool di aziende: distribuisce gli utenti in modo che ogni azienda
        // ne abbia piu' di uno (es. 20 utenti su ~10 aziende).
        $nAziende = max(1, min(count($clienti), (int) ceil($this->utenti / 2)));

        $creati = 0;
        for ($i = 1; $i <= (int) $this->utenti; $i++) {
            $cliente = $clienti[($i - 1) % $nAziende];
            $username = sprintf('cliente%02d', $i);
            $email = sprintf('cliente%02d@demo.local', $i);
            $nome = $nomi[($i - 1) % count($nomi)];
            $cognome = $cognomi[($i - 1) % count($cognomi)];

            $user = User::findOne(['username' => $username]);
            if ($user === null) {
                $user = new User();
                $user->username = $username;
                $user->auth_key = Yii::$app->security->generateRandomString();
                $user->setPassword($this->password);
            }
            $user->email = $email;
            $user->status = User::STATUS_ACTIVE;
            $user->level = 10;
            $user->is_esterno = 1;
            $user->cd_cli = $cliente['codice'];
            $user->save(false);

            // Collega l'azienda principale.
            $this->collega($user->id, [(int) $cliente['id']]);

            // Un utente su cinque e' collegato anche a una seconda azienda.
            if ($i % 5 === 0 && count($clienti) > 1) {
                $altra = $clienti[($i + 3) % count($clienti)];
                if ((int) $altra['id'] !== (int) $cliente['id']) {
                    MgCrmUtenteAnagrafica::findOrCreate($user->id, (int) $altra['id']);
                }
            }

            $this->stdout("Utente {$username} -> {$cliente['ragione_sociale']} ({$nome} {$cognome})\n");
            $creati++;
        }

        $this->stdout("\nCreati/aggiornati {$creati} utenti esterni. Password: {$this->password}\n");
        $this->stdout('Collegamenti utente-azienda: '
            . (new Query())->from('{{%mg_crm_utente_anagrafica}}')->count() . "\n");

        return ExitCode::OK;
    }

    /**
     * Genera i ticket distribuiti tra i vari tipi/stati/priorita'.
     */
    public function actionTicket()
    {
        $tipi = MgCrmTipo::find()->where(['attivo' => 1])->all();
        $stati = MgCrmStato::find()->where(['attivo' => 1])->all();
        $priorita = MgCrmPriorita::find()->where(['attivo' => 1])->all();

        if (empty($tipi) || empty($stati) || empty($priorita)) {
            $this->stderr("Lookup CRM mancanti: esegui prima le migrazioni.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $esterni = User::find()->where(['is_esterno' => 1])->all();
        if (empty($esterni)) {
            $this->stderr("Nessun utente esterno: esegui prima 'php yii crmdemo/utenti'.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $operatori = User::find()
            ->where(['status' => User::STATUS_ACTIVE])
            ->andWhere(['>=', 'level', 70])
            ->andWhere(['not', ['is_esterno' => 1]])
            ->all();
        if (empty($operatori)) {
            $operatori = User::find()->where(['>=', 'level', 50])->all();
        }

        $articoli = MgArticolo::find()->select(['id', 'codice'])->limit(200)->all();
        $matricole = MgMatricola::find()->select(['id', 'id_articolo'])->limit(200)->all();
        $documenti = MgDocumento::find()->select(['id', 'id_anagrafica'])->limit(500)->all();

        if ($this->reset) {
            $ids = $this->utentiDemoIds();
            $n = MgCrmTicket::deleteAll(['id_utente' => $ids ?: [-1]]);
            $this->stdout("Rimossi {$n} ticket demo esistenti.\n");
        }

        if ($this->ticket <= 0) {
            return ExitCode::OK;
        }

        $oggetti = [
            'assistenza' => ['Guasto macchina', 'Manutenzione programmata', 'Installazione nuova unità', 'Errore in fase di avvio', 'Rumore anomalo durante il funzionamento'],
            'amministrativa' => ['Richiesta copia fattura', 'Chiarimento su una scadenza', 'Copia documento di trasporto', 'Verifica pagamento', 'Richiesta nota di credito'],
            'preventivo' => ['Preventivo fornitura', 'Preventivo manutenzione annuale', 'Preventivo ricambi', 'Preventivo nuovo impianto', 'Preventivo servizio'],
            'commerciale' => ['Informazioni su un prodotto', 'Disponibilità e tempi di consegna', 'Richiesta scheda tecnica', 'Condizioni commerciali'],
            'reclamo' => ['Prodotto non conforme', 'Consegna in ritardo', 'Danno durante il trasporto', 'Assistenza non soddisfacente'],
        ];
        $messaggiCliente = [
            'Buongiorno, avrei bisogno di un intervento al più presto.',
            'Vi ho inviato una mail con i dettagli, potete verificare?',
            'Il problema si ripresenta anche dopo il riavvio.',
            'Resto in attesa di un vostro riscontro.',
        ];
        $messaggiOperatore = [
            'Buongiorno, abbiamo preso in carico la sua richiesta.',
            'Stiamo verificando, la aggiorneremo a breve.',
            'Intervento programmato, la contatteremo per l\'appuntamento.',
            'Richiesta risolta, la invitiamo a confermare.',
        ];
        $noteInterne = [
            'Verificare con il magazzino la disponibilità del ricambio.',
            'Cliente storico, applicare condizioni dedicate.',
            'Da richiamare domani mattina.',
            'Necessario sopralluogo tecnico.',
        ];

        $oggi = time();
        $inizio = strtotime('-6 months');
        $creati = 0;
        $contatori = [];

        for ($i = 1; $i <= (int) $this->ticket; $i++) {
            $esterno = $esterni[array_rand($esterni)];
            $idsAna = MgCrmUtenteAnagrafica::idsAnagraficheDiUtente($esterno->id);
            if (empty($idsAna)) {
                continue;
            }
            $idAna = $idsAna[array_rand($idsAna)];

            $tipo = $tipi[array_rand($tipi)];
            $stato = $stati[array_rand($stati)];
            $prio = $priorita[array_rand($priorita)];

            $data = date('Y-m-d\TH:i:s', mt_rand($inizio, $oggi));
            $anno = (int) date('Y', strtotime($data));
            $contatori[$anno] = ($contatori[$anno] ?? (int) MgCrmTicket::find()->where(['anno' => $anno])->max('numero')) + 1;

            $oggetto = $oggetti[$tipo->codice][array_rand($oggetti[$tipo->codice])];

            $t = new MgCrmTicket();
            $t->anno = $anno;
            $t->numero = $contatori[$anno];
            $t->id_tipo = $tipo->id;
            $t->id_stato = $stato->id;
            $t->id_priorita = $prio->id;
            $t->id_anagrafica = $idAna;
            $t->id_utente = $esterno->id;
            $t->oggetto = $oggetto;
            $t->descrizione = 'Richiesta di ' . strtolower($tipo->descrizione) . ' relativa a "' . $oggetto . '".';
            $t->data_apertura = $data;
            $t->created_at = $data;
            $t->updated_at = $data;
            $t->created_by = $esterno->username;

            // Articolo/matricola in circa meta' dei casi di assistenza.
            if ($tipo->codice === 'assistenza' && !empty($articoli) && mt_rand(0, 1)) {
                $art = $articoli[array_rand($articoli)];
                $t->id_articolo = $art->id;
                $candidate = array_values(array_filter($matricole, function ($m) use ($art) {
                    return (int) $m->id_articolo === (int) $art->id;
                }));
                if (!empty($candidate)) {
                    $t->id_matricola = $candidate[array_rand($candidate)]->id;
                }
            }

            // Documento collegato per le richieste amministrative.
            if ($tipo->codice === 'amministrativa' && !empty($documenti)) {
                $candidati = array_values(array_filter($documenti, function ($d) use ($idAna) {
                    return (int) $d->id_anagrafica === (int) $idAna;
                }));
                if (!empty($candidati)) {
                    $t->id_documento = $candidati[array_rand($candidati)]->id;
                }
            }

            if (!empty($operatori)) {
                $op = $operatori[array_rand($operatori)];
                $t->assegnato_a = $op->id;
                $t->data_presa_carico = date('Y-m-d\TH:i:s', strtotime($data) + mt_rand(3600, 3 * 86400));
            }

            if ($stato->is_chiuso) {
                $t->data_chiusura = date('Y-m-d\TH:i:s', strtotime($data) + mt_rand(2 * 86400, 20 * 86400));
            }

            $t->save(false);

            // Conversazione.
            $nMsg = mt_rand(0, 3);
            for ($m = 0; $m < $nMsg; $m++) {
                $msg = new MgCrmTicketMessaggio();
                $msg->id_ticket = $t->id;
                if (mt_rand(0, 1)) {
                    $msg->user_id = $esterno->id;
                    $msg->autore = $esterno->username;
                    $msg->is_nota_interna = 0;
                    $msg->messaggio = $messaggiCliente[array_rand($messaggiCliente)];
                } else {
                    $msg->user_id = $t->assegnato_a;
                    $msg->autore = 'operatore';
                    $msg->is_nota_interna = mt_rand(0, 3) === 0 ? 1 : 0;
                    $msg->messaggio = $msg->is_nota_interna
                        ? $noteInterne[array_rand($noteInterne)]
                        : $messaggiOperatore[array_rand($messaggiOperatore)];
                }
                $msg->created_at = date('Y-m-d\TH:i:s', strtotime($data) + ($m + 1) * 3600);
                $msg->save(false);
            }

            $creati++;
            if ($creati % 50 === 0) {
                $this->stdout("Creati {$creati} ticket...\n");
            }
        }

        $this->stdout("\nCreati {$creati} ticket.\n");
        $this->stdout('Totale ticket in tabella: ' . MgCrmTicket::find()->count() . "\n");

        return ExitCode::OK;
    }

    /**
     * Collega un utente a un elenco di anagrafiche (senza duplicati).
     */
    private function collega($userId, array $idsAnagrafiche)
    {
        foreach ($idsAnagrafiche as $idAna) {
            MgCrmUtenteAnagrafica::findOrCreate($userId, (int) $idAna);
        }
    }

    /**
     * Id degli utenti demo (username "clienteNN").
     *
     * @return int[]
     */
    private function utentiDemoIds()
    {
        return array_map('intval', User::find()
            ->select('id')
            ->where(['like', 'username', 'cliente'])
            ->andWhere(['is_esterno' => 1])
            ->column());
    }
}
