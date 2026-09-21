<?php

namespace app\controllers;

use Yii;
use app\models\Xtravelrow;
use app\models\XtravelrowSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\models\Xtravelhead;
use yii\db\Expression;
/**
 * XtravelrowController implements the CRUD actions for Xtravelrow model.
 */
class XtravelrowController extends Controller
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
     * Lists all Xtravelrow models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new XtravelrowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Xtravelrow model.
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
     * Creates a new Xtravelrow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Xtravelrow();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->tr_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


 


    /**
     * Updates an existing Xtravelrow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionUpdate($id, $isajax = null)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            // check_in
            if (!empty($model->check_in)) {
                $dateTime = new \DateTime($model->check_in);
                $model->check_in = $dateTime->format('d-m-Y H:i:s'); // ISO per DB
            }

            // check_out
            if (!empty($model->check_out)) {
                $dateTime2 = new \DateTime($model->check_out);
                $model->check_out = $dateTime2->format('d-m-Y H:i:s'); // ISO per DB
            }

            // data_pg
            if (!empty($model->data_pg)) {
                $dateTime3 = new \DateTime($model->data_pg);
                $model->data_pg = $dateTime3->format('d-m-Y H:i:s'); // ISO per DB
            }

            if ($model->save()) {
                //return $this->redirect(['view', 'id' => $model->tr_id]);
                return $this->redirect(['xtravelhead/masterhotel', 'id' => $model->th_id]);
            }
        }

        if ($isajax == 1) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $this->layout = false;
            return $this->renderAjax('update2', [
                'model' => $model,
            ]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }


    public function old_actionUpdate($id,$isajax=null)
    {
        $model = $this->findModel($id);

        




        if ($model->load(Yii::$app->request->post())  ) {
            $data_tappa_raw = $model->check_in;
            $dateTime = new \DateTime($data_tappa_raw);

            // Prova diversi formati

            $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito
            $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
            $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
            $data_tappa = $formato2; // o qualsiasi altro formato che hai verificato funzionare
            if (!empty($model->check_in)) {

                $model->check_in = isset($data_tappa) ?
                    $data_tappa : $model->check_in;
            } else {
                $model->check_in = $model->check_in;;
            }
            $data_tappa_raw2 = $model->check_out;
            $dateTime2 = new \DateTime($data_tappa_raw2);

            $formato2a = $dateTime2->format('d-m-Y H:i:s');
            $data_tappa2 = $formato2a; // o qualsiasi altro formato che hai verificato funzionare

            if (!empty($model->check_out)) {
                $model->check_out = isset($data_tappa2) ?   
                  $data_tappa2 : $model->check_out;
            } else {
                $model->check_out = $model->check_out;
            }

            $data_tappa_raw3 = $model->data_pg;
            $dateTime3 = new \DateTime($data_tappa_raw3);

            $formato2aa = $dateTime3->format('d-m-Y H:i:s');
            $data_tappa3 = $formato2aa; // o qualsiasi altro formato che hai verificato funzionare

            if (!empty($model->data_pg)) {
                $model->check_out = isset($data_tappa3) ?
                    $data_tappa3 : $model->data_pg;
            } else {
                $model->data_pg = $model->data_pg;
            }
            
            
            if ($model->save()){
   
     
            return $this->redirect(['view', 'id' => $model->tr_id]);
      

    }
}
        if ($isajax==1){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $this->layout = false;
            return $this->renderAjax('update2', [
                'model' => $model,
            ]);
        }else{
        return $this->render('update', [
            'model' => $model,
        ]);
    }



    }

    /**
     * Deletes an existing Xtravelrow model.
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
     * Finds the Xtravelrow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Xtravelrow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Xtravelrow::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionUpdateAjax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            return ['success' => false, 'message' => 'Richiesta non valida'];
        }

        $id = $request->post('id');
        $data = $request->post('data');

        if (!$id || !$data) {
            return ['success' => false, 'message' => 'Dati mancanti'];
        }

        $model = Xtravelrow::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Record non trovato'];
        }

        // --- 1. ASSEGNAZIONE CAMPI BASE ---
        $model->guest         = $data['guest'] ?? $model->guest;
        $model->ruolo         = $data['ruolo'] ?? $model->ruolo;
        $model->party         = $data['party'] ?? $model->party;
        $model->sottocommessa = $data['sottocommessa'] ?? $model->sottocommessa;
        $model->struttura     = $data['struttura'] ?? $model->struttura;
        $model->citta         = $data['citta'] ?? $model->citta;
        $model->citta_da      = $data['citta_da'] ?? $model->citta_da;
        $model->citta_a       = $data['citta_a'] ?? $model->citta_a;
        $model->cd_Ar         = $data['cd_Ar'] ?? $model->cd_Ar;
        $model->cd_cf_ft      = $data['cd_cf_ft'] ?? $model->cd_cf_ft;
        $model->fornitore     = $data['fornitore'] ?? $model->fornitore;
        $model->pnr           = $data['pnr'] ?? $model->pnr;
        $model->nr_biglietto  = $data['nr_biglietto'] ?? $model->nr_biglietto;
        $model->codiva        = $data['codiva'] ?? $model->codiva;
        $model->cd_pg         = $data['cd_pg'] ?? $model->cd_pg;
        $model->pagato = (!empty($rowData['pagato']) && $rowData['pagato'] !== 'false'
            && $rowData['pagato'] !== 0) ? 1 : 0;            // Numeri
        $model->stato = $data['stato'] ?? $model->stato;
        // --- 2. GESTIONE DATE ---
        $dateFields = ['check_in', 'check_out', 'data_pg'];
        foreach ($dateFields as $field) {
            if (!empty($data[$field])) {
                try {
                    $model->$field = (new \DateTime($data[$field]))->format('Ymd H:i:s');
                } catch (\Exception $e) {
                    $model->$field = null;
                }
            } else {
                $model->$field = null;
            }
        }

        // --- 3. VALORI NUMERICI PER CALCOLI ---
        $codiciPagamento = ['ACCONTO HTL', 'SALDO HTL', 'PAG'];
        if (!in_array($model->cd_Ar, $codiciPagamento)) {
        $model->qta      = isset($data['qta']) ? (float)$data['qta'] : $model->qta;
        }else{
            $model->qta      = 1;
        }


        
        $model->prezzo   = isset($data['prezzo']) ? (float)$data['prezzo'] : $model->prezzo;
        $model->tax_unit = isset($data['tax_unit']) ? (float)$data['tax_unit'] : $model->tax_unit;
        $model->fee      = isset($data['fee']) ? (float)$data['fee'] : $model->fee;
        $model->fee_perc = isset($data['fee_perc']) ? (float)$data['fee_perc'] : $model->fee_perc;

        // --- 4. RECUPERO DATI ESTERNI (ALIQUOTA E CLASSE) ---
        $aliquotaValore = (new \yii\db\Query())
            ->select(['Aliquota'])
            ->from('adb_auxcoop.dbo.Aliquota')
            ->where(['Cd_Aliquota' => $model->codiva])
            ->scalar(Yii::$app->db5) ?: 22;

        $Cd_ARClasse12 = (new \yii\db\Query())
            ->select(['Cd_ARClasse12'])
            ->from('adb_auxcoop.dbo.ar')
            ->where(['cd_ar' => $model->cd_Ar])
            ->scalar(Yii::$app->db5);

        // --- 5. LOGICA CALCOLI (ALLINEATA A JS) ---
        $tax_totale = $model->tax_unit * $model->qta;
        $prezzo_totale_servizio = $model->prezzo * $model->qta;
        $totale_parziale = $prezzo_totale_servizio + $tax_totale; // Base per calcolo fee (non ivata)
        if ($Cd_ARClasse12 == 'TRVACC') {
            // Ricalcola importo FEE dalla percentuale
           if ($model->cd_Ar=='ACC_FEE_FUORIORA'){
                if ($totale_parziale > 0) {
                    $model->fee_perc = ($model->fee * 100) / $totale_parziale;
                }
           }
           else{
            $model->fee = ($totale_parziale * $model->fee_perc) / 100;

           }

        } elseif ($Cd_ARClasse12 == 'TRVBIG') {
            // Ricalcola PERCENTUALE dall'importo FEE (per non lasciarla a 0)
            if ($totale_parziale > 0) {
                $model->fee_perc = ($model->fee * 100) / $totale_parziale;
            }
        }

        // B. Scorporo Servizio
        $ximp = ($prezzo_totale_servizio * 100) / (100 + $aliquotaValore);
        $xiva = $prezzo_totale_servizio - $ximp;

        // C. IVA sulla FEE (22% come da logica VFP)
        $xfeeiva = ($model->fee <> 0) ? ($model->fee * 0.22) : 0;

        // D. Totali Finali
        $model->imponibile = round($ximp + $model->fee, 3);
        $model->iva = round($xiva + $xfeeiva, 3); // Somma IVA Servizio + IVA Fee
        $model->totale = round($prezzo_totale_servizio, 3);
        $model->Totalegenerale = round($model->imponibile + $model->iva + $tax_totale, 3);

        // Il totfattura in VFP è Imponibile Totale + Tasse (l'IVA è esclusa dal campo totfattura di solito)
        // ma se vuoi che coincida con il pagamento:
        $model->totfattura = round($model->imponibile + $tax_totale, 3);

        $model->duseru = Yii::$app->user->id;

        // --- 6. SALVATAGGIO ---
        if ($model->save()) {
            $model->refresh();
            return [
                'success' => true,
                'message' => 'Record aggiornato con successo',
                'model' => $model->attributes
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Errore durante il salvataggio',
                'errors' => $model->errors
            ];
        }
    }
    public function actionUpdateAjax_old()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        if (!$request->isAjax) {
            return ['success' => false, 'message' => 'Richiesta non valida'];
        }

        $id = $request->post('id');
        $data = $request->post('data');

        if (!$id || !$data) {
            return ['success' => false, 'message' => 'Dati mancanti'];
        }

        $model = Xtravelrow::findOne($id);
        if (!$model) {
            return ['success' => false, 'message' => 'Record non trovato'];
        }

        // Aggiorna i campi del modello con i dati ricevuti
        $model->guest = isset($data['guest']) ? $data['guest'] : $model->guest;
        $model->ruolo = isset($data['ruolo']) ? $data['ruolo'] : $model->ruolo;
        $model->party = isset($data['party']) ? $data['party'] : $model->party;
        $model->sottocommessa = isset($data['sottocommessa']) ? $data['sottocommessa'] : $model->sottocommessa;
        $model->struttura = isset($data['struttura']) ? $data['struttura'] : $model->struttura;
        $data_tappa_raw = $data['check_in'];
        $dateTime = new \DateTime($data_tappa_raw);

        // Prova diversi formati
         
        $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito
        $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
        $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
        $data_tappa = $formato2; // o qualsiasi altro formato che hai verificato funzionare
        
        if (!empty($data['check_in'])) {
        $model->check_in = isset($data_tappa) ?     $data_tappa : $model->check_in;
        }else
        {
            $model->check_in= $data['check_in'];
        }

        $data_tappa_raw2c = $data['check_out'];
        $dateTime2c = new \DateTime($data_tappa_raw2c);
        $formato2ac = $dateTime2c->format('d-m-Y H:i:s');  // Europeo
        // Prova diversi formati
        $data_tappa2c = $formato2ac;
        // Invertito
        if (!empty($data['check_out'])) {
            $model->check_out = isset($data_tappa2c) ?  
            $data_tappa2c : $model->check_out;
        }else
        {
            $model->check_out= $data['check_out'];
        }


        $model->citta = isset($data['citta']) ? $data['citta'] : $model->citta;
        $model->citta_da = isset($data['citta_da']) ? $data['citta_da'] : $model->citta_da;
        $model->citta_a = isset($data['citta_a']) ? $data['citta_a'] : $model->citta_a;
        $model->qta = isset($data['qta']) ? $data['qta'] : $model->qta;
        $model->stato = isset($data['stato']) ? $data['stato'] : $model->stato;
        $model->cd_Ar = isset($data['cd_Ar']) ? $data['cd_Ar'] : $model->cd_Ar;
        $model->prezzo = isset($data['prezzo']) ? $data['prezzo'] : $model->prezzo;
        $model->tax_unit = isset($data['tax_unit']) ? $data['tax_unit'] : $model->tax_unit;
        $model->pnr = isset($data['pnr']) ? $data['pnr'] : $model->pnr;
        $model->nr_biglietto = isset($data['nr_biglietto']) ? $data['nr_biglietto'] : $model->nr_biglietto;
        //$model->data_pg = isset($data['data_pg']) ? $data['data_pg'] : $model->data_pg;
        $model->cd_pg = isset($data['cd_pg']) ? $data['cd_pg'] : $model->cd_pg;
        //  $model->fee_perc = isset($data['fee_perc']) ? $data['fee_perc'] : $model->fee_perc;
        //  $model->fee = isset($data['fee']) ? $data['fee'] : $model->fee;
        $imponibile    = isset($data['imponibile']) ? (float)$data['imponibile'] : 0;

        // Recuperiamo i valori attuali dal database (se il modello esiste) per il confronto
        $oldFeePerc = round((float)$model->fee_perc, 2);
        $oldFee     = round((float)$model->fee, 2);
        $newFeePerc = isset($data['fee_perc']) ? round((float)$data['fee_perc'], 2) : 0;
        $newFee     = isset($data['fee']) ? round((float)$data['fee'], 2) : 0;
        $tnewFee     = isset($data['fee']) ? round((float)$data['fee'], 2) : 0;

        if ($newFeePerc != $oldFeePerc) {
            // Se è cambiata la percentuale, ricalcoliamo l'importo della fee
            $model->fee_perc = $newFeePerc;
            $model->fee = round(($imponibile * $newFeePerc) / 100, 2);
        } elseif ($newFee != $oldFee) {
            // Se è cambiata la fee fissa, ricalcoliamo la percentuale (se l'imponibile lo permette)
            $model->fee = $newFee;
            if ($imponibile > 0) {
                $model->fee_perc = round(($newFee / $imponibile) * 100, 2);
            } else {
                $model->fee_perc = 0;
            }
        } else {
            // Se non è cambiato nulla o sono cambiati entrambi (raro), assegniamo i valori ricevuti
            $model->fee_perc = $newFeePerc;
            $model->fee = $newFee;
        }
      
      
      $model->codiva = isset($data['codiva']) ? $data['codiva'] : $model->codiva;
        $model->pagato = isset($data['pagato']) ? $data['pagato'] : $model->pagato; 
        $model->totale = isset($data['totale']) ? $data['totale'] : $model->totale;
        $model->imponibile = isset($data['imponibile']) ? $data['imponibile'] : $model->imponibile;
        $model->iva = isset($data['iva']) ? $data['iva'] : $model->iva;
        $model->Totalegenerale = isset($data['totalegenerale']) ? $data['totalegenerale'] : $model->Totalegenerale;
        $model->totfattura = isset($data['totfattura']) ? $data['totfattura'] : $model->totfattura;
        $model-> duseru = Yii::$app->user->id;
        $model->cd_cf_ft= isset($data['cd_cf_ft']) ? $data['cd_cf_ft'] : $model->cd_cf_ft;
        $model->fornitore = isset($data['fornitore']) ? $data['fornitore'] : $model->fornitore;
        $model->cd_pg = isset($data['cd_pg']) ? $data['cd_pg'] : $model->cd_pg;
        //$model->check_in = isset($data['check_in']) ? $data['check_in'] : $model->check_in;
        //$model->check_out = isset($data['check_out']) ? $data['check_out'] : $model->check_out;
        //$model->data_pg = isset($data['data_pg']) ? $data['data_pg'] : $model->data_pg;
        
        if (!empty($data['data_pg'])) {
            $model->data_pg = (new \DateTime($data['data_pg']))->format('d-m-Y H:i:s');
        }


        $Cd_ARClasse12 = (new \yii\db\Query())
            ->select(['Cd_ARClasse12'])
            ->from('adb_auxcoop.dbo.ar')
            ->where(['cd_ar' => $model->cd_Ar]) // fornitore è cd_cf_ft immagino
            ->scalar(Yii::$app->db5);


        if ($Cd_ARClasse12 == 'TRVACC') {
            // Se è cambiata la percentuale, ricalcoliamo l'importo della fee
          //  $model->fee_perc = $newFeePerc;
            // ($model->prezzo * $model->qta   + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100


            //$model->fee = round(($rowData['totale'] /100)* $newFeePerc  ,2);
            $model->fee =  ($model->prezzo * $model->qta
                + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100;
        }elseif ($Cd_ARClasse12 == 'TRVBIG')
        {
            $model->fee_perc=0;
            $model->fee= $tnewFee;
        }

        $aliquotaServizio = (new \yii\db\Query())
            ->select(['Aliquota'])
            ->from('Aliquota')
            ->where(['Cd_Aliquota' => $model->codiva])
            ->scalar(Yii::$app->db5) ?: 22;
        $prezzo_totale_servizio = $model->prezzo * $model->qta;
        $tax_totale = $model->tax_unit * $model->qta;
        $ximp = ($prezzo_totale_servizio * 100) / (100 + $aliquotaServizio);
        // Corrisponde a afn_Scorporo_GetImposta
        $xiva = $prezzo_totale_servizio - $ximp;
        $vfee = (float)$model->fee; // Prende la fee fissa se perc è 0
        $xfeeiva = ($vfee <> 0) ? ($vfee * 0.22) : 0;

        $model->fee = round($vfee, 3);
        $model->imponibile = round($ximp + $vfee, 3); // ximp + vfee
        $model->iva = round($xiva + $xfeeiva, 3);    // xiva + iva della fee
        $model->Totalegenerale = round($model->imponibile + $model->iva + $tax_totale, 3);
        $model->totfattura = round($model->imponibile + $tax_totale, 3);
        $model->totale = round($prezzo_totale_servizio, 3);

        // Salva il modello
        if ($model->save()) {
            $model->refresh(); // Ricarica il modello dal DB per avere i valori aggiornati dal beforeSave
            return [
                'success' => true,
                'message' => 'Record aggiornato con successo',
                'model' => $model->attributes,
               // 'data_tappa' => $data_tappa,
               // 'data_tappa2' => $data_tappa2c,
                'cd_Ar' => $data['cd_Ar'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Errore durante il salvataggio',
                'errors' => $model->errors
            ];
        }
    }


    public function actionCreateAjax($tappa)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Verifica se la richiesta è AJAX
        if (!Yii::$app->request->isAjax) {
            return [
                'success' => false,
                'message' => 'Richiesta non valida. È richiesta una richiesta AJAX.'
            ];
        }

        // Ottieni i dati dalla richiesta POST
        $postData = Yii::$app->request->post('data', []);

        // Crea un nuovo modello
        $model = new Xtravelrow();

        // Assegna gli attributi dal post data
        // Nota: mappare esplicitamente i campi per sicurezza
        $model->th_id = $postData['tr_head'] ?? null;
        //$model->datah = null;

        if ($model->th_id !== null) {
            $testa = Xtravelhead::findOne($model->th_id);
            if ($testa && $testa->datath) {
                //  $model->datah = $testa->datath;
                $model->datah =  \DateTime::createFromFormat('Y-m-d H:i:s',  ($testa->datath));
            }
        }

        $model->guest = $postData['guest'] ?? '';
        $model->ruolo = $postData['ruolo'] ?? '';
        $model->party = $postData['party'] ?? '';
        $model->struttura = $postData['struttura'] ?? '';
        $model->citta = $postData['citta'] ?? '';
        $model->citta_da = $postData['citta_da'] ?? '';
        $model->citta_a = $postData['citta_a'] ?? '';
        //$model->check_in = $postData['check_in'] ?? null;
        //$model->check_out = $postData['check_out'] ?? null;
        $model->check_in =null;// trim($postData['check_in'] ?? '') !== '' ? $postData['check_in'] : null;
       $model->check_out = null;//trim($postData['check_out'] ?? '') !== '' ? $postData['check_out'] : null;

        $model->qta = $postData['qta'] ?? 0;
        $model->cd_Ar = $postData['cd_Ar'] ?? '';
        $model->prezzo = $postData['prezzo'] ?? 0;
        $model->tax_unit = $postData['tax_unit'] ?? 0;
        $model->pnr = $postData['pnr'] ?? '';
        $model->nr_biglietto = $postData['nr_biglietto'] ?? '';
        //$model->data_pg = $postData['data_pg'] ?? null;
        $model->cd_pg = $postData['cd_pg'] ?? '';
        $model->fee_perc = $postData['fee_perc'] ?? 0;
        $model->fee = $postData['fee'] ?? 0;
        $model->codiva = $postData['codiva'] ?? '';
        $model->duseri = Yii::$app->user->id;
        $model->stato = $postData['stato'] ?? '';
        // Imposta i campi di audit
        //$model->utente_ins = Yii::$app->user->identity->username ?? 'sistema';
        //$model->data_ins = new \yii\db\Expression('NOW()');
        $model->data_pg = null;// trim($postData['data_pg'] ?? '') !== '' ? $postData['data_pg'] : null;
        // Salva il modello
        $model->id_tappa= $tappa;
        try {
            if ($model->save()) {
                // Prepara i dati della riga per il ritorno
                $newRowData = $model->attributes;

                // Assicuriamoci che il tr_id sia disponibile per l'uso sul client
                $newRowData['tr_id'] = $model->tr_id ?? $model->getPrimaryKey();

                return [
                    'success' => true,
                    'message' => 'Riga creata con successo',
                    'data' => $newRowData
                ];
            } else {
                // Log degli errori di validazione
                Yii::error('Errori di validazione: ' . Json::encode($model->errors), 'xtravelrow');

                return [
                    'success' => false,
                    'message' => 'Errore durante il salvataggio: ' . implode(', ', array_map(function ($errors) {
                        return implode(', ', $errors);
                    }, $model->errors)),
                    'errors' => $model->errors,
                    'postdata' => $postData
                ];
            }
        } catch (\Exception $e) {
            // Log dell'eccezione
            Yii::error('Eccezione durante la creazione della riga: ' . $e->getMessage(), 'xtravelrow');

            return [
                'success' => false,
                'message' => 'Si è verificato un errore: ' . $e->getMessage(),
                'postdata' => $postData
            ];
        }
    }

    public function actionDuplicateAjax($tappa)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Verifica se la richiesta è AJAX
        if (!Yii::$app->request->isAjax) {
            return [
                'success' => false,
                'message' => 'Richiesta non valida. È richiesta una richiesta AJAX.'
            ];
        }

        // Ottieni l'ID originale e i dati dalla richiesta POST
        $originalId = Yii::$app->request->post('id');
        $postData = Yii::$app->request->post('data', []);

        // Verifica che l'ID originale sia valido (opzionale)
        try {
            $originalModel = $this->findModel($originalId);
            // Potremmo usare questo modello per copiare attributi non forniti nel POST
        } catch (NotFoundHttpException $e) {
            // Procedi comunque se l'ID originale non esiste,
            // poiché abbiamo già i dati nel POST
            Yii::warning('Riga originale non trovata durante la duplicazione: ' . $originalId, 'xtravelrow');
        }
        $request = Yii::$app->request;
        // Crea un nuovo modello
        $model = new Xtravelrow();

        // Assegna gli attributi dal post data
        $model->th_id = $postData['tappa_id'] ?? null;
       //$model->datah  = $postData['data'] ?? null;
$r= $postData['data'];
try {
            $dateTime2 = \DateTime::createFromFormat('Y-m-d H:i:s', $r);
            if (!$dateTime2) {
                throw new \Exception("Formato data non valido: $r");
            }
            $formato2a = $dateTime2->format('Y-m-d H:i:s');
            $formato2a = (new \DateTime($r))->format('Y-m-d H:i:s'); // ISO 8601 SQL Server compatibile

            $model->datah = $formato2a;
        } catch (\Exception $e) {
            Yii::error("Errore parsing data: " . $e->getMessage());
            $model->datah = null; // oppure imposta una data predefinita
        }




        $model->guest = $postData['guest'] ?? '';
        $model->ruolo = $postData['ruolo'] ?? '';
        $model->party = $postData['party'] ?? '';
        $model->struttura = $postData['struttura'] ?? '';
        $model->citta = $postData['citta'] ?? '';
        $model->citta_da = $postData['citta_da'] ?? '';
        $model->citta_a = $postData['citta_a'] ?? '';


        //$model->check_in = $postData['check_in'] ?? null;
        //$model->check_out = $postData['check_out'] ?? null;
        $data_tappa_raw = $postData['check_in'];
        $dateTime = new \DateTime($data_tappa_raw);

        // Prova diversi formati

        $formato3 = $dateTime->format('Y-d-m H:i:s');  // Invertito
        $formato1 = $dateTime->format('Y-m-d H:i:s');  // ISO standard
        $formato2 = $dateTime->format('d-m-Y H:i:s');  // Europeo
        $data_tappa = $formato2; // o qualsiasi altro formato che hai verificato funzionare
        if (!empty($postData['check_in'])) {
            $model->check_in = isset($data_tappa) ?     $data_tappa : $model->check_in;
        } else {
            $model->check_in = $postData['check_in'];
        }
        $data_tappa_raw2 = $postData['check_out'];
        $dateTime2 = new \DateTime($data_tappa_raw2);
        $formato2a = $dateTime2->format('d-m-Y H:i:s');  // Europeo
        // Prova diversi formati
        $data_tappa2 = $formato2a;
        // Invertito
        if (!empty($postData['check_out'])) {
            $model->check_out = isset($data_tappa2) ?  $data_tappa2 : $model->check_out;
        } else {
            $model->check_out = $postData['check_out'];
        }
        $model->qta = $postData['qta'] ?? 0;
        $model->cd_Ar = $postData['cd_Ar'] ?? '';
        $model->prezzo = $postData['prezzo'] ?? 0;
        $model->tax_unit = $postData['tax_unit'] ?? 0;
        $model->pnr = $postData['pnr'] ?? '';
        $model->nr_biglietto = $postData['nr_biglietto'] ?? '';
      //  $model->data_pg = $postData['data_pg'] ?? null;
        $model->cd_pg = $postData['cd_pg'] ?? '';
        $model->fee_perc = $postData['fee_perc'] ?? 0;
        $model->fee = $postData['fee'] ?? 0;
        $model->codiva = $postData['codiva'] ?? '';
        $model->duseri = Yii::$app->user->id;
        $model->sottocommessa= $postData['sottocommessa'];
        $model->fornitore = $postData['fornitore'];
        $model->cd_cf_ft = $postData['cd_cf_ft'];
        $model->id_tappa =  $originalModel->id_tappa;
        $model->id_nominativo= $originalModel->id_nominativo;
        $model->stato          = $originalModel->stato;
        // Imposta i campi di audit per il nuovo record
        //$model->utente_ins = Yii::$app->user->identity->username ?? 'sistema';
        //$model->data_ins = new \yii\db\Expression('NOW()');
        $Cd_ARClasse12 = (new \yii\db\Query())
            ->select(['Cd_ARClasse12'])
            ->from('adb_auxcoop.dbo.ar')
            ->where(['cd_ar' => $model->cd_Ar]) // fornitore è cd_cf_ft immagino
            ->scalar(Yii::$app->db5);


        if ($Cd_ARClasse12 == 'TRVACC') {
            // Se è cambiata la percentuale, ricalcoliamo l'importo della fee
            //  $model->fee_perc = $newFeePerc;
            // ($model->prezzo * $model->qta   + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100


            //$model->fee = round(($rowData['totale'] /100)* $newFeePerc  ,2);
            $model->fee =  ($model->prezzo * $model->qta
                + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100;
        } elseif ($Cd_ARClasse12 == 'TRVBIG') {
            $model->fee_perc = 0;
            //$model->fee = $tnewFee;
        }

        // Salva il modello
        try {
            if ($model->save()) {
                // Prepara i dati della riga per il ritorno
                $newRowData = $model->attributes;

                // Assicuriamoci che il tr_id sia disponibile per l'uso sul client
                $newRowData['tr_id'] = $model->tr_id ?? $model->getPrimaryKey();

                return [
                    'success' => true,
                    'message' => 'Riga duplicata con successo',
                    'data' => $newRowData
                ];
            } else {
                // Log degli errori di validazione
                Yii::error('Errori di validazione durante la duplicazione: ' . Json::encode($model->errors), 'xtravelrow');

                return [
                    'success' => false,
                    'message' => 'Errore durante la duplicazione: ' . implode(', ', array_map(function ($errors) {
                        return implode(', ', $errors);
                    }, $model->errors)),
                    'errors' => $model->errors
                ];
            }
        } catch (\Exception $e) {
            // Log dell'eccezione
            Yii::error('Eccezione durante la duplicazione della riga: ' . $e->getMessage(), 'xtravelrow');

            return [
                'success' => false,
                'message' => 'Si è verificato un errore durante la duplicazione: ' . $e->getMessage()
            ];
        }
    }


    public function actionDeleteMultiple()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);

        if (!empty($ids)) {
            try {
                // Recupero i record prima della cancellazione
                $records = \app\models\Xtravelrow::find()->where(['tr_id' => $ids])->asArray()->all();

                if (!empty($records)) {
                    // Percorso file log
                    $logFile = Yii::getAlias('@runtime/logs/deleted_travelrow.log');

                    // Preparo contenuto da scrivere
                    $logContent = "=== " . date('Y-m-d H:i:s') . " - Eliminazione " . count($records) . " record ===\n";
                    foreach ($records as $rec) {
                        $logContent .= json_encode($rec, JSON_UNESCAPED_UNICODE) . "\n";
                    }
                    $logContent .= "===============================\n\n";

                    // Scrivo su file (append)
                    file_put_contents($logFile, $logContent, FILE_APPEND);
                }

                // Ora cancello
                \app\models\Xtravelrow::deleteAll(['tr_id' => $ids]);

                return ['success' => true];
            } catch (\Exception $e) {
                return ['success' => false, 'message' => $e->getMessage()];
            }
        }
        return ['success' => false, 'message' => 'Nessun ID ricevuto'];
    }
    /**
     * Salva tutte le righe inviate via JSON dalla DataTable.
     * Gestisce sia UPDATE che INSERT in un'unica transazione.
     */
    public function actionSaveallajax()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $rawBody = $request->getRawBody();
        $parsedData = json_decode($rawBody, true);

        $rows = isset($parsedData['rows']) ? $parsedData['rows'] : [];
        $th_id = isset($parsedData['th_id']) ? $parsedData['th_id'] : null;

        if (empty($rows)) {
            return ['success' => true, 'message' => 'Nessun dato ricevuto da salvare.'];
        }

        $transaction = Yii::$app->db->beginTransaction();
        $savedIds = []; // Array per memorizzare gli ID salvati
        try {
            foreach ($rows as $index => $rowData) {
                $model = null;
                $id = isset($rowData['tr_id']) && !empty($rowData['tr_id']) ? $rowData['tr_id'] : null;

                if ($id && ($existingModel = Xtravelrow::findOne($id))) {
                    $model = $existingModel;
                    $model->duseru = Yii::$app->user->id;
                } else {
                    $model = new Xtravelrow();
                    $model->th_id = $th_id;
                    $model->duseri = Yii::$app->user->id;
                
                }
                // --- 2. CALCOLO QUANTITÀ (GIORNI) ---
  
                $model->guest         = $rowData['guest'] ?? $model->guest;
                $model->ruolo         = $rowData['ruolo'] ?? $model->ruolo;
                $model->party         = $rowData['party'] ?? $model->party;
                $model->struttura     = $rowData['struttura'] ?? $model->struttura;
                $model->citta         = $rowData['citta'] ?? $model->citta;
                $model->citta_da      = $rowData['citta_da'] ?? $model->citta_da;
                $model->citta_a       = $rowData['citta_a'] ?? $model->citta_a;
                $model->sottocommessa = $rowData['sottocommessa'] ?? $model->sottocommessa;
                $model->fornitore     = $rowData['fornitore'] ?? $model->fornitore;
                $model->cd_cf_ft      = $rowData['cd_cf_ft'] ?? $model->cd_cf_ft;
                $model->cd_pg         = $rowData['cd_pg'] ?? $model->cd_pg;
                $model->cd_Ar         = $rowData['cd_Ar'] ?? $model->cd_Ar;
                $model->pnr           = $rowData['pnr'] ?? $model->pnr;
                $model->nr_biglietto  = $rowData['nr_biglietto'] ?? $model->nr_biglietto;
                $model->codiva        = $rowData['codiva'] ?? $model->codiva;
                $model->note          = $rowData['note'] ?? $model->note;
                $model->stato          = $rowData['stato'] ?? $model->stato;
                // 1. FIX PAGATO (BIT)
                // Forziamo il cast a intero (int) per essere sicuri che sia 1 o 0
                // Usiamo empty() che copre sia null, che false, che 0
                $model->pagato = (!empty($rowData['pagato']) && $rowData['pagato'] !== 'false' 
                && $rowData['pagato'] !== 0) ? 1 : 0;            // Numeri
                // $model->qta           = isset($rowData['qta']) ? $rowData['qta'] : 0;
                // $model->prezzo        = isset($rowData['prezzo']) ? $rowData['prezzo'] : 0;
                // 1. ASSEGNAZIONE VALORI DAL JSON (Fondamentale per i calcoli successivi)
                $model->qta      = isset($rowData['qta']) ? (float)$rowData['qta'] : 0;
                $model->prezzo   = isset($rowData['prezzo']) ? (float)$rowData['prezzo'] : 0;
                $model->tax_unit = isset($rowData['tax_unit']) ? (float)$rowData['tax_unit'] : 0;
                // Prendiamo la nuova percentuale/fee dal JSON
                $model->fee_perc = isset($rowData['fee_perc']) ? (float)$rowData['fee_perc'] : 0;
                $model->fee      = isset($rowData['fee']) ? (float)$rowData['fee'] : 0;
                // Nuovi valori ricevuti
                $newFeePerc = isset($rowData['fee_perc']) ? round((float)$rowData['fee_perc'],2) : 0;
                $newFee     = isset($rowData['fee']) ? round((float)$rowData['fee'],2) : 0;
                $tnewFee     = isset($rowData['fee']) ? round((float)$rowData['fee'], 2) : 0;

               



              
                // 5. GESTIONE DATE (Versione "Elastica")

                // 2. FIX CHECK-IN
                if (!empty($rowData['check_in'])) {
                    try {
                        $d = new \DateTime($rowData['check_in']);
                        // FORMATO ISO SENZA TRATTINI: YYYYMMDD HH:MM:SS
                        // Questo formato è universale per SQL Server
                        $model->check_in = $d->format('Ymd H:i:s');
                    } catch (\Exception $e) {
                        $model->check_in = null;
                    }
                } else {
                    $model->check_in = null;
                }


                // 3. FIX CHECK-OUT
                // Qui c'era un errore logico: usavi $postData['check_out'] ma lavoravi con $rowData
                if (!empty($rowData['check_out'])) {
                    try {
                        $d2 = new \DateTime($rowData['check_out']);
                        // FORMATO ISO SENZA TRATTINI
                        $model->check_out = $d2->format('Ymd H:i:s');
                    } catch (\Exception $e) {
                        // Se la data è non valida, mantieni l'originale o metti null a tua scelta
                        $model->check_out = null;
                    }
                } else {
                    $model->check_out = null;
                }


                // 1. Definiamo l'elenco dei codici che NON devono far scattare il ricalcolo della quantità
                $codiciPagamento = ['ACCONTO HTL', 'SALDO HTL', 'PAG'];

                // 4. FIX DATA PAGAMENTO (data_pg)
                if (!empty($rowData['data_pg'])) {
                    try {
                        $d3 = new \DateTime($rowData['data_pg']);
                        // FORMATO ISO SENZA TRATTINI
                        $model->data_pg = $d3->format('Ymd H:i:s');
                    } catch (\Exception $e) {
                        $model->data_pg = null;
                    }
                } else {
                    $model->data_pg = null;
                }

                if (!empty($rowData['check_in']) && !empty($rowData['check_out'])) {
                    $dIn = new \DateTime($rowData['check_in']);
                    $dOut = new \DateTime($rowData['check_out']);
                    $diff = $dIn->diff($dOut);
                    $giorni = $diff->days;
                    // Se i giorni sono 0 (stesso giorno), magari vuoi contare 1 o lasciare 0? 
                    // Di solito per i viaggi si usa max(1, giorni) o si lascia la differenza.
                    if ($giorni <> $model->qta && !in_array($model->cd_Ar, $codiciPagamento)) {
                        // Se i giorni sono 0, impostiamo almeno 1, altrimenti usiamo il numero di giorni
                        $model->qta = ($giorni > 0) ? $giorni : 1;
                    }
                    // Formattazione per SQL S}erver
                    $model->check_in = $dIn->format('Ymd H:i:s');
                    $model->check_out = $dOut->format('Ymd H:i:s');
                }

                // 3. RECUPERO ALIQUOTA E CLASSE
                $aliquotaValore = (new \yii\db\Query())
                    ->select(['Aliquota'])
                    ->from('adb_auxcoop.dbo.Aliquota')
                    ->where(['Cd_Aliquota' => $model->codiva])
                    ->scalar(Yii::$app->db5) ?: 22;
                $Cd_ARClasse12 = (new \yii\db\Query())
                    ->select(['Cd_ARClasse12'])
                    ->from('adb_auxcoop.dbo.ar')
                    ->where(['cd_ar' => $model->cd_Ar]) // fornitore è cd_cf_ft immagino
                    ->scalar(Yii::$app->db5);

                $tax_totale = $model->tax_unit * $model->qta;
                $totale_servizio = ($model->prezzo * $model->qta) + $tax_totale;
                $model->totale = $totale_servizio;



                // 4. CALCOLO TOTALI E FEE
                $tax_totale = $model->tax_unit * $model->qta;
                $totale_servizio = ($model->prezzo * $model->qta) + $tax_totale;
                $model->totale = $totale_servizio;

        

                Yii::error("DEBUG: ID: " . $model->cd_Ar . " Classe: " . $Cd_ARClasse12);
                if ($Cd_ARClasse12 == 'TRVACC') {
                    // Se è cambiata la percentuale, ricalcoliamo l'importo della fee
                    //  $model->fee_perc = $newFeePerc;
                    // ($model->prezzo * $model->qta   + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100

                    if ($model->cd_Ar == 'ACC_FEE_FUORIORA') {
                        if ($totale_servizio > 0) {
                            $model->fee_perc = ($model->fee * 100) / $totale_servizio;
                        } else {
                            $model->fee_perc = 0;
                        }
                    } else {
                        $model->fee =  ($model->prezzo * $model->qta
                            + ($model->tax_unit * $model->qta)) *  $model->fee_perc  / 100;
                    }
                    //$model->fee = round(($rowData['totale'] /100)* $newFeePerc  ,2);
                
                        } elseif ($Cd_ARClasse12 == 'TRVBIG') {
                    if ($totale_servizio > 0) {
                        $model->fee_perc = ($model->fee * 100) / $totale_servizio;
                    } else {
                        $model->fee_perc = 0;
                    }
                    $model->fee = $tnewFee;
                }

                $vfee = (float)$model->fee;


                $model->fee_perc = round($model->fee_perc, 2);

// 5. CALCOLO IMPONIBILE E IVA
// Scorporo l'imponibile dal totale servizio e aggiungo la fee
$imponibileBase = ($model->totale * 100) / (100 + $aliquotaValore);
$model->imponibile = $imponibileBase + $model->fee;

// Calcolo l'IVA sull'imponibile della fee e del servizio
$model->iva = (($model->imponibile * $aliquotaValore) / 100) - $model->fee;

// Totali finali
$model->Totalegenerale = $model->imponibile + $model->iva + $tax_totale;
$model->totfattura = $model->imponibile + $tax_totale;


$model->fee_perc = round($model->fee_perc, 2);

                $aliquotaServizio = (new \yii\db\Query())
                    ->select(['Aliquota'])
                    ->from('Aliquota')
                    ->where(['Cd_Aliquota' => $model->codiva])
                    ->scalar(Yii::$app->db5) ?: 22;
                $prezzo_totale_servizio = $model->prezzo * $model->qta;
                $tax_totale = $model->tax_unit * $model->qta;
                $ximp = ($prezzo_totale_servizio * 100) / (100 + $aliquotaServizio);
                // Corrisponde a afn_Scorporo_GetImposta
                $xiva = $prezzo_totale_servizio - $ximp;
                $vfee = (float)$model->fee; // Prende la fee fissa se perc è 0
                $xfeeiva = ($vfee <> 0) ? ($vfee * 0.22) : 0;

                $model->fee = round($vfee, 3);
                $model->imponibile = round($ximp + $vfee, 3); // ximp + vfee
                $model->iva = round($xiva + $xfeeiva, 3);    // xiva + iva della fee
                $model->Totalegenerale = round($model->imponibile + $model->iva + $tax_totale, 3);
                $model->totfattura = round($model->imponibile + $tax_totale, 3);
                $model->totale = round($prezzo_totale_servizio, 3);

                if (!$model->save()) {
                    $errors = $model->getFirstErrors();
                    $errorMsg = reset($errors);
                    throw new \Exception("Errore riga " . ($index + 1) . " (" . ($model->guest ?? 'Sconosciuto') . "): " . $errorMsg);
                }
                $savedIds[] = $model->tr_id; 
            }

            $transaction->commit();
            // NUOVO: Recuperiamo i dati aggiornati dal database per rinviarli alla tabella
            $updatedRows = Xtravelrow::find()
                ->where(['tr_id' => $savedIds]) // Filtro mirato sugli ID salvati
                ->asArray() // Importante: restituisce array per il JSON
                ->all();

            return [
                'success' => true,
                'message' => 'Salvataggio completato correttamente.',
                'data' => $updatedRows // <--- Inviata la collezione aggiornata
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error("Errore salvataggio massivo: " . $e->getMessage(), 'xtravelrow');

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function old_actionPaga($th_id, $id_struttura, $id_tappa = null, $prezzo = null, $check_in = null, $check_out = null)  {
        // 1. Recuperiamo tutti i record esistenti che corrispondono ai criteri
        $query = \app\models\Xtravelrow::find()->where([
            'th_id' => $th_id,
            'struttura' => $id_struttura,
        ]);

        // Aggiungiamo il filtro id_tappa solo se è fornito
        if ($id_tappa !== null) {
            $query->andWhere(['id_tappa' => $id_tappa]);
        }

        $storicoModelli = $query->all();

        // 2. Creiamo il nuovo modello per il form (come avevi già fatto)
        $model = new \app\models\Xtravelrow();
        $model->th_id = $th_id;
        $model->id_tappa = $id_tappa;
        $model->struttura = $id_struttura;
        // Popolamento dai dati passati dal pulsante
        if ($prezzo !== null) $model->prezzo = $prezzo;

        // Formattazione date (se arrivano in formato dd/mm/yy trasformale per il DB se necessario)
        if ($check_in !== null) $model->check_in = $check_in;
        if ($check_out !== null) $model->check_out = $check_out;
        // Se il form viene inviato
        // Recupero metodi di pagamento
        $model->data_pg = date('Y-m-d');
        $creditData = (new \yii\db\Query())
            ->select(['codicecarta as id', 'descrizione as text']) // Select2 vuole 'text'
            ->from('x_creditcard')
            ->createCommand(Yii::$app->db5)
            ->queryAll();



        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return "Salvataggio completato con successo!";
        }

        // Passiamo sia il nuovo modello che la lista di quelli esistenti alla vista
        return $this->render('__paga', [
            'model' => $model,
            'storicoModelli' => $storicoModelli ??[],
            'creditData' => \yii\helpers\ArrayHelper::map($creditData, 'id', 'text'),
        ]);
    }


    public function actionPaga($th_id, $id_struttura, $id_tappa = null, $prezzo = null, $check_in = null, $check_out = null)
    {
        // 3. CARICAMENTO DATI PER LA VISTA
        $creditData = (new \yii\db\Query())
            ->select(['codicecarta as id', 'descrizione as text'])
            ->from('x_creditcard')
            ->createCommand(Yii::$app->db5)
            ->queryAll();

        $storicoModelli = \app\models\Xtravelrow::find()
            ->where(['th_id' => $th_id, 'struttura' => $id_struttura])
            ->all();    
    
    $model = new \app\models\Xtravelrow();

        // 1. POPOLAMENTO INIZIALE (Dati che arrivano dal pulsante/URL)
        $model->th_id = $th_id;
        $model->id_tappa = $id_tappa;
        $model->struttura = $id_struttura;
        $model->prezzo = $prezzo;
        $model->data_pg = date('Y-m-d'); // Default data odierna

        // Carichiamo le date originali nel formato per il calendario (Y-m-d)
        if ($check_in) {
            $model->check_in = date('Y-m-d', strtotime(str_replace('/', '-', $check_in)));
        }
        if ($check_out) {
            $model->check_out = date('Y-m-d', strtotime(str_replace('/', '-', $check_out)));
        }

        // 2. GESTIONE SALVATAGGIO (POST)
        if ($model->load(Yii::$app->request->post())) {

            // TRASFORMAZIONE DATE PER SQL SERVER (Dopo il load, prima del save)
            $dateFields = ['check_in', 'check_out', 'data_pg'];
            foreach ($dateFields as $field) {
                if (!empty($model->$field)) {
                    try {
                        $d = new \DateTime($model->$field);
                        $model->$field = $d->format('Ymd H:i:s');
                    } catch (\Exception $e) {
                        $model->$field = null;
                    }
                }
            }
            $model->prezzo =  $model->prezzo *-1;
            $model->qta=1;
            $model->fee=0;
            $model->tax_unit=0;
            $model->imponibile = 0;
            $model->iva= 0;
         

            // RECUPERO CITTÀ DALLO STORICO
            if (!empty($storicoModelli)) {
                // Prendiamo la città dal primo record trovato nello storico
                // Usiamo [0] perché storicoModelli è un array di oggetti
                $model->citta = $storicoModelli[0]->citta;
                $model->cd_cf_ft = $storicoModelli[0]->cd_cf_ft;
                $model->cd_cf_ft = $storicoModelli[0]->cd_cf_ft;
                $model->sottocommessa = $storicoModelli[0]->sottocommessa;
                $model->fornitore = $storicoModelli[0]->fornitore;
                
            } else {
                // Opzionale: cosa fare se non c'è uno storico? 
                // Magari la lasci vuota o metti un default
                $model->citta = null;
            }
            if ($model->save()) {
                return $this->redirect(['xtravelhead/tool', 'id' => $th_id]);
            }
        }



        return $this->render('__paga', [
            'model' => $model,
            'storicoModelli' => $storicoModelli,
            'creditData' => \yii\helpers\ArrayHelper::map($creditData, 'id', 'text'),
            'xthid'=> $th_id
        ]);
    }


public function actionProcessTour($th_id)
{
    $request = Yii::$app->request->post();
    $tappeData = $request['Tappe'] ?? [];

    if (empty($tappeData)) {
        Yii::$app->session->setFlash('error', "Nessuna tappa configurata.");
        return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
    }

    // 1. Recuperiamo TUTTI i guest del tour, ma li dividiamo in due gruppi
    $tuttiIGuest = \app\models\XRoomlist::find()
        ->where(['th_id' => $th_id])
        ->orderBy(['id_guest' => SORT_ASC])
        ->all();

    $soloGuestNumerici = [];
    $nominativiReali = [];

    foreach ($tuttiIGuest as $g) {
        // Se il nome inizia con "Guest ", lo mettiamo nel gruppo prioritario
        if (stripos($g->nominativo, 'Guest ') === 0) {
            $soloGuestNumerici[] = $g->nominativo;
        } else {
            $nominativiReali[] = $g->nominativo;
        }
    }

    // Uniamo le liste: prima i "Guest X", poi i nomi reali (Marco, ecc.)
    $listaOrdinataPerPriorita = array_merge($soloGuestNumerici, $nominativiReali);
    $contatoreEsistenti = count($listaOrdinataPerPriorita);

    // 2. Decodifica Venue (Città)
    $codiciCitta = array_unique(array_column($tappeData, 'citta'));
    $venues = \app\models\XVenue::find()
        ->select(['id', 'venue', 'citta'])
        ->where(['id' => $codiciCitta])
        ->asArray()
        ->all();
    $mappaCitta = \yii\helpers\ArrayHelper::index($venues, 'id');

    $anteprima = [
        'tappe' => [],
        'guest_totali_nuovi' => 0,
        'righe_da_creare' => []
    ];

    $maxOspitiRichiesti = 0;

    foreach ($tappeData as $indice => $dati) {
        $numOspitiTappa = (int)$dati['ospiti'];
        if ($numOspitiTappa > $maxOspitiRichiesti) $maxOspitiRichiesti = $numOspitiTappa;

        $info = $mappaCitta[$dati['citta']] ?? null;
        $desc = $info ? $info['venue'] . " (" . $info['citta'] . ")" : $dati['citta'];

        $anteprima['tappe'][] = [
            'data' => $dati['data'],
            'citta' => $desc,
            'ospiti' => $numOspitiTappa
        ];

        for ($i = 1; $i <= $numOspitiTappa; $i++) {
            // Cerchiamo il nome nella nostra lista pesata per priorità
            if (isset($listaOrdinataPerPriorita[$i - 1])) {
                $nomeVisualizzato = $listaOrdinataPerPriorita[$i - 1];
                $isNew = false;
            } else {
                // Se non abbiamo abbastanza guest esistenti, ne generiamo uno nuovo
                // Il numero deve essere coerente con quanti "Guest X" abbiamo già
                $nomeVisualizzato = "Guest " . $i; 
                $isNew = true;
            }
            
            $anteprima['righe_da_creare'][] = [
                'guest' => $nomeVisualizzato,
                'citta_completa' => $desc,
                'data' => $dati['data'],
                'is_new' => $isNew
            ];
        }
    }

    $anteprima['guest_totali_nuovi'] = max(0, $maxOspitiRichiesti - $contatoreEsistenti);

    return $this->render('preview_wizard', [
        'anteprima' => $anteprima,
        'th_id' => $th_id,
        'originalData' => $request
    ]);
}

    public function ___actionProcessTour050326($th_id)
    {
        $request = Yii::$app->request->post();
        $tappeData = $request['Tappe'] ?? [];

        if (empty($tappeData)) {
            Yii::$app->session->setFlash('error', "Nessuna tappa configurata.");
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        }

        // 1. Controlliamo quanti Guest esistono già (es. se ne abbiamo 5, startCount = 5)
        $maxNum = (new \yii\db\Query())
            ->from('adb_auxcoop.dbo.x_roomlist')
            ->where(['th_id' => $th_id])
            ->andWhere(['like', 'nominativo', 'Guest %', false])
            ->max("TRY_CAST(REPLACE(nominativo, 'Guest ', '') AS INT)");

        $startCount = $maxNum ?? 0;

        $codiciCitta = array_unique(array_column($tappeData, 'citta'));
        $venues = \app\models\XVenue::find()
            ->select(['id', 'venue', 'citta'])
            ->where(['id' => $codiciCitta])
            ->asArray()
            ->all();

        $mappaCompleta = \yii\helpers\ArrayHelper::index($venues, 'id');

        $anteprima = [
            'tappe' => [],
            'guest_totali_da_creare' => 0, // Cambiato nome per chiarezza
            'righe_da_creare' => []
        ];

        $maxOspitiAssolutiRichiesti = 0;

        foreach ($tappeData as $indice => $dati) {
            $numOspitiTappa = (int)$dati['ospiti'];

            // Tracciamo il massimo di ospiti tra tutte le tappe (es. 12)
            if ($numOspitiTappa > $maxOspitiAssolutiRichiesti) {
                $maxOspitiAssolutiRichiesti = $numOspitiTappa;
            }

            $infoVenue = $mappaCompleta[$dati['citta']] ?? null;
            $descrizioneEstesa = $infoVenue ? $infoVenue['venue'] . " (" . $infoVenue['citta'] . ")" : $dati['citta'];

            $anteprima['tappe'][] = [
                'data' => $dati['data'],
                'citta' => $descrizioneEstesa,
                'id_citta' => $dati['citta'],
                'ospiti' => $numOspitiTappa
            ];

            // --- LOGICA DI VISUALIZZAZIONE GUEST ---
            for ($i = 1; $i <= $numOspitiTappa; $i++) {
                // Qui NON sommiamo startCount. Se la tappa ha 12 persone, mostriamo da 1 a 12.
                $anteprima['righe_da_creare'][] = [
                    'tappa_indice' => $indice,
                    'citta_completa' => $descrizioneEstesa,
                    'data' => $dati['data'],
                    'guest' => "Guest " . $i,
                    'is_new' => ($i > $startCount) // Informazione utile per la vista
                ];
            }
        }

        // Quanti NUOVI guest effettivamente il database dovrà creare?
        // Se ne servono 12 e ne abbiamo già 9, ne creeremo 3.
        $nuoviDaCreare = max(0, $maxOspitiAssolutiRichiesti - $startCount);
        $anteprima['guest_totali_nuovi'] = $nuoviDaCreare;

        return $this->render('preview_wizard', [
            'anteprima' => $anteprima,
            'th_id' => $th_id,
            'originalData' => $request,
            'startCount' => $startCount
        ]);
    }
    public function actionProcessTour_240226($th_id)
    {
        $request = Yii::$app->request->post();
        $tappeData = $request['Tappe'] ?? [];

        if (empty($tappeData)) {
            Yii::$app->session->setFlash('error', "Nessuna tappa configurata.");
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        }

        // Modifica la query per ignorare i nomi reali (come "assunta barbieri")
        // In actionProcessTour e actionConfirmSave
        $maxNum = (new \yii\db\Query())
            ->from('adb_auxcoop.dbo.x_roomlist')
            ->where(['th_id' => $th_id])
            ->andWhere(['like', 'nominativo', 'Guest %', false])
            ->max("TRY_CAST(REPLACE(nominativo, 'Guest ', '') AS INT)"); // TRY_CAST è la scelta migliore su SQL Server

        $startCount = $maxNum ?? 0;
        // ---------------------------------------------------------

        $codiciCitta = array_unique(array_column($tappeData, 'citta'));
        $venues = \app\models\XVenue::find()
            ->select(['id', 'venue', 'citta'])
            ->where(['id' => $codiciCitta])
            ->asArray()
            ->all();

        $mappaCompleta = \yii\helpers\ArrayHelper::index($venues, 'id');

        $anteprima = [
            'tappe' => [],
            'guest_totali_nuovi' => 0,
            'righe_da_creare' => []
        ];

        $maxOspitiRichiestiInQuestoLancio = 0;

        foreach ($tappeData as $indice => $dati) {
            $numOspitiTappa = (int)$dati['ospiti'];
            if ($numOspitiTappa > $maxOspitiRichiestiInQuestoLancio) {
                $maxOspitiRichiestiInQuestoLancio = $numOspitiTappa;
            }

            $infoVenue = $mappaCompleta[$dati['citta']] ?? null;
            $descrizioneEstesa = $infoVenue ? $infoVenue['venue'] . " (" . $infoVenue['citta'] . ")" : $dati['citta'];

            $anteprima['tappe'][] = [
                'data' => $dati['data'],
                'citta' => $descrizioneEstesa,
                'id_citta' => $dati['citta'],
                'ospiti' => $numOspitiTappa
            ];

            // Creazione righe per l'anteprima con i nomi CORRETTI (progressivi)
            for ($i = 1; $i <= $numOspitiTappa; $i++) {
                $numeroGuestProgressivo = $startCount + $i; // <--- Qui sta la magia
                $anteprima['righe_da_creare'][] = [
                    'tappa_indice' => $indice,
                    'citta_completa' => $descrizioneEstesa,
                    'data' => $dati['data'],
                    'guest' => "Guest " . $numeroGuestProgressivo,
                ];
            }
        }

        $anteprima['guest_totali_nuovi'] = $maxOspitiRichiestiInQuestoLancio;

        return $this->render('preview_wizard', [
            'anteprima' => $anteprima,
            'th_id' => $th_id,
            'originalData' => $request,
            'startCount' => $startCount // Passiamo l'offset per sicurezza
        ]);
    }
    public function actionConfirmSave($th_id)
{
    $request = Yii::$app->request->post();
    $rawData = json_decode($request['data_to_save'] ?? '{}', true);
    $tappeData = $rawData['Tappe'] ?? [];

    if (empty($tappeData)) {
        Yii::$app->session->setFlash('error', "Dati non validi.");
        return $this->redirect(['wizardtour', 'th_id' => $th_id]);
    }

    $transaction = Yii::$app->db->beginTransaction();
    try {
        $maxOspitiRichiesti = 0;
        $tappeSalvate = [];

        // --- STEP 1: Creazione Tappe ---
        foreach ($tappeData as $dati) {
            $modelTappa = new \app\models\XTappe();
            $modelTappa->th_id = $th_id;
            $modelTappa->data = $dati['data'];
            $modelTappa->citta = $dati['citta'];
            $modelTappa->evaso = 1;

            if (!$modelTappa->save(false)) {
                throw new \Exception("Errore salvataggio Tappa.");
            }
            $modelTappa->refresh();

            $numOspiti = (int)$dati['ospiti'];
            $tappeSalvate[] = [
                'id_tappa' => $modelTappa->id_tappa,
                'data' => $modelTappa->data,
                'citta' => $modelTappa->citta,
                'num_ospiti' => $numOspiti
            ];

            if ($numOspiti > $maxOspitiRichiesti) $maxOspitiRichiesti = $numOspiti;
        }

        // --- STEP 2: Gestione Roomlist (Mappatura Reale) ---
        $guestEsistenti = \app\models\XRoomlist::find()
            ->where(['th_id' => $th_id])
            ->orderBy(['id_guest' => SORT_ASC])
            ->all();

        $mappaGuest = [];
        $contatoreEsistenti = 0;

        foreach ($guestEsistenti as $ge) {
            $contatoreEsistenti++;
            $mappaGuest[$contatoreEsistenti] = [
                'id_guest' => $ge->id_guest,
                'nominativo' => $ge->nominativo
            ];
        }

        $countCreati = 0;
        for ($i = ($contatoreEsistenti + 1); $i <= $maxOspitiRichiesti; $i++) {
            $guest = new \app\models\XRoomlist();
            $guest->nominativo = "Guest " . $i;
            $guest->cd_ar = 'VARIE';
            $guest->th_id = $th_id;
            $guest->evaso = 1;

            if (!$guest->save(false)) throw new \Exception("Errore creazione Guest $i");
            $guest->refresh();

            $mappaGuest[$i] = [
                'id_guest' => $guest->id_guest,
                'nominativo' => $guest->nominativo
            ];
            $countCreati++;
        }

        // --- STEP 3: Creazione Righe di Viaggio (x_travelrow) ---
        foreach ($tappeSalvate as $tappa) {
            for ($i = 1; $i <= $tappa['num_ospiti']; $i++) {
                $xTravelRow = new \app\models\XTravelrow();
                $xTravelRow->th_id = $th_id;
                $xTravelRow->id_tappa = $tappa['id_tappa'];
                $xTravelRow->id_nominativo = $mappaGuest[$i]['id_guest'];
                $xTravelRow->guest = $mappaGuest[$i]['nominativo'];
                $xTravelRow->citta = $tappa['citta'];

                // Fix Data per SQL Server (Formato Ymd)
                if ($tappa['data']) {
                    $ts = strtotime($tappa['data']);
                    if ($ts !== false) $xTravelRow->check_in = date('Ymd', $ts);
                }

                $xTravelRow->duseri = (int)Yii::$app->user->id;
                $xTravelRow->tax = 0; $xTravelRow->tax_unit = 0;
                $xTravelRow->fee = 0; $xTravelRow->fee_perc = 0;
                $xTravelRow->codiva = '';
                $xTravelRow->cd_Ar = 'VARIE';

                if (!$xTravelRow->save(false)) {
                    throw new \Exception("Errore riga viaggio alla posizione $i");
                }
            }
        }

        $transaction->commit();
        Yii::$app->session->setFlash('success', "Generazione completata: $countCreati nuovi guest creati, " . count($tappeSalvate) . " tappe inserite.");
        return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);

    } catch (\Exception $e) {
        $transaction->rollBack();
        Yii::$app->session->setFlash('error', "Errore: " . $e->getMessage());
        return $this->redirect(['wizardtour', 'th_id' => $th_id]);
    }
}
    public function actionConfirmSave_050326($th_id)
    {
        $request = Yii::$app->request->post();
        $rawData = json_decode($request['data_to_save'] ?? '{}', true);
        $tappeData = $rawData['Tappe'] ?? [];

        if (empty($tappeData)) {
            Yii::$app->session->setFlash('error', "Dati non validi.");
            return $this->redirect(['wizardtour', 'th_id' => $th_id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $maxOspitiRichiesti = 0;
            $tappeSalvate = [];

            // --- STEP 1: Creazione Tappe ---
            foreach ($tappeData as $dati) {
                $modelTappa = new \app\models\XTappe();
                $modelTappa->th_id = $th_id;
                $modelTappa->data = $dati['data'];
                $modelTappa->citta = $dati['citta'];
                $modelTappa->evaso = 1;

                if (!$modelTappa->save(false)) {
                    throw new \Exception("Errore Tappa: " . implode(', ', $modelTappa->getFirstErrors()));
                }
                $modelTappa->refresh();

                $numOspiti = (int)$dati['ospiti'];
                $tappeSalvate[] = [
                    'id_tappa' => $modelTappa->id_tappa,
                    'data' => $modelTappa->data,
                    'citta' => $modelTappa->citta,
                    'num_ospiti' => $numOspiti
                ];

                if ($numOspiti > $maxOspitiRichiesti) {
                    $maxOspitiRichiesti = $numOspiti;
                }
            }

            // --- STEP 2: Gestione Roomlist (Riutilizzo + Integrazione) ---

            // 1. Quanti guest abbiamo già?
            $maxNumExist = (new \yii\db\Query())
                ->from('adb_auxcoop.dbo.x_roomlist')
                ->where(['th_id' => $th_id])
                ->andWhere(['like', 'nominativo', 'Guest %', false])
                ->max("TRY_CAST(REPLACE(nominativo, 'Guest ', '') AS INT)");

            $startCount = (int)($maxNumExist ?? 0);
            $mappaGuest = [];

            // 2. Carichiamo i Guest ESISTENTI nella mappa
            $guestEsistenti = \app\models\XRoomlist::find()
                ->where(['th_id' => $th_id])
                ->andWhere(['like', 'nominativo', 'Guest %', false])
                ->all();

            foreach ($guestEsistenti as $ge) {
                $numero = (int)str_replace('Guest ', '', $ge->nominativo);
                $mappaGuest[$numero] = [
                    'id_guest' => $ge->id_guest,
                    'nominativo' => $ge->nominativo
                ];
            }

            // 3. Creiamo SOLO i Guest che mancano
            // Se maxOspitiRichiesti è 12 e ne abbiamo 9, facciamo il ciclo da 10 a 12
            $countCreati = 0;
            for ($i = ($startCount + 1); $i <= $maxOspitiRichiesti; $i++) {
                $guest = new \app\models\XRoomlist();
                $guest->nominativo = "Guest " . $i;
                $guest->cd_ar = 'VARIE';
                $guest->th_id = $th_id;
                $guest->evaso = 1;

                if (!$guest->save(false)) {
                    throw new \Exception("Errore Roomlist Guest $i");
                }
                $guest->refresh();

                $mappaGuest[$i] = [
                    'id_guest' => $guest->id_guest,
                    'nominativo' => $guest->nominativo
                ];
                $countCreati++;
            }

            // --- STEP 3: Creazione Righe di Viaggio ---
            foreach ($tappeSalvate as $tappa) {
                for ($i = 1; $i <= $tappa['num_ospiti']; $i++) {
                    $xTravelRow = new \app\models\XTravelrow();
                    $xTravelRow->th_id = $th_id;
                    $xTravelRow->id_tappa = $tappa['id_tappa'];

                    // Qui prendiamo dalla mappa (che contiene sia i vecchi che i nuovi)
                    $xTravelRow->id_nominativo = $mappaGuest[$i]['id_guest'];
                    $xTravelRow->guest = $mappaGuest[$i]['nominativo'];
                    $xTravelRow->citta = $tappa['citta'];

                    if ($tappa['data']) {
                        $ts = strtotime($tappa['data']);
                        if ($ts !== false) $xTravelRow->check_in = date('Ymd', $ts);
                    }

                    $xTravelRow->duseri = (int)Yii::$app->user->id;
                    $xTravelRow->tax = 0;
                    $xTravelRow->tax_unit = 0;
                    $xTravelRow->fee = 0;
                    $xTravelRow->fee_perc = 0;
                    $xTravelRow->codiva = '';

                    if (!$xTravelRow->save(false)) {
                        throw new \Exception("Errore riga viaggio");
                    }
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Generazione completata: utilizzati " . $startCount . " guest esistenti e creati " . $countCreati . " nuovi.");
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', "Errore: " . $e->getMessage());
            return $this->redirect(['wizardtour', 'th_id' => $th_id]);
        }
    }


    public function actionConfirmSave_240226($th_id)
    {
        $request = Yii::$app->request->post();
        $rawData = json_decode($request['data_to_save'] ?? '{}', true);
        $tappeData = $rawData['Tappe'] ?? [];

        if (empty($tappeData)) {
            Yii::$app->session->setFlash('error', "Dati non validi.");
            return $this->redirect(['wizard-tour', 'th_id' => $th_id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $maxOspitiTotali = 0;
            $tappeSalvate = [];

            // --- STEP 1: Creazione Tappe (x_tappe) ---
            foreach ($tappeData as $indice => $dati) {
                $modelTappa = new \app\models\XTappe();
                // Generiamo il GUID manualmente se il modello non lo fa, 
                // per essere sicuri di averlo subito disponibile per le righe
                //$modelTappa->id_tappa = new \yii\db\Expression('NEWID()');
                $modelTappa->th_id = $th_id;
                $modelTappa->data = $dati['data'];
                $modelTappa->citta = $dati['citta']; // GUID della venue
                $modelTappa->evaso = 1;

                if (!$modelTappa->save()) {
                    throw new \Exception("Errore Tappa: " . implode(', ', $modelTappa->getFirstErrors()));
                }

                // Rinfreschiamo il modello per leggere il GUID generato da SQL Server
                $modelTappa->refresh();

                $tappeSalvate[] = [
                    'id_tappa' => $modelTappa->id_tappa,
                    'data' => $modelTappa->data,
                    'citta' => $modelTappa->citta,
                    'num_ospiti' => (int)$dati['ospiti']
                ];

                if ((int)$dati['ospiti'] > $maxOspitiTotali) {
                    $maxOspitiTotali = (int)$dati['ospiti'];
                }
            }

            // --- STEP 2: Creazione Roomlist (x_roomlist) ---
            $mappaGuest = [];
            for ($i = 1; $i <= $maxOspitiTotali; $i++) {
                $guest = new \app\models\XRoomlist();
                //$guest->id_guest = new \yii\db\Expression('NEWID()');
                $guest->nominativo = "Guest " . $i;
                $guest->cd_ar = 'VARIE'; // Campo obbligatorio nello schema (NOT NULL)
                $guest->th_id = $th_id;
                $guest->evaso = 1;

                if (!$guest->save()) {
                    throw new \Exception("Errore Roomlist Guest $i: " . implode(', ', $guest->getFirstErrors()));
                }

                $guest->refresh();
                $mappaGuest[$i] = [
                    'id_guest' => $guest->id_guest,
                    'nominativo' => $guest->nominativo
                ];
            }

            // --- STEP 3: Creazione Righe di Viaggio (x_travelrow) ---
            foreach ($tappeSalvate as $tappa) {
                for ($i = 1; $i <= $tappa['num_ospiti']; $i++) {
                    $xTravelRow = new \app\models\XTravelrow();
                    $xTravelRow->th_id = $th_id;
                    $xTravelRow->id_tappa = $tappa['id_tappa'];
                    $xTravelRow->id_nominativo = $mappaGuest[$i]['id_guest'];
                    $xTravelRow->guest = $mappaGuest[$i]['nominativo'];
                    $xTravelRow->citta = $tappa['citta'];
                    if ($tappa['data']) {
                        // Usiamo il formato Ymd (es. 20260218) senza trattini.
                        // SQL Server lo interpreta SEMPRE correttamente a prescindere dalla lingua.
                        $timestamp = strtotime($tappa['data']);
                        if ($timestamp !== false) {
                            $xTravelRow->check_in = date('Ymd', $timestamp);
                        }
                    }
                    $xTravelRow->duseri = (int)Yii::$app->user->id;
                   // $xTravelRow->cd_Ar = 'VARIE';

                    // Inseriamo dei valori numerici espliciti per evitare che stringhe vuote causino errori
                    $xTravelRow->tax = 0;
                    $xTravelRow->tax_unit = 0;
                    $xTravelRow->fee = 0;
                    $xTravelRow->fee_perc = 0;
                    $xTravelRow->codiva = '';
                    if (!$xTravelRow->save()) {
                        throw new \Exception("Errore riga viaggio: " . implode(', ', $xTravelRow->getFirstErrors()));
                    }
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Generazione completata con successo.");
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', "Errore: " . $e->getMessage());
            return $this->redirect(['wizardtour', 'th_id' => $th_id]);
        }
    }
    public function ___actionProcessTour($th_id) // Yii leggerà il th_id dall'URL dell'action del form
    {
        $request = Yii::$app->request->post();
        $tappe = $request['Tappe'] ?? [];

        if (empty($tappe)) {
            Yii::$app->session->setFlash('error', "Nessuna tappa configurata.");
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $maxOspitiTotali = 0;

            foreach ($tappe as $indice => $dati) {
                $modelTappa = new \app\models\XTappe();
                $modelTappa->th_id = $th_id;
                $modelTappa->data = $dati['data'];
                $modelTappa->citta = $dati['citta'];
                $modelTappa->num_ospiti = (int)$dati['ospiti'];

                if (!$modelTappa->save()) {
                    // Se fallisce, recuperiamo l'errore specifico del modello
                    $errori = implode(', ', $modelTappa->getFirstErrors());
                    throw new \Exception("Errore tappa $indice: $errori");
                }

                if ($modelTappa->num_ospiti > $maxOspitiTotali) {
                    $maxOspitiTotali = $modelTappa->num_ospiti;
                }
            }

            // Creazione Nominativi
            for ($i = 1; $i <= $maxOspitiTotali; $i++) {
                $guest = new \app\models\XRoomlist();
                $guest->th_id = $th_id;
                $guest->guest = "Guest " . $i;
                if (!$guest->save()) {
                    throw new \Exception("Errore creazione Guest $i");
                }
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Generazione completata! Tappe create e $maxOspitiTotali guest inseriti.");

            // Ritorna alla pagina masterhotel (quella con la tabella)
            return $this->redirect(['xtravelhead/masterhotel', 'id' => $th_id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['wizardtour', 'th_id' => $th_id]);
        }
    }
    /**
     * Visualizza la nuova form per la creazione guidata di tappe e ospiti
     * @param integer $th_id L'ID del record principale (Travel Head)
     */
    public function actionWizardtour($th_id = null)
    {
        if ($th_id === null) {
            return "Errore: th_id non è arrivato alla funzione!";
        }

        // NON USARE $this->findModel($th_id), perché quello cerca nelle RIGHE.
        // Dobbiamo cercare nella TESTATA:
        $model = \app\models\Xtravelhead::findOne($th_id);

        if ($model === null) {
            throw new NotFoundHttpException("Il Tour (Testata) con ID $th_id non esiste.");
        }

        return $this->render('wiztour', [
            'mth_id' => $th_id,
            'model' => $model,
        ]);
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


    /**
     * Recupera la classe dell'articolo tramite AJAX.
     * @param string $cd_ar Il codice dell'articolo selezionato.
     * @return array Restituisce un JSON con la classe dell'articolo.
     */
    public function actionGetClasseArticolo($cd_ar)
    {
        // Diciamo a Yii2 che la risposta dovrà essere in formato JSON
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        // Eseguiamo la query che mi hai fornito
        $classeArticolo = (new \yii\db\Query())
            ->select(['Cd_ARClasse12'])
            ->from('adb_auxcoop.dbo.ar')
            ->where(['cd_ar' => $cd_ar])
            ->scalar(Yii::$app->db5);

        // Restituiamo il risultato
        return [
            'success' => true,
            'classe' => $classeArticolo
        ];
    }
}
