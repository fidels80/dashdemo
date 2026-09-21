<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
class VtigerController extends Controller
{
    public function actionSearch()
    {
        // Recupera i parametri di ricerca dalla richiesta GET
        $dayFrom = Yii::$app->request->get('dayFrom');
        $dayTo = Yii::$app->request->get('dayTo');
        $utente = Yii::$app->request->get('utente');
        $gruppi=Yii::$app->request->get('groupusers');


        // Costruisci i parametri per la query
        $params = [];
        $whereClauses = [];

        if (!empty($dayFrom)) {
            $dateFrom = new \DateTime($dayFrom);
            $whereClauses[] = "DATE(orainizioevento) >= '" . $dateFrom->format('Y-m-d') . "'";
        }

        if (!empty($dayTo)) {
            $dateTo = new \DateTime($dayTo);
            $whereClauses[] = "DATE(orainizioevento) <= '" . $dateTo->format('Y-m-d') . "'";
        }
        
        if($gruppi !== null and strlen($gruppi)<>0 and  $utente == null){
//$utente=null;
$sql="
SELECT g.groupid, g.groupname ,concat(u.first_name,' ',u.last_name) AS user_name
FROM vtiger_groups g
JOIN vtiger_users2group ug ON g.groupid = ug.groupid
JOIN vtiger_users u ON ug.userid = u.id
WHERE u.status = 'Active'
and g.groupname='$gruppi';
";
 $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql);

$idgruppo = $command->queryAll();

$sql="
SELECT count(g.groupid) as conta, g.groupname 
FROM vtiger_groups g
JOIN vtiger_users2group ug ON g.groupid = ug.groupid
JOIN vtiger_users u ON ug.userid = u.id
WHERE u.status = 'Active'
and g.groupname='$gruppi'
GROUP BY g.groupname ;
";
 //$connection = Yii::$app->db6;
        $command = $connection->createCommand($sql);
$countgruppo = $command->queryAll();
// Usa array_column per ottenere la colonna 'conta'
$contag = array_column($countgruppo, 'conta');
// Assicurati che l'array non sia vuoto prima di accedere al primo elemento
$contaGruppo = !empty($contag) ? $contag[0] : 1;

 // Estrai i nomi degli utenti dal risultato della query
    $usernames = array_column($idgruppo, 'user_name');
    
    foreach ($usernames as $username) {
        if (stripos($username, 'marco') !== false) {
            $usernames[] = 'Marco Cardinale'; // Aggiunge Marco Cardinale
        }
        if (stripos($username, 'simone') !== false) {
            $usernames[] = 'Simone Ruffa'; // Aggiunge Simone Ruffa
        }
                if (stripos($username, 'francesco') !== false) {
            $usernames[] = 'benincasa'; // Aggiunge benincasa
            $usernames[]='Francesco Benincasa';
        }
    }
    
    // Crea la clausola WHERE dinamica con gli utenti
    if (!empty($usernames)) {
        // Escapa correttamente i valori per usarli nella clausola SQL
        $usernamesList = implode("', '", array_map('addslashes', $usernames));
        $whereClauses[] = "utente_destinatario IN ('$usernamesList')";
    }
 



        }
        
        
        
        
        
        
        
        
        if ($utente !== null ||$gruppi == null        )
        
        {
            // Gestione dei pattern per l'utente
            $utentePatterns = [];
            $utentePatterns[] = "utente_destinatario LIKE :utente";
            $params[':utente'] = "%$utente%";

            // Gestione delle varianti degli utenti
            if (stripos($utente, 'marco') !== false) {
                $utentePatterns[] = "utente_destinatario LIKE '%marco%'";
            }
            if (stripos($utente, 'simone') !== false) {
                $utentePatterns[] = "utente_destinatario LIKE '%simone%'";
            }

            $whereClauses[] = '(' . implode(' OR ', $utentePatterns) . ')';
        }










        $where = '';
        if (!empty($whereClauses)) {
            $where = 'WHERE ' . implode(' AND ', $whereClauses);
        }

