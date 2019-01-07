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

class NotificationCounter extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'NotificationCounter';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * notification:$userId:count
 * 
 * @var string
 */
    private $key = 'notification:%s:count';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
/**
 * Get key of dataset
 *
 * @param string $userId
 * @return string
 */
    public function key($userId) {
    	return sprintf($this->key, $userId);
    }
    
/**
 * Check whether the user has new arrival notification or not.
 *
 * @param string $userId
 * @return int The count of new message
 */
    public function count($userId) {
    	return $this->redis->get($this->key($userId));
    }
    
/**
 * Increse 1 for new message
 *
 * @param string $userId
 * @return int The count of new message
 */
    public function incr($userId) {
    	return $this->redis->incr($this->key($userId));
    }
    
/**
 * Reset the counter to zero
 *
 * @param string $userId
 * @return boolean
 */
    public function clean($userId) {
    	return $this->redis->set($this->key($userId), 0);
    }
}