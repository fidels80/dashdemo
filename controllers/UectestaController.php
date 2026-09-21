<?php

namespace app\controllers;

use Yii;
use app\models\Uectesta;
use app\models\UectestaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\Uecrighe;
use yii\helpers\StringHelper;
use app\models\Uecanagrafica;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\Uecarticoli;
use yii\db\Expression;
/**
 * UectestaController implements the CRUD actions for Uectesta model.
 */
class UectestaController extends Controller
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
     * Lists all Uectesta models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->getuser('Elenco Documenti');
        $searchModel = new UectestaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Uectesta model.
     * @param string $id
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
     * Creates a new Uectesta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionXcreate()
    {
        $model = new Uectesta();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Uectesta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Uectesta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Uectesta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Uectesta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Uectesta::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionCreate()
    {
        $this->getuser('Crea Documento');
        $modelHead = new Uectesta();
        $modelsRow = [new Uecrighe]; // Inizializza un array con un modello di riga vuoto
        $tnum = Uectesta::find()->max('numero') + 1;
       // $this->getuser();
  
        if ($modelHead->load(Yii::$app->request->post())) {
          // return  var_dump(Yii::$app->request->post()); 
            $modelsRow1 = Yii::$app->request->post('Uectesta')['righe']; // Ottieni i dati del campo docRows dal post
            //$modelsRow = Model::createMultiple(Doc_Rows::classname(), $modelsRow1); // Inizializza l'array di modelli figlio e carica i dati
            $modelsRow = $modelsRow1;
             //  return var_dump($modelsRow);
 
            $modelHead->numero = $tnum;
            if (!$modelHead->validate()){
                return var_dump($modelHead->getErrors()); 
            }
            $flag2 = false;
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $i = 1;
                if ($flag = $modelHead->save()) {
                    foreach ($modelsRow as $modelsRow_one) {

                        $modelsRow_ = new Uecrighe();
                        $modelsRow_->id_testa=$modelHead->id;
                        $modelsRow_->articolo = $modelsRow_one['articolo'];
                        $modelsRow_->nota = $modelsRow_one['nota'];
                        $modelsRow_->qta = $modelsRow_one['qta'];
                        $modelsRow_->prezzo = $modelsRow_one['prezzo'];
                         
                        // die(  print_r($modelsRow_));
                        if (!$modelsRow_->validate()) {
                            return var_dump($modelsRow_->getErrors());
                        }
                        if (!($flag2 = $modelsRow_->save())) {
                            $transaction->rollBack();
                            return $modelsRow_->error;

//                            //die($modelsRow_one->errors);
          break;
                      }
      
    $i = $i + 1;
                    }
                }else{
                    return var_dump($modelHead->getErrors());  
                }
                if ($flag && $flag2) {
                    $transaction->commit();
                    //return $this->redirect(['index']);
        return $this->render('view', [
            'model' => $this->findModel($modelHead->id),]);
                } else {
                    //return var_dump($modelsRow_->error);
                }
            } catch (Exception $e) {
                $transaction->rollBack();
            }
        }
        return $this->render('create', [
            'model' => $modelHead,
            'items' => $modelsRow,
            'tnum'  =>  $tnum
        ]);
    
            
    }

    public function actionWORKCreate()
    {
        $this->getuser();
        $modelHead = new Doc_head();
        $modelsRow = [new Doc_Rows];
        if ($modelHead->load(Yii::$app->request->post())) {
            $modelsRow1 = Yii::$app->request->post('Doc_head')['docRows']; // Ottieni i dati del campo docRows dal post
            //$modelsRow = Model::createMultiple(Doc_Rows::classname(), $modelsRow1); // Inizializza l'array di modelli figlio e carica i dati
            $modelsRow = $modelsRow1;
            //   return var_dump($modelsRow);
            $mdoc = yii::$app->db
                ->createCommand('select max(numdoc) as m from doc_head
where cd_doc=:PRV
                        ')->bindValues([':PRV' => $modelHead->cd_doc]);
            $mdoc_r            = $mdoc->queryAll();
            $modelHead->numdoc = strval($mdoc_r[0]['m'] + 1);
            $modelHead->validate();
            $flag2 = false;
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $i = 1;
                if ($flag = $modelHead->save()) {
                    foreach ($modelsRow as $modelsRow_one) {

                        $modelsRow_ = new Doc_Rows();

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
                        $modelsRow_->prz_tot = $modelsRow_one['totale'];



                        $modelsRow_->validate();
                        // return  $modelsRow_one->save();

                        if (!($flag2 = $modelsRow_->save())) {
                            $transaction->rollBack();
                            return $modelsRow_->error;

                            //die($modelsRow_one->errors);
                            break;
                        }
                        $i = $i + 1;
                    }
                }
                if ($flag && $flag2) {
                    $transaction->commit();
                    return $this->redirect(['index']);
                } else {
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


    public function actionPdf($id)
    {
        $model = $this->findModel($id); // Trova il modello basato sull'id
        $cli = Uecanagrafica::find()
            ->select(['nome', 'cognome'])
            ->where(['id' => $model->cliente])
            ->asArray()
            ->one();

        $tdoc = Uecrighe::find()
            ->where(['id_testa' => $model->id])
            ->asArray()
            ->all();

        $sumdoc = 0;
        foreach ($tdoc as $value) {
            $sumdoc += $value['prezzo'];
        }

        $content = $this->renderPartial('_pdf', [
            'model' => $model,
            'cli' => $cli,
            'tdoc' => $tdoc,
            'sumdoc' => $sumdoc,
        ]);

        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'fontDir' =>
            array_merge((new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
             [
                'C:\Windows\Fonts',
            ]),
            'fontdata' => [
                'sans' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
        ]);
        $pdf->useSubstitutions = false;
        $pdf->showImageErrors = false;
        $pdf->WriteHTML($content);
        return $pdf->Output($id.'.pdf', \Mpdf\Output\Destination::INLINE);
    }


    public function actionExport()
    {
        $this->getuser('Export Genya');
        // Recupera i dati da esportare
        $toexp = Yii::$app->db->createCommand("
    SELECT uec_testa.id AS id,
           uec_testa.data,
           uec_righe.prezzo AS dare,
           uec_righe.prezzo AS avere,
           'D' AS dareavere,
           'Ricevuta' AS tipomov,
           uec_righe.articolo AS desConto
    FROM uec_testa
    LEFT JOIN uec_righe ON uec_righe.id_testa = uec_testa.id
    WHERE uec_testa.esportato = 0
")->queryAll();
        // Passa i dati alla vista '_export'
        return $this->render('_export', [
            'toexp' => $toexp,
        ]);
    }

    public function actionGetta()
    {
        $toexp = Yii::$app->db->createCommand("
    SELECT uec_testa.id AS id,
           uec_testa.data,
           uec_righe.prezzo AS dare,
           uec_righe.prezzo AS avere,
           'D' AS dareavere,
           'Ricevuta' AS tipomov,
           uec_righe.articolo AS desConto
    FROM uec_testa
    LEFT JOIN uec_righe ON uec_righe.id_testa = uec_testa.id
    WHERE uec_testa.esportato = 0
")->queryAll();

        if (empty($toexp)) {
            Yii::$app->session->setFlash('info', 'Non ci sono dati da esportare.');
            return $this->redirect(['export']);
        }

        // Esporta in Excel e restituisci il file
        $filePath = $this->exportToExcelFile($toexp);
        Uectesta::updateAll(['esportato' => true], ['esportato' => false]);

        return Yii::$app->response->sendFile($filePath)->send();
    }


    public function actionExportDownload($file)
    {
        $filePath = Yii::getAlias('@webroot/exports/' . $file);

        if (file_exists($filePath)) {
            return Yii::$app->response->sendFile($filePath);
        } else {
            Yii::$app->session->setFlash('error', 'File non trovato.');
            return $this->redirect(['export']);
        }
    }

    // Metodo per generare il file Excel
    protected function exportToExcelFile($data)
    {
        $filePath = Yii::getAlias('@webroot/exports/Export.xlsx');
        // Codice per creare il file Excel (usando ad esempio PhpSpreadsheet o simile)

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Data');
        $sheet->setCellValue('C1', 'Dare');
        $sheet->setCellValue('D1', 'Avere');
        $sheet->setCellValue('E1', 'DareAvere');
        $sheet->setCellValue('F1', 'TipoMov');
        $sheet->setCellValue('G1', 'DesConto');

        $row = 2;
        foreach ($data as $item) {
            $sheet->setCellValue("A{$row}", $item['id']);
            $sheet->setCellValue("B{$row}", $item['data']);
            $sheet->setCellValue("C{$row}", $item['dare']);
            $sheet->setCellValue("D{$row}", $item['avere']);
            $sheet->setCellValue("E{$row}", $item['dareavere']);
            $sheet->setCellValue("F{$row}", $item['tipomov']);
            $sheet->setCellValue("G{$row}", $item['desConto']);
            $row++;
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }




    public function actionXPdf($id)
    {
        $model = $this->findModel($id); // Trova il modello basato sull'id
        $cli = Uecanagrafica::find()
            ->select(['nome', 'cognome'])
            ->where(['id' => $model->cliente])
            ->asArray()
            ->one();

        $tdoc = Uecrighe::find()
            ->where(['id_testa' => $model->id])
            ->asArray()
            ->all();

        $sumdoc = 0;
        foreach ($tdoc as $value) {
            $sumdoc += $value['prezzo'];
        }

        // Carica il contenuto da una vista parziale
        $content = $this->renderPartial('_pdf', [
            'model' => $model,
            'cli' => $cli,
            'tdoc' => $tdoc,
            'sumdoc' => $sumdoc,
        ]);

        // Crea un nuovo oggetto TCPDF
        $pdf = new \TCPDF();

        // Impostazioni del PDF
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Il tuo Nome');
        $pdf->SetTitle('Documento PDF');
        $pdf->SetSubject('Oggetto del documento');
        $pdf->SetKeywords('PDF, Yii2, TCPDF');

        // Rimuove intestazioni e piè di pagina di default (opzionale)
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Imposta il margine (sinistro, alto, destro)
        $pdf->SetMargins(15, 10, 15);

        // Aggiungi una pagina
        $pdf->AddPage();

        // Scrivi il contenuto HTML nel PDF
        $pdf->writeHTML($content, true, false, true, false, '');
        file_put_contents(Yii::getAlias('@runtime') . '/debug.html', $content);


        // Ritorna il PDF generato inline
        return $pdf->Output($id . '.pdf', 'I'); // 'I' per visualizzarlo nel browser, 'D' per forzare il download
    }


    public function getuser($xmodulo)
    {
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
                    if ($value == $xmodulo) {
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
}