        $sql = "
            SELECT * FROM 
            xestrazione
            $where
              order by DATE(orainizioevento) ASC
        ";
        $xtsql=$sql;
       // Yii::debug("SQL Query: $sql");
       // Yii::debug("Params: " . json_encode($params));

        // Esegui la query
       $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            
        ]);







        $ticketstat="
          SELECT MONTH(xestrazione.orainizioevento) AS mese,week(xestrazione.orainizioevento) AS   settimana,
			 WEEK(xestrazione.orainizioevento,5) - WEEK(DATE_SUB(xestrazione.orainizioevento, 
INTERVAL DAYOFMONTH(xestrazione.orainizioevento) - 1 DAY),5) + 1
AS settimana_mese		 ,
xestrazione.*,tcustom.*,vt.*,vp.*  
from xestrazione
LEFT JOIN vt.vtiger_ticketcf AS tcustom ON tcustom.ticketid=xestrazione.tid
LEFT JOIN  vt.vtiger_troubletickets vt ON vt.ticketid=xestrazione.tid
LEFT JOIN vt.vtiger_products AS vp ON vp.productid=vt.product_id
             
         $where AND tipo IN ('T','TA','TP')
              order by DATE(orainizioevento) ASC";
      $command = $connection->createCommand($ticketstat, $params);
        $ticketstat_res = $command->queryAll();
        

        // Conta i valori distinti della colonna custom1
        $custom1Count = [];
        foreach ($results as $result) {
            $value = $result['Custom1'];
            if (!isset($custom1Count[$value])) {
                $custom1Count[$value] = 0;
            }
            $custom1Count[$value]++;
        }

        // Crea le etichette e le serie per il grafico a torta
        $labels = array_keys($custom1Count);
        $series = array_values($custom1Count);

        $custom2Count = [];
        foreach ($results as $result) {
            $value =$result['custom2'];
            //var_dump($result);
             if (!isset($custom2Count[$value])) {
                $custom2Count[$value] = 0;
            }
            $custom2Count[$value]++;
        }


        // Crea le etichette e le serie per il grafico a torta
        $labels2 = array_keys($custom2Count);
        $series2 = array_values($custom2Count);

        $codicesoggettoCount = [];
        foreach ($results as $result) {
            $value = $result['soggetto'];
            //var_dump($result);
            if (!isset($codicesoggetto[$value])) {
                $codicesoggetto[$value] = 0;
            }
            $codicesoggetto[$value]++;
        }


        // Crea le etichette e le serie per il grafico a torta
        $labels3 = isset($codicesoggetto) ? array_keys($codicesoggetto):'nessuno dato disponibile';
        $series3 = isset($codicesoggetto) ?  array_values($codicesoggetto) :0;




        $tree = [];
