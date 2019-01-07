<?php
App::uses('AutoReplyCRUDController', 'Controller');

class CouponCodesControllerTest extends ControllerTestCase {
    
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
    }
    
    public function testIndex() {
        $controller = $this->getMockController();
        $this->testAction('/coupon_codes/index');
        $this->assertEqual(is_array($this->vars['messages']), true);
        $this->assertTextContains('选择时段', $this->view);
    }
    
    public function testIndexByPeriod() {
        $controller = $this->getMockController();
        $start = strftime('%Y-%m-%d', time() - 86400);
        $end = strftime('%Y-%m-%d', time() + 86400);
        $this->testAction("/coupon_codes/index/?start=$start&end=$end");
        $this->assertEqual(is_array($this->vars['messages']), true);
        $this->assertTextContains('选择时段', $this->view);
    }
    
    public function testIndexByShop() {
    	$controller = $this->getMockController();
    	$this->testAction("/coupon_codes/index/?shop=1");
//     	debug($this->vars);
    	$this->assertEqual(is_array($this->vars['messages']), true);
    	$this->assertTextContains('选择时段', $this->view);
    }
    
    public function testInvalid() {
        $controller = $this->getMockController();
        // expect success
        $this->testAction('/coupon_codes/invalid/1');
        $this->assertEqual($controller->Session->read('Message.flash')['message'], '设置成功');

        // expect success
        $this->testAction('/coupon_codes/invalid/1');
        $this->assertEqual($controller->Session->read('Message.flash')['message'], '设置成功');
        
        // expect failure, because no id
        $this->testAction('/coupon_codes/invalid/');
        $this->assertEqual($controller->Session->read('Message.flash')['message'], '设置失败');
        
        // expect failure, because no related would be found.
        $this->testAction('/coupon_codes/invalid/999');
        $this->assertEqual($controller->Session->read('Message.flash')['message'], '设置失败');
    }
    
/**
 * Helper method to get mock object of 
 * controller more easier.
 * 
 * @return Controller
 */
    private function getMockController() {
        $controller = $this->generate('CouponCodes');
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
