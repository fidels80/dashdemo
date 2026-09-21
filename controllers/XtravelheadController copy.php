<?php

namespace app\controllers;

use Yii;
use app\models\Xtravelhead;
use app\models\XtravelheadSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\sottocommessa;
use app\models\Xtravelrow;
use yii\helpers\ArrayHelper;
use yii\db\Query;
use yii\helpers\Json;
use yii\web\JsExpression;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;
use yii\db\Expression;
use app\models\XTappe;
use app\models\XRoomlist;
use app\models\XVenue;
use app\models\Xstruttura;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use yii\helpers\FileHelper;
use yii\web\Response;
use app\models\AllFiles;
use app\models\Cf;

//use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

/**
 * XtravelheadController implements the CRUD actions for Xtravelhead model.
 */
class XtravelheadController extends Controller
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
     * Lists all Xtravelhead models.
     * @return mixed
     */
public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $searchModel = new XtravelheadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$dataProvider->sort->defaultOrder = ['datath' => SORT_DESC];
//$dataProvider->pagination->pageSize=9;
  $dataProvider->query->andWhere(['fatturato' => '0']);
  $dataProvider->query->andWhere(['!=', 'bloccato', 1]);
 $dataProvider->pagination = false;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionIndex3()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $searchModel = new XtravelheadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$dataProvider->sort->defaultOrder = ['fatturato' => SORT_ASC,'datath' => SORT_DESC];
//$dataProvider->pagination->pageSize=9;
$userId = \Yii::$app->user->id;
$user = \app\models\User::findOne($userId);
  $dataProvider->query->andWhere(['fatturato' => '1']);
  if ( $user->level<>70) {
    $dataProvider->query->orWhere([ 'bloccato'=> 1]);
       }
  
 $dataProvider->pagination = false;
        return $this->render('index3', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }



    public function actionIndex2()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $searchModel = new XtravelheadSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
