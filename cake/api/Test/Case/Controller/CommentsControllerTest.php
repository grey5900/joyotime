<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Notification', 'Model');

class CommentsControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.voice',
    	'app.user',
    	'app.checkout',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Comment';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Comments';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->username = 'baohanddd';
    	$this->user_id = '51f0c30f6f159aec6fad8ce3';
    	$this->score = 5;
    	$this->voice = '51f225e26f159afa43e76aff';
    	$this->content = 'The first comment';
    	
    	$this->Notification = ClassRegistry::init('Notification');
    	$this->NotificationQueue = ClassRegistry::init('NotificationQueue');
    	
    	$fix = new VoiceFixture();
    	$this->fix['voice1'] = $fix->records[0];
    	$this->fix['voice2'] = $fix->records[1];
    }
    
    public function testAdd() {
        // Assert success...
        $this->testAction("/users/{$this->user_id}/comments.json", array(
            'data' => array(
                'voice_id' => $this->voice,
                'score' => $this->score,
                'content' => $this->content
            ),
            'method' => 'POST'
        ));
        $this->assertEqual(is_array($this->vars['root']['result']), true);
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        $this->assertEqual($this->vars['root']['result']['user_id'], $this->user_id);
        $this->assertEqual($this->vars['root']['result']['voice_id'], $this->voice);
        $this->assertEqual($this->vars['root']['result']['score'], $this->score);
        $this->assertEqual($this->vars['root']['result']['content'], $this->content);
        $this->assertEqual($this->vars['root']['result']['voice_title'], $this->fix['voice1']['title']);
        $this->assertEqual($this->vars['root']['result']['user']['username'], $this->username);
        $this->assertEqual(is_array($this->vars['root']['result']['user']['avatar']), true);
        
        $this->assertCountComment(1);
        $this->assertScoreComment(5);
        
        // Checking notification...
        $row1 = $this->Notification->find('first', array(
    		'conditions' => array(
    			'user_id' => $this->user_id
    		),
    		'order' => array('created' => 'desc')
        ));
        $this->assertEqual($row1['Notification']['type'], Notification::TYPE_NEW_COMMENT);
        $this->assertEqual($row1['Notification']['merged'], 1);
        
        $this->testAction("/users/{$this->user_id}/comments.json", array(
            'data' => array(
                'voice_id' => $this->voice,
                'score' => 1,
                'content' => $this->content
            ),
            'method' => 'POST'
        ));
        $this->assertEqual(is_array($this->vars['root']['result']), true);
        $this->assertEqual(isset($this->vars['root']['result']['_id']), true);
        $this->assertEqual($this->vars['root']['result']['user_id'], $this->user_id);
        $this->assertEqual($this->vars['root']['result']['voice_id'], $this->voice);
        $this->assertEqual($this->vars['root']['result']['score'], 1);
        $this->assertEqual($this->vars['root']['result']['content'], $this->content);
        $this->assertEqual($this->vars['root']['result']['user']['username'], $this->username);
        $this->assertEqual(is_array($this->vars['root']['result']['user']['avatar']), true);
        
        $this->assertCountComment(1);
        $this->assertScoreComment(1);
        
        // Checking notification...
        $row2 = $this->Notification->find('first', array(
    		'conditions' => array(
    			'user_id' => $this->user_id
    		),
    		'order' => array('created' => 'desc')
        ));
        $this->assertEqual($row2['Notification']['type'], Notification::TYPE_NEW_COMMENT);
        $this->assertEqual($row2['Notification']['merged'], 1);
        $this->assertEqual($row2['Notification']['_id'] == $row1['Notification']['_id'], true);
    }
    
    public function testDelete() {
        $this->testAction("/users/{$this->user_id}/comments.json", array(
            'data' => array(
                'voice_id' => $this->voice,
                'score' => 5,
                'content' => $this->content
            ),
            'method' => 'POST'
        ));
        
        $this->assertCountComment(1);
        $this->assertScoreComment(5);
        
        $commentId = $this->vars['root']['result']['_id'];
        $this->testAction("/users/{$this->user_id}/comments/{$commentId}.json", array(
            'method' => 'DELETE'
        ));
        $this->assertCountComment(0);
        $this->assertScoreComment(0);
        
        // Checking notification...
        $row = $this->Notification->find('first', array(
            'conditions' => array(
                'user_id' => $this->user_id
            ),
            'order' => array('created' => 'desc')
        ));
        debug($row);
        $this->assertEqual($row['Notification']['type'], Notification::TYPE_HIDE_COMMENT);
        
        // Checking push notice...
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($item->getUserId(), $this->userId);
        $this->assertEqual($item->getMessage(), $row['Notification']['message']);
    }
    
    public function testIndex() {
        $this->testAction("/users/{$this->user_id}/comments.json", array(
            'data' => array(
                'user_id' => $this->user_id,
                'voice_id' => $this->voice,
                'score' => 1,
                'content' => $this->content
            ),
            'method' => 'POST'
        ));
        $this->testAction("/voices/{$this->voice}/comments.json", array(
            'method' => 'GET'
        ));
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
    }
    
    public function testView() {
        $this->testAction("/users/{$this->user_id}/comments.json", array(
    		'data' => array(
				'voice_id' => $this->voice,
				'score' => $this->score,
				'content' => $this->content
    		),
    		'method' => 'POST'
        ));
        
        $this->commentId = $this->vars['root']['result']['_id'];
        $this->testAction("/comments/{$this->commentId}.json", array(
           'method' => 'GET' 
        ));
        $this->assertEqual($this->vars['root']['result']['voice_id'], $this->voice);
        $this->assertEqual($this->vars['root']['result']['user_id'], $this->user_id);
        $this->assertEqual($this->vars['root']['result']['user']['_id'], $this->user_id);
    }
    
/**
 * Assert the total of voices is same with $expect in a snapshot.
 */
    private function assertCountComment($expect) {
        $model = $this->controller->Voice;
		$voice = $model->find('first', array(
            'conditions' => array(
                '_id' => $this->voice,
            ) 
        ));
		$total = $this->model->find('count', array(
		    'conditions' => array(
		        'user_id' => $this->user_id,
		        'hide' => false,
		    )
		));
        $this->assertEqual($total, $expect);
        $this->assertEqual($voice['Voice']['comment_total'], $expect);
    }
    
/**
 * Assert the average score of voice.
 * @param float $expect
 */
    private function assertScoreComment($expect) {
        $model = $this->controller->Voice;
        $voice = $model->find('first', array(
    		'conditions' => array(
    			'_id' => $this->voice,
    		)
        ));
        $this->assertEqual($voice['Voice']['score'], $expect);
    }
}