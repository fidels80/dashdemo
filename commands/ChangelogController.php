<?php

namespace app\commands;

use Yii;
use app\models\DashChangelog;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Gestione del changelog applicativo (pagina "Changelog", livelli elevati).
 *
 * Uso tipico:
 *   php yii changelog/git          importa l'ultimo commit
 *   php yii changelog/git 5        importa gli ultimi 5 commit
 *   php yii changelog/git all      importa tutti i commit di tutte le branch
 *   php yii changelog/add "..." --tipo=feat --versione=1.36.0
 *   php yii changelog/set-dettaglio <id> "descrizione di cosa e in che modo"
 *
 * NOTA: i comandi git vengono eseguiti senza usare il formato `%...` nella
 * shell (svuotato da cmd.exe), quindi si usano rev-list + cat-file.
 * L'hook git post-commit invoca automaticamente `changelog/git`.
 */
class ChangelogController extends Controller
{
    /**
     * Importa uno o piu' commit git nel changelog.
     *
     * @param int|string $count numero di commit da importare (default: ultimo), oppure `all`
     * @return int
     */
    public function actionGit($count = 1)
    {
        $all = strtolower(trim((string) $count)) === 'all';
        $count = $all ? 10000 : max(1, (int) $count);

        if (!$this->gitAvailable()) {
            Console::output(Console::ansiFormat('git non disponibile: import saltato.', [Console::FG_YELLOW]));
            return ExitCode::OK;
        }

        $hashes = $this->gitLogHashes($all, $count);

        if (empty($hashes)) {
            Console::output('Nessun commit da importare.');
            return ExitCode::OK;
        }

        $imported = 0;
        foreach ($hashes as $hash) {
            if (DashChangelog::find()->where(['commit_hash' => $hash])->exists()) {
                continue;
            }

            $parsed = $this->parseCommit($this->runGit(['cat-file', 'commit', $hash]));
            if (!$parsed) {
                continue;
            }
            [$author, $email, $date, $subject, $body] = $parsed;

            $model = new DashChangelog();
            $model->commit_hash = $hash;
            $model->autore = $this->currentOperator() ?: ($author ?: $email);
            $model->data_commit = $this->toDateTimeExpr($date);
            $model->titolo = $this->cleanSubject($subject);
            $model->tipo = $this->detectTipo($subject);
            $model->versione = $this->detectVersione($subject . "\n" . $body);
            $model->file_modificati = $this->changedFiles($hash);
            $model->dettaglio = trim($body) !== '' ? trim($body) : null;
            $model->created_at = new \yii\db\Expression('GETDATE()');

            try {
                $ok = $model->save(false);
            } catch (\Throwable $e) {
                Console::output(Console::ansiFormat('Errore per commit ' . substr($hash, 0, 8) . ': ' . $e->getMessage(), [Console::FG_RED]));
                continue;
            }

            if ($ok === false) {
                Console::output(Console::ansiFormat('Salvataggio fallito per commit ' . substr($hash, 0, 8), [Console::FG_RED]));
                continue;
            }

            $imported++;
            Console::output(Console::ansiFormat("Importato commit {$model->titolo}", [Console::FG_GREEN]));
        }

        if ($imported === 0) {
            Console::output('Nessun nuovo commit da importare.');
        }

        return ExitCode::OK;
    }

