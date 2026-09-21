<?php

$params = require __DIR__ . '/params.php';

// Stesse connessioni al database usate dal web (config/db.php)
$dbs = require __DIR__ . '/db.php';

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

    'components' => array_merge([

        'urlManager' => [
            'enablePrettyUrl' => false,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'hostInfo' => 'https://dashboard.planorys.it:4433/',
            'baseUrl' => '/web',
        ],

        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'fileTransportPath' => '@runtime/mail',
            'messageConfig' => [
                'from' => ['dashboard@planorys.com' => 'Dashboard Planorys'],
                'charset' => 'UTF-8',
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtps.aruba.it',
                'port' => 465,
                'username' => 'dashboard@planorys.com',
                'password' => 'Soltantoplanorys1505!',
                'Timeout' => 120,
                'encryption' => 'SSL',
                'streamOptions' => [
                    'ssl' => [
                        'verify_peer' => false,
                        'allow_self_signed' => true,
                        'verify_peer_name' => false,
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

    ], $dbs),

    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

if (!YII_ENV_TEST) {
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'yii2-adminlte3' => '@vendor/hail812/yii2-adminlte3/src/gii/generators/crud/default',
                ],
            ],
        ],
    ];
}

return $config;
