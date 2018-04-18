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

namespace abexto\amylian\yii\phpunit;

/**
 * Description of Bootstrap
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 */
class Bootstrap
{

    public static $vendorPath = null;
    
    public static $testsPath = null;

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
     * Note: Before calling this function, composer-autoload.php should be loaded by calling [[Bootstrap::requireFramework()]]
     * 
     * @param type $bootstrapFile Filename of the PhpUnit bootstrap.php file
     * @param type $options
     * @param type $aliases
     */
    public static function initYii($bootstrapFile, $options = [], $aliases = [])
    {
        $bootstrapFileDir           = dirname($bootstrapFile);
        $options                    = \yii\helpers\ArrayHelper::merge([
                    'testsPath'            => dirname($bootstrapFile),
                    'vendorPath'           => null,
                    'errorReporting'       => -1,
                    'yiiEnbleErrorHandler' => false,
                    'yiiDebug'             => true,
                    'yiiEnv'               => 'test'], $options);
        if (isset($options['vendorPath'])) {
            static::$vendorPath = $options['vendorPath'];
        } elseif (!isset(static::$vendorPath)) {
            static::autoDetectVendorPath();
        }
        static::$testsPath = $options['testsPath'];
        error_reporting($options['errorReporting']);
        define('YII_ENABLE_ERROR_HANDLER', false);
        define('YII_DEBUG', true);
        define('YII_ENV', 'test');
        $_SERVER['SCRIPT_NAME']     = $bootstrapFile;
        $_SERVER['SCRIPT_FILENAME'] = $bootstrapFile;

        if ($options['yiiMainPhp']) {
            require_once str_replace('@vendor', static::$vendorPath, $options['yiiMainPhp']);
        }
        
        $aliases = array_merge([
            '@vendor' => static::$vendorPath,
            '@tests' => static::$testsPath,
            '@data' => static::$testsPath.'/data',
            '@runtime' => static::$testsPath.'/runtime'
        ], $aliases);
        foreach ($aliases as $alias => $path) {
            \Yii::setAlias($alias, $path);
        }
    }
    
    public static function initEnv($bootstrapFile, $options = [], $aliases = [])
    {
        $options = array_merge([
            'vendorPath' => null,
            'yiiMainPhp' => '@vendor/yiisoft/yii2/Yii.php'], $options
        );
        static::initYii($bootstrapFile, $options, $aliases);
    }

}
