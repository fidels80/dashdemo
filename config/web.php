<?php
 
use yii\base\ActionEvent;
use yii\base\Controller;
use yii\base\Event;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

// Definisci manualmente se sei in ambiente di sviluppo in base all’IP
$allowedDevIps = ['127.0.0.1', '::1', '95.255.177.151', '31.191.59.170'];

if (in_array($clientIp, $allowedDevIps)) {
    defined('YII_ENV_DEV') or define('YII_ENV_DEV', true);
} else {
    defined('YII_ENV_DEV') or define('YII_ENV_DEV', false);
}

use yii\db\ActiveRecord;
use app\components\DbLogger;

// Configuriamo l'evento globale prima che l'app parta
\yii\base\Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_INSERT, [DbLogger::class, 'monitor']);
\yii\base\Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_UPDATE, [DbLogger::class, 'monitor']);
// LOG PER CANCELLAZIONE (Aggiungi questa riga)
\yii\base\Event::on(ActiveRecord::class, ActiveRecord::EVENT_BEFORE_DELETE, [DbLogger::class, 'monitorDelete']);

$config = [

    'id' => 'Presenze',
    'name'=>'Presenze',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log',
        function () {
            Event::on(Controller::class, Controller::EVENT_AFTER_ACTION, 
            function (ActionEvent $event) {
                Yii::info('Called controller/action: ' . $event->action->id . '/' 
                . $event->action->controller->id);
            //    yii::error(($event->action->model));
            });
        }

],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
      //  '@wapi'=> 'app/modules/autoupdate/rest',
      //  '@modulo'=>'/modules/'
    ],
    'language' => 'it-IT',
    'components' => [

    /*'pdf' => [
        'class' => 'mikehaertl\wkhtmlto\Pdf',
        // Imposta il percorso dell'eseguibile di wkhtmltopdf. Assicurati di avere wkhtmltopdf installato sul tuo sistema e specifica il percorso corretto qui.
        'binary' => 'C:\Program Files\wkhtmltopdf\bin\wkhtmltopdf.exe',
        // Opzioni globali per wkhtmltopdf
        'options' => [
            'viewport-size' => '1280x1024',
        ],
    ],*/
        'formatter' => [
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => 'EUR',
        ],
         'session' => [
     //   'class' => 'yii\web\Session',
           'class' => 'yii\web\DbSession',
        'sessionTable' => 'session',
        'timeout' => 3600,
           'gcProbability' => 0, // Disabilita la pulizia automatica

        'writeCallback' => function ($session) {
        return [
           'user_id' => Yii::$app->user->id,
           'last_write' => date('Y-d-m H:i:s')
,
       ];
    },
        //'cookieParams' => ['lifetime' => 1 *60 * 60]
        //['lifetime' => 7 * 24 *60 * 60]
    ],

        'fontawesome' => [
            'class' => thoulah\fontawesome\IconComponent::class,],
        'i18n' => [
            'translations' => [
                'kvgrid' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@vendor/kartik-v/yii2-grid/messages',
                ],
          ]
            ],

      //  'view' => [
           // 'theme' => [
             //   'pathMap' => [
             //      '@app/views' => '@vendor/hail812/yii2-adminlte3/src/views'
             //   ],
         //   ],
       //],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'RYen6m3dEahZ9IP2ofggH98ijfp2j8xp',
            'parsers' => [
        'application/json' => 'yii\web\JsonParser',
    ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
          //   'authTimeout' =>30000*60,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
            // 'site/index',
           // 'errorHandler'	=>	'site/error',
           //'errorHandler'	=>	'exception/error-handler'
        ],
