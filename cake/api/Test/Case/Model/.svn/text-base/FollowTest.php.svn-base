<?php
APP::uses('AppTestCase', 'Test/Case/Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class FollowTest extends AppTestCase {
    
    public $fixtures = array(
        'app.follow',
        'app.voice',
    );
    
    private $fix = array();
    
/**
 * @var CakeEvent
 */
    private $event;
    
    public function getModelName() {
        return 'Follow';
    }
    
    public function setUp() {
        parent::setUp();
        $fix = new FollowFixture();
        $this->fix = $fix->records[0];
        
        $fix = new VoiceFixture();
        $voice = ClassRegistry::init('Voice');
        $voice->read(null, (string) $fix->records[0]['_id']);
        $this->event = new CakeEvent('mockup_event', $voice);
    }
    
    public function testResetNewPosts() {
        $userId = $this->fix['user_id'];
        $followerId = $this->fix['follower_id'];
        $result = $this->model->resetNewPosts($userId, $followerId);
        $this->assertEqual($result, true);
        $row = $this->model->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'follower_id' => $followerId
            )
        ));
        $this->assertEqual($row['Follow']['new_posts'], 0);
    }
    
    public function testIncrNewPosts() {
        $userId = $this->fix['user_id'];
        $followerId = $this->fix['follower_id'];
        
        $this->assertEqual($this->model->incrNewPosts($this->event), true);
        $this->assertEqual($this->model->incrNewPosts($this->event), true);
        $this->assertEqual($this->model->incrNewPosts($this->event), true);
        $this->assertEqual($this->model->incrNewPosts($this->event), true);
        $row = $this->model->find('first', array(
    		'conditions' => array(
				'user_id' => $userId,
				'follower_id' => $followerId
    		)
        ));
        $this->assertEqual($row['Follow']['new_posts'], 6);
    }
    
    public function testDecrNewPosts() {
        $userId = $this->fix['user_id'];
        $followerId = $this->fix['follower_id'];
        
        $this->assertEqual($this->model->decrNewPosts($this->event), true);
        $this->assertEqual($this->model->decrNewPosts($this->event), true);
        $row = $this->model->find('first', array(
    		'conditions' => array(
				'user_id' => $userId,
				'follower_id' => $followerId
    		)
        ));
        $this->assertEqual($row['Follow']['new_posts'], 0);
    }
    
    public function testCountNewPosts() {
        $this->assertEqual($this->model->incrNewPosts($this->event), true);
        $this->assertEqual($this->model->countNewPosts($this->fix['user_id']), 3);
    }
    
    public function testSave() {
        $this->model->create();
        $result = $this->model->save(array('user_id' => $this->fix['user_id']));
        $this->assertEqual($result, false);
        $this->assertEqual($this->model->validationErrors['follower_id'][0], 'Invalid follower id');
        $this->model->create();
        $result = $this->model->save(array('follower_id' => $this->fix['follower_id']));
        $this->assertEqual($result, false);
        $this->assertEqual($this->model->validationErrors['user_id'][0], 'Invalid user id');
        $this->model->create();
        $result = $this->model->save(array(
            'user_id' => $this->fix['user_id'], 
            'follower_id' => $this->fix['follower_id']
        ));
        $this->assertEqual((bool)$result, true);
    }
}