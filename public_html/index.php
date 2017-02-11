<?php
//error_reporting(E_ALL);
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');

defined('LOCALHOST') or define('LOCALHOST',
    in_array($_SERVER['SERVER_ADDR'], array('::1', '127.0.0.1')) &&
    in_array($_SERVER['REMOTE_ADDR'], array('::1', '127.0.0.1'))
);

defined('YII_DEBUG') or define('YII_DEBUG', LOCALHOST);
if(LOCALHOST)
    defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
