<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');

class FavoritesControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.voice',
    	'app.user',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Favorite';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Favorites';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->title = 'The first album';
    	$this->title2 = 'The second album';
    	$this->voice1 = '51f225e26f159afa43e76aff';
    	$this->voice2 = '51f223956f159afa43a9681d';
    }
    
    public function testAdd() {
        $this->title = 'The first album';
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title,
            ),
            'method' => 'POST'
        ));
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        
        // Post a request with the same title again...
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title,
            ),
            'method' => 'POST'
        ));
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'method' => 'POST'
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
    }
    
    public function testDelete() {
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title,
            ),
            'method' => 'POST' 
        ));
        $favoriteId = $this->vars['root']['result']['_id'];
        $this->testAction("/users/{$this->userId}/favorites/$favoriteId/voices.json?auth_token={$this->userToken}", array(
            'data' => array(
                'voice_id' => $this->voice1    
            ),
            'method' => 'PUT'
        ));
        
        $result = $this->testAction("/users/{$this->userId}/favorites/$favoriteId/voices.json?auth_token={$this->userToken}", array(
            'data' => array(
            	'voice_id' => $this->voice2
            ),
            'method' => 'PUT' 
        ));
        $this->assertEqual($result, false, 'The status of voice2 is pending, do not allow add to favorite.');
        $this->assertCountVoices(1);
        
        $result = $this->testAction("/users/{$this->userId}/favorites/$favoriteId/voices.json?voice_id=unknown&auth_token={$this->userToken}", array(
            'method' => 'DELETE'
        ));
        $this->assertEqual($result, false, 'The voice id is invalid.');
        $this->assertCountVoices(1);
        
        $this->testAction("/users/{$this->userId}/favorites/$favoriteId/voices.json?voice_id=$this->voice1&auth_token={$this->userToken}", array(
            'method' => 'DELETE'
        ));
        $this->assertEqual($this->vars['root']['result'], array());
        $this->assertCountVoices(0);
            
        // Delete whole favorite...
        $this->testAction("/users/{$this->userId}/favorites/$favoriteId.json?auth_token={$this->userToken}", array(
        	'method' => 'DELETE'
        ));
        $this->assertEqual($this->vars['root']['result'], array());
        $this->assertNoFound();
    }
    
    public function testIndex() {
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title,
            ),
            'method' => 'POST' 
        ));
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title2,
            ),
            'method' => 'POST' 
        ));
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['total'], 2);
        $this->assertEqual(count($this->vars['root']['result']['items']), 2);

        // Test case of pagination
        $this->testAction("/users/{$this->userId}/favorites.json?page=1&limit=1", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['total'], 2);
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['items'][0]['title'], $this->title);
        
        $this->testAction("/users/{$this->userId}/favorites.json?page=2&limit=1", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['total'], 2);
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['items'][0]['title'], $this->title2);
    }
    
    public function testView() {
        $this->testAction("/users/{$this->userId}/favorites.json", array(
            'data' => array(
                'title' => $this->title,
            ),
            'method' => 'POST' 
        ));
        
        $favorite_id = $this->vars['root']['result']['_id'];
        
        $this->testAction("/users/{$this->userId}/favorites/$favorite_id/voices.json?auth_token={$this->userToken}", array(
    		'data' => array(
    			'voice_id' => $this->voice1
    		),
    		'method' => 'PUT'
        ));
        
        $this->testAction("/users/{$this->userId}/favorites/{$favorite_id}.json?auth_token={$this->userToken}", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        $this->assertEqual($this->vars['root']['result']['items'][0]['_id'], $this->voice1);
        $this->assertEqual(isset($this->vars['root']['result']['items'][0]['user_id']), true);
        $this->assertEqual($this->vars['root']['result']['items'][0]['title'], 'voice title first');
        $this->assertEqual($this->vars['root']['result']['items'][0]['user']['_id'], $this->userId);
        $this->assertEqual((bool)stristr($this->vars['root']['result']['items'][0]['user']['avatar']['source'], 
                QiNiuComponent::BUCKET_AVATAR), true);
    }
    
/**
 * Assert no found any result 
 */
	 private function assertNoFound() {
		$this->assertEqual($this->model->find('count', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'title' => $this->title
            )
        )), 0);
	}
    
/**
 * Assert the total of voices is same with $expect in a snapshot.
 */
	 private function assertCountVoices($expect) {
		$favor = $this->model->find('first', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'title' => $this->title
            ) 
        ));
        $this->assertEqual($favor['Favorite']['size'], $expect);
	}
}