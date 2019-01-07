<?php
APP::uses('QiNiuComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');

class TestQiNiuController extends Controller {

}

class QiNiuComponentTest extends CakeTestCase {
    
/**
 * @var QiNiuComponentTest
 */
    private $qiniu;
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	// Setup our component and fake test controller
    	$Collection = new ComponentCollection();
    	$this->qiniu = new QiNiuComponent($Collection);
    	
    	$CakeRequest = new CakeRequest();
    	$CakeResponse = new CakeResponse();
    	$this->Controller = new TestQiNiuController($CakeRequest, $CakeResponse);
    	$this->Controller->Components->init($this->Controller);
    	$this->qiniu->startup($this->Controller);
    }
    
    public function testUploadToken() {
        $token = $this->qiniu->uploadToken(QiNiuComponent::BUCKET_COVER);
        $token2 = $this->qiniu->uploadToken(QiNiuComponent::BUCKET_COVER);
        $token3 = $this->qiniu->uploadToken(QiNiuComponent::BUCKET_AVATAR);

        $this->assertEqual(empty($token), false);
        $this->assertEqual($token, $token2);
        $this->assertNotEqual($token2, $token3);
    }
    
    public function testGetPrivateUrl() {
        $bucket = 'fishsaying-test';
        $key = 'FjgBT7_yj0FHr3y7-hppbjc9EySv';
        $url = $this->qiniu->getPrivateUrl($bucket, $key);
        $this->assertEqual(empty($url), false);
    }
    
    public function testGetTrialVoice() {
        $key = 'FnkVI1vmJSU8lDPo760Rfcl1nWEp';
        $url = $this->qiniu->getTrialVoice($key);
        debug($url);
    }
}