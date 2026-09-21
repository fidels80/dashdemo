<?php

namespace app\controllers;

use Yii;
use app\models\Xtappe;
use app\models\XtappeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\Xtravelrow;

/**
 * XtappeController implements the CRUD actions for Xtappe model.
 */
class XtappeController extends Controller
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * Lists all Xtappe models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XtappeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xtappe model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Xtappe model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xtappe();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_tappa]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Xtappe model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $changedRows = []; // terrà traccia delle modifiche
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $propaga = Yii::$app->request->post('propaga', 0); // default 0

            if ($propaga) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    // Recupero tutte le righe da aggiornare
                    $rows = Xtravelrow::find()
                        ->where(['id_tappa' => $id])
                        ->andWhere(['not', ['id_tappa' => null]])
                        ->all();

                    // Aggiorno ogni riga solo se i valori sono diversi
                    foreach ($rows as $row) {
                        $changed = false;

                        if ($row->citta !== $model->citta) {
                            $rowChanges['id_tappa '] = ['old' => $row->citta, 
                            'new' => $model->citta];
                            $rowChanges['des_tappa '] =['old'=>
                        $this->descodec($row->citta),
                                'new' =>     $this->descodec($model->citta)
                        ];
                                $row->citta = $model->citta;
                            $changed = true;
                        }
                        if ($row->check_in !== $model->data) {
                            $rowChanges['chechk_in'] = ['old' => $row->check_in,
                             'new' => $model->data];
                            $row->cd_Ar = $model->data;
                            // $row->descrizione=
                            $changed = true;
                        }
                  
                        
                        

                        if ($changed) {
                    
                            if (!$row->save()) { // salva senza validazioni
                              throw new \Exception('Errore nel salvataggio della riga ID: ' . $row->id);
                           }
                            $changedRows[] = [
                                'id' => $row->tr_id,
                                'changes' => $rowChanges
                            ];
                        }
                    }

                    $transaction->commit(); // conferma tutte le modifiche
                    // --- Invio email solo se il commit ha avuto successo ---
                    $this->sendEmail(
                        'marco.cardinale@ilvbc.it',  //$model->email,        // indirizzo email del destinatario
                        $model->citta,   // username
                        $id, //idnominativo
                        $changedRows, //righe cambiate
                        $model,      // modello
                        new \Exception('Aggiornamento XTravel completato con successo') // puoi passare un oggetto Exception o un messaggio personalizzato
                    );
                } catch (\Exception $e) {
                    $transaction->rollBack(); // annulla tutto in caso di errore
                    throw $e; // rilancia l'eccezione
                }

                $url = Url::to(['xtravelhead/masterhotel', 'id' => $model->th_id]);
                $backUrl = Url::to(['xtravelhead/tool', 'id' => $model->th_id]) ?: $url;


                return $this->redirect($backUrl);
            }


            $url = Url::to(['xtravelhead/masterhotel', 'id' => $model->th_id]);
            $backUrl = Url::to(['xtravelhead/tool', 'id' => $model->th_id]) ?: $url;

            return $this->redirect($backUrl);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Xtappe model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Xtappe model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Xtappe the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xtappe::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    private function sendEmail(
        $email,
        $username,
        $jobId,
        $changedRows,
        $modello,
        $exceptio
    ) {
        try {
            $subject = 'aggironamento Tappe - ' . $jobId;

            $body = "Ciao ,\n\n";
            $body .= "L'aggiornamento  è stato completato con successo.\n\n";
            $body .= "Dettagli modifiche:\n";
            $body .= "-  ID: $jobId\n";
            $body .= "- Numero righe modificate: " . count($changedRows) . "\n\n";

            foreach ($changedRows as $row) {
                $body .= "Riga ID: {$row['id']}\n";
                foreach ($row['changes'] as $field => $values) {
                    $body .= "  $field: '{$values['old']}' → '{$values['new']}'\n";
                }
                $body .= "\n";
            }

            $body .= "Data/Ora: " . date('Y-m-d H:i:s') . "\n\n";
            $body .= "Saluti,\nSistema modifica  XTravel";
            Yii::$app->mailer->compose()
                ->setFrom(['dashboard@planorys.com'
                => 'Dashboard Planorys'])
                ->setTo($email)
                ->setBcc('dashboard@planorys.com')
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();
            $logFile = Yii::getAlias('@app/runtime/logs/aggiornamento_righe_propagazione' . date('Y-m-d') . '.log');
            $this->logOperation($logFile, "email inviata", [
                'oggetto' => $subject,
                'job_id' => $jobId,
                'corpo' => $body
            ]);
        } catch (\Exception $e) {
            $logFile = Yii::getAlias('@app/runtime/logs/aggiornamento_righe_propagazione' . date('Y-m-d') . '.log');
            $this->logOperation($logFile, "email inviata", [
                'oggetto' => $subject,
                'job_id' => $jobId,
                'corpo' => $e->getMessage()
            ]);
            error_log("Errore invio email errore: " . $e->getMessage());
        }
    }



    private function logOperation($logFile, $message, $data = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $memory = round(memory_get_usage(true) / 1024 / 1024, 2);

        $logLine = sprintf(
            "[%s] [%s MB] %s %s\n",
            $timestamp,
            $memory,
            $message,
            !empty($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : ''
        );

        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    } 
    private function descodec($id){


        if (empty($id)) {
            return null;
        }

        // Se hai un model chiamato XVenue
        $venue = \app\models\XVenue::find()
            ->select('venue')
            ->where(['id' => $id])
            ->scalar();  // restituisce direttamente il valore della colonna

        return $venue ?: null;


    }
}
