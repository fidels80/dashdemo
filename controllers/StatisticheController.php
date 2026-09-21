<?php

namespace app\controllers;

use PHPUnit\Framework\Constraint\IsEmpty;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Dmsdocument;
use yii\helpers\Url;
use yii\helpers\Html;

use app\models\Agentifiles;

class StatisticheController extends \yii\web\Controller
{
    public function actionIndex()
    {

    $request = Yii::$app->request;
    $cd_agente = Yii::$app->user->identity->cd_agente ?? null;
    $lvl = Yii::$app->user->identity->level; // livello utente
$request = Yii::$app->request;
$session = Yii::$app->session;
// Leggi il parametro dal dropdown
$agenteSelezionato = $request->get('agente');

// Apri la sessione se non è già aperta
if (!$session->isActive) {
    $session->open();
}
    // Se lvl80 e c'è un agente selezionato, lo usiamo temporaneamente
    if ($lvl == 80 && $request->get('agente')) {
        $cd_agente = $request->get('agente');
         $session->set('cd_agente_filtrato', $cd_agente); // salvo in sessione
       //die("Filtro agente applicato: " . $cd_agente  );
    }else{
 $cd_agente = Yii::$app->user->identity->cd_agente ;

    }


        if ( empty($cd_agente) && $lvl == 80){
        $rows = Yii::$app->db5->createCommand("
            SELECT 
                FORMAT(DataDoc, 'yyyy-MM') AS mese,
                SUM(TotDocumentoE) AS TotDocumentoE,
                SUM(TotProvvigione_1E) AS TotProvvigione_1E,
                Count(x_ore) AS x_ore,
                Sum(TotImponibileE) as TotImponibileE
            FROM (
                SELECT 
                    dotes.DataDoc,
                    NULL AS TotDocumentoE,
                    NULL AS TotProvvigione_1E,
                    dorig.x_ore,
                      0 as TotImponibileE
                FROM dotes 
                LEFT JOIN dorig ON dorig.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'PRV'
                and year(dotes.datadoc)>=2024
                UNION ALL
                
                SELECT 
                    dotes.DataDoc,
                    DOTotali.TotDocumentoE,
                    DOTotali.TotProvvigione_1E,
                    NULL AS x_ore,
                      DOTotali.TotImponibileE
                FROM dotes
                LEFT JOIN DOTotali ON DOTotali.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'FTV'
                      and year(dotes.datadoc)>=2024
            ) AS unione
            GROUP BY FORMAT(DataDoc, 'yyyy-MM')
            ORDER BY FORMAT(DataDoc, 'yyyy-MM') DESC
        ")->queryAll();
        }
        else{
           // $cd_agente = Yii::$app->user->identity->cd_agente;
            $rows = Yii::$app->db5->createCommand("
            SELECT 
                FORMAT(DataDoc, 'yyyy-MM') AS mese,
                SUM(TotDocumentoE) AS TotDocumentoE,
                SUM(TotProvvigione_1E) AS TotProvvigione_1E,
                Count(x_ore) AS x_ore,
                sum(TotImponibileE) as TotImponibileE
            FROM (
                SELECT 
                    dotes.DataDoc,
                    NULL AS TotDocumentoE,
                    NULL AS TotProvvigione_1E,
                    dorig.x_ore,
                     0 as TotImponibileE
                FROM dotes 
                LEFT JOIN dorig ON dorig.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'PRV'
                 AND dotes.Cd_Agente_1 = '$cd_agente'
                         and year(dotes.datadoc)>=2024
                UNION ALL
                
                SELECT 
                    dotes.DataDoc,
                    DOTotali.TotDocumentoE,
                    DOTotali.TotProvvigione_1E,
                    NULL AS x_ore,
                       DOTotali.TotImponibileE
                FROM dotes
                LEFT JOIN DOTotali ON DOTotali.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'FTV'
                 AND dotes.Cd_Agente_1 = '$cd_agente'
                         and year(dotes.datadoc)>=2024
            ) AS unione
            GROUP BY FORMAT(DataDoc, 'yyyy-MM')
            ORDER BY FORMAT(DataDoc, 'yyyy-MM') asc
        ")
                 
        ->queryAll();


        }


        return $this->render('index', ['rows' => $rows]);
    }






    public function actionDatiDettaglio($mese)
    {
        try {
            $session = Yii::$app->session;

            $cd_agente = Yii::$app->user->identity->cd_agente ?? null;

            if ($session->isActive && $session->has('cd_agente_filtrato')) {
                $cd_agente = $session->get('cd_agente_filtrato');
            }
            $dataInizio = $mese . '-01';
            $dataFine   = date('Y-m-t', strtotime($dataInizio));
            $anno  = date('Y', strtotime($mese . '-01'));
            $meseNum = date('m', strtotime($mese . '-01'));

            // --- PRV ---
            $sqlPrv = "
              SELECT 
                dotes.Cd_Do,
                dotes.Cd_Agente_1,
                dotes.DataDoc,
                DOTes.NumeroDoc,
                DORig.Cd_AR,
                DORig.Descrizione,
                dorig.x_ore,
                dorig.x_cd_cf,
                dorig.x_citta,
                dorig.x_data,
                dorig.noteriga,
                dorig.cd_dosottocommessa,
                dosottocommessa.descrizione AS desc_sottocommessa,
                cf.Descrizione as descli
            FROM dotes 
            LEFT JOIN dorig ON dorig.Id_DoTes = dotes.Id_DoTes
            left join dosottocommessa on dosottocommessa.cd_dosottocommessa
            =dorig.cd_dosottocommessa
            left join cf on cf.Cd_CF=coalesce(dorig.x_cd_cf,dorig.cd_cf)
            WHERE dotes.Cd_Do = 'PRV'
              AND YEAR(dorig.x_data) = :anno
              AND MONTH(dorig.x_data) = :mese
        ";

            // Se l'utente è un agente, aggiungiamo il filtro
            $params = [':anno' => $anno, ':mese' => $meseNum];
            if (!empty($cd_agente)) {
                $sqlPrv .= " AND dotes.Cd_Agente_1 = :cd_agente";
                $params[':cd_agente'] = $cd_agente;
            }

            $prv = Yii::$app->db5->createCommand($sqlPrv, $params)->queryAll();


            // --- FTV ---
            $sqlFtv = "
            SELECT 
                dotes.DataDoc,
                dotes.NumeroDoc,
                dotes.Cd_CF AS cliente,
                dotes.Cd_Agente_1 AS agente,
                DOTotali.TotDocumentoE AS TotDocumentoE,
                DOTotali.TotProvvigione_1E AS TotProvvigione_1E,
                Liquidata,
                DataLiquidazione,
                  dototali.TotImponibileE AS TotImponibileE,
                  'https://example.com/pdf/FAT-123.pdf' AS pdf_url,
                  dotes.Id_DoTes AS id_dotes
            FROM dotes
            LEFT JOIN DOTotali ON DOTotali.Id_DoTes = dotes.Id_DoTes
            LEFT JOIN sc ON sc.Id_DOTes = dotes.Id_DoTes
            LEFT JOIN Provvigione ON sc.Id_SC = Provvigione.Id_SC
            WHERE dotes.Cd_Do = 'FTV'
              AND YEAR(dotes.DataDoc) = :anno
              AND MONTH(dotes.DataDoc) = :mese
              AND Cd_Agente_1 IS NOT NULL
        ";

            if (!empty($cd_agente)) {
                $sqlFtv .= " AND dotes.Cd_Agente_1 = :cd_agente";
            }

            $sqlFtv .= " ORDER BY DataDoc ASC";

            $ftv = Yii::$app->db5->createCommand($sqlFtv, $params)->queryAll();
foreach ($ftv as &$r) {
    $r['pdf_url'] = Url::to(['statistiche/genfile', 'id' => $r['id_dotes']], true);
}
            return [
                'prv' => $prv,
                'ftv' => $ftv,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }



    public function actionDatiDettaglioaj($mese)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $cd_agente = Yii::$app->user->identity->cd_agente ?? null;

            $dataInizio = $mese . '-01';
            $dataFine   = date('Y-m-t', strtotime($dataInizio));
            $anno  = date('Y', strtotime($mese . '-01'));
            $meseNum = date('m', strtotime($mese . '-01'));

            // === PRV ===
            $sqlPrv = "
   
           SELECT 
                dotes.Cd_Do,
                dotes.Cd_Agente_1,
                dotes.DataDoc,
                DOTes.NumeroDoc,
                DORig.Cd_AR,
                DORig.Descrizione,
                dorig.x_ore,
                dorig.x_cd_cf,
                dorig.x_citta,
                dorig.x_data,
                dorig.noteriga,
                dorig.cd_dosottocommessa,
                dosottocommessa.descrizione AS desc_sottocommessa,
                cf.Descrizione as descli
            FROM dotes 
            LEFT JOIN dorig ON dorig.Id_DoTes = dotes.Id_DoTes
            left join dosottocommessa on dosottocommessa.cd_dosottocommessa
            =dorig.cd_dosottocommessa
            left join cf on cf.Cd_CF=coalesce(dorig.x_cd_cf,dorig.cd_cf)
            WHERE dotes.Cd_Do = 'PRV'
              AND YEAR(dorig.x_data) = :anno
              AND MONTH(dorig.x_data) = :mese
        ";

            $params = [
                ':anno' => $anno,
                ':mese' => $meseNum,
            ];

            if (!empty($cd_agente)) {
                $sqlPrv .= " AND dotes.Cd_Agente_1 = :cd_agente";
                $params[':cd_agente'] = $cd_agente;
            }

            $prv = Yii::$app->db5->createCommand($sqlPrv, $params)->queryAll();


            // === FTV ===
            $sqlFtv = "
            SELECT 
                dotes.DataDoc,
                dotes.NumeroDoc,
                dotes.Cd_CF AS cliente,
                dotes.Cd_Agente_1 AS agente,
                DOTotali.TotDocumentoE AS TotDocumentoE,
                DOTotali.TotProvvigione_1E AS TotProvvigione_1E,
                dotes.TotImponibileE AS TotImponibileE,
            FROM dotes
            LEFT JOIN DOTotali ON DOTotali.Id_DoTes = dotes.Id_DoTes
            WHERE dotes.Cd_Do = 'FTV'
              AND YEAR(dotes.DataDoc) = :anno
              AND MONTH(dotes.DataDoc) = :mese
        ";

            if (!empty($cd_agente)) {
                $sqlFtv .= " AND dotes.Cd_Agente_1 = :cd_agente";
            }

            $ftv = Yii::$app->db5->createCommand($sqlFtv, $params)->queryAll();


            return [
                'prv' => $prv,
                'ftv' => $ftv,
            ];
        } catch (\Exception $e) {
            Yii::error($e->getMessage(), __METHOD__);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function actionDettaglio($mese)
    {
        $dati = $this->actionDatiDettaglio($mese);
        return $this->render('dettaglio', ['mese' => $mese, 'dati' => $dati]);
    }




    public function ___actionResetagente()
{
    $session = Yii::$app->session;
 $cd_agente= yii::$app->user->identity->cd_agente;
        $session->remove('cd_agente_filtrato');
         $session->set('cd_agente_filtrato', $cd_agente); // salvo in sessione

  $rows = Yii::$app->db5->createCommand("
            SELECT 
                FORMAT(DataDoc, 'yyyy-MM') AS mese,
                SUM(TotDocumentoE) AS TotDocumentoE,
                SUM(TotProvvigione_1E) AS TotProvvigione_1E,
                Count(x_ore) AS x_ore,
                sum(TotImponibileE) as TotImponibileE
            FROM (
                SELECT 
                    dotes.DataDoc,
                    NULL AS TotDocumentoE,
                    NULL AS TotProvvigione_1E,
                    dorig.x_ore,
                     0 as TotImponibileE
                FROM dotes 
                LEFT JOIN dorig ON dorig.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'PRV'
                 AND dotes.Cd_Agente_1 = '$cd_agente'
                UNION ALL
                
                SELECT 
                    dotes.DataDoc,
                    DOTotali.TotDocumentoE,
                    DOTotali.TotProvvigione_1E,
                    NULL AS x_ore,
                       DOTotali.TotImponibileE
                FROM dotes
                LEFT JOIN DOTotali ON DOTotali.Id_DoTes = dotes.Id_DoTes
                WHERE dotes.Cd_Do = 'FTV'
                 AND dotes.Cd_Agente_1 = '$cd_agente'
            ) AS unione
            GROUP BY FORMAT(DataDoc, 'yyyy-MM')
            ORDER BY FORMAT(DataDoc, 'yyyy-MM') asc
        ")
                 
        ->queryAll();


       


        return $this->render('index', ['rows' => $rows]);
}


public function actionResetagente()
{
    $session = Yii::$app->session;
    $cd_agente = Yii::$app->user->identity->cd_agente;

    $session->remove('cd_agente_filtrato');
    $session->set('cd_agente_filtrato', $cd_agente);

    return $this->redirect(['statistiche/index']);
}


    public function actionGenfile($id, $filename = null)
    {

       // $this->getuser();
        $tmpfile =Dmsdocument::find()
        ->where(['EntityId' => $id])
        ->andWhere(['EntityTable'=>'DOTES'])
        ->andWhere(['DmsClass3'=>'FTV'])
        ->andWhere(['FileExt'=>'PDF'])
        ->asArray()->one();
        //AllFiles::find()->where(['id' => $id])->asArray()->one();

$id = (int)$id; // sicurezza base, anche se è già un numero

$sql = "
    SELECT TOP 1 *
    FROM ADB_AUXCOOP.dbo.DmsDocument
    WHERE EntityId = :id
      AND EntityTable = 'DOTES'
      AND DmsClass3 = 'FTV'
      AND FileExt = 'PDF'
";

$tmpfile = Yii::$app->db5
    ->createCommand($sql, [':id' => $id])
    ->queryOne();


       //var_dump($tmpfile);
       Yii::warning(['queryResult' => $tmpfile, 'id' => $id], 'debug.genfile');
       if (!$tmpfile) {
        throw new \yii\web\NotFoundHttpException("Documento non trovato per ID $id");
    }
        try {
            $path = Yii::getAlias('@webroot') . '/uploads/';
            $file = str_replace(' ', '_', $tmpfile['FileName']);
            $file2 = $path . $tmpfile['EntityId'] . '_' . $file;
            //yii::warning
            //         var_dump($tmpfile['nome_file']);

            if (!is_null($tmpfile['FileName'])) {

                $tmf = fopen($file2, 'w');
                fwrite($tmf, (
                    $tmpfile['Content']));
                fclose($tmf);

                $tmpf =
                Yii::$app->response->SendFile(
                    $file2,
                    $file,
                     ['mimeType' => 'application/pdf',
                'inline' => true,]
                    // file_get_contents($file2, FILE_USE_INCLUDE_PATH)
                    // 'application/pdf'
                )->send();
                register_shutdown_function(function() use ($file2) {
            @unlink($file2);
        });
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        ob_clean();
        //unlink($file2);
        // return $tmpf;//    file_get_contents( $file2 );
        //return 'stocazzo';

    }

    public function actionDownload($id)
{
    $model =Agentifiles::findOne($id);

        // Scegli quale campo contiene il file binario
        $content = $model->f_content;

    if (!$content) {
        throw new \yii\web\NotFoundHttpException('Il file non è disponibile.');
    }

    // Nome file con estensione
    $filename = $model->nome_file;
    if (stripos($filename, '.' . $model->estenzione) === false) {
        $filename .= '.' . $model->estenzione;
    }

    // Restituisce il file per il download
    return Yii::$app->response->sendContentAsFile(
        $content,
        $filename,
        [
            'mimeType' => $this->getMimeType($model->estenzione),
            'inline' => false, // true se vuoi aprirlo nel browser
        ]
    );
}

/**
 * Restituisce il mime type in base all’estensione
 */
private function getMimeType($ext)
{
    $ext = strtolower($ext);
    $map = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'txt' => 'text/plain',
    ];

    return $map[$ext] ?? 'application/octet-stream';
}
public function actionUploadFile()
{
    $model = new Agentifiles();

    // Imposto automaticamente il codice agente loggato
    $model->cd_agente = Yii::$app->user->identity->cd_agente ?? null;

    if ($model->load(Yii::$app->request->post())) {
        $file = \yii\web\UploadedFile::getInstance($model, 'uplfile');

        if ($file) {
            $model->nome_file = $file->baseName;
            $model->estenzione = $file->extension;
            $model->f_content = file_get_contents($file->tempName);
            $model->cartella = $model->cartella ?? 'Root';
            $model->cartella_padre = $model->cartella_padre ?? null;
    
        }

        if ($model->save(false)) {
            Yii::$app->session->setFlash('success', 'File caricato con successo.');
            return $this->redirect(['statistiche/index']);
        } else {
            Yii::$app->session->setFlash('error', 'Errore durante il salvataggio.');
        }
    }

    return $this->renderAjax('upload-file', [
        'model' => $model,
    ]);
}
public function actionCartelle()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    $out = [];

    $cartelle = [
        'Retribuzioni' => [
            'C1' => [],
            'Buste paga' => [],
            'Cud' => []
        ],
        'Documenti Personali' => [
            'Carta Identità' => [],
            'Passaporto' => [],
            'Tessera Sanitaria' => []
        ],
        'Documentazione' => [
            'Contratti' => [],
            'Attestati' => [],
            'Dpi' => [],
            'Visite Mediche' => []
        ],
        'C1' => [
            '2025'=>[],'2026'=>[],'2027'=>[],'2028'=>[],'2029'=>[],'2030'=>[],
            '2031'=>[],'2032'=>[],'2033'=>[],'2034'=>[],'2035'=>[]
        ],
        'Buste paga' => [
                '2025'=>[],'2026'=>[],'2027'=>[],'2028'=>[],'2029'=>[],'2030'=>[],
            '2031'=>[],'2032'=>[],'2033'=>[],'2034'=>[],'2035'=>[]     ],
        'Cud' => [
                     '2025'=>[],'2026'=>[],'2027'=>[],'2028'=>[],'2029'=>[],'2030'=>[],
            '2031'=>[],'2032'=>[],'2033'=>[],'2034'=>[],'2035'=>[]     ],
    ];

    if (isset($_POST['depdrop_parents'])) {
        $parents = $_POST['depdrop_parents'];

        if ($parents != null) {
            $padre = $parents[0];       // livello 1 (es. "Retribuzioni")
            $figlio = $parents[1] ?? null; // livello 2 (es. "C1")

            // Se siamo al primo livello
            if (isset($cartelle[$padre])) {
                // Se è stato scelto anche un secondo livello (es. "C1")
                if ($figlio && isset($cartelle[$padre][$figlio])) {
                    // Mostra gli anni
                    foreach ($cartelle[$padre][$figlio] as $anno) {
                        $out[] = ['id' => $anno, 'name' => $anno];
                    }
                } else {
                    // Mostra le sotto-cartelle (chiavi)
                    foreach ($cartelle[$padre] as $sotto => $anni) {
                        $out[] = ['id' => $sotto, 'name' => $sotto];
                    }
                }

                return ['output' => $out, 'selected' => ''];
            }
        }
    }
    return ['output' => '', 'selected' => ''];
}

 

}
