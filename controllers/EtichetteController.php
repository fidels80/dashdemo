<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Json;

class EtichetteController extends Controller
{
    /**
     * Recupera il componente di connessione db5
     * Assicurati che 'db5' sia configurato nei componenti in web.php
     */
    private function xdbsource()
    {
        return Yii::$app->db5;
    }

   public function actionIndex()
{

    $db5 = $this->xdbsource();
    // Carichiamo i tipi documento disponibili per la Select2
    $tipiDoc = (new \yii\db\Query())
        ->select(['cd_do', 'descrizione'])
        ->from('do')
        ->all($db5);
    $listaTipi = \yii\helpers\ArrayHelper::map($tipiDoc, 'cd_do', function($m) {
        return $m['cd_do'] . ' - ' . $m['descrizione'];
    });

    // Carichiamo i clienti per la Select2 (attenzione se sono troppi, meglio caricarli via AJAX)
    $clienti = (new \yii\db\Query())
        ->select(['cd_cf', 'descrizione'])
        ->from('cf')
        ->where(['cliente' => 1])
        ->limit(1000) // Limite di sicurezza
        ->all($db5);
    $listaClienti = \yii\helpers\ArrayHelper::map($clienti, 'cd_cf', 'descrizione');
    return $this->render('index', [
        'tipiDoc' => $listaTipi,
        'clienti' => $listaClienti,
    ]);

    }

public function actionFetchTestate($cd_do = null, $cd_cf = null, $pivacf = null, $data = null, $numero = null, $numerorif = null)
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    
    $query = (new \yii\db\Query())
        ->select([
            'dotes.Id_DoTes', 'dotes.cd_do', 'dotes.DataDoc', 'dotes.NumeroDoc', 
            'dotes.NumeroDocRif', 'cf.Descrizione as cf_desc','dotes.x_eth'
        ])
        ->from('dotes')
        ->leftJoin('cf', 'dotes.cd_cf = cf.cd_cf')
        ->orderBy(['dotes.DataDoc' => SORT_DESC]);

    // Nuova logica filtri
    if ($cd_do) $query->andWhere(['dotes.cd_do' => $cd_do]);
    if ($cd_cf) $query->andWhere(['dotes.cd_cf' => $cd_cf]);
    if ($data) $query->andWhere(['dotes.DataDoc' => $data]);
 if ($numero) {
    $query->andWhere(['like', 'dotes.NumeroDoc', $numero]);
}

if ($numerorif) {
    $query->andWhere(['like', 'dotes.NumeroDocRif', $numerorif]);
}
    if ($pivacf) {
        $query->andWhere(['or', 
            ['like', 'cf.partitaiva', $pivacf],
            ['like', 'cf.CodiceFiscale', $pivacf]
        ]);
    }

 // ESECUZIONE DELLA QUERY: Salviamo il risultato in una variabile array
    $results = $query->all($this->xdbsource());

    // ORA usiamo array_map sui risultati reali
    return array_map(function($r) {
        return array_change_key_case($r, CASE_LOWER);
    }, $results);
}
public function actionFetchRighe($id_dotes)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    // 1. Costruiamo l'oggetto Query (senza eseguire ancora nulla)
    $query = (new \yii\db\Query())
        ->select(['Id_DoRig', 'cd_ar', 'descrizione', 'qta', 'dorig.x_eth'])
        ->from('dorig')
        ->where(['id_dotes' => $id_dotes])
        ->andWhere(['not', ['cd_ar' => null]])
        ->andWhere(['<>', 'cd_ar', '']);
        
    // 2. Eseguiamo la query su db5 e otteniamo un ARRAY di risultati
    $rawResults = $query->all($this->xdbsource());

    // 3. Elaboriamo l'array con array_map
    return array_map(function($r) {
        // Forza le chiavi in minuscolo per compatibilità con il JS
        $r = array_change_key_case($r, CASE_LOWER);
        
        // Aggiungiamo il flag per il frontend
        $r['has_eth'] = !empty($r['x_eth']);
        
        return $r;
    }, $rawResults);
}

