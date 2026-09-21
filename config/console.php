<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => false,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'hostInfo' => 'https://dashboard.planorys.it:4433/',
            'baseUrl' => '/web',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            //     'useFileTransport' => true,
            //   'fileTransportPath'=>'@runtime/mail/send',
            'useFileTransport' => false,
            'fileTransportPath' => '@runtime/mail', // Le mail verranno salvate qui come file
            'messageConfig' => [
                'from' => ['dashboard@planorys.com' => 'Dashboard Planorys'],
                'charset' => 'UTF-8',
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                //'host' => 'authsmtp.securemail.pro',
                'host'=> 'smtps.aruba.it',
                'port' => 465,
                'username' => 'dashboard@planorys.com',
                'password' => 'Soltantoplanorys1505!',
                'Timeout' => 120,
                'encryption' => 'SSL',
                'streamOptions' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'allow_self_signed' => true,
                        'verify_peer_name' => false, // Aggiungi questa riga
                    ],
                ],
            ],
 

        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'db5' => [
            'class' => 'yii\db\Connection',
            //      'dsn' => 'sqlsrv:Server=10.1.0.10\WKI;Database=ADB_VIVENDASRL',
            // 'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_AUXCOOP',
            'dsn' => 'sqlsrv:Server=10.10.10.6;Database=ADB_AUXCOOP',
          
          'username' => 'yii',
      // 'username' => 'sa',
          'password' => 'missorif.p.26',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
            // Duration of schema cache.
         //   'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
         //   'schemaCache' => 'cache',
                       ],
      //  'session' => [
      //      'class' => 'yii\web\DbSession',
     //   'sessionTable' => 'session',],
    ],

    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}
if (!YII_ENV_TEST) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [ // here
            'crud' => [ // generator name
                'class' => 'yii\gii\generators\crud\Generator', // generator class
                'templates' => [ // setting for our templates
                    'yii2-adminlte3' => '@vendor/hail812/yii2-adminlte3/src/gii/generators/crud/default' // template name => path to template
                ]
            ]
        ]
    ];
}
return $config;
