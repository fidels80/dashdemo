<?php

/**
 * Configurazione di TUTTE le connessioni al database dell'applicazione.
 *
 * Restituisce un array di connessioni indicizzate per nome componente
 * ('db', 'db2', ... 'db6'), pronto per essere inserito nella chiave
 * 'components' delle configurazioni web e console tramite array_merge().
 *
 * NOTA: 'db' è la connessione principale (Arca Evolution / web_frontier)
 * ed è quella usata dai modelli, dalle migrazioni e dal microgestionale.
 */

$charset = 'utf8';

$sqlServerAttributes = [
    'Application Name' => 'Microsoft SQL Server Management Studio',
];

return [

    // ------------------------------------------------------------------
    // db - Connessione principale (Arca Evolution / web_frontier)
    // ------------------------------------------------------------------
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=rog;Database=web_frontier;app=Arca Evolution',
        'username' => 'yii',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'attributes' => $sqlServerAttributes,
        'enableSchemaCache' => false,
        'schemaCacheDuration' => 3600,
    ],

    // ------------------------------------------------------------------
    // db2 - ADB_VIVENDASRL
    // ------------------------------------------------------------------
    'db2' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_VIVENDASRL',
        'username' => 'sa',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'enableSchemaCache' => false,
    ],

    // ------------------------------------------------------------------
    // db3 - rest_frontier
    // ------------------------------------------------------------------
    'db3' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=rest_frontier',
        'username' => 'sa',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'enableSchemaCache' => false,
    ],

    // ------------------------------------------------------------------
    // db4 - ADB_WINNER_ITALIA
    // ------------------------------------------------------------------
    'db4' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_WINNER_ITALIA',
        'username' => 'sa',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'enableSchemaCache' => false,
    ],

    // ------------------------------------------------------------------
    // db5 - ADB_UFFICIO2000 (Arca Evolution)
    // ------------------------------------------------------------------
    'db5' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlsrv:Server=rog;Database=ADB_UFFICIO2000',
        'username' => 'yii',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'enableSchemaCache' => false,
    ],

    // ------------------------------------------------------------------
    // db6 - Vtiger (MySQL)
    // ------------------------------------------------------------------
    'db6' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=10.10.10.253;dbname=vt',
        'username' => 'root',
        'password' => 'missorif.p.26',
        'charset' => $charset,
        'enableSchemaCache' => true,
        'schemaCacheDuration' => 3600,
        'schemaCache' => 'cache',
    ],
];
