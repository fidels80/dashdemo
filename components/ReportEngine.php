<?php

namespace app\components;

use Yii;
use app\models\DashReport;
use app\models\DashReportEsecuzione;

/**
 * Motore di esecuzione dei report definiti con il report designer.
 *
 * Compila la definizione JSON di un report (colonne, filtri, parametri,
 * raggruppamenti, aggregazioni, colonne calcolate) in una query SQL sicura:
 * gli identificatori sono validati e quotati, i valori sono sempre passati
 * come parametri bindati. Non viene mai eseguito SQL scritto dall'utente
 * finale.
 *
 * Fonti dati:
 * - "strutturata": tabella base + join; i join su database diversi della
 *   stessa istanza SQL Server usano il naming a tre parti [db].[dbo].[tabella].
 * - "sql": query tecnica (riservata ai supervisori) usata come tabella
 *   derivata; le colonne del report vi fanno riferimento come t.<colonna>.
 *
 * Schema di `definizione` (report):
 * {
 *   "colonne":       [ {"campo","alias","label","tipo","formato"} ],
 *   "parametri":     [ {"nome","label","tipo","default"} ],
 *   "filtri":        [ {"campo","operatore","param1","param2","opzionale"} ],
 *   "raggruppamenti":[ {"campo","alias","label","tipo","formato"} ],
 *   "aggregazioni":  [ {"campo","funzione","alias","label","tipo","formato"} ],
 *   "calcolati":     [ {"nome","label","formula","tipo","formato"} ],
 *   "ordinamento":   [ {"campo","dir"} ]
 * }
 */
class ReportEngine
{
    const MAX_ROWS = 50000;

    /** Operatori supportati nei filtri. */
    private static $operatori = [
        '=', '<>', '>', '>=', '<', '<=', 'like', 'in', 'between', 'isnull', 'notnull',
    ];

