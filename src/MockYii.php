<?php

/*
 * Copyright (c) 2018, Andreas Prucha, Abexto - Helicon Software Development
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
 * Description of mockYii
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 */
class MockYii
{

    /**
     * Flag for {@link static::destroyYiiApplication}: sets \Yii::$app to null (included in {@link static::DESTROY_DEFAULT})
     */
    const DESTROY_NULL_APP        = 0x00000001;

    /**
     * Flag for {@link static::destroyYiiApplication}: Calls {@link \yii\log\Logger::flush()} (included in {@link static::DESTROY_DEFAULT})
     */
    const DESTROY_FLUSH_LOG       = 0x00000002;

    /**
     * Flag for {@link static::destroyYiiApplication}: Calls {@link \yii\web\Session::close()} before  (included in {@link static::DESTROY_DEFAULT})
     */
    const DESTROY_CLOSE_SESSION   = 0x00000004;

    /**
     * Flag for {@link static::destroyYiiApplication}: Calls {@link \yii\di\Container::clear()} for every item (included in {@link static::DESTROY_DEFAULT})
     */
    const DESTROY_CLEAR_CONTAINER = 0x00000008;
    
    
    /**
     * Flag for {@link static::destroyYiiApplication}: Sets \Yii::$container to null (This is not part of DESTROY_DEFAULT and usually not necessary)
     */
    const DESTROY_NULL_CONTAINER = 0x00010000;
    
    /**
     * Flag for {@link static::destroyYiiApplication}: All usually necessary destructions are done
     */
    const DESTROY_ALL         = 0x0000ffff;
    
    /**
     * Flag for {@link static::destroyYiiApplication}: same as {@link static::DESTROY_ALL}
     */
    const DESTROY_DEFAULT           = self::DESTROY_ALL;

    /**
     * Flag for {@link static::destroyYiiApplication}: Sets all DESTROY_Xxxx flags
     */
    const DESTROY_REALLY_EVERYTHING             = 0xffffffff;

    /**
     * Flag for {@link static::destroyYiiApplication}: Does not destroy anything and turns the function call into an noop
     */
    const DESTROY_NOTHING         = 0x00000000;

    /**
     * Destroys application in Yii::$app by setting it to null.
     * 
     * @param int $destroyFlags 
     */
    public static function destroyYiiApplication($destroyFlags = self::DESTROY_DEFAULT)
    {
        if (!$destroyFlags)
            return false;

        //
        // Close Session
        //
        
        if ($destroyFlags & static::DESTROY_CLOSE_SESSION && \Yii::$app !== null) {
            if (\Yii::$app && \Yii::$app->has('session', true)) {
                \Yii::$app->session->close();
            }
        }

        //
        // Flush log
        //
        
        if ($destroyFlags & static::DESTROY_FLUSH_LOG && \Yii::$app !== null) {
            $logger = \Yii::getLogger();
            $logger->flush();
        }

        //
        //  null Yii App
        //
        
        if ($destroyFlags & static::DESTROY_NULL_APP && \Yii::$app !== null) {
            \Yii::$app = null;
        }

        //
        // Destroy all container defintions
        //
        
        if ($destroyFlags & static::DESTROY_CLEAR_CONTAINER && \Yii::$container !== null) {
            $defs = \Yii::$container->getDefinitions();
            foreach ($defs as $class => $definition) {
                \Yii::$container->clear($class);
            }
        }
        
        // Null container
        
        if ($destroyFlags & static::DESTROY_NULL_CONTAINER) {
           \Yii::$container = null;
        }
        
        
    }

    /**
     * Creates a new Yii mockup application
     * 
     * @param type $config
     * @param class $appClass
     */
    public static function mockYiiApplication($config = [],  $appClass)
    {
        new $appClass(\yii\helpers\ArrayHelper::merge([
                    'id'         => 'testapp',
                    'aliases'    => Bootstrap::$defaultAliases,
                    'basePath'   => Bootstrap::$basePath,
                    'runtimePath'=> Bootstrap::$basePath.'/runtime',
                    'vendorPath' => Bootstrap::$vendorPath,
                        ], $config));
    }

    /**
     * Creates a new Yii console mockup application 
     * 
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    public static function mockYiiConsoleApplication($config = [], $appClass = '\yii\console\Application')
    {
        static::mockYiiApplication($config, $appClass);
    }

    /**
     * Creates a new Yii web mockup application 
     * 
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    public static function mockYiiWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        static::mockYiiApplication(\yii\helpers\ArrayHelper::merge([
                    'aliases'    => [
                        '@bower' => '@vendor/bower-asset',
                        '@npm'   => '@vendor/npm-asset',
                    ],
                    'components' => [
                        'request' => [
                            'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                            'scriptFile'          => __DIR__ . '/index.php',
                            'scriptUrl'           => '/index.php',
                        ],
                    ],
                        ], $config), $appClass);
    }

}
