<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('NotificationQueue', 'Model');
APP::uses('NotificationItem', 'Item');
APP::uses('AndroidNotificationItem', 'Item');
APP::uses('IosNotificationItem', 'Item');

/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class NotificationQueueTest extends AppTestCase {
    
    public function getModelName() {
        return 'NotificationQueue';
    }
    
    public function setUp() {
        parent::setUp();
        
        $this->voiceId = '51f225e26f159afa43e76aff';
        $this->userId = '51f0c30f6f159aec6fad8ce3';
    }
    
    public function testEnqueue() {
        $message = 'mockup_message';
        $badge = 1;
        
        $ios = new IosNotificationItem();
        $ios->setUserId($this->userId);
        $ios->setMessage($message);
        $ios->setBadge($badge);
        
        $android = new AndroidNotificationItem();
        $android->setUserId($this->userId);
        $android->setMessage($message);
        $android->setBadge($badge);
        
        // assert success...
        $this->assertEqual($this->model->enqueue($ios), true);
        $this->assertEqual($this->model->enqueue($android), true);
        
        $this->assertEqual($this->model->dequeue()->getPlatform(), 'ios');
        $this->assertEqual($this->model->dequeue()->getPlatform(), 'android');
        $this->assertEqual($this->model->dequeue(), false);
    }
}