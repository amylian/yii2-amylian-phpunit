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
 * Description of AbstractYiiApplicationTestCase
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 */
abstract class AbstractYiiTestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * Specifies that {@link destroyYiiApplication()} is called in {@link self::tearDown()}
     */
    const DESTROY_YII_IN_TEARDOWN = 0x01;

    /**
     * Specifies that {@link destroyYiiApplication()} is called in {@link self::tearDownAfterClass()}
     */
    const DESTROY_YII_IN_TEARDWON_AFTERCLASS = 0x02;

    /**
     * Default teardown flag - No automatic tear down at all
     */
    const DESTROY_YII_NEVER = 0x00;

    /**
     * Default teardown flag - includes {@link static::DESTROY_YII_IN_TEARDOWN} and {@link self::DESTROY_YII_IN_TEARDWON_AFTERCLASS}
     */
    const DESTROY_YII_IN_ANY_TEARDOWN = 0xFF;

    /**
     * @var int controls when {@link destroyYiiApplication()} is called automatically
     */
    protected static $_autoDestroyYiiInFlags = self::DESTROY_YII_IN_ANY_TEARDOWN;

    /**
     * @var int controls when {@link destroyYiiApplication()} is called automatically
     */
    protected static $_autoDestroyYiiFlags   = MockYii::DESTROY_DEFAULT;

    /**
     * @inheritDoc
     * 
     * Note: if [[$$autoDestroyYiiApplicationAfterTest]] is set to true, 
     * [[destroyYiiApplication()]] is called.
     */
    protected function tearDown()
    {
        parent::tearDown();
        if (static::$_autoDestroyYiiInFlags & static::DESTROY_YII_IN_TEARDOWN) {
            static::destroyYiiApplication(static::$_autoDestroyYiiFlags);
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
        if (static::$_autoDestroyYiiInFlags & static::DESTROY_YII_IN_TEARDWON_AFTERCLASS) {
            static::destroyYiiApplication(static::$_autoDestroyYiiFlags);
        }
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected static function destroyYiiApplication($destroyFlags = MockYii::DESTROY_DEFAULT)
    {
        /*
          if (\Yii::$app && \Yii::$app->has('session', true)) {
          \Yii::$app->session->close();
          }
          \Yii::$app = null;
         */
        return MockYii::destroyYiiApplication($destroyFlags);
    }

    /**
     * Creates a new Yii mockup application
     * 
     * This function calls {@link MockYii::mockYiiApplication()}. See there for details
     * 
     * @param type $config
     * @param int|bool $destroyIn see {@link setAutoDestroyYiiFlags()}
     * @param int|bool $destroyFlags see {@link setAutoDestroyYiiFlags()}
     * @param class $appClass
     */
    protected static function mockYiiApplication($config = [], $destroyIn, $destroyFlags, $appClass)
    {
        /*
          new $appClass(\yii\helpers\ArrayHelper::merge([
          'id' => 'testapp',
          'aliases' => Bootstrap::$defaultAliases,
          'basePath' => Bootstrap::$basePath,
          'vendorPath' => Bootstrap::$vendorPath,
          ], $config));
         */
        static::setAutoDestroyYiiFlags($destroyIn, $destroyFlags);
        return MockYii::mockYiiApplication($config, $appClass);
    }

    /**
     * Sets when the Yii Mock Application is destroyed
     * 
     * @param bool|int $destroyIn   DESTROY_YII_IN_Xxxx flags or 
     *                              true for {@link static::DESTROY_YII_IN_ANY_TEARDOWN} or
     *                              false for {@link static::DESTROY_YII_NEVER}
     * @param bool|int $destroyFlags    Any {@link MockYii}::DESTROY_Xxxx flag combintion or 
     *                                  true for {@link MockYii::DESTROY_DEFAULT} or
     *                                  false for {@link MockYii::DESTROY_NOTHING}
     */
    protected static function setAutoDestroyYiiFlags($destroyIn = true, $destroyFlags = true)
    {
        if ($destroyIn === true)
            $destroyIn = static::DESTROY_YII_IN_ANY_TEARDOWN;
        elseif ($destroyIn === false)
            $destroyIn = static::DESTROY_YII_NEVER;
        if ($destroyFlags === true)
            $destroyFlags === MockYii::DESTROY_DEFAULT;
        elseif ($destroyFlags === false)
            $destroyFlags === MockYii::DESTROY_NOTHING;
        static::$_autoDestroyYiiFlags = $destroyIn;
        static::$_autoDestroyYiiInFlags = $destroyFlags;
    }

    /**
     * Creates a new Yii console mockup application 
     * 
     * This function calls {@link MockYii::mockYiiConsoleApplication()}. See there for details
     * 
     * @param array $config The application configuration, if needed
     * @param int|bool $destroyIn see {@link setAutoDestroyYiiFlags()}
     * @param int|bool $destroyFlags see {@link setAutoDestroyYiiFlags()}
     * @param string|null $appClass name of the application class to create
     *                    If this parameter is not specified or null is passed,
     *                    {@link \yii\console\Application} is used.
     */
    protected static function mockYiiConsoleApplication($config = [], $destroyIn = true, $destroyFlags = true, $appClass = '\yii\console\Application')
    {
        static::setAutoDestroyYiiFlags($destroyIn, $destroyFlags);
        return MockYii::mockYiiConsoleApplication($config, $appClass);
    }

    /**
     * Creates a new Yii web mockup application 
     * 
     * This function calls {@link MockYii::mockYiiWebApplication()}. See there for details
     * 
     * 
     * @param array $config The application configuration, if needed
     * @param int|bool $destroyIn see {@link setAutoDestroyYiiFlags()}
     * @param int|bool $destroyFlags see {@link setAutoDestroyYiiFlags()}
     * @param string $appClass name of the application class to create
     */
    protected static function mockYiiWebApplication($config = [], $destroyIn = true, $destroyFlags = true,  $appClass = '\yii\web\Application')
    {
        /*
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
         */
        static::setAutoDestroyYiiFlags($destroyIn, $destroyFlags);
        return MockYii::mockYiiWebApplication($config, $appClass);
    }

}
