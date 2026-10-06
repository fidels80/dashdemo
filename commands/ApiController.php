<?php

namespace app\commands;

use Yii;
use app\models\DashApiEntita;
use app\models\DashApiRel;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Manutenzione del registro entita' del servizio REST.
 *
 * Aggiorna dash_api_entita e dash_api_rel a partire dal codice: registra le
 * nuove tabelle mg_* e deriva i vincoli di integrita' dalle chiavi esterne
 * gia' presenti nel database. Serve dopo aver creato una nuova tabella del
 * microgestionale, cosi' da poterla subito spuntare nella schermata dei token.
 *
 * Uso:
 *   php yii api/sync              registra entita' mancanti e deriva i vincoli
 *   php yii api/sync --dry=1      mostra cosa farebbe, senza scrivere
 */
class ApiController extends Controller
{
    /** @var int 1 = mostra le modifiche senza scriverle */
    public $dry = 0;

    /** @var string prefisso delle tabelle da considerare */
    public $prefisso = 'mg_';

    /**
     * Opzioni accettate dalla riga di comando.
     *
     * @param string $actionID
     * @return array
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['dry', 'prefisso']);
    }

    public function actionSync()
    {
        $dry = (bool) $this->dry;

        $this->stdout("Registro entità del servizio REST\n", Console::FG_CYAN);
        $this->stdout("Prefisso tabelle: {$this->prefisso}\n\n");

        $modelli = $this->trovaModelli();
        if (empty($modelli)) {
            $this->stderr("Nessun modello trovato con prefisso {$this->prefisso}.\n", Console::FG_RED);
            return ExitCode::DATAERR;
        }
        $this->stdout(count($modelli) . " modelli trovati.\n\n");

        // 1. Entità nuove
        $aggiunte = [];
        foreach ($modelli as $tabella => $classe) {
            if (DashApiEntita::findOne(['tabella' => $tabella]) !== null) {
                continue;
            }
            $codice = $this->codiceDaTabella($tabella);
            if (DashApiEntita::findOne(['codice' => $codice]) !== null) {
                $this->stdout("  ! saltato {$tabella}: il codice '{$codice}' è già assegnato a un'altra entità\n",
                    Console::FG_YELLOW);
                continue;
            }
            $aggiunte[$codice] = $tabella;
            if (!$dry) {
                $e = new DashApiEntita();
                $e->codice = $codice;
                $e->alias = '';
                $e->classe = $classe;
                $e->tabella = $tabella;
                $e->descrizione = $this->descrizioneDaTabella($tabella);
                $e->ordinamento = 900;
                $e->save();
            }
            $this->stdout("  + entità '{$codice}' ({$tabella})\n", Console::FG_GREEN);
        }
        if (empty($aggiunte)) {
            $this->stdout("  Nessuna entità nuova da registrare.\n", Console::FG_GREY);
        }

        // 2. Vincoli derivati dalle chiavi esterne
        $this->stdout("\nVincoli di integrità derivati dalle chiavi esterne\n", Console::FG_CYAN);
        $n = $dry ? $this->derivaVincoli(true) : $this->derivaVincoli(false);
        if ($n === 0) {
            $this->stdout("  Nessun vincolo nuovo.\n", Console::FG_GREY);
        }

        if ($dry) {
            $this->stdout("\nDry run: nessuna scrittura eseguita.\n", Console::FG_YELLOW);
        }

        return ExitCode::OK;
    }

    /**
     * Elenco tabella => classe per i modelli ActiveRecord della tabella indicata.
     *
     * @return array<string, string>
     */
    private function trovaModelli()
    {
        $out = [];
        $files = glob(Yii::getAlias('@app') . '/models/*.php') ?: [];

        foreach ($files as $file) {
            $class = $this->classeNelFile($file);
            if ($class === null) {
                continue;
            }

            try {
                if (!class_exists($class) || !is_subclass_of($class, \yii\db\ActiveRecord::className())) {
                    continue;
                }
                /** @var \yii\db\ActiveRecord $model */
                $model = new $class();
                $tabella = $model::tableName();
            } catch (\Exception $e) {
                continue;
            }

            $tabella = str_replace(['{{%', '}}'], '', (string) $tabella);
            if (stripos($tabella, $this->prefisso) !== 0) {
                continue;
            }
            $out[$tabella] = $class;
        }

        ksort($out);
        return $out;
    }

    /**
     * Nome completo della classe dichiarata nel file, se il file contiene una
     * sola classe ActiveRecord il cui nome coincide con quello del file.
     *
     * Serve a non far scattare l'autoloader su file che dichiarano una classe
     * diversa (duplicati o rinominati), che causerebbero errori fatali.
     *
     * @param string $file
     * @return string|null
     */
    private function classeNelFile($file)
    {
        $nome = basename($file, '.php');
        $codice = @file_get_contents($file);
        if ($codice === false) {
            return null;
        }
        if (!preg_match('/^\s*(?:abstract\s+|final\s+)?class\s+' . preg_quote($nome, '/') . '\b/mi', $codice)) {
            return null;
        }
        if (!preg_match('/^\s*class\s+' . preg_quote($nome, '/') . '\s+extends\s+([\\\\\w]+)/mi', $codice, $m)) {
            return null;
        }
        $parent = ltrim($m[1], '\\');
        if ($parent !== \yii\db\ActiveRecord::className()
            && !is_subclass_of($parent, \yii\db\ActiveRecord::className(), true)) {
            return null;
        }

        if (preg_match('/^\s*namespace\s+([^;\s]+)\s*;/mi', $codice, $n)) {
            return trim($n[1]) . '\\' . $nome;
        }
        return $nome;
    }

