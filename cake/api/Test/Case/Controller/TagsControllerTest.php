<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class TagsControllerTest extends AppControllerTestCase {
    
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
    	return 'Tags';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    }
    
    public function testAdminIndex() {
        $result = $this->testAction("/admin/tags.json?auth_token={$this->adminToken}", array(
            'method' => 'GET',
        ));
        $this->assertEqual($result, true);
    }
    
    public function testAdminEdit() {
        $result = $this->testAction("/admin/tags.json?auth_token={$this->adminToken}", array(
            'method' => 'PUT',
            'data' => array(
                'tag' => 'mock_tag',
                'visble' => 0
            )
        ));
        $this->assertEqual($result, true);
        
        // Assert fails...
        $result = $this->testAction("/admin/tags.json?auth_token={$this->adminToken}", array(
            'method' => 'PUT',
            'data' => array(
        		'tag' => 'unknown_tag',
        		'visble' => 1
            )
        ));
        $this->assertEqual($result, false);
    }
}