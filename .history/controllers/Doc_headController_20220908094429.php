<?php

namespace app\controllers;

use Yii;
use app\models\Doc_head;
use app\models\Doc_headSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Elemail;
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
                'class' => VerbFilter::className(),
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

        $searchModel = new Doc_headSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
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
    public function actionCreate()
    {
        $this->getuser();

        $model = new Doc_head();

        if ($model->load(Yii::$app->request->post())  ) {
            $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);
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

        if ($model->load(Yii::$app->request->post())  ) {

$t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);
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
        $model->setAttribute('confermato',1);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

        $model->save();

        $query2 = yii::$app->db2
    ->createCommand()
    ->update('DOTES', ['xconfermato' => 1, 'xannullato' => 0,'cd_cf'=>$model->cd_cli,'xdashcli'=>$model->altcli]
        , ['id_dotes' => $model->xid_testa]);
$query2->execute();

$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['email'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
    //->AsArray();
}

$subject = 'Documento Confermato';
$body = 'Documento Confermato  ' . $model->numdoc . 'del ' . $model->data
. ' per il cliente ' . $model->cd_cli . PHP_EOL ;
$message = Yii::$app->mailer->compose()
//->setTo(Yii::$app->params['senderEmail'])
    ->setto($ris['email'])
    ->setFrom(yii::$app->params['supportEmail'])
    ->setReplyTo([yii::$app->params['supportEmail']])
    ->setSubject($subject)
    ->setTextBody($body);
$message->send();

$message2 = Yii::$app->mailer->compose()
    ->setTo(yii::$app->params['adminEmail'])
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo($ris['email'])
    ->setSubject($subject)
    ->setTextBody($body.'da parte dell\'utente '.$ris['email'] );
//  ->attach($this->files);
$message2->send();





$elemail= new Elemail();
$elemail->setAttribute('nome', $ris['email']);
$elemail->setAttribute('email', $ris['email']);
$elemail->setAttribute('Soggetto',$subject);
$elemail->setAttribute('Corpo',$body);
$elemail->setAttribute('allegati',  $model->id);
$elemail->save(false);
















return $this->redirect(['view', 'id' => $model->id]);
    }


    public function actionRifiuta($id,$rifiutato_nota=null)
    {
        $this->getuser();
 
        $model = $this->findModel($id);
        $model->setAttribute('rifiutato',1);
        $model->setAttribute('rifiutato_nota',$rifiutato_nota);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

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
    'op' => $model->className() . '-->' . $this->action->id]);

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



public function actionNrifiuta($id=null ){
    $this->getuser();


    if (($model = Doc_head::findOne($id)) !== null) {
 if ($model->load(Yii::$app->request->post())  ){

    
$model->setAttribute('rifiutato', 1);
$model->setAttribute('rifiutato_nota', $model->rifiutato_nota);
if (strlen($model->rifiutato_nota)==0){
return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);



}
$t = Yii::$app->runAction('log/set', ['data' => $model,
    'op' => $model->className() . '-->' . $this->action->id]);

$model->save();

$query2 = yii::$app->db2
    ->createCommand()
    ->update('DOTES',['NotePiede'=>$model->rifiutato_nota,'xannullato'=>1]
    ,['id_dotes'=>$model->xid_testa]);
$query2->execute();


$query3 = yii::$app->db2
    ->createCommand()
    ->update('DOrig', ['qtaevadibile' => 0]
        , ['id_dotes' => $model->xid_testa]);
$query3->execute();


$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['email'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
    //->AsArray();
}
/*
$message = Yii::$app->mailer->compose()
    ->setTo(yii::$app->params['supportEmail'])
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo([$ris['email']])
    ->setSubject('Documento Rifiutato')
    ->setTextBody('Documento Rifiutato  ' . $model->numdoc . 'del ' . $model->data
        . ' per il cliente ' . $model->cd_cli.PHP_EOL.'Motivazione: '.$model->rifiutato_nota);
//  ->attach($this->files);
$message->send();
*/
$subject='Documento Rifiutato';
$body='Documento Rifiutato  ' . $model->numdoc . 'del ' . $model->data
        . ' per il cliente ' . $model->cd_cli.PHP_EOL.'Motivazione: '.$model->rifiutato_nota;
$message = Yii::$app->mailer->compose()
//->setTo(Yii::$app->params['senderEmail'])
    ->setto($ris['email'])
    ->setFrom(yii::$app->params['supportEmail'])
    ->setReplyTo([yii::$app->params['supportEmail']])
    ->setSubject($subject)
    ->setTextBody($body);
$message->send();

$message2 = Yii::$app->mailer->compose()
    ->setTo(yii::$app->params['adminEmail'])
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo($ris['email'])
    ->setSubject($subject)
    ->setTextBody($body.'da parte dell\'utente '.$ris['email'] );
//  ->attach($this->files);
$message2->send();




return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);



 }

  //  return $model;
}

return $this->renderAjax('noterif',['id'=>$id,'model'=>$model]);

}


public function actionSede($id){

$this->getuser();
if (($model = Doc_head::findOne($id)) !== null) {
    if ($model->load(Yii::$app->request->post())) {
//return var_dump($model);


$model->setAttribute('altcli', $model->altcli);
$alcli=$model->altcli;
$cf=$model->cd_cli;
//$model->setAttribute('cd_cli', $model->altcli);

//return var_dump($alcli.' '.$cf);
       yii::error($model->altcli);

//rimosso 11/08/22
//$model->setAttribute('altcli', $cf);
//$model->setAttribute('cd_cli', $alcli);
    

       // $model->setAttribute('rifiutato_nota', $model->rifiutato_nota);
        $t = Yii::$app->runAction('log/set', ['data' => $model,
            'op' => $model->className() . '-->' . $this->action->id]);

        $model->save();
        return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);

    }

    //  return $model;
}

return $this->renderAjax('sede', ['id' => $id, 'model' => $model]);


}


    

public function getuser(){
$usrid = Yii::$app->user->Id;

if ($usrid !== null) {
    $ris = (new \yii\db\Query())
        ->select(['level', 'cd_cli'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();
//->AsArray();

}
//var_dump($ris);
if (Yii::$app->user->isGuest || $ris['level'] == null) {

$messaggio =
    "<h1>Attenzione</h1>\n\n"
    . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n";

exit($messaggio);

}


}
}
