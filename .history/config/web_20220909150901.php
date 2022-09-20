<?php
 
use yii\base\ActionEvent;
use yii\base\Controller;
use yii\base\Event;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'Vivenda',
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
        'formatter' => [
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'currencyCode' => '',
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
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
           // 'errorHandler'	=>	'site/error',
           //'errorHandler'	=>	'exception/error-handler'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
       //     'useFileTransport' => true,
         //   'fileTransportPath'=>'@runtime/mail/send',
'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.office365.com',
               // 'smtp.office365.com',
                'port' => 587,
              
                'username' =>//'caterina.iannucci@vivenda.it',
                 'pubblicazioni@vivenda.it',
                
               // 'redazione@vivenda.it',
               //'marco.cardinale@ilvbc.it',
               //  'sviluppo@ilvbc.it',
                'password' => 
              //'Serval_2019@',
                 'Condor2021!!',
                //'Vivenda2022!',
               // 'POLI-grafico22',
                //'Ilvbc2021!',
                'encryption' => 'TLS',
               // 'encryption' => '',
                
                'streamOptions' => [
            'ssl' => [
                'verify_peer' => false,
                'allow_self_signed' => true
            ],
        ],
            ],
/*
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.office365.com',
               // 'smtp.office365.com',
                'port' => 587,
              
                'username' => //'redazione@vivenda.it',
                'marco.cardinale@ilvbc.it',
                'password' => //'POLI-grafico22',
                'Ilvbc2021!',
                'encryption' => 'TLS',
               // 'encryption' => '',
                
                'streamOptions' => [
            'ssl' => [
                'verify_peer' => false,
                'allow_self_signed' => true
            ],
        ],
*/

        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
 
        'db' => $db,
       'db2' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlsrv:Server=VIVROMSRW010\WKI;Database=ADB_VIVENDASRL',
            'username' => 'yii',
            'password' => 'missorif.p.26',
            'charset' => 'utf8',
            'enableSchemaCache' => true,

            // Duration of schema cache.
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
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
       'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
     'allowedIPs' =>  ['10.1.0.14','127.0.0.1','10.1.106.99'],
              //    'panels' => [
           //     'httpclient' => [
           //         'class' => 'yii\\httpclient\\debug\\HttpClientPanel',
            //    ],
            //],
    ]; 

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
       
       
       
        'allowedIPs' =>  ['10.1.0.14','127.0.0.1','10.1.106.99'],
    ];
}


return $config;
