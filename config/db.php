<?php

return [  
    'class' => 'yii\db\Connection',
//    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'dsn' => 'sqlsrv:Server=DESKTOP-2CS5H4S;Database=web_frontier;app=Arca Evolution                                                                                                                  ',
    'username' => 'sa',
    'password' => 'missorif.p.26',
    'charset' => 'utf8',
    'attributes' => [
       // 'PDO::SQLSRV_ATTR_ENCODING' => \PDO::SQLSRV_ENCODING_SYSTEM,
        'Application Name'=>'Microsoft SQL Server Management Studio',
        'AppName'=>'stocazzo'//,
    //    AppName=>'cicci'

    ]

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