    /**
     * Codice entità ricavato dal nome della tabella: mg_documento_ordine -> documento-ordine.
     *
     * @param string $tabella
     * @return string
     */
    private function codiceDaTabella($tabella)
    {
        $codice = stripos($tabella, $this->prefisso) === 0
            ? substr($tabella, strlen($this->prefisso))
            : $tabella;
        return strtolower(str_replace('_', '-', $codice));
    }

    /**
     * Descrizione leggibile ricavata dal nome della tabella.
     *
     * @param string $tabella
     * @return string
     */
    private function descrizioneDaTabella($tabella)
    {
        $parte = $this->codiceDaTabella($tabella);
        return ucfirst(str_replace('-', ' ', $parte));
    }

    /**
     * Crea le regole mancanti leggendo sys.foreign_keys.
     *
     * Una chiave esterna con cancellazione a cascata diventa un vincolo di tipo
     * "figlio" con cascade = 1; le altre diventano "riferimento" (bloccano la
     * cancellazione ma non si rimuovono mai in automatico).
     *
     * @param bool $dry
     * @return int numero di regole create
     */
    private function derivaVincoli($dry)
    {
        // Entità già registrate, indicizzate per tabella.
        $perTabella = [];
        foreach (DashApiEntita::find()->where(['attiva' => 1])->all() as $e) {
            $perTabella[$e->tabella] = $e;
        }

        $creati = 0;
        foreach ($this->foreignKey() as $fk) {
            $figliaTab = $fk['figlia'];
            $padreTab = $fk['padre'];
            $colonna = $fk['colonna'];
            $cascata = isset($fk['cascata']) ? (int) $fk['cascata'] : 0;

            if (!isset($perTabella[$padreTab])) {
                continue;   // il padre non è un'entità API
            }

            // delete_referential_action = 1 (CASCADE) -> figlio smontabile;
            // qualsiasi altro valore -> semplice riferimento, mai rimosso in automatico.
            $tipo = $cascata === 1
                ? DashApiRel::TIPO_FIGLIO
                : DashApiRel::TIPO_RIFERIMENTO;
            $cascade = $tipo === DashApiRel::TIPO_FIGLIO ? 1 : 0;

            $esiste = DashApiRel::findOne([
                'entita' => $perTabella[$padreTab]->codice,
                'tipo' => $tipo,
                'tabella' => $figliaTab,
                'colonna' => $colonna,
            ]) !== null;
            if ($esiste) {
                continue;
            }

            $etichetta = sprintf('righe in %s (%s)', $figliaTab, $colonna);
            if ($dry) {
                $creati++;
                $this->stdout("  + [dry] {$perTabella[$padreTab]->codice}: $tipo $figliaTab.$colonna\n",
                    Console::FG_YELLOW);
                continue;
            }

            $regola = new DashApiRel();
            $regola->entita = $perTabella[$padreTab]->codice;
            $regola->tipo = $tipo;
            $regola->tabella = $figliaTab;
            $regola->colonna = $colonna;
            $regola->etichetta = $etichetta;
            $regola->cascade = $cascade;
            $regola->ordinamento = 100;
            if (!$regola->save()) {
                $this->stderr("  ! non salvata {$figliaTab}.{$colonna}: "
                    . json_encode($regola->getErrors(), JSON_UNESCAPED_UNICODE) . "\n", Console::FG_RED);
                continue;
            }
            $creati++;
            $this->stdout("  + {$perTabella[$padreTab]->codice}: $tipo $figliaTab.$colonna\n",
                Console::FG_GREEN);
        }

        return $creati;
    }

    /**
     * Chiavi esterne del database con tabella figlia, colonna, tabella padre.
     *
     * @return array<int, array<string, mixed>>
     */
    private function foreignKey()
    {
        $db = Yii::$app->db;
        try {
            // Il driver sqlsrv di Yii va in errore con SQL multi-riga e con lo
            // stesso parametro ripetuto: query su una riga e prefisso in linea.
            $pref = str_replace("'", "''", $this->prefisso) . '%';
            $sql = 'SELECT OBJECT_NAME(fk.parent_object_id) AS figlia, '
                . 'COL_NAME(fkc.parent_object_id, fkc.parent_column_id) AS colonna, '
                . 'OBJECT_NAME(fk.referenced_object_id) AS padre, '
                . 'fk.delete_referential_action AS cascata '
                . 'FROM sys.foreign_keys fk '
                . 'JOIN sys.foreign_key_columns fkc ON fkc.constraint_object_id = fk.object_id '
                . "WHERE OBJECT_NAME(fk.parent_object_id) LIKE '$pref' "
                . "AND OBJECT_NAME(fk.referenced_object_id) LIKE '$pref'";

            $rows = $db->createCommand($sql)->queryAll();
        } catch (\Exception $e) {
            $this->stderr('Impossibile leggere le chiavi esterne: ' . $e->getMessage() . "\n",
                Console::FG_RED);
            return [];
        }

        return is_array($rows) ? $rows : [];
    }
}