if (isset($codicesoggetto)) {
    foreach ($codicesoggetto as $key => $value) {
        $tree[] = ['x' => $key, 'y' => $value];
    }
} else {
    $tree = 'nessuno dato disponibile';
}

        $TipotoCount = [];
        foreach ($results as $result) {
            $value = $result['tipo'];
            //var_dump($result);
            if (!isset($tipo[$value])) {
                $tipo[$value] = 0;
            }
            $tipo[$value]++;
        }


        // Crea le etichette e le serie per il grafico a torta
        $labels4 = isset($tipo) ? array_keys($tipo) : 'nessuno dato disponibile';
        $series4 = isset($tipo) ?  array_values($tipo) : 0;




        $sql = "
            SELECT DATE(orainizioevento) AS giorno, 
                   round(SUM(oredelta),2) AS totale_lavorato
            FROM xestrazione
            $where
            GROUP BY DATE(orainizioevento)
            ORDER BY DATE(orainizioevento)
        ";

        // Esegui la query
     //   $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, $params);
        $resultsd = $command->queryAll();

        // Preparazione dei dati per il grafico
        $cdCfs = [];
        $totaleLavorato = [];
        foreach ($resultsd as $result) {
            $cdCfs[] = $result['giorno']; // Giorni per l'asse X
            $totaleLavorato[] = $result['totale_lavorato']; // Somma ore lavorate
        }

        // Dati per il grafico
         if($gruppi!==null){
        $totaleQuotidiano = array_fill(0,
                count($cdCfs),
                8* (isset($contaGruppo) ? $contaGruppo: 1)
            ); // 8 ore quotidiane

        }else{
 $totaleQuotidiano = array_fill(0,
                count($cdCfs),
                8
            ); // 8 ore 


        }
        





        $xTipologiatoCount = [];
        foreach ($results as $result) {
            $value = $result['xtipologia'];
            //var_dump($result);
            if (!isset($xtipologia[$value])) {
                $xtipologia[$value] = 0;
            }
            $xtipologia[$value]++;
        }


        // Crea le etichette e le serie per il grafico a torta
        $labels5 = isset($xtipologia) ? array_keys($xtipologia) : 'nessuno dato disponibile';
        $series5 = isset($xtipologia) ?  array_values($xtipologia) : 0;










        $sql = "
            SELECT tipo, DATE(orainizioevento) AS day
            FROM xestrazione
            $where
             order by DATE(orainizioevento) ASC
        ";

        // Esegui la query
       // $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, $params);
        $results = $command->queryAll();

        // Organizza i dati per la visualizzazione
        $data = [];
        foreach ($results as $row) {
            $data[$row['tipo']][$row['day']][] = $row['tipo'];
        }

        // Prepara la serie per il grafico
        $series7 = [];
        foreach ($data as $type => $dates) {
            $series7[] = [
                'name' => $type,
                'data' => array_map(function ($date) use ($dates) {
                    return [

                        $date,
                        count($dates[$date])
                    ];
                }, array_keys($dates)),
            ];
        };
        // Prepara i dati delle date per l'asse x
        $categories = array_keys(array_merge(...array_values($data)));


        if (!empty($where)) {
            $where = $where. '  AND  ';
        }else{

            $where=" WHERE 1=1 and";
        }

        $sql = "
SELECT * FROM xestrazione  $where   
ltrim(Rtrim(STATUS)) NOT IN('Closed','archived','delivered','Completed') AND tipo='T'  ORDER BY dataevento ASC LIMIT 5";
        $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql,$params);
        $resultstk = $command->queryAll();


$sql= "
 SELECT * FROM xestrazione  $where   oggetto not like '%ferie%'
 ORDER BY oredelta DESC LIMIT 5;

