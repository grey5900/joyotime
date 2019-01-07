<?php
APP::uses('AppTestCase', 'Test/Case/Model');
/**
 * @package       app.Test.Case.Model
 */
class UsernameTest extends AppTestCase {
    
    public function getModelName() {
        return 'Username';
    }
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testHash() {
        $hashed = $this->model->hash('abcd', '123');
        debug($hashed);
        $hashed = $this->model->hash('西门吹雪', '123');
        debug($hashed);
    }
    
    public function testUpdate() {
        $hashed = $this->model->hash('a', 'user1');
        $this->model->update($hashed);
        
        $hashed = $this->model->hash('ab', 'user2');
        $this->model->update($hashed);
        
        $saved = $this->model->getHashed();
        debug($saved);
        
        foreach($saved as $word => $set) {
            debug($this->model->getSet($set));
        }
    }
    
    public function testRemove() {
        $hashed = $this->model->hash('a', 'user1');
        $this->model->update($hashed);
        $this->model->remove($hashed);
        
        $saved = $this->model->getHashed();
        debug($saved);
        
        foreach($saved as $word => $set) {
            debug($this->model->getSet($set));
        }
    }
    
    public function testSearch() {
        $hashed = $this->model->hash('西门吹雪', '123');
        $this->model->update($hashed);
        
        $result = $this->model->search('吹雪');
        $this->assertEqual($result[0], '123');
        
        $result = $this->model->search('雪');
        $this->assertEqual($result[0], '123');
        
        $result = $this->model->search('吹');
        $this->assertEqual($result[0], '123');
        
        $result = $this->model->search('门吹');
        $this->assertEqual($result[0], '123');
    }
    
    public function testLimit() {
        $this->model->limit = 1;
        $hashed = $this->model->hash('ab', 'user1');
        $this->model->update($hashed);
        
        $hashed = $this->model->hash('a', 'user2');
        $this->model->update($hashed);
        
        $list = $this->model->getSet('un:a');
        debug($list);
        $this->assertEqual($list[0], 'user2');
    }
}