<?php
$user = 'dashboard@planorys.com';
$pass = 'Soltantoplanorys1505!';
$host = 'smtp.aruba.it';

echo "Test invio con fsockopen...\n";
$conn = fsockopen("ssl://$host", 465, $errno, $errstr, 20);
if (!$conn) {
    die("Connessione fallita: $errstr ($errno)\n");
}
echo "Connesso! Risposta: " . fgets($conn) . "\n";

fwrite($conn, "EHLO " . gethostname() . "\r\n");
echo "EHLO: " . fgets($conn) . "\n";

fwrite($conn, "AUTH LOGIN\r\n");
echo "AUTH LOGIN: " . fgets($conn) . "\n";

fwrite($conn, base64_encode($user) . "\r\n");
echo "USER: " . fgets($conn) . "\n";

fwrite($conn, base64_encode($pass) . "\r\n");
echo "PASS: " . fgets($conn) . "\n"; // Se qui ricevi 535, la password è errata per il server

fwrite($conn, "QUIT\r\n");
fclose($conn);
