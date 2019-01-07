<?php
class ShopsControllerTest extends ControllerTestCase {
    
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
        $this->testAction('/shops/index');
        $this->assertEqual(is_array($this->vars['shops']) 
                && !empty($this->vars['shops']), true);
        $this->assertTextContains('新建门店', $this->view);
    }
    
    public function testAdd() {
        $this->controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // inserting new one.
        $this->controller->data = array(
        	'Shop' => array(
        	    'id' => '',
				'name' => 'shop2',
			),
        );
        $this->controller->params = Router::parse('/shops/add/');
        $this->controller->beforeFilter();
        $result = $this->controller->add();
        
        $resp = json_decode($result);
        $this->assertEqual($resp->result, true);
    }
    
    public function testAddWithoutName() {
        $this->controller->request
            ->expects($this->once())
            ->method('is')
            ->will($this->returnValue('POST'));
        
        // inserting new one.
        $this->controller->data = array(
        	'Shop' => array(
        	    'id' => '',
				'name' => '',
			),
        );
        $this->controller->params = Router::parse('/shops/add/');
        $this->controller->beforeFilter();
        $result = $this->controller->add();
        
        $resp = json_decode($result);
        $this->assertEqual($resp->result, false);
    }


    
    public function testDelete() {
        $this->testAction('/shops/delete/1');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, true);
    }
    
    public function testDeleteWithoutId() {
        $this->testAction('/shops/delete/');
        $resp = json_decode($this->result);
        $this->assertEqual($resp->result, false);
    }
    
    public function testDeleteNoPermission() {
        $this->testAction('/shops/delete/999');
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
        $controller = $this->generate('Shops');
        $controller->constructClasses();
        $controller->Components->init($controller);
        $controller->Session->write('Auth.User', array(
        	'id' => 1,
        ));
        return $controller;
    }

    public function tearDown() {
        unset($this->controller);
        parent::tearDown();
    }
}
