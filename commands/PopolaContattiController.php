<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Query;

/**
 * Popola i contatti delle anagrafiche (mg_anagrafica_contatto) con dati demo.
 *
 * Uso:
 *   php yii popola-contatti --count=600
 *   php yii popola-contatti --count=600 --reset=1
 */
class PopolaContattiController extends Controller
{
    /** Numero massimo di contatti da generare. */
    public $count = 600;

    /** Se 1, cancella i contatti esistenti prima di generare. */
    public $reset = 0;

    public function options($actionID)
    {
        return ['count', 'reset'];
    }

    public function optionAliases()
    {
        return ['c' => 'count', 'r' => 'reset'];
    }

    public function actionIndex()
    {
        $db = Yii::$app->db;

        if ($this->reset) {
            $eliminati = $db->createCommand()->delete('mg_anagrafica_contatto')->execute();
            $this->stdout("Cancellati $eliminati contatti esistenti.\n");
        }

        $anagrafiche = (new Query())
            ->select(['id', 'codice', 'ragione_sociale'])
            ->from('mg_anagrafica')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $tipi = (new Query())
            ->select(['id', 'codice'])
            ->from('mg_tipo_contatto')
            ->where(['attivo' => 1])
            ->indexBy('codice')
            ->all();

        if (empty($anagrafiche) || empty($tipi)) {
            $this->stderr("Servono anagrafiche e tipi contatto per procedere.\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $visti = [];
        foreach ((new Query())
            ->select(['id_anagrafica', 'id_tipo_contatto', 'valore'])
            ->from('mg_anagrafica_contatto')
            ->all() as $r) {
            $visti[(int) $r['id_anagrafica'] . '|' . (int) $r['id_tipo_contatto'] . '|' . trim($r['valore'])] = true;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [];
        $max = max(0, (int) $this->count);

        $add = function ($idAnagrafica, $tipoCodice, $valore, $etichetta = null, $predefinito = 0, $note = null)
                use (&$rows, &$visti, $tipi, $now) {
            if (!isset($tipi[$tipoCodice])) {
                return false;
            }
            $idTipo = (int) $tipi[$tipoCodice]['id'];
            $valore = trim((string) $valore);
            $chiave = $idAnagrafica . '|' . $idTipo . '|' . $valore;
            if ($valore === '' || isset($visti[$chiave])) {
                return false;
            }
            $visti[$chiave] = true;
            $rows[] = [
                'id_anagrafica' => $idAnagrafica,
                'id_tipo_contatto' => $idTipo,
                'valore' => $valore,
                'etichetta' => $etichetta,
                'predefinito' => $predefinito,
                'note' => $note,
                'attivo' => 1,
                'created_at' => $now,
            ];
            return true;
        };

        $ruoli = [
            'Referente amministrazione', 'Ufficio tecnico', 'Commerciale', 'Direzione',
            'Assistenza', 'Magazzino', 'Segreteria', 'Responsabile cantiere', 'Contabilità',
        ];
        $nomi = [
            'Marco', 'Luca', 'Giuseppe', 'Anna', 'Paola', 'Stefano', 'Francesca', 'Andrea',
            'Chiara', 'Roberto', 'Elena', 'Matteo', 'Silvia', 'Alessandro', 'Laura', 'Davide',
        ];
        $cognomi = [
            'Rossi', 'Bianchi', 'Ferrari', 'Russo', 'Romano', 'Colombo', 'Ricci', 'Marino',
            'Greco', 'Bruno', 'Gallo', 'Conti', 'Costa', 'Giordano', 'Mancini', 'Rizzo',
        ];

        $preparate = [];
        foreach ($anagrafiche as $a) {
            $id = (int) $a['id'];
            $nome = $nomi[array_rand($nomi)];
            $cognome = $cognomi[array_rand($cognomi)];
            $preparate[$id] = [
                'slug' => $this->slug($a['ragione_sociale']),
                'ruolo' => $ruoli[array_rand($ruoli)],
                'persona' => $nome . ' ' . $cognome,
            ];
        }

        foreach ($preparate as $id => $p) {
            if (count($rows) >= $max) {
                break;
            }
            $add($id, 'email', 'info@' . $p['slug'] . '.it', 'Email aziendale', 1, null);
            if (count($rows) < $max && mt_rand(1, 100) <= 40) {
                $add($id, 'email', 'amministrazione@' . $p['slug'] . '.it', 'Amministrazione', 0, null);
            }
        }

        foreach ($preparate as $id => $p) {
            if (count($rows) >= $max) {
                break;
            }

            $slug = $p['slug'];
            $ruolo = $p['ruolo'];
            $persona = $p['persona'];

            if (mt_rand(1, 100) <= 60) {
                $add($id, 'pec', $slug . '@pec.it', 'PEC aziendale', 1, null);
            }
            if (mt_rand(1, 100) <= 90) {
                $add($id, 'cellulare', $this->cellulare(), $persona, 1, null);
            }
            if (mt_rand(1, 100) <= 55) {
                $add($id, 'telefono_fisso', $this->fisso(), $ruolo, 1, null);
            }
            if (mt_rand(1, 100) <= 25) {
                $add($id, 'telefono_personale', $this->cellulare(), $persona, 0, 'Recapito personale');
            }
            if (mt_rand(1, 100) <= 15) {
                $add($id, 'fax', $this->fisso(), 'Fax ufficio', 0, null);
            }
            if (mt_rand(1, 100) <= 40) {
                $add($id, 'sito_web', 'https://www.' . $slug . '.it', 'Sito aziendale', 0, null);
            }
            if (mt_rand(1, 100) <= 30) {
                $add($id, 'linkedin', 'https://www.linkedin.com/company/' . $slug, 'Pagina LinkedIn', 0, null);
            }
            if (mt_rand(1, 100) <= 18) {
                $add($id, 'discord', $this->nick($persona, 'discord'), $persona, 0, 'Canale supporto');
            }
            if (mt_rand(1, 100) <= 25) {
                $add($id, 'whatsapp', $this->cellulare(), $persona, 0, 'Solo messaggi');
            }
            if (mt_rand(1, 100) <= 15) {
                $add($id, 'telegram', '@' . $this->nick($persona, 'telegram'), $persona, 0, null);
            }
            if (mt_rand(1, 100) <= 10) {
                $add($id, 'skype', 'live:' . $this->nick($persona, 'skype'), $persona, 0, null);
            }
        }

        if (empty($rows)) {
            $this->stdout("Nessun nuovo contatto da inserire.\n");
            return ExitCode::OK;
        }

        foreach (array_chunk($rows, 100) as $chunk) {
            $db->createCommand()
                ->batchInsert('mg_anagrafica_contatto', array_keys($chunk[0]), $chunk)
                ->execute();
        }

        $this->stdout('Inseriti ' . count($rows) . " contatti.\n");
        $this->stdout('Totale in tabella: ' . (new Query())->from('mg_anagrafica_contatto')->count() . "\n");

        return ExitCode::OK;
    }

    private function cellulare()
    {
        return sprintf('+39 %d %07d', mt_rand(320, 399), mt_rand(0, 9999999));
    }

    private function fisso()
    {
        return sprintf('0%d %07d', mt_rand(2, 99), mt_rand(0, 9999999));
    }

    private function nick($persona, $canale)
    {
        $base = strtolower(str_replace(' ', '.', trim((string) $persona)));
        $base = preg_replace('/[^a-z.]+/', '', $base);
        if ($canale === 'discord') {
            return $base . '_' . mt_rand(1000, 9999);
        }
        if ($canale === 'skype') {
            return $base . mt_rand(10, 99);
        }
        return str_replace('.', '', $base) . mt_rand(10, 99);
    }

    private function slug($testo)
    {
        $testo = strtolower((string) $testo);
        $testo = str_replace(["'", 'à', 'è', 'é', 'ì', 'ò', 'ù'], ['', 'a', 'e', 'e', 'i', 'o', 'u'], $testo);
        $testo = preg_replace('/[^a-z0-9]+/', '-', $testo);
        return trim($testo, '-');
    }
}
