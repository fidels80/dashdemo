<?php
// Abilitiamo la visualizzazione degli errori a schermo per il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carichiamo le librerie del tuo progetto
require_once __DIR__ . '/../vendor/autoload.php';

echo "<h2>Test isolato di invio email con Relay IP (Porta 25 + TLS)</h2>\n";

try {
    $host='ufficio2000-it01i.mail.protection.outlook.com';
    //$host = 'ufficio-2000-it.mail.protection.outlook.com';
    // 1. Configurazione del Trasporto
    // Usiamo l'host, la porta 25 e forziamo l'uso della crittografia 'tls' come richiesto da Microsoft
    $transport = (new Swift_SmtpTransport($host, 25, 'tls'))
    //->setPassword('ptsthtcspfjgltx')
        ->setStreamOptions([
            'ssl' => [
                'allow_self_signed' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

    // 2. Creazione del "Postino" (Mailer)
    $mailer = new Swift_Mailer($transport);

    // 3. Creazione del Messaggio
    $message = (new Swift_Message('Test Connessione Office 365 tramite IP e TLS'))
        // Mittente: deve essere un'email valida del tuo dominio
        ->setFrom(['dashboard@ufficio-2000.it' => 'Test Dashboard'])
        // Destinatario: la tua email
        ->setTo(['marco.cardinale@ilvbc.it']) 
        ->setBody('Ciao Marco! Se leggi questa email, il Relay SMTP su Porta 25 con STARTTLS funziona perfettamente.');

    // 4. Invio
    echo "<p>Tentativo di connessione a Microsoft in corso (Porta 25 con crittografia TLS)...</p>\n";
    $result = $mailer->send($message);

    if ($result) {
        echo "<h3 style='color: green;'>Successo! L'email è stata accettata da Microsoft e inviata.</h3>\n";
    } else {
        echo "<h3 style='color: orange;'>Processo completato, ma nessuna email inviata.</h3>\n";
    }

} catch (Exception $e) {
    // Stampa dell'errore
    echo "<h3 style='color: red;'>Errore durante l'invio:</h3>\n";
    echo "<pre style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb;'>\n";
    echo $e->getMessage();
    echo "\n</pre>\n";
}
?>