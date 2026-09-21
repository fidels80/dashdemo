<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\ApiToken;
use app\models\MgTipoDocumento;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgDocumento;
use app\models\MgDocumentoRiga;

/**
 * Servizio REST per import/export dati (autenticazione Bearer token).
 *
 * Endpoint (URL non pretty):
 *   GET  index.php?r=api/index                       stato e conteggi
 *   GET  index.php?r=api/export&entity=documenti      esportazione
 *   GET  index.php?r=api/view&entity=documenti&id=1   singolo record
 *   POST index.php?r=api/import                       {entity, records[]}
 *   POST index.php?r=api/delete                       {entity, ids[]}
 *
 * Header: Authorization: Bearer <token>
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    private function entityMap()
    {
        return [
            'tipi-documento' => MgTipoDocumento::className(),
            'tipodocumento' => MgTipoDocumento::className(),
            'anagrafica' => MgAnagrafica::className(),
            'articoli' => MgArticolo::className(),
            'documenti' => MgDocumento::className(),
            'righe' => MgDocumentoRiga::className(),
        ];
    }

    private function uniqueKeys($class)
    {
        if ($class === MgTipoDocumento::className() || $class === MgAnagrafica::className()
            || $class === MgArticolo::className()) {
            return ['codice'];
        }
        if ($class === MgDocumento::className()) {
            return ['id_tipo', 'anno', 'numero', 'suffisso'];
        }
        return null;
    }

    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $this->enableCsrfValidation = false;

        // CORS
        $headers = Yii::$app->response->headers;
        $headers->set('Access-Control-Allow-Origin', '*');
        $headers->set('Access-Control-Allow-Methods', 'GET, POST, DELETE, OPTIONS');
        $headers->set('Access-Control-Allow-Headers', 'Authorization, Content-Type');

        if (Yii::$app->request->isOptions) {
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = ['success' => true];
            return false;
        }

        $token = $this->getBearerToken();
        if (empty($token)) {
            return $this->fail(401, 'Token Bearer mancante.');
        }

        $apiToken = ApiToken::findValid($token);
        if (!$apiToken) {
            return $this->fail(401, 'Token non valido o scaduto.');
        }
        $apiToken->markUsed();

        return parent::beforeAction($action);
    }

    private function getBearerToken()
    {
        $auth = Yii::$app->request->headers->get('Authorization');
        if ($auth && preg_match('/^Bearer\s+(.+)$/i', trim($auth), $m)) {
            return trim($m[1]);
        }
        // fallback comodo per test
        return Yii::$app->request->get('token');
    }

    private function fail($code, $message)
    {
        Yii::$app->response->statusCode = $code;
        Yii::$app->response->data = ['success' => false, 'error' => $message];
        return false;
    }

    private function resolveEntity($name)
    {
        $map = $this->entityMap();
        return $map[strtolower(trim((string) $name))] ?? null;
    }

    /**
     * GET: stato del servizio e conteggi per entita'.
     */
    public function actionIndex()
    {
        $counts = [];
        foreach (['tipi-documento' => MgTipoDocumento::className(),
                     'anagrafica' => MgAnagrafica::className(),
                     'articoli' => MgArticolo::className(),
                     'documenti' => MgDocumento::className(),
                     'righe' => MgDocumentoRiga::className()] as $name => $class) {
            $counts[$name] = (int) $class::find()->count();
        }

        return ['success' => true, 'service' => 'dashdemo-api', 'version' => '1.0.0', 'counts' => $counts];
    }

    /**
     * GET: esporta i record di una entita'.
     */
    public function actionExport()
    {
        $entity = Yii::$app->request->get('entity');
        $class = $this->resolveEntity($entity);
        if (!$class) {
            return ['success' => false, 'error' => 'Entità non riconosciuta: ' . $entity];
        }

        $query = $class::find();

        $filters = Yii::$app->request->get('filters', []);
        if (is_string($filters)) {
            $filters = json_decode($filters, true) ?: [];
        }
        foreach ((array) $filters as $field => $value) {
            $query->andWhere([$field => $value]);
        }

        $records = $query->asArray()->all();

        if ($class === MgDocumento::className() && Yii::$app->request->get('with_righe')) {
            foreach ($records as &$r) {
                $r['righe'] = MgDocumentoRiga::find()->where(['id_documento' => $r['id']])
                    ->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC])->asArray()->all();
            }
            unset($r);
        }

        return ['success' => true, 'entity' => $entity, 'count' => count($records), 'records' => $records];
    }

    /**
     * GET: singolo record.
     */
    public function actionView()
    {
        $entity = Yii::$app->request->get('entity');
        $id = Yii::$app->request->get('id');
        $class = $this->resolveEntity($entity);
        if (!$class) {
            return ['success' => false, 'error' => 'Entità non riconosciuta: ' . $entity];
        }

        $record = $class::find()->where(['id' => $id])->asArray()->one();
        if (!$record) {
            Yii::$app->response->statusCode = 404;
            return ['success' => false, 'error' => 'Record non trovato.'];
        }

        if ($class === MgDocumento::className()) {
            $record['righe'] = MgDocumentoRiga::find()->where(['id_documento' => $id])
                ->orderBy(['ordine' => SORT_ASC, 'id' => SORT_ASC])->asArray()->all();
        }

        return ['success' => true, 'entity' => $entity, 'record' => $record];
    }

    /**
     * POST: importa (upsert) una lista di record.
     * Body: {"entity": "documenti", "records": [ {...}, ... ]}
     */
    public function actionImport()
    {
        $entity = Yii::$app->request->post('entity', Yii::$app->request->post('source_type'));
        $class = $this->resolveEntity($entity);
        if (!$class) {
            return ['success' => false, 'error' => 'Entità non riconosciuta: ' . $entity];
        }

        $records = Yii::$app->request->post('records', []);
        if (is_string($records)) {
            $records = json_decode($records, true) ?: [];
        }
        if (!is_array($records) || empty($records)) {
            return ['success' => false, 'error' => 'Nessun record da importare.'];
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($records as $i => $record) {
            if (!is_array($record)) {
                $errors[] = ['index' => $i, 'error' => 'Record non valido'];
                continue;
            }
            $res = $this->upsertRecord($class, $record);
            if ($res['success']) {
                if ($res['created']) {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                $errors[] = ['index' => $i, 'errors' => $res['errors']];
            }
        }

        return [
            'success' => empty($errors),
            'entity' => $entity,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * POST: elimina record per id.
     * Body: {"entity": "documenti", "ids": [1,2,3]}
     */
    public function actionDelete()
    {
        $entity = Yii::$app->request->post('entity', Yii::$app->request->post('source_type'));
        $class = $this->resolveEntity($entity);
        if (!$class) {
            return ['success' => false, 'error' => 'Entità non riconosciuta: ' . $entity];
        }

        $ids = Yii::$app->request->post('ids', []);
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: [];
        }
        if (empty($ids)) {
            return ['success' => false, 'error' => 'Nessun id da eliminare.'];
        }

        $deleted = $class::deleteAll(['id' => $ids]);
        return ['success' => true, 'entity' => $entity, 'deleted' => $deleted];
    }

    /**
     * Upsert di un singolo record.
     */
    private function upsertRecord($class, $record)
    {
        $model = null;

        if (!empty($record['id'])) {
            $model = $class::findOne($record['id']);
        }

        if (!$model) {
            $unique = $this->uniqueKeys($class);
            if (!empty($unique)) {
                $cond = [];
                foreach ($unique as $k) {
                    if (array_key_exists($k, $record)) {
                        $cond[$k] = $record[$k];
                    }
                }
                if (count($cond) === count($unique)) {
                    $model = $class::findOne($cond);
                }
            }
        }

        $created = false;
        if (!$model) {
            $model = new $class();
            $created = true;
        }

        // Risoluzione codici comodi (opzionali)
        if ($class === MgDocumento::className() && !empty($record['anagrafica_codice'])) {
            $ana = MgAnagrafica::findOne(['codice' => $record['anagrafica_codice']]);
            if ($ana) {
                $record['id_anagrafica'] = $ana->id;
            }
        }
        if ($class === MgDocumentoRiga::className() && !empty($record['articolo_codice'])) {
            $art = MgArticolo::findOne(['codice' => $record['articolo_codice']]);
            if ($art) {
                $record['id_articolo'] = $art->id;
            }
        }

        // Carica solo gli attributi noti al modello
        $attrs = array_flip($model->attributes());
        $data = array_intersect_key($record, $attrs);
        $model->load($data, '');

        if (!$model->validate()) {
            return ['success' => false, 'errors' => $model->getErrors()];
        }

        if (!$model->save(false)) {
            return ['success' => false, 'errors' => ['save' => 'Errore di salvataggio']];
        }

        return ['success' => true, 'created' => $created, 'id' => $model->id];
    }
}
