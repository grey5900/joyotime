<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class AvailableTagsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.tag'
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Tag';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'AvailableTags';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    }
    
    public function testAdminIndex() {
        $result = $this->testAction("/available_tags.json?auth_token={$this->adminToken}", array(
            'method' => 'GET',
            'data' => array(
                'keyword' => 'mock'
            )
        ));
        $this->assertEqual($result, true);
        
        // Assert fails...
        $result = $this->testAction("/available_tags.json?auth_token={$this->adminToken}", array(
            'method' => 'GET',
        ));
        $this->assertEqual($result, false);
    }
}