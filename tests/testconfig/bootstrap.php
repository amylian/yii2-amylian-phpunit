<?php

$autoLoader = __DIR__.'/../../../../autoload.php';
if (file_exists($autoLoader)) {
    require_once $autoLoader;
} else {
    $autoLoader = __DIR__.'/../../vendor/autoload.php';
    require_once $autoLoader;
}
define ('ABEXTO_AMYLIAN_VENDOR_PATH' , dirname($autoLoader));
abexto\amylian\yii\phpunit\Bootstrap::initEnv(__FILE__, __DIR__.'/..');

