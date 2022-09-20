<?php

namespace app\controllers;

use Yii;


use app\models\Allfiles;
use app\models\AllfilesSearch;
use yii\web\UploadedFile;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\httpclient\Client;

/**
 * AllfilesController implements the CRUD actions for Allfiles model.
 */
class AllfilesController extends Controller
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
     * Lists all Allfiles models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser();

        $searchModel = new AllfilesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Allfiles model.
     * @param integer $id
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
     * Creates a new Allfiles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->getuser();

        $model = new Allfiles();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
   // ajupd


    public function actionAjupd()
    {
        $this->getuser();

        $model = new Allfiles();
      //  mb_internal_encoding( 'UTF-8' );

        if ($model->load(Yii::$app->request->post())){
            $x=$model->f_content;
            $tmpfile=UploadedFile::getInstance($model, 'f_content');
           $h=//'0x'.bin2hex((binary)
           //pack("0x*",
           
           (binary)(file_get_contents($tmpfile->tempName));
           var_dump($model); 
           $model->nomefile=basename($tmpfile);
           $model->origine='S';
           $model->estensione=pathinfo($tmpfile ,PATHINFO_EXTENSION);
            $model->setAttribute('f_content','');
           $model->save(false); 
           $id=$model->getPrimaryKey();
           $connection = Yii::$app->getDb();
           $command = $connection->createCommand("
           update all_files set f_content=convert(
            VARBINARY(max) ,  
            :contenuto ,1) where id=:id
           
           ",[':id'=>$id,':contenuto'=>'0x'.bin2hex($h)]);
            $command->execute();
           //\Yii::$app->response->format = Response::FORMAT_JSON;

$query2 = yii::$app->db2
    ->createCommand("insert into DmsDocument(
    content,
    entityTable,
    descrizione,
    filename,
    EntityId,
DocumentDate,
LinkedToFS,
filesize,
FilePath,
note,
ComputerName,
id_dmsclass1,
id_dmsclass2,
dmsclass3,EntityDescription,Cd_DmsType
)
values
( select f_content as content,
entita as entityTable,
'caricato da portale web ' + nomefile as descrizione,
nomefile as [filename],
id_padre as entityid,
getdate() as documentdate,
':filesize' as filesize,
'\\web\upload\' as FilePath,
'caricato da portale web ' + nomefile as note,
'Portal' as ComputerName,
1 as id_dmsclass1 ,
5 as id_dmsclass2 ,
cd_doc as id_dmsclass3,
cd_doc+ numdoc +' del ' +convert(varchar(10),data),
'00' as Cd_DmsType
from all_files
left join doc_head on doc_head.xid_testa=id_padre
 where id_padre=:idpadre          )

"

                )
    
        ->bindValues([':allfilesid' => $model->id,
        ':idpadre' => $model->id_padre,
        ':filesize' => $tmpfile->size]);
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

$message = Yii::$app->mailer->compose()
    ->setTo(yii::$app->params['supportEmail'])
    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
    ->setReplyTo([$ris['email']])
    ->setSubject('File  Caricato')
    ->setTextBody('File Caricato  ' . $model->numdoc . 'del ' . $model->data
        . ' per il cliente ' . $model->cd_cli);
//  ->attach($this->files);
$message->send();





/*

$client = new Client();
$response = $client->createRequest()
//->setMethod('POST')
->setUrl('http://vivenda.locl:8080/web/index.php?r=rest/arcapostfile')
->setData([
  'content' =>//'pino',
  // '0x'.bin2hex($h),
  //$model['f_content'],
    $tmpfile,
 'id' => 
 $model['id_padre'],
 'fn'=>//'asd',
 $model['nomefile'],
 'hash'=>'$2y$13$6dpv6XRT1tRQu9RZFfnKvOYIoiOiS5.PHzP5qsQqp1s83LBu90d.6']
 )
->send();
if ($response->isOk) {
die (var_dump($response));
//    $newUserId = $response->data['id'];
}else{
    die(var_dump($response->content));
}
*/



















           return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Allfiles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->getuser();

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Allfiles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->getuser();

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Allfiles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Allfiles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $this->getuser();

        if (($model = Allfiles::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public  function  actionGenfile($id,$filename=null){

        $this->getuser();
                $tmpfile= AllFiles::find()->where(['id'=>$id])->asArray()->one() ;
 //var_dump($tmpfile);
                try {
                $path=Yii::getAlias('@webroot').'/uploads/';
                $file=str_replace(' ', '_',$tmpfile['nomefile']);
                 $file2 = $path . $tmpfile['id'].'_'.$file;
                 //yii::warning
        //         var_dump($tmpfile['nome_file']);
                
                    if (!is_null($tmpfile['nomefile'])) {
                  
               
                      $tmf=fopen($file2, 'w');
                      fwrite($tmf, (
                      $tmpfile['f_content'])) ;
                      fclose($tmf);
 
                  
                            $tmpf=
                 Yii::$app->response->SendFile(
                     $file2,
                     $file,
                     $options = ['inline'=>false]
                    // file_get_contents($file2, FILE_USE_INCLUDE_PATH)
                    // 'application/pdf'
                 ); 
                }
                } catch (Exception $e) {
                    echo 'Caught exception: ',  $e->getMessage(), "\n";
                }
                 ob_clean();
                 unlink($file2);
         // return $tmpf;//    file_get_contents( $file2 );
//return 'stocazzo';


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
