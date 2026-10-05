<?php

namespace app\controllers;

use Yii;
use app\components\ApiAccess;
use app\models\ApiToken;
use app\models\DashApiEntita;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * Gestione dei token Bearer per il servizio REST.
 *
 * I permessi si impostano per entita' e per operazione (lettura, inserimento,
 * modifica, cancellazione) e vengono salvati nella colonna api_token.scopes.
 */
class ApitokenController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ApiToken::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => false,
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    public function actionCreate()
    {
        $model = new ApiToken();
        $scopes = '';

        if (Yii::$app->request->isPost) {
            $scopes = ApiToken::composiScopes($this->permessiPostati());

            $scadenza = Yii::$app->request->post('scadenza');
            $expiresAt = null;
            if (!empty($scadenza)) {
                $expiresAt = strtotime($scadenza . ' 23:59:59');
            }

            if ($scopes === '') {
                Yii::$app->session->setFlash('error', 'Seleziona almeno un\'entità e un\'operazione.');
            } else {
                $plain = ApiToken::generate(
                    Yii::$app->request->post('descrizione') ?: 'Token API',
                    Yii::$app->user->id,
                    $expiresAt,
                    $scopes
                );

                if ($plain) {
                    Yii::$app->session->setFlash('token_generato', $plain);
                    return $this->redirect(['index']);
                }
                Yii::$app->session->setFlash('error', 'Impossibile generare il token.');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'entita' => DashApiEntita::attive(),
            'operazioni' => ApiAccess::operazioni(),
            'selezionati' => [],
        ]);
    }

    /**
     * Modifica i permessi di un token esistente senza rigenerarlo.
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {
            $scopes = ApiToken::composiScopes($this->permessiPostati());

            if ($scopes === '') {
                Yii::$app->session->setFlash('error', 'Seleziona almeno un\'entità e un\'operazione.');
            } else {
                $model->scopes = $scopes;
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Permessi del token aggiornati.');
                    return $this->redirect(['index']);
                }
                Yii::$app->session->setFlash('error', 'Aggiornamento non riuscito.');
            }
        }

        $selezionati = $model->getScope();

        return $this->render('update', [
            'model' => $model,
            'entita' => DashApiEntita::attive(),
            'operazioni' => ApiAccess::operazioni(),
            'selezionati' => $selezionati,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Token revocato.');
        return $this->redirect(['index']);
    }

    public function actionTest()
    {
        return $this->render('test', [
            'endpoints' => [
                'index' => Url::to(['/api/index'], true),
                'export' => Url::to(['/api/export'], true),
                'view' => Url::to(['/api/view'], true),
                'import' => Url::to(['/api/import'], true),
                'delete' => Url::to(['/api/delete'], true),
            ],
            'entita' => DashApiEntita::attive(),
        ]);
    }

    /**
     * Permessi arrivati dal form: array codice => lista di operazioni.
     *
     * @return array<string, string[]>
     */
    private function permessiPostati()
    {
        $post = Yii::$app->request->post('permessi', []);
        if (is_string($post)) {
            $post = json_decode($post, true);
        }
        if (!is_array($post)) {
            return [];
        }

        $out = [];
        foreach ($post as $codice => $ops) {
            $codice = trim((string) $codice);
            if ($codice === '' || !is_array($ops)) {
                continue;
            }
            $norm = ApiAccess::normalizzaOperazioni($ops);
            if (!empty($norm)) {
                $out[$codice] = $norm;
            }
        }
        return $out;
    }

    protected function findModel($id)
    {
        if (($model = ApiToken::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Token non trovato.');
    }
}
