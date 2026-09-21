<?php  namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\DocumentForm;
use app\models\DocumentAnalysis; // Il modello della tabella SQL Server

class AnalysisController extends Controller
{

    public function actionUpload()
    {
        $model = new DocumentForm();
        $risultato = null;

        if ($model->load(Yii::$app->request->post())) {
            $model->pdfFile = \yii\web\UploadedFile::getInstance($model, 'pdfFile');

            // LOGICA: Se c'è il testo incollato, usiamo quello (consuma meno quota!)
            if (!empty($model->testoDocumento)) {
                $risultato = Yii::$app->gemini->call($model->testoDocumento, null, 'text/plain');
            }
            // ALTRIMENTI: Se è stato caricato un file
            elseif ($model->pdfFile && $model->validate()) {
                $content = base64_encode(file_get_contents($model->pdfFile->tempName));
                $risultato = Yii::$app->gemini->call("Analizza questo PDF", $content, $model->pdfFile->type);
            }
        }

        return $this->render('upload', [
            'model' => $model,
            'risultato' => $risultato
        ]);
    }

public function zzzzactionUpload()
{
$model = new DocumentForm();
$risultato = null; // Variabile per contenere la risposta dell'IA

if (Yii::$app->request->isPost) {
$model->pdfFile = UploadedFile::getInstance($model, 'pdfFile');

if ($model->validate()) {
// 1. Leggi il file
$content = base64_encode(file_get_contents($model->pdfFile->tempName));

// 2. Prepara il Prompt (istruzioni per l'IA)
$prompt = "Sei un analista dati. Analizza questo documento forestale ucraino. 
Rispondi SOLO con un oggetto JSON con queste chiavi: 
{
  'tipo': 'Contratto' o 'Visura' o 'Permesso',
  'soggetto': 'Nome azienda o persona',
  'data_scadenza': 'YYYY-MM-DD',
  'valido': true/false
}
Se è il permesso di taglio, la scadenza è il 31.03.2026.";

try {
// 3. Chiamata al componente Gemini che hai creato
$risultato = Yii::$app->gemini->call($prompt, $content);

                    $jsonPulito = str_replace(['```json', '```'], '', $risultato);

                    $analysis = new DocumentAnalysis();
                    $analysis->filename = $model->pdfFile->name;
                    $analysis->raw_response = trim($jsonPulito); // Salva il JSON pulito
                    $analysis->created_at = time();
                    $analysis->save();

} catch (\Exception $e) {
Yii::$app->session->setFlash('error', 'Errore: ' . $e->getMessage());
}
}
}

// FONDAMENTALE: Passiamo sia il $model che il $risultato alla vista
return $this->render('upload', [
'model' => $model,
'risultato' => $risultato
]);
}
}

?>