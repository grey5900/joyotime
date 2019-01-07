<?php
App::uses('LocationsController', 'Controller');

class LocationsControllerTest extends ControllerTestCase {
    
    private $placeIds = array();
    
    public function setUp() {
    	parent::setUp();
    	$this->placeIds = array(
    	    'inchengdu' => 12120
    	);
    }
    
    public function testIndex() {
        $controller = $this->getMockController();
        $this->testAction('/locations/index/'.$this->placeIds['inchengdu']);
//         $this->assertEqual(is_array($this->vars['messages']), true);
//         $this->assertTextContains('选择时段', $this->view);
    }

/**
 * Helper method to get mock object of
 * controller more easier.
 *
 * @return Controller
 */
    private function getMockController() {
    	$controller = $this->generate('Locations');
    	$controller->constructClasses();
    	$controller->Components->init($controller);
    	return $controller;
    }
}
