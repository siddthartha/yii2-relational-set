<?php
/**
 * Author: Anton Sadovnikoff
 * Email: sadovnikoff@gmail.com
 */

namespace siddthartha\tests\relationalSetBehavior;
use siddthartha\helpers\Arr;

class RelationalSetBehaviorTest extends \siddthartha\tests\BaseTestCase
{

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $host1 = \siddthartha\tests\models\Host::findOne(['id' => 1]);
        $host2 = \siddthartha\tests\models\Host::findOne(['id' => 2]);

        \siddthartha\tests\models\HostSlave::deleteAll();
        $host1->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 1]));
        $host1->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 3]));
        $host1->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 5]));
        $host2->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 6]));
        $host2->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 8]));
        $host2->link('slaves', \siddthartha\tests\models\Slave::findOne(['id' => 10]));

    }

    public function testReadSet()
    {
        $host1 = \siddthartha\tests\models\Host::findOne(['id' => 1]);
        echo "\n" . Arr::rsLog($host1->_slaves, \siddthartha\tests\models\Slave::class);
        $this->assertArrayHasKey(1, $host1->_slaves);
        $this->assertArrayHasKey(3, $host1->_slaves);
        $this->assertArrayHasKey(5, $host1->_slaves);
        $this->assertArrayNotHasKey(2, $host1->_slaves);
        $this->assertArrayNotHasKey(4, $host1->_slaves);
        $this->assertArrayNotHasKey(6, $host1->_slaves);

        $host2 = \siddthartha\tests\models\Host::findOne(['id' => 2]);
        echo "\n" . Arr::rsLog($host2->_slaves, \siddthartha\tests\models\Slave::class);
        $this->assertArrayHasKey(6, $host2->_slaves);
        $this->assertArrayHasKey(8, $host2->_slaves);
        $this->assertArrayHasKey(10, $host2->_slaves);
        $this->assertArrayNotHasKey(5, $host2->_slaves);
        $this->assertArrayNotHasKey(7, $host2->_slaves);
        $this->assertArrayNotHasKey(9, $host2->_slaves);
    }

    public function testWriteSet()
    {
        $host1 = \siddthartha\tests\models\Host::findOne(['id' => 1]);
        $host2 = \siddthartha\tests\models\Host::findOne(['id' => 2]);
        $host1->_slaves = [2, 3, 4, 5, 6];
        $host2->_slaves = [4, 5, 6, 7, 8];
        $host1->save();
        $host2->save();
    }

}