<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use app\models\Presenze;
use app\models\Personale;

class GeobadgeConnector extends Component
{
    public $apiKey = '019d25d9-bf04-74ed-8669-3bcee0fa62da.019d25d9-bef1-7f05-8646-874db0abacde';
    public $baseUrl = 'https://mytest.geobadge.com/wa'; // URL dallo Swagger

    private $_token;

    /**
     * Ottiene il Token di accesso (OAuth2 style dallo swagger)
     */
    public function getToken()
    {
        if ($this->_token) return $this->_token;

        $client = new Client(['baseUrl' => $this->baseUrl]);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setUrl('public/ApiToken')
            ->setData(['ApiKey' => $this->apiKey])
            ->send();

        if ($response->isOk) {
            // Dallo swagger: la chiave è 'access_token'
            $this->_token = $response->data['access_token']; 
            return $this->_token;
        }
        
        Yii::error("Errore ottenimento Token Geobadge: " . $response->content);
        return false;
    }

    /**
     * Metodo generico per chiamate GET autorizzate
     */
 private function getRequest($endpoint, $params = [])
{
    $token = $this->getToken();
    if (!$token) return false;

    $client = new Client(['baseUrl' => $this->baseUrl]);
    $request = $client->createRequest()
        ->setMethod('GET')
        ->setUrl('api/public/v1/' . $endpoint)
        ->addHeaders(['Authorization' => 'Bearer ' . $token])
        ->setData($params);

    // LOG DELLA CHIAMATA (Parametri e URL finale)
    Yii::info("GEOBADGE CALL URL: " . $request->getFullUrl(), "application");
    Yii::info("GEOBADGE PARAMS: " . json_encode($params), "application");

    $response = $request->send();

    if ($response->isOk) {
        return $response->data;
    }
    
    Yii::error("--- ERRORE GEOBADGE ---", "application");
    Yii::error("URL CHIAMATO: " . $request->getFullUrl());
    Yii::error("RISPOSTA SERVER: " . $response->content);
    
    return false;
}


    // --- METODI RICHIAMABILI ---

    // 1. Sincronizza Personale (Persona)
    public function old_syncPersonale()
    {
        $persone = $this->getRequest('Persona/Elenco');
        if (!$persone) return false;

        $risultati = ['creati' => 0, 'aggiornati' => 0];
        foreach ($persone as $p) {
            $model = Personale::findOne(['geobadge_id' => (string)$p['Id']]);
            if (!$model) {
                $model = new Personale();
                $model->geobadge_id = (string)$p['Id'];
                $risultati['creati']++;
            } else {
                $risultati['aggiornati']++;
            }
            $model->nome = $p['Nome'];
            $model->cognome = $p['Cognome'];
            $model->codice_fiscale = $p['CodiceFiscale'];
            $model->email = $p['Email'];
            $model->save();
        }
        return $risultati;
    }

    // 2. Sincronizza Timbrature
    public function old_syncTimbrature($dataInizio = null, $dataFine = null)
    {
        $params = [];
        if ($dataInizio) $params['dataInizio'] = $dataInizio;
        if ($dataFine) $params['dataFine'] = $dataFine;

        $timbrature = $this->getRequest('Timbratura/Elenco', $params);
        if (!$timbrature) return false;

        foreach ($timbrature as $t) {
            // Qui inserisci la tua logica di salvataggio in tabella Presenze
            // Nota dallo swagger: 'Verso' indica se Entrata o Uscita
        }
        return count($timbrature);
    }