    /**
     * Esegue un report e restituisce colonne, righe e totali.
     *
     * @return array{colonne:array,righe:array,totali:array,righe_totali:int,sql:string,parametri:array}
     */
    public static function run(DashReport $report, array $params = [], $limit = self::MAX_ROWS)
    {
        $fonte = $report->getFonte()->one();
        if (!$fonte) {
            throw new \RuntimeException('Fonte dati non configurata per il report "' . $report->codice . '".');
        }

        $def = $report->getDefinizioneArray();
        $connessione = $fonte->connessione ?: 'db';
        $fonteDef = $fonte->getDefinizioneArray();

        $effParams = self::resolveParams($def, $params);

        list($fromSql, $bind) = self::buildFrom($fonte, $fonteDef, $connessione);

        if (!empty($def['raggruppamenti']) && !empty($def['colonne'])) {
            return self::runGrouped($def, $fromSql, $bind, $connessione, $effParams, $limit);
        }

        $usedAlias = [];
        $colonne = [];
        $selectParts = [];
        $groupBy = [];

        $aggregato = !empty($def['raggruppamenti']) || !empty($def['aggregazioni']);

        if ($aggregato) {
            foreach (($def['raggruppamenti'] ?? []) as $g) {
                $alias = self::aliasFor($g['alias'] ?? null, $g['campo'], $usedAlias);
                $selectParts[] = self::qcol($g['campo']) . ' AS ' . self::q($alias);
                $groupBy[] = self::qcol($g['campo']);
                $colonne[] = [
                    'alias' => $alias,
                    'label' => $g['label'] ?? $alias,
                    'tipo' => $g['tipo'] ?? 'testo',
                    'formato' => $g['formato'] ?? null,
                    'aggregato' => false,
                ];
            }
            foreach (($def['aggregazioni'] ?? []) as $a) {
                $func = strtoupper($a['funzione'] ?? 'SUM');
                if (!in_array($func, ['SUM', 'AVG', 'COUNT', 'MIN', 'MAX'], true)) {
                    throw new \RuntimeException('Funzione di aggregazione non supportata: ' . $func);
                }
                $campo = $a['campo'] ?? '';
                $alias = self::aliasFor($a['alias'] ?? null, $campo . '_' . strtolower($func), $usedAlias);
                if ($func === 'COUNT' && $campo === '') {
                    $arg = '*';
                } else {
                    if ($campo === '') {
                        throw new \RuntimeException('Aggregazione "' . $func . '" senza campo.');
                    }
                    $arg = self::qcol($campo);
                }
                $selectParts[] = $func . '(' . $arg . ') AS ' . self::q($alias);
                $colonne[] = [
                    'alias' => $alias,
                    'label' => $a['label'] ?? $alias,
                    'tipo' => $a['tipo'] ?? 'numero',
                    'formato' => $a['formato'] ?? null,
                    'aggregato' => true,
                    'funzione' => $func,
                    'tot' => ($a['totale'] ?? true) !== false,
                ];
            }
        } else {
            foreach (($def['colonne'] ?? []) as $c) {
                $alias = self::aliasFor($c['alias'] ?? null, $c['campo'], $usedAlias);
                $selectParts[] = self::qcol($c['campo']) . ' AS ' . self::q($alias);
                $colonne[] = [
                    'alias' => $alias,
                    'label' => $c['label'] ?? $alias,
                    'tipo' => $c['tipo'] ?? 'testo',
                    'formato' => $c['formato'] ?? null,
                    'aggregato' => false,
                    'tot' => ($c['totale'] ?? true) !== false,
                ];
            }
        }

        if (empty($selectParts)) {
            throw new \RuntimeException('Il report non ha colonne selezionate.');
        }

        list($whereSql, $whereBind) = self::buildWhere($def['filtri'] ?? [], $effParams);
        $bind = array_merge($bind, $whereBind);

        $sql = 'SELECT ' . implode(', ', $selectParts) . ' FROM ' . $fromSql;
        if ($whereSql !== '') {
            $sql .= ' WHERE ' . $whereSql;
        }
        if (!empty($groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $groupBy);
        }

        $db = Yii::$app->get($connessione);
        if (!$db instanceof \yii\db\Connection) {
            throw new \RuntimeException('Connessione "' . $connessione . '" non trovata.');
        }

        $command = $db->createCommand($sql);
        foreach ($bind as $name => $value) {
            $command->bindValue($name, $value);
        }
        $rows = $command->queryAll();
        if ($limit > 0 && count($rows) > $limit) {
            $rows = array_slice($rows, 0, $limit);
        }

        // Colonne calcolate (valutate in PHP: nessun eval, parser sicuro).
        foreach (($def['calcolati'] ?? []) as $c) {
            $nome = self::aliasFor($c['nome'] ?? null, 'calc', $usedAlias);
            $colonne[] = [
                'alias' => $nome,
                'label' => $c['label'] ?? $nome,
                'tipo' => $c['tipo'] ?? 'numero',
                'formato' => $c['formato'] ?? null,
                'aggregato' => false,
                'calcolato' => true,
                'tot' => ($c['totale'] ?? true) !== false,
            ];
            foreach ($rows as $i => $row) {
                $rows[$i][$nome] = self::evalFormula($c['formula'] ?? '', $row);
            }
        }

        // Ordinamento (in PHP, così funziona anche sui campi calcolati).
        if (!empty($def['ordinamento'])) {
            self::sortRows($rows, $def['ordinamento']);
        }

        $totali = self::computeTotali($colonne, $rows);

        return [
            'colonne' => $colonne,
            'righe' => $rows,
            'totali' => $totali,
            'righe_totali' => count($rows),
            'sql' => $sql,
            'parametri' => $effParams,
        ];
    }

