<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UserFixture', 'Test/Case/Fixture');
APP::uses('GiftLog', 'Model');
/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class GiftLogTest extends AppTestCase {
    
    public $fixtures = array(
        'app.user',
    );
    
    public function getModelName() {
        return 'GiftLog';
    }
    
    public function setUp() {
        parent::setUp();
        $this->User = ClassRegistry::init('User');
        
        $fixUser = new UserFixture();
        $this->fix['user1'] = $fixUser->records[0];
        $this->fix['user2'] = $fixUser->records[1];
    }
    
    public function testSave() {
        $saved = $this->model->save(array());
        $this->assertEqual($saved, false);
        
        // checking empty message...
        $saved = $this->model->save(array(
        	'user_id' => (string) $this->fix['user1']['_id'],
        	'message' => ''
        ));
        $this->assertEqual($saved, false);
        
        // Check save() with an invalid user id...
        $saved = $this->model->save(array(
        	'user_id' => 'invalid_user_id',
        	'message' => 'mockup_message'
        ));
        $this->assertEqual($saved, false);
        
        // Check save message only...
        $saved = $this->model->save(array(
        	'user_id' => (string) $this->fix['user1']['_id'],
        	'message' => 'mockup_message'
        ));
        $this->assertEqual((bool)$saved, false);
        
        // check to save gift message...
        $saved = $this->model->save(array(
        	'user_id' => (string) $this->fix['user1']['_id'],
        	'amount' => array('time' => 200),
        	'message' => 'mockup_message'
        ));
        $this->assertEqual((bool)$saved, true);
        $this->assertEqual($saved[$this->model->name]['amount']['time'], 200);
        $this->assertEqual($saved[$this->model->name]['type'], GiftLog::TYPE_SPECIFIED);
        
        // check to save gift message in broadcasting...
        $saved = $this->model->save(array(
        	'amount' => array('time' => 200),
        	'message' => 'mockup_message'
        ));
        $this->assertEqual((bool)$saved, true);
        $this->assertEqual($saved[$this->model->name]['amount']['time'], 200);
        $this->assertEqual($saved[$this->model->name]['type'], GiftLog::TYPE_GLOBAL);
        
        // check to save gift message...
        $saved = $this->model->save(array(
        	'user_id' => (string) $this->fix['user1']['_id'],
            'amount' => array('time' => -1),
        	'message' => 'mockup_message'
        ));
        $this->assertEqual((bool)$saved, false);
        
        // check to save gift message...
        $this->model->create();
        $saved = $this->model->save(array(
        	'user_id' => (string) $this->fix['user1']['_id'],
            'seconds' => 200,
        	'message' => 'mockup_message'
        ));
        $this->assertEqual((bool)$saved, true);
        $this->assertEqual($saved[$this->model->name]['amount']['time'], 200);
    }
}