<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use app\models\Agente;
use app\models\AgentiFiles;
use yii\db\Exception;

class BustepagaController extends Controller
{
    public $path = '@app/web/uploads/buste_paga';
    public $processedPath = '@app/web/uploads/buste_paga/processed';


    public function actionImport2()
    {
        echo "Funziona!";
    }





    public function actionImport()
    {
        $dir = Yii::getAlias($this->path);
        $processedDir = Yii::getAlias($this->processedPath);

        if (!is_dir($dir)) {
            Console::error("Cartella non trovata: $dir");
            return;
        }

        if (!is_dir($processedDir)) {
            mkdir($processedDir, 0777, true);
        }

        $files = glob($dir . '/*.PDF');

        if (!$files) {
            Console::output("Nessun file trovato.");
            return;
        }

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            Console::output("Elaboro: $fileName");
           
            $parts = explode('_', $fileName);
            if (count($parts) < 4) {
                $this->logFile($fileName, null, 'ERRORE', 'Nome file non valido');
                continue;
            }

            $matricola = $parts[1];
            $annoMese = $parts[4];
            $anno = substr($annoMese, 0, 4);

            $agente = Agente::find()->where(['like', 'Note_Agente', $matricola])->one();

            if (!$agente) {
                $this->logFile($fileName, null, 'ERRORE', "Nessun agente trovato per matricola: $matricola");
                Console::error("Nessun agente trovato per matricola: $matricola");
                continue;
            }

            try {
                $model = new AgentiFiles();
                $model->cd_agente = $agente->Cd_Agente;
                $model->descrizione = "Busta paga $annoMese";
                $model->nota = "Import automatica da file $fileName";
                $model->cartella = $anno;
                $model->cartella_padre = 'Buste paga';
                $model->nome_file = pathinfo($fileName, PATHINFO_FILENAME);
                $model->estenzione = 'pdf';
                $model->file = $fileName;
                $model->kiave_arch = null;

                // IMPORTANTE: leggi il file come binario
                $model->f_content = file_get_contents($filePath);

                // Data scadenza
                $dataString = substr($parts[6] ?? '', 0, 8);
                if (preg_match('/^\d{8}$/', $dataString)) {
                    $model->data_scadenza = substr($dataString, 0, 4)
                        . '-' . substr($dataString, 4, 2)
                        . '-' . substr($dataString, 6, 2);
                }

                if ($model->save(false)) {  // false = salta la validazione
                    Console::output("✔ Salvato → agente {$agente->Cd_Agente}");

                    // Sposta file in processed/
                    $dest = $processedDir . '/' . $fileName;
                    if (!rename($filePath, $dest)) {
                        Console::error("Errore nello spostamento del file: $fileName");
                    }

                    $this->logFile($fileName, $agente->Cd_Agente, 'OK', 'Import riuscito');
                } else {
                    Console::error("Errore salvataggio: " . json_encode($model->errors));
                    $this->logFile($fileName, $agente->Cd_Agente, 'ERRORE', json_encode($model->errors));
                }
            } catch (Exception $e) {
                Console::error("Eccezione: " . $e->getMessage());
                $this->logFile($fileName, $agente->Cd_Agente, 'ERRORE', $e->getMessage());
            }
        }
    }

    /**
     * Registra un log in DB
     */
    protected function logFile($fileName, $cdAgente, $esito, $message)
    {
        Yii::$app->db->createCommand()->insert('agenti_files_log', [
            'file_name' => $fileName,
            'cd_agente' => $cdAgente,
            'esito'     => $esito,
            'message'   => $message,
            'created_at' => new \yii\db\Expression('GETDATE()'),
        ])->execute();
    }
}
