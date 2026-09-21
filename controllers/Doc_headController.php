<?php
namespace app\controllers;

use app\models\Doc_head;
use app\models\Doc_headSearch;
use app\models\Doc_rows;
use app\models\Elemail;
use app\models\Model;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Doc_headController implements the CRUD actions for Doc_head model.
 */
class Doc_headController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Doc_head models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();
        $searchModel  = new Doc_headSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Doc_head model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->getuser();
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Creates a new Doc_head model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function odl_actionCreate()
    {
        $this->getuser();
        $model = new Doc_head();
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->runAction('log/set', ['data' => $model,
                'op'                                         => $model->className() . '-->' . $this->action->id]);
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Doc_head model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getuser();
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            $t = Yii::$app->runAction('log/set', ['data' => $model,
                'op'                                         => $model->className() . '-->' . $this->action->id]);
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionConferma($id)
    {
        $this->getuser();
        $model = $this->findModel($id);
        $model->setAttribute('confermato', 1);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
            'op'                                         => $model->className() . '-->' . $this->action->id]);
        $model->save();
        $query2 = yii::$app->db2
            ->createCommand()
            ->update('DOTES', ['xconfermato' => 1, 'xannullato' => 0, 'cd_cf' => $model->cd_cli, 'xdashcli' => $model->altcli]
                , ['id_dotes' => $model->xid_testa]);
        $query2->execute();
        $usrid = Yii::$app->user->Id;
        if (null !== $usrid) {
            $ris = (new \yii\db\Query ())
                ->select(['email', 'cd_cli'])
                ->from('user')
                ->where(['id' => $usrid])
                ->one();
        }
        $cc = (new \yii\db\Query ())
            ->select(['ccemail'])
            ->from('ana_cli')
            ->where(['cd_cli' => $ris['cd_cli']])
            ->one();
        $rcc     = isset($cc['ccemail']) ? $cc['ccemail'] : '';
        $subject = 'Documento Confermato';
        $body    = 'Documento Confermato  ' . $model->numdoc . 'del ' . $model->data
        . ' per il cliente ' . $model->cd_cli . PHP_EOL;
        $message = Yii::$app->mailer->compose()
            ->setto($ris['email'])
            ->setFrom(yii::$app->params['supportEmail'])
            ->setReplyTo([yii::$app->params['supportEmail']])
            ->setSubject($subject)
            ->setTextBody($body);
        $rcc = isset($cc['ccemail']) ? $message->setCC(explode(';',$cc['ccemail'])) : '';
        $message->send();
        $message2 = Yii::$app->mailer->compose()
            ->setTo(yii::$app->params['adminEmail'])
            ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
            ->setReplyTo($ris['email'])
            ->setSubject($subject)
            ->setTextBody($body . 'da parte dell\'utente ' . $ris['email']);
        $rcc = isset($cc['ccemail']) ? $message->setCC(explode(';',$cc['ccemail'])) : '';
        $message2->send();
        $elemail = new Elemail();
        $elemail->setAttribute('nome', $ris['email']);
        $elemail->setAttribute('email', $ris['email']);
        $elemail->setAttribute('Soggetto', $subject);
        $elemail->setAttribute('Corpo', $body);
        $elemail->setAttribute('allegati', $model->id);
        $elemail->save(false);
        return $this->redirect(['view', 'id' => $model->id]);
    }
    public function actionRifiuta($id, $rifiutato_nota = null)
    {
        $this->getuser();
        $model = $this->findModel($id);
        $model->setAttribute('rifiutato', 1);
        $model->setAttribute('rifiutato_nota', $rifiutato_nota);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
            'op'                                         => $model->className() . '-->' . $this->action->id]);
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);
    }
    /**
     * Deletes an existing Doc_head model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getuser();
        $t = Yii::$app->runAction('log/set', ['data' => $model,
            'op'                                         => $model->className() . '-->' . $this->action->id]);

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    /**
     * Finds the Doc_head model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Doc_head the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $this->getuser();
        if (($model = Doc_head::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionNrifiuta($id = null)
    {
        $this->getuser();
        if (($model = Doc_head::findOne($id)) !== null) {
            if ($model->load(Yii::$app->request->post())) {
                $model->setAttribute('rifiutato', 1);
                $model->setAttribute('rifiutato_nota', $model->rifiutato_nota);
                if (strlen($model->rifiutato_nota) == 0) {
                    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
                }
                $t = Yii::$app->runAction('log/set', ['data' => $model,
                    'op'                                         => $model->className() . '-->' . $this->action->id]);
                $model->save();
                $query2 = yii::$app->db2
                    ->createCommand()
                    ->update('DOTES', ['NotePiede' => $model->rifiutato_nota, 'xannullato' => 1]
                        , ['id_dotes' => $model->xid_testa]);
                $query2->execute();
                $query3 = yii::$app->db2
                    ->createCommand()
                    ->update('DOrig', ['qtaevadibile' => 0]
                        , ['id_dotes' => $model->xid_testa]);
                $query3->execute();
                $usrid = Yii::$app->user->Id;
                if (null !== $usrid) {
                    $ris = (new \yii\db\Query ())
                        ->select(['email', 'cd_cli'])
                        ->from('user')
                        ->where(['id' => $usrid])
                        ->one();
                    //->AsArray();
                }
                $cc = (new \yii\db\Query ())
                    ->select(['ccemail'])
                    ->from('ana_cli')
                    ->where(['cd_cli' => $ris['cd_cli']])
                    ->one();
                $rcc     = isset($cc['ccemail']) ? $cc['ccemail'] : '';
                $subject = 'Documento Rifiutato';
                $body    = 'Documento Rifiutato  ' . $model->numdoc . 'del ' . $model->data
                . ' per il cliente ' . $model->cd_cli . PHP_EOL . 'Motivazione: ' . $model->rifiutato_nota;
                $message = Yii::$app->mailer->compose()
                    ->setto($ris['email'])
                    ->setFrom(yii::$app->params['supportEmail'])
                    ->setReplyTo([yii::$app->params['supportEmail']])
                    ->setSubject($subject)
                    ->setTextBody($body);
                $rcc = isset($cc['ccemail']) ? $message->setCC(explode(';',$cc['ccemail'])) : '';
                $message->send();
                $message2 = Yii::$app->mailer->compose()
                    ->setTo(yii::$app->params['adminEmail'])
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setReplyTo($ris['email'])
                    ->setSubject($subject)
                    ->setTextBody($body . 'da parte dell\'utente ' . $ris['email']);
                $message2->send();
                $elemail = new Elemail();
                $elemail->setAttribute('nome', $ris['email']);
                $elemail->setAttribute('email', $ris['email']);
                $elemail->setAttribute('Soggetto', $subject);
                $elemail->setAttribute('Corpo', $body);
                $elemail->setAttribute('allegati', $model->id);
                $elemail->save(false);
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
        }
        return $this->renderAjax('noterif', ['id' => $id, 'model' => $model]);
    }
    public function actionSede($id)
    {
        $this->getuser();
        if (($model = Doc_head::findOne($id)) !== null) {
            if ($model->load(Yii::$app->request->post())) {
                $model->setAttribute('altcli', $model->altcli);
                $alcli = $model->altcli;
                $cf    = $model->cd_cli;
                yii::error($model->altcli);
                $t = Yii::$app->runAction('log/set', ['data' => $model,
                    'op'                                         => $model->className() . '-->' . $this->action->id]);
                $model->save();
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
            }
        }
        return $this->renderAjax('sede', ['id' => $id, 'model' => $model]);
    }
    public function actionNannota($id)
    {
        $this->getuser();
        if (($model = Doc_head::findOne($id)) !== null) {
            if ($model->load(Yii::$app->request->post())) {
                $model->setAttribute('altcli', $model->altcli);
                $alcli = $model->altcli;
                $cf    = $model->cd_cli;
                yii::error($model->altcli);
                $t = Yii::$app->runAction('log/set', ['data' => $model,
                    'op'                                         => $model->className() . '-->' . $this->action->id]);
                $xid    = $model->xid_testa;
                $xnota  = $model->xnota;
                $query2 = yii::$app->db2
                    ->createCommand("
update dotes set xnotedash=:znota where id_dotes=:xid "
                    )->bindValues([':znota' => $xnota, ':xid' => $xid]);
                $query2->execute();
                $chk = $model->dest ?? '0';
                if (0 != $chk) {
                    $ints = yii::$app->db2
                        ->createCommand('select top 1 dotes.cd_cf,dotes.Cd_CF,dotes.Cd_CFSede,dotes.Cd_CFDest,
                        dotes.numerodoc,1,CFSede.Descrizione,CFSede.EMail as sdMail,
                        CFContatto.email as cfcemail,cfcontatto.emailpec
                        from dotes
                        left join CFSede on CFSede.Cd_Cf=dotes.Cd_CF and CFSede.Cd_CFSede=dotes.Cd_CFSede
                        left join CFContatto on CFContatto.Cd_CF=dotes.Cd_CF and CFContatto.Cd_CFDest=dotes.Cd_CFDest
                        where dotes.id_dotes=:id_Dotes
                        ')->bindValues([':id_Dotes' => $model->xid_testa]);
                    $intesta = $ints->queryAll();
                }
                $usrid = Yii::$app->user->Id;
                if (null !== $usrid) {
                    $ris = (new \yii\db\Query ())
                        ->select(['cd_cli'])
                        ->from('user')
                        ->where(['id' => $usrid])
                        ->one();
                    //->AsArray();
                }
                if (isset($intesta)) {
                    $cc = (new \yii\db\Query ())
                        ->select(['ccemail'])
                        ->from('ana_cli')
                        ->where(['cd_cli' => $ris['cd_cli']])
                        ->one();
                    $rcc     = isset($cc['ccemail']) ? $cc['ccemail'] : '';
                    $subject = 'Effettuata Modifica/aggiunta alla nota del docummento '
                    . $model->numdoc . 'del ' . $model->data;
                    $body    = 'Testo nota :' . PHP_EOL . $xnota;
                    $message = Yii::$app->mailer->compose()
                        ->setto($intesta[0]['cfcemail'] ?? $intesta[0]['sdMail'])
                        ->setFrom(yii::$app->params['supportEmail'])
                        ->setReplyTo([yii::$app->params['supportEmail']])
                        ->setSubject($subject)
                        ->setTextBody($body);
                    $rcc = isset($cc['ccemail']) ? $message->setCC(explode(';',$cc['ccemail'])) : '';
                    $message->send();
                    $message2 = Yii::$app->mailer->compose()
                        ->setTo(yii::$app->params['adminEmail'])
                        ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                        ->setReplyTo($intesta[0]['cfcemail'] ?? $intesta[0]['sdMail'])
                        ->setSubject($subject)
                        ->setTextBody($body . PHP_EOL . 'da parte dell\'utente '
                            . $intesta[0]['cfcemail'] ?? $intesta[0]['sdMail']);
                    $message2->send();
                }

                $model->save();
                return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

            }
        }
        return $this->renderAjax('nota', ['id' => $id, 'model' => $model]);
    }
    public function getuser()
    {
        $usrid = Yii::$app->user->Id;
        if (null !== $usrid) {
            $ris = (new \yii\db\Query ())
                ->select(['level', 'cd_cli', 'moduli'])
                ->from('user')
                ->where(['id' => $usrid])
                ->one();
            $nmod = (str_replace('app\controllers', '', str_replace('Controller', '', __CLASS__)));
            $nmod = (str_replace('\\', '', $nmod));
            $nmod = strtoupper($nmod);
            $mn   = (new \yii\db\Query ())
                ->select(['voce', 'url', 'Nmodulo'])
                ->from('xmenu')
                ->where(['upper(Nmodulo)' => strtoupper($nmod)])
                ->one();
        }
        $arrmod = unserialize($ris['moduli'] ?? '');
        $go     = 0;
        if (($ris['level'] ?? 0) != 100) {
            if (is_array($arrmod)) {
                foreach ($arrmod as $value) {
                    if (strtoupper($value) == strtoupper(($mn['voce'] ?? 'default value'))) {
                        $go = 1;
                    }
                    if ('DOC_HEAD' == $nmod || strtoupper($value) == 'ELENCO') {
                        $go = 1;
                    }
                }
            }
        } else {
            $go = 1;
        }
        if (Yii::$app->user->isGuest || null == $ris['level']) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";
            exit($messaggio);
        }
        if (0 == $go) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";
            exit($messaggio);
        }
    }

    public function actionCreate()
    {
        $this->getuser();
        $modelHead = new Doc_head();
        $modelsRow = [new Doc_Rows];
        if ($modelHead->load(Yii::$app->request->post())) {
$modelsRow1 = Yii::$app->request->post('Doc_head')['docRows']; // Ottieni i dati del campo docRows dal post
//$modelsRow = Model::createMultiple(Doc_Rows::classname(), $modelsRow1); // Inizializza l'array di modelli figlio e carica i dati
$modelsRow=$modelsRow1;
         //   return var_dump($modelsRow);
            $mdoc = yii::$app->db
                ->createCommand('select max(numdoc) as m from doc_head
where cd_doc=:PRV
                        ')->bindValues([':PRV' => $modelHead->cd_doc]);
            $mdoc_r            = $mdoc->queryAll();
            $modelHead->numdoc = strval($mdoc_r[0]['m'] + 1);
            $modelHead->validate();
           $flag2=false;
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $i=1;
                    if ($flag = $modelHead->save()) {
                        foreach ($modelsRow as $modelsRow_one) {
                          
                            $modelsRow_= new Doc_Rows();

                            $modelsRow_->doc_head_id = $modelHead->id;
                            $modelsRow_->cd_doc      = $modelHead->cd_doc;
                            $modelsRow_->data        = $modelHead->data;
                            $modelsRow_->cd_cli      = $modelHead->cd_cli;
                            $modelsRow_->numdoc      = $modelHead->numdoc;
                            $modelsRow_->iva         = rtrim(ltrim($modelsRow_one['iva']));
                            $modelsRow_->cd_art = $modelsRow_one['cd_art'];
                            $modelsRow_->descrizione = $modelsRow_one['descrizione'];
                            $modelsRow_->um = $modelsRow_one['um'];
                            $modelsRow_->qta = $modelsRow_one['qta'];
                            $modelsRow_->prz_unit = $modelsRow_one['prezzo'];
                            $modelsRow_->prezzo = $modelsRow_one['prezzo'];

                            $modelsRow_->sconto = $modelsRow_one['sconto'];
                            $modelsRow_->totale = $modelsRow_one['totale'];
                            $modelsRow_->nriga = $i;
                            $modelsRow_->note = $modelsRow_one['note'];
                            $modelsRow_->prz_tot=$modelsRow_one['totale'];



                            $modelsRow_->validate();
                           // return  $modelsRow_one->save();

                            if (!($flag2 = $modelsRow_->save())) {
                                $transaction->rollBack();
                                return $modelsRow_->error;

                                //die($modelsRow_one->errors);
                                 break;

                            }
                            $i=$i+1;
                        }
                    }
                    if ($flag && $flag2) {
                        $transaction->commit();
                        return $this->redirect(['index']);
                    }
                    else {
                        return var_dump($modelsRow_One->error);

                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
        
        }
        return $this->render('create', [
            'model' => $modelHead,
            'items' => $modelsRow,
        ]);
    }
    public function actionDupd($id)
    {
        $model  = $this->findModel($id);
        $items2 = Doc_rows::find()->where(['doc_head_id' => $id])->orderBy(['nriga' => SORT_ASC]);

if ($model->is_locked && $model->locked_by != Yii::$app->user->identity->username) {
    // throw new \yii\web\ForbiddenHttpException('Il record è attualmente bloccato da un altro utente.');
    return $this->redirect(['view', 'id' => $model->id]);

}


        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            // return var_dump($_POST['Doc_head'] ['docRows']);
            try {
                // Rimozione dei record eliminati dall'utente
                $oldIds = ArrayHelper::getColumn($model->docRows, 'id');
                $newIds = array_filter($_POST['Doc_head']['docRows'] ?? [],
                    function ($item) {
                        return isset($item['id']);
                    });
                $toDeleteIds = array_diff($oldIds, $newIds);
                if (!empty($toDeleteIds)) {
                    Doc_rows::deleteAll(['id' => $toDeleteIds]);
                }
                $i = 1;
                // Aggiornamento o creazione dei record modificati o nuovi
                foreach ($_POST['Doc_head']['docRows'] as $item) {
                    if (!empty($item['id'])) {
                        $docRow             = Doc_rows::findOne($item['id']);
                        $docRow->attributes = $item;
                        $docRow->setAttribute('nriga', $i);
                        $docRow->save();
                    } else {
                        $docRow             = new Doc_rows();
                        $docRow->attributes = $item;
                        $docRow->setAttribute('nriga', $i);
                        $docRow->doc_head_id = $model->id;
                        $docRow->save();
                    }
                    $i = $i + 1;
                }
$model->is_locked = false;
$model->locked_by = null;

                $model->save();
                $transaction->commit();
                return $this->redirect(['view', 'id' => $model->id]);

            } catch (Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        }
$model->is_locked = true;
$model->setAttribute('is_locked', true);
$model->setAttribute('locked_by', Yii::$app->user->identity->username);
if ($model->save() == false) {
    return var_dump($errors = $model->getErrors());
}

        return $this->render('create', [
            'model'  => $model,
            'items2' => (empty($items2)) ? [new Doc_rows()] : $items2,
        ]);

    }
    public function actionDdelete($id)
    {
        $modelHead  = $this->findModelHead($id);
        $modelsRows = $modelHead->docRows;

        foreach ($modelsRows as $modelRow) {
            $modelRow->delete();
        }
        $modelHead->delete();
        Yii::$app->session->setFlash('success', 'Document deleted successfully.');
        return $this->redirect(['index']);
    }

    public function actionGetrows($id)
    {
        $artdett = Doc_rows::find()
            ->where(['doc_head_id' => $id])
            ->all();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//$out = ['result'=>['']];
// $out['results'] = array_values($artdett);

        return $artdett;

    }
    public function afterFind()
    {
        parent::afterFind();
        $this->schedule = \yii\helpers\Json::decode($this->schedule);
    }
}
/*
$cc = (new \yii\db\Query())
->select(['ccemail'])
->from('ana_cli')
->where(['cd_cli' => $ris['cd_cli']])
->one();

$rcc = isset($cc['ccemail']) ? $cc['ccemail'] : '';
$rcc = isset($cc['ccemail']) ? $message->setCC(explode(';',$cc['ccemail'])) : '';

 */
