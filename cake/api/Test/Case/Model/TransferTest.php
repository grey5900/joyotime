<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Transfer', 'Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class TransferTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.voice',
    );
    
    public function getModelName() {
        return 'Transfer';
    }
    
    public function setUp() {
        parent::setUp();
        
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testAdd() {
        $price = 100;
        $payer = $this->User->getById((string)$this->fix['user1']['_id']);
        $payee = $this->User->getById((string)$this->fix['user2']['_id']);
        $result = $this->model->add($payer, $payee, $price);
        $this->assertEqual($result['Transfer']['user_id'], $this->fix['user1']['_id']);
        $this->assertEqual($result['Transfer']['type'], Transfer::TYPE);
        $this->assertEqual($result['Transfer']['amount']['time'], $price);
        $this->assertEqual($result['Transfer']['to']['user_id'], $this->fix['user2']['_id']);
        $this->assertEqual($result['Transfer']['to']['username'], $this->fix['user2']['username']);
        $this->assertEqual(isset($result['Transfer']['modified']), true);
        $this->assertEqual(isset($result['Transfer']['created']), true);
    }

}