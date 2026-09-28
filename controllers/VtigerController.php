<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class VtigerController extends Controller

{


    public function beforeAction($action)
    {
        // Controlla se l'utente è guest (non loggato)
        if (Yii::$app->user->isGuest) {
            // Redirige su site/index
            return $this->redirect(['site/index'])->send();
            // send() invia subito la risposta
        }

        return parent::beforeAction($action);
    }
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
and g.groupname=:gruppi;
";
 $connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, [':gruppi' => $gruppi]);

$idgruppo = $command->queryAll();

$sql="
SELECT count(g.groupid) as conta, g.groupname 
FROM vtiger_groups g
JOIN vtiger_users2group ug ON g.groupid = ug.groupid
JOIN vtiger_users u ON ug.userid = u.id
WHERE u.status = 'Active'
and g.groupname=:gruppi
GROUP BY g.groupname ;
";
 //$connection = Yii::$app->db6;
        $command = $connection->createCommand($sql, [':gruppi' => $gruppi]);
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
        if ($where<> "WHERE (utente_destinatario LIKE :utente)"){
        
        $sql = "
            SELECT * FROM 
            xestrazione
            $where
              order by DATE(orainizioevento) ASC
        ";
            // Esegui la query con cache (30s)
            $connection = Yii::$app->db6;
            $cacheKey = 'vtiger_search_main_' . md5($sql . '|' . serialize($params));
            $results = Yii::$app->cache->get($cacheKey);
            if ($results === false) {
                $command = $connection->createCommand($sql, $params);
                $results = $command->queryAll();
                Yii::$app->cache->set($cacheKey, $results, 30);
            }
        }
        else{

            $sql = "
            SELECT  * FROM 
            xestrazione
              order by DATE(orainizioevento) ASC
              limit 1000 
        ";
            // Esegui la query con cache (30s)
            $connection = Yii::$app->db6;
            $cacheKey = 'vtiger_search_main_' . md5($sql);
            $results = Yii::$app->cache->get($cacheKey);
            if ($results === false) {
                $command = $connection->createCommand($sql );
                $results = $command->queryAll();
                Yii::$app->cache->set($cacheKey, $results, 30);
            }
        }


        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $results,
            'pagination' => false
        ]);






// Nota: la query $ticketstat (SELECT * con 3 LEFT JOIN) è stata rimossa:
        // $ticketstat_res non è mai usato dalle viste.





        

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
            if (isset($result['xtipologia']) && $result['xtipologia'] === "Licenze D'uso") {
               // yii::error("tipologia: ".$result['xtipologia']);
        continue; // salta questo record
    }

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


if ($where<> "WHERE (utente_destinatario LIKE :utente)"){
        $sql = "
            SELECT DATE(orainizioevento) AS giorno, 
                   round(SUM(oredelta),2) AS totale_lavorato
            FROM xestrazione
            $where
            GROUP BY DATE(orainizioevento)
            ORDER BY DATE(orainizioevento)
        ";

        // Esegui la query con cache (30s)
        $cacheKey = 'vtiger_search_daily_' . md5($sql . '|' . serialize($params));
        $resultsd = Yii::$app->cache->get($cacheKey);
        if ($resultsd === false) {
            $command = $connection->createCommand($sql, $params);
            $resultsd = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsd, 30);
        }
}else{
            $sql = "
            SELECT DATE(orainizioevento) AS giorno, 
                   round(SUM(oredelta),2) AS totale_lavorato
            FROM xestrazione
         
            GROUP BY DATE(orainizioevento)
            ORDER BY DATE(orainizioevento)
            limit 100
        ";

        // Esegui la query con cache (30s)
        $cacheKey = 'vtiger_search_daily_' . md5($sql);
        $resultsd = Yii::$app->cache->get($cacheKey);
        if ($resultsd === false) {
            $command = $connection->createCommand($sql );
            $resultsd = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsd, 30);
        }
}
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









        if ($where <> "WHERE (utente_destinatario LIKE :utente)") {
        $sql = "
            SELECT tipo, DATE(orainizioevento) AS day
            FROM xestrazione
            $where
             order by DATE(orainizioevento) ASC
        ";

        // Esegui la query con cache (30s)
        $cacheKey = 'vtiger_search_series_' . md5($sql . '|' . serialize($params));
        $results = Yii::$app->cache->get($cacheKey);
        if ($results === false) {
            $command = $connection->createCommand($sql, $params);
            $results = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $results, 30);
        }

        }else{
            $sql = "
            SELECT tipo, DATE(orainizioevento) AS day
            FROM xestrazione
            
             order by DATE(orainizioevento) ASC
        limit 1000
             ";

        // Esegui la query con cache (30s)
        $cacheKey = 'vtiger_search_series_' . md5($sql);
        $results = Yii::$app->cache->get($cacheKey);
        if ($results === false) {
            $command = $connection->createCommand($sql);
            $results = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $results, 30);
        }

        }


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
        $cacheKey = 'vtiger_search_opentk_' . md5($sql . '|' . serialize($params));
        $resultstk = Yii::$app->cache->get($cacheKey);
        if ($resultstk === false) {
            $command = $connection->createCommand($sql, $params);
            $resultstk = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultstk, 30);
        }


