<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class AuthenticatesControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    );
    
    private $fix = array();
    private $password = '';
    
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
    	return 'Authenticates';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$fix = new UserFixture();
    	$this->fix = $fix->records[0];
    	$this->password = 'pppppppp';
    }
    
    public function testAuthorize() {
        // Assert success login by username...
        $this->testAction("/authenticates.json?api_key={$this->apikey}", array(
            'method' => 'PUT',
            'data' => array(
                'authorize' => $this->fix['username'],
                'password' => $this->password
            )
        ));
        $token = $this->vars['root']['result']['auth_token'];
        $user = $this->vars['root']['result']['user'];
        $this->assertEqual(!empty($token), true);
        $this->assertEqual($user['_id'], (string) $this->fix['_id']);

        // Assert success login by email...
        $this->testAction("/authenticates.json?api_key={$this->apikey}", array(
            'method' => 'PUT',
            'data' => array(
                'authorize' => $this->fix['email'],
                'password' => $this->password
            )
        ));
        $token = $this->vars['root']['result']['auth_token'];
        $user = $this->vars['root']['result']['user'];
        $this->assertEqual(!empty($token), true);
        $this->assertEqual($user['_id'], (string) $this->fix['_id']);

        // Assert fail login by wrong name and wrong password...
        $this->testAction("/authenticates.json?api_key={$this->apikey}", array(
            'method' => 'PUT',
            'data' => array(
                'authorize' => $this->fix['email'],
                'password' => 'unknown password'
            )
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Username or password is wrong');
    }
    
}