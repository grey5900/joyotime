<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Voice', 'Model');
APP::uses('Checkout', 'Model');
APP::uses('VoiceFixture', 'Test/Case/Fixture');
APP::uses('UserFixture', 'Test/Case/Fixture');

class CheckoutsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.voice',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Checkout';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Checkouts';
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
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    	$this->fix['voice'] = $fixVoice->records[0];
    }
    
    public function testBuy() {
        $result = $this->testAction("/checkouts/buy/{$this->user}/{$this->voice}");
        $this->assertEqual($result, true);
        $user2 = ClassRegistry::init('User')->findById($this->user);
        $this->assertEqual($user2['User']['money'], 
                $this->fix['user2']['money'] - $this->fix['voice']['length']);
        $this->assertEqual($user2['User']['cost'], 
                $this->fix['user2']['cost'] + $this->fix['voice']['length']);
        $user1 = ClassRegistry::init('User')->findById($this->fix['user1']['_id']);
        $this->assertEqual($user1['User']['money'],
        		$this->fix['user1']['money'] + $this->fix['voice']['length']);
        $this->assertEqual($user1['User']['earn'],
        		$this->fix['user1']['earn'] + $this->fix['voice']['length']);
        
        $result = $this->model->findByUserId((string)$this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_VOICE_INCOME);
        $this->assertEqual($result['Checkout']['avatar'], $this->fix['voice']['cover']);
        $this->assertEqual($result['Checkout']['title'], $this->fix['voice']['title']);
        $this->assertEqual($result['Checkout']['voice_id'], $this->fix['voice']['_id']);
        $this->assertEqual($result['Checkout']['amount']['time'], $this->fix['voice']['length']);
        $this->assertEqual($result['Checkout']['from']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['from']['username'], $this->fix['user2']['username']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
        
        $result = $this->model->findByUserId((string)$this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_VOICE_COST);
        $this->assertEqual($result['Checkout']['avatar'], $this->fix['voice']['cover']);
        $this->assertEqual($result['Checkout']['title'], $this->fix['voice']['title']);
        $this->assertEqual($result['Checkout']['voice_id'], $this->fix['voice']['_id']);
        $this->assertEqual($result['Checkout']['amount']['time'], $this->fix['voice']['length']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
    }
    
    public function testTransfer() {
        $price = 100;
        $payerId = (string)$this->fix['user1']['_id'];
        $payeeId = (string)$this->fix['user2']['_id'];
        $result = $this->testAction("/checkouts/transfer/{$payerId}/{$payeeId}/{$price}.json");
        $this->assertEqual($result, true);
        $payer = $this->User->getById($payerId);
        $this->assertEqual($payer['User']['money'], $this->fix['user1']['money'] - $price);
        $this->assertEqual($payer['User']['cost'], $this->fix['user1']['cost'] + $price);
        $payee = $this->User->getById($payeeId);
        $this->assertEqual($payee['User']['money'], $this->fix['user2']['money'] + $price);
        $this->assertEqual($payee['User']['earn'], $this->fix['user2']['earn'] + $price);
        
        $result = $this->model->findByUserId($payerId);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_TRANSFER);
        $this->assertEqual($result['Checkout']['amount']['time'], $price);
        $this->assertEqual($result['Checkout']['to']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['to']['username'], $this->fix['user2']['username']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
        
        $result = $this->model->findByUserId((string)$this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_RECEIVED);
        $this->assertEqual($result['Checkout']['amount']['time'], $price);
        $this->assertEqual($result['Checkout']['from']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['from']['username'], $this->fix['user1']['username']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
    }
    
    public function testBought() {
        $voices = array(
            (string)$this->fix['voice']['_id'],
            str_replace('a', 'b', (string)$this->fix['voice']['_id']),
            str_replace('b', 'c', (string)$this->fix['voice']['_id']),
        );
        
        $userId = 'baohanddd';
        $cache = $this->controller->Cache->voice()->bought();
        
        foreach($voices as $voiceId) {
            $cache->push($userId, $voiceId);
        }
        
        $this->testAction("/checkouts/boughts/$userId.json?api_key=".$this->apikey);
    }
    
    public function testWithdraw() {
        $userid = (string)$this->fix['user1']['_id'];
        $second = 1000;
        $gateway = 'alipay';
        $account = 'baohanddd@gmail.com';
        $realname = 'haha';
        
        $result = $this->testAction("/checkouts/withdraw/$userid/$second/$gateway.json?account=$account&realname=$realname");
        $this->assertEqual($result, true);

        // no enough time to withdraw
        $userid = (string)$this->fix['user2']['_id'];
        $second = 1000;
        $gateway = 'alipay';
        $account = 'baohanddd@gmail.com';
        $realname = 'haha';
        
        $result = $this->testAction("/checkouts/withdraw/$userid/$second/$gateway.json?account=$account&realname=$realname");
        $this->assertEqual($result, false);
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Failed to withdraw, because no enough remaining seconds');
        
    }
}