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
 * Description of AbstractYiiApplicationTestCase
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 */
abstract class AbstractYiiTestCase extends \PHPUnit\Framework\TestCase
{
    
    /**
     * @var bool If true, the mock application will be destroyed automatically after each test
     */
    protected $autoDestroyYiiApplicationAfterTest = true;
    
    /**
     * @inheritDoc
     * 
     * Note: if [[$$autoDestroyYiiApplicationAfterTest]] is set to true, 
     * [[destroyYiiApplication()]] is called.
     */
    protected function tearDown()
    {
        parent::tearDown();
        if ($this->autoDestroyYiiApplicationAfterTest) {
            static::destroyYiiApplication();
        }
    }
    
    /**
     * @inheritDoc
     * 
     * Note: if a Yii mockup application exists, 
     * [[destroyYiiApplication()]] is called.
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        if (isset(\Yii::$app)) {
            $logger = \Yii::getLogger();
            $logger->flush();
        }
        static::destroyYiiApplication();
    }    
    
    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected static function destroyYiiApplication()
    {
        if (\Yii::$app && \Yii::$app->has('session', true)) {
            \Yii::$app->session->close();
        }
        \Yii::$app = null;
    }    
    
    /**
     * Creates a new Yii mockup application
     * 
     * @param type $config
     * @param class $appClass
     */
    
    protected static function mockYiiApplication($config = [], $appClass)
    {
        new $appClass(\yii\helpers\ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => Bootstrap::$vendorPath,
        ], $config));
    }
    
    
    /**
     * Creates a new Yii console mockup application 
     * 
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected static function mockYiiConsoleApplication($config = [], $appClass = '\yii\console\Application')
    {
        static::mockYiiApplication($config, $appClass);
    }

    /**
     * Creates a new Yii web mockup application 
     * 
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected static function mockYiiWebApplication($config = [], $appClass = '\yii\web\Application')
    {
        static::mockYiiApplication(\yii\helpers\ArrayHelper::merge([
            'aliases' => [
                '@bower' => '@vendor/bower-asset',
                '@npm' => '@vendor/npm-asset',
            ],
            'components' => [
                'request' => [
                    'cookieValidationKey' => 'wefJDF8sfdsfSDefwqdxj9oq',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
            ],
        ], $config), $appClass);
    }
    
    
}