    /**
     * Esegue un report con raggruppamento e dettaglio (report "raggruppato"
     * con colonne di dettaglio definite): recupera le righe di dettaglio,
     * le raggruppa per i campi indicati e calcola per ogni gruppo i subtotali
     * (aggregazioni) e le colonne calcolate, oltre ai totali complessivi.
     */
    private static function runGrouped(array $def, $fromSql, array $bind, $connessione, array $effParams, $limit)
    {
        $usedAlias = [];
        $selectParts = [];
        $orderBy = [];

        $ragCols = [];
        foreach (($def['raggruppamenti'] ?? []) as $g) {
            $alias = self::aliasFor($g['alias'] ?? null, $g['campo'], $usedAlias);
            $selectParts[] = self::qcol($g['campo']) . ' AS ' . self::q($alias);
            $orderBy[] = self::qcol($g['campo']);
            $ragCols[] = [
                'alias' => $alias,
                'label' => $g['label'] ?? $alias,
                'tipo' => $g['tipo'] ?? 'testo',
                'formato' => $g['formato'] ?? null,
            ];
        }

        $detailCols = [];
        foreach (($def['colonne'] ?? []) as $c) {
            $alias = self::aliasFor($c['alias'] ?? null, $c['campo'], $usedAlias);
            $selectParts[] = self::qcol($c['campo']) . ' AS ' . self::q($alias);
            $detailCols[] = [
                'alias' => $alias,
                'label' => $c['label'] ?? $alias,
                'tipo' => $c['tipo'] ?? 'testo',
                'formato' => $c['formato'] ?? null,
                'tot' => ($c['totale'] ?? true) !== false,
            ];
        }

        $measureCols = [];
        $rawMap = [];
        foreach (($def['aggregazioni'] ?? []) as $a) {
            $func = strtoupper($a['funzione'] ?? 'SUM');
            if (!in_array($func, ['SUM', 'AVG', 'COUNT', 'MIN', 'MAX'], true)) {
                throw new \RuntimeException('Funzione di aggregazione non supportata: ' . $func);
            }
            $campo = $a['campo'] ?? '';
            $alias = self::aliasFor($a['alias'] ?? null, $campo . '_' . strtolower($func), $usedAlias);
            if ($func === 'COUNT' && $campo === '') {
                $rawMap[$alias] = null;
            } else {
                if ($campo === '') {
                    throw new \RuntimeException('Aggregazione "' . $func . '" senza campo.');
                }
                $rawAlias = self::aliasFor(null, $alias . '_raw', $usedAlias);
                $selectParts[] = self::qcol($campo) . ' AS ' . self::q($rawAlias);
                $rawMap[$alias] = $rawAlias;
            }
            $measureCols[] = [
                'alias' => $alias,
                'label' => $a['label'] ?? $alias,
                'tipo' => $a['tipo'] ?? 'numero',
                'formato' => $a['formato'] ?? null,
                'funzione' => $func,
            ];
        }

        $calcolatiMeta = [];
        foreach (($def['calcolati'] ?? []) as $c) {
            $nome = self::aliasFor($c['nome'] ?? null, 'calc', $usedAlias);
            $calcolatiMeta[] = [
                'nome' => $nome,
                'label' => $c['label'] ?? $nome,
                'tipo' => $c['tipo'] ?? 'numero',
                'formato' => $c['formato'] ?? null,
                'formula' => $c['formula'] ?? '',
            ];
        }

        list($whereSql, $whereBind) = self::buildWhere($def['filtri'] ?? [], $effParams);
        $bind = array_merge($bind, $whereBind);

        $sql = 'SELECT ' . implode(', ', $selectParts) . ' FROM ' . $fromSql;
        if ($whereSql !== '') {
            $sql .= ' WHERE ' . $whereSql;
        }
        if (!empty($orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $orderBy);
        }

        $db = Yii::$app->get($connessione);
        if (!$db instanceof \yii\db\Connection) {
            throw new \RuntimeException('Connessione "' . $connessione . '" non trovata.');
        }
        $command = $db->createCommand($sql);
        foreach ($bind as $name => $value) {
            $command->bindValue($name, $value);
        }
        $rows = $command->queryAll();
        if ($limit > 0 && count($rows) > $limit) {
            $rows = array_slice($rows, 0, $limit);
        }

        $groups = [];
        $groupIndex = [];
        $allValues = [];
        foreach ($measureCols as $m) {
            $allValues[$m['alias']] = [];
        }

        foreach ($rows as $row) {
            $keyParts = [];
            foreach ($ragCols as $rg) {
                $keyParts[] = $row[$rg['alias']] ?? null;
            }
            $key = json_encode($keyParts, JSON_UNESCAPED_UNICODE);
            if (!array_key_exists($key, $groupIndex)) {
                $groupIndex[$key] = count($groups);
                $groups[] = [
                    'chiavi' => array_combine(array_column($ragCols, 'alias'), $keyParts),
                    'righe' => [],
                ];
            }
            $gi = $groupIndex[$key];
            $groups[$gi]['righe'][] = $row;

            foreach ($measureCols as $m) {
                if ($rawMap[$m['alias']] === null) {
                    $allValues[$m['alias']][] = 1;
                } else {
                    $allValues[$m['alias']][] = $row[$rawMap[$m['alias']]] ?? null;
                }
            }
        }

        foreach ($groups as &$g) {
            $subtotali = [];
            foreach ($measureCols as $m) {
                if ($rawMap[$m['alias']] === null) {
                    $subtotali[$m['alias']] = count($g['righe']);
                } else {
                    $vals = array_column($g['righe'], $rawMap[$m['alias']]);
                    $subtotali[$m['alias']] = self::aggregateValues($m['funzione'], $vals);
                }
            }
            $g['subtotali'] = $subtotali;
            $vars = array_merge($subtotali, $g['chiavi']);
            $calc = [];
            foreach ($calcolatiMeta as $c) {
                $calc[$c['nome']] = self::evalFormula($c['formula'], $vars);
            }
            $g['calcolati'] = $calc;
            $g['num_righe'] = count($g['righe']);
            if (!empty($def['ordinamento'])) {
                self::sortRows($g['righe'], $def['ordinamento']);
            }
        }
        unset($g);

        $totali = [];
        foreach ($measureCols as $m) {
            $totali[$m['alias']] = self::aggregateValues($m['funzione'], $allValues[$m['alias']]);
        }
        foreach ($detailCols as $c) {
            if (($c['tot'] ?? true) === false) {
                continue;
            }
            if (!in_array($c['tipo'], ['numero', 'valuta', 'intero'], true)) {
                continue;
            }
            $totali[$c['alias']] = array_sum(array_map(function ($r) use ($c) {
                return (float) ($r[$c['alias']] ?? 0);
            }, $rows));
        }

        return [
            'colonne' => $detailCols,
            'raggruppamenti' => $ragCols,
            'misure' => $measureCols,
            'calcolati' => $calcolatiMeta,
            'gruppi' => $groups,
            'totali' => $totali,
            'righe_totali' => count($rows),
            'sql' => $sql,
            'parametri' => $effParams,
        ];
    }

