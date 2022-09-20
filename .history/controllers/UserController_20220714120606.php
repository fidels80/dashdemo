<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\anacli;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use yii\web\UploadedFile;


/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) ) {
            // var_dump(implode($model->moduli));
            $model->file = UploadedFile::getInstance($model, 'file');
            
            if (is_array($model->moduli)) {
                $model->setAttribute("moduli", serialize($model->moduli));
                //implode('|', $model->moduli));
            }
            if (is_array($model->reports)) {
                $model->setAttribute("reports", serialize($model->reports));
                //implode('|', $model->reports));
            }
            
        //    die(print_r($_POST['User']['password']));


            if(isset($_POST['User']['password']) ){

                if($_POST['User']['password']<> null || trim($_POST['User']['password']) <> ''){
            $model->setAttribute('password_hash',
            Yii::$app->security->generatePasswordHash($_POST['User']['password']));
            //        die(print_r(Yii::$app->security->generatePasswordHash($model->password)));
                }


            }
            $usrid = Yii::$app->user->Id;
            $ris  = user::find()
    ->select(['level', 'cd_cli','id'])
    ->where(['id' => $usrid])
    ->asArray()
    ->one();
//die('prima di salvare');
            $model->save();
if (!empty($model->file)) {
    $model->upload();
}
            if ($ris['level'] ==100) {
                return $this->redirect(['index']);
            } else {
                return $this->redirect(['view', 'id'=> $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function getuser()
    {
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
    public function actionRp()
    {
        $mmodel = new PasswordResetRequestForm();
        if ($mmodel->load(Yii::$app->request->post())) {
            //return $model;
            /*
                Yii::$app->mailer->compose()
                ->setFrom('somebody@domain.com')
                ->setTo('myemail@yourserver.com')
                ->setSubject('Email sent from Yii2-Swiftmailer')
                ->send();
            */
if ($mmodel->sendEmail()) {
    // $mmodel->sendEmail();
    //print_R(Yii::$app->mailer);
    Yii::$app->session->setFlash('inviato');
}ELSE{
     Yii::$app->session->setFlash('errore');

}
       //   return   $this->refresh();

            return $this->goHome();


        /*
}else{

          return "ERRORE DI INVIO EMAIL";
}
*/
    /*&& $model->validate()) {

    if ($model->sendEmail()) {
        Yii::$app->session->setFlash('success',
            'Check your email for further instructions.');
die();
        return  $model;

    } else {
        Yii::$app->session->setFlash('error',
            'Sorry, we are unable to reset password for email provided.');
    }*/
        } else {
            return $this->render('rp', ['model'=>$mmodel]);
        }
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
  /*  public function actionResetp($token)
    {
        try {
            //  $model = new ResetPasswordForm($token);
   if ($token !== null) {
       $ris = user::find()
       // ->select(['level', 'cd_cli'])
        
        ->where(['password_reset_token' => $token])
        ->one();
       $model = $ris;
   }
          //  yii::warning($model);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post())
        && $model->validate()
       && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
                  }


            return $this->render('preset', [
           'model' => $model,
        ]);
        }*/

 public function actionResetp($token){
$model = new ResetPasswordForm($token);
if ($model->load(Yii::$app->request->post())
    && $model->validate()
    && $model->resetPassword()) {
    Yii::$app->session->setFlash('success', 'New password was saved.');

    return $this->goHome();
}

    return $this->renderpartial('preset', [
    'model' => $model,
]);

 }


    }