    // Sincronizza il personale usando le chiavi MAIUSCOLE
    public function syncPersonale()
    {
        $persone = $this->getRequest('Persona/Elenco');
        if (!$persone) return false;

        $creati = 0;
        $aggiornati = 0;

        foreach ($persone as $p) {
            // Usiamo 'Id' e 'CodiceFiscale' con la maiuscola come da tuo test
            $model = Personale::findOne(['geobadge_id' => (string)$p['Id']]);

            if (!$model && !empty($p['CodiceFiscale'])) {
                $model = Personale::findOne(['codice_fiscale' => $p['CodiceFiscale']]);
            }

            if (!$model) {
                $model = new Personale();
                $model->geobadge_id = (string)$p['Id'];
                $model->data_inserimento = date('Y-m-d H:i:s');
                $creati++;
            } else {
                if (empty($model->geobadge_id)) $model->geobadge_id = (string)$p['Id'];
                $aggiornati++;
            }

            $model->nome = $p['Nome'] ?? '';
            $model->cognome = $p['Cognome'] ?? '';
            $model->codice_fiscale = $p['CodiceFiscale'] ?? $model->codice_fiscale;
            $model->email = $p['Email'] ?? $model->email;
            $model->cellulare = $p['TelefonoMobile'] ?? $model->cellulare;
            $model->stato_attivo = 1;

            if (!$model->save()) {
                Yii::error("Errore salvataggio dipendente ID {$p['Id']}: " . json_encode($model->getErrors()));
            }
        }
        return ['creati' => $creati, 'aggiornati' => $aggiornati];
    }

    // Sincronizza le timbrature correggendo i parametri
/**
     * Sincronizza i Rapporti Contrattuali per mappare le timbrature ai dipendenti
     */
    // In GeobadgeConnector.php

public function syncRapporti()
{
    // Recupera l'elenco dei rapporti contrattuali (dove c'è il legame Persona-Contratto)
    $rapporti = $this->getRequest('RapportoContrattuale/Elenco');
    if (!$rapporti) return false;

    $aggiornati = 0;
    foreach ($rapporti as $r) {
        // Troviamo il dipendente usando l'Id della Persona
        $model = Personale::findOne(['geobadge_id' => (string)$r['PersonaId']]);
        
        if ($model) {
            // Salviamo l'Id del Rapporto (quello che userà la timbratura)
            $model->geobadge_rapporto_id = (string)$r['Id'];
            if ($model->save()) {
                $aggiornati++;
            }
        }
    }
    return $aggiornati;
}
/**
 * Recupera le timbrature grezze (senza filtri) per scopi di debug
 */
public function getTimbratureGrezze()
{
    return $this->getRequest('Timbratura/Elenco');
}
    /**
     * Sincronizza le timbrature con una gestione delle date più robusta
     */
 public function syncTimbrature($dataInizio = null)
{
    // Se non passiamo una data, partiamo da 30 giorni fa (sicuro entro i 3 mesi)
    $tsInizio = $dataInizio ? strtotime($dataInizio) : strtotime('-30 days');
    
    // Vincolo Geobadge: non superare i 3 mesi
    $treMesiFa = strtotime('-3 months + 1 day');
    if ($tsInizio < $treMesiFa) {
        $tsInizio = $treMesiFa;
    }

    // Formattazione esatta come da tuo test Postman
    $params = [
        'dataInizio' => date('Y-m-d\TH:i:s.000', $tsInizio),
        'dataFine'   => date('Y-m-d\TH:i:s.000') // Adesso
    ];

    $timbrature = $this->getRequest('Timbratura/Elenco', $params);
    
    if ($timbrature === false) return false;

    // Se arrivano dati, procediamo al salvataggio
    $salvate = 0;
    if (is_array($timbrature)) {
        foreach ($timbrature as $t) {
            // ... logica di salvataggio già discussa ...
            $salvate++;
        }
        return count($timbrature);
    }
    
    return 0;
}
    public function getRapporti() { return $this->getRequest('RapportoContrattuale/Elenco'); }
    // 3. Elenco Clienti
    public function getClienti()
    {
        return $this->getRequest('Cliente/Elenco');
    }

    // 4. Elenco Servizi (Commessi/Cantieri)
    public function getServizi()
    {
        return $this->getRequest('Servizio/Elenco');
    }

    // 5. Elenco Luoghi
    public function getLuoghi()
    {
        return $this->getRequest('Luogo/Elenco');
    }
    // Aggiungi questo per recuperare le persone senza salvarle (solo per il test)
public function getPersone()
{
    return $this->getRequest('Persona/Elenco');
}
// Da aggiungere se le timbrature rimangono a zero
public function getAttivita($dataInizio) {
    return $this->getRequest('TipoAttivita/Elenco'); // O l'endpoint specifico per i rapportini
}
}