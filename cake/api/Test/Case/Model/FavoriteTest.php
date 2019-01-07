<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('UpdaterComponent', 'Controller/Component');
APP::uses('Controller', 'Controller');

/**
 * The user model test class
 *
 * @package       app.Test.Case.Model
 */
class FavoriteTest extends AppTestCase {
    
    public $fixtures = array(
        'app.favorite',
        'app.user',
        'app.voice',
    );
    
    /**
     * @var string
     */
    private $userId;
    
    public function getModelName() {
        return 'Favorite';
    }
    
    public function setUp() {
        parent::setUp();
        
        $this->id = '55f0c30f6f159aec6fad8ce3';
        $this->userId = '51f0c30f6f159aec6fad8ce3';
        $this->title = 'The first favorite';
        $this->title2 = 'The second favorite';
        $this->voice = '51f225e26f159afa43e76aff';
        $this->voice2 = '51f225e26f159afa43e76afe';
        $this->voice3 = '51f225e26f159afa43e76afd';
        $this->voice4 = '51f225e26f159afa43e76afc';
        $this->voice5 = '51f225e26f159afa43e76afb';
        $this->voice6 = '51f225e26f159afa43e76afa';
        
        $this->cover = 'mockup_cover_name';
        
        $this->User = ClassRegistry::init('User');
    }
    
    public function getData() {
        return array(
            'title' => 'new favorite',
            'user_id' => $this->userId
        );
    }
    
    public function testSave() {
        $fav = $this->model->save($this->data(array('voices' => 'invalid')))['Favorite'];
        $this->assertEqual((bool)$fav, false);
        $this->model->create();
        $fav = $this->model->save($this->data())['Favorite'];
        $this->assertEqual($fav['user_id'], $this->userId);
        $this->assertEqual($fav['title'], 'new favorite');
        $this->assertEqual($fav['size'], 0);
        $this->assertEqual($fav['isdefault'], 0);
        $this->assertEqual($fav['voices'], array());
        $this->assertEqual($fav['thumbnail'], array());
        $this->assertEqual(isset($fav['created']), true);
        $this->assertEqual(isset($fav['modified']), true);
    }
    
    public function testGetFavoriteId() {
        $id = $this->model->getFavoriteId($this->userId, $this->title2);
        $this->assertEqual($id == true, true);
        $id = $this->model->getFavoriteId($this->userId, $this->title);
        $this->assertEqual($id, $this->id);
    }
    
    public function testIsDefault() {
        $result = $this->model->isDefault($this->id);
        $this->assertEqual($result, true);
    }
    
    public function testCreateDefaultFavorite() {
        $this->User->read(null, $this->userId);
        $event = new CakeEvent('mock event', $this->User);
        $result = $this->model->createDefault($event);
        $this->assertEqual($result['Favorite']['isdefault'], 1);
        $this->assertEqual($result['Favorite']['title'], Favorite::DEFAULT_FAVORITE_TITLE);
        $this->assertEqual($result['Favorite']['user_id'], $this->userId);
    }
    
    public function testPush() {
        $result = $this->model->push($this->id, $this->voice, $this->cover);
        $this->assertEqual($result, true);
        $fav = $this->model->find('first', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'title' => $this->title,
            )
        ));
        $this->assertEqual($fav['Favorite']['user_id'], $this->userId);
        $this->assertEqual($fav['Favorite']['title'], $this->title);
        $this->assertEqual($fav['Favorite']['size'], 1);
        $this->assertEqual($fav['Favorite']['thumbnail'][0], $this->cover);
        $this->assertEqual(count($fav['Favorite']['voices']), 1);
    }
    
    public function testPull() {
        $result = $this->model->push($this->id, $this->voice);
        $result = $this->model->pull($this->id, 'unknown');
        $this->assertEqual($result, false);
        $result = $this->model->pull($this->id, $this->voice);
        $this->assertEqual($result, true);
        $fav = $this->model->find('first', array(
            'conditions' => array(
                'user_id' => $this->userId,
                'title' => $this->title
            )
        ));
        $this->assertEqual($fav['Favorite']['user_id'], $this->userId);
        $this->assertEqual($fav['Favorite']['title'], $this->title);
        $this->assertEqual($fav['Favorite']['size'], 0);
        $this->assertEqual($fav['Favorite']['voices'], array());
        $this->assertEqual($fav['Favorite']['thumbnail'], array());
    }
    
    public function testIsExist() {
        $id = $this->model->isExist($this->userId, $this->title);
        $this->assertEqual($id, $this->id);
        $id = $this->model->isExist($this->userId, $this->title2);
        $this->assertEqual($id, false);
    }
    
    public function testGetVoices() {
        $result = $this->model->push($this->id, $this->voice);
        $result = $this->model->push($this->id, $this->voice2);
        $result = $this->model->push($this->id, $this->voice3);
        $result = $this->model->push($this->id, $this->voice4);
        $result = $this->model->push($this->id, $this->voice5);
        $result = $this->model->push($this->id, $this->voice6);
        $fav = $this->model->getVoices($this->id, 1, 2);
        $this->assertEqual($fav['Favorite']['voices'][0], $this->voice);
        $this->assertEqual($fav['Favorite']['voices'][1], $this->voice2);
        $fav = $this->model->getVoices($this->id, 2, 2);
        $this->assertEqual($fav['Favorite']['voices'][0], $this->voice3);
        $this->assertEqual($fav['Favorite']['voices'][1], $this->voice4);
        $fav = $this->model->getVoices($this->id, 3, 2);
        $this->assertEqual($fav['Favorite']['voices'][0], $this->voice5);
        $this->assertEqual($fav['Favorite']['voices'][1], $this->voice6);
        $fav = $this->model->getVoices($this->id, 4, 2);
        $this->assertEqual($fav['Favorite']['voices'], array());
    }
}