    /**
     * Applica una funzione di aggregazione (SUM/AVG/COUNT/MIN/MAX) a un
     * insieme di valori, ignorando null e stringhe vuote.
     */
    private static function aggregateValues($func, array $values)
    {
        $nums = [];
        foreach ($values as $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $nums[] = (float) $v;
        }
        switch ($func) {
            case 'COUNT':
                return count($nums);
            case 'SUM':
                return array_sum($nums);
            case 'AVG':
                return count($nums) ? array_sum($nums) / count($nums) : 0.0;
            case 'MIN':
                return count($nums) ? min($nums) : 0.0;
            case 'MAX':
                return count($nums) ? max($nums) : 0.0;
        }
        return 0.0;
    }

    /**
     * Costruisce la clausola FROM (tabella base + join) e i bind della fonte.
     */
    private static function buildFrom($fonte, array $fonteDef, $connessione)
    {
        $bind = [];

        if ($fonte->tipo === 'sql') {
            $sql = trim((string) $fonte->definizione);
            // La definizione puo' contenere il solo SQL (stringa) oppure
            // un oggetto {"sql": "..."}.
            if (isset($fonteDef['sql'])) {
                $sql = (string) $fonteDef['sql'];
            }
            if ($sql === '') {
                throw new \RuntimeException('Fonte SQL "' . $fonte->codice . '" senza query.');
            }
            return ['(' . $sql . ') AS ' . self::q('t'), $bind];
        }

        $base = $fonteDef['base'] ?? [];
        $dbName = self::databaseForComponent($base['db'] ?? $connessione);
        $table = $base['tabella'] ?? $fonte->base_tabella;
        if (empty($table)) {
            throw new \RuntimeException('Fonte "' . $fonte->codice . '" senza tabella base.');
        }
        $baseAlias = $base['alias'] ?? 'b';
        $from = self::qtable($dbName, $base['schema'] ?? 'dbo', $table) . ' AS ' . self::q($baseAlias);

        foreach (($fonteDef['join'] ?? []) as $j) {
            $tipo = strtoupper($j['tipo'] ?? 'left');
            if (!in_array($tipo, ['LEFT', 'INNER', 'RIGHT', 'FULL'], true)) {
                throw new \RuntimeException('Tipo di join non supportato: ' . $tipo);
            }
            if ($tipo === 'FULL') {
                $tipo = 'FULL OUTER';
            }
            $jdb = self::databaseForComponent($j['db'] ?? $base['db'] ?? $connessione);
            $jtable = self::qtable($jdb, $j['schema'] ?? 'dbo', $j['tabella'] ?? '');
            $jalias = $j['alias'] ?? 'j' . count($bind);

            $conds = [];
            foreach (($j['on'] ?? []) as $on) {
                $conds[] = self::qcol($on['left']) . ' = ' . self::qcol($on['right']);
            }
            if (empty($conds)) {
                throw new \RuntimeException('Join senza condizione di collegamento.');
            }

            $from .= ' ' . $tipo . ' JOIN ' . $jtable . ' AS ' . self::q($jalias)
                . ' ON ' . implode(' AND ', $conds);
        }

        return [$from, $bind];
    }

