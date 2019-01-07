<?php
APP::uses('AppControllerTestCase', 'Test/Case/Controller');
APP::uses('Voice', 'Model');
APP::uses('VoiceFixture', 'Test/Case/Fixture');
APP::uses('UserFixture', 'Test/Case/Fixture');

class VoicesControllerTest extends AppControllerTestCase {
    
    public $fixtures = array(
    	'app.user',
    	'app.voice',
    	'app.follow',
    );
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getModelName()
 */
    public function getModelName() {
    	return 'Voice';
    }
    
/**
 * (non-PHPdoc)
 * @see AppControllerTestCase::getControllerName()
 */
    public function getControllerName() {
    	return 'Voices';
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->user_id = '51f0c30f6f159aec6fad8ce3';
    	$this->username = 'baohanddd';
    	$this->voice = '51f225e26f159afa43e76aff';
    	$this->latitude = '38.186387';
    	$this->longitude = '-96.617203';
    	
    	$this->png60 = dirname(__FILE__).'/Component/60.png';
    	$this->gif600 = dirname(__FILE__).'/Component/600.gif';
    	$this->jpg600 = dirname(__FILE__).'/Component/600.jpg';
    	$this->jpg700 = dirname(__FILE__).'/Component/700.jpg';
    	$this->png600 = dirname(__FILE__).'/Component/600.png';
    	$this->png600x8 = dirname(__FILE__).'/Component/600-8.png';
    	$this->png600x24 = dirname(__FILE__).'/Component/600-24.png';
    	
    	$fixUser = new UserFixture();
    	$fixVoice = new VoiceFixture();
    	$fixFollow = new FollowFixture();
    	$this->fix['user1'] = $fixUser->records[0];
    	$this->fix['user2'] = $fixUser->records[1];
    	$this->fix['voice'] = $fixVoice->records[0];
    	$this->fix['voice2'] = $fixVoice->records[1];
    	$this->fix['follow1'] = $fixFollow->records[0];
    	
    	$source = $this->model->getDataSource();
    	$collection = $source->getMongoCollection($this->model);
    	$collection->ensureIndex(array('location' => '2d'));
    	
    	$this->NotificationQueue = ClassRegistry::init('NotificationQueue');
    	$this->User = ClassRegistry::init('User');
    	$this->Follow = ClassRegistry::init('Follow');
    }
    
    public function testAdd() {
        $data = array(
            "user_id" => $this->user_id,
          	"title" => "The second voice",
          	"length" => "180",
          	"status" => Voice::STATUS_PENDING,
          	"isfree" => true,
          	"latitude" => "38.186387",
          	"longitude" => "-96.617203",
          	"language" => "zh_CN",
          	"voice" => "voice_file_key",
          	'cover' => 'cover_file_key',
          	'address' => 'mockup_address',
          	'address_components' => '[]',
          	'description' => 'mockup_description',
          	'tags' => 'one, two, three, four, five',
        );
            
        $result = $this->testAction("/users/{$this->userId}/voices.json?api_key={$this->apikey}&auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $data
        ));
        $vars = $this->vars;
        
        $domain = 'qiniudn.com';
        
        $this->assertEqual(is_array($vars['root']['result']), true);
        $this->assertEqual(isset($vars['root']['result']['user_id']), true);
        $this->assertEqual($vars['root']['result']['user_id'], $this->user_id);
        $this->assertEqual($vars['root']['result']['user']['_id'], $this->user_id);
        $this->assertEqual($vars['root']['result']['user']['username'], $this->username);
        $this->assertEqual(isset($vars['root']['result']['user']['password']), false);
        $this->assertEqual((bool)stristr($vars['root']['result']['cover']['source'], $domain), true);
        $this->assertEqual((bool)stristr($vars['root']['result']['cover']['x80'], $domain), true);
        $this->assertEqual((bool)stristr($vars['root']['result']['cover']['x160'], $domain), true);
        $this->assertEqual((bool)stristr($vars['root']['result']['cover']['x640'], $domain), true);
        $this->assertEqual($vars['root']['result']['location']['lat'], '38.186387');
        $this->assertEqual($vars['root']['result']['location']['lng'], '-96.617203');
        $this->assertEqual(isset($vars['root']['result']['voice']), true);
        $this->assertEqual($vars['root']['result']['checkout_total'], 0);
        $this->assertEqual((bool)$vars['root']['result']['isfree'], true);
        $this->assertEqual($vars['root']['result']['language'], 'zh_CN');
        $this->assertEqual($vars['root']['result']['status'], Voice::STATUS_PENDING);
        $this->assertEqual($vars['root']['result']['score'], 0);
        $this->assertEqual($vars['root']['result']['comment_total'], 0);
        $this->assertEqual($vars['root']['result']['address_components'], array());
        $this->assertEqual($vars['root']['result']['description'], 'mockup_description');
        $this->assertEqual($vars['root']['result']['tags'], array('one', 'two', 'three', 'four', 'five'));
        $this->assertEqual(isset($vars['root']['result']['_id']), true);
        