";
       // $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, $params);
        $resultBIGTK = $command->queryAll();


        // Crea il provider dei dati
  

        // Ottieni gli utenti per i filtri (modifica questa query se necessario)
        $users = $connection->
        //createCommand("SELECT DISTINCT utente_destinatario 
        //FROM xestrazione")
        createcommand("SELECT CONCAT(first_name,' ',last_name)AS utente_destinatario 
        FROM vt.vtiger_users")
        ->queryColumn();

    $groupusers = $connection->createCommand("SELECT DISTINCT groupname FROM vtiger_groups")
            ->queryColumn();



        return $this->render('search', [
            'dataProvider' => $dataProvider,
            'users' => $users,
            'dayFrom' => $dayFrom,
            'dayTo' => $dayTo,
            'utente' => $utente,
            'q' => $sql,
            'custom1Count' => $custom1Count,
            'labels' => $labels,
            'series' => $series,
            'custom2Count' => $custom2Count,
            'labels2' => $labels2,
            'series2' => $series2,
            'labels3' => $labels3,
            'series3' => $series3,
            'cdCfs' => $cdCfs,
            'totaleLavorato' => $totaleLavorato,
            'totaleQuotidiano' => $totaleQuotidiano,
            'TipotoCount'=> $TipotoCount,
            'labels4' => $labels4,
            'series4' => $series4,
            'series7' => $series7,
            'categories' => $categories,
            'xTipologiatoCount'=> $xTipologiatoCount,
            'labels5' => $labels5,
            'series5' => $series5,
            'resultstk' => $resultstk,
            'resultBIGTK'=>$resultBIGTK ,
            'tree'=>$tree,
            'groupusers'=>$groupusers,
            'idgruppo'=>$idgruppo??'' ,
            'countgruppo'=>$countgruppo??0,
            'xtsql'=>$xtsql,
            'ticketstat_res'=> $ticketstat_res 
            
        ]);
    }


 public function actionProgetti(){

$dayFrom = Yii::$app->request->get('dayFrom');
        $dayTo = Yii::$app->request->get('dayTo');
        $listap = Yii::$app->request->get('listap');
        $listac= Yii::$app->request->get('listac');
        $utente = Yii::$app->request->get('utente');
 $params = [];
        $whereClauses = [];

        if (!empty($dayFrom)) {
            $dateFrom = new \DateTime($dayFrom);
            $whereClauses[] = "DATE(orainizioevento) >= '" . $dateFrom->format('Y-m-d') . "'";
            $dtstart = "DATE(orainizioevento) >= '" . $dateFrom->format('Y-m-d') . "'";
        }

        if (!empty($dayTo)) {
            $dateTo = new \DateTime($dayTo);
            $whereClauses[] = "DATE(orainizioevento) <= '" . $dateTo->format('Y-m-d') . "'";
            $dtend = "DATE(orainizioevento) z= '" . $dateFrom->format('Y-m-d') . "'";
        }
        if ($listac !== null
            and !empty($listac)
        ) {
            // Gestione dei pattern per ciente
            $listacPatterns = [];
            $listacPatterns[] = "soggetto LIKE :listac";
            $params[':listac'] = "%$listac%";

            $whereClauses[] = '(' . implode(' OR ', $listacPatterns) . ')';
        }

        if ($listap !== null and !empty($listap)
        ) {
            if (is_array($listap)) {
                // Se listap è un array, costruisci una query con IN
                $placeholders = [];
                foreach ($listap as $index => $value) {
                    $paramKey = ":listap_$index";
                    $placeholders[] = $paramKey;
                    $params[$paramKey] = $value;
                }
                $whereClauses[] = 'codiceprogetto IN (' . implode(', ', $placeholders) . ')';
            } else {
                // Se listap è una stringa (fallback per compatibilità), usa il vecchio approccio
                $listapPatterns = [];
                $listapPatterns[] = "codiceprogetto LIKE :listap";
                $params[':listap'] = "$listap";
                $whereClauses[] = '(' . implode(' OR ', $listapPatterns) . ')';
            }
        }


        if ($utente !== null and !empty($utente)) {
            // Gestione dei pattern per l'utente
            $utentePatterns = [];
            $utentePatterns[] = "utente_destinatario LIKE :utente";
            $params[':utente'] = "%$utente%";

            // Gestione delle varianti degli utenti
            if (stripos($utente, 'marco') !== false) {
                $utentePatterns[] = "utente_destinatario LIKE '%marco%'";
            }
            if (stripos($utente, 'simone') !== false) {
                $utentePatterns[] = "utente_destinatario LIKE '%simone%'";
            }

            $whereClauses[] = '(' . implode(' OR ', $utentePatterns) . ')';
        }

        $where = '';
        if (!empty($whereClauses)) {
            $where =   implode(' AND ', $whereClauses);
        }
        else {$where=' 1=1 ';
        }





   


 $sql = "
            SELECT * FROM 
            x_vistaprog 
            WHERE 
           $where
              order by DATE(orainizioevento) ASC
        ";


 
     //   Yii::debug("SQL Query: $sql");
     //   Yii::debug("Params: " . json_encode($params));

        // Esegui la query
        $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql,$params);
        //var_dump($sql );
        //var_dump($params);
        // $params);
        $results1 = $command->queryAll();
        

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results1,

        ]);

$tsql="SELECT DISTINCT pid,SUM(oredelta) as totaleore,monteore,p.projectname,a.accountname,tipo
 FROM x_vistaprog  

  LEFT JOIN vtiger_project AS p ON x_vistaprog.pid=p.projectid
  LEFT JOIN vtiger_account AS a ON p.linktoaccountscontacts= a.accountid
 where
 $where

 GROUP BY pid,tipo";
  $connection = Yii::$app->db6;


   //Yii::debug("SQL Query: $sql");
    //    Yii::debug("Params: " . json_encode($params));
        $command = $connection->createCommand($tsql,$params);
        // $params);
        $resultstotali = $command->queryAll();
