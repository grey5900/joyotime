<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllControllers extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Tests');

		$path = APP_TEST_CASES . DS . 'Controller' . DS;

		$suite->addTestFile($path . 'ApiControllerTest.php');
		$suite->addTestFile($path . 'CouponCodesControllerTest.php');
		$suite->addTestFile($path . 'SalersControllerTest.php');
		$suite->addTestFile($path . 'ShopsControllerTest.php');
		$suite->addTestFile($path . 'UsersControllerTest.php');
		$suite->addTestFile($path . 'Component/WeixinApiComponentTest.php');
		return $suite;
	}
}