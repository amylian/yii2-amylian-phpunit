<?php

$autoLoader = __DIR__.'/../../../../autoload.php';
if (file_exists($autoLoader)) {
    require_once $autoLoader;
} else {
    $autoLoader = __DIR__.'/../../vendor/autoload.php';
    require_once $autoLoader;
}
abexto\amylian\yii\phpunit\Bootstrap::initEnv(__FILE__, __DIR__.'/..', dirname($autoLoader));