        // no cover image uploaded...
        $postData = $data;
        unset($postData['cover']);
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
            'method' => 'POST',
            'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'The cover of voice has to upload');
        
        // no voice uploaded...
        $postData = $data;
        unset($postData['voice']);
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'The file of voice must be upload');
        
        // no title supplied...
        $postData = $data;
        unset($postData['title']);
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'The title must supply');
        
        // invalid latitude...
        $postData = $data;
        $postData['latitude'] = 91;
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Invalid latitude');

        $postData = $data;
        $postData['latitude'] = -91;
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Invalid latitude');
        
        // invalid longitude...
        $postData = $data;
        $postData['longitude'] = 181;
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Invalid longitude');

        $postData = $data;
        $postData['longitude'] = -181;
        $this->testAction("/users/{$this->userId}/voices.json?auth_token={$this->userToken}", array(
    		'method' => 'POST',
    		'data' => $postData
        ));
        $this->assertEqual($this->vars['result']['code'], 400);
        $this->assertEqual($this->vars['result']['message'], 'Invalid longitude');
    }
    
    public function testEdit() {
        $voiceId = (string) $this->fix['voice2']['_id'];
        $data = array(
            "user_id" => $this->fix['user2']['_id'],
            "title" => "The second voice",
            "length" => (int) 170,
            "status" => Voice::STATUS_APPROVED,
            "isfree" => true,
            "latitude" => 69.186387,
            "longitude" => -106.617203,
            "language" => "zh_CN",
            "voice" => "voice_file_key",
            'cover' => 'cover_file_key'
        );
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?auth_token={$this->userToken}", array(
            'method' => 'PUT',
            'data' => $data
        ));
        
        $updated = $this->model->findById($voiceId);
        $updated = $updated['Voice'];
        
        $this->assertEqual($result, true);
        $this->assertEqual($updated['_id'], $voiceId);
        $this->assertEqual($updated['user_id'], (string)$data['user_id']);
        $this->assertEqual($updated['title'], $data['title']);
        $this->assertEqual($updated['length'], $data['length']);
        $this->assertEqual($updated['status'], $data['status']);
        $this->assertEqual($updated['isfree'], $data['isfree']);
        $this->assertEqual($updated['language'], $data['language']);
        $this->assertEqual($updated['location']['lat'], $data['latitude']);
        $this->assertEqual($updated['location']['lng'], $data['longitude']);
        $this->assertNotEqual($updated['voice'], $this->fix['voice']['voice']);
        $this->assertNotEqual($updated['cover']['source'], $this->fix['voice']['cover']['source']);
        $this->assertNotEqual($updated['cover']['x80'], $this->fix['voice']['cover']['x80']);
        $this->assertNotEqual($updated['cover']['x160'], $this->fix['voice']['cover']['x160']);
        $this->assertNotEqual($updated['cover']['x640'], $this->fix['voice']['cover']['x640']);
        $this->assertEqual(!empty($updated['voice']), true);
        $this->assertEqual(!empty($updated['cover']['source']), true);
        $this->assertEqual(!empty($updated['cover']['x80']),  true);
        $this->assertEqual(!empty($updated['cover']['x160']), true);
        $this->assertEqual(!empty($updated['cover']['x640']), true);
    }
    
    public function testEditWithNotification() {
        $voiceId = (string) $this->fix['voice2']['_id'];
        $data = array(
            "status" => Voice::STATUS_INVALID,
        );
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?auth_token={$this->userToken}", array(
            'method' => 'PUT',
            'data' => $data
        ));
        $item = $this->NotificationQueue->dequeue();
        $this->assertEqual($result, true);
        $this->assertEqual($item->getUserId(), $this->userId);
    }
    
    public function testEditWithUserVoiceTotal() {
        $voiceId = (string) $this->fix['voice2']['_id'];
        $data = array(
            "status" => Voice::STATUS_APPROVED,
        );
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?auth_token={$this->userToken}", array(
            'method' => 'PUT',
            'data' => $data
        ));
        
        $this->assertEqual($result, true);
        $data = $this->User->findById($this->userId);
        $this->assertEqual($data['User']['voice_total'], $this->fix['user1']['voice_total'] + 1);
    }
    
    public function testEditWithFollowNewPosts() {
        $voiceId = (string) $this->fix['voice2']['_id'];
        $data = array(
            "status" => Voice::STATUS_APPROVED,
        );
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?auth_token={$this->userToken}", array(
            'method' => 'PUT',
            'data' => $data
        ));
        
        $this->assertEqual($result, true);
        $data = $this->Follow->findByFollowerId($this->userId);
        $this->assertEqual($data['Follow']['new_posts'], $this->fix['follow1']['new_posts'] + 1);

        // decrease 1 to news_post
        $voiceId = (string) $this->fix['voice2']['_id'];
        $data = array(
            "status" => Voice::STATUS_UNAVAILABLE,
        );
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?auth_token={$this->userToken}", array(
            'method' => 'PUT',
            'data' => $data
        ));
        
        $this->assertEqual($result, true);
        $data = $this->Follow->findByFollowerId($this->userId);
        $this->assertEqual($data['Follow']['new_posts'], $this->fix['follow1']['new_posts']);
    }
    
    public function testDelete() {
        $voiceId = (string)$this->fix['voice']['_id'];
        $result = $this->testAction("/users/{$this->userId}/voices/{$voiceId}.json?api_key={$this->apikey}&auth_token={$this->userToken}", array(
            'method' => 'DELETE'
        ));
        $this->assertTrue($result);
        $voice = $this->model->findById($voiceId)['Voice'];
        $this->assertTrue($voice['deleted'] == 1);
        
        // Checking new_posts of Follow...
//         $data = $this->Follow->findByFollowerId($this->userId);
//         $this->assertEqual($data['Follow']['new_posts'], $this->fix['follow1']['new_posts'] - 1);
        
        // Checking voice_total of User...
//         $data = $this->User->findById($this->userId);
//         $this->assertEqual($data['User']['voice_total'], $this->fix['user1']['voice_total'] - 1);
    }
    
    public function testView() {
        $owner = 'baohanddd';
        $this->redis->lPush("voices:$owner:bought", $this->voice);
        $this->testAction("/voices/{$this->voice}.json?owner=$owner", array(
        	'method' => 'GET'
        ));
        $this->assertEqual(is_array($this->vars['root']['result']), true);
        $this->assertEqual($this->vars['root']['result']['_id'], $this->voice);
        $this->assertEqual($this->vars['root']['result']['user']['_id'], $this->user_id);
        $this->assertEqual($this->vars['root']['result']['user']['username'], $this->username);
        $this->assertEqual($this->vars['root']['result']['bought'], 1);
        $this->assertEqual(isset($this->vars['root']['result']['user']['password']), false);
        
        $unknown = str_replace('a', 'b', $this->voice);
        $this->testAction("/voices/{$unknown}.json", array(
        	'method' => 'GET'
        ));
        $this->assertEqual($this->vars['result']['code'], 404);
    }
    
    public function testIndex() {
        $domain = 'qiniudn.com';
        $page = 1;
        $limit = 20;
        $owner = 'baohanddd';
        
        $this->testAction("/voices.json?page=$page&limit=$limit&latitude={$this->latitude}&longitude={$this->longitude}"."&owner=".$owner);
        $this->assertEqual(count($this->vars['root']['result']['items']), 2);
        $this->assertEqual($this->vars['root']['result']['total'], 2);
        $this->assertEqual($this->vars['root']['result']['items'][0]['bought'], 0);
        $this->assertEqual($this->vars['root']['result']['items'][0]['user']['_id'], $this->user_id);
        $this->assertEqual($this->vars['root']['result']['items'][0]['user']['username'], $this->username);
        $this->assertEqual(isset($this->vars['root']['result']['items'][0]['user']['password']), false);
        $this->assertEqual((bool)stristr($this->vars['root']['result']['items'][0]['cover']['source'],
                $domain), true);
    }
    
    public function testIndexWithKeyword() {
        $domain = 'qiniudn.com';
        $page = 1;
        $limit = 20;
        $owner = 'baohanddd';
        $keyword = 'good';
        
        $this->testAction("/voices.json?page=$page&limit=$limit&keyword={$keyword}&latitude={$this->latitude}&longitude={$this->longitude}"."&owner=".$owner, 
            array('method' => 'GET'));
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        
        $keyword = 'first';
        $this->testAction("/voices.json?page=$page&limit=$limit&keyword={$keyword}&latitude={$this->latitude}&longitude={$this->longitude}"."&owner=".$owner, 
            array('method' => 'GET'));
        $this->assertEqual(count($this->vars['root']['result']['items']), 1);
        $this->assertEqual($this->vars['root']['result']['total'], 1);
        
        $keyword = 'unknown';
        $this->testAction("/voices.json?page=$page&limit=$limit&keyword={$keyword}&latitude={$this->latitude}&longitude={$this->longitude}"."&owner=".$owner, 
            array('method' => 'GET'));
        $this->assertEqual(count($this->vars['root']['result']['items']), 0);
        $this->assertEqual($this->vars['root']['result']['total'], 0);
    }
}