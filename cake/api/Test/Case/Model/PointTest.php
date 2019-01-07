<?php
APP::uses('AppTestCase', 'Test/Case/Model');
APP::uses('Point', 'Model');

/**
 * @package       app.Test.Case.Model
 */
class PointTest extends AppTestCase {
    
    public $fixtures = array(
        'app.voice',
//         'app.user',
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
        
        $this->p1 = new Point($this->fix['voice1']['location']['lng'], $this->fix['voice1']['location']['lat']);
        $this->p2 = new Point($this->fix['voice2']['location']['lng'], $this->fix['voice2']['location']['lat']);
    }
    
    public function testGetDistance() {
        $pot = new Point(-96.617203, 38.186387);
        $dis = $pot->distance($this->p1);
        debug($dis);
        $dis = $pot->distance($this->p2);
        debug($dis);
    }
    
    public function testGetDistanceFlat() {
        $pot = new Point(-96.617203, 38.186387);
        $dis = $pot->distanceFlat($this->p1);
        debug($dis);
        $dis = $pot->distanceFlat($this->p2);
        debug($dis);
    }
    
    public function testGetDistanceFlat2() {
//         高雄(22.38,120.17)台北(25.03,121.30)
        $pot1 = new Point(120.17, 22.38);
        $pot2 = new Point(121.30, 25.03);
        $dis = $pot1->distanceFlat($pot2);
        debug($dis);
    }
    
    public function tearDown() {
        parent::tearDown();
    }
}