<?php
/**
 * Author: Anton Sadovnikoff
 * Email: sadovnikoff@gmail.com
 */

namespace siddthartha\tests;

use yii\console\Application;
use yii\helpers\Console;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        new Application(require( __DIR__ . '/config.php' ));
    }

    /**
     *
     * @param \PHPUnit_Framework_TestResult $result
     */
    public function run(\PHPUnit_Framework_TestResult $result = null)
    {
        $test_title[] = Console::ansiFormat(\siddthartha\helpers\Name::shortClass(static::class), [Console::BG_CYAN]);
        $test_title[] = " -> ";
        $test_title[] = Console::ansiFormat(preg_replace("#^test[0-9]*#","",$this->getName()), [Console::BOLD]);

        echo "\n" . implode("", $test_title) . "\t";

        parent::run($result);
    }

}