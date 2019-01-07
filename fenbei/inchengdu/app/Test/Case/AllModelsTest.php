<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllModelsTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Model Tests');

		$path = APP_TEST_CASES . DS . 'Model' . DS;

		$suite->addTestFile($path . 'ShopTest.php');
		$suite->addTestFile($path . 'SalerTest.php');
		return $suite;
	}
}