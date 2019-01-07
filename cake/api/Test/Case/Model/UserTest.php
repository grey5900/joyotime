<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UserFixture', 'Test/Fixture');
APP::uses('VoiceFixture', 'Test/Fixture');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class UserTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
        'app.voice',
    );
    
    public function getModelName() {
        return 'User';
    }
    
    public function setUp() {
        parent::setUp();
        $this->user1 = '51f0c30f6f159aec6fad8ce3';
        $this->user2 = '51f0c30f6f159aec6fad8ce4';
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    private function assertVoiceTotal($userId, $expected) {
        $user = $this->model->find('first', array(
            'conditions' => array(
                '_id' => new MongoId($userId)
            )
        ));
        $this->assertEqual($user['User']['voice_total'], $expected);
    }
    
    public function testCost() {
        $price = 9201;
        $result = $this->model->cost($this->fix['user1']['_id'], $price);
        $this->assertEqual($result, false);
        $this->assertMoney($this->fix['user1']['_id'], $this->fix['user1']['money'], array('cost' => 0));
        $price = 100;
        $result = $this->model->cost($this->fix['user1']['_id'], $price);
        $this->assertEqual($result, true);
        $this->assertMoney($this->fix['user1']['_id'], 
                $this->fix['user1']['money'] - $price, array('cost' => $price));
        $result = $this->model->cost($this->fix['user1']['_id'], $price);
        $this->assertEqual($result, true);
        $this->assertMoney($this->fix['user1']['_id'], 
                $this->fix['user1']['money'] - $price * 2, array('cost' => $price * 2));
    }
    
    public function testCostWhenMoneyLessThanEarn() {
        // The case of `money` is less than `earn`
        // `earn` is 5000, `money` is 9200
        // It will cost 5000 at once, then `money` is 4200
        $result = $this->model->cost($this->fix['user1']['_id'], 5000);
        $this->assertEqual($result, true);
        $this->assertMoney($this->fix['user1']['_id'],
        		$this->fix['user1']['money'] - 5000,
        		array('cost' => 5000));
        $user = $this->model->findById($this->fix['user1']['_id']);
        $this->assertEqual($user['User']['earn'], $this->fix['user1']['money'] - 5000);
    }
    
    public function testEarn() {
        $price = 10;
        $result = $this->model->earn($this->fix['user1']['_id'], $price);
        $this->assertEqual($result, true);
        $this->assertMoney($this->fix['user1']['_id'], 
                $this->fix['user1']['money'] + $price, array('earn' => $this->fix['user1']['earn'] + $price));
        $price = 10;
        $result = $this->model->earn($this->fix['user1']['_id'], $price);
        $this->assertEqual($result, true);
        $this->assertMoney($this->fix['user1']['_id'], 
                $this->fix['user1']['money'] + $price * 2, array('earn' => $this->fix['user1']['earn'] + $price * 2));
    }
    
//     public function testPayment() {
//         $seconds = 10;
//         $userId = (string) $this->fix['user1']['_id'];
//         $result = $this->model->payment($userId, $seconds);
//         $this->assertEqual($result, true);
//         $row = $this->model->findById($userId);
//         $row = $row[$this->model->name];
//         $this->assertEqual($row['money'], $this->fix['user1']['money'] + $seconds);
//         $this->assertEqual($row['income'], $this->fix['user1']['income'] + $seconds);
//         $this->assertEqual($row['earn'], $this->fix['user1']['earn']);
//     }
    
    public function testWithdrawRevert() {
        $userId = (string) $this->fix['user1']['_id'];
        $price = 1000;
        $result = $this->model->withdrawRevert($userId, $price);
        $this->assertEqual($result, true);
        $user = $this->model->findById($userId);
        $user = $user['User'];
        $this->assertEqual($user['money'], $this->fix['user1']['money'] + $price);
        $this->assertEqual($user['earn'], $this->fix['user1']['earn'] + $price);
        $this->assertEqual($user['income'], $this->fix['user1']['income'] + $price);
    }
    
    public function testTransfer() {
        $userId = (string) $this->fix['user1']['_id'];
        $price = 1000;
        $result = $this->model->transfer($userId, $price);
        $this->assertEqual($result, true);
        $user = $this->model->findById($userId);
        $user = $user['User'];
        $this->assertEqual($user['money'], $this->fix['user1']['money'] + $price);
        $this->assertEqual($user['earn'], $this->fix['user1']['earn']);
        $this->assertEqual($user['income'], $this->fix['user1']['income'] + $price);
    }
    
    public function testGift() {
        $userId = (string) $this->fix['user1']['_id'];
        $price = 1000;
        $deviceCode = 'mockup_device_code';
        $result = $this->model->gift($userId, $price, $deviceCode);
        $this->assertEqual($result, true);
        $user = $this->model->findById($userId);
        $user = $user['User'];
        $this->assertEqual($user['gift']['register']['device_code'], $deviceCode);
        $this->assertEqual($user['money'], $this->fix['user1']['money'] + $price);
        $this->assertEqual($user['earn'], $this->fix['user1']['earn']);
        $this->assertEqual($user['income'], $this->fix['user1']['income'] + $price);
    }
    
    public function testChkAvatar() {
        $result = $this->model->chkAvatar(array('avatar' => array(
            'source' => 'mockup_source_path'
        )));
        $this->assertEqual($result, true);
        
        // Assert fails because without field named `source`
        $result = $this->model->chkAvatar(array('avatar' => array(
            'x80' => 'mockup_x80_path'
        )));
        $this->assertEqual($result, false);
    }
    
    public function testGetById() {
        $userId = (string) $this->fix['user1']['_id'];
        $result = $this->model->getById($userId);
        $this->assertEqual($result['User']['_id'], $userId);
        $this->assertEqual(isset($result['User']['password']), false);
    }
    
    public function testIsUserNameUnique() {
        $fields = array('username' => $this->fix['user1']['username']);
        $this->assertEqual($this->model->isUserNameUnique($fields), false);
        $fields = array('email' => $this->fix['user1']['email']);
        $this->assertEqual($this->model->isUserNameUnique($fields), false);
        $fields = array('username' => 'mockup_username');
        $this->assertEqual($this->model->isUserNameUnique($fields), true);
        $this->model->data[$this->model->name]['_id'] = (string) $this->fix['user1']['_id'];
        $fields = array('email' => $this->fix['user1']['email']);
        $this->assertEqual($this->model->isUserNameUnique($fields), true);
    }
    
    public function testRegister() {
        $username = 'bob.bao';
        $email = 'bob.bao@gmail.com';
        $password = 'pppppppp';
        $avatar = array(
    		'source' => 'mockup_source_path',
    		'x80' => 'mockup_x80_path',
    		'x180' => 'mockup_x180_path',
        );
        $money = 10000;                 // 账户总额
        $earn = 0;                  // voice销售所得
        $cost = 0;                  // 支出总计
        $income = 0;                // 收入总计，包括转账，赠送，销售
        $latest_voice_posts = 0;     // 最新一条语音发布的时间
        $voice_total = 0;             // 发布的语音总数
        $favorite_size = 0;
        $locale = 'zh_CN';
        $role = USER::ROLE_USER;
        $deviceCode = 'mockup_device_code1';
        
        $result = $this->model->register(array());
        $this->assertEqual($result, false);
        $result = $this->model->register(array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'avatar' => $avatar,
            'money' => $money,
            'earn' => $earn,
            'cost' => $cost,
            'income' => $income,
            'latest_voice_posts' => $latest_voice_posts,
            'voice_total' => $voice_total,
            'favorite_size' => $favorite_size,
            'locale' => $locale,
            'role' => $role,
            'device_code' => $deviceCode,
        ));
        $this->assertEqual((boolean)$result, true);
        $this->assertEqual(isset($result['User']['password']), false);
    }
    
//     public function testVoiceTotalDecr() {
//     	$fix = new VoiceFixture();
//     	$voice = ClassRegistry::init('Voice');
//     	$voice->read(null, $fix->records[0]['_id']);
//     	$event = new CakeEvent('mockup.event', $voice);
//     	$this->model->voiceTotalDecr($event);
//     	$this->assertEqual($this->model->findById($this->fix['user1']['_id'])['User']['voice_total'], 0);
//     	$this->model->voiceTotalDecr($event);
//     	$this->assertEqual($this->model->findById($this->fix['user1']['_id'])['User']['voice_total'], 0);
//     }

    public function testFindAuthorize() {
        $data = array(
            'authorize' => 'baohanddd@gmail.com',
            'password' => 'pppppppp'
        );
        $user = $this->model->find('authorize', array(
            'conditions' => $data
        ));
        
        $this->assertEqual(is_array($user) && !empty($user), true);
        $this->assertEqual(isset($user['User']['password']), false);
    }
    
    private function assertMoney($userid, $money, $cost_or_earn) {
        $key = key($cost_or_earn);
        $user = $this->model->findById($userid);
        $this->assertEqual($user['User']['money'], $money);
        $this->assertEqual($user['User'][$key], $cost_or_earn[$key]);
    }
}