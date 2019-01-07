<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Checkout', 'Model');
APP::uses('ComponentCollection', 'Controller');
APP::uses('PriceComponent', 'Controller/Component');
APP::uses('VoiceFixture', 'Test/Case/Fixture');
APP::uses('UserFixture', 'Test/Case/Fixture');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class CheckoutTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.voice',
        'app.checkout',
    );
    
    public function getModelName() {
        return 'Checkout';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        $this->Voice = ClassRegistry::init('Voice');
        $this->user = $this->User->getById('51f0c30f6f159aec6fad8ce4');
        $this->voice = $this->Voice->findById('51f225e26f159afa43e76aff');
        
        $fixUser = new UserFixture();
        $fixVoice = new VoiceFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
        $this->fix['voice'] = $fixVoice->records[0];
    }
    
    public function testVoiceIncome() {
        $price = 100;
        $result = $this->model->voiceIncome($this->user, $this->voice, $price);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_VOICE_INCOME);
        $this->assertEqual($result['Checkout']['cover'], $this->fix['voice']['cover']);
        $this->assertEqual($result['Checkout']['title'], $this->fix['voice']['title']);
        $this->assertEqual($result['Checkout']['voice_id'], $this->fix['voice']['_id']);
        $this->assertEqual($result['Checkout']['amount']['time'], $price);
        $this->assertEqual($result['Checkout']['from']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['from']['username'], $this->fix['user2']['username']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
    }
    
    public function testVoiceCost() {
        $price = 100;
        $result = $this->model->voiceCost($this->user, $this->voice, $price);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_VOICE_COST);
        $this->assertEqual($result['Checkout']['cover'], $this->fix['voice']['cover']);
        $this->assertEqual($result['Checkout']['title'], $this->fix['voice']['title']);
        $this->assertEqual($result['Checkout']['voice_id'], $this->fix['voice']['_id']);
        $this->assertEqual($result['Checkout']['amount']['time'], $price);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
    }
    
    public function testReceived() {
        $price = 100;
        $payer = $this->User->getById((string)$this->fix['user1']['_id'])['User'];
        $payee = $this->User->getById((string)$this->fix['user2']['_id'])['User'];
        $result = $this->model->received($payer, $payee, $price);
        $this->assertEqual($result['Checkout']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Checkout']['type'], Checkout::TYPE_RECEIVED);
        $this->assertEqual($result['Checkout']['amount']['time'], $price);
        $this->assertEqual($result['Checkout']['from']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Checkout']['from']['username'], $this->fix['user1']['username']);
        $this->assertEqual(isset($result['Checkout']['modified']), true);
        $this->assertEqual(isset($result['Checkout']['created']), true);
    }
    
    public function testTransfer() {
    	$price = 100;
    	$payer = $this->User->getById((string)$this->fix['user1']['_id'])['User'];
    	$payee = $this->User->getById((string)$this->fix['user2']['_id'])['User'];
    	$result = $this->model->transfer($payer, $payee, $price);
    	debug($this->model->validationErrors);
    	$this->assertEqual($result['Checkout']['user_id'], $this->fix['user1']['_id']);
    	$this->assertEqual($result['Checkout']['type'], Checkout::TYPE_TRANSFER);
    	$this->assertEqual($result['Checkout']['amount']['time'], $price);
    	$this->assertEqual($result['Checkout']['to']['user_id'], $this->fix['user2']['_id']);
    	$this->assertEqual($result['Checkout']['to']['username'], $this->fix['user2']['username']);
    	$this->assertEqual(isset($result['Checkout']['modified']), true);
    	$this->assertEqual(isset($result['Checkout']['created']), true);
    }
}