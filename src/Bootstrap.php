<?php

/*
 * Copyright (c) 2017, Andreas Prucha, Abexto - Helicon Software Development
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * * Neither the name of the copyright holder nor the names of its contributors 
 *   may be used to endorse or promote products derived from this software 
 *   without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace amylian\yii\phpunit;

/**
 * Description of Bootstrap
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 * 
 */
class Bootstrap
{

    public static $vendorPath = null;
    public static $runtimePath = null;
    public static $defaultAliases = [];
    public static $basePath = null;

    /**
     * Automatically sets the vendorPath
     */
    protected static function autoDetectVendorPath()
    {
        if (defined('ABEXTO_AMYLIAN_VENDOR_PATH')) {
            static::$vendorPath = ABEXTO_AMYLIAN_VENDOR_PATH;
        }
    }

    /**
     * Initializes Yii for PhpUnit-Tests
     * 
     * @param string $mainScript    Path to the main script. $_SERVER['SCRIPT_NAME'] and $_SERVER['SCRIPT_FILENAME']
     *                              will be set to this value if they are not already defined. 
     * @param string $basePath      Tests Root directory
     * @param string $vendorPath    Tests Root directory
     * @param array $options        Array of options for the runtime environment:
     *                              <ul>
     *                                  <li>'yiiMainPhp': (Default: '@vendor/yiisoft/yii2/Yii.php') Path to php file defining the Yii Static class.
     *                                  <li>'errorReporting': (Default: -1) Error Reporting flag. See {@link error_reporting()}</li>
     *                                  <li>'yiiEnbleErrorHandler': (Default: false) Value for YII_ENABLE_ERROR_HANDLER constant</li>
     *                                  <li>'yiiDebug': (Default: true) Value for YII_DEBUG</li>
     *                                  <li>'yiiEnv': (Default: 'test') Value for YII_ENV</li>
     *                              </ul>
     *      <b>Note: The options used as default are commonly used for testing, so it's usually not necessary to specify an option.
     *          If a constant is already defined, the option will be ignored</b>
     * @param array $aliases Array of Yii Aliases (See: {@link \Yii::setAlias}
     */
    public static function initYii($mainScript, $basePath, $vendorPath, $options = [], $aliases = [])
    {
        $options                    = \yii\helpers\ArrayHelper::merge([
                    'yiiMainPhp'           => '@vendor/yiisoft/yii2/Yii.php',
                    'errorReporting'       => -1,
                    'yiiEnbleErrorHandler' => false,
                    'yiiDebug'             => true,
                    'yiiEnv'               => 'test'], $options);
        static::$vendorPath         = $vendorPath;
        static::$basePath           = $basePath;
        error_reporting($options['errorReporting']);
        defined('YII_ENABLE_ERROR_HANDLER') || define('YII_ENABLE_ERROR_HANDLER', $options['yiiEnbleErrorHandler']);
        defined('YII_DEBUG') || define('YII_DEBUG', $options['yiiDebug']);
        defined('YII_ENV') || define('YII_ENV', $options['yiiEnv']);
        $_SERVER['SCRIPT_NAME']     = $mainScript;
        $_SERVER['SCRIPT_FILENAME'] = $mainScript;

        if ($options['yiiMainPhp']) {
            require_once str_replace('@vendor', static::$vendorPath, $options['yiiMainPhp']);
        }

        $aliases = array_merge([
            '@app'    => static::$basePath,
            '@vendor' => static::$vendorPath,
            '@tests'  => static::$basePath,
            '@data'   => static::$basePath . '/data',
                ], $aliases);

        self::$defaultAliases = $aliases;

        foreach (self::$defaultAliases as $alias => $value) {
            \Yii::setAlias($alias, $value);
        }
    }

    public static function initEnv($testsRootDir, $vendorPath, $options = [], $aliases = [])
    {
        static::initYii($testsRootDir, $vendorPath, $options, $aliases);
        if (!class_exists('\PHPUnit_Framework_TestCase')) {
            require_once __DIR__.'/_oldNameWrappers.php';
        }
    }

}