$t=$command;


$sql="SELECT  codiceprogetto,oggetto,orainizioevento,
    CASE 
        WHEN orafineevento IS NULL THEN 
            CASE
                WHEN DATE_ADD(orainizioevento, INTERVAL oredelta HOUR) >= DATE(orainizioevento) AND DATE(DATE_ADD(orainizioevento, INTERVAL oredelta HOUR)) = DATE(orainizioevento) 
                THEN DATE_ADD(DATE_ADD(orainizioevento, INTERVAL oredelta HOUR), INTERVAL 1 DAY)
                ELSE DATE_ADD(orainizioevento, INTERVAL oredelta HOUR)
            END
        WHEN DATE(orafineevento) = DATE(orainizioevento) THEN DATE_ADD(orafineevento, INTERVAL 1 DAY)
        ELSE orafineevento 
    END AS orafineevento,
 oredelta,pid FROM x_vistaprog 
 where 
 $where  and tipo='AP'";
  $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql,$params);
        // $params);
        $Timelineprogetti = $command->queryAll();





 $connection = Yii::$app->db6;
     $listap = $connection->createCommand("SELECT DISTINCT codiceprogetto 
     FROM x_vistaprog where codiceprogetto not like '%ticket%'")
    ->queryColumn();


$listac= $connection->createCommand("SELECT DISTINCT soggetto FROM x_vistaprog")
            ->queryColumn();






        $users = $connection->
            //createCommand("SELECT DISTINCT utente_destinatario 
            //FROM xestrazione")
            createcommand("SELECT CONCAT(first_name,' ',last_name)AS utente_destinatario 
        FROM vt.vtiger_users")
            ->queryColumn();


$mainfiltro= "SELECT distinct pid,codiceprogetto  FROM 
            x_vistaprog
            where 
          $where
              order by DATE(pid) ASC
           
            ";
        $connection = Yii::$app->db6;
        $command = $connection->createCommand($mainfiltro, $params);
        //var_dump($sql );
        //var_dump($params);
        // $params);
        $resultsfiltro = $command->queryAll();
        // Estrai la colonna `pid` dai risultati di $resultsfiltro
        $pids = array_column($resultsfiltro, 'pid');

        // Assicurati che ci siano valori prima di proseguire
        if (!empty($pids)) {
            // Converti l'array in una lista separata da virgole, usando parametri per la sicurezza
            $placeholders = implode(',',  $pids);
//var_dump($placeholders);
            // Query principale con il filtro sui pid
            $query = "SELECT DISTINCT pid AS id,
        codiceprogetto, soggetto,
        (SELECT vtiger_project.startdate FROM vtiger_project WHERE
        vtiger_project.projectid = xestrazione.pid) AS da,
        COALESCE(
            (SELECT vtiger_project.actualenddate FROM vtiger_project
             WHERE vtiger_project.projectid = xestrazione.pid),
            DATE_ADD(
                (SELECT vtiger_project.startdate FROM vtiger_project
                 WHERE vtiger_project.projectid = xestrazione.pid),
                INTERVAL 30 DAY
            )
        ) AS a,
        utente_destinatario, 0 AS oredelta,
        (SELECT vtiger_project.progress FROM vtiger_project
         WHERE vtiger_project.projectid = xestrazione.pid) AS progress
        FROM xestrazione
        WHERE pid IN ($placeholders) AND codiceprogetto NOT LIKE 'Ticket%' limit 1000 ";

            $query2 = "SELECT DISTINCT pid AS id, tid,
        oggetto, soggetto,
        COALESCE(orainizioevento, NOW()) AS da,
        COALESCE(
            orafineevento,
            DATE_ADD(COALESCE(orainizioevento, NOW()), INTERVAL 3 DAY)
        ) AS a,
        utente_destinatario, COALESCE(oredelta, 0) AS oredelta,
        (SELECT projecttaskprogress FROM vtiger_projecttask
         WHERE vtiger_projecttask.projecttaskid = tid) AS progress
        FROM xestrazione
        WHERE pid IN ($placeholders) AND codiceprogetto NOT LIKE 'Ticket%'
        ORDER BY COALESCE(orainizioevento, NOW()) ASC limit 1000 ";

            // Esegui le query usando i valori di $pids
            $dbName = 'db6';
            $db = Yii::$app->$dbName;

            $resultsQuery1 = $db->createCommand($query)->queryAll();
            $resultsQuery2 =   $db->createCommand($query2)->queryAll();
        } else {
            // Gestione del caso in cui $resultsfiltro sia vuoto
            $resultsQuery1 = [];
            $resultsQuery2 = [];
        }



           return $this->render('progetti', ['dataProvider'=>$dataProvider,
           'listap'=>$listap,
            'dayFrom'=>$dayFrom,
            'dayTo'=>$dayTo,
            'listac'=>$listac,
            'resultstotali'=>$resultstotali,
             'results1'=>   $results1 ,
             'Timelineprogetti'=>$Timelineprogetti,
             'tsql'=>$tsql,             
             'tracking'=>$t,
            'users' => $users,
            'utente' => $utente,
            'query'=> $resultsQuery1,
            'query2'=> $resultsQuery2
        
        ]);
 }





    public function actionTicket()
    {
       
$dayFrom = Yii::$app->request->get('dayFrom');
        $dayTo = Yii::$app->request->get('dayTo');
        $listap = Yii::$app->request->get('listap');
        $listac= Yii::$app->request->get('listac');
 $params = [];
        $whereClauses = [];

        if (!empty($dayFrom)) {
            $dateFrom = new \DateTime($dayFrom);
            $whereClauses[] = "DATE(orainizioevento) >= '" . $dateFrom->format('Y-m-d') . "'";
        }

        if (!empty($dayTo)) {
            $dateTo = new \DateTime($dayTo);
            $whereClauses[] = "DATE(orainizioevento) <= '" . $dateTo->format('Y-m-d') . "'";
        }
    if (!empty($listac)) {
    // Gestione dei pattern per listac come array
    if (is_array($listac)) {
        // Creazione del placeholder per i parametri IN
        $placeholders = [];
        foreach ($listac as $key => $value) {
            $paramName = ":listac$key";
            $placeholders[] = $paramName;
            $params[$paramName] = $value;
        }
        // Aggiungi la condizione IN con i placeholder
        $whereClauses[] = 'soggetto IN (' . implode(', ', $placeholders) . ')';
    } else {
        // Se listac non è un array, usa LIKE
        $whereClauses[] = 'soggetto LIKE :listac';
        $params[':listac'] = "%$listac%";
    }
}

        if ($listap !== null
        ) {
            // Gestione dei pattern per ciente
            $listapPatterns = [];
            $listapPatterns[] = "codiceprogetto LIKE :listap";
            $params[':listap'] = "%$listap%";

            $whereClauses[] = '(' . implode(' OR ', $listapPatterns) . ')';
        }

        $where = '';
        if (!empty($whereClauses)) {
            $where =   implode(' AND ', $whereClauses);
        }
        else {$where=' 1=1 ';
        }





   


 $sql = "
          SELECT *  
from xestrazione
LEFT JOIN vt.vtiger_ticketcf AS tcustom ON tcustom.ticketid=xestrazione.tid
LEFT JOIN  vt.vtiger_troubletickets vt ON vt.ticketid=xestrazione.tid
LEFT JOIN vt.vtiger_products AS vp ON vp.productid=vt.product_id
            WHERE 
           $where
              order by DATE(orainizioevento) ASC
             
        ";



     //   Yii::debug("SQL Query: $sql");
     //   Yii::debug("Params: " . json_encode($params));

        // Esegui la query
        $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql,$params);
        // $params);
        $results1 = $command->queryAll();
        

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results1,

        ]);