    /**
     * Costruisce la clausola WHERE dai filtri e dai valori dei parametri.
     */
    private static function buildWhere(array $filtri, array $params)
    {
        $parts = [];
        $bind = [];
        $i = 0;

        foreach ($filtri as $f) {
            $op = strtolower($f['operatore'] ?? '=');
            if (!in_array($op, self::$operatori, true)) {
                throw new \RuntimeException('Operatore di filtro non supportato: ' . $op);
            }
            $col = self::qcol($f['campo']);
            $opzionale = !empty($f['opzionale']);

            if ($op === 'isnull') {
                $parts[] = $col . ' IS NULL';
                continue;
            }
            if ($op === 'notnull') {
                $parts[] = $col . ' IS NOT NULL';
                continue;
            }

            $p1 = $f['param1'] ?? null;
            $p2 = $f['param2'] ?? null;
            $v1 = $p1 !== null ? ($params[$p1] ?? null) : null;
            $v2 = $p2 !== null ? ($params[$p2] ?? null) : null;

            if ($op === 'between') {
                if ($v1 === null || $v1 === '' || $v2 === null || $v2 === '') {
                    if ($opzionale) {
                        continue;
                    }
                    throw new \RuntimeException('Filtro tra valori senza parametri.');
                }
                $b1 = ':w' . ($i++);
                $b2 = ':w' . ($i++);
                $parts[] = $col . ' BETWEEN ' . $b1 . ' AND ' . $b2;
                $bind[$b1] = $v1;
                $bind[$b2] = $v2;
                continue;
            }

            if ($op === 'in') {
                if ($v1 === null || $v1 === '') {
                    if ($opzionale) {
                        continue;
                    }
                    throw new \RuntimeException('Filtro IN senza valori.');
                }
                $vals = is_array($v1) ? $v1 : array_map('trim', explode(',', (string) $v1));
                $ph = [];
                foreach ($vals as $val) {
                    if ($val === '') {
                        continue;
                    }
                    $b = ':w' . ($i++);
                    $ph[] = $b;
                    $bind[$b] = $val;
                }
                if (empty($ph)) {
                    if ($opzionale) {
                        continue;
                    }
                    throw new \RuntimeException('Filtro IN senza valori validi.');
                }
                $parts[] = $col . ' IN (' . implode(', ', $ph) . ')';
                continue;
            }

            if ($v1 === null || $v1 === '') {
                if ($opzionale) {
                    continue;
                }
                // Filtro non opzionale senza valore: ignorato per non bloccare il report.
                continue;
            }

            $b = ':w' . ($i++);
            if ($op === 'like') {
                $parts[] = $col . ' LIKE ' . $b;
                $bind[$b] = '%' . $v1 . '%';
            } else {
                $parts[] = $col . ' ' . $op . ' ' . $b;
                $bind[$b] = $v1;
            }
        }

        return [implode(' AND ', $parts), $bind];
    }

