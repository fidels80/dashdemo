<?php

namespace app\controllers;

use Yii;
use app\models\Xroomlist;
use app\models\XroomlistSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\Xtravelrow;
use app\models\Xtappe;
 
/**
 * XroomlistController implements the CRUD actions for Xroomlist model.
 */
class XroomlistController extends Controller
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
     * Lists all Xroomlist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XroomlistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xroomlist model.
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
     * Creates a new Xroomlist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xroomlist();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ['success' => true];
            }
            
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('createaj', [
                'model' => $model,
            ]);
        }



        return $this->render('create', [
            'model' => $model,
        ]);
    }
    public function actionCreateaj($th_id)
    {
        $model = new Xroomlist();
        $model->th_id = $th_id;

        // Recupera un nuovo GUID da SQL Server
        $guid = Yii::$app->db->createCommand("SELECT NEWID()")->queryScalar();
        $model->id_guest = $guid;

        // IMPORTANTE: Imposta il formato JSON SUBITO per le richieste AJAX
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($model->load(Yii::$app->request->post())) {
                // Rigenera sempre un nuovo GUID prima di salvare
                $model->id_guest = Yii::$app->db
                    ->createCommand("SELECT NEWID()")
                    ->queryScalar();

                if ($model->save()) {
                    return [
                        'success' => true,
                        'data' => [
                            'id_guest' => $model->id_guest,
                            'nominativo' => $model->nominativo,
                            'cd_ar' => $model->cd_ar,
                            'ruolo' => $model->ruolo,
                            'party' => $model->party,
                            'commessa' => $model->commessa,
                            'note' => $model->note,
                        ]
                    ];
                }
            } else {
                // Errore di validazione
                return [
                    'success' => false,
                    'errors' => $model->errors,
                    'html' => $this->renderAjax('createaj', [
                        'model' => $model,
                    ])
                ];
            }
        }
        $guid = Yii::$app->db->createCommand("SELECT NEWID()")->queryScalar();
        $model->id_guest = $guid;
        // Rendering normale (solo se non è AJAX)
        return $this->render('createaj', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Xroomlist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $changedRows = []; // terrà traccia delle modifiche per l'email

        // Recuperiamo sempre le tappe per popolare la tabella nella vista
        //$tappeDisponibili =Xtappe::find()->where(['th_id' => $model->th_id])->all();
        $tappeDisponibili =  Xtappe::find()
            ->alias('t')
            ->innerJoin('xtravelrow r', 't.id_tappa = r.id_tappa')
            ->where(['t.th_id' => $model->th_id])
            ->andWhere(['r.id_nominativo' => $id])
            ->andWhere(['not', ['r.id_tappa' => null]])
            ->distinct() // Evita duplicati se l'ospite ha più righe nella stessa tappa
            ->all();


        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            // Recuperiamo i dati dai due diversi sistemi di propagazione
            $propagaTutto = Yii::$app->request->post('propaga', 0); // Vecchio checkbox
            $propagaTappe = Yii::$app->request->post('propaga_tappe', 0); // Nuovo checkbox trigger
            $tappeSelezionate = Yii::$app->request->post('tappe_selezionate', []); // Array di ID tappe

            // Procediamo se almeno uno dei due metodi di propagazione è attivo
            if ($propagaTutto || ($propagaTappe && !empty($tappeSelezionate))) {

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // Prepariamo la query base per recuperare le righe Xtravelrow
                    $query = Xtravelrow::find()
                        ->where(['id_nominativo' => $id])
                        ->andWhere(['not', ['id_nominativo' => null]])
                        ->andWhere(['th_id' => $model->th_id]);

                    // Se la propagazione è mirata alle tappe, aggiungiamo il filtro sulla colonna id_tappa
                    if ($propagaTappe && !empty($tappeSelezionate)) {
                        $query->andWhere(['id_tappa' => $tappeSelezionate]);
                    }

                    $rows = $query->all();

                    foreach ($rows as $row) {
                        $changed = false;
                        $rowChanges = [];
                        $cd_cf_ft_valore = (new \yii\db\Query())
                            ->select(['cd_cf'])
                            ->from('adb_auxcoop.dbo.DOSottoCommessa')
                            ->where(['Cd_DOSottoCommessa' => $model->commessa])
                            ->scalar();
                        // 2. Recupero la descrizione dalla tabella cf usando il cd_cf appena trovato
                        $descli_valore = null;
                        if ($cd_cf_ft_valore) {
                            $descli_valore = (new \yii\db\Query())
                                ->select(['descrizione'])
                                ->from('adb_auxcoop.dbo.cf')
                                ->where(['cd_cf' => $cd_cf_ft_valore])
                                ->scalar();
                        }


                        // Sincronizzazione campi
                        if ($row->guest !== $model->nominativo) {
                            $rowChanges['guest'] = ['old' => $row->guest, 'new' => $model->nominativo];
                            $row->guest = $model->nominativo;
                            $changed = true;
                        }
                        if ($row->cd_Ar !== $model->cd_ar) {
                            $rowChanges['cd_Ar'] = ['old' => $row->cd_Ar, 'new' => $model->cd_ar];
                            $row->cd_Ar = $model->cd_ar;
                            $changed = true;
                        }
                        if ($row->ruolo !== $model->ruolo) {
                            $rowChanges['ruolo'] = ['old' => $row->ruolo, 'new' => $model->ruolo];
                            $row->ruolo = $model->ruolo;
                            $changed = true;
                        }
                        if ($row->party !== $model->party) {
                            $rowChanges['party'] = ['old' => $row->party, 'new' => $model->party];
                            $row->party = $model->party;
                            $changed = true;
                        }
                        if ($row->sottocommessa !== $model->commessa) {
                            $rowChanges['sottocommessa'] = ['old' => $row->sottocommessa, 'new' => $model->commessa];
                            $row->sottocommessa = $model->commessa;
                            $changed = true;
                        }
                        if ($row->cd_cf_ft !== $cd_cf_ft_valore) {
                            $rowChanges['cd_cf_ft'] = ['old' => $row->cd_cf_ft, 'new' => $cd_cf_ft_valore];
                            $row->cd_cf_ft = $cd_cf_ft_valore;
                            $changed = true;
                        }
                        // Campo descli
                        if ($row->descli !== $descli_valore) {
                            $rowChanges['descli'] = ['old' => $row->descli, 'new' => $descli_valore];
                            $row->descli = $descli_valore;
                            $changed = true;
                        }
                        if ($changed) {
                            $attributiDaSalvare = ['guest', 'cd_Ar', 'ruolo', 'party', 
                            'sottocommessa', 'cd_cf_ft',
                                'descli'];
                            if (!$row->save(true, $attributiDaSalvare)) {
                                $errori = json_encode($row->getErrors(), JSON_UNESCAPED_UNICODE);
                                throw new \Exception("Errore salvataggio riga ID: {$row->tr_id}. Dettagli: " . $errori);
                            }
                            $changedRows[] = [
                                'id' => $row->tr_id,
                                'changes' => $rowChanges
                            ];
                        }
                    }

                    $transaction->commit();

                    // Invio email di notifica (solo se ci sono stati cambiamenti effettivi)
                    if (!empty($changedRows)) {
                        $this->sendEmail(
                            'marco.cardinale@ilvbc.it',
                            $model->nominativo,
                            $id,
                            $changedRows,
                            $model,
                            new \Exception('Aggiornamento XTravel completato con successo')
                        );
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }
            }

            // Redirect post-salvataggio
            $url = Url::to(['xtravelhead/masterhotel', 'id' => $model->th_id]);
            $backUrl = Url::to(['xtravelhead/tool', 'id' => $model->th_id]) ?: $url;
            return $this->redirect($backUrl);
        }

        return $this->render('update', [
            'model' => $model,
            'tappeDisponibili' => $tappeDisponibili,
        ]);
    }
    public function old_actionUpdate($id)
    {
        $model = $this->findModel($id);
        $changedRows = []; // terrà traccia delle modifiche
        $tappeDisponibili = Xtappe::find()->where(['th_id' => $model->th_id])->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $propaga = Yii::$app->request->post('propaga', 0); // default 0

            if ($propaga) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    // Recupero tutte le righe da aggiornare
                    $rows = Xtravelrow::find()
                        ->where(['id_nominativo' => $id])
                        ->andWhere(['not', ['id_nominativo' => null]])
                        ->andwhere(['th_id'=>$model->th_id])
                        ->all();

                    // Aggiorno ogni riga solo se i valori sono diversi
                    foreach ($rows as $row) {
                        $changed = false;

                        if ($row->guest !== $model->nominativo) {
                            $rowChanges['guest'] = ['old' => $row->guest, 'new' => $model->nominativo];
                            $row->guest = $model->nominativo;
                            $changed = true;
                        }
                        if ($row->cd_Ar !== $model->cd_ar) {
                            $rowChanges['cd_Ar'] = ['old' => $row->cd_Ar, 'new' => $model->cd_ar];
                            $row->cd_Ar = $model->cd_ar;
                           // $row->descrizione=
                            $changed = true;
                        }
                        if ($row->ruolo !== $model->ruolo) {
                            $rowChanges['ruolo'] = ['old' => $row->ruolo, 'new' => $model->ruolo];
                            $row->ruolo = $model->ruolo;
                            $changed = true;
                        }
                        if ($row->party !== $model->party) {
                            $rowChanges['party'] = ['old' => $row->party, 'new' => $model->party];
                            $row->party = $model->party;
                            $changed = true;
                        }
                        if ($row->sottocommessa !== $model->commessa) {
                            $rowChanges['sottocommessa'] = ['old' => $row->sottocommessa, 'new' => $model->commessa];
                            $row->sottocommessa = $model->commessa;
                            $changed = true;
                        }
                    //$row->pagato = !empty($row->pagato) ? 1 : 0;
                        if ($changed) {
                            // Definiamo quali campi vogliamo validare
                            $attributiDaSalvare = ['guest', 'cd_Ar', 'ruolo', 'party', 'sottocommessa'];

                            if (!$row->save(true, $attributiDaSalvare)) {
                                $errori = json_encode($row->getErrors(), JSON_UNESCAPED_UNICODE);
                                throw new \Exception("Errore validazione campi propagati riga ID: {$row->tr_id}. Dettagli: " . $errori);
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
                        $model->nominativo,   // username
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
            'tappeDisponibili' => $tappeDisponibili, 
        ]);
    }


    /**
     * Deletes an existing Xroomlist model.
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
     * Finds the Xroomlist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Xroomlist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xroomlist::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



    private function sendEmail($email, $username, $jobId, $changedRows,
     $modello, $exceptio)
    {
        try {
            $subject = 'aggironamento nominativi - ' . $jobId;

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
                'corpo'=> $body
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
}
