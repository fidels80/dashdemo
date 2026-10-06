<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\components\ApiAccess;
use app\components\ApiIntegrity;
use app\models\ApiToken;
use app\models\DashApiEntita;
use app\models\DashApiRel;
use app\models\MgAnagrafica;
use app\models\MgArticolo;
use app\models\MgUnitaMisura;

/**
 * Servizio REST per import/export dati (autenticazione Bearer token).
 *
 * Ogni entita' e ogni operazione sono soggette ai permessi del token, definiti
 * in api_token.scopes con la forma "entita:operazione" (vedi ApiAccess).
 * Le entita' disponibili sono quelle registrate in dash_api_entita.
 *
 * Endpoint (URL non pretty):
 *   GET  index.php?r=api/index                       stato, entita' e permessi
 *   GET  index.php?r=api/export&entity=documenti      esportazione
 *   GET  index.php?r=api/view&entity=documenti&id=1   singolo record
 *   POST index.php?r=api/import                       {entity, records[]}
 *   POST index.php?r=api/delete                       {entity, ids[], cascade}
 *
 * Header: Authorization: Bearer <token>
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Entita' associata alla richiesta (validata contro i permessi).
     *
     * @var DashApiEntita|null
     */
    private $entita;

    /**
     * Token autenticato nella richiesta corrente.
     *
     * @var ApiToken|null
     */
    private $apiToken;

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

        $plain = $this->getBearerToken();
        if (empty($plain)) {
            Yii::$app->response->data = $this->fallito(401, 'Token Bearer mancante.');
            return false;
        }

        $this->apiToken = ApiToken::findValid($plain);
        if (!$this->apiToken) {
            Yii::$app->response->data = $this->fallito(401, 'Token non valido o scaduto.');
            return false;
        }
        $this->apiToken->markUsed();

        ApiAccess::setCurrentToken($this->apiToken);

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

    /**
     * Risposta d'errore strutturata.
     *
     * @param int $code
     * @param string $message
     * @param array $extra
     * @return array
     */
    private function fallito($code, $message, array $extra = [])
    {
        Yii::$app->response->statusCode = $code;
        return array_merge(['success' => false, 'error' => $message], $extra);
    }

    /**
     * Risposta di permesso negato.
     *
     * @param DashApiEntita $entita
     * @param string $operazione
     * @return array
     */
    private function negato(DashApiEntita $entita, $operazione)
    {
        return $this->fallito(403, ApiAccess::errore($entita, $operazione, $this->apiToken), [
            'entity' => $entita->codice,
            'operazione' => $operazione,
        ]);
    }

    /**
     * Risposta di entita' inesistente o non attiva.
     *
     * @param string $name
     * @return array
     */
    private function entitaIgnorata($name)
    {
        return $this->fallito(404, sprintf(
            'Entità non riconosciuta: "%s". Le entità disponibili sono %s.',
            $name,
            implode(', ', array_map(function ($e) {
                return '"' . $e->codice . '"';
            }, ApiAccess::entitaConcesse($this->apiToken))) ?: 'nessuna'
        ));
    }

    /**
     * Risolve l'entita' dalla richiesta.
     *
     * Se $operazione e' indicata viene verificato anche il permesso su
     * quell'operazione: cosi' un token "insert-only" o "delete-only" puo'
     * usare l'entita' senza dover avere la lettura.
     *
     * @param string|null $operazione una delle costanti ApiAccess::OP_*
     * @return DashApiEntita|array|null entita' se Ok, array di errore altrimenti
     */
    private function risolviEntita($operazione = null)
    {
        $name = Yii::$app->request->get('entity');
        if (empty($name)) {
            $name = Yii::$app->request->post('entity', Yii::$app->request->post('source_type'));
        }

        $entita = DashApiEntita::findByName($name);
        if ($entita === null || !$entita->attiva) {
            return $this->entitaIgnorata($name);
        }

        $this->entita = $entita;

        if ($operazione !== null && !ApiAccess::can($entita->codice, $operazione, $this->apiToken)) {
            return $this->negato($entita, $operazione);
        }

        return $entita;
    }

    /**
     * Verifica l'accesso a un'entita' con una operazione specifica.
     *
     * @param string $codice
     * @param string $operazione
     * @return array|null payload di errore, null se autorizzato
     */
    private function verificaPermesso($codice, $operazione)
    {
        if (ApiAccess::can($codice, $operazione, $this->apiToken)) {
            return null;
        }
        $entita = DashApiEntita::findOne(['codice' => $codice]);
        if ($entita === null) {
            return $this->entitaIgnorata($codice);
        }
        return $this->negato($entita, $operazione);
    }

    /**
     * GET: stato del servizio, entita' utilizzabili e permessi del token.
     */
    public function actionIndex()
    {
        $entita = ApiAccess::entitaConcesse($this->apiToken);
        $scope = ApiAccess::scope($this->apiToken);
        $operazioni = ApiAccess::operazioni();

        $counts = [];
        $dettaglio = [];
        foreach ($entita as $e) {
            $model = $e->getModel();
            $n = $model === null ? 0 : (int) $model::find()->count();

            $counts[$e->codice] = $n;

            $ops = [];
            foreach ($operazioni as $op => $etichetta) {
                $ops[$op] = ApiAccess::can($e->codice, $op, $this->apiToken);
            }

            $dettaglio[] = [
                'entity' => $e->codice,
                'descrizione' => $e->descrizione,
                'tabella' => $e->tabella,
                'count' => $n,
                'sola_lettura' => (bool) $e->sola_lettura,
                'cancellabile' => (bool) $e->cancellabile,
                'operazioni' => $ops,
                'vincoli' => ApiIntegrity::vincoliEntita($e->codice),
            ];
        }

        return [
            'success' => true,
            'service' => 'dashdemo-api',
            'version' => '2.0.0',
            'token' => $this->apiToken ? $this->apiToken->descrizione : null,
            'scopes' => $this->apiToken ? $this->apiToken->scopes : null,
            'counts' => $counts,
            'entita' => $dettaglio,
        ];
    }

    /**
     * GET: esporta i record di una entita'.
     */
    public function actionExport()
    {
        $entita = $this->risolviEntita(ApiAccess::OP_READ);
        if (!$entita instanceof DashApiEntita) {
            return $entita;
        }

        $model = $entita->getModel();
        if ($model === null) {
            return $this->fallito(500, sprintf('Modello non valido per l\'entità "%s".', $entita->descrizione));
        }

        $query = $model::find();

        $filters = Yii::$app->request->get('filters', []);
        if (is_string($filters)) {
            $filters = json_decode($filters, true);
            if (!is_array($filters)) {
                return $this->fallito(400, 'Il parametro "filters" deve essere un JSON valido.');
            }
        }
        if (!is_array($filters)) {
            return $this->fallito(400, 'Il parametro "filters" deve essere un oggetto JSON.');
        }

        // Solo colonne esistenti sul modello: nessun filtro arbitrario.
        $attributi = array_flip($model->attributes());
        $sconosciute = array_values(array_diff(array_keys($filters), array_keys($attributi)));
        if (!empty($sconosciute)) {
            return $this->fallito(400, sprintf(
                'Campi di filtro inesistenti in "%s": %s.',
                $entita->tabella,
                implode(', ', $sconosciute)
            ), ['campi_validi' => array_keys($attributi)]);
        }
        foreach ($filters as $field => $value) {
            $query->andWhere([$field => $value]);
        }

        $limit = (int) Yii::$app->request->get('limit', 0);
        if ($limit > 0) {
            $query->limit($limit)->offset((int) Yii::$app->request->get('offset', 0));
        }

        $records = $query->asArray()->all();

        $with = $this->entitaDaAllegare($entita);
        foreach ($with as $figlia) {
            $this->allegaFigli($records, $figlia);
        }

        return [
            'success' => true,
            'entity' => $entita->codice,
            'count' => count($records),
            'records' => $records,
        ];
    }

    /**
     * GET: singolo record.
     */
    public function actionView()
    {
        $entita = $this->risolviEntita(ApiAccess::OP_READ);
        if (!$entita instanceof DashApiEntita) {
            return $entita;
        }

        $model = $entita->getModel();
        if ($model === null) {
            return $this->fallito(500, sprintf('Modello non valido per l\'entità "%s".', $entita->descrizione));
        }

        $id = Yii::$app->request->get('id');
        $record = $model::find()->where(['id' => $id])->asArray()->one();
        if (!$record) {
            Yii::$app->response->statusCode = 404;
            return ['success' => false, 'error' => 'Record non trovato.'];
        }

        foreach ($this->entitaDaAllegare($entita) as $figlia) {
            $record[$figlia->codice] = $this->figliDi($figlia, $id);
        }

        return ['success' => true, 'entity' => $entita->codice, 'record' => $record];
    }

    /**
     * POST: importa (upsert) una lista di record.
     * Body: {"entity": "documenti", "records": [{...}]}
     */
    public function actionImport()
    {
        $entita = $this->risolviEntita();
        if (!$entita instanceof DashApiEntita) {
            return $entita;
        }

        $puoScrivere = ApiAccess::can($entita->codice, ApiAccess::OP_INSERT, $this->apiToken)
            || ApiAccess::can($entita->codice, ApiAccess::OP_UPDATE, $this->apiToken);
        if (!$puoScrivere) {
            return $this->negato($entita, ApiAccess::OP_UPDATE);
        }

        $records = Yii::$app->request->post('records', []);
        if (is_string($records)) {
            $records = json_decode($records, true) ?: [];
        }
        if (!is_array($records) || empty($records)) {
            return $this->fallito(400, 'Nessun record da importare.');
        }

        $model = $entita->getModel();
        if ($model === null) {
            return $this->fallito(500, sprintf('Modello non valido per l\'entità "%s".', $entita->descrizione));
        }

        $created = 0;
        $updated = 0;
        $errors = [];

        foreach ($records as $i => $record) {
            if (!is_array($record)) {
                $errors[] = ['index' => $i, 'error' => 'Record non valido'];
                continue;
            }

            $esistente = $this->trovaEsistente($entita, $record);

            // L'operazione richiesta dipende dal fatto che il record esista gia'.
            $operazione = $esistente === null ? ApiAccess::OP_INSERT : ApiAccess::OP_UPDATE;
            $negato = $this->verificaPermesso($entita->codice, $operazione);
            if ($negato !== null) {
                $errors[] = ['index' => $i, 'error' => $negato['error'], 'operazione' => $operazione];
                continue;
            }

            $res = $this->salvaRecord($entita, $model, $record, $esistente);
            if ($res['success']) {
                if ($esistente === null) {
                    $created++;
                } else {
                    $updated++;
                }
            } else {
                $errors[] = ['index' => $i, 'errors' => $res['errors']];
            }
        }

        Yii::$app->response->statusCode = empty($errors) ? 200 : 422;

        return [
            'success' => empty($errors),
            'entity' => $entita->codice,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * POST: elimina record per id, verificando l'integrita' referenziale.
     * Body: {"entity": "documenti", "ids": [1,2], "cascade": false}
     */
    public function actionDelete()
    {
        $entita = $this->risolviEntita();
        if (!$entita instanceof DashApiEntita) {
            return $entita;
        }

        $negato = $this->verificaPermesso($entita->codice, ApiAccess::OP_DELETE);
        if ($negato !== null) {
            return $negato;
        }

        $ids = Yii::$app->request->post('ids', []);
        if (is_string($ids)) {
            $ids = json_decode($ids, true) ?: [];
        }
        if (empty($ids)) {
            return $this->fallito(400, 'Nessun id da eliminare.');
        }

        $cascade = filter_var(
            Yii::$app->request->post('cascade', false),
            FILTER_VALIDATE_BOOLEAN
        );

        $res = ApiIntegrity::cancella($entita, (array) $ids, $cascade, $this->apiToken);

        if (!$res['ok']) {
            Yii::$app->response->statusCode = 409;
            return [
                'success' => false,
                'entity' => $entita->codice,
                'error' => $res['error'],
                'vincoli' => $res['vincoli'],
                'cascade' => $cascade,
            ];
        }

        Yii::$app->response->statusCode = 200;

        return [
            'success' => true,
            'entity' => $entita->codice,
            'deleted' => $res['deleted'],
            'cascade' => $cascade,
            'cascaded' => $res['cascaded'],
            'skipped' => $res['skipped'],
            // id non eliminati perche' vincolati, anche se altri sono andati a buon fine
            'vincoli' => $res['vincoli'],
        ];
    }

    /**
     * Cerca il record esistente a cui l'upsert si riferisce:
     * per id oppure per chiave naturale dell'entita'.
     *
     * @param DashApiEntita $entita
     * @param array $record
     * @return \yii\db\ActiveRecord|null
     */
    private function trovaEsistente(DashApiEntita $entita, array $record)
    {
        if (!empty($record['id'])) {
            $model = $entita->getModel();
            $trovato = $model::findOne($record['id']);
            if ($trovato !== null) {
                return $trovato;
            }
        }

        $chiave = $entita->getChiaveUpsertArray();
        if (empty($chiave)) {
            return null;
        }

        $cond = [];
        foreach ($chiave as $colonna) {
            if (!array_key_exists($colonna, $record)) {
                return null;   // chiave incompleta: non e' un upsert
            }
            $cond[$colonna] = $record[$colonna];
        }

        $model = $entita->getModel();
        return $model::findOne($cond);
    }

    /**
     * Valida e salva un singolo record.
     *
     * @param DashApiEntita $entita
     * @param \yii\db\ActiveRecord $model
     * @param array $record
     * @param \yii\db\ActiveRecord|null $esistente
     * @return array{success: bool, errors: array, id: mixed}
     */
    private function salvaRecord(DashApiEntita $entita, $model, array $record, $esistente)
    {
        if ($esistente === null) {
            $classe = get_class($model);
            $model = new $classe();
        }

        $record = $this->risolviCodiciEsterni($model, $record);

        // Carica solo gli attributi noti al modello
        $data = array_intersect_key($record, array_flip($model->attributes()));
        $model->load($data, '');

        if (!$model->validate()) {
            return ['success' => false, 'errors' => $model->getErrors()];
        }

        try {
            if (!$model->save(false)) {
                return ['success' => false, 'errors' => ['save' => 'Errore di salvataggio']];
            }
        } catch (\yii\db\IntegrityException $e) {
            return ['success' => false, 'errors' => ['save' => 'Violazione di un vincolo del database: ' . $e->getMessage()]];
        }

        return ['success' => true, 'errors' => [], 'id' => $model->id];
    }

    /**
     * Traduce i codici comuni in chiavi esterne, se il modello le espone.
     * anagrafica_codice -> id_anagrafica, articolo_codice -> id_articolo,
     * unita_misura_codice -> id_unita_misura.
     *
     * @param \yii\db\ActiveRecord $model
     * @param array $record
     * @return array
     */
    private function risolviCodiciEsterni($model, array $record)
    {
        $attributi = $model->attributes();

        if (!empty($record['anagrafica_codice']) && in_array('id_anagrafica', $attributi, true)) {
            $ana = MgAnagrafica::findOne(['codice' => $record['anagrafica_codice']]);
            $record['id_anagrafica'] = $ana ? $ana->id : null;
        }

        if (!empty($record['articolo_codice']) && in_array('id_articolo', $attributi, true)) {
            $art = MgArticolo::findOne(['codice' => $record['articolo_codice']]);
            $record['id_articolo'] = $art ? $art->id : null;
        }

        if (!empty($record['unita_misura_codice']) && in_array('id_unita_misura', $attributi, true)) {
            $um = MgUnitaMisura::findOne(['codice' => $record['unita_misura_codice']]);
            $record['id_unita_misura'] = $um ? $um->id : null;
        }

        unset($record['anagrafica_codice'], $record['articolo_codice'], $record['unita_misura_codice']);

        return $record;
    }

    /**
     * Entita' figlie da allegare ai record esportati, in base ai parametri
     * "with" (lista di codici) e al parametro legacy "with_righe".
     *
     * @param DashApiEntita $entita
     * @return array[] lista di ['entita' => DashApiEntita, 'regola' => DashApiRel]
     */
    private function entitaDaAllegare(DashApiEntita $entita)
    {
        $richiesti = [];

        $with = Yii::$app->request->get('with');
        if (is_string($with) && $with !== '') {
            $richiesti = array_filter(array_map('trim', explode(',', $with)));
        }
        if (Yii::$app->request->get('with_righe') && $entita->tabella === 'mg_documento') {
            $richiesti[] = 'righe';
        }
        if (empty($richiesti)) {
            return [];
        }

        // Solo figli realmente collegati a questa entita' e leggibili dal token.
        $out = [];
        foreach ($this->figliDiEntita($entita) as $coppia) {
            if (in_array($coppia['entita']->codice, $richiesti, true)) {
                $out[] = $coppia;
            }
        }
        return $out;
    }

    /**
     * Entita' che hanno righe figlie verso l'entita' indicata.
     *
     * @param DashApiEntita $entita
     * @return array[] lista di ['entita' => DashApiEntita, 'regola' => DashApiRel]
     */
    private function figliDiEntita(DashApiEntita $entita)
    {
        $out = [];
        foreach (DashApiRel::perEntita($entita->codice) as $regola) {
            if (!$regola->isFiglio()) {
                continue;
            }
            $figlia = DashApiEntita::findOne(['tabella' => $regola->tabella]);
            if ($figlia === null
                || !$figlia->attiva
                || !ApiAccess::can($figlia->codice, ApiAccess::OP_READ, $this->apiToken)) {
                continue;
            }
            $out[] = ['entita' => $figlia, 'regola' => $regola];
        }
        return $out;
    }

    /**
     * Righe figlie di un singolo record padre.
     *
     * @param array $coppia ['entita' => DashApiEntita, 'regola' => DashApiRel]
     * @param mixed $idPadre
     * @return array
     */
    private function figliDi(array $coppia, $idPadre)
    {
        /** @var DashApiEntita $figlia */
        $figlia = $coppia['entita'];
        /** @var DashApiRel $regola */
        $regola = $coppia['regola'];

        $model = $figlia->getModel();
        if ($model === null) {
            return [];
        }

        return $model::find()
            ->where([$regola->colonna => $idPadre])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * Allega i figli a tutti i record di un'esportazione.
     *
     * @param array $records
     * @param array $coppia ['entita' => DashApiEntita, 'regola' => DashApiRel]
     */
    private function allegaFigli(array &$records, array $coppia)
    {
        if (empty($records)) {
            return;
        }

        $ids = [];
        foreach ($records as $r) {
            if (isset($r['id'])) {
                $ids[] = $r['id'];
            }
        }
        if (empty($ids)) {
            return;
        }

        $figlia = $coppia['entita'];
        $colonna = $coppia['regola']->colonna;

        $model = $figlia->getModel();
        if ($model === null) {
            return;
        }

        $righe = $model::find()
            ->where([$colonna => $ids])
            ->orderBy(['id' => SORT_ASC])
            ->asArray()
            ->all();

        $gruppo = [];
        foreach ($righe as $r) {
            $chiave = (string) $r[$colonna];
            if (!isset($gruppo[$chiave])) {
                $gruppo[$chiave] = [];
            }
            $gruppo[$chiave][] = $r;
        }

        foreach ($records as $k => $r) {
            $records[$k][$figlia->codice] = isset($gruppo[(string) $r['id']])
                ? $gruppo[(string) $r['id']]
                : [];
        }
    }
}