    /**
     * Risolve i valori effettivi dei parametri (forniti o default).
     */
    private static function resolveParams(array $def, array $params)
    {
        $out = [];
        foreach (($def['parametri'] ?? []) as $p) {
            $nome = $p['nome'] ?? null;
            if (empty($nome)) {
                continue;
            }
            $tipo = strtolower($p['tipo'] ?? 'testo');
            $value = $params[$nome] ?? null;
            if ($value === null || $value === '') {
                $value = self::expandDefault($p['default'] ?? null);
            }
            if ($value !== null && $value !== '' && $tipo === 'date') {
                $ts = strtotime((string) $value);
                $value = $ts ? date('Ymd', $ts) : $value;
            }
            $out[$nome] = ($value === null) ? '' : $value;
        }
        return $out;
    }

    /**
     * Espande i default simbolici dei parametri.
     */
    private static function expandDefault($default)
    {
        if ($default === null) {
            return null;
        }
        switch ((string) $default) {
            case 'today':
                return date('Y-m-d');
            case 'first-day-month':
                return date('Y-m-01');
            case 'last-day-month':
                return date('Y-m-t');
            default:
                return $default;
        }
    }

    /**
     * Somma i totali delle colonne di tipo numerico/valuta.
     */
    private static function computeTotali(array $colonne, array $rows)
    {
        $totali = [];
        foreach ($colonne as $c) {
            if (array_key_exists('tot', $c) && $c['tot'] === false) {
                continue;
            }
            $tipo = $c['tipo'] ?? 'testo';
            $isNum = in_array($tipo, ['numero', 'valuta', 'intero'], true)
                || ($c['aggregato'] ?? false);
            if (!$isNum) {
                continue;
            }
            // Per AVG/MIN/MAX il totale complessivo non e' una somma sensata.
            if (($c['funzione'] ?? '') === 'AVG' || ($c['funzione'] ?? '') === 'MIN'
                || ($c['funzione'] ?? '') === 'MAX') {
                continue;
            }
            $sum = 0.0;
            foreach ($rows as $r) {
                $sum += (float) ($r[$c['alias']] ?? 0);
            }
            $totali[$c['alias']] = $sum;
        }
        return $totali;
    }

