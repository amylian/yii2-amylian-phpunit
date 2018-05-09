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

namespace amylian\yii\phpunit\tests\units;

/**
 * Description of AbstractYiiTestCaseTest
 *
 * @author Andreas Prucha, Abexto - Helicon Software Development
 */
class YiiTestCaseTest extends \amylian\yii\phpunit\AbstractYiiTestCase
{
    
    public function testAutoDestroyInTearDownStep1()
    {
        static::mockYiiConsoleApplication([], true);
        $this->assertInstanceOf(\yii\console\Application::class, \Yii::$app);
    }

    /**
     * @depends testAutoDestroyInTearDownStep1
     */
    public function testAutoDestroyInTearDownStep2()
    {
        $this->assertNull(\Yii::$app); // Must be null
    }
    
    public function testDoNotAutoDestroyInTearDownStep1()
    {
        static::mockYiiConsoleApplication([], false);
        $this->assertInstanceOf(\yii\console\Application::class, \Yii::$app);
        $this->assertEquals(static::$_autoDestroyYiiInFlags, static::DESTROY_YII_IN_ANY_TEARDOWN);
    }

    /**
     * @depends testDoNotAutoDestroyInTearDownStep1
     */
    public function testDoNotAutoDestroyInTearDownStep2()
    {
        $this->assertInstanceOf(\yii\console\Application::class, \Yii::$app);
    }
    
    
    public function testDestroyYiiApplicaiton()
    {
        static::destroyYiiApplication(\amylian\yii\phpunit\MockYii::DESTROY_ALL); // Make sure we do not have anything any more
        static::mockYiiConsoleApplication(); // Create an app
        $this->assertInstanceOf(\yii\console\Application::class, \Yii::$app); // now it must exist
        static::destroyYiiApplication(); // Destroy
        $this->assertNull(\Yii::$app); // now it must exist
    }

    public function testMockYiiConsoleApplication()
    {
        static::mockYiiConsoleApplication();
        $this->assertInstanceOf(\yii\console\Application::class, \Yii::$app);
    }

    public function testMockYiiWebApplication()
    {
        static::mockYiiWebApplication();
        $this->assertInstanceOf(\yii\web\Application::class, \Yii::$app);
    }

    public function testRuntimeAlias()
    {
        static::mockYiiWebApplication();
        \yii\helpers\BaseFileHelper::removeDirectory(__DIR__ . '/../runtime');
        $rt = \Yii::getAlias('@runtime');
        \yii\helpers\BaseFileHelper::createDirectory($rt);
        $this->assertSame(realpath($rt), realpath(__DIR__ . '/../runtime'));
        $this->assertDirectoryExists(__DIR__ .'/../runtime');
    }

}