    /**
     * Inserisce manualmente una voce di changelog.
     *
     * @param string $titolo
     * @return int
     */
    public function actionAdd($titolo, $dettaglio = null, $tipo = 'chore', $versione = null)
    {
        $model = new DashChangelog();
        $model->titolo = $titolo;
        $model->dettaglio = $dettaglio;
        $model->tipo = $tipo;
        $model->versione = $versione;
        $model->autore = getenv('USERNAME') ?: getenv('USER') ?: 'sistema';
        $model->data_commit = $this->toDateTimeExpr(date('Y-m-d H:i:s'));
        $model->created_at = new \yii\db\Expression('GETDATE()');

        if (!$model->save()) {
            Console::output(Console::ansiFormat('Errore: ' . json_encode($model->getErrors()), [Console::FG_RED]));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        Console::output(Console::ansiFormat("Voce changelog #{$model->id} inserita.", [Console::FG_GREEN]));
        return ExitCode::OK;
    }

    /**
     * Aggiorna il dettaglio (cosa e in che modo) di una voce esistente.
     * Usato dall'agente documentale dopo la riscrittura del manuale.
     *
     * @param int $id
     * @param string $dettaglio
     * @return int
     */
    public function actionSetDettaglio($id, $dettaglio)
    {
        $model = DashChangelog::findOne((int) $id);
        if (!$model) {
            Console::output(Console::ansiFormat("Voce #{$id} non trovata.", [Console::FG_RED]));
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $model->dettaglio = $dettaglio;
        $model->save(false);
        Console::output(Console::ansiFormat("Dettaglio aggiornato per la voce #{$id}.", [Console::FG_GREEN]));
        return ExitCode::OK;
    }

    // ------------------------------------------------------------------
    // Helper
    // ------------------------------------------------------------------

    protected function gitAvailable()
    {
        $out = $this->runGit(['--version']);
        return $out !== null && stripos($out, 'git version') !== false;
    }

    /**
     * Elenco degli hash dei commit (uno per riga), dal piu' recente al piu' antico.
     *
     * @param bool $all
     * @param int $count
     * @return string[]
     */
    protected function gitLogHashes($all, $count)
    {
        $args = ['rev-list'];
        if ($all) {
            $args[] = '--all';
            $args[] = '--no-merges';
        } else {
            $args[] = 'HEAD';
        }
        $args[] = '--max-count=' . $count;

        $out = $this->runGit($args);
        if ($out === null) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $out)), function ($l) {
            return $l !== '';
        }));
    }

    /**
     * Esegue un comando git nella root del progetto e ne restituisce l'output.
     *
     * @param array $args
     * @return string|null
     */
    protected function runGit(array $args)
    {
        $root = Yii::getAlias('@app');
        $cmd = 'git -C ' . escapeshellarg($root) . ' ' . implode(' ', array_map('escapeshellarg', $args));
        $output = [];
        $exitCode = 0;
        @exec($cmd . ' 2>&1', $output, $exitCode);

        if ($exitCode !== 0) {
            return null;
        }

        return implode("\n", $output);
    }

    /**
     * Elenco dei file modificati (stato + percorso) come righe di testo.
     */
    protected function changedFiles($hash)
    {
        $out = $this->runGit(['diff-tree', '--no-commit-id', '--name-status', '-r', $hash]);
        if ($out === null) {
            return null;
        }

        $rows = [];
        foreach (preg_split('/\r\n|\r|\n/', $out) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $rows[] = preg_replace('/\t+/', ' ', $line);
            }
        }

        return implode("\n", $rows);
    }

    /**
     * Parsa l'oggetto raw di un commit (`git cat-file commit <hash>`).
     *
     * @param string|null $raw
     * @return array|null [autore, email, data, soggetto, corpo]
     */
    protected function parseCommit($raw)
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', $raw);
        $author = null;
        $email = null;
        $date = null;
        $n = count($lines);
        $i = 0;

        while ($i < $n && $lines[$i] !== '') {
            $line = $lines[$i];
            if (strpos($line, 'author ') === 0) {
                if (preg_match('/^author\s+(.+?)<(.+?)>\s+(\d+)\s*([+-]\d{4})?$/', $line, $m)) {
                    $author = trim($m[1]);
                    $email = trim($m[2]);
                    $date = date('Y-m-d H:i:s', (int) $m[3]);
                }
            }
            $i++;
        }

        // Messaggio: dopo l'header (separato da riga vuota)
        $msg = array_slice($lines, $i + 1);
        while ($msg && trim(end($msg)) === '') {
            array_pop($msg);
        }

        $subject = null;
        $bodyLines = [];
        foreach ($msg as $line) {
            if ($subject === null) {
                if (trim($line) === '') {
                    continue;
                }
                $subject = trim($line);
            } else {
                $bodyLines[] = $line;
            }
        }

        if ($subject === null) {
            $subject = '(nessun messaggio)';
        }

        return [$author, $email, $date, $subject, implode("\n", $bodyLines)];
    }

    protected function currentOperator()
    {
        return getenv('USERNAME') ?: getenv('USER') ?: null;
    }

    protected function toDateTimeExpr($str)
    {
        // CONVERT con stile 120: indipendente dal DATEFORMAT della connessione
        // (qui impostato in dmy, che farebbe fallire stringhe yyyy-mm-dd).
        return new \yii\db\Expression("CONVERT(datetime, '" . str_replace("'", "''", (string) $str) . "', 120)");
    }

    protected function cleanSubject($subject)
    {
        // Rimuove il prefisso conventional-commit (es. "feat(scope): ") per il titolo.
        $clean = preg_replace('/^\s*[a-z]+(\([^)]+\))?!?:\s*/i', '', (string) $subject);
        return trim($clean) !== '' ? trim($clean) : trim((string) $subject);
    }

    protected function detectTipo($subject)
    {
        if (preg_match('/^\s*([a-z]+)(\([^)]+\))?!?:/i', (string) $subject, $m)) {
            $tipo = strtolower($m[1]);
            if (array_key_exists($tipo, DashChangelog::tipiDisponibili())) {
                return $tipo;
            }
        }
        return 'chore';
    }

    protected function detectVersione($text)
    {
        if (preg_match('/versione\s+([0-9]+\.[0-9]+\.[0-9]+)/i', (string) $text, $m)) {
            return $m[1];
        }
        return null;
    }
}