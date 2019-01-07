<?php
APP::uses('AppComponentTestCase', 'Test/Case/Controller/Component');

class PatchComponentTest extends AppComponentTestCase {
    
    private $patch;
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->patch = $this->getComponent();
    }
    
/**
 * (non-PHPdoc)
 * @see AppComponentTestCase::getComponentName()
 */
    public function getComponentName() {
        return 'PatchComponent';
    }
    
    public function testPath() {
        $dimension = 'x640';
        $this->assertEqual(intval(substr($dimension, 1, strlen($dimension))), 640);
    }
}