<?php

return [  
    'class' => 'yii\db\Connection',
  //    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
  //  'dsn' => 'sqlsrv:Server=10.1.0.10\WKI;Database=web_frontier;app=Arca Evolution                                                                                                                  ',
  //'dsn' => 'sqlsrv:Server=WIN-NC9I5BRDLM6;Database=web_frontier;app=Arca Evolution ',
  // 'dsn' => 'sqlsrv:Server=P2K-SVIL\web;Database=web_frontier;app=Arca Evolution',
  'dsn' => 'sqlsrv:Server=UFF2000;Database=web_frontier;app=Arca Evolution                                                                                                                  ',

    'username' => 'yii',
//  'username' => 'sa', 
   'password' => 'missorif.p.26',
    'charset' => 'utf8',
    'attributes' => [
       // 'PDO::SQLSRV_ATTR_ENCODING' => \PDO::SQLSRV_ENCODING_SYSTEM,
        'Application Name'=>'Microsoft SQL Server Management Studio',
      //  'AppName'=>'stocazzo'//,
    //    AppName=>'cicci'

    ],'enableSchemaCache' => false,

    // Duration of schema cache.
    'schemaCacheDuration' => 3600,

    // Name of the cache component used to store schema information
    //'schemaCache' => 'cache',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