$sql2 = "select
 case when isnull(cf_909) then 'non impostato' ELSE cf_909 
             END  AS name,
             COUNT(*) as data
            from xestrazione 
             LEFT JOIN  vt.vtiger_ticketcf  AS tcustom ON tcustom.ticketid=xestrazione.tid 
            WHERE 
           $where and tipo IN ('T','TP','TA')
           GROUP by cf_909
        
        ";

     
        $command = $connection->createCommand($sql2,$params);
        // $params);
        $resultstipoass = $command->queryAll();

      $sqllistaeventi=" SELECT  cf_909 FROM vtiger_cf_909 WHERE cf_909 IN (
SELECT DISTINCT(cf_909) FROM vt.vtiger_ticketcf)
UNION 
SELECT 'non impostato' " ;
  
      $command = $connection->createCommand($sqllistaeventi);
      $listaeventi = $command->queryAll();
      $listaeventi=array_column($listaeventi, 'cf_909');
        
$prod="SELECT vp.productname AS name , sum(oredelta)AS data  FROM xestrazione 
LEFT JOIN  vt.vtiger_troubletickets vt ON vt.ticketid=xestrazione.tid
LEFT JOIN vt.vtiger_products AS vp ON vp.productid=vt.product_id
  WHERE 
           $where and  tipo IN ('T','TP','TA')  

