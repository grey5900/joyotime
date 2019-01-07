<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Checkout', 'Model');
APP::uses('ReverseWithdrawal', 'Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class ReverseWithdrawalTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.voice',
        'app.withdrawal',
    );
    
    public function getModelName() {
        return 'ReverseWithdrawal';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        $this->user = $this->User->getById('51f0c30f6f159aec6fad8ce4');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testExist() {
        $fix = new WithdrawalFixture();
        $co1 = $fix->records[0];
        $coId = (string) $co1['_id'];
        $result = $this->model->exist($coId);
        $this->assertEqual($result, false);
        
        // Now revert it...
        $result = $this->model->add($co1['user_id'], $coId, $co1['amount']['time'], 'why revert');
        $result = $this->model->exist($coId);
        $this->assertEqual((boolean) $result, true);
    }
    
    public function testAdd() {
        $userId = (string) $this->fix['user1']['_id'];
        $fix = new WithdrawalFixture();
        $co1 = $fix->records[0];
        $coId = (string) $co1['_id'];
        $second = 1000;
        $reason = "That's why you want to revert.";
        $result = $this->model->add($userId, $coId, $second, $reason);
        
        $this->assertEqual((boolean)$result, true);
        $newCoId = $result['ReverseWithdrawal']['_id'];
        $co = $this->model->findById($newCoId);
        $co = $co['ReverseWithdrawal'];
        $this->assertEqual($co['_id'], $newCoId);
        $this->assertEqual($co['user_id'], $userId);
        $this->assertEqual($co['type'], ReverseWithdrawal::TYPE);
        $this->assertEqual($co['reverted'], $coId);
        $this->assertEqual($co['amount']['time'], $second);
        $this->assertEqual($co['reason'], $reason);
    }
}