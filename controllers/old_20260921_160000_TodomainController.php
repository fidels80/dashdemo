<?php

namespace app\controllers;

use Yii;
use app\models\Todomain;
use app\models\TodomainSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
/*use garethp\ews\API;
use garethp\ews\API\Message\CreateItemType;
use garethp\ews\API\Message\SendItemType;
use garethp\ews\API\Type\CalendarItemType;
use garethp\ews\API\Type\MessageType;
use garethp\ews\API\Type\EmailAddressType;
use garethp\ews\API\Type\SingleRecipientType;
*/
use app\models\Todocommenti;
 use app\models\Todotag;
use yii\web\Response;
use app\models\Todoreltags;
use app\models\User;
/**
 * TodomainController implements the CRUD actions for Todomain model.
 */
class TodomainController extends Controller
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
     * Lists all Todomain models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TodomainSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Todomain model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $commentModel = new Todocommenti();
        $comments = Todocommenti::find()->where(['id_todo' => $id])
            ->orderBy(['data' => SORT_DESC]) // Ordina i commenti per data in ordine decrescente
            ->all();

        return $this->render('view', [
            'model' => $model,
            'commentModel' => $commentModel,
            'comments' => $comments,
        ]);
    }


    /**
     * Creates a new Todomain model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Todomain();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            Todoreltags::deleteAll(['id_to_do' => $model->id]);

            // Inserisci i nuovi tag
            foreach ($model->tagValues as $tag) {
                $relTag = new Todoreltags();
                $relTag->id_to_do = $model->id;
                $relTag->tag = $tag;
                $relTag->save();
            }

           
           
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Todomain model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // Converti i tagValues in una stringa separata da virgole prima di salvare
            $model->tagValues = array_filter($model->tagValues, function ($value) {
                return trim($value) !== ''; // Rimuove i valori vuoti (stringhe vuote o null)
            });


            $tagIds = [];
            foreach ($model->tagValues as $tagValue) {
                $tag = Todotag::findOne(['Tag' => $tagValue]);
                if (!$tag) {
                    // Se il tag non esiste, crea un nuovo record
                    $newTag = new Todotag();
                    $newTag->Tag = $tagValue;
                    if ($newTag->save()) {
                        $tagIds[] = $newTag->id; // Aggiungi l'ID del nuovo tag alla lista
                    }
                } else {
                    $tagIds[] = $tag->id; // Aggiungi l'ID del tag esistente alla lista
                }
            }







            $model->tags = implode(',', $model->tagValues);

            if ($model->save()) {

                Todoreltags::deleteAll(['id_to_do' => $model->id]);

                // Inserisci i nuovi tag
                foreach ($model->tagValues as $tag) {
                    $relTag = new Todoreltags();
                    $relTag->id_to_do = $model->id;
                    $relTag->tag = $tag;
                    $relTag->save();
                }



                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Todomain model.
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
     * Finds the Todomain model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Todomain the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Todomain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    public function actionSendEventEmail($id)
    {
        $model = $this->findModel($id);

        $to = 'marco.cardinale@ilvbc.it';
        $subject = 'Meeting Invitation';
        $description = $model->descrizione;
        $location = 'Meeting Location';
        $startTime = $model->data_inizio;
        $endTime = $model->data_fine;

        $server = 'https://outlook.office365.com/EWS/Exchange.asmx';
        $username = 'marco.cardinale@ilvbc.it';
        $password = 'rkrwyckbpdsbbtdr'; //'Ilvbc2021!';

        // Crea una nuova istanza di API
        $api = API::withUsernameAndPassword($server, $username, $password);
        yii::error($api);

        $calendar = $api->getCalendar();

        $start = new \DateTime($model->data_inizio);
        $end = new \DateTime($model->data_fine);
        try {
            $createdItemIds = $calendar->createCalendarItems(array(
                'Subject' => 'Test',
                'Start' => $start->format('c'),
                'End' => $end->format('c')
            ));
        } catch (\garethp\ews\API\Exception\UnauthorizedException $e) {
            // Gestisci l'eccezione di autorizzazione qui
            echo 'Errore di autorizzazione: ' . $e->getMessage();
        } catch (\Exception $e) {
            // Gestisci altre eccezioni qui
            echo 'Errore generico: ' . $e->getMessage();
        }


        // Crea l'elemento del calendario
        $calendarItem = new CalendarItemType();
        $calendarItem->setSubject($subject)
            ->setBody(['BodyType' => 'Text', 'Body' => $description])
            ->setStart($startTime)
            ->setEnd($endTime)
            ->setLocation($location);

        // Aggiungi i destinatari richiesti
        $attendee = new SingleRecipientType();
        $attendee->setMailbox(new EmailAddressType(['EmailAddress' => $to]));
        $calendarItem->setRequiredAttendees([$attendee]);

        // Crea l'elemento del messaggio
        $createItem = new CreateItemType();
        $createItem->setItems([$calendarItem])
            ->setSendMeetingInvitations('SendToAllAndSaveCopy');

        try {
            // Invia l'invito
            $api->getClient()->CreateItem($createItem);

            Yii::$app->session->setFlash('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            Yii::error('Failed to send email: ' . $e->getMessage()); // Registra l'errore nel log
            Yii::$app->session->setFlash('error', 'Failed to send email. Check logs for details.'); // Messaggio generico di errore
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }














































    public function actionSendEventEmail2($id)
    {
        $model = $this->findModel($id);
        $mail
        = (new \yii\db\Query())
        ->select(['email', 'status'])
        ->from('user')
        ->where(['id' => $model->user])
        ->one();
        $to = $mail['email']; 
        //'marco.cardinale@ilvbc.it';
        $subject = 'Meeting Invitation';
        $description = $model->descrizione;
        $location = 'Meeting Location';
        $startTime = $model->data_inizio;
        $endTime = $model->data_fine;

        $icalContent = $this->generateIcs($subject, $description, $location, $startTime, $endTime);

        try {
            Yii::$app->mailer->compose()
                ->setTo($to)
                ->setFrom(['marco.cardinale@ilvbc.it' => 'EVENTATORE'])
                ->setSubject($subject)
                ->setTextBody('Please find the meeting invitation attached.')
                ->attachContent($icalContent, ['fileName' => 'invite.ics', 'contentType' => 'text/calendar'])
                ->send();

            Yii::$app->session->setFlash('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            Yii::error('Failed to send email: ' . $e->getMessage()); // Registra l'errore nel log
            yii::info($e);
            Yii::$app->session->setFlash('error', 'Failed to send email: ' . $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }


    private function generateIcs($subject, $description, $location, $startTime, $endTime)
    {
        $uid = uniqid();
        $startTime = gmdate('Ymd\THis\Z', strtotime($startTime));
        $endTime = gmdate('Ymd\THis\Z', strtotime($endTime));
        $now = gmdate('Ymd\THis\Z');

        /*     return <<<EOD
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN
CALSCALE:GREGORIAN
METHOD:PUBLISH
BEGIN:VEVENT
UID:$uid
DTSTAMP:$now
DTSTART;TZID=UTC:$startTime
DTEND;TZID=UTC:$endTime
SUMMARY:$subject
DESCRIPTION:$description
X-ALT-DESC;FMTTYPE=text/html:<html>$description</html>
URL;VALUE=URI:http://tracker/ics/
LOCATION:$location
ORGANIZER;CN=EVENTATORE:mailto:marco.cardinale@ilvbc.it
ATTENDEE;RSVP=TRUE;PARTSTAT=NEEDS-ACTION;CN=Recipient Name;EMAIL:marco.cardinale@ilvbc.it:mailto:marco.cardinale@ilvbc.it
STATUS:CONFIRMED
SEQUENCE:0
TRANSP:OPAQUE
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
END:VALARM
END:VEVENT
END:VCALENDAR
EOD;*/

        return <<<EOD
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Company//NONSGML v1.0//EN
CALSCALE:GREGORIAN
BEGIN:VEVENT
UID:$uid
DTSTAMP: $now
DTSTART:$startTime
DTEND:$endTime
SUMMARY:Meeting
DESCRIPTION: $description
LOCATION:Meeting location
END:VEVENT
END:VCALENDAR
EOD;
    }


    public function actionSendeventemailwithics($id)
    {
        $model = $this->findModel($id);

        $to = 'marco.cardinale@ilvbc.it';
        $subject = 'Meeting Invitation';
        $description = $model->descrizione;
        $location = 'Meeting Location';
        $startTime = $model->data_inizio;
        $endTime = $model->data_fine;

        $icalContent = $this->generateIcs($subject, $description, $location, $startTime, $endTime);

        try {
            $message = Yii::$app->mailer->compose()
                ->setTo($to)
                ->setFrom(['marco.cardinale@ilvbc.it' => 'EVENTATORE'])
                ->setSubject($subject);

            // Aggiungi la parte di testo semplice
            $message->setTextBody('Please find the meeting invitation attached.');

            // Ottieni il messaggio Swift
            $swiftMessage = $message->getSwiftMessage();

            // Crea una nuova parte per il contenuto ICS
            $icalPart = new \Swift_Attachment($icalContent, 'invite.ics', 'text/calendar; charset=UTF-8; method=REQUEST');

            // Imposta il corpo come multipart/alternative
            $swiftMessage->setBody('This is an invite in iCalendar format', 'text/plain');
            $swiftMessage->addPart($icalContent, 'text/calendar; charset=UTF-8; method=REQUEST');

            // Invia il messaggio
            Yii::$app->mailer->send($message);

            Yii::$app->session->setFlash('success', 'Email sent successfully!');
        } catch (\Exception $e) {
            Yii::error('Failed to send email: ' . $e->getMessage());
            Yii::$app->session->setFlash('error', 'Failed to send email: ' . $e->getMessage());
        }

        return $this->redirect(['view', 'id' => $model->id]);
    }



    private function generateIcs2($subject, $description, $location, $startTime, $endTime)
    {
        $uid = uniqid();
        $startTime = gmdate('Ymd\THis\Z', strtotime($startTime));
        $endTime = gmdate('Ymd\THis\Z', strtotime($endTime));
        $now = gmdate('Ymd\THis\Z');

        return <<<EOD
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Company//NONSGML v1.0//EN
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
UID:$uid
DTSTAMP:$now
DTSTART:$startTime
DTEND:$endTime
SUMMARY:$subject
DESCRIPTION:$description
LOCATION:$location
ORGANIZER;CN=EVENTATORE:mailto:marco.cardinale@ilvbc.it
ATTENDEE;RSVP=TRUE;PARTSTAT=NEEDS-ACTION;CN=Recipient Name;EMAIL=marco.cardinale@ilvbc.it:mailto:marco.cardinale@ilvbc.it
STATUS:CONFIRMED
SEQUENCE:0
TRANSP:OPAQUE
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR
EOD;
    }



    public function actionAddComment($id)
    {
        $model = new Todocommenti();
        $model->id_todo = $id;
        $model->user = Yii::$app->user->identity->username;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $comments = Todocommenti::find()->where(['id_todo' => $id])->all();
            return $this->renderPartial('_comments', ['comments' => $comments]);
        }

        Yii::$app->session->setFlash('error', 'Failed to add comment.');
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionAddreply($id_todo, $id_commento)
    {
        //return 'asdasdasda';

        $commentModel = new Todocommenti();
        if ($commentModel->load(Yii::$app->request->post())) {
            $commentModel->id_todo = $id_todo;
            $commentModel->id_commento = $id_commento;
            $commentModel->user = Yii::$app->user->identity->username;
            $commentModel->data = date('Y-m-d H:i:s'); // Assicurati che 'data' venga impostata correttamente
            if ($commentModel->save()) {
                // Successo: Ritorna la sezione dei commenti aggiornata
                return $this->renderPartial('_comments', [
                    'comments' =>  Todocommenti::find()->where(['id_todo' => $id_todo])->orderBy(['data' => SORT_DESC])->all(), // Carica di nuovo tutti i commenti
                    'newCommentModel' => new TodoCommenti(), // Modello per il nuovo commento
                ]);
            } else {
                // Gestisci l'errore nel salvataggio
                Yii::$app->session->setFlash('error', 'Error adding reply.');
            }
        }

        // Se qualcosa va storto, ritorna un messaggio di errore (puoi personalizzare in base alla tua logica)
        return 'Errore durante il salvataggio del commento.';
    }


    public function actionCreateTag($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $out = [];
        $tags = Todotag::find()->where(['like', 'Tag', $q])->all();
        foreach ($tags as $tag) {
            $out[] = ['id' => $tag->Tag, 'text' => $tag->Tag];
        }

        if (empty($out)) {
            $newTag = new Todotag();
            $newTag->Tag = $q; // Non impostare manualmente 'id', sarà generato automaticamente
            if ($newTag->save()) {
                $out[] = ['id' => $newTag->Tag, 'text' => $newTag->Tag];
            }
        }

        return ['results' => $out];
    }

    public function actionSearchTag($q)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $tags = Todotag::find()
            ->select(['Tag as id', 'Tag as text'])
            ->where(['like', 'Tag', $q])
            ->asArray()
            ->all();

        return ['results' => $tags];
    }

}