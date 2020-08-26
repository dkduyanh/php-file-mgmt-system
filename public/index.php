<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../application/config/_constants.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require APPLICATION_DIR.'/config/web.php';

(new yii\web\Application($config))->run();

function dd($var){
    echo "<pre>"; var_dump($var); echo "</pre>"; die;
}
function o($var){
    echo "<pre>"; var_dump($var); echo "</pre>"; die;
}
