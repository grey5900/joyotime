<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Voice', 'Model');
APP::uses('Checkout', 'Model');

class ReverseWithdrawalsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.voice',
    	'app.withdrawal',
    );
    
    /**
     * @var array
     */
    public $fix;
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'ReverseWithdrawal';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'ReverseWithdrawals';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$fix = new WithdrawalFixture();
    	$this->fix['checkout1'] = $fix->records[0];
    }
    
    public function testAdminAdd() {
        // Assert success...
        $result = $this->testAction("/admin/reverse_withdrawals.json?auth_token={$this->adminToken}", array(
            'method' => 'POST',
            'data' => array(
                'checkout_id' => (string)$this->fix['checkout1']['_id'],
                'reason' => 'that\'s why',
            )
        ));
        $this->assertEqual($result, true);
        $this->assertEqual(isset($this->headers['Location']), true);
    }
}