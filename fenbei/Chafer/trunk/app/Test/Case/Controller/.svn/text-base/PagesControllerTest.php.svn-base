<?php
App::uses('PagesController', 'Controller');

class PagesControllerTest extends ControllerTestCase {
    
    public function setUp() {
    	parent::setUp();
    }
    
    function startTest($method) {
    	$this->Pages = new PagesController(new CakeRequest(null, false), new CakeResponse());
    	$this->Pages->constructClasses();
    }
    
    public function testLogin() {
        $this->testAction('/pages/login');
        $this->assertTextContains('请输入用户名和密码登录', $this->contents);
    }
    
    public function testIndex() {
        $this->Pages->Session->write('Auth.User', array(
    		'id' => 1,
    		'username' => 'bob',
        ));
        $this->Pages->index();
        $this->assertTextContains('bs-docs-sidebar', $this->Pages->response->body());
    }
    
    function endTest($method) {
    	unset($this->Pages);
    	ClassRegistry::flush();
    }
}
