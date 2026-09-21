<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use Smalot\PdfParser\Parser;

class IngestController extends Controller
{
    public function actionIndex()
    {
        // 1. Configurazione ambiente e reset buffer
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        ob_implicit_flush(true);
        while (ob_get_level()) ob_end_flush();

        echo "<html><body style='font-family: monospace; background: #1e1e1e; color: #dcdcdc; padding: 20px;'>";
        echo "<h2>🚀 Avvio Ingest Ultra-Corazzato DashDemo</h2><hr>";

        $folders = ['@app/controllers', '@app/models', '@app/views', '@app/commands', '@app/components', '@app/web/uploads'];
        $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);
        $totalChunks = 0;

        foreach ($folders as $folderAlias) {
            $path = Yii::getAlias($folderAlias);
            if (!is_dir($path)) continue;

            echo "<h3>📂 Cartella: $folderAlias</h3>";
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
            
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;
                
                $extension = strtolower($file->getExtension());
                if (!in_array($extension, ['php', 'js', 'txt', 'html', 'htm', 'json', 'pdf'])) continue;

                $filePath = $file->getPathname();
                $filename = $file->getFilename();

                // Salta file eccessivamente grandi (Sopra i 500KB)
                if ($file->getSize() > 500000) {
                    echo "⏩ <span style='color:yellow'>Saltato (Troppo pesante): $filename</span><br>";
                    continue;
                }

                echo "📄 Elaborazione: <strong>$filename</strong> ... ";
                flush();

                try {
                    $content = "";
                    if ($extension === 'pdf') {
                        $parser = new Parser();
                        $content = $parser->parseFile($filePath)->getText();
                    } else {
                        $content = file_get_contents($filePath);
                        // Pulizia UTF-8 per evitare crash JSON
                        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
                        $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $content);
                    }

                    if (empty(trim($content))) {
                        echo "<span style='color:gray'>Vuoto o illeggibile.</span><br>";
                        continue;
                    }

                    // --- NUOVA LOGICA DI CHUNKING PER CARATTERI (Anti-Context Error) ---
                    // Dividiamo il file ogni 3000 caratteri (circa 800-1000 token)
                    $maxChars = 3000;
                    $contentLength = mb_strlen($content);
                    $fileChunks = 0;

                    for ($i = 0; $i < $contentLength; $i += $maxChars) {
                        $chunkText = mb_substr($content, $i, $maxChars);
                        if (mb_strlen(trim($chunkText)) < 10) continue;

                        // 1. OLLAMA EMBEDDING
                        $ollamaRes = $client->createRequest()
                            ->setMethod('POST')
                            ->setUrl('http://127.0.0.1:11434/api/embeddings')
                            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                            ->setData(['model' => 'nomic-embed-text', 'prompt' => $chunkText])
                            ->send();

                        if (!$ollamaRes->isOk) {
                            $errorDetail = $ollamaRes->content ?: "Errore sconosciuto";
                            echo "❌ <span style='color:red'>Errore Ollama: $errorDetail</span> ";
                            // Se fallisce il singolo chunk, passiamo al prossimo invece di bloccare il file
                            continue;
                        }

                        $vector = $ollamaRes->data['embedding'] ?? null;
                        if (!$vector) continue;

                        // 2. QDRANT STORAGE
                        // Usiamo un ID univoco basato sul percorso e sulla posizione del chunk
                        $uniqueId = crc32($filePath . $i);
                        $client->createRequest()
                            ->setMethod('PUT')
                            ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points?wait=true')
                            ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                            ->setData([
                                'points' => [[
                                    'id' => $uniqueId,
                                    'vector' => $vector,
                                    'payload' => [
                                        'text' => $chunkText,
                                        'filename' => $filename,
                                        'full_path' => $filePath,
                                        'folder' => $folderAlias,
                                        'timestamp' => date('Y-m-d H:i:s')
                                    ]
                                ]]
                            ])->send();

                        $totalChunks++;
                        $fileChunks++;
                    }

                    echo "<span style='color:#4caf50'>OK</span> ($fileChunks pezzi)<br>";

                } catch (\Exception $e) {
                    echo "❌ <span style='color:red'>Errore Critico: " . $e->getMessage() . "</span><br>";
                }
                flush();
            }
        }
        echo "<hr><h2>✅ Ingest Completato con Successo!</h2>";
        echo "<p>Frammenti totali inviati a Qdrant: $totalChunks</p></body></html>";
    }


