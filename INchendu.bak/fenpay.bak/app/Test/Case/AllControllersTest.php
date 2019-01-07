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
		$suite = new PHPUnit_Framework_TestSuite('All Controller Tests');

		$path = APP_TEST_CASES . DS . 'Controller' . DS;

		$suite->addTestFile($path . 'ApiControllerTest.php');
		$suite->addTestFile($path . 'AutoReplyFixcodesControllerTest.php');
		$suite->addTestFile($path . 'AutoReplyLocationExtendsControllerTest.php');
		$suite->addTestFile($path . 'AutoReplyLocationsControllerTest.php');
		$suite->addTestFile($path . 'AutoReplyMessagesControllerTest.php');
		$suite->addTestFile($path . 'PagesControllerTest.php');
		$suite->addTestFile($path . 'Component/WeixinApiComponentTest.php');
		$suite->addTestFile($path . 'Component/RemoteComponentTest.php');
		$suite->addTestFile($path . 'Component/ResponseComponentTest.php');
		return $suite;
	}
}