    /**
     * Ordina le righe in base alla lista `ordinamento` (anche su calcolati).
     */
    private static function sortRows(array &$rows, array $ordinamento)
    {
        usort($rows, function ($a, $b) use ($ordinamento) {
            foreach ($ordinamento as $o) {
                $campo = $o['campo'] ?? null;
                if ($campo === null) {
                    continue;
                }
                $dir = strtoupper($o['dir'] ?? 'ASC') === 'DESC' ? -1 : 1;
                $va = $a[$campo] ?? null;
                $vb = $b[$campo] ?? null;
                if (is_numeric($va) && is_numeric($vb)) {
                    $cmp = ((float) $va <=> (float) $vb);
                } else {
                    $cmp = strcmp((string) $va, (string) $vb);
                }
                if ($cmp !== 0) {
                    return $cmp * $dir;
                }
            }
            return 0;
        });
    }

    // ------------------------------------------------------------------
    //  Colonne calcolate: parser di espressioni sicuro (niente eval)
    // ------------------------------------------------------------------

    /**
     * Valuta una formula aritmetica sui valori di una riga.
     * Supporta + - * / %, parentesi e le funzioni ROUND(x[,n]), ABS(x).
     */
    public static function evalFormula($formula, array $vars)
    {
        if (trim((string) $formula) === '') {
            return null;
        }
        $tokens = self::tokenize($formula);
        $pos = 0;
        $value = self::parseExpr($tokens, $pos, $vars);
        if ($pos < count($tokens)) {
            throw new \RuntimeException('Formula non valida: "' . $formula . '"');
        }
        return $value;
    }

    private static function tokenize($formula)
    {
        preg_match_all(
            '/\s*([0-9]+(?:\.[0-9]+)?|[A-Za-z_][A-Za-z0-9_]*|\+|\-|\*|\/|%|\(|\)|,)/',
            (string) $formula,
            $m
        );
        return $m[1];
    }

    private static function parseExpr(array $t, &$pos, array $vars)
    {
        $value = self::parseTerm($t, $pos, $vars);
        while ($pos < count($t) && ($t[$pos] === '+' || $t[$pos] === '-')) {
            $op = $t[$pos++];
            $rhs = self::parseTerm($t, $pos, $vars);
            $value = ($op === '+') ? $value + $rhs : $value - $rhs;
        }
        return $value;
    }

    private static function parseTerm(array $t, &$pos, array $vars)
    {
        $value = self::parseFactor($t, $pos, $vars);
        while ($pos < count($t) && in_array($t[$pos], ['*', '/', '%'], true)) {
            $op = $t[$pos++];
            $rhs = self::parseFactor($t, $pos, $vars);
            if (($op === '/' || $op === '%') && (float) $rhs == 0.0) {
                return 0.0;
            }
            if ($op === '*') {
                $value = $value * $rhs;
            } elseif ($op === '/') {
                $value = $value / $rhs;
            } else {
                $value = fmod($value, $rhs);
            }
        }
        return $value;
    }

    private static function parseFactor(array $t, &$pos, array $vars)
    {
        if ($pos >= count($t)) {
            throw new \RuntimeException('Formula incompleta.');
        }
        $tok = $t[$pos];

        if ($tok === '-') {
            $pos++;
            return -1 * self::parseFactor($t, $pos, $vars);
        }
        if ($tok === '+') {
            $pos++;
            return self::parseFactor($t, $pos, $vars);
        }
        if ($tok === '(') {
            $pos++;
            $value = self::parseExpr($t, $pos, $vars);
            if ($pos < count($t) && $t[$pos] === ')') {
                $pos++;
            }
            return $value;
        }
        if (is_numeric($tok)) {
            $pos++;
            return (float) $tok;
        }

        // Identificatore: funzione o variabile.
        $pos++;
        if ($pos < count($t) && $t[$pos] === '(') {
            $pos++;
            $args = [];
            if ($pos < count($t) && $t[$pos] !== ')') {
                $args[] = self::parseExpr($t, $pos, $vars);
                while ($pos < count($t) && $t[$pos] === ',') {
                    $pos++;
                    $args[] = self::parseExpr($t, $pos, $vars);
                }
            }
            if ($pos < count($t) && $t[$pos] === ')') {
                $pos++;
            }
            return self::callFunc($tok, $args);
        }

        $val = $vars[$tok] ?? 0;
        return is_numeric($val) ? (float) $val : 0.0;
    }