$sql= "
 SELECT * FROM xestrazione  $where   oggetto not like '%ferie%'
 ORDER BY oredelta DESC LIMIT 5;

";
        $cacheKey = 'vtiger_search_bigtk_' . md5($sql . '|' . serialize($params));
        $resultBIGTK = Yii::$app->cache->get($cacheKey);
        if ($resultBIGTK === false) {
            $command = $connection->createCommand($sql, $params);
            $resultBIGTK = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultBIGTK, 30);
        }


        // Crea il provider dei dati
  

        // Ottieni gli utenti per i filtri (modifica questa query se necessario)
      if (Yii::$app->user->identity->level ??0  >= 80){
        $users = $connection->
         createcommand("SELECT CONCAT(first_name,' ',last_name)AS utente_destinatario 
        FROM vt.vtiger_users")
        ->queryColumn();
      }
        else{
            $users = $connection->
            createcommand("SELECT CONCAT(first_name,' ',last_name)AS utente_destinatario 
             FROM vt.vtiger_users where email1=:email",
             [':email' => Yii::$app->user->identity->email ?? ''])
             ->queryColumn();
        }
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

        // Senza filtri (apertura pagina di default) limitiamo le query dei grafici a 100 record
        // per non appesantire il rendering; con i filtri la ricerca resta completa.
        $hasFilters = !empty($whereClauses);
        $limitChart = $hasFilters ? '' : ' LIMIT 100';
        $limitMain = $hasFilters ? ' LIMIT 1000' : ' LIMIT 100';





   


 $sql = "
            SELECT * FROM 
            x_vistaprog 
            WHERE 
           $where
              order by DATE(orainizioevento) ASC
              $limitMain
        ";


 
     //   Yii::debug("SQL Query: $sql");
     //   Yii::debug("Params: " . json_encode($params));

        // Esegui la query con cache (30s)
        $connection = Yii::$app->db6;
        $cacheKey = 'vtiger_progetti_results1_' . md5($sql . '|' . serialize($params));
        $results1 = Yii::$app->cache->get($cacheKey);
        if ($results1 === false) {
            $command = $connection->createCommand($sql, $params);
            $results1 = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $results1, 30);
        }
        

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results1,
            'pagination'=>false

        ]);

$tsql="SELECT DISTINCT pid,SUM(oredelta) as totaleore,monteore,
p.projectname,a.accountname,tipo
 FROM x_vistaprog  

  LEFT JOIN vtiger_project AS p ON x_vistaprog.pid=p.projectid
  LEFT JOIN vtiger_account AS a ON p.linktoaccountscontacts= a.accountid
 where
 $where
and xtipologia<>'Trasferta'
  GROUP BY pid,tipo$limitChart";
  $connection = Yii::$app->db6;

        $cacheKey = 'vtiger_progetti_totali_' . md5($tsql . '|' . serialize($params));
        $resultstotali = Yii::$app->cache->get($cacheKey);
        if ($resultstotali === false) {
            $command = $connection->createCommand($tsql, $params);
            $resultstotali = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultstotali, 30);
        }

