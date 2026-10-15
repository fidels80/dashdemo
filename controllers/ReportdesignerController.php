<?php

namespace app\controllers;

use Yii;
use app\models\DashReport;
use app\models\DashReportFonte;
use app\components\ReportEngine;
use app\components\Licensing;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

/**
 * Report designer: creazione e modifica dei report (wizard + editor JSON) e
 * delle relative fonti dati (con join visuale, anche cross-database).
 *
 * Licenza: ogni report puo' essere legato a una feature (`licenza_feature`);
 * se la feature non e' abilitata dalla licenza il report non compare nel
 * catalogo e non puo' essere eseguito. Il flag `attivo` consente inoltre di
 * disattivare un report indipendentemente dalla licenza.
 */
class ReportdesignerController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'fonte-delete' => ['POST'],
                ],
            ],
        ];
    }

    // ------------------------------------------------------------------
    //  Report
    // ------------------------------------------------------------------

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DashReport::find()->orderBy(['ordine' => SORT_ASC, 'nome' => SORT_ASC]),
            'pagination' => false,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'features' => Licensing::features(),
        ]);
    }

    public function actionCreate()
    {
        $model = new DashReport();
        $model->attivo = true;
        $model->tipo = 'tabella';

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->load($post);
            $model->attivo = !empty($post['DashReport']['attivo']) ? 1 : 0;
            $model->ordine = (int) ($post['DashReport']['ordine'] ?? 0);
            $model->licenza_feature = ($post['DashReport']['licenza_feature'] ?? '') ?: null;
            $model->creato_da = Yii::$app->user->id;
            $model->created_at = date('Ymd H:i:s');

            $error = null;
            $def = $this->buildDefinizione($post, $error);
            if ($def !== null) {
                $model->definizione = json_encode($def, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Report creato.');
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', $error);
            }
        }

        return $this->render('form', $this->formData($model));
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->load($post);
            $model->attivo = !empty($post['DashReport']['attivo']) ? 1 : 0;
            $model->ordine = (int) ($post['DashReport']['ordine'] ?? 0);
            $model->licenza_feature = ($post['DashReport']['licenza_feature'] ?? '') ?: null;
            $model->updated_at = date('Ymd H:i:s');

            $error = null;
            $def = $this->buildDefinizione($post, $error);
            if ($def !== null) {
                $model->definizione = json_encode($def, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Report salvato.');
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', $error);
            }
        }

        return $this->render('form', $this->formData($model));
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->session->setFlash('success', 'Report eliminato.');
        return $this->redirect(['index']);
    }

    /**
     * Anteprima del report in costruzione (non salva): esegue il motore sui
     * dati inviati dal form del designer.
     */
    public function actionAnteprima()
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Richiesta non valida.');
        }

        $post = Yii::$app->request->post();
        $report = new DashReport();
        $report->load($post);
        $report->fonte_id = (int) ($post['DashReport']['fonte_id'] ?? 0);
        $report->attivo = 1;

        $error = null;
        $def = $this->buildDefinizione($post, $error);
        $result = null;

        if ($def === null) {
            $error = $error ?: 'Definizione non valida.';
        } else {
            $report->definizione = json_encode($def, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            try {
                $result = ReportEngine::run($report, $post);
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return $this->render('anteprima', [
            'report' => $report,
            'def' => $def ?: [],
            'result' => $result,
            'error' => $error,
        ]);
    }

    // ------------------------------------------------------------------
    //  Fonti dati
    // ------------------------------------------------------------------

    public function actionFonti()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => DashReportFonte::find()->orderBy(['nome' => SORT_ASC]),
            'pagination' => false,
        ]);
        return $this->render('fonti', ['dataProvider' => $dataProvider]);
    }

    public function actionFonteCreate()
    {
        $model = new DashReportFonte();
        $model->attivo = true;
        $model->tipo = 'strutturata';
        $model->connessione = 'db';

        if (Yii::$app->request->isPost && $this->saveFonte($model)) {
            Yii::$app->session->setFlash('success', 'Fonte dati creata.');
            return $this->redirect(['fonti']);
        }

        return $this->render('fonte-form', $this->fonteFormData($model));
    }

    public function actionFonteUpdate($id)
    {
        $model = $this->findFonte($id);

        if (Yii::$app->request->isPost && $this->saveFonte($model)) {
            Yii::$app->session->setFlash('success', 'Fonte dati salvata.');
            return $this->redirect(['fonti']);
        }

        return $this->render('fonte-form', $this->fonteFormData($model));
    }

    public function actionFonteDelete($id)
    {
        $model = $this->findFonte($id);
        if (DashReport::find()->where(['fonte_id' => $model->id])->exists()) {
            Yii::$app->session->setFlash('error', 'Impossibile eliminare: la fonte è usata da uno o più report.');
            return $this->redirect(['fonti']);
        }
        $model->delete();
        Yii::$app->session->setFlash('success', 'Fonte dati eliminata.');
        return $this->redirect(['fonti']);
    }

    /**
     * AJAX: elenco tabelle/viste di una connessione (db, db2, ... db6).
     */
    public function actionTabelle($componente = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $db = $this->dbComponent($componente);
        if ($db === null) {
            return ['ok' => false, 'tabelle' => []];
        }
        $rows = $db->createCommand(
            "SELECT TABLE_SCHEMA, TABLE_NAME FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_TYPE IN ('BASE TABLE','VIEW') ORDER BY TABLE_NAME"
        )->queryAll();
        $tabelle = [];
        foreach ($rows as $r) {
            $tabelle[] = ['schema' => $r['TABLE_SCHEMA'], 'tabella' => $r['TABLE_NAME']];
        }
        return ['ok' => true, 'tabelle' => $tabelle];
    }

    /**
     * AJAX: elenco colonne di una fonte (alias.colonna) per le tendine del
     * builder dei report.
     */
    public function actionColonne($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $fonte = $id ? DashReportFonte::findOne((int) $id) : null;
        if (!$fonte) {
            return ['ok' => false, 'colonne' => []];
        }
        try {
            return ['ok' => true, 'colonne' => $this->fonteColumns($fonte)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage(), 'colonne' => []];
        }
    }

    // ------------------------------------------------------------------
    //  Helper: form / salvataggio
    // ------------------------------------------------------------------

    private function formData($model)
    {
        $fonti = DashReportFonte::find()->where(['attivo' => 1])->orderBy(['nome' => SORT_ASC])->all();
        $def = $model->isNewRecord ? $this->emptyDef() : $model->getDefinizioneArray();

        // Colonne della fonte selezionata (per il datalist).
        $colonne = [];
        if ($model->fonte_id && ($fonte = DashReportFonte::findOne($model->fonte_id))) {
            try {
                $colonne = $this->fonteColumns($fonte);
            } catch (\Throwable $e) {
                $colonne = [];
            }
        }

        return [
            'model' => $model,
            'fonti' => $fonti,
            'def' => $def,
            'colonne' => $colonne,
            'features' => Licensing::features(),
        ];
    }

    private function fonteFormData($model)
    {
        $def = $model->isNewRecord ? [] : $model->getDefinizioneArray();
        return [
            'model' => $model,
            'def' => $def,
            'connessioni' => $this->connessioni(),
        ];
    }

    private function saveFonte(DashReportFonte $model)
    {
        $post = Yii::$app->request->post();
        $model->load($post);
        $model->attivo = !empty($post['DashReportFonte']['attivo']) ? 1 : 0;
        $model->connessione = ($post['DashReportFonte']['connessione'] ?? '') ?: 'db';

        $def = $this->buildFonteDefinizione($post);

        if ($model->tipo === 'sql') {
            $sql = trim((string) ($post['fonte_sql'] ?? ''));
            if ($sql === '') {
                $model->addError('definizione', 'Query SQL mancante.');
                return false;
            }
            $model->definizione = $sql;
            $model->base_tabella = null;
        } else {
            if (empty($def['base']['tabella'])) {
                $model->addError('definizione', 'Tabella base mancante.');
                return false;
            }
            $model->definizione = json_encode($def, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $model->base_tabella = $def['base']['tabella'];
        }

        $model->updated_at = date('Ymd H:i:s');
        if ($model->isNewRecord) {
            $model->creato_da = Yii::$app->user->id;
            $model->created_at = date('Ymd H:i:s');
        }

        return $model->save();
    }

    /**
     * Costruisce la definizione del report dai campi del wizard (o dal JSON).
     * Restituisce null e valorizza $error in caso di problema.
     */
    private function buildDefinizione(array $post, &$error)
    {
        $error = null;

        if (($post['modalita'] ?? 'wizard') === 'json') {
            $json = trim((string) ($post['def_json'] ?? ''));
            $decoded = json_decode($json, true);
            if (!is_array($decoded)) {
                $error = 'JSON non valido: ' . json_last_error_msg();
                return null;
            }
            return $decoded;
        }

        $def = [];

        $colonne = $this->collect($post, [
            'campo' => 'col_campo', 'alias' => 'col_alias', 'label' => 'col_label',
            'tipo' => 'col_tipo', 'formato' => 'col_formato', 'totale' => 'col_totale',
        ], 'campo');
        if ($colonne) {
            $def['colonne'] = array_map(function ($r) {
                return $this->clean([
                    'campo' => $r['campo'], 'alias' => $r['alias'], 'label' => $r['label'],
                    'tipo' => $r['tipo'] ?: 'testo', 'formato' => $this->formato($r['formato']),
                    'totale' => $r['totale'] !== '0',
                ]);
            }, $colonne);
        }

        $parametri = $this->collect($post, [
            'nome' => 'par_nome', 'label' => 'par_label', 'tipo' => 'par_tipo', 'default' => 'par_default',
        ], 'nome');
        if ($parametri) {
            $def['parametri'] = array_map(function ($r) {
                return $this->clean([
                    'nome' => $r['nome'], 'label' => $r['label'], 'tipo' => $r['tipo'] ?: 'testo',
                    'default' => $r['default'],
                ]);
            }, $parametri);
        }

        $filtri = $this->collect($post, [
            'campo' => 'fil_campo', 'operatore' => 'fil_operatore', 'param1' => 'fil_param1',
            'param2' => 'fil_param2', 'opzionale' => 'fil_opzionale',
        ], 'campo');
        if ($filtri) {
            $def['filtri'] = array_map(function ($r) {
                return $this->clean([
                    'campo' => $r['campo'], 'operatore' => $r['operatore'] ?: '=',
                    'param1' => $r['param1'], 'param2' => $r['param2'], 'opzionale' => $r['opzionale'] !== '0',
                ]);
            }, $filtri);
        }

        $rag = $this->collect($post, [
            'campo' => 'rag_campo', 'alias' => 'rag_alias', 'label' => 'rag_label',
        ], 'campo');
        if ($rag) {
            $def['raggruppamenti'] = array_map(function ($r) {
                return $this->clean(['campo' => $r['campo'], 'alias' => $r['alias'], 'label' => $r['label']]);
            }, $rag);
        }

        $agg = $this->collect($post, [
            'campo' => 'agg_campo', 'funzione' => 'agg_funzione', 'alias' => 'agg_alias',
            'label' => 'agg_label', 'tipo' => 'agg_tipo', 'formato' => 'agg_formato',
        ], 'alias');
        if ($agg) {
            $def['aggregazioni'] = array_map(function ($r) {
                return $this->clean([
                    'campo' => $r['campo'], 'funzione' => $r['funzione'] ?: 'SUM', 'alias' => $r['alias'],
                    'label' => $r['label'], 'tipo' => $r['tipo'] ?: 'numero', 'formato' => $this->formato($r['formato']),
                ]);
            }, $agg);
        }

        $cal = $this->collect($post, [
            'nome' => 'cal_nome', 'label' => 'cal_label', 'formula' => 'cal_formula',
            'tipo' => 'cal_tipo', 'formato' => 'cal_formato',
        ], 'nome');
        if ($cal) {
            $def['calcolati'] = array_map(function ($r) {
                return $this->clean([
                    'nome' => $r['nome'], 'label' => $r['label'], 'formula' => $r['formula'],
                    'tipo' => $r['tipo'] ?: 'numero', 'formato' => $this->formato($r['formato']),
                ]);
            }, $cal);
        }

        $ord = $this->collect($post, ['campo' => 'ord_campo', 'dir' => 'ord_dir'], 'campo');
        if ($ord) {
            $def['ordinamento'] = array_map(function ($r) {
                return $this->clean(['campo' => $r['campo'], 'dir' => strtoupper($r['dir'] ?: 'ASC')]);
            }, $ord);
        }

        return $def;
    }

    private function buildFonteDefinizione(array $post)
    {
        $base = [
            'db' => ($post['base_db'] ?? '') ?: 'db',
            'tabella' => trim((string) ($post['base_tabella'] ?? '')),
            'alias' => trim((string) ($post['base_alias'] ?? 'b')) ?: 'b',
        ];
        if (!empty($post['base_schema'])) {
            $base['schema'] = $post['base_schema'];
        }

        $def = ['base' => $base];

        $joins = $this->collect($post, [
            'tipo' => 'join_tipo', 'db' => 'join_db', 'schema' => 'join_schema',
            'tabella' => 'join_tabella', 'alias' => 'join_alias', 'on' => 'join_on',
        ], 'tabella');

        $out = [];
        foreach ($joins as $j) {
            $on = [];
            foreach (preg_split('/\r\n|\r|\n/', $j['on']) as $line) {
                $line = trim($line);
                if ($line === '' || strpos($line, '=') === false) {
                    continue;
                }
                list($l, $r) = array_map('trim', explode('=', $line, 2));
                if ($l !== '' && $r !== '') {
                    $on[] = ['left' => $l, 'right' => $r];
                }
            }
            if (empty($on)) {
                continue;
            }
            $row = [
                'tipo' => strtolower($j['tipo'] ?: 'left'),
                'db' => $j['db'] ?: $base['db'],
                'tabella' => $j['tabella'],
                'alias' => $j['alias'] ?: 'j',
                'on' => $on,
            ];
            if (!empty($j['schema']) && $j['schema'] !== 'dbo') {
                $row['schema'] = $j['schema'];
            }
            $out[] = $row;
        }
        if ($out) {
            $def['join'] = $out;
        }

        return $def;
    }

    private function emptyDef()
    {
        return [
            'colonne' => [], 'parametri' => [], 'filtri' => [],
            'raggruppamenti' => [], 'aggregazioni' => [], 'calcolati' => [], 'ordinamento' => [],
        ];
    }

    /**
     * Trasforma array paralleli (campo[], alias[], ...) in righe associative,
     * scartando le righe con il campo chiave vuoto.
     */
    private function collect(array $post, array $map, $requiredKey)
    {
        $n = 0;
        foreach ($map as $postKey) {
            $n = max($n, count($post[$postKey] ?? []));
        }
        $rows = [];
        for ($i = 0; $i < $n; $i++) {
            $row = [];
            foreach ($map as $key => $postKey) {
                $row[$key] = trim((string) (($post[$postKey][$i] ?? '')));
            }
            if ($row[$requiredKey] === '') {
                continue;
            }
            $rows[] = $row;
        }
        return $rows;
    }

    private function clean(array $row)
    {
        $out = [];
        foreach ($row as $k => $v) {
            if ($v === '' || $v === null) {
                continue;
            }
            $out[$k] = $v;
        }
        return $out;
    }

    private function formato($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return is_numeric($value) ? (int) $value : $value;
    }

    // ------------------------------------------------------------------
    //  Helper: connessioni / colonne
    // ------------------------------------------------------------------

    private function connessioni()
    {
        $out = [];
        foreach (['db', 'db2', 'db3', 'db4', 'db5', 'db6'] as $c) {
            if (Yii::$app->has($c)) {
                $out[$c] = $c;
            }
        }
        return $out;
    }

    private function dbComponent($componente)
    {
        $componente = $componente ?: 'db';
        $db = Yii::$app->has($componente) ? Yii::$app->get($componente) : null;
        return ($db instanceof \yii\db\Connection) ? $db : null;
    }

    /**
     * Colonne disponibili di una fonte, come alias.colonna (per le tendine).
     */
    private function fonteColumns(DashReportFonte $fonte)
    {
        $conn = $fonte->connessione ?: 'db';
        $db = $this->dbComponent($conn);
        if ($db === null) {
            return [];
        }
        $def = $fonte->getDefinizioneArray();

        if ($fonte->tipo === 'sql') {
            $sql = trim((string) $fonte->definizione);
            if (isset($def['sql'])) {
                $sql = (string) $def['sql'];
            }
            $reader = $db->createCommand('SELECT TOP 0 * FROM (' . $sql . ') AS t')->query();
            $cols = [];
            foreach ($reader->getColumnNames() as $cn) {
                $cols[] = ['value' => 't.' . $cn, 'label' => 't.' . $cn];
            }
            $reader->close();
            return $cols;
        }

        $tables = [];
        if (!empty($def['base']['tabella'])) {
            $tables[] = $def['base'];
        }
        foreach (($def['join'] ?? []) as $j) {
            $tables[] = $j;
        }

        $cols = [];
        foreach ($tables as $t) {
            $alias = $t['alias'] ?? 'b';
            $dbName = ReportEngine::databaseForComponent($t['db'] ?? $conn);
            $schema = $t['schema'] ?? 'dbo';
            $src = ($dbName ? '[' . str_replace(']', ']]', $dbName) . '].' : '') . 'INFORMATION_SCHEMA.COLUMNS';
            $rows = $db->createCommand(
                "SELECT COLUMN_NAME, DATA_TYPE FROM $src WHERE TABLE_NAME = :t AND TABLE_SCHEMA = :s ORDER BY ORDINAL_POSITION",
                [':t' => $t['tabella'], ':s' => $schema]
            )->queryAll();
            foreach ($rows as $r) {
                $cols[] = [
                    'value' => $alias . '.' . $r['COLUMN_NAME'],
                    'label' => $alias . '.' . $r['COLUMN_NAME'] . ' (' . $r['DATA_TYPE'] . ')',
                ];
            }
        }
        return $cols;
    }

    protected function findModel($id)
    {
        if (($model = DashReport::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Report non trovato.');
    }

    protected function findFonte($id)
    {
        if (($model = DashReportFonte::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Fonte dati non trovata.');
    }
}
