<?php
/**
 * AllTests class
 *
 * This test group will run all tests.
 *
 * @package       Cake.Test.Case
 */
class AllModels extends PHPUnit_Framework_TestSuite {

/**
 * Suite define the tests for this suite
 *
 * @return void
 */
	public static function suite() {
		$suite = new PHPUnit_Framework_TestSuite('All Models Tests');

		$path = APP_TEST_CASES . DS . 'Model' . DS;

		$suite->addTestFile($path . 'AuthorizeTokenTest.php');
		$suite->addTestFile($path . 'BroadcastTest.php');
		$suite->addTestFile($path . 'CheckoutTest.php');
		$suite->addTestFile($path . 'CommentTest.php');
		$suite->addTestFile($path . 'FavoriteTest.php');
		$suite->addTestFile($path . 'FeedbackTest.php');
		$suite->addTestFile($path . 'FollowTest.php');
		$suite->addTestFile($path . 'GiftTest.php');
		$suite->addTestFile($path . 'GiftBroadcastTest.php');
		$suite->addTestFile($path . 'GiftLogTest.php');
		$suite->addTestFile($path . 'MessageBroadcastTest.php');
		$suite->addTestFile($path . 'NotificationTest.php');
		$suite->addTestFile($path . 'NotificationCounterTest.php');
		$suite->addTestFile($path . 'NotificationQueueTest.php');
		$suite->addTestFile($path . 'PointTest.php');
		$suite->addTestFile($path . 'ReverseWithdrawalTest.php');
		$suite->addTestFile($path . 'TagTest.php');
		$suite->addTestFile($path . 'TransferTest.php');
		$suite->addTestFile($path . 'UserTest.php');
		$suite->addTestFile($path . 'VoiceTest.php');
		$suite->addTestFile($path . 'WithdrawalTest.php');
		return $suite;
	}
}