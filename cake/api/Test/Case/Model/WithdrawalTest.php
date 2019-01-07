<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Withdrawal', 'Model');
APP::uses('ComponentCollection', 'Controller');
APP::uses('PriceComponent', 'Controller/Component');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class WithdrawalTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.withdrawal',
    );
    
    public function getModelName() {
        return 'Withdrawal';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        $this->user = $this->User->getById('51f0c30f6f159aec6fad8ce4');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testAdd() {
        $currency = 'CNY';
        $gateway = 'alipay';
        $collection = new ComponentCollection();
        $price = new PriceComponent($collection);
        $cash = $price->toCash($currency);
        $fee = $price->fee($gateway);
        $second = 1000;
        $amount = $cash->calc($second);
        $cost = $fee->draw($amount);
        $money = $amount - $cost;
        
        $user = (string)$this->fix['user1']['_id'];
        $account = 'baohanddd@gmail.com';
        $realname = 'haha';
        $result = $this->model->add($user, $second, $cash, $fee, $account, $realname);
        $record = $this->model->findById($result['Withdrawal']['_id']);
        $record = $record['Withdrawal'];
        $this->assertEqual($record['user_id'], $user);
        $this->assertEqual($record['type'], Withdrawal::TYPE);
        $this->assertEqual($record['amount']['time'], $second);
        $this->assertEqual($record['amount']['currency'], $currency);
        $this->assertEqual($record['amount']['money'], $money);
        $this->assertEqual($record['amount']['fee'], $cost);
        $this->assertEqual($record['amount']['gateway'], $gateway);
        $this->assertEqual($record['amount']['account'], $account);
        $this->assertEqual($record['amount']['realname'], $realname);
    }
    
    public function testUpdate() {
        $fix = new WithdrawalFixture();
        $co1 = $fix->records[0];
        $coId = (string) $co1['_id'];
        $result = $this->model->update($coId, Withdrawal::PROCESSED);
        $this->assertEqual((boolean)$result, true);
        $result = $this->model->update($coId, Withdrawal::REVERTED);
        $this->assertEqual((boolean)$result, true);
        $result = $this->model->update($coId, 'unknown_code');
        $this->assertEqual((boolean)$result, false);
    }
}