<?php
APP::uses('AlipayCheckoutApiComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');

class TestController extends Controller {

}

class AlipayCheckoutApiComponentTest extends CakeTestCase {
    
    private $com;
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	// Setup our component and fake test controller
    	$Collection = new ComponentCollection();
    	$this->com = new AlipayCheckoutApiComponent($Collection);
    	
    	$CakeRequest = new CakeRequest();
    	$CakeResponse = new CakeResponse();
    	$this->Controller = new TestController($CakeRequest, $CakeResponse);
    	$this->Controller->Components->init($this->Controller);
    	$this->com->startup($this->Controller);
    }
    
    public function testPaid() {
    	$resp = $this->com->paid(array(
    		'checkout_id' => '5270bf716703339b4b8b48e5',
    	));
    	
    	debug($resp->getData());
    	debug($resp->getCode());
    	debug($resp->getMessage());
    }
}