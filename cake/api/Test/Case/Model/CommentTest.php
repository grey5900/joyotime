<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Feedback', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class CommentTest extends AppTestCase {
    
    public $fixtures = array(
        'app.comment'
    );
    
    private $fix = array();
    
    public function getModelName() {
        return 'Comment';
    }
    
    public function getData() {
    	return array(
    		'user_id' => '51f0c30f6f159aec6fad8ce3',
    		'voice_id' => '51f225e26f159afa43e76aff',
    		'score' => 5.0,
    		'content' => 'hahaha',
    		'voice_title' => 'mockup title',
    		'voice_user_id' => '51f0c30f6f159aec6fad8ce3',
    	);
    }
    
    public function setUp() {
        parent::setUp();
        $fix = new CommentFixture();
        $this->fix = $fix->records[0];
    }
    
    public function testSave() {
        // regular saving...
        $data = $this->data();
        $this->model->create();
        
        $result = $this->model->save($data)[$this->model->name];
        $this->assertEqual($result['user_id'], $data['user_id']);
        $this->assertEqual($result['voice_id'], $data['voice_id']);
        $this->assertEqual($result['content'], $data['content']);
        $this->assertEqual($result['score'], intval($data['score']));
        $this->assertEqual($result['voice_title'], $data['voice_title']);
        $this->assertEqual($result['voice_user_id'], $data['voice_user_id']);
        $this->assertEqual(isset($result['modified']), true);
        $this->assertEqual(isset($result['created']), true);
    }
    
    public function testUpdate() {
        $commentId = (string)$this->fix['_id'];
        $score = 1;
        $result = $this->model->save(array(
            '_id' => new MongoId($commentId),
            'score' => $score
        ));
        $this->assertEqual((bool)$result, TRUE);
        $comment = $this->model->findById($commentId)[$this->model->name];
        $this->assertEqual($comment['score'], $score);
        $this->assertEqual($comment['prev_score'], 5);
    }
}