$tsql="SELECT DISTINCT pid,SUM(oredelta) as totaleore,monteore,
p.projectname,a.accountname,tipo
 FROM x_vistaprog  

  LEFT JOIN vtiger_project AS p ON x_vistaprog.pid=p.projectid
  LEFT JOIN vtiger_account AS a ON p.linktoaccountscontacts= a.accountid
 where
 $where
and xtipologia='Trasferta' and tipo='AP'
  GROUP BY pid,tipo$limitChart";
  $connection = Yii::$app->db6;

        $cacheKey = 'vtiger_progetti_trasf_' . md5($tsql . '|' . serialize($params));
        $resultstotalitra = Yii::$app->cache->get($cacheKey);
        if ($resultstotalitra === false) {
            $command = $connection->createCommand($tsql, $params);
            $resultstotalitra = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultstotalitra, 30);
        }




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
 $where  and tipo='AP'$limitChart";
  $connection = Yii::$app->db6;
        $cacheKey = 'vtiger_progetti_timeline_' . md5($sql . '|' . serialize($params));
        $Timelineprogetti = Yii::$app->cache->get($cacheKey);
        if ($Timelineprogetti === false) {
            $command = $connection->createCommand($sql, $params);
            $Timelineprogetti = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $Timelineprogetti, 30);
        }


 $connection = Yii::$app->db6;
     $cacheKey = 'vtiger_progetti_listap';
     $listap = Yii::$app->cache->get($cacheKey);
     if ($listap === false) {
         $listap = $connection->createCommand("SELECT DISTINCT codiceprogetto 
         FROM x_vistaprog where codiceprogetto not like '%ticket%'")
        ->queryColumn();
         Yii::$app->cache->set($cacheKey, $listap, 300);
     }


$cacheKey = 'vtiger_progetti_listac';
$listac = Yii::$app->cache->get($cacheKey);
if ($listac === false) {
    $listac = $connection->createCommand("SELECT DISTINCT soggetto FROM x_vistaprog")
                ->queryColumn();
    Yii::$app->cache->set($cacheKey, $listac, 300);
}






        $cacheKey = 'vtiger_users_list';
        $users = Yii::$app->cache->get($cacheKey);
        if ($users === false) {
            $users = $connection->
                createcommand("SELECT CONCAT(first_name,' ',last_name)AS utente_destinatario 
            FROM vt.vtiger_users")
                ->queryColumn();
            Yii::$app->cache->set($cacheKey, $users, 300);
        }


$mainfiltro= "SELECT distinct pid,codiceprogetto  FROM 
            x_vistaprog
            where 
          $where
              order by DATE(pid) ASC
           $limitChart
            ";
        $connection = Yii::$app->db6;
        $cacheKey = 'vtiger_progetti_filtro_' . md5($mainfiltro . '|' . serialize($params));
        $resultsfiltro = Yii::$app->cache->get($cacheKey);
        if ($resultsfiltro === false) {
            $command = $connection->createCommand($mainfiltro, $params);
            $resultsfiltro = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsfiltro, 30);
        }
        // Estrai la colonna `pid` dai risultati di $resultsfiltro
        $pids = array_column($resultsfiltro, 'pid');

        // Filtra solo i pid numerici e rimuovi eventuali valori vuoti
        $pids = array_values(array_filter($pids, function ($v) {
            return is_numeric($v);
        }));

        // Assicurati che ci siano valori prima di proseguire
        if (!empty($pids)) {
            // Converti l'array in una lista separata da virgole, cast a intero per sicurezza
            $placeholders = implode(',', array_map('intval', $pids));
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
        WHERE pid IN ($placeholders) AND codiceprogetto NOT LIKE 'Ticket%' $limitMain ";

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
        ORDER BY COALESCE(orainizioevento, NOW()) ASC $limitMain ";

            // Esegui le query usando i valori di $pids
            $dbName = 'db6';
            $db = Yii::$app->$dbName;

            $cacheKey = 'vtiger_progetti_q1_' . md5($query);
            $resultsQuery1 = Yii::$app->cache->get($cacheKey);
            if ($resultsQuery1 === false) {
                $resultsQuery1 = $db->createCommand($query)->queryAll();
                Yii::$app->cache->set($cacheKey, $resultsQuery1, 30);
            }

            $cacheKey = 'vtiger_progetti_q2_' . md5($query2);
            $resultsQuery2 = Yii::$app->cache->get($cacheKey);
            if ($resultsQuery2 === false) {
                $resultsQuery2 = $db->createCommand($query2)->queryAll();
                Yii::$app->cache->set($cacheKey, $resultsQuery2, 30);
            }
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
            'resultstotalitra'=>$resultstotalitra,
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
        // LIKE 'OR' per ogni cliente selezionato (più tollerante del confronto esatto)
        $likeClauses = [];
        foreach ($listac as $key => $value) {
            $paramName = ":listac$key";
            $likeClauses[] = "soggetto LIKE $paramName";
            $params[$paramName] = "%$value%";
        }
        $whereClauses[] = '(' . implode(' OR ', $likeClauses) . ')';
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

        // Senza filtri mostriamo al massimo 100 risultati per non appesantire la pagina;
        // appena si filtra la ricerca torna completa (nessun limite).
        $hasFilters = !empty($whereClauses);
        $limitMain = $hasFilters ? '' : ' LIMIT 100';



 $sql = "
          SELECT *  
from xestrazione
LEFT JOIN  vt.vtiger_troubletickets vt ON vt.ticketid=xestrazione.tid
            WHERE 
           $where
              order by DATE(orainizioevento) ASC
              $limitMain
        ";



     //   Yii::debug("SQL Query: $sql");
     //   Yii::debug("Params: " . json_encode($params));

        // Esegui la query con cache (30s)
        $connection = Yii::$app->db6;
        $cacheKey = 'vtiger_ticket_main_' . md5($sql . '|' . serialize($params));
        $results1 = Yii::$app->cache->get($cacheKey);
        if ($results1 === false) {
            $command = $connection->createCommand($sql, $params);
            $results1 = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $results1, 30);
        }
        

        $dataProvider = new ArrayDataProvider([
            'allModels' => $results1,
            'pagination' => false
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

     
        $cacheKey = 'vtiger_ticket_tipoass_' . md5($sql2 . '|' . serialize($params));
        $resultstipoass = Yii::$app->cache->get($cacheKey);
        if ($resultstipoass === false) {
            $command = $connection->createCommand($sql2, $params);
            $resultstipoass = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultstipoass, 30);
        }

      $sqllistaeventi=" SELECT  cf_909 FROM vtiger_cf_909 WHERE cf_909 IN (
SELECT DISTINCT(cf_909) FROM vt.vtiger_ticketcf)
UNION 
SELECT 'non impostato' " ;
  
      $listaeventi = Yii::$app->cache->get('vtiger_ticket_listaeventi');
      if ($listaeventi === false) {
          $command = $connection->createCommand($sqllistaeventi);
          $listaeventi = $command->queryAll();
          $listaeventi = array_column($listaeventi, 'cf_909');
          Yii::$app->cache->set('vtiger_ticket_listaeventi', $listaeventi, 300);
      }
        
$prod="SELECT vp.productname AS name , sum(oredelta)AS data  FROM xestrazione 
LEFT JOIN  vt.vtiger_troubletickets vt ON vt.ticketid=xestrazione.tid
LEFT JOIN vt.vtiger_products AS vp ON vp.productid=vt.product_id
  WHERE 
           $where and  tipo IN ('T','TP','TA')  

AND  product_id  <>0  
GROUP BY vp.productname";
       
  
        $cacheKey = 'vtiger_ticket_prod_' . md5($prod . '|' . serialize($params));
        $resultsprod = Yii::$app->cache->get($cacheKey);
        if ($resultsprod === false) {
            $command = $connection->createCommand($prod, $params);
            $resultsprod = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsprod, 30);
        }


