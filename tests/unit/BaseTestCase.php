<?php
namespace siddthartha\behaviors\relativeSet\tests\unit;

use yii\console\Application;

class BaseTestCase extends \PHPUnit_Framework_TestCase {
	/**
	 * @inheritdoc
	 */
	protected function setUp() {
		new Application( require( __DIR__ . '/config.php' ) );
	}
}