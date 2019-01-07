<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllTests extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$path = APP_TEST_CASES . DS;

		$suite->addTestFile($path . 'AllControllersTest.php');
		$suite->addTestFile($path . 'AllModelsTest.php');
		return $suite;
	}
}