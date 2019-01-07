<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class NotificationQueue extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'NotificationQueue';
    
/**
 * @var Redis
 */
    protected $redis;
    
    private $keyQueue = 'push_notice:items';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
        
        $this->validate = array(
            'user_id' => array(
	            'required' => array(
    				'rule' => array('isMongoId'),
    				'allowEmpty' => false,
    				'message' => __('Invalid user id supplied')
        		),
	        ),
            'message' => array(
	            'required' => array(
    				'rule' => 'notEmpty',
    				'required' => 'create',
    				'allowEmpty' => true,
    				'message' => __('Invalid message supplied')
        		),
	        ),
            'badge' => array(
	            'required' => array(
    				'rule' => array('naturalNumber', true),	// Zero is valid
    				'required' => false,
    				'allowEmpty' => false,
    				'message' => __('Invalid badge supplied')
        		),
	        )
        );
        
    }
    
    /**
     * Push an item into queue
     * 
     * @param array $item
     * @return boolean
     */
    public function enqueue(array $item) {
        $this->create();
        $this->set($item);
        if($this->validates()) {
            return $this->redis->lPush($this->keyQueue, json_encode($item));
        }
        return false;
    }
    
    /**
     * Get an first item of queue
     * 
     * @return boolean it will return `false` if nothing found
     */
    public function dequeue() {
        $item = $this->redis->rPop($this->keyQueue);
        return json_decode($item, TRUE);
    }
}