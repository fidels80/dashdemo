<?php

/**
 * File: commands/BackgroundExportController.php
 * Console Controller per processare export Excel in background
 */

namespace app\commands;

use yii\console\Controller;
use yii\helpers\FileHelper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yii;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use app\models\Xtravelhead;
use app\models\Export_jobs;
use app\models\Xvenue;
use app\models\Xstruttura;
use app\models\Cf;
class BackgroundExportController extends Controller
{
    /**
     * Processa export Excel in background
     * Uso: php yii background-export/process-excel jobId dataId username userEmail
     */
    public function actionProcessExcel($jobId, $dataId, $username, $userEmail, $tipoExport =null)
    {
        $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . '.log');

        $this->logOperation($logFile, "=== INIZIO PROCESSO BACKGROUND ===", [
            'job_id' => $jobId,
            'data_id' => $dataId,
            'username' => $username,
            'email' => $userEmail,
            'pid' => getmypid(),
            'tipoexport'=> $tipoExport
        ]);

        try {
            // Aggiorna status a processing
            $this->updateExportJob($jobId, [
                'status' => 'processing',
                'progress' => 10,
                'message' => 'Inizializzazione export...'
            ]);

            $startTime = microtime(true);

            // Esegui export
            $result = $this->processExcelExport($jobId, $dataId, $username, $logFile, $tipoExport);

            $totalTime = microtime(true) - $startTime;

            // Aggiorna job completato
            $this->updateExportJob($jobId, [
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Export completato con successo',
                'file_path' => $result['file_path'],
                'file_size' => $result['file_size'],
                'records_count' => $result['records_count'],
                'processing_time' => round($totalTime, 3)
            ]);

            // Invia email di successo
            $this->sendSuccessEmail($userEmail, $username, $jobId, $result, $totalTime, $dataId);

            $this->logOperation($logFile, "=== PROCESSO COMPLETATO ===", [
                'job_id' => $jobId,
                'total_time' => round($totalTime, 3),
                'records' => $result['records_count'],
                'file_size_mb' => round($result['file_size'] / 1024 / 1024, 2)
            ]);

            return 0;
        } catch (\Exception $e) {
            // Aggiorna job fallito
            $this->updateExportJob($jobId, [
                'status' => 'failed',
                'progress' => 0,
                'message' => 'Export fallito: ' . $e->getMessage(),
                'error_message' => $e->getTraceAsString()
            ]);

            // Invia email di errore
            $this->sendErrorEmail($userEmail, $username, $jobId, $e);

            $this->logOperation($logFile, "=== PROCESSO FALLITO ===", [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return 1;
        }
    }

    /**
     * Esegue l'export Excel ottimizzato
     */
    private function processExcelExport($jobId, $dataId, $username, $logFile,$tipoexp)
    {
        // Configurazione memoria
        ini_set('memory_limit', '1024M');
        set_time_limit(0); // Nessun limite di tempo per console
        ini_set('pcre.backtrack_limit', '5000000');
        ini_set('pcre.recursion_limit', '100000');

        // Aggiorna progresso
        $this->updateExportJob($jobId, [
            'progress' => 20,
            'message' => 'Configurazione sistema...'
        ]);

        // Setup paths
        $exportDir = Yii::getAlias('@app/web/xls_REP');
        if (!is_dir($exportDir)) {
            FileHelper::createDirectory($exportDir);
        }

        $templatePath = Yii::getAlias('@app/templates/tmp_UNICO.xlsx');
        $username_clean = str_replace('.', '_', $username);
        $destinationFile = $exportDir . '/' . $this->getDocumentNumber() . '_' . $username_clean . '_' . $jobId . '.xlsx';

        // Copia template
        if (!copy($templatePath, $destinationFile)) {
            throw new \Exception('Impossibile copiare il template Excel');
        }

        $this->logOperation($logFile, "Template copiato", [
            'source' => $templatePath,
            'destination' => $destinationFile,
            'file_size' => filesize($destinationFile),
            'tipoexport '=>$tipoexp
        ]);

        // Aggiorna progresso
        $this->updateExportJob($jobId, [
            'progress' => 30,
            'message' => 'Recupero dati dal database...'
        ]);

        // Recupera dati con la TUA query
        $exportData = $this->getOptimizedExportData($dataId, $logFile,$tipoexp);

        if (empty($exportData)) {
            throw new \Exception('Nessun dato trovato per l\'ID specificato');
        }

        $this->logOperation($logFile, "Dati recuperati", [
            'records_count' => count($exportData),
            'sample_record' => !empty($exportData) ? array_keys($exportData[0]) : []
        ]);

        // Aggiorna progresso
        $this->updateExportJob($jobId, [
            'progress' => 50,
            'message' => 'Caricamento file Excel...'
        ]);

        // Carica Excel con ottimizzazioni
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
       // $reader->setReadEmptyCells(false);

        // Configurazioni cache
        //$cache = new \PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache3();
       // \PhpOffice\PhpSpreadsheet\Settings::setCache($cache);
       // \PhpOffice\PhpSpreadsheet\Calculation\Calculation::getInstance()->setCalculationCacheEnabled(false);

        $spreadsheet = $reader->load($destinationFile);
        $worksheet = $spreadsheet->getActiveSheet();

        // Aggiorna progresso
        $this->updateExportJob($jobId, [
            'progress' => 70,
            'message' => 'Inserimento dati...'
        ]);

        // Popola dati
        //$this->populateExcelDataBatch($worksheet, $exportData, $logFile, $jobId);



        $categoryConfigs = [
            'cat_biglietti' => null,
            'cat_Hotel' => null,
        ];

        // Ricava i valori di configurazione categoria (una tantum)
        foreach ($exportData as $row) {
            if ($row['cat_biglietti_config']) {
                $categoryConfigs['cat_biglietti'] = $row['cat_biglietti_config'];
            }
            if ($row['cat_hotel_config']) {
                $categoryConfigs['cat_Hotel'] = $row['cat_hotel_config'];
            }
            if ($categoryConfigs['cat_biglietti'] && $categoryConfigs['cat_Hotel']) {
                break; // Ottimizzazione: se li hai trovati entrambi, esci
            }
        }

        $startRow = 2;
        $currentRow = $startRow;
        foreach ($exportData as $record) {
            $this->populateSingleRow($worksheet, $record, 
            $currentRow+4, $categoryConfigs);
            $currentRow++;
        }
        $this->populatelastRow(
            $worksheet,
            $record,
            $currentRow + 4,
            $categoryConfigs, $dataId,
            $jobId);
        // Log a fine processo
        $this->logOperation($logFile, "Popolamento Excel completato (per righe)", [
            'total_rows_written' => $currentRow - $startRow,
            'records_count' => count($exportData)
        ]);












        // Aggiorna progresso
        $this->updateExportJob($jobId, [
            'progress' => 90,
            'message' => 'Salvataggio file...'
        ]);

        // Salva file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        //  $writer->setPreCalculateFormulas(false);
        // $writer->setUseDiskCaching(true, sys_get_temp_dir());
        $worksheet->getParent()->getActiveSheet()->setSelectedCell('A1');
        //$worksheet->getParent()->getActiveSheet()->getSheetView()->setTopLeftCell('A1');
        $writer->save($destinationFile);

        // Cleanup
        $spreadsheet->disconnectWorksheets();
        $recordsCount = count($exportData);
        unset($exportData, $spreadsheet, $worksheet, $writer, $reader);
        gc_collect_cycles();

        return [
            'file_path' => $destinationFile,
            'file_size' => filesize($destinationFile),
            'records_count' =>  $recordsCount
        ];
    }

    /**
     * LA TUA QUERY - Recupera dati ottimizzati per export
     */
    protected function getOptimizedExportData($id, $logFile ,$tipoexp)
    {
        $this->logOperation($logFile, "Inizio query database", ['id' => $id]);

        $sql = "
            SELECT 
                xtr.*,
                xr.descrizione as ruolo_desc,
                ar.Cd_ARClasse2,
                COALESCE(ali.Aliquota, def_ali.Aliquota, 0) as vat_rate,
                cfg_biglietti.valore as cat_biglietti_config,
                cfg_hotel.valore as cat_hotel_config
            FROM xtravelrow xtr
            LEFT JOIN dotes ON dotes.id_dotes = xtr.evadi_a
            LEFT JOIN xruoli xr ON xr.cd_ruolo = xtr.ruolo
            LEFT JOIN ar ON ar.CD_AR = UPPER(TRIM(xtr.cd_ar))
            LEFT JOIN Aliquota ali ON LTRIM(RTRIM(ali.cd_aliquota)) = TRIM(xtr.codiva)
            LEFT JOIN (
                SELECT ali_def.Aliquota 
                FROM Impostazione imp 
                LEFT JOIN Aliquota ali_def ON ali_def.Cd_Aliquota = imp.Cd_Aliquota_1 
                WHERE imp.Cd_Impostazione = 'IVA'
            ) def_ali ON xtr.codiva IS NULL OR xtr.codiva = ''
            LEFT JOIN xtravelconfig cfg_biglietti ON UPPER(cfg_biglietti.parametro) = 'CAT_BIGLIETTI'
            LEFT JOIN xtravelconfig cfg_hotel ON UPPER(cfg_hotel.parametro) = 'CAT_HOTEL'
            WHERE xtr.th_id = :id
              AND xtr.cd_ar NOT IN (
                  SELECT ar_acc.cd_ar FROM ar ar_acc WHERE ar_acc.x_isacconto = 1
              )
           
        ";
        // aggiunta filtro extra in base a tipoexp
        /*       if ($tipoexp == 1) {
            // Solo hotel
            $sql .= " AND (ar.Cd_ARClasse2 IN (
    SELECT TRIM(value)
    FROM STRING_SPLIT(cfg_hotel.valore, ',')) or xtr.cd_Ar='FIDO ACCONTI'
)";
        } elseif ($tipoexp == 2) {
            // Solo biglietti
            $sql .= " AND (ar.Cd_ARClasse2 IN (
    SELECT TRIM(value)
    FROM STRING_SPLIT(cfg_biglietti.valore, ',')) or xtr.cd_Ar='FIDO ACCONTI'
)";
        }
*/

        // Gestione filtri dinamici in base a tipoexp e cd_do
        if ($tipoexp == 1) {
            // Solo hotel: Classe2 in config hotel OPPURE FIDO ACCONTI con documento FVH
            $sql .= " AND (
        ar.Cd_ARClasse2 IN (SELECT TRIM(value) FROM STRING_SPLIT(cfg_hotel.valore, ',')) 
        OR (xtr.cd_Ar = 'FIDO ACCONTI' AND dotes.cd_do = 'FVH')
    )";
        } elseif ($tipoexp == 2) {
            // Solo biglietti: Classe2 in config biglietti OPPURE FIDO ACCONTI con documento FVB
            $sql .= " AND (
        ar.Cd_ARClasse2 IN (SELECT TRIM(value) FROM STRING_SPLIT(cfg_biglietti.valore, ',')) 
        OR (xtr.cd_Ar = 'FIDO ACCONTI' AND dotes.cd_do = 'FVB')
    )";
        } elseif ($tipoexp == 3) {
            // Tipo 3: Prende sempre FIDO ACCONTI (oltre alla logica standard se prevista)
           // $sql .= " A D (xtr.cd_Ar = 'FIDO ACCONTI')";
        }


