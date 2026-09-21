<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\httpclient\Client;

class TicketingestController extends Controller
{
    public function actionIndex()
    {
        set_time_limit(0);
        ini_set('memory_limit', '2048M'); // Aumentata per gestire l'array completo

        echo "<h2>🚀 Ingest Totale (Vista xestrazione - Full Fields)</h2><hr>";
        $client = new Client(['transport' => 'yii\httpclient\CurlTransport']);

        // Recuperiamo tutto dalla tua vista
        $sql = "SELECT * FROM xestrazione"; 
        $rows = Yii::$app->db6->createCommand($sql)->queryAll();

        echo "📦 Caricamento di " . count($rows) . " record con metadati completi...<br><br>";

        foreach ($rows as $index => $row) {
            // 1. COSTRUZIONE DEL TESTO SEMANTICO (Per il "ragionamento" dell'IA)
            // Usiamo i campi principali per creare una descrizione leggibile
            $semanticText = "--- DETTAGLIO OPERATIVO CRM ---\n" .
                            "Soggetto: {$row['soggetto']} (Cod: {$row['codicesoggetto']})\n" .
                            "Area/Settore: {$row['AREA']} - {$row['xtipologia']}\n" .
                            "Attività: {$row['oggetto']} ({$row['TIPOEVENTO']})\n" .
                            "Status: {$row['STATUS']} (Cod. Stato: {$row['codicestatoevento']})\n" .
                            "Eseguito da: {$row['utente_destinatario']} | Creato da: {$row['utentecreatore']}\n" .
                            "Data Evento: {$row['dataevento']} | Ore Lavorate: {$row['oredelta']}\n" .
                            "Progetto: {$row['codiceprogetto']}\n" .
                            "Note Tecniche: " . strip_tags($row['descrizioneprogetto'] ?? 'Nessuna');

            // 2. EMBEDDING
            try {
                $ollamaRes = $client->createRequest()
                    ->setMethod('POST')
                    ->setUrl('http://127.0.0.1:11434/api/embeddings')
                    ->setFormat(Client::FORMAT_JSON)
                    ->setData(['model' => 'nomic-embed-text', 'prompt' => $semanticText])
                    ->send();

                if (!$ollamaRes->isOk) throw new \Exception("Errore Ollama");
                $vector = $ollamaRes->data['embedding'];

                // 3. PAYLOAD COMPLETO (Includiamo TUTTO l'array $row)
                // Puliamo i dati per assicurarci che siano compatibili con JSON
                $fullPayload = array_merge([
                    'filename' => "CRM-RECORD-" . ($row['tid'] ?: $row['pid']),
                    'text' => $semanticText, // Questo serve per la ricerca
                    'type' => 'crm_full_record',
                    'ingest_date' => date('Y-m-d H:i:s')
                ], $row); // <--- Qui iniettiamo tutti i campi della vista (soggetto, oredelta, custom1, custom2, etc.)

                // Invio a Qdrant
                $client->createRequest()
                    ->setMethod('PUT')
                    ->setUrl("http://127.0.0.1:6333/collections/progetto_dashdemo/points?wait=true")
                    ->setFormat(Client::FORMAT_JSON)
                    ->setData([
                        'points' => [[
                            'id' => 3000000 + $index, // Offset dedicato per questa vista
                            'vector' => $vector,
                            'payload' => $fullPayload
                        ]]
                    ])->send();

                if ($index % 50 == 0) {
                    echo "✅ Elaborati $index record...<br>";
                    flush();
                }

            } catch (\Exception $e) {
                echo "❌ Errore al record $index: " . $e->getMessage() . "<br>";
            }
        }

        echo "<h3>🎉 Ingest concluso! L'IA ora possiede l'intera vista xestrazione.</h3>";
    }
}

?>