<?php

// comment out the following two lines when deployed to production

$clientIp = $_SERVER['REMOTE_ADDR'] ?? '';
//$allowedDevIps = ['127.0.0.1', '::1', '95.255.177.151'];
$allowedDevIps = ['127.0.0.1', '::1', '95.255.177.151', '31.191.59.170'];
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

//'YII_ENV_PROD'true,
//  defined('YII_ENV') or define('YII_ENV', 'PROD');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();
