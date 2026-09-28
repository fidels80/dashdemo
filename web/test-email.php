<?php
// Abilitiamo la visualizzazione degli errori a schermo per il debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carichiamo le librerie del tuo progetto
require_once __DIR__ . '/../vendor/autoload.php';

echo "<h2>Test isolato di invio email con Office 365 SMTP (Porta 587 + TLS)</h2>\n";

try {
    $host='smtp.office365.com';
    // 1. Configurazione del Trasporto
    $transport = (new Swift_SmtpTransport($host, 587, 'TLS'))
        ->setUsername('marco.cardinale@ilvbc.it')
        ->setPassword('rkrwyckbpdsbbtdr')
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
    $message = (new Swift_Message('Test Connessione Office 365 SMTP'))
        // Mittente: valida per il dominio
        ->setFrom(['marco.cardinale@ilvbc.it' => 'Test DashDemo'])
        // Destinatario: la tua email
        ->setTo(['marco.cardinale@ilvbc.it'])
        // Copia nascosta fissa del supporto
        ->setBcc(['supporto@programma2000.com'])
        ->setBody('Ciao Marco! Se leggi questa email, il relay SMTP su Porta 587 con STARTTLS funziona perfettamente.');

    // 4. Invio
    echo "<p>Tentativo di connessione a Microsoft in corso (Porta 587 con STARTTLS)...</p>\n";
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