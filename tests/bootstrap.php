<?php

//
// Find the vendor dir and load the autoloader
//
$autoLoader = __DIR__.'/../vendor/autoload.php'; // Try at the usual location of vendor
if (file_exists($autoLoader)) {
    require_once $autoLoader;
} else {
    $autoLoader = __DIR__.'/../../../autoload.php'; // As no vendor dir could be found, the package is propably in the vendor dir
    require_once $autoLoader;
}
amylian\yii\phpunit\Bootstrap::initEnv(__DIR__, __DIR__, dirname($autoLoader));

