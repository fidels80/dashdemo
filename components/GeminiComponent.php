<?php

namespace app\components; // 'app' è il namespace standard in Yii2 Basic

use yii\base\Component;
use GuzzleHttp\Client;
use Exception;

class GeminiComponent extends Component
{
    public $apiKey;


    public function call($prompt, $testoDocumento = null)
    {
        $client = new Client(['timeout' => 60.0]);
        // Usiamo l'endpoint che ha funzionato in Postman
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

        // Costruiamo il contenuto unendo il prompt al testo del documento
        // Rafforziamo il prompt per forzare l'italiano e una struttura pulita
        $istruzioniSistema = "Sei un assistente esperto in analisi di documenti legali. 
                          Rispondi SEMPRE in lingua ITALIANA. 
                          Analizza il testo seguente e fanne un riassunto strutturato. ed estrai tutti i dati possibili  che potrei analizzare";

        $testoCompleto = $istruzioniSistema . "\n\nRICHIESTA UTENTE: " . $prompt .
            "\n\nTESTO DOCUMENTO DA ANALIZZARE:\n" . $testoDocumento;

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $testoCompleto]
                    ]
                ]
            ]
        ];

        try {
            $response = $client->post($url, [
                'json' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => 'AIzaSyClj2fQ9uYl2Shks7qSp5wJrVyBBRdQQrE',
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                return $result['candidates'][0]['content']['parts'][0]['text'];
            }

            return "Struttura risposta imprevista.";
        } catch (Exception $e) {
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                return "Dettaglio Errore Google: " . $e->getResponse()->getBody()->getContents();
            }
            return "Errore connessione: " . $e->getMessage();
        }
    }
    public function callold($prompt, $base64Content, $mimeType = 'application/pdf')
    {
       // $client = new Client();
        // $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;
        // Prova prima questa versione (v1)
        // Seconda opzione (v1beta con versione specifica)
        //  $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=" . $this->apiKey;
        // Cambia l'URL in v1 (versione stabile) e usa il nome modello semplice
        $client = new Client([
            'timeout' => 30.0, // Aumenta il timeout a 30 secondi
        ]);
         
$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-001:generateContent?key=" . $this->apiKey;
        // Il resto del codice (il corpo $body) rimane uguale.  
        try {
            $body = [
                'contents' => [[
                    'parts' => [
                        ['text' => $prompt],
                        ['inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $base64Content
                        ]]
                    ]
                ]]
            ];

            $response = $client->post($url, ['json' => $body]);
            $result = json_decode($response->getBody()->getContents(), true);

            // Restituisce il testo puro (che sarà il tuo JSON di risposta)
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } catch (Exception $e) {
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                // Questo ti mostra l'errore REALE di Google senza tagli
                return "Dettaglio Errore Google: " . $e->getResponse()->getBody()->getContents() . $url;
            }
            return "Errore connessione Gemini: " . $e->getMessage();
        }
    }


    public function sssscall($prompt, $base64Content, $mimeType = 'application/pdf')
    {
        $client = new \GuzzleHttp\Client();
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent";

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $base64Content
                            ]
                        ]
                    ]
                ]
            ]
        ];

        try {
            $response = $client->post($url, [
                'json' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $this->apiKey, // La chiave che hai usato in Postman
                ]
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            return $result['candidates'][0]['content']['parts'][0]['text'];
        } catch (\Exception $e) {
            if ($e instanceof \GuzzleHttp\Exception\ClientException) {
                return "Errore Google: " . $e->getResponse()->getBody()->getContents();
            }
            return "Errore: " . $e->getMessage();
        }
    }
    }



 /*curl "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent" \
  -H 'Content-Type: application/json' \
  -H 'X-goog-api-key: AIzaSyAa3ztbvPCZVUiehEArPSwhuwcQb_qgCxY' \
  -X POST \
  -d '{
    "contents": [
      {
        "parts": [
          {
            "text": "Explain how AI works in a few words"
          }
        ]
      }
    ]
  }'*/