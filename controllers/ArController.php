<?php

namespace app\controllers;
use app\models\AR;
use yii\db\Query;
class ArController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

        
    
     
    public function actionList($q = null, $id = null) {
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $out = ['results' => ['id' => '', 'text' => '']];
    if (!is_null($q)) {
       // $query = new Query;
       // $query->select('Cd_Ar as id,[Cd_Ar]+\'-\'+[Descrizione] as text')
            //select (['ID', 'CONCAT(ID,\' \',Descrizione) AS text'])
        //        ->from('AR')
        //    ->where(['like', 'descrizione', $q])
        //    ->OrWhere(['like','Cd_Ar',$q])
        //    ->limit(20);
        //$command = $query->createCommand();
        //$data = $command->queryAll();
        
        $artdett = AR::find()
    ->select(['[Cd_AR] as id', '[Cd_Ar]+\'-\'+[Descrizione] as text'])
    ->where(['like', 'descrizione', $q])
    ->orWhere(['like', 'Cd_Ar', $q])
    ->asArray()
    ->limit(20)
    ->all(); 
        
        $out['results'] = array_values($artdett);
    }
    elseif ($id > 0) {
        $out['results'] = ['id' => $id, 'text' => 
        ar::find($id)->Descrizione];
    }
    return $out;
}




public function actionGetardet($ar){
$artdett=AR::find()
->select (['Descrizione',
'Cd_ARMisura',
'isnull(Cd_Aliquota_V,\'22\') as Cd_Aliquota_V','Cd_ARGruppo123','Cd_CGConto_VI',
'Cd_AR'])
->where(['Cd_AR'=>$ar])
->one();
\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
//$out = ['result'=>['']];
// $out['results'] = array_values($artdett);
return $artdett;

} 

}