$comprod="SELECT custom2 AS name, COUNT(*)  AS data FROM xestrazione
where $where  and  tipo IN ('T','TP','TA')  
GROUP BY custom2";

 
        $cacheKey = 'vtiger_ticket_comprod_' . md5($comprod . '|' . serialize($params));
        $resultscomprod = Yii::$app->cache->get($cacheKey);
        if ($resultscomprod === false) {
            $command = $connection->createCommand($comprod, $params);
            $resultscomprod = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultscomprod, 30);
        }

$stato="SELECT case when isnull(codicestatoevento) and tipo='TA' then 'ATTIVITA' ELSE 
codicestatoevento END  AS name , COUNT(* ) AS data    FROM xestrazione
where $where  and  tipo IN ('T','TP')  
GROUP BY codicestatoevento";
 
        $cacheKey = 'vtiger_ticket_stato_' . md5($stato . '|' . serialize($params));
        $resultsstato = Yii::$app->cache->get($cacheKey);
        if ($resultsstato === false) {
            $command = $connection->createCommand($stato, $params);
            $resultsstato = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsstato, 30);
        }



$cf="SELECT case when isnull(soggetto) then 'non assegnato' ELSE soggetto end
 AS name ,COUNT(*) AS data    FROM xestrazione
where $where  and  tipo IN ('T','TP','TA')  
 GROUP BY soggetto
