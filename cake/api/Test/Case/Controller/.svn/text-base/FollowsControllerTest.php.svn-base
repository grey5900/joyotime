<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class FollowsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.follow',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Follow';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Follows';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$mongo = $this->model->getDataSource();
    	$mongo->ensureIndex($this->model, array('user_id' => 1, 'follower_id' => 1));
    	
    	$this->user2 = '51f0c30f6f159aec6fad8ce4';
    	$this->followId = '61f0c30f6f159aec6fad8ce3';
    }
    
    public function testEdit() {
        $result = $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'data' => array(
                'follower_id' => $this->user2
            ),
            'method' => 'PUT',
        ));
        $this->assertEqual($result, true);
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        
        $row = $this->model->find('first', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'follower_id' => $this->user2
            )
        ));
        $this->assertEqual(isset($row['Follow']['_id']), true);
        $this->assertEqual($row['Follow']['user_id'], $this->userId);
        $this->assertEqual($row['Follow']['follower_id'], $this->user2);
        
        // do follow again with same users...
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'data' => array(
                'follower_id' => $this->user2 
            ),
            'method' => 'PUT' 
        ));
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        
        $count = $this->model->find('count', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'follower_id' => $this->user2
            )
        ));
        $this->assertEqual($count, 1);
        
        // If it has followed...
        $result = $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'data' => array(
                'follower_id' => $this->user2 
            ),
            'method' => 'PUT' 
        ));
        $this->assertEqual($result, true);
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        $this->assertEqual($this->vars['root']['result']['_id'], $this->followId);
        
        // If try to follow self...
        $result = $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'data' => array(
                'follower_id' => $this->userId 
            ),
            'method' => 'PUT' 
        ));
        $this->assertEqual($result, false);
        $this->assertEqual($this->vars['result']['code'], 400);
        
        // no enough params supplied.
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'data' => array(
    		),
    		'method' => 'PUT'
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        
        // invalid format of follower_id.
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'data' => array(
    		    'follower_id' => 'invalid_format_user_id'
    		),
    		'method' => 'PUT'
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
    }
    
    public function testIndex() {
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'data' => array(
				'follower_id' => $this->user2
    		),
    		'method' => 'PUT',
        ));
        
        $page = 1;
        $limit = 20;
        $this->testAction("/users/{$this->userId}/follows.json?page=$page&limit=$limit&auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'GET'
        ));
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        $this->assertEqual($this->vars['root']['result']['items'][0]['_id'], $this->user2);
        // without page and limit
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'GET'
        ));
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        $this->assertEqual($this->vars['root']['result']['items'][0]['_id'], $this->user2);
    }
    
    public function testView() {
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'data' => array(
				'follower_id' => $this->user2
    		),
    		'method' => 'PUT',
        ));
        $this->testAction("/users/{$this->userId}/follows/{$this->user2}.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['user_id'], $this->userId);
        $this->assertEqual($this->vars['root']['result']['follower_id'], $this->user2);
    }
    
    public function testDelete() {
        $this->testAction("/users/{$this->userId}/follows.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'data' => array(
				'follower_id' => $this->user2
    		),
    		'method' => 'PUT',
        ));
        $this->testAction("/users/{$this->userId}/follows/{$this->user2}.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
            'method' => 'DELETE'
        ));
        
        $this->assertEqual($this->vars['root']['result'], array());
        $this->assertEqual($this->model->find('count', array(
        	'conditions' => array('user_id' => $this->userId)
        )), 0);
    }
    
    public function testPull() {
        $result = $this->testAction("/users/{$this->userId}/follows/new_posts.json?auth_token={$this->userToken}&api_key={$this->apikey}", array(
    		'method' => 'GET',
        ));
        $this->assertEqual($result, true);
        $this->assertEqual($this->vars['root']['result']['total'], 2);
    }
}