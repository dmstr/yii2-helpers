<?php

namespace tests\codeception\unit\models;

use dektrium\user\models\User;
use dmstr\helpers\Metadata;
use yii\codeception\TestCase;

class UserTest extends TestCase
{

    public $appConfig = '@tests/tests/_config/test.php';

    protected function setUp()
    {
        parent::setUp();
    }

    public function testAllControllers()
    {
        $controllers = Metadata::getAllControllers();
        $this->assertEquals('/site',$controllers[0]['route']);
        $this->assertEquals('/en/site',$controllers[0]['url']);
    }

    public function testModuleControllers()
    {
        $controllers = Metadata::getModuleControllers('user');
        $this->assertEquals('/user/admin',$controllers[0]['route']);
        $this->assertEquals('/en/user/admin',$controllers[0]['url']);
    }

}