$dataProvider->sort->defaultOrder = ['fatturato' => SORT_ASC,'datath' => SORT_DESC];
 $dataProvider->pagination = false;

        return $this->render('index2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xtravelhead model.
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
     * Creates a new Xtravelhead model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
      /*  $model = new Xtravelhead();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->th_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);*/
    }

    /**
     * Updates an existing Xtravelhead model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
     /*   $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->th_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);*/
    }

    /**
     * Deletes an existing Xtravelhead model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
    /*    $this->findModel($id)->delete();

        return $this->redirect(['index']);*/
    }

    /**
     * Finds the Xtravelhead model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Xtravelhead the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xtravelhead::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionDupd($th_id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $model = $this->findModel($th_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'th_id' => $model->th_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    public function actionLoadmenu($tab, $q = null)

    {
        $out = ['results' => ['id' => '', 'text' => '']];

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        switch ($tab) {
            case "art":
                $query = new Query;
                $query->select(['Cd_AR as id', '[Cd_AR]+\' \'+[Descrizione] as text'])
                    ->from('adb_auxcoop.dbo.AR')
                    ->where(['Cd_ARClasse1' => 'TRV'])
                    ->andWhere(['LIKE', 'Cd_AR', $q])
                    ->limit(40);

                $command = $query->createCommand();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                //$listart = ArrayHelper::map($tdart, 'id', 'text');
                break;
            case "ruoli":

                $query = new Query;
                $query->select(['cd_ruolo as id', '[cd_ruolo]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.xruoli')
                    ->Where(['LIKE', 'cd_ruolo', $q])
                    ->Orwhere(['like', 'descrizione', $q])
                    ->limit(40);
                $command = $query->createCommand();

                $data           = $command->queryAll();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;


                break;
            case "cli":

                $query = new Query;
                $query->select(['cd_cf as id', '[cd_Cf]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.cf')

                    ->where(['TipoCF' => 'C'])
                    ->andWhere(['LIKE', 'descrizione', $q])
                    ->limit(40);
                $command = $query->createCommand();

                $data           = $command->queryAll();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                break;
            case "citta":
                $query = new Query;
                $query->select(['cd_citta as id', '[cd_citta]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.x_citta')
                    ->Where(['LIKE', 'cd_citta', $q])
                    ->Orwhere(['like', 'descrizione', $q])
                    ->limit(40);
                $command        = $query->createCommand();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;



                break;
            case "sottocommessa":
                $query = new Query;
                $query->select(['[Cd_DOsottoCommessa] as id', '[Cd_DOsottoCommessa]+\' \' +[Descrizione] as text'])
                    //select (['ID', 'CONCAT(ID,\' \',Descrizione) AS text'])
                    ->from('adb_auxcoop.dbo.dosottocommessa')
                    ->Where(['LIKE', 'Cd_DOsottoCommessa', $q])
                    ->Orwhere(['like', 'descrizione', $q])
                    ->limit(40);
                $command = $query->createCommand();
                $data = $command->queryAll();
                $out['results'] = array_values($data);





                /*
foreach ($data as $i => $t) {

    Yii::info("sono dentro e l'il risultato  è    " . $t['text']);
    Yii::info("sono dentro e l'id  è    " . $t['id']);
    //          var_dump($data);
    //$out[]    = //['id' => $t['id'], 'text' => $t['text']];
    $selected = $t['id'];
}*/
                $data           = $command->queryAll();
                $out['results'] = array_values($data);


                return  $out;
                // ArrayHelper::map($tdA, 'ID', 'text');
                break;
            case "for":

                $query = new Query;
                $query->select(['cd_cf as id', '[cd_Cf]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.cf')
                    ->where(['TipoCF' => 'F'])
                    ->andWhere(['LIKE', 'descrizione', $q])
                    ->limit(40);
                $command = $query->createCommand();

                $data           = $command->queryAll();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                break;
            case "struttura":

                $query = new Query;
                $query->select(['struttura as id', '[struttura]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.x_struttura')
                    ->Where(['LIKE', 'descrizione', $q])
                    ->orWhere(['LIKE', 'struttura', $q])
                    ->limit(40);
                $command = $query->createCommand();
                $data           = $command->queryAll();
                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                break;
            case "iva":

                $query = new Query;
                $query->select(['Cd_Aliquota as id', '[Cd_Aliquota]+\' \'+[Descrizione] as text'])
                    ->from('adb_auxcoop.dbo.Aliquota')
                    ->Where(['LIKE', 'cd_aliquota', $q])
                    ->orWhere(['LIKE', 'Descrizione', $q])
                    ->limit(40);
                $command = $query->createCommand();
                $data           = $command->queryAll();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                break;

            case "credito":

                $query = new Query;
                $query->select(['codicecarta as id', '[codicecarta]+\' \'+[descrizione] as text'])
                    ->from('adb_auxcoop.dbo.x_creditcard')
                    ->Where(['LIKE', 'codicecarta', $q])
                    ->orWhere(['LIKE', 'descrizione', $q]);
                $command = $query->createCommand();
                $data           = $command->queryAll();

                $data           = $command->queryAll();
                $out['results'] = array_values($data);

                return $out;

                break;



                
        }
    }


    public function actionMasterhotel($id)
    {

$this->getuser();
        $query = new Query;
        $query->select([

            new \yii\db\Expression("isnull(party,'noset')as  party"),
            //' case when citta is null then citta_da else citta end as citta    ',
            new \yii\db\Expression("sum(tax) as ttassa"),
            new \yii\db\Expression("sum(imponibile) as timponibile"),
            new \yii\db\Expression("sum(iva) as tiva"),
            new \yii\db\Expression("sum(fee) as tfee"),
            new \yii\db\Expression("sum(totale) as ttotale"),
        ])
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->where([
                'and',
                ['th_id' => $id],
                ['AR.x_isacconto' => null]
            ])
            ->groupBy([
                'party',
                //'case when citta is null then citta_da else citta end as citta',

            ])
            ->orderBy(['party' => SORT_ASC]); // Aggiungiamo l'ordinamento qui

        $command = $query->createCommand();
        $data           = $command->queryAll();
        $out = array_values($data);
        $labels = [];
        $series = [];
        foreach ($out as $value) {
            $labels[] = $value['party'];
            $series[] = $value['ttassa'] + $value['ttotale'];
        };






        $tabellat = $this->Analisiparty($id);
        $analisitappe = $this->Analisitappe($id);
        $tappe = $this->Loadtappe($id);


        /*add per totali hotel e viaggi
      X  echo "<td>" . ($datahoteltotali[0]['timponibile'] ?? 0) . "</td>";
    echo "<td>" . ($datahoteltotali[0]['tnimp'] ?? 0) . "</td>";
   X echo "<td>" . ($datahoteltotali[0]['tiva'] ?? 0) . "</td>";
   X echo "<td>" . ($datahoteltotali[0]['tpagato'] ?? 0) . "</td>";
  X  echo "<td>" . ($datahoteltotali[0]['tfee'] ?? 0) . "</td>";
    echo "<td>" . ($datahoteltotali[0]['timpfattura'] ?? 0) . "</td>";

        
        
        */
        $query = new Query;
        $query->select([

      
            //' case when citta is null then citta_da else citta end as citta    ',
            new \yii\db\Expression("sum(tax) as ttassa"),
            new \yii\db\Expression("(sum(adb_auxcoop.[dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))) as timponibile"),
            new \yii\db\Expression(" (sum( adb_auxcoop.[dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))) as tiva"),
            new \yii\db\Expression("sum(x_pagato) as tpagato"),
            new \yii\db\Expression("sum(fee) as tfee"),

            new \yii\db\Expression("sum(totale) as ttotale"),
            new \yii\db\Expression("avg(prezzo)  as mprezzo"),

        ])
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->leftJoin('adb_auxcoop.dbo.aliquota', 'aliquota.Cd_Aliquota = xtravelrow.codiva')
            ->where([
                'and',
                ['th_id' => $id],
                ['AR.x_isacconto' => null]

            ])
            ->andWhere(['AR.cd_arclasse12' => 'TRVACC']);
           
                    $command = $query->createCommand();
        $datah           = $command->queryOne();


        $query = new Query;
        $query->select([


            //' case when citta is null then citta_da else citta end as citta    ',
            new \yii\db\Expression("sum(tax) as ttassa"),
            new \yii\db\Expression("(sum( adb_auxcoop.[dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))) as timponibile"),
            new \yii\db\Expression(" (sum( adb_auxcoop.[dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))) as tiva  "),
            new \yii\db\Expression("sum(x_pagato) as tpagato"),
            new \yii\db\Expression("sum(fee) as tfee"),

            new \yii\db\Expression("sum(totale) as ttotale"),
            new \yii\db\Expression("avg(prezzo)  as mprezzo"),
        ])
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->leftJoin('adb_auxcoop.dbo.aliquota', 'aliquota.Cd_Aliquota = xtravelrow.codiva')
            ->where([
                'and',
                ['th_id' => $id],
                ['AR.x_isacconto' => null]

            ])
            ->andWhere(['IN', 'AR.cd_arclasse12', ['TRVBIG', 'TRVVOL', 'TRVTRA','TRVNOL']]);

        $command = $query->createCommand();
        $datav           = $command->queryOne();



        // Calcolo totale generale
        $total_budget = floatval($datah['ttotale']) + floatval($datav['ttotale']);

        // Calcolo percentuali sul budget (bdg)
        $datah['bdg'] = $total_budget != 0 ? round(($datah['ttotale'] / $total_budget) * 100, 2) : 0;
        $datav['bdg'] = $total_budget != 0 ? round(($datav['ttotale'] / $total_budget) * 100, 2) : 0;











        return $this->render('masterhotel', [
            'model' => $this->findModel($id),
            'tappe' => $tappe,
            'pagine' => count($tappe),
            'labels' => $labels,
            'series' => $series,
            'dettaglio' => $tabellat,
            'analisitappe' => $analisitappe,
            'pivot' => $this->mediaprezzi($id),
            'roomlist' => $this->roomlist($id),
            'recap' => $this->recap($id),
            'listatappetool' => $this->listatappetool($id),
            'listaart' => $this->listaart(),
            'datahoteltotali' => $datah,
            'dataviaggitotali' => $datav,
        ]);
    }




    public function Loadtappe($th_id = null)
    {

        $query = new Query;
        $query->select([
            'check_in',
            //' case when citta is null then citta_da else citta end as citta    ',
            new \yii\db\Expression("coalesce(CASE WHEN citta IS NULL THEN citta_da ELSE citta END,'non corretto') AS citta"),

            'cd_cf_ft'
        ])
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->where([
                'and',
                ['th_id' => $th_id],
                ['AR.x_isacconto' => null]
            ])
            ->groupBy([
                'check_in',
                //'case when citta is null then citta_da else citta end as citta',
                new \yii\db\Expression("coalesce(CASE WHEN citta IS NULL 
                THEN citta_da ELSE citta END,'non corretto') "),

                'cd_cf_ft'
            ])
            ->orderBy(['cd_cf_ft' => SORT_ASC,  
            'check_in' => SORT_ASC,'citta' => SORT_ASC]); // Aggiungiamo l'ordinamento qui

        $command = $query->createCommand();
        $data           = $command->queryAll();
        $out = array_values($data);




        return $out;
    }

    public function actionLoadtappa($th_id, $cliente, $citta)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses[] = "citta='" . $citta . "'";
        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses[] = "citta_da='" . $citta . "'";
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";



            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses2[] = "citta='" . $citta . "'";
        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);
        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $pagamenti = $command->queryAll();
        // Yii::debug("SQL Query: $sql");
        //Yii::debug("Params: " . json_encode($params));
        //   var_dump(empty($pagamenti));
        if (1 == 2) {
            //(empty($pagamenti)==true ){
            var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;

            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses3[] = "citta_da='" . $citta . "'";
            $where3 = 'WHERE ' . implode(' AND ', $whereClauses2);


            $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where3  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
        $html = $this->renderajax('_detail', [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ]);
        return Json::encode($html);
    }


    /*public function actionTabsdata($id)
    {
        // return '/index.php?r=gacattivita%2Fmyatt%3Fid%3D1';
        $searchModel             = new GacspeseSearch();
        $searchModel->id_sub_prv = $id;

        $dataProvider = $searchModel->search(['id_sub_prv' => $id]);

        $html = $this->renderajax('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'id'           => $id,
        ]);
       

    }*/



    public function Analisiparty($id)
    {
        $query = new Query;
        $query->select([
            'party',
            //' case when citta is null then citta_da else citta end as citta    ',
            new \yii\db\Expression("sum(tax) as ttassa"),
            new \yii\db\Expression("sum(imponibile) as timponibile"),
            new \yii\db\Expression("sum(iva) as tiva"),
            new \yii\db\Expression("sum(fee) as tfee"),
            new \yii\db\Expression("sum(totale) as ttotale"),
        ])
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->where([
                'and',
                ['th_id' => $id],
                ['AR.x_isacconto' => null]
            ])
            ->groupBy([
                'party',
                //'case when citta is null then citta_da else citta end as citta',

            ])
            ->orderBy(['party' => SORT_ASC]); // Aggiungiamo l'ordinamento qui

        $command = $query->createCommand();
        $data           = $command->queryAll();
        $out = array_values($data);
        $labels = [];
        $series = [];
        foreach ($out as $value) {
            $labels[] = $value['party'];
            $series[] = $value['ttassa'] + $value['ttotale'];
        };






        return $out;
    }
    public function Analisitappe($id)
    {
        $query = new Query;
        $query->select(
            [

                //' case when citta is null then citta_da else citta end as citta    ',
                new \yii\db\Expression("coalesce(CASE WHEN citta IS NULL THEN citta_da ELSE citta END,'non corretto') as citta"),
                new \yii\db\Expression("avg(prezzo) as prezzo"),
            ]
        )
            ->from('adb_auxcoop.dbo.xtravelrow')
            ->leftJoin('adb_auxcoop.dbo.AR', 'AR.Cd_AR = xtravelrow.cd_Ar')
            ->where([
                'and',
                ['th_id' => $id],
                ['AR.x_isacconto' => null]
            ])
            ->groupBy([
                new \yii\db\Expression("CASE WHEN citta IS NULL THEN citta_da ELSE citta END"),
                //'case when citta is null then citta_da else citta end as citta',

            ]);
        // ->orderBy(['party' => SORT_ASC]); // Aggiungiamo l'ordinamento qui

        $command = $query->createCommand();
        $data           = $command->queryAll();
        $out = array_values($data);


        $labels = [];
        $series = [];
        foreach ($out as $value) {
            $labels[] = $value['citta'];
            $series[] = [
                'name' => $value['citta'],
                'data' => $value['prezzo']
            ];
        };

        return [$labels, $series];
    }


    public function mediaprezzi($id)
    {

        $db = Yii::$app->db5;

        // 1. Estrai le città uniche
        $command = $db->createCommand("
        SELECT DISTINCT 
       --    CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta
       coalesce( CASE WHEN citta IS NULL THEN REPLACE(citta_da, '''', '''''')  ELSE REPLACE(citta, '''', '''''')  END ,'non corretto') AS citta 
       FROM xtravelrow
        LEFT JOIN ar ON ar.Cd_AR = xtravelrow.cd_Ar
        WHERE th_id = $id
        AND ar.x_isacconto IS NULL
        		and len( coalesce( CASE WHEN citta IS NULL THEN REPLACE(citta_da, '''', '''''') 
	   ELSE REPLACE(citta, '''', '''''')  END ,'non corretto'))<>0
    ");

        $cities = $command->queryColumn(); // Restituisce un array di città
        $columns = array_map(fn($city) => "[$city]", $cities);
        $columnsList = implode(', ', $columns);
        if (!empty($columnsList)) {
        // 2. Costruisci la query dinamica PIVOT
        $pivotQuery = "
        DECLARE @cols AS NVARCHAR(MAX), @query AS NVARCHAR(MAX);

        -- Imposta l'elenco delle città come colonne
        SET @cols = N'" . $columnsList . "';

        -- Costruisci la query dinamica PIVOT
        SET @query = '
        SELECT 
            party, ' + @cols + '
        FROM
        (
            SELECT 
                coalesce(party,''non corretto'')    AS party,
               coalesce( CASE WHEN citta IS NULL THEN citta_da ELSE citta END,
               ''non corretto'') AS citta,
                AVG(prezzo) AS media
            FROM 
                xtravelrow
            LEFT JOIN 
                ar ON ar.Cd_AR = xtravelrow.cd_Ar
            WHERE 
                th_id = $id
                AND ar.x_isacconto IS NULL
            GROUP BY 
                party,
               coalesce( CASE WHEN citta IS NULL THEN citta_da ELSE citta END,''non corretto'')
        ) AS SourceTable
        PIVOT
        (
            MAX(media)
            FOR citta IN (' + @cols + ')
        ) AS PivotTable
        ORDER BY party;
        ';

        -- Esegui la query dinamica
        EXEC sp_executesql @query;
    ";

        $command = $db->createCommand($pivotQuery);
        $results = $command->queryAll();

        }else{$results=[];}


        // 3. Calcola le medie per colonna
        $columnSums = array_fill_keys($cities, 0);
        $rowCount = count($results );

        foreach ($results as $row) {
            foreach ($cities as $city) {
                $columnSums[$city] += $row[$city] ?? 0;
            }
        }

        $columnAverages = array_map(fn($sum) => $sum / $rowCount, $columnSums);

        // 3. Visualizza i risultati
        return  [
            'results' => $results,
            'columns' => $cities,
            'columnAverages' => $columnAverages
        ];
    }

    public function roomlist($id,$d=null)
    {

        $db = Yii::$app->db5;

        // 1. Estrai le città uniche
        if ($d) {
            $command = $db->createCommand(
                "select distinct UPPER(nominativo) as guest,[id_guest],
        (cd_Ar) as cd_Ar ,note,evaso,ruolo,party,commessa from x_roomlist where th_id=$id
         and nominativo is not null
                and cd_Ar not in (select cd_ar from ar where x_isacconto =1)

     /*  and cd_ar not like '%#%%'*/
group by nominativo  ,cd_ar,[id_guest],note,evaso,ruolo,party,commessa
      "
            );
        }else {
             
   
        $command = $db->createCommand("select distinct UPPER(nominativo) as guest,[id_guest],
        (cd_Ar) as cd_Ar ,note,evaso,ruolo,party,commessa from x_roomlist where th_id=$id
         and nominativo is not null
                and cd_Ar not in (select cd_ar from ar where x_isacconto =1)

     /*  and cd_ar not like '%#%%'*/
group by nominativo  ,cd_ar,[id_guest],note,evaso,ruolo,party,commessa
 
         ");
        }
        $results = $command->queryAll();
        return $results;
    }


    public function listatappetool($id)
    {
        $db = Yii::$app->db5;

        // 1. Estrai le città uniche
        $command = $db->createCommand("select id_tappa,th_id,data,citta,evaso	
        	 from x_tappe
where th_id=$id
order by data asc 
");

        $results = $command->queryAll();
        return $results;
    }

    public function listaart()
    {
        $db = Yii::$app->db5;




        // 1. Estrai le città uniche
        $command = $db->createCommand("select cd_ar as ID ,descrizione  as Desk from ar 
where( Cd_ARClasse2 in(select valore from xtravelconfig where Parametro ='cat_Hotel')
or Cd_ARClasse2 in (select valore from xtravelconfig where Parametro ='cat_biglietti')
or Cd_ARClasse1 in (select valore from xtravelconfig where Parametro ='Categoria_Ar'))
and cd_Ar not in (select valore from xtravelconfig where Parametro ='extra' or Parametro='parking')
and cd_ar not like '%10%'
and obsoleto=0
");

        $results = $command->queryAll();
        return $results;
    }

    public function recap($id,$tipo=null)
    {
        $db = Yii::$app->db5;

        // 1. Estrai le città uniche
        $command = $db->createCommand("select dotes.DataDoc,dotes.NumeroDoc,
         dotes.cd_do, DOTotali.TotaPagareV,DOTotali.AccontoV,DOTotali.TotDocumentoV,
         dototali.totimponibilev from dotes
left join dototali on DOTotali.Id_DoTes=dotes.Id_DoTes
where dotes.Id_DoTes in (select xid_dotesft from xtravelhead where th_id=$id)
or dotes.x_th_id=$id
");

        $dotes = $command->queryAll();



        $command = $db->createCommand(
            "SELECT x_tiposhow FROM xtravelhead WHERE th_id = :id"
        );
        $tipo = $command->bindValue(':id', $id)->queryOne();
        //Yii::warning($tipo);



         
            switch ($tipo['x_tiposhow']){
           case null:

case 1:   
            $command = $db->createCommand("
       SELECT 
    sub.citta_eff AS citta,
    MIN(sub.check_in) AS datainizio,
    MAX(sub.check_out) AS datafine,
    AVG(sub.prezzo) AS mprezzo,
    SUM(sub.imponibile_calc) AS imponibile,
    SUM(sub.tax) AS ctax,
    SUM(sub.iva_calc) AS iva,
    SUM(sub.imponibile_calc + sub.tax + sub.iva_calc) AS totale,
    SUM(sub.fee) AS fee,
    SUM(sub.imponibile_calc + sub.fee) AS totaleimpfatt,
    SUM(sub.totalegenerale) AS ttotalegenerale,
    SUM(sub.totfattura) AS tfatt,
    SUM(sub.real_iva_calc) AS real_iva
FROM (
    SELECT 
        x.check_in,
        x.check_out,
        x.prezzo,
        x.qta,
        x.totale,
        x.fee,
        x.totalegenerale,
        x.totfattura,
        x.citta,
        x.citta_da,
        x.cd_Ar,
        x.codiva,
        ar.x_isacconto,
        Aliquota.aliquota,
        -- calcoli UDF una volta per riga
        [dbo].[afn_Scorporo_GetImponibile](x.prezzo * x.qta, Aliquota.Aliquota, 2) AS imponibile_calc,
        [dbo].[afn_Scorporo_GetImposta](x.prezzo * x.qta, Aliquota.Aliquota, 2) AS iva_calc,
        [dbo].[afn_Scorporo_GetImposta](x.totale, Aliquota.Aliquota, 2) AS real_iva_calc,
        -- colonna calcolata per il GROUP BY
        CASE WHEN x.citta IS NULL THEN x.citta_da ELSE x.citta END AS citta_eff,
        x.tax
    FROM xtravelrow AS x
    LEFT JOIN ar ON ar.Cd_AR = x.cd_Ar
    LEFT JOIN Aliquota ON Aliquota.Cd_Aliquota = x.codiva
    LEFT JOIN x_tappe ON x_tappe.id_tappa = x.id_tappa
                     AND x_tappe.th_id = x.th_id
    WHERE x.th_id = :id
      AND ar.x_isacconto IS NULL
) AS sub
GROUP BY sub.citta_eff
ORDER BY MIN(sub.check_in) ASC
        
        
        ");
        break;
        case 2:
                $command = $db->createCommand("
        select 
--case when citta is null then citta_da else citta end 
sottocommessa
as citta,
min(check_in) as datainizio,
max (check_out) as datafine,
--struttura,
avg(prezzo) as mprezzo,
--sum(imponibile) as imponibile,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as imponibile,
sum(tax) as ctax,
--sum(iva) as iva,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))+sum(tax)+sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2)) as totale,
sum(fee) as fee,
sum(imponibile) +sum(fee) as totaleimpfatt,
sum(totalegenerale)  as ttotalegenerale,
sum(totfattura) as tfatt,
sum([dbo].[afn_Scorporo_GetImposta](xtravelrow.totale,Aliquota.Aliquota,2)) as real_iva
from xtravelrow
left join ar on ar.Cd_AR=xtravelrow.cd_Ar
left join  Aliquota on Aliquota.Cd_Aliquota=xtravelrow.codiva
where th_id=:id
and ar.x_isacconto is null
group by 
sottocommessa
--case when citta is null then citta_da else citta end 
order by min(check_in) asc 
--,
--struttura
        
        
        ");
                break;
   case 3:
                $command = $db->createCommand("
        select 
--case when citta is null then citta_da else citta end 
sottocommessa
as citta,
min(check_in) as datainizio,
max (check_out) as datafine,
--struttura,
avg(prezzo) as mprezzo,
--sum(imponibile) as imponibile,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))as imponibile,
sum(tax) as ctax,
--sum(iva) as iva,
sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2))as iva,
sum( [dbo].[afn_Scorporo_GetImponibile]((prezzo *qta),aliquota.aliquota,2))+sum(tax)+sum( [dbo].[afn_Scorporo_GetImposta]((prezzo *qta),aliquota.aliquota,2)) as totale,
sum(fee) as fee,
sum(imponibile) +sum(fee) as totaleimpfatt,
sum(totalegenerale)  as ttotalegenerale,
sum(totfattura) as tfatt,
sum([dbo].[afn_Scorporo_GetImposta](xtravelrow.totale,Aliquota.Aliquota,2)) 
as real_iva,
cd_cf_ft
from xtravelrow
left join ar on ar.Cd_AR=xtravelrow.cd_Ar
left join  Aliquota on Aliquota.Cd_Aliquota=xtravelrow.codiva
where th_id=:id
and ar.x_isacconto is null
group by 
--case when citta is null then citta_da else citta end 
sottocommessa,
cd_cf_ft
order by min(check_in) asc ,sottocommessa,
cd_cf_ft
--,
--struttura
        
        
        ");
                break;                 
         }
 $command->bindValue(':id', $id);

        $tappetour = $command->queryAll();



        switch ($tipo['x_tiposhow']) {
          case null:
case 1:
case 1:
                // 1. Estrai le città uniche
                $command = $db->createCommand(" 
        
        select 
case when citta is null then citta_da else citta end as citta,
descli,
min(check_in) as datainizio,
max (check_out) as datafine,
--struttura,
avg(prezzo) as mprezzo,
sum(imponibile) as imponibile,
sum(tax) as ctax,
sum(iva) as iva,
sum(totale) as totale,
sum(fee) as fee,
sum(imponibile) +sum(fee) as totaleimpfatt
from xtravelrow
left join ar on ar.Cd_AR=xtravelrow.cd_Ar
where th_id=:id
and ar.x_isacconto is null
group by 
case when citta is null then citta_da else citta end ,
--struttura,
descli
order by descli asc 
        
        ");
        break;
            case 2:
                $command = $db->createCommand(" 
        
        select 
--case when citta is null then citta_da else citta end 
sottocommessa
as citta,
descli,
isnull(min(check_in),getdate()) as datainizio,
max (check_out) as datafine,
--struttura,
avg(prezzo) as mprezzo,
sum(imponibile) as imponibile,
sum(tax) as ctax,
sum(iva) as iva,
sum(totale) as totale,
sum(fee) as fee,
sum(imponibile) +sum(fee) as totaleimpfatt
from xtravelrow
left join ar on ar.Cd_AR=xtravelrow.cd_Ar
where th_id=:id
and ar.x_isacconto is null
group by 
sottocommessa,
--case when citta is null then citta_da else citta end ,
--struttura,
descli
order by descli asc 
        
        ");
break;
            case 3:
                // 1. Estrai le città uniche
                $command = $db->createCommand(" 
        
        select 
--case when citta is null then citta_da else citta end 
sottocommessa
as citta,
descli,
min(check_in) as datainizio,
max (check_out) as datafine,
--struttura,
avg(prezzo) as mprezzo,
sum(imponibile) as imponibile,
sum(tax) as ctax,
sum(iva) as iva,
sum(totale) as totale,
sum(fee) as fee,
sum(imponibile) +sum(fee) as totaleimpfatt
from xtravelrow
left join ar on ar.Cd_AR=xtravelrow.cd_Ar
where th_id=:id
and ar.x_isacconto is null
group by 
--case when citta is null then citta_da else citta end ,
sottocommessa,
--struttura,
descli
order by descli asc 
        
        ");
                break;




            }

        $command->bindValue(':id', $id);
        $tappetourcli = $command->queryAll();
        return ['dotes' => $dotes, 'tappetour' => $tappetour,
         'tappetourcli' => $tappetourcli];
    }

    public function actionSavetappe()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = ['success' => false];
    
        if (Yii::$app->request->isPost) {
            $tappe = Yii::$app->request->post('Tappe', []);
            $db = Yii::$app->db5;
    
            foreach ($tappe as $tappa) {
                $idTP = Yii::$app->db5->createCommand("SELECT NEWID()")->queryScalar();
    
                $idTappa = $idTP;       // È già un GUID passato dal form
                $thId = (int) $tappa['id'];    // ID del documento, puoi cambiarlo se è un campo diverso
                $data = $tappa['data'];
                $citta = $tappa['citta'];
                try {
                // Esegui l'inserimento diretto
                $db->createCommand()->insert('x_tappe', [
                    'id_tappa' => $idTappa,
                    'th_id' => $thId,
                    'data' => $data,
                    'citta' => $citta,
                    'evaso' => 0,
                ])->execute();


$tras=Xvenue::find()->where(['id' => $citta])->one();

                $savedTappe[] = [
                    'id_tappa' => $idTappa,
                    'th_id' => $thId,
                    'data' => $data,
                    'citta' => $tras->venue.'-'.$tras->citta,
                    'evaso' => 0,
                ];

                    XRoomlist::updateAll(['evaso' => 0], ['th_id' => $thId]);

            } catch (\yii\db\Exception $e) {
                Yii::error($e->getMessage(), __METHOD__);
                return ['success' => false, 'error' => $e->getMessage()];
            }


            }
    
            //Yii::$app->session->setFlash('success', 'Tappe salvate con successo.');
            $response['success'] = true;
            $response['tappe'] = $savedTappe;
        }
    
        return $response;


    }
    public function actionSavenominativo()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = ['success' => false];

        if (Yii::$app->request->isPost) {
            $model = new \app\models\XRoomlist(); // Usa il namespace corretto se è diverso

            $model->id_guest = Yii::$app->db5->createCommand("SELECT NEWID()")->queryScalar();
            $model->nominativo = Yii::$app->request->post('nominativo');
            $model->cd_ar = Yii::$app->request->post('cd_Ar');
            $model->th_id = Yii::$app->request->post('th_id');
            $model->note = Yii::$app->request->post('nota');
            $model->evaso = 0;
            $model->ruolo = Yii::$app->request->post('ruolo');
            $model->commessa = Yii::$app->request->post('commessa');
            $model->party = Yii::$app->request->post('party');

            try {
                if ($model->save()) {
                    $response['success'] = true;
                    $response['id_guest'] = $model->id_guest;
                    $response['data'] = [
                        'nominativo' => $model->nominativo,
                        'cd_ar' => $model->cd_ar,
                        'note' => $model->note,
                        'evaso' => $model->evaso,
                        'ruolo' => $model->ruolo,
                        'commessa' => $model->commessa,
                        'party' => $model->party,   
                    ];
                } else {
                    $response['error'] = $model->getErrors();
                }
            } catch (\Exception $e) {
                $response['error'] = $e->getMessage();
            }
        }

        return $response;

        // return json_encode(['success' => true]);
    }


    public function actionExportDettaglio($dettaglio = null)
    {

        if ($dettaglio !== null) {
            $dettaglio = unserialize(urldecode($dettaglio));
        } else {
            throw new \yii\web\BadRequestHttpException('Dati non disponibili per l\'esportazione.');
        }



        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definisci le intestazioni
        $headers = [
            'Commessa',
            'Cliente',
            'Cognome - Nome',
            'RUOLO',
            'PARTY',
            'STRUTTURA',
            'DATA IN',
            'DATA OUT',
            'TOT.NOTTI',
            'Notti Tax',
            'CONFERMA',
            'TIPOLOGIA',
            'Costo Notte',
            'CITY TAX',
            'TOT COST',
            'TOT CITY',
            'TAX PARK',
            'EXTRAS',
            'TOTALE Complessivo',
            '7%',
            'FEE',
            'IVA'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        // Inserisci i dati
        $row = 2;
        foreach ($dettaglio as $data) {
            $sheet->fromArray([
                $data['x_scdesc'],
                $data['descli'],
                $data['guest'],
                $data['ruolo'],
                $data['party'],
                $data['struttura'],
                date('d/m/Y', strtotime($data['check_in'])),
                date('d/m/Y', strtotime($data['check_out'])),
                $data['qta'],
                $data['qta'],
                'si',
                $data['cd_Ar'],
                $data['prezzo'],
                $data['tax_unit'],
                $data['totale'],
                $data['tax'],
                $data['tax'],
                $data['extras'],
                $data['totale_complessivo'],
                $data['fee_perc'],
                $data['fee'],
                $data['iva']
            ], NULL, 'A' . $row++);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Dettaglio_Export_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function actionLoadtappa2($th_id, $cliente, $citta)
    {
        $params = [];
        $whereClauses = [];
        $whereClauses[] = "th_id=" . $th_id;
        $whereClauses[] = "AR.x_isacconto is null";
        $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses[] = "citta='" . $citta . "'";
        $where = 'WHERE ' . implode(' AND ', $whereClauses);
        $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";
        // Esegui la query
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();
        if (count($results) == 0) {
            $params = [];
            $whereClauses = [];
            $whereClauses[] = "th_id=" . $th_id;
            $whereClauses[] = "AR.x_isacconto is null";
            $whereClauses[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses[] = "citta_da='" . $citta . "'";
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
            $sql = "
            SELECT *  from 
            adb_auxcoop.dbo.xtravelrow
            left join ar on ar.cd_ar=xtravelrow.cd_Ar  
            $where
              order by x_scdesc asc,cd_cf_ft ASC,citta asc ,struttura asc 
        ";



            // Esegui la query
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
        }
        $sql = "select x_scdesc,descli,min(check_in) as startdate, max(check_out) as enddate ,CASE WHEN citta IS NULL THEN citta_da ELSE citta END AS citta,
sum(fee_perc) as fee,sum(totale) as totale,sum(tax) as  tassa,SUM(TOTALEGENERALE) as totalegenerale
from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar  
$where   
group by x_scdesc,descli,CASE WHEN citta IS NULL THEN citta_da ELSE citta END 
order by min(check_in) asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $totale = $command->queryAll();


        $whereClauses2 = [];
        $whereClauses2[] = "th_id=" . $th_id;

        $whereClauses2[] = "cd_cf_ft='" . $cliente . "'";
        $whereClauses2[] = "citta='" . $citta . "'";
        $where2 = 'WHERE ' . implode(' AND ', $whereClauses2);
        $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where2  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
        $connection = Yii::$app->db5;
        $command = $connection->createCommand($sql, $params);
        $pagamenti = $command->queryAll();
        // Yii::debug("SQL Query: $sql");
        //Yii::debug("Params: " . json_encode($params));
        //   var_dump(empty($pagamenti));
        if (1 == 2) {
            //(empty($pagamenti)==true ){
            var_dump($pagamenti);
            $whereClauses3 = [];
            $whereClauses3[] = "th_id=" . $th_id;

            $whereClauses3[] = "cd_cf_ft='" . $cliente . "'";
            $whereClauses3[] = "citta_da='" . $citta . "'";
            $where3 = 'WHERE ' . implode(' AND ', $whereClauses2);


            $sql = "select case when x_pagato<0 then x_pagato*-1 else x_pagato end as xpagato,
data_pg,descontab,pg.Descrizione,xtravelrow.cd_pg from xtravelrow 
left join ar on ar.cd_ar=xtravelrow.cd_Ar
left join pg on pg.Cd_PG=xtravelrow.cd_pg
$where3  
and (ar.x_isacconto is not null or contabile is not  null)
order by data_pg asc ";
            $connection = Yii::$app->db5;
            $command = $connection->createCommand($sql, $params);
            $pagamenti = $command->queryAll();
        }
        $dettatappa = [
            'dettaglio' => $results,
            'th_id' => $th_id,
            'cli' => $cliente,
            'citta' => $citta,
            'totale' => $totale,
            'pagamenti' => $pagamenti
        ];
        return $dettatappa;
    }
    public function actionXcaricacitta($q = null)
    {
        $db = Yii::$app->db5;
        $query = new \yii\db\Query;
        $query->select(['cd_citta as id', 'descrizione as text'])
            ->from('x_citta')
            ->where(['like', 'descrizione', $q])
            ->limit(20);

        $command = $query->createCommand($db);
        $data = $command->queryAll();
        //var_dump($data); // Aggiungi questa linea per verificare
        //die();


        return \yii\helpers\Json::encode(['items' => $data]);
    }

    public function actionXcaricatappa($q = null)
    {
        $db = Yii::$app->db5;
        $query = new \yii\db\Query;
        $query->select([
            'id',
            new Expression("venue + ' - ' + citta AS text")
        ])
            ->from('x_venue')
            ->where(['like', 'venue', $q])
            ->orWhere(['like', 'citta', $q])
            ->limit(20);

        $command = $query->createCommand($db);
        $data = $command->queryAll();
        //var_dump($data); // Aggiungi questa linea per verificare
        //die();


        return \yii\helpers\Json::encode(['items' => $data]);
    }


    public function actionLoadcitta($q = null)
    {
        $db = Yii::$app->db5;
        $query = new \yii\db\Query;
        $query->select(['cd_citta as id', 'descrizione as text'])
            ->from('x_citta')
            ->where(['like', 'descrizione', $q])
            ->limit(20);

        $command = $query->createCommand();
        $data = $command->queryAll();

        return \yii\helpers\Json::encode(['items' => $data]);
    }


public function actionRanalisi( )
{
$data = json_decode(Yii::$app->request->rawBody, true);
  Yii::$app->response->format = \yii\web\Response::FORMAT_HTML;
    $dettaglio = $data['dettaglio'] ?? [];
    $pivot = $data['pivot'] ?? [];
    $labels = $data['labels'] ?? [];
    $series = $data['series'] ?? [];
    $analisitappe = $data['analisitappe'] ?? [];


    return $this->renderAjax('_analisi', [
        'dettaglio' => $dettaglio,
        'pivot' => $pivot,
        'labels' => $labels,
        'series' => $series,
        'analisitappe' => $analisitappe,
    ]);
}


 public function actionDettaglio2( )
{
$data = json_decode(Yii::$app->request->rawBody, true);

 $dettaglio = $data['dettaglio'] ?? [];
    $th_id =  $data['th_id'];
    $cli = $data['cli'];
    $citta = $data['citta'];
    $totale = $data['totale'];
  $pagamenti = $data['pagamenti'];





    
    return             $this->renderAjax('_detail2', [
                'dettaglio' => $dettaglio,
                'th_id' => $th_id,
                'cli' => $cli,
                'citta' => $citta,
                'totale' => $totale,
                'pagamenti' => $pagamenti

    ]);
}

public function actionRoomlist2(){
$data = json_decode(Yii::$app->request->rawBody, true);
$recap2=$data['recap2'] ??[];

return $this->renderAjax('_roomlist2', [
        'roomlist' =>$recap2,
        'tipo' => 2,
    ]);




}

public function actionRoomlist1(){
$data = json_decode(Yii::$app->request->rawBody, true);
$recap2=$data['roomlist2'] ??[];

return $this->renderAjax('_roomlist2', [
        'roomlist' =>$recap2,
        'tipo' => 1,
    ]);




}
    public function actionTools_(){
$data = json_decode(Yii::$app->request->rawBody, true);

            $dettaglio = $data['$dettaglio'] ??[];
            $th_id = $data['th_id'] ?? '';
            $pivot = $data['pivot']  ??[];
            $labels = $data['labels']  ??[];
            $series = $data['series']  ??[];
            $tappe = $data['uniqueTappe']  ??[];
            $roomlist = $data['roomlist']  ??[];
            $analisitappe = $data['analisitappe']  ??[];
            $listatappetool = $data['listatappetool'] ??[];
            $listaart = $data['listaart']  ??[];
 
return  $this->renderAjax('_tools', [
            'dettaglio' => $dettaglio,
            'th_id' => $th_id,
            'pivot' => $pivot,
            'labels' => $labels,
            'series' => $series,
            'tappe' => $tappe,
            'roomlist' => $roomlist,
            'analisitappe' => $analisitappe,
            'listatappetool' => $listatappetool,
            'listaart' => $listaart
]);



    }



public function actionGetchartdata()
{
    return $this->asJson([
        'categories' => ['Gen', 'Feb', 'Mar', 'Apr', 'Mag'],
        'series' => [10, 15, 20, 25, 30],
    ]);
}

public function actionUploadimage()
{
  $model = new xtravelhead(); // Crea un nuovo modello
   // Verifica se il form è stato caricato correttamente con i dati
    if ($model->load(Yii::$app->request->post())) {
        // Verifica che l'ID del documento (decodificatore) sia stato selezionato
       //return print_r($model->attributes, true);
 //return       print_r($_POST);
 $model->th_id=$_POST['Xtravelhead']['th_id'];
            $model->imageFile=UploadedFile::getInstance($model, 'imageFile');


      //  Yii::$app->session->setFlash('error', 'ID del documento non selezionato.'.
      //  print_r($model->attributes, true));
        if ($model->th_id) {
            // Verifica se l'immagine è stata caricata
            $image = \yii\web\UploadedFile::getInstance($model, 'imageFile');
            if ($image) {
                // Costruisci il percorso per salvare l'immagine
                $imagePath = Yii::getAlias('@webroot') . '/uploads/' .$model->th_id.'_'.$image->name;

                // Salva l'immagine nella cartella 'uploads'
                if ($image->saveAs($imagePath)) {
                    // Aggiorna il campo 'imageFile' con il percorso dell'immagine
                    $model->imageFile = '/uploads/' . $model->th_id.'_'.$image->name;

                    // Esegui l'update del documento associato
                    $document = xtravelhead::findOne(['th_id' => $model->th_id]);
if ($document) {
    // Aggiorna solo il campo 'imageFile'
    $document->updateAttributes(['imageFile' => $model->imageFile]);

    Yii::$app->session->setFlash('success', 'Immagine caricata e documento aggiornato con successo.');
} else {
    Yii::$app->session->setFlash('error', 'Documento non trovato.');
}
                } else {
                    Yii::$app->session->setFlash('error', 'Errore durante il salvataggio del file.');
                }
            } else {
                Yii::$app->session->setFlash('error', 'Nessuna immagine selezionata.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'ID del documento non selezionato.'.print_r($model->attributes, true));
        }
    }

    // Rendi la vista del form
    return $this->render('upload_image', [
        'model' => $model,
    ]);

}






public function actionDetail($id)
{
    $model = Xtravelhead::findOne($id); // Sostituisci 'Document' con il tuo modello
    return $this->renderPartial('_expand-row', ['model' => $model]);
}



public function actionAjximage()
{
    $model = new xtravelhead();

    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model->th_id = Yii::$app->request->post('Xtravelhead')['th_id'];
        $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

        if ($model->th_id && $model->imageFile) {
            $imagePath = Yii::getAlias('@webroot') . '/uploads/' . $model->th_id . '_' . $model->imageFile->name;

            if ($model->imageFile->saveAs($imagePath)) {
                $model->imageFile = '/uploads/' . $model->th_id . '_' . $model->imageFile->name;
                $document = xtravelhead::findOne(['th_id' => $model->th_id]);

                if ($document) {
                    $document->updateAttributes(['imageFile' => $model->imageFile]);
                    return ['success' => true, 'message' => 'Immagine caricata con successo'];
                } else {
                    return ['success' => false, 'message' => 'Documento non trovato'];
                }
            } else {
                return ['success' => false, 'message' => 'Errore nel salvataggio del file'];
            }
        } else {
            return ['success' => false, 'message' => 'ID documento o immagine mancanti'];
        }
    }

    return $this->render('_modal_upload_image', ['model' => $model]);
}

/*
public function actionAggiorna(){
    $nomi = [
    "Bevilaqua Martina", "Contini Giulio", "Solimbergo Fabio", "Tiberi Emanuele", "Zanoncelli Claudia", "Patricelli Ilaria", "Pampaloni Matteo", "Trigiani Alessia", "Serravalli Angela", "Bastianoni Elena", "Mongardi Angela", "Balsamo Sara", "Contini Mauro", "Astolfi Federica", "Arduini Luca", "Filippucci Martina", "Antonucci Francesco", "Patricelli Massimo", "Lucatelli Angela", "Serravalli Sara", "Fabbri Teresa", "Saroldi Nicola", "Vignoletti Serena", "Serravalli Francesco", "Marvelli Gabriele", "Sampieri Carlo", "Orlandelli Monica", "Luzzatti Stefano", "Tiberi Daniele", "Casucci Angela", "Cardaropoli Roberto", "Ursini Gabriele", "Villoresi Paolo", "Orlandelli Lorenzo", "Serravalli Andrea", "Fabbri Elena", "Palmati Giada", "Dallera Roberta", "Ursini Giovanni", "Antonucci Valentina", "Lentini Lorenzo", "Bertucci Lorenzo", "Luraschi Nicola", "Albanesi Fabio", "Saroldi Ilaria", "Luraschi Eleonora", "Zanoncelli Paolo", "Marchitelli Giovanni", "Mollicone Daniele", "Beninati Carlo", "Mattonelli Paolo", "Orlandelli Giovanni", "Fabbri Ilaria", "Villoresi Matteo", "Moroni Paolo", "Rossetto Angela", "Bevilaqua Simona", "Tamberi Marco", "Zanoncelli Laura", "Ricchetti Simona", "Gualtieri Anna", "Tamberi Enrico", "Crespi Alessandro", "Crespi Ilaria", "Palmati Ilaria", "Bocchetti Valeria", "Bastianoni Teresa", "Rivoldi Giada", "Mollicone Angela", "Montesi Anna", "Tamberi Emanuele", "Villoresi Sofia", "Serracchi Davide", "Tiberi Federica", "Saroldi Lorenzo", "Serracchi Anna", "Mongardi Paolo", "Zanoncelli Angela", "Tamberi Sara", "Gherardi Stefano", "Gherardi Pietro", "Antonucci Martina", "Dallera Alessandro", "Pancaldi Federica", "Casalgrandi Alessia", "Sampieri Lorenzo", "Tiberi Simona", "Beninati Eleonora", "Solimbergo Laura", "Renzulli Enrico", "Luzzatti Pietro", "Marvelli Alessia", "Contini Angela", "Zanoncelli Teresa", "Crespi Mauro", "Lentini Marco", "Dominicis De Giovanni", "Renzulli Elisa", "Sampieri Serena", "Giordanelli Marco", "Saroldi Enrico", "Villoresi Gabriele", "Palmati Stefano", "Cardaropoli Massimo", "Massimelli Anna", "Marchitelli Laura", "Gualtieri Daniele", "Carminati Pietro", "Luzzatti Andrea", "Filippucci Chiara", "Balsamo Andrea", "Montesi Antonio", "Palmati Luca", "Saroldi Francesco", "Moroni Elena", "Mattonelli Valeria", "Balsamo Antonio", "Palmati Alessandro", "Casucci Francesca", "Fabbri Giovanni", "Lucatelli Giulia", "Dallera Carlo", "Luraschi Monica", "Ursini Paolo", "Pancaldi Francesca", "Dallera Giovanni", "Torreggiani Francesca", "Pradini Chiara", "Vignoletti Andrea", "Zanoncelli Anna", "Ursini Massimo", "Rossetto Carlo", "Rossetto Valeria", "Bandinelli Stefano", "Rivoldi Matteo", "Dominicis De Luca", "Pampaloni Valentina", "Saroldi Antonio", "Casalgrandi Francesca", "Bandinelli Eleonora", "Ricchetti Martina", "Trigiani Nicola", "Giordanelli Matteo", "Luraschi Sofia", "Cardaropoli Gabriele", "Bocchetti Alessia", "Fabbri Carlo", "Filippucci Emanuele", "Casucci Fabio", "Bastianoni Claudia", "Tiberi Sofia", "Mattonelli Serena", "Gherardi Emanuele", "Saroldi Claudia", "Filippucci Alessandro", "Ricchetti Giovanni", "Bertucci Anna", "Luraschi Elisa", "Antonucci Laura", "Luraschi Matteo", "Pradini Laura", "Serravalli Paolo", "Casucci Nicola", "Gherardi Roberta", "Bevilaqua Chiara", "Antonucci Ilaria", "Pradini Mauro", "Tiberi Anna", "Luzzatti Chiara", "Solimbergo Francesco", "Moroni Francesca", "Filippucci Ilaria", "Pradini Alessia", "Gherardi Teresa", "Massimelli Nicola", "Gherardi Alessandro", "Massimelli Paolo", "Serravalli Elisa", "Marchitelli Valeria", "Gherardi Matteo", "Lucatelli Lorenzo", "Renzulli Emanuele", "Zanoncelli Giulia", "Arduini Alessandro", "Palmati Roberta", "Massimelli Serena", "Lucatelli Claudia", "Torreggiani Daniele", "Solimbergo Eleonora", "Gherardi Federica", "Albanesi Giulia", "Orlandelli Matteo", "Mollicone Roberta", "Marchitelli Carlo", "Balsamo Eleonora", "Serracchi Laura", "Contini Simona", "Trigiani Andrea", "Rivoldi Francesca", "Contini Lorenzo", "Pampaloni Carlo", "Palmati Francesco", "Villoresi Giada", "Saroldi Giada", "Bevilaqua Elena", "Rivoldi Federica", "Trigiani Teresa", "Mattonelli Daniele", "Luzzatti Elisa", "Carminati Lorenzo", "Mollicone Elisa", "Dominicis De Ilaria", "Trigiani Simona", "Marchitelli Francesco", "Pradini Giada", "Moroni Massimo", "Contini Monica", "Serravalli Alessandro", "Casalgrandi Gabriele", "Montesi Sofia", "Contini Matteo", "Pancaldi Roberta", "Dallera Ilaria", "Contini Carlo", "Gherardi Paolo", "Pradini Giovanni", "Bastianoni Anna", "Villoresi Emanuele", "Renzulli Eleonora", "Carminati Roberto", "Bertucci Roberto", "Pradini Davide", "Tiberi Marco", "Torreggiani Giada", "Pampaloni Claudia", "Marchitelli Lorenzo", "Montesi Enrico", "Cardaropoli Lorenzo", "Bandinelli Valentina", "Rossetto Andrea", "Serravalli Valentina", "Zanoncelli Sara", "Giordanelli Roberto", "Ursini Francesco", "Vignoletti Antonio", "Montesi Davide", "Lentini Antonio", "Montesi Teresa", "Vignoletti Sara", "Dallera Anna", "Bevilaqua Claudia", "Trigiani Giada", "Arduini Teresa", "Pampaloni Giulia", "Dominicis De Daniele", "Giordanelli Laura", "Moroni Pietro", "Tiberi Andrea", "Pancaldi Giovanni", "Carminati Anna", "Tamberi Sofia", "Zanoncelli Massimo", "Patricelli Eleonora", "Saroldi Alessandro", "Balsamo Carlo", "Luraschi Paolo", "Bevilaqua Alessia", "Marvelli Andrea", "Renzulli Stefano", "Solimbergo Alessandro", "Giordanelli Martina", "Bevilaqua Antonio", "Cardaropoli Luca", "Renzulli Andrea", "Trigiani Sara", "Palmati Federica", "Albanesi Chiara", "Bevilaqua Paolo", "Tiberi Mauro", "Crespi Veronica", "Serracchi Alessandro", "Bertucci Monica", "Lucatelli Laura", "Lucatelli Valentina", "Albanesi Stefano", "Mongardi Sara", "Montesi Martina", "Balsamo Valeria", "Luzzatti Mauro", "Fabbri Mauro", "Montesi Francesco", "Bocchetti Davide", "Carminati Francesca", "Ursini Laura", "Bandinelli Paolo", "Mattonelli Teresa", "Massimelli Laura", "Beninati Monica", "Bocchetti Lorenzo", "Luraschi Sara"
    ];
    
    // Mischia i nomi per ottenere casualità
    shuffle($nomi);
    
    // Ottieni tutte le righe che devono essere aggiornate
    $righe = Xtravelrow::find()
        ->where(  ['th_id' => 1378]) // Se guest è vuoto o NULL
        ->all();
    
    $index = 0;
    foreach ($righe as $riga) {
        if ($index >= count($nomi)) {
            shuffle($nomi); // Mischia di nuovo se i nomi finiscono
            $index = 0;
        }
    
        $riga->guest = $nomi[$index];
        $riga->save(false); // Salva senza validazione
    
        $index++;
    }
    
    echo "Aggiornati " . count($righe) . " record con nomi casuali.";
    
    
    
    }
    
*/

public function actionWizardrighe($th_id)
{
    $model = Xtravelhead::findOne($th_id); // Sostituisci 'Document' con il tuo modello
    $tappe=$this->listatappetool($th_id);
    $roomlist=$this->roomlist($th_id,true);


    $tappe_filtrate = array_filter($tappe, function($item) {
        return $item['evaso'] == 0;
    });
    
    // Filtra solo le stanze dove evaso è null
    $roomlist_filtrati = array_filter($roomlist, function($item) {
        return $item['evaso'] == 0;
    });



    $tappe_provider = new ArrayDataProvider([
        'allModels' => $tappe_filtrate,
        'pagination' => false,
 
    ]);
    $room_provider = new ArrayDataProvider([
        'allModels' => $roomlist_filtrati,
        'pagination' => false,
    ]);
    $righe=xtravelrow::find()
    ->where(['th_id' => $th_id])->asArray()->all();
    $righe_provider = new ArrayDataProvider([
        'allModels' => $righe,
        'pagination' => false,
    ]);




    $righe_g = xtravelrow::find()
    ->select([
        new Expression('COALESCE(citta, citta_da) AS citta'),
        //'check_in',
        'th_id',
        'COUNT(*) AS totale'
    ])
    ->where(['th_id' => $th_id])
    ->groupBy([
        new Expression('COALESCE(citta, citta_da)'),
        //'check_in',
        'th_id'
    ])
    ->asArray()
    ->all();
$righe_g_provider = new ArrayDataProvider([
    'allModels' => $righe_g,
    'pagination' => false,
]);

    return $this->render('wizardrighe', ['model' => $model,
    'tappe'=>$tappe_provider,'roomlist'=>$room_provider,'wzrighe'=>$righe,
    'wzrighe_g'=>$righe_g_provider,
                'xth' => $th_id]
);
}

 
 

public function actionEliminarecord()
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    // Ottenere l'ID dalla richiesta POST
    $id = Yii::$app->request->post('id');
    
    if (!$id) {
        return [
            'success' => false,
            'message' => 'ID non fornito'
        ];
    }
    
    try {
        $model = Xtravelrow::findOne($id);
        
        if ($model) {
            // Eliminazione del record
            $model->delete();
            

   $this->aggiornateste($id);
          


            return [
                'success' => true,
                'message' => 'Record eliminato con successo',
                'id' => $id
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Record non trovato'
            ];
        }
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Errore durante l\'eliminazione: ' . $e->getMessage()
        ];
    }
}

public function actionWizardrighesalva()
{
    if (Yii::$app->request->isPost) {
        $th_id = Yii::$app->request->post('th_id');
        $tappeData = json_decode(Yii::$app->request->post('tappe'), true);
        $roomlistData = json_decode(Yii::$app->request->post('roomlist'), true);
        
        // Array per debugging
        $debugInfo = [];
        $debugInfo['num_tappe'] = count($tappeData);
        $debugInfo['num_roomlist'] = count($roomlistData);
        $debugInfo['tappe'] = array_column($tappeData, 'citta');
        $debugInfo['first_guests'] = array_slice(array_column($roomlistData, 'guest'), 0, 5); // primi 5 guest
        
        // Contatori per statistiche
        $insertedRows = 0;
        $tappeCounts = [];
        
        // Transazione per garantire l'integrità dei dati
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Opzionale: elimina le righe esistenti per evitare duplicati
         //   $deletedRows = XTravelrow::deleteAll(['th_id' => $th_id]);
         //   $debugInfo['deleted_rows'] = $deletedRows;
            
            // Per ogni tappa...
            foreach ($tappeData as $index => $tappa) {
                $id_tappa = $tappa['id_tappa'];
               // $data_tappa = $tappa['data'];
                $citta_tappa = $tappa['citta'];
                
 // Converti la data nel formato corretto per SQL Server
                // Se la data è già in formato SQL Server (yyyy-mm-dd), usiamola direttamente
                // altrimenti tentiamo di convertirla
                $data_tappa_raw = $tappa['data'];
                $dateTime = new \DateTime($data_tappa_raw);
                
                // Prova diversi formati
                $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
                $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
                $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito
                
              // Yii::info("Data originale: $data_tappa_raw");
               // Yii::info("Formato ISO: $formato1");
                //Yii::info("Formato Europeo: $formato2");
               // Yii::info("Formato Invertito: $formato3");
                
                // Usa quello che funziona
                $data_tappa = $formato3; // o qualsiasi altro formato che hai verificato funzionare

                $tappaCounts = ['citta' => $citta_tappa, 'inseriti' => 0, 'errori' => 0];
                
                // Per ogni guest nella roomlist...
                foreach ($roomlistData as $guest) {
                    // Creo un nuovo record nella tabella xtravelrow
                    $xTravelRow = new XTravelrow();
                    $xTravelRow->th_id = $th_id;
                    $xTravelRow->guest = $guest['guest'];
                    $xTravelRow->cd_Ar = $guest['cd_Ar'];
                    $xTravelRow->citta = $citta_tappa;
                    $xTravelRow->check_in = $data_tappa;
                    $xTravelRow->ruolo = $guest['ruolo'];
                        $xTravelRow->party = $guest['party'];
                        $xTravelRow->sottocommessa = $guest['commessa'];
                   $xTravelRow->id_tappa = $id_tappa;
                   // $xTravelRow->cd_cf_ft = $guest['cd_cf_ft'];
                    $xTravelRow->id_nominativo = $guest['id_guest'];
                        $xTravelRow->duseri= Yii::$app->user->id;
                       // Salvo il record
                        if ($xTravelRow->save()) {
                        $insertedRows++;
                        $tappaCounts['inseriti']++;
                    } else {
                        $tappaCounts['errori']++;
                        $tappaCounts['ultimo_errore'] = json_encode($xTravelRow->errors);
                    }
                }
                
                // Aggiorno la tappa come evasa
                $xTappe = XTappe::findOne(['id_tappa' => $id_tappa]);
                if ($xTappe) {
                    $xTappe->evaso = 1;
                    $xTappe->save();
                } else {
                    $tappaCounts['errore_tappa'] = "Tappa non trovata con ID: " . $id_tappa;
                }
                
                $tappeCounts[] = $tappaCounts;
            }
            
            // Aggiorno tutti i guest nella tabella x_roomlist come evasi
            $updatedGuests = 0;
            $guestErrors = 0;
            
            foreach ($roomlistData as $guest) {
                $guestId = $guest['id_guest'];
                $xRoomlist = XRoomlist::findOne(['id_guest' => $guestId, ]);
                if ($xRoomlist) {
                    $xRoomlist->evaso = 1;
                        $xRoomlist->th_id= $th_id;
                    if ($xRoomlist->save()) {
                        $updatedGuests++;
                    } else {
                        $guestErrors++;
                    }
                }
            }
            
            // Aggiungo informazioni finali di debug
            $debugInfo['inserted_rows'] = $insertedRows;
            $debugInfo['updated_guests'] = $updatedGuests;
            $debugInfo['guest_errors'] = $guestErrors;
            $debugInfo['tappe_counts'] = $tappeCounts;
                $debugInfo['righe']= json_encode(
                    $xTravelRow);
            
            // Commit della transazione
            $transaction->commit();
            
            // Salvo info di debug in sessione per visualizzarle nella pagina successiva
            Yii::$app->session->set('debug_wizard', $debugInfo);
            
            $message = 'Operazione completata. Inserite ' . $insertedRows . ' righe su ' . 
                      count($tappeData) . ' tappe e ' . count($roomlistData) . ' ospiti.';
            
            Yii::$app->session->setFlash('success', $message);
            



$this->aggiornateste($th_id);





            // Redirect alla pagina appropriata
            return $this->redirect(['xtravelhead/wizardrighe', 'th_id' => $th_id]);
            
        } catch (\Exception $e) {
            // Rollback in caso di errore
            $transaction->rollBack();
            
            // Salvo info errore in sessione
            $debugInfo['error'] = $e->getMessage();
            $debugInfo['error_trace'] = $e->getTraceAsString();
            Yii::$app->session->set('debug_wizard', $debugInfo);
            
            Yii::$app->session->setFlash('error', 'Si è verificato un errore: ' . $e->getMessage());
            
            // Redirect alla pagina precedente o alla pagina di errore
            return $this->redirect(['xtravelhead/wizardrighe', 'th_id' => $th_id]);
        }
    }
    
    // Se non è una richiesta POST, redirect alla home o alla pagina appropriata
    return $this->redirect(['xtravelhead/index']);
}


public function aggiornateste($id){

        $db = Yii::$app->db5;

        // Recupera i valori da assegnare
        $righe = (new \yii\db\Query())
            ->from('xtravelrow')
            ->where(['th_id' => $id])
            ->count('*', $db);

        $totft = (new \yii\db\Query())
            ->from('xtravelrow')
            ->where(['th_id' => $id])
            ->andWhere([
                'not in',
                'cd_Ar',
                (new \yii\db\Query())
                    ->select('cd_ar')
                    ->from('ar')
                    ->where(['x_isacconto' => 1])
            ])
            ->sum('ISNULL(totfattura, 0)', $db);

        $fee = (new \yii\db\Query())
            ->from('xtravelrow')
            ->where(['th_id' => $id])
            ->andWhere([
                'not in',
                'cd_Ar',
                (new \yii\db\Query())
                    ->select('cd_ar')
                    ->from('ar')
                    ->where(['x_isacconto' => 1])
            ])
            ->sum('ISNULL(fee, 0)', $db);

        $totaleservizi = (new \yii\db\Query())
            ->from('xtravelrow')
            ->where(['th_id' => $id])
            ->andWhere([
                'not in',
                'cd_Ar',
                (new \yii\db\Query())
                    ->select('cd_ar')
                    ->from('ar')
                    ->where(['x_isacconto' => 1])
            ])
            ->sum('ISNULL(totale, 0)', $db);

        $tax = (new \yii\db\Query())
            ->from('xtravelrow')
            ->where(['th_id' => $id])
            ->andWhere([
                'not in',
                'cd_Ar',
                (new \yii\db\Query())
                    ->select('cd_ar')
                    ->from('ar')
                    ->where(['x_isacconto' => 1])
            ])
            ->sum('ISNULL(tax, 0)', $db);

        // Fai l'UPDATE
        $db->createCommand()
            ->update('xtravelhead', [
                'righe' => $righe,
                'totft' => $totft,
                'fee' => $fee,
                'totaleservizi' => $totaleservizi,
                'tax' => $tax,
            ], ['th_id' => $id])
            ->execute();


}




public function actionWizartappe($th_id,$tappa,$data=null)
{
    $model = Xtravelhead::findOne($th_id); // Sostituisci 'Document' con il tuo modello
    $tappe=$this->listatappetool($th_id);
    $roomlist=$this->roomlist($th_id);


    $tappe_filtrate = array_filter($tappe, function($item) {
        return $item['evaso'] === null;
    });
    
    // Filtra solo le stanze dove evaso è null
    $roomlist_filtrati = array_filter($roomlist, function($item) {
        return $item['evaso'] === null;
    });

   
  
    $data_tappa_raw = $data;
    $dateTime = new \DateTime($data_tappa_raw);
    
    // Prova diversi formati
    $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
    $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
    $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito
    
  // Yii::info("Data originale: $data_tappa_raw");
   // Yii::info("Formato ISO: $formato1");
    //Yii::info("Formato Europeo: $formato2");
   // Yii::info("Formato Invertito: $formato3");
    
    // Usa quello che funziona
    $data_tappa = $formato3;
 
    $tappe_provider = new ArrayDataProvider([
        'allModels' => $tappe_filtrate,
        'pagination' => false,
 
    ]);
    $room_provider = new ArrayDataProvider([
        'allModels' => $roomlist_filtrati,
        'pagination' => false,
    ]);
    
    $query = xtravelrow::find()
    ->where(['th_id' => $th_id]);

 if (isset($data))  // Controlla se la data è valida
{
    $query->andWhere(['check_in' => $data_tappa]);
}else{
   // $query->andWhere(['check_in' => null]);
}

$query->andWhere([
    'or',
    ['citta' => $tappa],
    ['citta_da' => $tappa]
]);
$query->orderBy(['check_in'=> SORT_ASC,
'citta'=>SORT_ASC,
'tr_id'=>SORT_ASC

]);
$righe = $query->asArray()->all();


    $righe_provider = new ArrayDataProvider([
        'allModels' => $righe,
        'pagination' => false,
    ]);




    $righe_g = xtravelrow::find()
    ->select([
        new Expression('COALESCE(citta, citta_da) AS citta'),
        //'check_in',
        'th_id',
        'COUNT(*) AS totale'
    ])
    ->where(['th_id' => $th_id])
    ->groupBy([
        new Expression('COALESCE(citta, citta_da)'),
        //'check_in',
        'th_id'
    ])
    //->orderBy(['check_in','tr_id'])
    ->asArray()
    ->all();
$righe_g_provider = new ArrayDataProvider([
    'allModels' => $righe_g,
    'pagination' => false,
]);

    return $this->render('Wizartappe',
     ['righe'=>$righe,'tappa'=>$tappa,'data'=>$data_tappa,
    'th_id'=>$th_id]);
}




    public function actionGetcities($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = (new \yii\db\Query())
            ->select(['UPPER(cd_citta) as id', 'upper(descrizione) as text'])
            ->from('x_citta')
            ->where(['like', 'descrizione', $q]);
           
           // ->limit(15); // Limit for better performance

        $command = $query->createCommand(Yii::$app->db5);
        $data = $command->queryAll();

        return ['results' => $data];
    }
    public function actionGetart($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = (new \yii\db\Query())
            ->select(['cd_Ar as id', 'descrizione as text'])
            ->from('ar')
            ->andWhere(['Obsoleto' => 0])
            ->andWhere([
                'or',
                ['like', 'descrizione', $q],
                ['like', 'cd_Ar', $q]
            ]);
        // ->limit(15); // Limit for better performance

        $command = $query->createCommand(Yii::$app->db5);
        $data = $command->queryAll();

        return ['results' => $data];
    }
    //Cd_Aliquota
    public function actionGetali($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = (new \yii\db\Query())
            ->select(['Cd_Aliquota as id', 'Cd_Aliquota as text'])
            ->from('Aliquota')
            ->where(['like', 'descrizione', $q])
            ->Orwhere(['like', 'Cd_Aliquota', $q]);

        // ->limit(15); // Limit for better performance

        $command = $query->createCommand(Yii::$app->db5);
        $data = $command->queryAll();

        return ['results' => $data];
    }
    public function actionGetaliart($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $query = (new \yii\db\Query())
            ->select(['cd_aliquota_v'])
            ->from('ar')
             ->where(['like', 'cd_Ar', $q]);

        // ->limit(15); // Limit for better performance

        $command = $query->createCommand(Yii::$app->db5);
        $data = $command->queryAll();

        return ['results' => $data];
    }

    public function actionGetvenue()
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $id = \Yii::$app->request->get('id');
    
    if (!$id) {
        return [
            'success' => false,
            'message' => 'ID non fornito'
        ];
    }
    
    // Verifica che sia un UUID valido
    if (!preg_match('/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/', $id)) {
        return [
            'success' => false,
            'message' => 'Formato ID non valido'
        ];
    }
    
    $venue = Xvenue::find()->where(['id' => $id])->one();
    
    if ($venue) {
        return [
            'success' => true,
            'data' => [
                'venue' => $venue->venue,
                'citta' => $venue->citta
            ]
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Venue non trovato'
        ];
    }
}
    public function actionGetstruttura($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Supponiamo tu abbia un modello chiamato Struttura
        $model = xStruttura::findOne($id);

        if ($model) {
            return [
                'success' => true,
                'data' => [
                    'nome' => $model->Struttura . '-' . $model->Citta, // Cambia con il campo che ti interessa
                    // eventualmente aggiungi altri campi, es:
                    // 'localita' => $model->localita,
                    // 'codice' => $model->codice_struttura
                ],'model'=> $model
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Struttura non trovata'
            ];
        }
    }


    public function actionGetAliquota()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $codice = Yii::$app->request->get('codiva');

        if (empty($codice)) {
            return ['success' => false, 'message' => 'Codice aliquota mancante'];
        }

        $record = (new \yii\db\Query())
            ->select(['Aliquota'])
            ->from('Aliquota')
            ->where(['Cd_Aliquota' => $codice])
            ->createCommand(Yii::$app->db5) // o 'db' se la connessione è standard
            ->queryOne();

        if ($record) {
            return ['success' => true, 'aliquota' => (float)$record['Aliquota']];
        } else {
            return ['success' => false, 'message' => 'Aliquota non trovata'];
        }
    }
    public function actionTornaindietro($tappa,$th_id)
    {
        Yii::$app->cache->set($tappa, 0);
        //$id= Yii::$app->request->get('th_id');
        // Redirect alla pagina precedente
        // return $this->redirect(Yii::$app->request->referrer ?: ['index']);
          return $this->redirect(['wizardrighe', 'th_id' => $th_id]);
        // return $this->redirect(['/xtravelhead/wizardrighe', 'th_id' => $thid]);
     
    }


    public function actionGetimportitot($th_id,$tipologia = null)
    {
        $query = (new \yii\db\Query())
            ->select(['SUM(totft) AS totft', 
            'SUM(fee) AS fee', 'SUM(totaleservizi) AS totaleservizi', 
            'SUM(tax) AS tax'])
            ->from('xtravelrow')
            ->leftJoin('ar', 'ar.cd_Ar = xtravelhead.cd_Ar')
            ->where(['th_id' => $th_id])
            ->andWhere(['not in', 'cd_Ar', 
                (new \yii\db\Query())
                    ->select('cd_ar')
                    ->from('ar')
                    ->where(['x_isacconto' => 1])
            ])
            ->andWhere(['ar.cd_arclasse12'=> $tipologia]);

        $command = $query->createCommand(Yii::$app->db5);
        return $command->queryOne();
}

 
/**
 * MODIFICHE AL TUO CONTROLLER PRINCIPALE
 * Sostituisci il tuo actionExportToExcel con questa versione asincrona
 */

/**
 * Export Excel Asincrono - versione adattata per XTravel
 */
public function actionExportToExcel($id,$tipoexp)
{
    $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . date('Y-m-d') . '.log');
    $username = Yii::$app->user->identity->username ?? 'guest';
    $userEmail = Yii::$app->user->identity->email ?? null;
    
    // Validate ID parameter
    if (empty($id)) {
        Yii::$app->session->setFlash('error', 'ID mancante per l\'esportazione');
        return $this->redirect(['index']);
    }
    
    if (empty($userEmail)) {
        Yii::$app->session->setFlash('error', 'Email utente non trovata per la notifica');
        return $this->redirect(['index']);
    }

    // Verifica che esistano dati da esportare
    $checkSql = "SELECT COUNT(*) FROM xtravelrow WHERE th_id = :id";
    $recordCount = Yii::$app->db5->createCommand($checkSql, [':id' => $id])->queryScalar();
    
    if ($recordCount == 0) {
        Yii::$app->session->setFlash('warning', 'Nessun dato trovato per l\'ID specificato: ' . $id);
        return $this->redirect(['index']);
    }

        // Genera ID univoco per il job
        //$jobId = uniqid('xtravel_' . $id . '_', true);


        // Controlla se esiste già un job attivo per questo utente e data_id
        $existingJob = Yii::$app->db->createCommand("
    SELECT TOP 1 job_id 
    FROM export_jobs 
    WHERE data_id = :data_id 
      AND username = :username 
      AND user_email = :email 
      AND progress < 100
    ORDER BY created_at DESC
", [
            ':data_id' => $id,
            ':username' => $username,
            ':email' => $userEmail
        ])->queryOne();

        if ($existingJob && isset($existingJob['job_id'])) {
            Yii::$app->session->setFlash('info', 'Hai già un esportazione in corso. Verrai reindirizzato al job esistente.');
            return $this->redirect(['export-status', 'jobId' => $existingJob['job_id'], 'tipoexp'=> $tipoexp]);
        }

        // Se non ci sono job attivi, procedi a crearne uno nuovo
        $jobId = uniqid('xtravel_' . $id . '_', true);



        $this->logOperation($logFile, "=== AVVIO EXPORT ASINCRONO XTRAVEL ===", [
        'id' => $id,
        'job_id' => $jobId,
        'user' => $username,
        'email' => $userEmail,
        'record_count' => $recordCount,
        'timestamp' => date('Y-m-d H:i:s')
    ]);

    // Salva informazioni job nel database
    $this->createExportJob($jobId, $id, $username, $userEmail, $recordCount);
    
    // Avvia processo in background
    $command = $this->buildBackgroundCommand($jobId, $id, $username, $userEmail, $tipoexp);
    
    if (PHP_OS_FAMILY === 'Windows') {
            // Windows
            pclose(popen("start /B " . $command, "r"));
           // exec($command);
    } else {
        // Linux/Unix
        exec($command . " > /dev/null 2>&1 &");
    }
    
    $this->logOperation($logFile, "Comando background eseguito", [
        'command' => $command,
        'job_id' => $jobId,
        'record_count' => $recordCount
    ]);

    Yii::$app->session->setFlash('success', 
        "Export XTravel avviato in background per {$recordCount} record. " .
        "Riceverai una email quando sarà completato. Job ID: {$jobId}"
    );



        return $this->redirect(['export-status', 'jobId' => $jobId, 'tipoexp'=> $tipoexp]);
}

/**
 * Crea record job nel database con info aggiuntive
 */
private function createExportJob($jobId, $dataId, $username, $userEmail, $recordCount = 0)
{
    $connection = Yii::$app->db;
    
    // Assicura che la tabella esista
    $this->ensureExportJobsTable();
    
    $connection->createCommand()->insert('export_jobs', [
        'job_id' => $jobId,
        'data_id' => $dataId,
        'username' => $username,
        'user_email' => $userEmail,
        'status' => 'queued',
        'progress' => 0,
        'message' => "In coda per export di {$recordCount} record XTravel",
        'records_count' => $recordCount,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ])->execute();
}

/**
 * METODI HELPER AGGIUNTIVI
 */

/**
 * Costruisce comando per background processing
 */
private function buildBackgroundCommand($jobId, $id, $username, $userEmail,$tipoexp)
{
    $yiiPath = Yii::getAlias('@app') . '/yii';
    $phpPath = 'C:\xampp\php\php.exe';
        // PHP_BINARY;


        $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . date('Y-m-d') . '.log');
        $this->logOperation($logFile, "buildBackgroundCommand(", [
            'yiiPath' =>  $yiiPath,
            'phpPath' =>  $phpPath,
            'jobId' => $jobId,
            'id' => $id,
            'username' => $username,
            'userEmail' => $userEmail,
            'tipoexp' => $tipoexp//,
            //'comando' => $command
        ]);



        return sprintf(
            // '"%s" "%s" background-export/process-excel "%s" "%s" "%s" "%s" "%s"',
            //         '"%s" "%s" background-export/process-excel "%s" "%s" "%s" "%s" "%s"', 
            '"%s" "%s" background-export/process-excel "%s" "%s" "%s" "%s" =%s',

            $phpPath,
        $yiiPath,
        $jobId,
        $id,
        $username,
        $userEmail,
            $tipoexp
    ); 


        

    

      

}

/**
 * Assicura che la tabella export_jobs esista con campi aggiuntivi
 */
private function ensureExportJobsTable()
{
    $connection = Yii::$app->db;
    
    $tableExists = $connection->createCommand(
            " SELECT 1 
    FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_NAME = 'export_jobs' "
    )->queryScalar();
    
    if (!$tableExists) {
        $connection->createCommand("
         CREATE TABLE export_jobs (
            id INT IDENTITY(1,1) PRIMARY KEY,
            job_id VARCHAR(255) NOT NULL UNIQUE,
            data_id VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            user_email VARCHAR(255) NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'queued' 
                CHECK (status IN ('queued', 'processing', 'completed', 'failed')),
            progress INT DEFAULT 0,
            message TEXT,
            file_path VARCHAR(500),
            file_size BIGINT,
            records_count INT DEFAULT 0,
            processing_time DECIMAL(10,3),
            error_message TEXT,
            export_type VARCHAR(50) DEFAULT 'xtravel',
            server_info NVARCHAR(MAX),
            created_at DATETIME2 DEFAULT GETDATE(),
            updated_at DATETIME2 DEFAULT GETDATE(),
            INDEX idx_job_id (job_id),
            INDEX idx_status (status),
            INDEX idx_export_type (export_type),
            INDEX idx_created_at (created_at),
            INDEX idx_username (username)
        );
        
        CREATE TRIGGER trg_update_export_jobs
        ON export_jobs
        AFTER UPDATE
        AS
        BEGIN
            SET NOCOUNT ON;
            UPDATE export_jobs
            SET updated_at = GETDATE()
            FROM export_jobs ej
            INNER JOIN inserted i ON ej.id = i.id;
        END
            ")->execute();
        
        // Inserisci info iniziali
        $connection->createCommand()->insert('export_jobs', [
            'job_id' => 'system_init',
            'data_id' => '0',
            'username' => 'system',
            'user_email' => 'system@localhost',
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Tabella export_jobs inizializzata',
            'export_type' => 'system',
            'server_info' => json_encode([
                'php_version' => PHP_VERSION,
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'os' => PHP_OS_FAMILY
            ]),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ])->execute();
    }
}

/**
 * Log delle operazioni
 */
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

/**
 * Recupera informazioni job
 */
private function getExportJob($jobId)
{
    return Yii::$app->db->createCommand(
        'SELECT * FROM export_jobs WHERE job_id = :jobId'
    )->bindValue(':jobId', $jobId)->queryOne();
}

/**
 * API per controllare status via AJAX
 */
public function actionCheckStatus($jobId)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $job = $this->getExportJob($jobId);
    
    if (!$job) {
        return ['status' => 'not_found'];
    }
    
    // Calcola tempo trascorso
    $createdTime = strtotime($job['created_at']);
    $currentTime = time();
    $elapsedMinutes = round(($currentTime - $createdTime) / 60, 1);
    
    // Stima tempo rimanente basata sui record
    $estimatedMinutes = 0;
    if ($job['status'] === 'processing' && $job['records_count'] > 0) {
        $recordsPerMinute = 1000; // Stima conservativa
        $estimatedMinutes = round($job['records_count'] / $recordsPerMinute, 1);
    }
    
    return [
        'status' => $job['status'],
        'progress' => (int)($job['progress'] ?? 0),
        'message' => $job['message'] ?? '',
        'records_count' => (int)($job['records_count'] ?? 0),
        'elapsed_minutes' => $elapsedMinutes,
        'estimated_minutes' => $estimatedMinutes,
        'download_url' => $job['status'] === 'completed' && !empty($job['file_path']) 
            ? \yii\helpers\Url::to(['download-export', 'jobId' => $jobId]) 
            : null,
        'file_size_mb' => $job['file_size'] ? round($job['file_size'] / 1024 / 1024, 2) : 0,
        'processing_time' => $job['processing_time'] ?? 0,
        'created_at' => $job['created_at'],
        'updated_at' => $job['updated_at'],
        'export_type' => $job['export_type'] ?? 'xtravel'
    ];
}

/**
 * Download file completato
 */
public function actionDownloadExport($jobId)
{
    $job = $this->getExportJob($jobId);
    
    if (!$job) {
        Yii::$app->session->setFlash('error', 'Job non trovato');
        return $this->redirect(['index']);
    }
    
    if ($job['status'] !== 'completed') {
        Yii::$app->session->setFlash('error', 'Export non ancora completato');
        return $this->redirect(['export-status', 'jobId' => $jobId]);
    }
    
    $filePath = $job['file_path'];
    
    if (!file_exists($filePath)) {
        Yii::$app->session->setFlash('error', 'File non trovato o già eliminato');
        return $this->redirect(['index']);
    }
    
    // Log download
    $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . date('Y-m-d') . '.log');
    $this->logOperation($logFile, "Download file export", [
        'job_id' => $jobId,
        'user' => Yii::$app->user->identity->username ?? 'guest',
        'file_size' => filesize($filePath),
        'records_count' => $job['records_count']
    ]);
    
    return Yii::$app->response->sendFile($filePath, basename($filePath), [
        'mimeType' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'inline' => false
    ]);
}

/**
 * Mostra status dell'export con informazioni dettagliate
 */
public function actionExportStatus($jobId)
{
    $job = $this->getExportJob($jobId);
    
    if (!$job) {
        Yii::$app->session->setFlash('error', 'Job non trovato: ' . $jobId);
        return $this->redirect(['index']);
    }
    
    // Recupera statistiche aggiuntive se disponibili
    $stats = $this->getExportStats($jobId);
    
    return $this->render('export-status', [
        'job' => $job,
        'jobId' => $jobId,
        'stats' => $stats
    ]);
}

/**
 * Recupera statistiche aggiuntive per l'export
 */
private function getExportStats($jobId)
{
    $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . date('Y-m-d') . '.log');
    
    $stats = [
        'avg_processing_time' => 0,
        'similar_exports_today' => 0,
        'user_exports_today' => 0,
        'system_load' => 'normale'
    ];
    
    try {
        // Media tempi processing ultimi 10 export completati
        $avgTime = Yii::$app->db->createCommand(
            'SELECT AVG(processing_time) FROM export_jobs WHERE status = "completed" AND export_type = "xtravel" ORDER BY id DESC LIMIT 10'
        )->queryScalar();
        
        $stats['avg_processing_time'] = round($avgTime, 2);
        
        // Export simili oggi
        $todayStart = date('Y-m-d 00:00:00');
        $similarCount = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM export_jobs WHERE export_type = "xtravel" AND created_at >= :date'
        )->bindValue(':date', $todayStart)->queryScalar();
        
        $stats['similar_exports_today'] = (int)$similarCount;
        
        // Export dell'utente oggi
        $username = Yii::$app->user->identity->username ?? 'guest';
        $userCount = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM export_jobs WHERE username = :user AND created_at >= :date'
        )->bindValues([':user' => $username, ':date' => $todayStart])->queryScalar();
        
        $stats['user_exports_today'] = (int)$userCount;
        
        // Determina carico sistema
        $processingCount = Yii::$app->db->createCommand(
            'SELECT COUNT(*) FROM export_jobs WHERE status = "processing"'
        )->queryScalar();
        
        if ($processingCount > 5) {
            $stats['system_load'] = 'alto';
        } elseif ($processingCount > 2) {
            $stats['system_load'] = 'medio';
        }
        
    } catch (\Exception $e) {
        // Log errore ma continua
        error_log("Errore nel recupero statistiche export: " . $e->getMessage());
    }
    
    return $stats;
}

/**
 * Cancella job manualmente (per admin)
 */
public function actionCancelJob($jobId)
{
    // Verifica permessi admin
    if (!Yii::$app->user->can('admin')) {
        throw new \yii\web\ForbiddenHttpException('Accesso negato');
    }
    
    $job = $this->getExportJob($jobId);
    
    if (!$job) {
        Yii::$app->session->setFlash('error', 'Job non trovato');
        return $this->redirect(['index']);
    }
    
    try {
        // Aggiorna status a failed
        Yii::$app->db->createCommand()->update('export_jobs', [
            'status' => 'failed',
            'progress' => 0,
            'message' => 'Job cancellato manualmente dall\'amministratore',
            'error_message' => 'Cancellazione manuale - ' . date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ], ['job_id' => $jobId])->execute();
        
        // Elimina file se esiste
        if (!empty($job['file_path']) && file_exists($job['file_path'])) {
            unlink($job['file_path']);
        }
        
        // Log operazione
        $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . date('Y-m-d') . '.log');
        $this->logOperation($logFile, "Job cancellato manualmente", [
            'job_id' => $jobId,
            'admin_user' => Yii::$app->user->identity->username,
            'original_status' => $job['status']
        ]);
        
        Yii::$app->session->setFlash('success', "Job {$jobId} cancellato con successo");
        
    } catch (\Exception $e) {
        Yii::$app->session->setFlash('error', 'Errore nella cancellazione: ' . $e->getMessage());
    }
    
    return $this->redirect(['export-status', 'jobId' => $jobId]);
}

/**
 * Lista job per admin
 */
public function actionListJobs($status = null, $user = null)
{
    // Verifica permessi admin
    if (!Yii::$app->user->can('admin')) {
        throw new \yii\web\ForbiddenHttpException('Accesso negato');
    }
    
    $query = Yii::$app->db->createCommand();
    $sql = 'SELECT * FROM export_jobs WHERE 1=1';
    $params = [];
    
    if ($status) {
        $sql .= ' AND status = :status';
        $params[':status'] = $status;
    }
    
    if ($user) {
        $sql .= ' AND username LIKE :user';
        $params[':user'] = '%' . $user . '%';
    }
    
    $sql .= ' ORDER BY created_at DESC LIMIT 100';
    
    $jobs = $query->setSql($sql)->bindValues($params)->queryAll();
    
    return $this->render('list-jobs', [
        'jobs' => $jobs,
        'currentStatus' => $status,
        'currentUser' => $user
    ]);
}

/**
 * VISTA AGGIORNATA per export-status.php
 * (Da aggiungere ai metodi helper)
 */

/**
 * Genera statistiche dashboard per admin
 */
public function actionExportDashboard()
{
    // Verifica permessi admin
    if (!Yii::$app->user->can('admin')) {
        throw new \yii\web\ForbiddenHttpException('Accesso negato');
    }
    
    $stats = [
        'today' => [],
        'week' => [],
        'month' => [],
        'system' => []
    ];
    
    // Statistiche oggi
    $todayStart = date('Y-m-d 00:00:00');
    $stats['today'] = Yii::$app->db->createCommand("
        SELECT 
            status,
            COUNT(*) as count,
            AVG(processing_time) as avg_time,
            SUM(records_count) as total_records,
            SUM(file_size) as total_size
        FROM export_jobs 
        WHERE created_at >= :date 
        GROUP BY status
    ")->bindValue(':date', $todayStart)->queryAll();
    
    // Top utenti settimana
    $weekStart = date('Y-m-d 00:00:00', strtotime('-7 days'));
    $stats['top_users'] = Yii::$app->db->createCommand("
        SELECT 
            username,
            COUNT(*) as exports,
            SUM(records_count) as total_records,
            AVG(processing_time) as avg_time
        FROM export_jobs 
        WHERE created_at >= :date 
        GROUP BY username 
        ORDER BY exports DESC 
        LIMIT 10
    ")->bindValue(':date', $weekStart)->queryAll();
    
    // Job più lenti
    $stats['slow_jobs'] = Yii::$app->db->createCommand("
        SELECT 
            job_id,
            username,
            records_count,
            processing_time,
            file_size,
            created_at
        FROM export_jobs 
        WHERE status = 'completed' 
        AND processing_time IS NOT NULL 
        ORDER BY processing_time DESC 
        LIMIT 10
    ")->queryAll();
    
    // Sistema
    $stats['system'] = [
        'processing_jobs' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM export_jobs WHERE status = "processing"')->queryScalar(),
        'queued_jobs' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM export_jobs WHERE status = "queued"')->queryScalar(),
        'failed_today' => Yii::$app->db->createCommand('SELECT COUNT(*) FROM export_jobs WHERE status = "failed" AND created_at >= :date')->bindValue(':date', $todayStart)->queryScalar(),
        'disk_usage' => $this->calculateDiskUsage(),
        'avg_processing_time' => Yii::$app->db->createCommand('SELECT AVG(processing_time) FROM export_jobs WHERE status = "completed" AND created_at >= :date')->bindValue(':date', $weekStart)->queryScalar()
    ];
    
    return $this->render('export-dashboard', [
        'stats' => $stats
    ]);
}

/**
 * Calcola utilizzo disco
 */
private function calculateDiskUsage()
{
    $exportDir = Yii::getAlias('@app/web/xls_REP');
    $totalSize = 0;
    $fileCount = 0;
    
    try {
        if (is_dir($exportDir)) {
            $files = glob($exportDir . '/*.{xlsx,xls}', GLOB_BRACE);
            foreach ($files as $file) {
                if (is_file($file)) {
                    $totalSize += filesize($file);
                    $fileCount++;
                }
            }
        }
    } catch (\Exception $e) {
        error_log("Errore calcolo disk usage: " . $e->getMessage());
    }
    
    return [
        'total_size_mb' => round($totalSize / 1024 / 1024, 2),
        'file_count' => $fileCount,
        'avg_file_size_mb' => $fileCount > 0 ? round(($totalSize / 1024 / 1024) / $fileCount, 2) : 0
    ];
}
    /*FILES*/
    public function actionUploadFile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $uploadedFile = UploadedFile::getInstanceByName('upload_file');

            if (!$uploadedFile) {
                return [
                    'success' => false,
                    'message' => 'Nessun file selezionato'
                ];
            }

            // Validazioni base
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($uploadedFile->size > $maxSize) {
                return [
                    'success' => false,
                    'message' => 'Il file è troppo grande (massimo 10MB)'
                ];
            }

            // Estensioni permesse (personalizza secondo le tue esigenze)
            $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'txt'];
            $extension = strtolower($uploadedFile->extension);

            if (!in_array($extension, $allowedExtensions)) {
                return [
                    'success' => false,
                    'message' => 'Formato file non supportato'
                ];
            }

            // Crea il modello AllFiles
            $fileModel = new AllFiles();
            $fileModel->id_padre = Yii::$app->request->post('id_padre');
            $fileModel->entita = Yii::$app->request->post('entita', 'Xtravelrow');
            $fileModel->nomefile = $uploadedFile->name;
            $fileModel->estensione = $extension;
            $userId = \Yii::$app->user->id;
            $user = \app\models\User::findOne($userId);
     
  



           if ($user->level >= 80){
                $fileModel->origine = 'U'; // S = Sistema/Utente
            }else {
            $fileModel->origine = 'S'; // S = Sistema/Utente
           }
            $fileModel->nota = 'Caricato il ' . date('d/m/Y H:i:s');

            // Leggi il contenuto del file
            $fileContent = file_get_contents($uploadedFile->tempName);
            $fileModel->f_content = $fileContent;

            if ($fileModel->save()) {
                // Opzionale: salva anche fisicamente il file
                $uploadPath = Yii::getAlias('@webroot/uploads/');
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fileName = $fileModel->id . '_' . str_replace(' ', '_', $uploadedFile->name);
                $uploadedFile->saveAs($uploadPath . $fileName);

                return [
                    'success' => true,
                    'message' => 'File caricato con successo',
                    'file' => [
                        'id' => $fileModel->id,
                        'nomefile' => $fileModel->nomefile,
                        'estensione' => $fileModel->estensione,
                        'origine' => $fileModel->origine
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Errore durante il salvataggio: ' . implode(', ', $fileModel->getFirstErrors())
                ];
            }
        } catch (Exception $e) {
            Yii::error('Errore upload file: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore interno del server'
            ];
        }
    }

    /**
     * Action per eliminare un file
     */
    public function actionDeleteFile()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $request = Yii::$app->request;
            $data = json_decode($request->getRawBody(), true);
            $fileId = $data['id'] ?? null;

            if (!$fileId) {
                return [
                    'success' => false,
                    'message' => 'ID file non specificato'
                ];
            }

            $fileModel = AllFiles::findOne($fileId);

            if (!$fileModel) {
                return [
                    'success' => false,
                    'message' => 'File non trovato'
                ];
            }

            // Verifica permessi (opzionale)
            // Qui puoi aggiungere controlli per verificare se l'utente può eliminare questo file

            // Elimina il file fisico se esiste
            $uploadPath = Yii::getAlias('@webroot/uploads/');
            $fileName = $fileModel->id . '_' . str_replace(' ', '_', $fileModel->nomefile);
            $filePath = $uploadPath . $fileName;

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Elimina il record dal database
            if ($fileModel->delete()) {
                return [
                    'success' => true,
                    'message' => 'File eliminato con successo'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Errore durante l\'eliminazione del file'
                ];
            }
        } catch (Exception $e) {
            Yii::error('Errore eliminazione file: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Errore interno del server'
            ];
        }
    }

    /**
     * Action per scaricare/visualizzare un file (modifica della tua action esistente)
     */
    public function actionGenfile($id, $file = null)
    {
        $fileModel = AllFiles::findOne($id);

        if (!$fileModel) {
            throw new \yii\web\NotFoundHttpException('File non trovato');
        }

        // Se il file è salvato nel database come BLOB
        if ($fileModel->f_content) {
            $response = Yii::$app->response;
            $response->headers->set('Content-Type', $this->getMimeType($fileModel->estensione));
            $response->headers->set('Content-Disposition', 'inline; filename="' . $fileModel->nomefile . '"');
            $response->content = $fileModel->f_content;
            return $response;
        }

        // Se il file è salvato fisicamente
        $uploadPath = Yii::getAlias('@webroot/uploads/');
        $fileName = $fileModel->id . '_' . str_replace(' ', '_', $fileModel->nomefile);
        $filePath = $uploadPath . $fileName;

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath, $fileModel->nomefile);
        }

        throw new \yii\web\NotFoundHttpException('File fisico non trovato');
    }

    /**
     * Helper per determinare il MIME type
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'txt' => 'text/plain',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }









    /*getuser */
    public function getuser()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/index']);
        }
        $usrid = Yii::$app->user->Id;
        if (null !== $usrid) {
            $ris = (new \yii\db\Query())
                ->select(['level', 'cd_cli', 'moduli'])
                ->from('user')
                ->where(['id' => $usrid])
                ->one();
            $nmod = (str_replace('app\controllers', '', str_replace('Controller', '', __CLASS__)));
            $nmod = (str_replace('\\', '', $nmod));
            $nmod = strtoupper($nmod);
            $mn   = (new \yii\db\Query())
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
                    if (strtoupper($value) == strtoupper(($mn['voce'] ?? 
                    'default value'))) {
                        $go = 1;
                    }
                    if ('DOC_HEAD' == $nmod || strtoupper($value) == 'ELENCO') {
                        $go = 1;
                    }
                    if('XTRAVELHEAD'== $nmod){
                        $go=1;
                    }
                }
            }
        } else {
            $go = 1;
        }
        if (Yii::$app->user->isGuest || null == $ris['level']) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.</strong></p>\n"
                . '<p><a href="' . Yii::$app->homeUrl . '">Torna alla home</a></p>';
            exit($messaggio);
        }
        if (0 == $go) {
            $messaggio =
                "<h1>Attenzione</h1>\n\n"
                . "<p><strong>NON SEI AUTORIZZATO AD ACCEDERE!!!.
                </strong></p>\n". $usrid. $nmod
                . '<p><a href="' . Yii::$app->homeUrl . '">Torna alla home</a></p>';
            exit($messaggio );
        }
    }




    public function actionEliminatappa()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id_tappa = Yii::$app->request->post('id_tappa');

        if (!$id_tappa) {
            return ['success' => false, 'error' => 'ID tappa mancante'];
        }

        // Controllo evasione
        $row = Yii::$app->db5->createCommand("
            SELECT evaso 
            FROM x_tappe 
            WHERE id_tappa = :id
        ")->bindValue(':id', $id_tappa)->queryOne();

        if (!$row) {
            return ['success' => false, 'error' => 'Tappa non trovata'];
        }

        if ($row['evaso']) {
            return ['success' => false, 'error' => 'La tappa è già stata evasa e non può essere eliminata'];
        }

        // Eliminazione
        Yii::$app->db5->createCommand("
            DELETE FROM x_tappe WHERE id_tappa = :id
        ")->bindValue(':id', $id_tappa)->execute();

        return ['success' => true];
    }

    public function actionEliminanominativo()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id_nominativo = Yii::$app->request->post('id_nominativo');

        if (!$id_nominativo) {
            return ['success' => false, 'error' => 'ID nominativo mancante'];
        }

        // Controllo evasione
        $row = Yii::$app->db5->createCommand("
            SELECT evaso 
            FROM x_roomlist 
            WHERE id_guest = :id
        ")->bindValue(':id', $id_nominativo)->queryOne();

        if (!$row) {
            return ['success' => false, 'error' => 'Nominativo non trovato'];
        }

        if ($row['evaso']) {
            return ['success' => false, 'error' => 'Il nominativo è già stato evaso e non può essere eliminato'];
        }

        // Eliminazione
        Yii::$app->db5->createCommand("
            DELETE FROM x_roomlist WHERE id_guest = :id
        ")->bindValue(':id', $id_nominativo)->execute();

        return ['success' => true];
    }



    public function actionWizardrighesalvaospiti()
    {
        if (Yii::$app->request->isPost) {
            $th_id = Yii::$app->request->post('th_id');
            $tappeData = json_decode(Yii::$app->request->post('tappe'), true);
            $roomlistData = json_decode(Yii::$app->request->post('roomlist'), true);

            // Array per debugging
            $debugInfo = [];
            $debugInfo['num_tappe'] = count($tappeData);
            $debugInfo['num_roomlist'] = count($roomlistData);
            $debugInfo['tappe'] = array_column($tappeData, 'citta');
            $debugInfo['first_guests'] = array_slice(array_column($roomlistData, 'guest'), 0, 5); // primi 5 guest

            // Contatori per statistiche
            $insertedRows = 0;
            $tappeCounts = [];

            // Transazione per garantire l'integrità dei dati
            $transaction = Yii::$app->db->beginTransaction();

            try {
                // Opzionale: elimina le righe esistenti per evitare duplicati
                //   $deletedRows = XTravelrow::deleteAll(['th_id' => $th_id]);
                //   $debugInfo['deleted_rows'] = $deletedRows;

                // Per ogni tappa...
                foreach ($tappeData as $index => $tappa) {
                    $id_tappa = $tappa['id_tappa'];
                    // $data_tappa = $tappa['data'];
                    $citta_tappa = $tappa['citta'];

                    // Converti la data nel formato corretto per SQL Server
                    // Se la data è già in formato SQL Server (yyyy-mm-dd), usiamola direttamente
                    // altrimenti tentiamo di convertirla
                    $data_tappa_raw = $tappa['data'];
                    $dateTime = new \DateTime($data_tappa_raw);

                    // Prova diversi formati
                    $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
                    $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
                    $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito

                    // Yii::info("Data originale: $data_tappa_raw");
                    // Yii::info("Formato ISO: $formato1");
                    //Yii::info("Formato Europeo: $formato2");
                    // Yii::info("Formato Invertito: $formato3");

                    // Usa quello che funziona
                    $data_tappa = $formato3; // o qualsiasi altro formato che hai verificato funzionare

                    $tappaCounts = ['citta' => $citta_tappa, 'inseriti' => 0, 'errori' => 0];

                    // Per ogni guest nella roomlist...
                    foreach ($roomlistData as $guest) {
                        // Creo un nuovo record nella tabella xtravelrow
                        $xTravelRow = new XTravelrow();
                        $xTravelRow->th_id = $th_id;
                        $xTravelRow->guest = $guest['guest'];
                        $xTravelRow->cd_Ar = $guest['cd_Ar'];
                        $xTravelRow->citta = $citta_tappa;
                        $xTravelRow->check_in = $data_tappa;
                        $xTravelRow->ruolo = $guest['ruolo'];
                        $xTravelRow->party = $guest['party'];
                        $xTravelRow->sottocommessa = $guest['commessa'];
                        $xTravelRow->id_tappa = $id_tappa;
                        // $xTravelRow->cd_cf_ft = $guest['cd_cf_ft'];
                        $xTravelRow->id_nominativo = $guest['id_guest'];
                        $xTravelRow->duseri = Yii::$app->user->id;
                        // Salvo il record
                        if ($xTravelRow->save()) {
                            $insertedRows++;
                            $tappaCounts['inseriti']++;
                        } else {
                            $tappaCounts['errori']++;
                            $tappaCounts['ultimo_errore'] = json_encode($xTravelRow->errors);
                        }
                    }

                    // Aggiorno la tappa come evasa
                    $xTappe = XTappe::findOne(['id_tappa' => $id_tappa]);
                    if ($xTappe) {
                        $xTappe->evaso = 1;
                        $xTappe->save();
                    } else {
                        $tappaCounts['errore_tappa'] = "Tappa non trovata con ID: " . $id_tappa;
                    }

                    $tappeCounts[] = $tappaCounts;
                }

                // Aggiorno tutti i guest nella tabella x_roomlist come evasi
                $updatedGuests = 0;
                $guestErrors = 0;

                foreach ($roomlistData as $guest) {
                    $guestId = $guest['id_guest'];
                    $xRoomlist = XRoomlist::findOne(['id_guest' => $guestId,]);
                    if ($xRoomlist) {
                        $xRoomlist->evaso = 1;
                        $xRoomlist->th_id = $th_id;
                        if ($xRoomlist->save()) {
                            $updatedGuests++;
                        } else {
                            $guestErrors++;
                        }
                    }
                }

                // Aggiungo informazioni finali di debug
                $debugInfo['inserted_rows'] = $insertedRows;
                $debugInfo['updated_guests'] = $updatedGuests;
                $debugInfo['guest_errors'] = $guestErrors;
                $debugInfo['tappe_counts'] = $tappeCounts;
                $debugInfo['righe'] = json_encode(
                    $xTravelRow
                );

                // Commit della transazione
                $transaction->commit();

                // Salvo info di debug in sessione per visualizzarle nella pagina successiva
                Yii::$app->session->set('debug_wizard', $debugInfo);

                $message = 'Operazione completata. Inserite ' . $insertedRows . ' righe su ' .
                    count($tappeData) . ' tappe e ' . count($roomlistData) . ' ospiti.';

                Yii::$app->session->setFlash('success', $message);




                $this->aggiornateste($th_id);





                // Redirect alla pagina appropriata
                return $this->redirect(['xtravelhead/wizardrighe', 'th_id' => $th_id]);
            } catch (\Exception $e) {
                // Rollback in caso di errore
                $transaction->rollBack();

                // Salvo info errore in sessione
                $debugInfo['error'] = $e->getMessage();
                $debugInfo['error_trace'] = $e->getTraceAsString();
                Yii::$app->session->set('debug_wizard', $debugInfo);

                Yii::$app->session->setFlash('error', 'Si è verificato un errore: ' . $e->getMessage());

                // Redirect alla pagina precedente o alla pagina di errore
                return $this->redirect(['xtravelhead/wizardrighe', 'th_id' => $th_id]);
            }
        }

        // Se non è una richiesta POST, redirect alla home o alla pagina appropriata
        return $this->redirect(['xtravelhead/index']);
    }




    public function actionTool()
    {
        $data = Yii::$app->session->get('tool_data');

        if (!$data) {
            throw new \yii\web\BadRequestHttpException('Dati non trovati in sessione');
        }

        return $this->render('_tool', [
            'mdettaglio' => $data['mdettaglio'],
            'mth_id' => $data['mth_id'],
            'mpivot' => $data['mpivot'],
            'mlabels' => $data['mlabels'],
            'mseries' => $data['mseries'],
            'mtappe' => $data['mtappe'],
            'mroomlist' => $data['mroomlist'],
            'manalisitappe' => $data['manalisitappe'],
            'mlistaart' => $data['mlistaart'],
            'listaart' => $data['mlistaart2'],
            'mlistatappetool' => $data['mlistatappetool'],
        ]);
    }





}