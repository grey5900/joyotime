<?php
APP::uses('AppTestCase', 'Test/Case/Model');

/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class VoiceTest extends AppTestCase {
    
    public $fixtures = array(
        'app.voice',
        'app.user',
    );
    
    private $fix;
    
    public function getModelName() {
        return 'Voice';
    }
    
    public function setUp() {
        parent::setUp();
        
        $fix = new VoiceFixture();
        $this->fix['voice1'] = $fix->records[0];
        $this->fix['voice2'] = $fix->records[1];
        
        $fix = new UserFixture();
        $this->fix['user1'] = $fix->records[0];
        $this->fix['user2'] = $fix->records[1];
        
        $this->voice_id = (string) $this->fix['voice1']['_id'];
        
        $this->tag = ClassRegistry::init('Tag');
        $this->model->getEventManager()->attach($this->tag);
    }
    
    public function testSave() {
        $userId = (string) $this->fix['user1']['_id'];
        $title = 'mockup_title';
        $cover = 'mockup_cover_path';
        $length = '180';
        $voice = 'mockup_voice_path';
        $status = '3';
        $isfree = '0';
        $latitude = -16.29334;
        $longitude = 69.75362;
        $address = 'mockup_address';
        $address_components = '[{"long_name":"479号-785号","short_name":"479号-785号","types":["street_number"]},{"long_name":"天府大道中段","short_name":"天府大道中段","types":["route"]},{"long_name":"武侯区","short_name":"武侯区","types":["sublocality","political"]},{"long_name":"成都","short_name":"成都","types":["locality","political"]},{"long_name":"四川省","short_name":"四川省","types":["administrative_area_level_1","political"]},{"long_name":"中国","short_name":"CN","types":["country","political"]}]';
        $language = 'zh_CN';
        $description = 'mockup_description';
        $checkout_total = '1';
        $tags = 'one, two,    three,     four,  five, ';
        
        $result = $this->model->save(array(
            'user_id' => $userId,
            'title' => $title,
            'cover' => $cover,
            'length' => $length,
            'voice' => $voice,
            'status' => $status,
            'isfree' => $isfree,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'address_components' => $address_components,
            'language' => $language,
            'comment_total' => 0,
            'score' => 5,
            'description' => $description,
            'tags' => $tags
        ));
        
        $voice = $result['Voice'];
        $this->assertEqual((boolean)$result, true);
        $this->assertEqual(isset($voice['_id']), true);
        $this->assertEqual(isset($voice['short_id']), true);
        $this->assertEqual(strlen($voice['short_id']), 6);
        $this->assertEqual($voice['description'], $description);
        $this->assertEqual($voice['status'] === Voice::STATUS_PENDING, true);
        $this->assertEqual($voice['isfree'] === (int) $isfree, true);
        $this->assertEqual($voice['checkout_total'] === 0, true);
        $this->assertEqual($voice['length'] === (int) $length, true);
        $location = array_values($voice['location']);
        $this->assertEqual($location[0], $latitude);
        $this->assertEqual($location[1], $longitude);
        $this->assertEqual($voice['location']['lat'], $latitude);
        $this->assertEqual($voice['location']['lng'], $longitude);
        $this->assertEqual($voice['tags'], array('one', 'two', 'three', 'four', 'five'));
        $this->assertEqual(isset($voice['cover']['source']), true);
        $this->assertEqual(isset($voice['cover']['x80']), true);
        $this->assertEqual(isset($voice['cover']['x160']), true);
        $this->assertEqual(isset($voice['cover']['x640']), true);
        $this->assertEqual($voice['address'], $address);
        $this->assertEqual(isset($voice['address_components']['country']), true);
        $this->assertEqual(isset($voice['address_components']['administrative_area_level_1']), true);
        $this->assertEqual(isset($voice['address_components']['locality']), true);
        $this->assertEqual(isset($voice['address_components']['sublocality']), true);
        $this->assertEqual(isset($voice['address_components']['route']), true);
        $this->assertEqual(isset($voice['address_components']['street_number']), true);
        $this->assertEqual(isset($voice['latitude']), false);
        $this->assertEqual(isset($voice['longitude']), false);
        
        // checking tag...
        $tags = $this->tag->findByVoice($voice['_id']);
        $tags = Hash::extract($tags, '{n}.Tag.title');
        foreach($voice['tags'] as $tag) {
            $this->assertEqual(in_array($tag, $tags), true);
        }
    }
    
    public function testSaveAssertFail() {
        $userId = (string) $this->fix['user1']['_id'];
        $title = 'mockup_title';
        $cover = 'mockup_cover_path';
        $length = '180';
        $voice = 'mockup_voice_path';
        $status = '3';
        $isfree = '0';
        $latitude = -16.29334;
        $longitude = 69.75362;
        $address = 'mockup_address';
        $address_components = '[{"long_name":"479号-785号","short_name":"479号-785号","types":["street_number"]},{"long_name":"天府大道中段","short_name":"天府大道中段","types":["route"]},{"long_name":"武侯区","short_name":"武侯区","types":["sublocality","political"]},{"long_name":"成都","short_name":"成都","types":["locality","political"]},{"long_name":"四川省","short_name":"四川省","types":["administrative_area_level_1","political"]},{"long_name":"中国","short_name":"CN","types":["country","political"]}]';
        $language = 'zh_CN';
        $description = 'mockup_description';
        $checkout_total = '1';
        
        $result = $this->model->save(array(
            'user_id' => $userId,
        ));
        $this->assertEqual((boolean)$result, false);
    }
    
    public function testEdit() {
        $checkoutTotal = '2';
        $commentTotal = '9';
        $score = '3.2';
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice1']['_id'],
    		'title' => 'Good!!!',
    		'isfree' => (string) 2,
    		'status' => (string) Voice::STATUS_UNAVAILABLE,
    	    'score' => (string) 3.2,
    		'checkout_total' => $checkoutTotal,
    		'comment_total' => $commentTotal,
    	    'tags' => 'haha, hehe',
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($saved['Voice']['title'], 'Good!!!');
    	$this->assertEqual($saved['Voice']['status'] === Voice::STATUS_UNAVAILABLE, true);
    	$this->assertEqual($saved['Voice']['checkout_total'] === (int)$checkoutTotal, true);
    	$this->assertEqual($saved['Voice']['comment_total'] === (int)$commentTotal, true);
    	$this->assertEqual($saved['Voice']['score'] === (float) $score, true);
    	$this->assertEqual($saved['Voice']['isfree'] === 1, true);
    	
    	// checking tag...
    	$tags = $this->tag->findByVoice($saved['Voice']['_id']);
    	$tags = Hash::extract($tags, '{n}.Tag.title');
    	foreach($saved['Voice']['tags'] as $tag) {
    		$this->assertEqual(in_array($tag, $tags), true);
    	}
    	
    	$saved = $this->model->save(array(
			'_id' => (string) $this->fix['voice1']['_id'],
			'comment' => '',
    	));
    	$this->assertEqual((bool)$saved, false);
    }
    
    public function testEditWithStatusTransferPresentation() {
        // The original voice status is approved...
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice1']['_id'],
    		'status' => Voice::STATUS_UNAVAILABLE,
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($this->model->getStatusTransferPresentation(), Voice::FROM_APPROVED_TO_UNAVAILABLE);
    	
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice1']['_id'],
    		'status' => Voice::STATUS_APPROVED,
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($this->model->getStatusTransferPresentation(), Voice::FROM_UNAVAILABLE_TO_APPROVED);
    	
    	// The original voice status is pending...
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice2']['_id'],
    		'status' => Voice::STATUS_INVALID,
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($this->model->getStatusTransferPresentation(), Voice::FROM_PENDING_TO_INVALID);
    	
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice2']['_id'],
    		'status' => Voice::STATUS_APPROVED,
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($this->model->getStatusTransferPresentation(), Voice::FROM_INVALID_TO_APPROVED);
    	
    	// Reset voice status to pending...
    	$saved = $this->model->save(array(
    		'_id' => (string) $this->fix['voice2']['_id'],
    		'status' => Voice::STATUS_PENDING,
    	));
    	$this->assertEqual((bool)$saved, true);
    	
    	$saved = $this->model->save(array(
			'_id' => (string) $this->fix['voice2']['_id'],
			'status' => Voice::STATUS_APPROVED,
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($this->model->getStatusTransferPresentation(), Voice::FROM_PENDING_TO_APPROVED);
    }
    
    public function testEditWithPrimaryKey() {
    	$this->model->id = (string) $this->fix['voice1']['_id'];
    	$saved = $this->model->save(array(
    		'title' => 'Good!!!',
    		'status' => Voice::STATUS_UNAVAILABLE
    	));
    	$this->assertEqual((bool)$saved, true);
    	$this->assertEqual($saved['Voice']['title'], 'Good!!!');
    	$this->assertEqual($saved['Voice']['status'], Voice::STATUS_UNAVAILABLE);
    }
    
    public function testUpdateByComment() {
        $result = $this->model->updateByComment($this->voice_id, 5, 1);
        $this->assertEqual($result['comment_total'], 1);
        $this->assertEqual($result['score'], 5);
        $result = $this->model->updateByComment($this->voice_id, 4, 1);
        $this->assertEqual($result['comment_total'], 2);
        $this->assertEqual($result['score'], 4.5);
        $result = $this->model->updateByComment($this->voice_id, 5, -1);
        $this->assertEqual($result['comment_total'], 1);
        $this->assertEqual($result['score'], 4);
        $result = $this->model->updateByComment($this->voice_id, 4, -1);
        $this->assertEqual($result['comment_total'], 0);
        $this->assertEqual($result['score'], 0);
    }
    
    public function testUpdateEarnTotal() {
        $result = $this->model->updateEarnTotal($this->voice_id, 10);
        $this->assertEqual($result, true);
        $result = $this->model->updateEarnTotal($this->voice_id, 60);
        $this->assertEqual($result, true);
        $voice = $this->model->findById($this->voice_id);
        $this->assertEqual($voice['Voice']['earn_total'], 70);
    }
    
    public function testUpdateCheckoutTotal() {
        $result = $this->model->updateCheckoutTotal($this->voice_id);
        $this->assertEqual($result, true);
        $result = $this->model->updateCheckoutTotal($this->voice_id);
        $this->assertEqual($result, true);
        $voice = $this->model->findById($this->voice_id);
        $this->assertEqual($voice['Voice']['checkout_total'], 2);
    }
}