    private static function callFunc($name, array $args)
    {
        $name = strtoupper($name);
        $a = isset($args[0]) ? (float) $args[0] : 0.0;
        switch ($name) {
            case 'ROUND':
                $n = isset($args[1]) ? (int) $args[1] : 0;
                return round($a, $n);
            case 'ABS':
                return abs($a);
            case 'MAX':
                return empty($args) ? 0.0 : max(array_map('floatval', $args));
            case 'MIN':
                return empty($args) ? 0.0 : min(array_map('floatval', $args));
            default:
                throw new \RuntimeException('Funzione non supportata nelle formule: ' . $name);
        }
    }

    // ------------------------------------------------------------------
    //  Identificatori e connessioni
    // ------------------------------------------------------------------

    /**
     * Quota un singolo identificatore: foo -> [foo].
     */
    private static function q($ident)
    {
        $ident = (string) $ident;
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $ident)) {
            throw new \RuntimeException('Identificatore non valido: ' . $ident);
        }
        return '[' . $ident . ']';
    }

    /**
     * Quota un riferimento a colonna, anche con alias: p.data -> [p].[data].
     */
    private static function qcol($expr)
    {
        $expr = (string) $expr;
        if ($expr !== '' && strpos($expr, '(') !== false) {
            // Consente funzioni note su una colonna (es. ISNULL usato nei filtri).
            if (preg_match('/^([A-Za-z_][A-Za-z0-9_]*)\((\*|[A-Za-z_][A-Za-z0-9_]*(\.[A-Za-z_][A-Za-z0-9_]*)?)\)$/', $expr, $m)) {
                $arg = $m[2] === '*' ? '*' : self::qcol($m[2]);
                return strtoupper($m[1]) . '(' . $arg . ')';
            }
            throw new \RuntimeException('Espressione colonna non valida: ' . $expr);
        }
        $parts = explode('.', $expr);
        $out = [];
        foreach ($parts as $p) {
            $out[] = self::q($p);
        }
        return implode('.', $out);
    }

    /**
     * Quota un nome di tabella: [database].[schema].[tabella].
     */
    private static function qtable($database, $schema, $table)
    {
        $ref = '';
        if (!empty($database)) {
            $ref .= self::q($database) . '.';
        }
        $ref .= self::q($schema) . '.' . self::q($table);
        return $ref;
    }

    /**
     * Ricava il nome del database dal DSN della connessione indicata.
     */
    public static function databaseForComponent($component)
    {
        if (empty($component)) {
            return null;
        }
        $db = Yii::$app->get($component, false);
        if (!$db instanceof \yii\db\Connection) {
            return null;
        }
        if (preg_match('/[Dd]atabase=([^;]+)/', (string) $db->dsn, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Genera un alias SQL univoco e valido.
     */
    private static function aliasFor($alias, $fallback, array &$used)
    {
        $alias = $alias ?: self::lastSegment($fallback);
        $alias = preg_replace('/[^A-Za-z0-9_]/', '_', (string) $alias);
        if ($alias === '' || !preg_match('/^[A-Za-z_]/', $alias)) {
            $alias = 'c_' . $alias;
        }
        $base = $alias;
        $n = 1;
        while (isset($used[$alias])) {
            $alias = $base . '_' . (++$n);
        }
        $used[$alias] = true;
        return $alias;
    }

    private static function lastSegment($value)
    {
        $parts = explode('.', (string) $value);
        return end($parts);
    }
}
