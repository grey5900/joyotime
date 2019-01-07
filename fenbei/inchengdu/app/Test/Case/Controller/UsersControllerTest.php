<?php
class UsersControllerTest extends ControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
        'app.user'
    );
    
    public function setUp() {
    	parent::setUp();
    	$this->controller = $this->getMockController();
    	$this->controller->Session->destroy();
    }
    
    public function testLogin() {
        $this->testAction('/users/login', array(
            'data' => array(
            	'User' => array(
    				'username' => 'admin',
    				'password' => 'pppp',
    			),
            ),
        ));
        
        $this->assertEqual($this->controller->Session->read('Auth.User.id'), 1);
        $this->assertEqual($this->controller->Session->read('Auth.User.username'), 'admin');
        $this->assertEqual($this->controller->Session->read('Auth.User.name'), 'I am administrator');
    }
    
    public function testLoginWithWrongPassword() {
        $this->testAction('/users/login', array(
            'data' => array(
            	'User' => array(
    				'username' => 'admin',
    				'password' => 'wrong_password',
    			),
            ),
        ));
        $this->assertEqual($this->controller->Session->read('Message.flash.message'), '用户名不存在/密码错误');
    }

    public function testLoginWithRememberMe() {
        $this->testAction('/users/login', array(
            'data' => array(
                'User' => array(
                    'username' => 'admin',
                    'password' => 'pppp',
                    'remember_me' => 1
                ) 
            ) 
        ));
        
        $this->assertEqual($this->controller->Session->read('Auth.User.id'), 1);
        $this->assertEqual($this->controller->Session->read('Auth.User.username'), 'admin');
        $this->assertEqual($this->controller->Session->read('Auth.User.name'), 'I am administrator');
        $this->assertEqual($this->controller->Cookie->read('Auth.User.id'), 1);
        $this->assertEqual($this->controller->Cookie->read('Auth.User.username'), 'admin');
        $this->assertEqual($this->controller->Cookie->read('Auth.User.name'), 'I am administrator');
    }

    public function testLoginUsingCookie() {
        $this->controller->Cookie->write('Auth.User', array(
            'id' => 1,
            'username' => 'admin',
            'name' => 'I am administrator'
        ));
        
        // editing...
        $this->controller->params = Router::parse('/users/login/');
        $this->controller->beforeFilter();
        $this->controller->login();
        
        $this->assertEqual($this->controller->Session->read('Auth.User.id'), 1);
        $this->assertEqual($this->controller->Session->read('Auth.User.username'), 'admin');
        $this->assertEqual($this->controller->Session->read('Auth.User.name'), 'I am administrator');
    }
    
/**
 * Helper method to get mock object of
 * controller more easier.
 *
 * @return Controller
 */
    private function getMockController() {
        $controller = $this->generate('Users');
        $controller->constructClasses();
        $controller->Components->init($controller);
        return $controller;
    }

/**
 * (non-PHPdoc)
 * @see CakeTestCase::tearDown()
 */
    public function tearDown() {
        unset($this->controller);
        parent::tearDown();
    }
}
