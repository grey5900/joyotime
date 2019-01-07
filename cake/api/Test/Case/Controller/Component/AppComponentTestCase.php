<?php
APP::uses('Controller', 'Controller');

abstract class AppComponentTestCase extends CakeTestCase {
    
/**
 * @var Redis
 */
    public $redis;
    
/**
 * @var AuthorizeToken
 */
    protected $AuthorizeToken;
    
/**
 * Get component name
 * 
 * @return string
 */
    abstract function getComponentName();
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$this->AuthorizeToken = ClassRegistry::init('AuthorizeToken');
    	$this->redis = $this->AuthorizeToken->getDatasource()->getInstance();
    }
    
/**
 * @return Component
 */
    public function getComponent() {
        // Setup our component and fake test controller
        $componentClass = $this->getComponentName();
        APP::uses($componentClass, 'Controller/Component');
        $Collection = new ComponentCollection();
        $component = new $componentClass($Collection);
         
        $this->Controller = new TestComponentController(new CakeRequest(), new CakeResponse());
        $this->Controller->Components->init($this->Controller);
        $component->startup($this->Controller);
        return $component;
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::tearDown()
 */
    public function tearDown() {
        $this->redis->del($this->redis->keys('*'));
    	ClassRegistry::flush();
    	unset($this->controller);
    	parent::tearDown();
    }
}

class TestComponentController extends Controller {

}