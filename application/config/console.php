<?php

$params = require __DIR__ . '/_params.php';
$db = require __DIR__ . '/_db.php';
$cache = require __DIR__ . '/_cache.php';
$solr = require __DIR__ . '/_solr.php';

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'runtimePath' => ROOT_DIR . '/data/runtime',
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'urlManager' => [
            'hostInfo' => DOMAIN,
            'baseUrl' => BASE_URL
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => require __DIR__ . '/_mailer.php',
        'db' => $db,
        'cache' => $cache,
        'redis' => require __DIR__ . '/_redis.php',
        'solr' => $solr,
        'queue' => require __DIR__ . '/_queue.php',
    ],
    'params' => $params,
    /*
    'controllerMap' => [
        'fixture' => [ // Fixture generation command line.
            'class' => 'yii\faker\FixtureController',
        ],
    ],
    */
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
