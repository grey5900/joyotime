<?php
APP::uses('CredentialItem', 'Item');

abstract class AppControllerTestCase extends ControllerTestCase {
    
/**
 * @var Controller
 */
    public $controller;
/**
 * @var Model
 */
    public $model;
/**
 * @var string
 */
    protected $apikey;
/**
 * @var Redis
 */
    public $redis;
    
/**
 * The auth token for admin
 * 
 * @var string
 */
    public $adminToken = '';
    
    public $admin = '';
    
/**
 * The auth token for user
 * 
 * @var string
 */
    public $userToken = '';

/**
 * Prepared userId
 * 
 * @var string
 */
    public $userId = '';
    
    /**
     * @var AuthorizeToken
     */
    protected $AuthorizeToken;
    
/**
 * Get model name
 * @return string
 */
    abstract function getModelName();

/**
 * Get controller name
 * @return string
 */
    abstract function getControllerName();
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::setUp()
 */
    public function setUp() {
    	parent::setUp();
    	
    	$this->controller = $this->getMockController();
    	
    	$this->AuthorizeToken = ClassRegistry::init('AuthorizeToken');
    	$this->redis = $this->AuthorizeToken->getDatasource()->getInstance();
    	
    	if($this->getModelName()) {
    	    $this->model = ClassRegistry::init($this->getModelName());
    	}
    	$this->apikey = '362edb126af7cacbae0d20051cbb2e76';
    	/*
    	 * Prepare authorize information...
    	 */
    	$this->admin = '51f0c30f6f159aec6fad8ce4';
    	$this->userId = '51f0c30f6f159aec6fad8ce3';
    	
    	$this->adminToken = $this->AuthorizeToken->add($this->getMockAdmin());
    	$this->userToken = $this->AuthorizeToken->add($this->getMockUser());
    }
    
    private function getMockAdmin() {
    	$item = new CredentialItem(array(
    		'_id' => $this->admin,
    		'role' => User::ROLE_ADMIN
    	));
    	return $item;
    }
    
    private function getMockUser() {
    	$item = new CredentialItem(array(
    		'_id' => $this->userId,
    		'role' => User::ROLE_USER
    	));
    	return $item;
    }
    
/**
 * Helper method to get mock object of
 * controller more easier.
 *
 * @param string $model the name of model
 *
 * @return Controller
 */
    public function getMockController() {
    	$controller = $this->generate($this->getControllerName());
    	$controller->constructClasses();
    	$controller->Components->init($controller);
    	return $controller;
    }
    
/**
 * (non-PHPdoc)
 * @see CakeTestCase::tearDown()
 */
    public function tearDown() {
        if($this->model) {
    	    $this->dropData($this->model);
        }
    	ClassRegistry::flush();
    	unset($this->controller);
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
            $db = $model->getDataSource()->getMongoDb();
            foreach($db->listCollections() as $collection) {
                $response = $collection->remove();
            }
            $this->redis->del($this->redis->keys('*'));
        } catch(MongoException $e) {
            trigger_error($e->getMessage());
        }
    }
}