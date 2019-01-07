<?php
class SalersControllerTest extends ControllerTestCase {
    
/**
 * Pre-defined fixtrues want to load.
 *
 * @var array
 */
    public $fixtures = array(
		'app.saler',
		'app.shop',
		'app.coupon_code',
    );
    
    public function setUp() {
    	parent::setUp();
    	$this->controller = $this->getMockController();
    }
    
    public function testIndex() {
        $this->testAction('/salers/index');
        $this->assertEqual(is_array($this->vars['salers']) 
                && !empty($this->vars['salers']), true);
        $this->assertTextContains('新建收银员', $this->view);
    }
    
    public function testAdding() {
        $this->testAction('/salers/add', array('method' => 'GET'));
        $this->assertEqual(!empty($this->vars['shops']), true);
        $this->assertTextContains('编辑收银员', $this->view);
    }
    
    public function testAdd() {
        $this->controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // inserting new one.
        $this->controller->data = array(
        	'Saler' => array(
        	    'id' => '',
				'name' => 'saler3',
			    'shop_id' => 1,
				'contact' => 'The contact info belongs saler3.'
			),
        );
        $this->controller->params = Router::parse('/salers/add/');
        $this->controller->beforeFilter();
        $this->controller->add();
        
        $this->assertEqual($this->controller->Session->read('Message.flash')['message'], '保存成功');
    }

    public function testEdit() {
        $this->controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // editing...
        $this->controller->data = array(
        	'Saler' => array(
        	    'id' => 1,
				'name' => 'saler3',
			    'shop_id' => 1,
				'contact' => 'The contact info belongs saler3.'
			),
        );
        $this->controller->params = Router::parse('/salers/add/');
        $this->controller->beforeFilter();
        $this->controller->add();
        
        $this->assertEqual($this->controller->Session->read('Message.flash')['message'], '保存成功');
    }
    
    public function testEditing() {
        $this->testAction('/salers/edit/1');
        $this->assertEqual(!empty($this->controller->request->data), true);
        $this->assertTextContains('编辑收银员', $this->view);
    }
    
    public function testDelete() {
        $this->testAction('/salers/delete/1');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, true);
    }
    
    public function testDeleteWithoutId() {
        $this->testAction('/salers/delete/');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
    public function testDeleteNoPermission() {
        $this->testAction('/salers/delete/999');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
/**
 * Helper method to get mock object of
 * controller more easier.
 *
 * @return Controller
 */
    private function getMockController() {
        $controller = $this->generate('Salers');
        $controller->constructClasses();
        $controller->Components->init($controller);
        $controller->Session->write('Auth.User', array(
        	'id' => 1,
        ));
        return $controller;
    }

    public function tearDown() {
        parent::tearDown();
    }
}