'mailer' => [
            'class' => \yii\swiftmailer\Mailer::class,
            
            // FONDAMENTALE: Se impostato a true, Yii2 salva l'email come file 
            // di testo sul server invece di inviarla. Assicurati che sia false!
            'useFileTransport' => false, 
            
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                // L'host MX vincente che hai trovato
                'host' => 'ufficio2000-it01i.mail.protection.outlook.com',
                'port' => 25,
                // Attiviamo lo STARTTLS
                'encryption' => 'tls',
                
                // Mettiamo utente e password a null poiché Microsoft ci riconosce dall'IP
                'username' => null,
                'password' => null,
                
                // Le stesse identiche opzioni del test per non far bloccare i certificati locali
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
            // 'traceLevel' => YII_DEBUG ? 3 : 0,
           // 'traceLevel' => 3,
        /* 'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info' ,'error', 'warning',],
                    'categories' => ['yii\db\Command::query'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/db_trace.log',
                ],
            ],
 */       ],  
     'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => $db,
       'db2' => [
            'class' => 'yii\db\Connection',
      //      'dsn' => 'sqlsrv:Server=10.1.0.10\WKI;Database=ADB_VIVENDASRL',
          'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_VIVENDASRL',
      //    'username' => 'yii',
       'username' => 'sa',
          'password' => 'missorif.p.26',
            'charset' => 'utf8',
           'enableSchemaCache' => false,
            // Duration of schema cache.
         //   'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
         //   'schemaCache' => 'cache',
        
        ],
               'db3' => [
            'class' => 'yii\db\Connection',
      //      'dsn' => 'sqlsrv:Server=10.1.0.10\WKI;Database=ADB_VIVENDASRL',
          'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=rest_frontier',
           
          
      //    'username' => 'yii',
       'username' => 'sa',
          'password' => 'missorif.p.26',
            'charset' => 'utf8',
            'enableSchemaCache' => false,

            // Duration of schema cache.
         //   'schemaCacheDuration' => 3600,

            // Name of the cache component used to store schema information
         //   'schemaCache' => 'cache',
        
        ],
                       'db4' => [
            'class' => 'yii\db\Connection',
          'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_WINNER_ITALIA',
       'username' => 'sa',
          'password' => 'missorif.p.26',
            'charset' => 'utf8',
            'enableSchemaCache' => false,
            // Duration of schema cache.
         //   'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
         //   'schemaCache' => 'cache',
                       ]
                       ,   'db5' => [
            'class' => 'yii\db\Connection',
            //      'dsn' => 'sqlsrv:Server=10.1.0.10\WKI;Database=ADB_VIVENDASRL',
            // 'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=ADB_AUXCOOP',
            'dsn' => 'sqlsrv:Server=UFF2000;Database=ADB_UFFICIO2000',
          
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
        'db6' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=10.10.10.253;dbname=vt',
            'username' => 'root',
            'password' => 'missorif.p.26',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            // Name of the cache component used to store schema information
            'schemaCache' => 'cache',
        ],


        /* 'wh'=>   [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wh',
            'username' => 'root',
            'password' => 'missorif.p.26',
            'charset' => 'utf8',],*/
    'urlManager' => [
    'enablePrettyUrl' => false,
    'enableStrictParsing' => true,
    'showScriptName' => false,
    'rules' => [
      //  ['class' => 'yii\rest\UrlRule', 'controller' => 'tblshop'],
        ['class' => 'yii\rest\UrlRule', 'controller' => 'rest'],
     //   ['class' => 'yii\rest\UrlRule', 'controller' => 'app\modules\autoupdate\controllers\rest'],
    //    ['class' => 'yii\rest\UrlRule', 'controller' => 'app\modules\presenze\controllers\rest']
    ], 
]
    ],
    'modules' => [
  /*      'autoupdate' => [
            'class' => 'app\modules\autoupdate\Module',
        ],
        'Warehouse' => [
            'class' => 'app\modules\warehouse\Module',
        ],
        'Presenze' => [
            'class' => 'app\modules\presenze\Module',
        ],*/
        'Chart' => [
            'class' => 'app\modules\chart\Module',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module'],
          //  'Dintable' => [
           //     'class' => 'app\modules\dintable\Module',
           // ],

              'treemanager' =>  [
        'class' => '\kartik\tree\Module',
        // other module settings, refer detailed documentation
              ],
                 'dynagrid'=> [
        'class'=>'\kartik\dynagrid\Module',
        // other module settings
    ],
    ],
    'params' => $params,
    'on beforeAction' => function ($event) {
        $action = $event->action;
        $controller = $action->controller;

        // Elenco azioni pubbliche (accessibili ai guest)
        $publicRoutes = [
            'site/index',
            'site/login',
            'site/signup', // <--- AGGIUNTA QUESTA RIGA PER LA REGISTRAZIONE!
            'site/error',  // <--- AGGIUNTA QUESTA PER EVITARE LOOP DEGLI ERRORI
            'user/rp',
            'two-factor/verify', // <--- VERIFICA 2FA (utente non ancora loggato)
        ];

        $route = $controller->id . '/' . $action->id;

        if (Yii::$app->user->isGuest && !in_array($route, $publicRoutes)) {
            Yii::$app->response->redirect(['site/index'])->send();
            $event->isValid = false;
        }
    },

];





if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
       'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
       'allowedIPs' => ['127.0.0.1', '95.255.177.151', '151.31.163.227', '151.25.246.202', '31.191.59.170'],
 
    ]; 

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
       
       
       
        'allowedIPs' =>  ['10.1.0.14','127.0.0.1','10.1.106.99','192.168.10.43','192.168.10.27','95.255.177.151', '151.31.163.227','77.39.213.108','31.191.31.1', '31.191.59.170', '151.25.246.202'],
    ];
}


return $config;
