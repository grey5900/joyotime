<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('UsersController', 'Controller');
APP::uses('UserFixture', 'Test/Case/Fixture');

class UsersControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
        'app.user',        
        'app.comment',        
        'app.voice',        
    );
    
    /**
     * @var array
     */
    private $fix;
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
        return 'User';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
        return 'Users';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$this->id = '51f0c30f6f159aec6fad8ce3';    //baohanddd
    	$this->username = 'baohan';
    	$this->password = 'pppppp';
    	$this->email = 'bob@gmail.com';
    	
    	$this->regged = array(
    	    'username' => 'baohanddd',
    	    'password' => 'pppppppp',
    	    'email' => 'baohanddd@gmail.com',
    	);
    	
    	$this->png60 = dirname(__FILE__).'/Component/60.png';
    	$this->gif600 = dirname(__FILE__).'/Component/600.gif';
    	$this->jpg600 = dirname(__FILE__).'/Component/600.jpg';
    	$this->png600 = dirname(__FILE__).'/Component/600.png';
    	$this->png600x8 = dirname(__FILE__).'/Component/600-8.png';
    	$this->png600x24 = dirname(__FILE__).'/Component/600-24.png';
    	
    	$fixUser = new UserFixture();
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testRegister() {
        $avatar = 'mockup_avatar_path';
        $deviceCode = 'mockup_device_code';
        $this->testAction("/users.json?api_key={$this->apikey}&device_code=$deviceCode&language=en_US", array(
            'data' => array(
                'password' => $this->password,
                'email' => $this->email,
                'username' => $this->username,
                "avatar" => $avatar,
            ),
            'method' => 'POST'
        ));
        $saved = $this->vars['root']['result']['user'];
        $this->assertEqual($this->result, true);
        $this->assertEqual(isset($this->vars['root']['result']['auth_token']), true);
        $this->assertEqual($saved['role'], User::ROLE_USER);
        $this->assertEqual($saved['email'], $this->email);
        $this->assertEqual($saved['username'], $this->username);
        $this->assertEqual(isset($saved['password']), false);
        $this->assertEqual(isset($saved['_id']), true);
        $this->assertEqual(isset($saved['avatar']['source']), true);
        $this->assertEqual(isset($saved['avatar']['x80']), true);
        $this->assertEqual(isset($saved['avatar']['x180']), true);
        $this->assertEqual($saved['voice_total'], 0);
        $this->assertEqual($saved['favorite_size'], 0);
        $this->assertEqual($saved['money'], 0);
        $this->assertEqual($saved['earn'], 0);
        $this->assertEqual($saved['cost'], 0);
        $this->assertEqual($saved['locale'], 'en_US');
        $this->assertEqual($saved['device_code'][0], $deviceCode);
        
        $fav = ClassRegistry::init('Favorite');
        $item = $fav->findByUserId($saved['_id'])['Favorite'];
        $this->assertEqual($item['isdefault'], 1);
        $this->assertEqual($item['title'], Favorite::DEFAULT_FAVORITE_TITLE);
        $this->assertEqual($item['user_id'], $saved['_id']);
    }
    
    public function testEdit() {
        // change password
        $deviceCode = 'mockup_device_code';
        $this->testAction("/users/{$this->userId}/profile.json?api_key={$this->apikey}&auth_token={$this->userToken}&device_code=$deviceCode", array(
            'method' => 'PUT',
            'data' => array(
                'id' => $this->id,
                'old_password' => $this->regged['password'], 
                'password' => '123456', 
            ) 
        ));
        $this->assertEqual($this->result, true);
//         $this->assertEqual($saved['device_code'][0], $deviceCode);
    }
    
//     public function testView() {
//         $id = new MongoId();
//         $this->testAction("/users/$id.json", array(
//             'method' => 'GET'
//         ));
//         $this->assertEqual($this->vars['result']['code'], 404);
        
//         $this->testAction("/users/{$this->userId}.json", array(
//             'method' => 'GET'
//         ));
//         $this->assertEqual($this->vars['root']['result']['_id'], $this->id);
//         $this->assertEqual($this->vars['root']['result']['username'], $this->regged['username']);
//         $this->assertEqual($this->vars['root']['result']['email'], $this->regged['email']);
//         $this->assertEqual(isset($this->vars['root']['result']['password']), false);
//         $this->assertEqual(isset($this->vars['root']['result']['avatar']['source']), true);
//     }
    
//     public function testIndex() {
//         $result = $this->testAction("/users.json", array(
//             'method' => 'GET'
//         ));
//         debug($this->vars);
//         $total = $this->vars['root']['result']['total'];
//         $items = $this->vars['root']['result']['items'];
//         $this->assertEqual($total, 2);
//         $this->assertEqual(count($items), 2);
//         $this->assertEqual($items[0]['_id'], (string)$this->fix['user1']['_id']);
//         $this->assertEqual($items[1]['_id'], (string)$this->fix['user2']['_id']);
        
//         $result = $this->testAction("/users.json", array(
//         	'method' => 'GET',
//             'data' => array(
//                 'username' => 'bao'    // a part of username...
//             )
//         ));
//         $total = $this->vars['root']['result']['total'];
//         $items = $this->vars['root']['result']['items'];
//         $this->assertEqual($total, 1);
//         $this->assertEqual(count($items), 1);
//         $this->assertEqual($items[0]['_id'], (string)$this->fix['user1']['_id']);
        
//         $result = $this->testAction("/users.json", array(
//         	'method' => 'GET',
//             'data' => array(
//                 'username' => 'bao',    // a part of username...
//                 'except' => 'baohanddd'    // an username excepted
//             )
//         ));
//         $total = $this->vars['root']['result']['total'];
//         $items = $this->vars['root']['result']['items'];
//         $this->assertEqual($total, 0);
//         $this->assertEqual(count($items), 0);
//     }
}