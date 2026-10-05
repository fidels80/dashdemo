<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Rapportini;
use app\models\MgDocumentoRiga;

/**
 * Popola la tabella rapportini con dati realistici.
 *
 * Uso:
 *   php yii seed/rapportini --reset=1 --count=100
 */
class SeedController extends Controller
{
    /** Numero di rapportini da generare. */
    public $count = 100;

    /** Se 1, cancella i rapportini esistenti (tranne quelli collegati a documenti). */
    public $reset = 0;

    public function options($actionID)
    {
        return ['count', 'reset'];
    }

    public function optionAliases()
    {
        return ['c' => 'count', 'r' => 'reset'];
    }

    public function actionRapportini()
    {
        $clienti = (new \yii\db\Query())->select('codice')->from('mg_anagrafica')
            ->where(['is_cliente' => 1])->andWhere(['not', ['codice' => null]])->column();
        $sottocommesse = (new \yii\db\Query())->select('codice')->from('mg_sottocommessa')
            ->andWhere(['not', ['codice' => null]])->column();
        $articoli = (new \yii\db\Query())->select(['codice', 'descrizione', 'um'])->from('mg_articolo')
            ->where(['not', ['codice' => 'API-TEST']])->all();
        $utenti = (new \yii\db\Query())->select('id')->from('user')
            ->where(['>=', 'level', 70])->column();

        if (empty($clienti) || empty($sottocommesse) || empty($articoli) || empty($utenti)) {
            $this->stderr("Dati di base mancanti (clienti/sottocommesse/articoli/utenti).\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->reset) {
            $protetti = MgDocumentoRiga::find()
                ->select('id_rapportino')
                ->where(['not', ['id_rapportino' => null]])
                ->column();
            $protetti = array_values(array_filter(array_map('strval', $protetti)));

            if (!empty($protetti)) {
                $n = Rapportini::deleteAll(['not in', 'id', $protetti]);
            } else {
                $n = Rapportini::deleteAll();
            }
            $this->stdout("Cancellati $n rapportini (conservati " . count($protetti) . " collegati a documenti).\n");

            try {
                \Yii::$app->db->createCommand("DBCC CHECKIDENT ('rapportini', RESEED, 0)")->execute();
                $this->stdout("Numerazione riavviata da 1.\n");
            } catch (\Exception $e) {
                $this->stderr("Impossibile resettare la numerazione: " . $e->getMessage() . "\n");
            }
        }

        $attivita = [
            'Montaggio e posa in opera',
            'Sostituzione componente usurato',
            'Manutenzione ordinaria programmata',
            'Collaudo e verifica funzionale',
            'Installazione e cablaggio',
            'Riparazione guasto in cantiere',
            'Pulizia e controllo impianto',
            'Assistenza tecnica in cantiere',
            'Regolazione e taratura',
            'Smontaggio e rimontaggio',
            'Verifica strumentale',
            'Messa in sicurezza area di lavoro',
        ];

        $oggi = time();
        $inizio = strtotime('-12 months');
        $creati = 0;

        for ($i = 1; $i <= $this->count; $i++) {
            do {
                $ts = mt_rand($inizio, $oggi);
                $giorno = (int) date('N', $ts);
            } while ($giorno >= 6);

            $art = $articoli[array_rand($articoli)];
            $cliente = $clienti[array_rand($clienti)];
            $alt = $clienti[array_rand($clienti)];
            $sotto = $sottocommesse[array_rand($sottocommesse)];
            $uid = $utenti[array_rand($utenti)];

            $um = trim((string) ($art['um'] ?? ''));
            if (in_array($um, ['PZ', 'CF', 'NR'], true)) {
                $qta = mt_rand(1, 40);
            } else {
                $qta = round(mt_rand(5, 300) / 10, 1);
            }

            $r = new Rapportini();
            $r->id = $this->guid();
            $r->data = date('Y-m-d\TH:i:s', $ts);
            $r->cd_cli = $cliente;
            $r->altcli = $alt;
            $r->commessa = $sotto;
            $r->cd_art = $art['codice'];
            $r->des_art = $art['descrizione'];
            $r->qta = $qta;
            $r->userid = (int) $uid;
            $r->ora_in = $this->ora(mt_rand(7 * 60, 9 * 60 + 30));
            $r->ora_out = $this->ora(mt_rand(16 * 60, 18 * 60 + 30));
            $pausaIn = mt_rand(12 * 60, 13 * 60);
            $r->pausa_in = $this->ora($pausaIn);
            $r->pausa_out = $this->ora($pausaIn + mt_rand(30, 60));
            $r->note = $attivita[array_rand($attivita)] . ' - commessa ' . $sotto;
            $r->evaso = 0;

            if ($r->save(false)) {
                $creati++;
            } else {
                $this->stderr("Errore sul rapportino n.$i\n");
            }
        }

        $this->stdout("Creati $creati rapportini.\n");
        $this->stdout("Totale in tabella: " . Rapportini::find()->count() . "\n");

        return ExitCode::OK;
    }

    private function ora($minuti)
    {
        return sprintf('%02d:%02d:00', intdiv($minuti, 60), $minuti % 60);
    }

    private function guid()
    {
        $d = random_bytes(16);
        $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
        $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
        return strtoupper(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4)));
    }
}
