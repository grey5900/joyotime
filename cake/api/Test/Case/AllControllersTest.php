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

		$suite->addTestFile($path . 'AuthenticatesControllerTest.php');
// 		$suite->addTestFile($path . 'CheckoutsControllerTest.php');
		$suite->addTestFile($path . 'CommentsControllerTest.php');
		$suite->addTestFile($path . 'FavoritesControllerTest.php');
		$suite->addTestFile($path . 'FeedbacksControllerTest.php');
		$suite->addTestFile($path . 'FollowsControllerTest.php');
// 		$suite->addTestFile($path . 'NotificationsControllerTest.php');
		$suite->addTestFile($path . 'ReverseWithdrawalsControllerTest.php');
		$suite->addTestFile($path . 'TransfersControllerTest.php');
		$suite->addTestFile($path . 'UsersControllerTest.php');
// 		$suite->addTestFile($path . 'VoicesControllerTest.php');
		$suite->addTestFile($path . 'WithdrawalsControllerTest.php');
		$suite->addTestFile($path . 'Component/PushNoticeComponentTest.php');
		$suite->addTestFile($path . 'Component/QiNiuComponentTest.php');
		$suite->addTestFile($path . 'Component/ReceiptComponentTest.php');
		return $suite;
	}
}