        //$sql .=" ORDER BY xtr.citta,xtr.cd_ar";
        $sql .= " ORDER BY xtr.tr_id ASC";
        $queryStart = microtime(true);
        $connection = Yii::$app->db5;
        $command =  $connection->createCommand($sql, [':id' => $id]);
        $this->logOperation($logFile, "tipoexp", [
            $tipoexp
        ]);
        // Log della query SQL (solo in debug)
        if (YII_DEBUG) {
            $this->logOperation($logFile, "SQL Query", [
                'sql' => $command->getRawSql()
            ]);
        }

        $result = $command->queryAll();
        $queryTime = microtime(true) - $queryStart;

        $this->logOperation($logFile, "Query completata", [
            'execution_time' => round($queryTime, 3),
            'records_found' => count($result),
            'memory_after' => memory_get_usage(true)
        ]);

        return $result;
    }

    /**
     * Popola Excel con dati in batch ottimizzato
     */
    private function populateExcelDataBatch($worksheet, $exportData, $logFile, $jobId = null)
    {
        $populateStart = microtime(true);
        $totalRecords = count($exportData);

        $this->logOperation($logFile, "Inizio popolamento Excel", [
            'total_records' => $totalRecords,
            'job_id' => $jobId
        ]);

        // Definisci mapping colonne per i tuoi dati specifici
        $columnMapping = $this->getXTravelColumnMapping();

        // Inizia dalla riga 2 (assume header in riga 1)
        $startRow = 2;

        // Batch processing per performance
        $batchSize = 100;
        $batches = array_chunk($exportData, $batchSize);
        $currentRow = $startRow;

        foreach ($batches as $batchIndex => $batch) {
            $batchStart = microtime(true);

            // Aggiorna progress ogni 10 batch
            if ($jobId && $batchIndex % 10 === 0) {
                $progress = 70 + (($batchIndex / count($batches)) * 15); // Da 70% a 85%
                $this->updateExportJob($jobId, [
                    'progress' => (int)$progress,
                    'message' => sprintf(
                        'Inserimento dati: batch %d/%d (%d record)...',
                        $batchIndex + 1,
                        count($batches),
                        $totalRecords
                    )
                ]);
            }

            // Prepara array per batch insert
            $batchValues = [];

            foreach ($batch as $record) {
               $rowData = $this->prepareXTravelRowData($record, $columnMapping);
                $batchValues[] = array_values($rowData);
           
           
            }

            // Inserimento batch ottimizzato
            if (!empty($batchValues)) {
                $startCell = 'A' . $currentRow;
                $worksheet->fromArray($batchValues, null, $startCell, false);
                $currentRow += count($batchValues);
            }

            $batchTime = microtime(true) - $batchStart;

            $this->logOperation($logFile, "Batch Excel inserito", [
                'batch_index' => $batchIndex + 1,
                'batch_size' => count($batch),
                'current_row' => $currentRow,
                'batch_time_seconds' => round($batchTime, 3),
                'memory_usage' => memory_get_usage(true)
            ]);

            // Garbage collection periodico
            if ($batchIndex % 20 === 0) {
                gc_collect_cycles();
            }
        }

        $populateTime = microtime(true) - $populateStart;

        $this->logOperation($logFile, "Popolamento Excel completato", [
            'total_rows_written' => $currentRow - $startRow,
            'populate_time_seconds' => round($populateTime, 3),
            'records_per_second' => round($totalRecords / $populateTime, 2)
        ]);
    }

    /**
     * Mapping colonne specifico per i tuoi dati XTravel
     */
    private function getXTravelColumnMapping()
    {
        return [
            'A' => 'th_id',
            'B' => 'stato',
            'C' => 'ruolo_desc',
            'D' => 'Cd_ARClasse2',
            'E' => 'vat_rate',
            'F' => 'cat_biglietti_config',
            'G' => 'cat_hotel_config',
            // Aggiungi altre colonne secondo la struttura del tuo template
            // Puoi aggiungere tutti i campi da xtravelrow che ti servono
        ];
    }

    /**
     * Prepara dati riga per Excel specifico per XTravel
     */
    private function prepareXTravelRowData($record, $columnMapping)
    {
        $rowData = [];

        foreach ($columnMapping as $column => $field) {
            $value = $record[$field] ?? '';

            // Gestione tipi specifici per i tuoi dati
            switch ($field) {
                case 'vat_rate':
                    $rowData[$column] = (float)$value;
                    break;

                case 'th_id':
                    $rowData[$column] = (int)$value;
                    break;

                case 'stato':
                    $rowData[$column] = trim(strtoupper($value));
                    break;

                case 'ruolo_desc':
                    $rowData[$column] = $this->cleanTextForExcel($value);
                    break;

                default:
                    $rowData[$column] = $value;
            }
        }

        return $rowData;
    }

    /**
     * Aggiorna job nel database
     */
    private function updateExportJob($jobId, $data)
    {
        if (empty($jobId)) return;

        try {
            $data['updated_at'] = date('Y-m-d H:i:s');

            Yii::$app->db->createCommand()->update(
                'export_jobs',
                $data,
                ['job_id' => $jobId]
            )->execute();
        } catch (\Exception $e) {
            // Log errore ma non interrompere il processo
            error_log("Errore aggiornamento job {$jobId}: " . $e->getMessage());
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
     * Pulisce testo per Excel
     */
    private function cleanTextForExcel($text)
    {
        if (empty($text)) return '';

        // Rimuovi caratteri non stampabili
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);

        // Limita lunghezza
        if (strlen($text) > 32000) {
            $text = substr($text, 0, 32000) . '...';
        }

        return trim($text);
    }

    /**
     * Genera numero documento
     */
    private function getDocumentNumber()
    {
        return 'XTRAVEL_' . date('Ymd_His');
    }


    private function sendSuccessEmail($email, $username, $jobId, $result, $totalTime, $dataId)
    {
        $logFile = Yii::getAlias('@app/runtime/logs/excel_export_.log');
        try {
            $ris = Xtravelhead::find()->where(['th_id' => $dataId])->asArray()->one();

            // Se $ris è null, l'email fallirebbe qui. Aggiungiamo un check:
            $descrizione = $ris ? $ris['descrizione'] : "Export $dataId";

            $downloadUrl = "https://dashboard.planorys.it:4433/index.php?r=xtravelhead%2Fdownload-export&jobId=" . $jobId;
            $subject = '[' . $descrizione . '] Export dell\'estratto conto';
            $body = "Ciao $username,\n\nIl tuo export è pronto.\nDownload: $downloadUrl";

            $this->logOperation($logFile, "Tento invio email a $email");

            $sent = Yii::$app->mailer->compose()
                ->setFrom(['dashboard@planorys.com' => 'Dashboard Planorys'])
                ->setTo($email)
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();

            if ($sent) {
                $this->logOperation($logFile, "EMAIL INVIATA CON SUCCESSO");
            } else {
                $this->logOperation($logFile, "ERRORE: Mailer ha restituito FALSE");
            }
        } catch (\Exception $e) {
            $this->logOperation($logFile, "ECCEZIONE INVIO EMAIL: " . $e->getMessage());
        }
    }

    /**
     * Invia email di successo
     */
    private function ___sendSuccessEmail($email, $username, $jobId, $result, $totalTime, $dataId)
    {
        try {


$ris=Xtravelhead::find()->where(['th_id'=>$dataId])->asArray()->one();




            $downloadUrl = "https://dashboard.planorys.it:4433/index.php?r=xtravelhead%2Fdownload-export&jobId=". $jobId;

            $subject = '['.$ris['descrizione']. ']  Export dell\'estratto conto  ';
            $body = sprintf(
                "
Ciao %s,

Il tuo export di Estratto Conto  è stato completato con successo!

 

Puoi scaricare il file al seguente link:
%s

Il file sarà disponibile per 7 giorni.

Saluti,
Sistema Export XTravel
            ",
                $username,
                //$jobId,
                //number_format($result['records_count']),
                //$result['file_size'] / 1024 / 1024,
                //$totalTime,
                $downloadUrl
            );

            Yii::$app->mailer->compose()
                ->setFrom(['dashboard@planorys.com' 
                => 'Dashboard Planorys'])
                ->setTo($email)
                ->setBcc('dashboard@planorys.com')
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();

            $logFile = Yii::getAlias('@app/runtime/logs/excel_export_' . '.log');


            $this->logOperation($logFile, "email inviata", [
                'oggetto' => $subject,
                'job_id' => $jobId
            ]);
        } catch (\Exception $e) {
            error_log("Errore invio email successo: " . $e->getMessage());
            $logFile = Yii::getAlias('@app/runtime/logs/excel_export_'  . '.log');


            $this->logOperation($logFile, "email fallita", [
                'oggetto' => $subject,
                'errore'=> $e->getMessage(),
                'job_id' => $jobId
            ]);
        }
    }

    /**
     * Invia email di errore
     */
    private function sendErrorEmail($email, $username, $jobId, $exception)
    {
        try {
            $subject = 'Export XTravel Fallito - ' . $jobId;
            $body = sprintf(
                "
Ciao %s,

Purtroppo il tuo export XTravel è fallito.

Dettagli errore:
- Job ID: %s
- Errore: %s
- Data/Ora: %s

Ti preghiamo di contattare il supporto tecnico.

Saluti,
Sistema Export XTravel
            ",
                $username,
                $jobId,
                $exception->getMessage(),
                date('Y-m-d H:i:s')
            );

            Yii::$app->mailer->compose()
                ->setTo($email)
                ->setBcc('dashboard@planorys.com')
                ->setSubject($subject)
                ->setTextBody($body)
                ->send();
        } catch (\Exception $e) {
            error_log("Errore invio email errore: " . $e->getMessage());
        }
    }


    protected function populateSingleRow(Worksheet $worksheet, $record, $row, $categoryConfigs)
    {
        // Determine category once using pre-loaded config
        $category = $this->determineCategoryOptimized($record['Cd_ARClasse2'], $categoryConfigs);

        // Prepare all values in arrays for batch setting
        $values = [];

        // Column A - x_scdesc
        if (isset($record['x_scdesc'])) {
            $values["A{$row}"] = '   ' . $record['x_scdesc'];
        }

        // Column B - guest
        if (isset($record['guest'])) {
            $values["B{$row}"] = trim($record['guest']);
        }

        // Column C - ruolo (using pre-joined data)
        if (isset($record['ruolo']) && !empty($record['ruolo'])) {
            $values["C{$row}"] = trim($record['ruolo_desc'] ?: $record['ruolo']);
        }

        // Column D - party
        if (isset($record['party'])) {
            $values["D{$row}"] = trim($record['party']);
        }

        // Column E - citta
        if (isset($record['citta'])) {
            $citta = trim($record['citta']);

            // Controllo se il valore è un UUID (uniqueidentifier)
            if (preg_match('/^[0-9a-fA-F-]{36}$/', $citta)) {
                $venue = Xvenue::findOne(['id' => $citta]);
                if ($venue) {
                    $values["E{$row}"] = $venue->citta;
                } else {
                    // Se non trova il record metti il valore grezzo
                    $values["E{$row}"] = $citta;
                }
            } else {
                // Caso normale: non è un UUID, lo scrivi così com'è
                $values["E{$row}"] = $citta;
            }
        }

        // Column F - cd_ar
        if (isset($record['stato'])) {
            $values["F{$row}"] = trim($record['stato']);
        }

        // Column G - descrizione
        if (isset($record['descrizione'])) {
            $values["G{$row}"] = trim($record['descrizione']);
        }

        // Column H - desfor
        /*  if (isset($record['desfor'])) {
            $values["H{$row}"] = trim($record['desfor']);
        }

        // Column I - struttura
        if (isset($record['struttura'])) {
            $values["I{$row}"] = trim($record['struttura']);
        }
*/



        // Gestione Colonna I (Struttura) e Colonna H (Fornitore/Desfor)
        /*   if (isset($record['struttura']) && is_numeric($record['struttura']) && $record['struttura'] > 0) {

            // Cerchiamo la struttura
            $modelloStruttura = \app\models\Xstruttura::findOne($record['struttura']);

            if ($modelloStruttura) {
                // ATTENZIONE: Usa il nome esatto della proprietà nel modello (Descrizione)
                // Se 'Descrizione' è null, usiamo 'Struttura' come fallback
                $values["I{$row}"] = !empty($modelloStruttura->Descrizione)
                    ? trim($modelloStruttura->Descrizione)
                    : trim($modelloStruttura->Struttura);

                // Cerchiamo il fornitore se Cd_cf è presente
                if (!empty($modelloStruttura->Cd_cf)) {
                    $modelloCf = \app\models\Cf::findOne(['Cd_CF' => trim($modelloStruttura->Cd_cf)]);

                    if ($modelloCf) {
                        $values["H{$row}"] = trim($modelloCf->Descrizione);
                    } elseif (isset($record['desfor'])) {
                        // Se non trovo il CF nel database, tengo il valore originale di desfor
                        $values["H{$row}"] = trim($record['desfor']);
                    }
                } elseif (isset($record['desfor'])) {
                    $values["H{$row}"] = trim($record['desfor']);
                }
            } else {
                // Se non trova il record ID nel DB, eseguiamo il fallback
                if (isset($record['desfor'])) $values["H{$row}"] = trim($record['desfor']);
                if (isset($record['struttura'])) $values["I{$row}"] = trim($record['struttura']);
            }
        } else {
            // Logica se 'struttura' non è un ID valido
            if (isset($record['desfor'])) $values["H{$row}"] = trim($record['desfor']);
            if (isset($record['struttura'])) $values["I{$row}"] = trim($record['struttura']);
        }*/

        // Gestione Colonna I (Struttura) e Colonna H (Fornitore/Desfor)
        if (isset($record['struttura']) && is_numeric($record['struttura']) && $record['struttura'] > 0) {

            // 1. Usiamo direttamente db5 per evitare problemi se il model Xstruttura punta al db sbagliato
            $strutturaDb = Yii::$app->db5->createCommand("
                SELECT struttura, citta, Cd_cf
                FROM x_struttura
                WHERE id = :id
            ", [':id' => $record['struttura']])->queryOne();

            if ($strutturaDb) {
                // 2. Costruiamo il nome IDENTICO a quello della DataTables (Es: "Mercure Roma West | Roma-Roma")
                $nomeStruttura = trim($strutturaDb['struttura'] ?? '');

                if (!empty($strutturaDb['citta'])) {
                    $nomeStruttura .= ' | ' . trim($strutturaDb['citta']);
                }

                // Assegniamo il nome formattato alla colonna I
                $values["I{$row}"] = $nomeStruttura;

                // 3. Gestione del Fornitore (Colonna H)
                if (!empty($strutturaDb['Cd_cf'])) {
                    $nomeFornitore = Yii::$app->db5->createCommand("
                        SELECT descrizione
                        FROM cf
                        WHERE cd_cf = :cf
                    ", [':cf' => trim($strutturaDb['Cd_cf'])])->queryScalar();

                    if ($nomeFornitore) {
                        $values["H{$row}"] = trim($nomeFornitore);
                    } elseif (isset($record['desfor'])) {
                        $values["H{$row}"] = trim($record['desfor']);
                    }
                } elseif (isset($record['desfor'])) {
                    $values["H{$row}"] = trim($record['desfor']);
                }
            } else {
                // Se non trova il record ID nel DB, eseguiamo il fallback
                if (isset($record['desfor'])) $values["H{$row}"] = trim($record['desfor']);
                if (isset($record['struttura'])) $values["I{$row}"] = trim($record['struttura']);
            }
        } else {
            // Logica se 'struttura' non è un ID numerico valido (es. testo libero inserito a mano)
            if (isset($record['desfor'])) $values["H{$row}"] = trim($record['desfor']);
            if (isset($record['struttura'])) $values["I{$row}"] = trim($record['struttura']);
        }



        // Hotel-specific fields
        if ($category === 'cat_Hotel') {
            if (isset($record['check_in']) && !empty($record['check_in'])) {
                //   $values["K{$row}"] = $record['check_in'];
                $values["K{$row}"] = date('d/m/Y', strtotime($record['check_in']));
            }
            if (isset($record['check_out']) && !empty($record['check_out'])) {
                // $values["L{$row}"] = $record['check_out'];
                $values["l{$row}"] = date('d/m/Y', strtotime($record['check_out']));
            }
            if (isset($record['qta'])) {
                $values["M{$row}"] = $record['qta'];
            }
        }

        // Ticket-specific fields
        if ($category === 'cat_biglietti') {
            if (isset($record['check_in']) && !empty($record['check_in'])) {
                $values["O{$row}"] = date('d/m/Y', strtotime($record['check_in']));
                //  $values["R{$row}"] = date('d/m/Y', strtotime($record['check_in'])); // Duplicate
                $values["R{$row}"] = date('H:i', strtotime($record['check_in']));
            }
            if (isset($record['citta_da'])) {
                $values["P{$row}"] = trim($record['citta_da']);
            }
            if (isset($record['citta_a'])) {
                $values["Q{$row}"] = trim($record['citta_a']);
            }
            if (isset($record['pnr'])) {
                $values["S{$row}"] = trim($record['pnr']);
            }
            if (isset($record['nr_biglietto'])) {
                $values["T{$row}"] = trim($record['nr_biglietto']);
            }
            if (isset($record['qta'])) {
                $values["U{$row}"] = $record['qta'];
            }
        }

        // Common fields
        if (isset($record['data_pg']) && !empty($record['data_pg'])) {
            $values["W{$row}"] = date(
                'd/m/Y',
                strtotime($record['data_pg']));
        }
        if (isset($record['cd_pg']) && !empty($record['cd_pg'])) {
            $values["X{$row}"] = $record['cd_pg'];
        }
        if (isset($record['contabile'])) {
            $values["Y{$row}"] = '[' . trim($record['contabile']) . ']';
        }
        if (isset($record['prezzo'])) {
            $values["Z{$row}"] = $record['prezzo'];
        }

        // VAT rate (using pre-joined data)
        if (isset($record['vat_rate'])) {
            $values["AA{$row}"] = (int)$record['vat_rate'];
        }

        // Tax per unit
        if (isset($record['tax']) && isset($record['qta'])
             && $record['qta'] > 0) {
            $values["AC{$row}"] = $record['tax'] / $record['qta'];
        }
        if (
            isset($record['tax_unit']) && isset($record['qta'])
            && $record['qta'] > 0
        ) {
            $values["AC{$row}"] = $record['tax_unit'] ;
        }
        // Fee fields
        if (isset($record['fee'])) {
            if (isset($record['fee_perc']) && $record['fee_perc'] != 0) {
                $values["AF{$row}"] = trim($record['fee_perc']) . '%';
            }
            $values["AG{$row}"] = $record['fee'];
        }

        // Batch set all values
        foreach ($values as $cell => $value) {
            $worksheet->setCellValue($cell, $value);
        }

        // Set text format for column A if it has a value
        if (isset($values["A{$row}"])) {
            $worksheet->getStyle("A{$row}")->getNumberFormat()->setFormatCode('@');
        }
    }

    protected function determineCategoryOptimized($articleClass, $categoryConfigs)
    {
        foreach ($categoryConfigs as $categoryName => $categoryValues) {
            if (!empty($categoryValues) && in_array($articleClass, explode(',', $categoryValues))) {
                return $categoryName;
            }
        }
        return '';
    }

    protected function populatelastRow(Worksheet $worksheet, $record, $row, $categoryConfigs,$dataId, $jobId){
        $value=Xtravelhead::find()->where(['th_id'=>$dataId])->asArray()->one();
        $job=Export_jobs::find()->where(['job_id'=>$jobId])->asArray()->one();
        $worksheet->setCellValue("C3", $value['descrizione']);
        $worksheet->setCellValue("C2", $job['created_at'] );
    }

    /**
     * Cancella i file Excel generati più vecchi di 7 giorni
     * Uso: php yii background-export/cleanup-old-files
     */
    public function actionCleanupOldFiles()
    {
        $exportDir = Yii::getAlias('@app/web/xls_REP');
        $days = 7;
        $now = time();
        $deleted = 0;

        if (is_dir($exportDir)) {
            $files = glob($exportDir . '/*.xlsx');

            foreach ($files as $file) {
                if (is_file($file)) {
                    $fileAge = $now - filemtime($file);
                    if ($fileAge > ($days * 24 * 60 * 60)) {
                        if (@unlink($file)) {
                            $deleted++;
                            echo "Eliminato: " . basename($file) . "\n";
                        }
                    }
                }
            }
        }

        echo "Cleanup completato: eliminati {$deleted} file più vecchi di {$days} giorni.\n";
    }
}
