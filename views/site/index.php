<?php
$this->title = '';
//$this->params['breadcrumbs'] = [['label' => $this->title]];
use app\models\doc_head;
use app\models\payments;
use app\models\User;
use yii\helpers\Url;

try {
    $cf = Yii::$app->user->identity->cd_cli;
} catch (Exception $e) {
    $usrid = '';
    //Yii::$app->user->identity->id;
    $ris = (new \yii\db\Query())
        ->select(['cd_cli', 'email', 'username', 'piva'])
        ->from('user')
        ->where(['id' => $usrid])
        ->one();

    $cf = '';
    // $ris['cd_cli'];
}
//yii::error($cf);
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>6 Cards with Bootstrap</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<style>
    .card {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card-title {
        font-weight: bold;
    }

    .card-text {
        color: #555;
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    /* Stile del contenitore */
    .planorys-container {
        font-family: 'Montserrat', sans-serif;
        font-size: 14px;
        line-height: 1.6;
        color: #333333;
        max-width: 400px;
        /* Larghezza massima opzionale */
        padding: 20px;
    }

    /* Stile per il nome principale */
    .planorys-brand {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 5px;
        color: #000;
        text-transform: uppercase;
    }

    /* Stile per il sottotitolo aziendale */
    .planorys-company {
        font-weight: 600;
        margin-bottom: 15px;
        display: block;
    }

    /* Blocchi di testo separati */
    .planorys-block {
        margin-bottom: 15px;
    }

    /* Stile dei link (telefono, mail, web) */
    .planorys-container a {
        color: #333333;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .planorys-container a:hover {
        color: #0056b3;
        /* Colore al passaggio del mouse */
        text-decoration: underline;
    }

    /* Grassetto per le etichette */
    strong {
        font-weight: 600;
    }
</style>

<body class="d-flex justify-content-center">
    <table class="table mx-auto text-center">
        <div dir="ltr" style="font-family: Aptos, Arial, Helvetica, sans-serif; font-size: 12pt; color: rgb(0, 0, 0);">
            <br>

        </div>
        <p dir="ltr" style="text-align: left; text-indent: 0px; text-transform: none; margin: 0cm;">
<span style="font-family: 'Montserrat ExtraBold', sans-serif; font-size: 11pt; color: white; background-color: black; display: inline-block; padding: 10px; border-radius: 4px;">
    <b>
        <img decoding="async" src="https://wkiroma.it/wp-content/uploads/2024/03/logo-wki-bianco-01.svg" alt="Logo WKI" class="wp-image-895" style="width:300px; display: block;">
    </b>
</span>
        </p>
        <div class="planorys-container">
            <div class="planorys-brand">
                <h3><b>Presenze</b></h3></div>
                <span class="planorys-company">è un Brand Programma 2000<br><b>di Programma 2000 srl </b></span>

                <div class="planorys-block">
                    Via Gianluca Squarcialupo 58<br>
                    00162 Roma
                </div>

                <div class="planorys-block">
                    <strong>Tel:</strong> <a href="tel:0644292931">06.44292931</a><br>

                </div>
                <div class="planorys-block">
                    <a href="mailto:supporto@programma2000.com">supporto@programma2000.com</a><br>
                    
                </div>
            </div>
    </table>
</body>


</html>