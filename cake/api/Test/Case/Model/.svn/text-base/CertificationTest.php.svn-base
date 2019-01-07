<?php
APP::uses('AppTestCase', 'Test/Case/Model');

/**
 * @package       app.Test.Case.Model
 */
class CertificationTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user'
    );
    
    private $fix;
    
    public function getModelName() {
        return 'Certification';
    }
    
    public function setUp() {
        parent::setUp();
    }
    
    public function testSaveWithInvalidName() {
        $data = array(
    		'user_id' => (string) new MongoId(),
    		'name' => 'invalid_certification_name',
    		'token' => 'mock up credential'
        );
        
        $this->assertEqual($this->model->save($data), false);
    }
    
    public function testSave() {
        $data = array(
            'user_id' => (string) new MongoId(),
            'name' => Certification::NAME_SINA_WEIBO,
            'token' => 'mock up credential'
        );
        
        $saved = $this->model->save($data);
        
        foreach($data as $key => $val) {
            $this->assertEqual($saved[$this->model->name][$key], $data[$key]);
        }
    }
    
    public function testFind() {
        $this->User = ClassRegistry::init('User');
        $result = $this->User->find('authorize', array(
            'conditions' => array(
                'authorize' => 'baohanddd',
                'password' => 'pppppppp'
            )
        ));
        
        debug($result);
    }
}