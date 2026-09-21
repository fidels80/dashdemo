<?php

use yii\base\ActionEvent;
use yii\base\Controller;
use yii\base\Event;
use yii\db\ActiveRecord;
use app\components\DbLogger;

$params = require __DIR__ . '/params.php';

// Tutte le connessioni al database sono definite in config/db.php
$dbs = require __DIR__ . '/db.php';

// ----------------------------------------------------------------------
// Rilevamento ambiente di sviluppo in base all'IP del client
// ----------------------------------------------------------------------
$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
$allowedDevIps = ['127.0.0.1', '::1', '95.255.177.151', '31.191.59.170'];

if (in_array($clientIp, $allowedDevIps)) {
    defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
} else {
    defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);
}

// ----------------------------------------------------------------------
// Logging automatico delle operazioni sui modelli (insert/update/delete)
// ----------------------------------------------------------------------
Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_INSERT, [DbLogger::class, 'monitor']);
Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_UPDATE, [DbLogger::class, 'monitor']);
Event::on(ActiveRecord::class, ActiveRecord::EVENT_BEFORE_DELETE, [DbLogger::class, 'monitorDelete']);

$config = [

    'id' => 'Presenze',
    'name' => 'Presenze',
    'basePath' => dirname(__DIR__),
    'language' => 'it-IT',

    'bootstrap' => [
        'log',
        function () {
            Event::on(Controller::class, Controller::EVENT_AFTER_ACTION, function (ActionEvent $event) {
                Yii::info('Called controller/action: ' . $event->action->id . '/'
                    . $event->action->controller->id);
            });
        },
    ],

    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    'components' => array_merge([

        'formatter' => [
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'EUR',
        ],

        'session' => [
            'class' => 'yii\web\DbSession',
            'sessionTable' => 'session',
            'timeout' => 3600,
            'gcProbability' => 0, // Disabilita la pulizia automatica
            'writeCallback' => function ($session) {
                return [
                    'user_id' => Yii::$app->user->id,
                    'last_write' => date('Y-d-m H:i:s'),
                ];
            },
        ],

        'fontawesome' => [
            'class' => thoulah\fontawesome\IconComponent::class,
        ],

        'i18n' => [
            'translations' => [
                'kvgrid' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/kartik-v/yii2-grid/messages',
                ],
            ],
        ],

        'request' => [
            'cookieValidationKey' => 'RYen6m3dEahZ9IP2ofggH98ijfp2j8xp',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],

        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],

        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
        ],

        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            // Se impostato a true, Yii2 salva l'email come file invece di inviarla.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'ufficio2000-it01i.mail.protection.outlook.com',
                'port' => 25,
                'encryption' => 'tls',
                // Microsoft riconosce il server dall'IP: nessuna credenziale.
                'username' => null,
                'password' => null,
                'streamOptions' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ],
        ],

        'log' => [
            // Configurare qui i target di log se necessario.
        ],

        'urlManager' => [
            'enablePrettyUrl' => false,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
            ],
        ],

    ], $dbs),

    'modules' => [
        'Chart' => [
            'class' => 'app\modules\chart\Module',
        ],
        'gridview' => [
            'class' => '\kartik\grid\Module',
        ],
        'treemanager' => [
            'class' => '\kartik\tree\Module',
        ],
        'dynagrid' => [
            'class' => '\kartik\dynagrid\Module',
        ],
    ],

    'params' => $params,

    // ------------------------------------------------------------------
    // Controllo accessi globale: le rotte pubbliche non richiedono login.
    // Le rotte api/* sono autenticate tramite Bearer token (ApiController).
    // ------------------------------------------------------------------
    'on beforeAction' => function ($event) {
        $action = $event->action;
        $controller = $action->controller;
        $route = $controller->id . '/' . $action->id;

        // Le API usano il Bearer token, non la sessione
        if (strpos($route, 'api/') === 0) {
            return;
        }

        $publicRoutes = [
            'site/index',
            'site/login',
            'site/signup',
            'site/error',
            'user/rp',
            'two-factor/verify',
        ];

        if (Yii::$app->user->isGuest && !in_array($route, $publicRoutes)) {
            Yii::$app->response->redirect(['site/index'])->send();
            $event->isValid = false;
            return;
        }

        // Controllo permessi ACL (vista/crea/modifica/elimina) per gli utenti autenticati.
        // Il livello 100 ha sempre accesso; senza configurazione l'accesso e' negato.
        if (!Yii::$app->user->isGuest
            && !\app\components\AccessControl::allowed($controller->id, $action->id, Yii::$app->user->identity)) {
            Yii::$app->session->setFlash('error', 'Non sei autorizzato ad accedere a questa funzione.');
            Yii::$app->response->redirect(['site/index'])->send();
            $event->isValid = false;
        }
    },

];

// ----------------------------------------------------------------------
// Configurazioni aggiuntive in ambiente di sviluppo
// ----------------------------------------------------------------------
if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '95.255.177.151', '151.31.163.227', '151.25.246.202', '31.191.59.170'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['10.1.0.14', '127.0.0.1', '10.1.106.99', '192.168.10.43', '192.168.10.27', '95.255.177.151', '151.31.163.227', '77.39.213.108', '31.191.31.1', '31.191.59.170', '151.25.246.202'],
    ];
}

return $config;