public function actionProcess($id_dotes, $mode, $selected_righe)
{
    $righeIds = Json::decode($selected_righe);
    $db5 = $this->xdbsource();
    $transaction = $db5->beginTransaction();

    try {
        $labelsToPrint = [];
        $commonGuid = $this->generateUuid();

        if ($mode === 'doc') {
            // 1. CASO DOCUMENTO INTERO (GENERAZIONE NUOVA)
            $db5->createCommand()->update('dotes', ['x_eth' => $commonGuid], ['Id_DoTes' => $id_dotes])->execute();
            $db5->createCommand()->update('dorig', ['x_eth' => $commonGuid], ['Id_DoTes' => $id_dotes])->execute();
            $labelsToPrint[] = [
                'guid' => $commonGuid, 
                'cd_ar' => 'DOC', 
                'desc' => 'ID TESTATA: ' . $id_dotes, 
                'qty' => 1
            ];
        } 
        elseif ($mode === 'reprint') {
            // 2. CASO RISTAMPA (LEGGIAMO SOLO DAL DB)
            foreach ($righeIds as $idRig) {
                $riga = (new \yii\db\Query())->from('dorig')->where(['Id_DoRig' => $idRig])->one($db5);
                if ($riga) {
                    $riga = array_change_key_case($riga, CASE_LOWER);
                    // Usiamo il GUID esistente, se manca mettiamo un avviso
                    $guidEsistente = !empty($riga['x_eth']) ? $riga['x_eth'] : 'NON ASSEGNATO';
                    
                    $labelsToPrint[] = [
                        'guid' => $guidEsistente,
                        'cd_ar' => $riga['cd_ar'] ?? 'N/D',
                        'desc' => '[RISTAMPA] ' . ($riga['descrizione'] ?? ''),
                        'qty' => 1 // Nella ristampa solitamente si stampa una copia
                    ];
                }
            }
        }
else {
    // CASO GENERAZIONE PER RIGA (ART O QTY)
    foreach ($righeIds as $idRig) {
        $riga = (new \yii\db\Query())->from('dorig')->where(['Id_DoRig' => $idRig])->one($db5); 
        if ($riga) {
            $riga = array_change_key_case($riga, CASE_LOWER);
            $guid = $this->generateUuid();
            
            // Aggiorna il database con il nuovo GUID
            $db5->createCommand()->update('dorig', ['x_eth' => $guid], ['Id_DoRig' => $idRig])->execute();
            $db5->createCommand()->update('dotes', ['x_eth' => $guid], ['Id_DoTes' => $id_dotes])->execute();   
            // LOGICA CRUCIALE: Se mode è 'qty', prende il valore reale della qta, altrimenti 1
            $qtyPrint = ($mode === 'qty') ? (int)($riga['qta'] ?? 0) : 1;
            
            $labelsToPrint[] = [
                'guid' => $guid,
                'cd_ar' => $riga['cd_ar'] ?? 'N/D',
                'desc' => $riga['descrizione'] ?? '',
                'qty' => $qtyPrint
            ];
        }
    }
}

        $transaction->commit();
       // Esempio: recupero misure da input, altrimenti default 80x50
$width = Yii::$app->request->get('w', 80);
$height = Yii::$app->request->get('h', 50);

return $this->generatePdf($labelsToPrint, $width, $height);

    } catch (\Exception $e) {
        $transaction->rollBack();
        throw $e;
    }
}

    private function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

/**
 * @param array $labels Dati delle etichette
 * @param int $w Larghezza etichetta in mm (default 80)
 * @param int $h Altezza etichetta in mm (default 50)
 */
private function generatePdf($labels, $w = 80, $h = 50)
{
    // Orientamento 'L' (Landscape - Orizzontale) se la larghezza è maggiore dell'altezza
    $orientation = ($w >= $h) ? 'L' : 'P';
    
    $pdf = new \TCPDF($orientation, 'mm', [$w, $h], true, 'UTF-8', false);
    $pdf->SetAutoPageBreak(false);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(2, 2, 2); // Margini minimi per sfruttare lo spazio

    foreach ($labels as $l) {
        for ($i = 0; $i < $l['qty']; $i++) {
            $pdf->AddPage();
            
            // --- CALCOLO PROPORZIONALE DELLE POSIZIONI ---
            // Usiamo percentuali dell'altezza ($h) e larghezza ($w) per posizionare gli elementi
            
            // 1. CODICE ARTICOLO (In alto a sinistra)
            $pdf->SetFont('helvetica', 'B', ($h * 0.20)); // Font dinamico basato sull'altezza (20% dell'altezza)
            $pdf->Text($w * 0.05, $h * 0.05, "Cod: " . ($l['cd_ar'] ?? 'N/D'));
            
            // 2. DESCRIZIONE (Sotto il codice)
            $pdf->SetFont('helvetica', '', ($h * 0.12)); // Font più piccolo (12% dell'altezza)
            $pdf->Text($w * 0.05, $h * 0.25, substr($l['desc'] ?? '', 0, 45));
            
            // 3. BARCODE (Adattato allo spazio rimanente)
            // Lo posizioniamo al 45% dell'altezza e gli diamo il 45% dello spazio verticale
            $style = [
                'position' => '', 'align' => 'C', 'stretch' => true, 'fitwidth' => true,
                'cellfitalign' => '', 'border' => false, 'hpadding' => 'auto', 'vpadding' => 'auto',
                'fgcolor' => [0,0,0], 'bgcolor' => false, 'text' => true, 
                'font' => 'helvetica', 'fontsize' => ($h * 0.10), 'padding' => 2
            ];
            
            // write1DBarcode(codice, tipo, x, y, larghezza, altezza, xres, stile, align)
            $pdf->write1DBarcode(
                $l['guid'], 
                'C128', 
                $w * 0.05,          // X: 5% dal bordo
                $h * 0.45,          // Y: 45% dall'alto
                $w * 0.90,          // Larghezza: 90% dell'etichetta
                $h * 0.45,          // Altezza: 45% dell'etichetta
                0.4, 
                $style, 
                'N'
            );
        }
    }
    
    return $pdf->Output('etichette_eth.pdf', 'I');
}
}