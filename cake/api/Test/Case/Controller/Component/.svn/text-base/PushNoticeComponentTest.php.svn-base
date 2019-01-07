<?php
APP::uses('AppComponentTestCase', 'Test/Case/Controller/Component');

class PushNoticeComponentTest extends AppComponentTestCase {
    
    private $push;
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->push = $this->getComponent();
    	$this->NotificationQueue = ClassRegistry::init('NotificationQueue');
    }
    
/**
 * (non-PHPdoc)
 * @see AppComponentTestCase::getComponentName()
 */
    public function getComponentName() {
        return 'PushNoticeComponent';
    }
    
    public function testSend() {
        // default platform is `ios`
        $this->push->sendNow('test123', '52484a0b6f159a3350b2d4eb');
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($item->getPlatform(), 'android');
        $this->assertEqual($item->getUserId(), '52484a0b6f159a3350b2d4eb');
        $this->assertEqual($item->getMessage(), 'test123');
        $this->assertEqual($item->getBadge(), 1);
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($item->getPlatform(), 'ios');
        $this->assertEqual($item->getUserId(), '52484a0b6f159a3350b2d4eb');
        $this->assertEqual($item->getMessage(), 'test123');
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($item == NULL, true);
    }
    
    public function testSendNowWithoutUserId() {
        $this->push->sendNow('test123');
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($item->getPlatform(), 'android');
        $this->assertEqual($item->getUserId(), '');
        $this->assertEqual($item->getMessage(), 'test123');
    }
}