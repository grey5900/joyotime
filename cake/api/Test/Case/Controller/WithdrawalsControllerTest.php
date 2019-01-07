<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Voice', 'Model');
APP::uses('Withdrawal', 'Model');

class WithdrawalsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.voice',
    	'app.withdrawal',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Withdrawal';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Withdrawals';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->User = ClassRegistry::init('User');
    	$this->Voice = ClassRegistry::init('Voice');
    	
    	$this->user = '51f0c30f6f159aec6fad8ce4';
    	$this->voice = '51f225e26f159afa43e76aff';
    	
    	$fixUser = new UserFixture();
    	$fixVoice = new VoiceFixture();
    	$fixWithdrawal = new WithdrawalFixture();
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    	$this->fix['voice'] = $fixVoice->records[0];
    	$this->fix['checkout1'] = $fixWithdrawal->records[0];
    }
    
    public function testAdd() {
        $userid = (string)$this->fix['user1']['_id'];
        $second = 1200;
        $gateway = 'alipay';
        $account = 'baohanddd@gmail.com';
        $realname = 'haha';
        
        // invalid second
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => -1,
                'gateway' => $gateway,
                'account' => $account,
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, false);
        
        // invalid gateway
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => $second,
                'gateway' => 'unknown_gateway',
                'account' => $account,
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, false);
        
        // invalid account
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => $second,
                'gateway' => $gateway,
                'account' => 'invalid_account',
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, false);
        
        // missing realname
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => $second,
                'gateway' => $gateway,
                'account' => $account
            )
        ));
        $this->assertEqual($result, false);
        
        // no enough money...
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => 2000000000,  // very big number...
                'gateway' => $gateway,
                'account' => $account,
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, false);
        
        // second less than 20 mins
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => 1199,  // less than 20 mins
                'gateway' => $gateway,
                'account' => $account,
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, false);
        
        $result = $this->testAction("/users/{$this->userId}/withdrawals.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => array(
                'second' => $second,
                'gateway' => $gateway,
                'account' => $account,
                'realname' => $realname
            )
        ));
        $this->assertEqual($result, true);
        $this->assertEqual(isset($this->headers['Location']), true);
        $this->assertEqual(isset($this->vars['root']['result']), true);
    }
    
    public function testAdminEdit() {
        $coId = (string) $this->fix['checkout1']['_id'];
        $result = $this->testAction("/admin/withdrawals/{$coId}.json?auth_token={$this->adminToken}", array(
    		'method' => 'PUT',
        ));
        $this->assertEqual($result, true);
        
        $co = $this->model->findById($coId);
        $co = $co['Withdrawal'];
        $this->assertEqual($co['processed'], Withdrawal::PROCESSED);
    }
}