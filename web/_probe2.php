<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

$root = dirname(__DIR__);
require $root . '/vendor/autoload.php';
require $root . '/vendor/yiisoft/yii2/Yii.php';

$config = require $root . '/config/web.php';
$app = new yii\web\Application($config);
$app->setIdentity(app\models\User::findOne(38));

$route = isset($argv[1]) ? $argv[1] : 'rapportini/index';

Yii::$app->response->format = 'html';
try {
    $out = $app->runAction($route, []);
    $out = preg_replace('/<\!-- YII-BLOCK-.*?-->/', '', $out);
    echo "=== OK $route len=" . strlen($out) . " ===\n";
    file_put_contents(sys_get_temp_dir() . '/probe_' . str_replace('/', '_', $route) . '.html', $out);
} catch (\Throwable $e) {
    echo "=== EXCEPTION $route ===\n";
    echo get_class($e) . ': ' . $e->getMessage() . "\n";
    echo $e->getFile() . ':' . $e->getLine() . "\n";
}