AND  product_id  <>0  
GROUP BY vp.productname";
       
  
        $command = $connection->createCommand($prod,$params);
        // $params);
        $resultsprod = $command->queryAll();


$comprod="SELECT custom2 AS name, COUNT(*)  AS data FROM xestrazione
where $where  and  tipo IN ('T','TP','TA')  
GROUP BY custom2";

 
        $command = $connection->createCommand($comprod,$params);
        // $params);
        $resultscomprod = $command->queryAll();

$stato="SELECT case when isnull(codicestatoevento) and tipo='TA' then 'ATTIVITA' ELSE 
codicestatoevento END  AS name , COUNT(* ) AS data    FROM xestrazione
where $where  and  tipo IN ('T','TP')  
GROUP BY codicestatoevento";
 
        $command = $connection->createCommand($stato,$params);
        // $params);
        $resultsstato = $command->queryAll();



$cf="SELECT case when isnull(soggetto) then 'non assegnato' ELSE soggetto end
 AS name ,COUNT(*) AS data    FROM xestrazione
where $where  and  tipo IN ('T','TP','TA')  
 GROUP BY soggetto
";
 
        $command = $connection->createCommand($cf,$params);
        // $params);
        $resultscf = $command->queryAll();


$utenti="SELECT utente_destinatario AS name, COUNT(*) AS data    FROM xestrazione
WHERE $where and  tipo IN ('T','TP','TA')  
 GROUP BY utente_destinatario;
";

 
        $command = $connection->createCommand($utenti,$params);
        // $params);
        $resultsutenti = $command->queryAll();




$listacq= $connection->createCommand("SELECT accountname AS id ,
accountname AS desk FROM vt.vtiger_account")
           ->queryAll();

         $listac=   ArrayHelper::map($listacq,'id','desk');

$utentiore="
SELECT utente_destinatario AS name, SUM(oredelta) AS data    FROM xestrazione
WHERE  $where  and  tipo IN ('T','TP','TA')  
 GROUP BY utente_destinatario
";
 
        $command = $connection->createCommand($utentiore,$params);
        // $params);
        $resultsutentiore = $command->queryAll();









           return $this->render('ticket', 
           ['dataProvider'=>$dataProvider,
           //'listap'=>$listap,
            'dayFrom'=>$dayFrom,
            'dayTo'=>$dayTo,
            'listac'=>$listac,
            //'resultstotali'=>$resultstotali,
            // 'results1'=>   $results1 ,
            // 'Timelineprogetti'=>$Timelineprogetti,
            // 'tsql'=>$tsql,
             'new'=>'nuovi',
             'tracking'=>$where,
        'resultstipoass'=>$resultstipoass,
        'listaeventi'=>$listaeventi,
        'resultsprod'=>$resultsprod ,
        'resultscomprod'=>$resultscomprod,
        'resultsstato'=>$resultsstato,
        'resultscf'=>   $resultscf,
        'resultsutenti'=>$resultsutenti,
        'resultsutentiore'=>$resultsutentiore
        ]);    }


}