public function actionCrmIngest()
{
    set_time_limit(0);
    ini_set('memory_limit', '2048M');

    ob_implicit_flush(true);
    while (ob_get_level()) ob_end_flush();

    echo "<html><body style='font-family: monospace; background: #1e1e1e; color: #dcdcdc; padding: 20px;'>";
    echo "<h2>📊 Ingest CRM (xestrazione) - Protezione Duplicati Attiva</h2><hr>";

    $client = new \yii\httpclient\Client(['transport' => 'yii\httpclient\CurlTransport']);
    
    try {
        echo "🔍 Recupero record... ";
        $rows = Yii::$app->db6->createCommand("SELECT * FROM xestrazione")->queryAll();
        $count = count($rows);
        echo "<span style='color:#4caf50'>OK</span> ($count record trovati)<br><br>";
        flush();

        foreach ($rows as $index => $row) {
            // 1. GENERAZIONE ID UNIVOCO FISSO
            $uniqueId = crc32('crm_v3_' . $row['tipo'] . '_' . $row['tid'] . '_' . $row['pid']);

            // Recuperiamo il codice soggetto per il log a video
            $codiceCliente = !empty($row['codicesoggetto']) 
            ? $row['codicesoggetto'] : 'N.D.';

            echo "📦 [$index/$count] Cliente: <strong style='color:#00bc8c;'>$codiceCliente</strong> - ";

            // 2. COSTRUZIONE TESTO SEMANTICO
            $semanticText = "--- CRM: {$row['soggetto']} ---\n" .
                            "AREA: {$row['AREA']} | TIPO: {$row['xtipologia']}\n" .
                            "ATTIVITÀ: {$row['oggetto']} | STATO: {$row['STATUS']}\n" .
                            "DATA: {$row['dataevento']} | ORE: {$row['oredelta']}\n" .
                            "TECNICO: {$row['utente_destinatario']}\n" .
                            "NOTE: " . strip_tags($row['descrizioneprogetto'] ?? '');

            // 3. EMBEDDING OLLAMA
            $ollamaRes = $client->createRequest()
                ->setMethod('POST')
                ->setUrl('http://127.0.0.1:11434/api/embeddings')
                ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                ->setData(['model' => 'nomic-embed-text', 'prompt' => $semanticText])
                ->send();

            if ($ollamaRes->isOk) {
                // 4. SALVATAGGIO IN QDRANT
                $payload = array_merge([
                    'text' => $semanticText,
                    'source' => 'vtiger_xestrazione',
                    'type' => 'crm_record',
                    'ingest_date' => date('Y-m-d H:i:s')
                ], $row);

                $client->createRequest()
                    ->setMethod('PUT')
                    ->setUrl('http://127.0.0.1:6333/collections/progetto_dashdemo/points?wait=true')
                    ->setFormat(\yii\httpclient\Client::FORMAT_JSON)
                    ->setData(['points' => [['id' => $uniqueId, 'vector' => $ollamaRes->data['embedding'], 'payload' => $payload]]])
                    ->send();

                echo "<span style='color:#4caf50'>Sincronizzato correttamente</span> (ID: $uniqueId)<br>";
            } else {
                echo "❌ <span style='color:red'>Errore Embedding per $codiceCliente</span><br>";
            }

            // Flush ogni 5 record per un feedback più fluido
            if ($index % 5 == 0) flush();
        }
    } catch (\Exception $e) {
        echo "❌ <span style='color:red'>Errore Critico: " . $e->getMessage() . "</span><br>";
    }

    echo "<hr><h2>✅ Sincronizzazione CRM Ultimata!</h2></body></html>";
}

}