";
 
        $cacheKey = 'vtiger_ticket_cf_' . md5($cf . '|' . serialize($params));
        $resultscf = Yii::$app->cache->get($cacheKey);
        if ($resultscf === false) {
            $command = $connection->createCommand($cf, $params);
            $resultscf = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultscf, 30);
        }


$utenti="SELECT utente_destinatario AS name, COUNT(*) AS data    FROM xestrazione
WHERE $where and  tipo IN ('T','TP','TA')  
 GROUP BY utente_destinatario;
";

 
        $cacheKey = 'vtiger_ticket_utenti_' . md5($utenti . '|' . serialize($params));
        $resultsutenti = Yii::$app->cache->get($cacheKey);
        if ($resultsutenti === false) {
            $command = $connection->createCommand($utenti, $params);
            $resultsutenti = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsutenti, 30);
        }

$listacq = Yii::$app->cache->get('vtiger_ticket_listac');
if ($listacq === false) {
    $listacq= $connection->createCommand("SELECT accountname AS id ,
accountname AS desk FROM vt.vtiger_account")
           ->queryAll();
    Yii::$app->cache->set('vtiger_ticket_listac', $listacq, 300);
}

         $listac=   ArrayHelper::map($listacq,'id','desk');

$utentiore="
SELECT utente_destinatario AS name, SUM(oredelta) AS data    FROM xestrazione
WHERE  $where  and  tipo IN ('T','TP','TA')  
 GROUP BY utente_destinatario
";
 
        $cacheKey = 'vtiger_ticket_utentiore_' . md5($utentiore . '|' . serialize($params));
        $resultsutentiore = Yii::$app->cache->get($cacheKey);
        if ($resultsutentiore === false) {
            $command = $connection->createCommand($utentiore, $params);
            $resultsutentiore = $command->queryAll();
            Yii::$app->cache->set($cacheKey, $resultsutentiore, 30);
        }









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


    public function actionClientiAjax()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $q = trim((string) Yii::$app->request->get('q', ''));

        $connection = Yii::$app->db6;
        $sql = "SELECT accountname AS id, accountname AS text FROM vt.vtiger_account";
        if ($q !== '') {
            $sql .= " WHERE accountname LIKE :q LIMIT 100";
            $rows = $connection->createCommand($sql)
                ->bindValue(':q', '%' . $q . '%')
                ->queryAll();
        } else {
            $rows = $connection->createCommand($sql . ' LIMIT 100')->queryAll();
        }

        return ['results' => $rows, 'pagination' => ['more' => false]];
    }


    public function actionGanttprogetti()
    {
        $request = Yii::$app->request;

        // Recupero parametri (assicurati che i nomi corrispondano al form)
        $dayFrom = $request->get('dayFrom');
        $dayTo = $request->get('dayTo');
        $listac = $request->get('listac');
        $assegnato_a = $request->get('assegnato_a');
        $stato = $request->get('stato');

        $params = [];
        $whereClauses = ["ent.deleted = 0"];

        // Applica filtri
        if (!empty($dayFrom)) {
            $whereClauses[] = "p.startdate >= :df";
            $params[':df'] = $dayFrom;
        }
        if (!empty($dayTo)) {
            $whereClauses[] = "p.startdate <= :dt";
            $params[':dt'] = $dayTo;
        }
        if (!empty($listac)) {
            $whereClauses[] = "acc.accountname LIKE :listac";
            $params[':listac'] = "%$listac%";
        }
        if (!empty($assegnato_a)) {
            $whereClauses[] = "CONCAT(u.first_name, ' ', u.last_name) LIKE :owner";
            $params[':owner'] = "%$assegnato_a%";
        }
        if (!empty($stato)) {
            $whereClauses[] = "p.projectstatus LIKE :stato";
            $params[':stato'] = "%$stato%";
        }

        $whereSQL = implode(' AND ', $whereClauses);

        $sql = "
        SELECT 
            p.projectid, p.projectname as nome_progetto, p.project_no, p.startdate AS data_inizio, 
            p.targetenddate AS data_fine_obiettivo, p.projectstatus AS stato, 
            acc.accountname AS azienda, p.progress AS progresso,
            pcf.cf_901 AS monte_ore, pcf.cf_963 AS residuo,
            CONCAT(u.first_name, ' ', u.last_name) AS assegnato_a
        FROM vtiger_project p
        INNER JOIN vtiger_crmentity ent ON p.projectid = ent.crmid
        INNER JOIN vtiger_users u ON ent.smownerid = u.id
        LEFT JOIN vtiger_projectcf pcf ON p.projectid = pcf.projectid
        LEFT JOIN vtiger_account acc ON p.linktoaccountscontacts = acc.accountid
        WHERE $whereSQL
        ORDER BY acc.accountname ASC, p.startdate ASC
    ";

        $cacheKey = 'vtiger_gantt_progetti_' . md5($sql . '|' . serialize($params));
        $progetti = Yii::$app->cache->get($cacheKey);
        if ($progetti === false) {
            $progetti = Yii::$app->db6->createCommand($sql, $params)->queryAll();
            Yii::$app->cache->set($cacheKey, $progetti, 30);
        }

        // Genera mesi
        $d1 = !empty($dayFrom) ? new \DateTime($dayFrom) : new \DateTime(date('Y-01-01'));
        $d2 = !empty($dayTo) ? new \DateTime($dayTo) : new \DateTime(date('Y-12-31'));
        $period = new \DatePeriod($d1->modify('first day of this month'), new \DateInterval('P1M'), $d2->modify('last day of this month')->modify('+1 day'));

        return $this->render('gantt_progetti', [
            'progetti' => $progetti,
            'period' => $period,
            'filters' => $request->get()
        ]);
    }



















    public function actionExportgantt()
    {
        $request = Yii::$app->request;

        // 1. Recupero parametri (stessi nomi usati nella form e in actionGanttProgetti)
        $dayFrom = $request->get('dayFrom');
        $dayTo = $request->get('dayTo');
        $listac = $request->get('listac');
        $assegnato_a = $request->get('assegnato_a');
        $prodotto = $request->get('prodotto');
        $azienda_interna = $request->get('azienda_interna');
        $stato = $request->get('stato');

        $params = [];
        $whereClauses = ["ent.deleted = 0"];

        // 2. Ricostruzione logica filtri
        if (!empty($dayFrom)) {
            $whereClauses[] = "p.startdate >= :df";
            $params[':df'] = $dayFrom;
        }
        if (!empty($dayTo)) {
            $whereClauses[] = "p.startdate <= :dt";
            $params[':dt'] = $dayTo;
        }
        if (!empty($listac)) {
            $whereClauses[] = "acc.accountname LIKE :listac";
            $params[':listac'] = "%$listac%";
        }
        if (!empty($assegnato_a)) {
            $whereClauses[] = "CONCAT(u.first_name, ' ', u.last_name) LIKE :owner";
            $params[':owner'] = "%$assegnato_a%";
        }
        if (!empty($prodotto)) {
            $whereClauses[] = "pcf.cf_869 LIKE :prod";
            $params[':prod'] = "%$prodotto%";
        }
        if (!empty($azienda_interna)) {
            $whereClauses[] = "pcf.cf_867 LIKE :azi_int";
            $params[':azi_int'] = "%$azienda_interna%";
        }
        if (!empty($stato)) {
            $whereClauses[] = "p.projectstatus LIKE :stato";
            $params[':stato'] = "%$stato%";
        }

        $whereSQL = implode(' AND ', $whereClauses);

        // 3. Query dati
        $sql = "
        SELECT 
            acc.accountname AS azienda,
            p.projectname AS nome_progetto,
            p.project_no AS n_progetto,
            CONCAT(u.first_name, ' ', u.last_name) AS assegnato_a,
            p.projectstatus AS stato,
            p.startdate AS data_inizio,
            p.targetenddate AS data_fine_obiettivo,
            pcf.cf_901 AS monte_ore,
            pcf.cf_963 AS residuo,
            p.progress AS progresso,
            pcf.cf_869 AS prodotto,
            pcf.cf_867 AS azienda_interna
        FROM vtiger_project p
        INNER JOIN vtiger_crmentity ent ON p.projectid = ent.crmid
        INNER JOIN vtiger_users u ON ent.smownerid = u.id
        LEFT JOIN vtiger_projectcf pcf ON p.projectid = pcf.projectid
        LEFT JOIN vtiger_account acc ON p.linktoaccountscontacts = acc.accountid
        WHERE $whereSQL
        ORDER BY acc.accountname ASC, p.startdate ASC
    ";

        $progetti = Yii::$app->db6->createCommand($sql, $params)->queryAll();

        // 4. Creazione File Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Report Progetti Gantt');

        // Intestazioni (Stile Bold e Sfondo Grigio)
        $headers = [
            'AZIENDA',
            'PROGETTO',
            'N. PROJ',
            'ASSEGNATO',
            'STATO',
            'INIZIO',
            'FINE OBIETTIVO',
            'MONTE ORE',
            'RESIDUO',
            '% PROG',
            'PRODOTTO',
            'AZIENDA INT.'
        ];

        $sheet->fromArray($headers, NULL, 'A1');
        $headerRange = 'A1:L1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0E0E0');

        // Inserimento Dati
        $rowIdx = 2;
        foreach ($progetti as $p) {
            $sheet->setCellValue('A' . $rowIdx, $p['azienda']);
            $sheet->setCellValue('B' . $rowIdx, $p['nome_progetto']);
            $sheet->setCellValue('C' . $rowIdx, $p['n_progetto']);
            $sheet->setCellValue('D' . $rowIdx, $p['assegnato_a']);
            $sheet->setCellValue('E' . $rowIdx, $p['stato']);
            $sheet->setCellValue('F' . $rowIdx, $p['data_inizio']);
            $sheet->setCellValue('G' . $rowIdx, $p['data_fine_obiettivo']);
            $sheet->setCellValue('H' . $rowIdx, $p['monte_ore']);
            $sheet->setCellValue('I' . $rowIdx, $p['residuo']);
            $sheet->setCellValue('J' . $rowIdx, $p['progresso'] . '%');
            $sheet->setCellValue('K' . $rowIdx, $p['prodotto']);
            $sheet->setCellValue('L' . $rowIdx, $p['azienda_interna']);

            // Coloriamo il residuo in rosso se negativo
            if ($p['residuo'] < 0) {
                $sheet->getStyle('I' . $rowIdx)->getFont()->getColor()->setARGB('FF0000');
            }

            $rowIdx++;
        }

        // Auto-size delle colonne
        foreach (range('A', 'L') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Invio al browser per il download
        $filename = 'Export_Progetti_' . date('Ymd_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }








        }
