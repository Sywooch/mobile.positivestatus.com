<?php

$config = [
    'id' => 'basic',
    'name' => 'MobileMakler',
    'basePath' => dirname(__DIR__),
    'language' => 'ru',
    'bootstrap' => ['log'],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        '/js/jquery111.min.js',
                        //'//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
//                    'sourcePath' => null,   // do not publish the bundle
//                    'js' => ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js'],
//                    'css' => ['https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css'],
                ],
            ],
        ],
        'view' => [         // https://gist.github.com/MGHollander/d438691179466f983a2a
            'class'=>'app\components\ClientScriptView',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Uv6eeuQ3clhHRhVw347j',
            'enableCsrfValidation' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/login'],
            'on afterLogin' => function(\yii\web\UserEvent $event) {
                $user = $event->identity;
                $user->updateAttributes(['lastvisit_at' => time()]);
            }
        ],
        'security' => [
            'class' => 'yii\base\Security',
            'passwordHashStrategy' => 'crypt',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => YII_DEBUG,
        ],
        'urlManager' => [
            'class' => 'app\components\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '<lang:(ru|de)>/start' => 'site/start',
                '<lang:(ru|de)>/<trans_cat:\w+>' => 'site/proposal',
                '<lang:(ru|de)>/<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/captcha/<refresh:\d+>' => '<controller>/captcha',
                '<controller:\w+>/captcha/<v:\w+>' => '<controller>/captcha',
            ],
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
        'db' => require(__DIR__ . '/db.php'),
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'basePath' => '@app/messages',
                    //'sourceLanguage' => 'en-US',
                ],
            ],
        ],
        'formatter' => [
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',            
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
    'aliases' => require(__DIR__ . '/aliases.php'),
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
