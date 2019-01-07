<?php
APP::uses('AppTestCase', 'Test/Case/Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class TagTest extends AppTestCase {
    
    public $fixtures = array(
        'app.tag'
    );
    
    public function getModelName() {
        return 'Tag';
    }
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testIncr() {
        $this->assertEqual($this->model->incr('tag1', 'voice1'), true);
        $this->assertEqual($this->model->incr('tag1', 'voice2'), true);
        $this->assertEqual($this->model->incr('tag1', 'voice3'), true);
        $this->assertEqual($this->model->incr('tag1', 'voice4'), true);
        $this->assertEqual($this->model->incr('tag1', 'voice5'), true);
        $this->assertEqual($this->model->findByTitle('tag1')['Tag']['counter'], 5);
        $this->assertEqual($this->model->findByTitle('tag1')['Tag']['visble'], 1);
        $this->assertEqual($this->model->incr('tag1', 'voice1'), false);
        $this->assertEqual($this->model->incr('tag1', 'voice2'), false);
        $this->assertEqual($this->model->findByTitle('tag1')['Tag']['counter'], 5);
        
        // decrease...
        $this->assertEqual($this->model->decr('tag1', 'voice1'), true);
        $this->assertEqual($this->model->findByTitle('tag1')['Tag']['counter'], 4);
        $this->assertEqual($this->model->findByTitle('tag1')['Tag']['visble'], 0);
    }
    
    public function testDecr() {
        $this->assertEqual($this->model->decr('tag1', 'voice1'), false);
        $this->assertEqual($this->model->decr('mock_tag', 'mock_voice_1'), true);
        $this->assertEqual($this->model->decr('mock_tag', 'mock_voice_1'), false);
    }
    
    public function testFindByRegex() {
        $this->assertEqual((bool)$this->model->findByRegex('mock'), true);
        $this->assertEqual((bool)$this->model->findByRegex('unknown'), false);
    }
    
    public function testFindByVoice() {
        $this->assertEqual((bool)$this->model->findByVoice('mock_voice_1'), true);
        $this->assertEqual((bool)$this->model->findByVoice('unknown'), false);
    }
    
    public function testDecrByVoice() {
        $this->model->decrByVoice('mock_voice_1');
        $this->assertEqual($this->model->findByTitle('mock_tag')['Tag']['counter'], 0);
        $this->assertEqual($this->model->findByTitle('mock_tag')['Tag']['voices'], array());
    }
    
    public function testSetVisble() {
        $this->assertEqual($this->model->setVisble('mock_tag', 0), true);
        $this->assertEqual($this->model->findByTitle('mock_tag')['Tag']['visble'], 0);
        $this->assertEqual($this->model->setVisble('mock_tag', 1), true);
        $this->assertEqual($this->model->findByTitle('mock_tag')['Tag']['visble'], 1);
    }
}