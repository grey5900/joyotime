<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllUtilitiesTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Utilities Tests');

		$path = APP_TEST_CASES . DS . 'Utility' . DS;

		$suite->addTestFile($path . 'MongoTokenTest.php');
		return $suite;
	}
}