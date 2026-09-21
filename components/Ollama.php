<?php
namespace app\components;

use yii\base\Component;
use yii\httpclient\Client;

class Ollama extends Component {
    
    public $host = 'http://127.0.0.1:11434';
    public $model = "dashboard-ai";

    /**
     * @param string $model Modello da usare
     * @param array $messages Storico conversazione
     * @param array|null $tools Lista dei tools (opzionale)
     */
    public function chat($model, $messages, $tools = null)
    {
        $targetModel = $model ? $model : $this->model;

        $client = new Client([
            'transport' => 'yii\httpclient\CurlTransport'
        ]);

        // Prepariamo i dati per la richiesta
        $payload = [
            'model' => $targetModel,
            'messages' => $messages,
            'stream' => false,
            'options' => [
                'temperature' => 0.1,
                'num_ctx' => 8192, 
                'num_thread' => 7,
            ]
        ];

        // Se sono stati passati dei tools, li aggiungiamo al payload
        if ($tools !== null) {
            $payload['tools'] = $tools;
        }

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl($this->host . '/api/chat')
            ->setData($payload)
            ->setFormat(Client::FORMAT_JSON)
            ->setOptions([
                CURLOPT_CONNECTTIMEOUT => 12,
                CURLOPT_TIMEOUT => 1200,
            ])
            ->send();

        if ($response->isOk) {
            // FONDAMENTALE: Restituiamo tutto l'array data
            // così il controller può leggere sia 'content' che 'tool_calls'
            return $response->data;
        }

        return false;
    }
}