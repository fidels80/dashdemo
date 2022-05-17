<?php
namespace app\controllers;

 


use yii\helpers\Url;
use app\models\ContactForm;
use app\models\LoginForm;
use app\models\PasswordResetRequestForm;
use app\models\ResetPasswordForm;
use app\models\SignupForm;
use app\models\UploadImageForm;
use app\models\User;
use app\models\Xmenu;
use app\models\Xsubmenu;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\UploadedFile;
use kartik\grid\GridView;
//use app\models\form\FileForm;

/**
 * AgendaController implements the CRUD actions for Agenda model.
 */
class ReportsController extends Controller
{





public function actionIndex()
    {
        $this->getuser();

     //   $searchModel = new FilesSearch();
      //  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index');//, [
           // 'searchModel' => $searchModel,
           // 'dataProvider' => $dataProvider,
        //]);
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







public function actionZreport()
    {
        $report = new \app\views\reports\myreport;
        $report->run();
        return $this->render('report',array(
            "report"=>$report
        ));
        
    }


public function actionChart()
    {
 
        return $this->render('chart');
        
    }




}

?>