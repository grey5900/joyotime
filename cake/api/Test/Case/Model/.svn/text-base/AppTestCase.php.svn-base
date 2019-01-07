<?php
/**
 * The class is parent of all model testcase.
 * 
 * @package       app.Test.Case.Model
 */
abstract class AppTestCase extends CakeTestCase {
    
/**
 * @var Model
 */
    public $model;
    
/**
 * Get model name
 * @return string
 */
    abstract function getModelName();
    
    /**
     * @return array
     */
    public function getData() {
    	return array();
    }
    
    /**
     * Get data for testing
     */
    public function data($replace = array(), $except = '') {
    	$data = $this->getData();
    	if($data) {
    		if($except) {
    			unset($data[$except]);
    		}
    		if($replace) {
    			$data = array_merge($data, $replace);
    		}
    	}
    	return $data;
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	$this->model = ClassRegistry::init($this->getModelName());
    	$this->AuthorizeToken = ClassRegistry::init('AuthorizeToken');
    	$this->redis = $this->AuthorizeToken->getDatasource()->getInstance();
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::tearDown()
 */
    public function tearDown() {
    	$this->dropData($this->model);
    	ClassRegistry::flush();
    	parent::tearDown();
    }

/**
 * Drop database
 *
 * @return void
 * @access public
 */
    public function dropData(Model $model) {
        try {
            $ds = $model->getDataSource();
            if(method_exists($ds, 'getMongoDb')) {
                $db = $ds->getMongoDb();
                foreach($db->listCollections() as $collection) {
                	$response = $collection->remove();
                }
            } elseif(method_exists($ds, 'getInstance')) {
                // It's redis...
                $db = $ds->getInstance();
                $db->del($db->keys('*'));
            }
            $this->redis->del($this->redis->keys('*'));
        } catch(MongoException $e) {
            trigger_error($e->getMessage());
        }
    }
}