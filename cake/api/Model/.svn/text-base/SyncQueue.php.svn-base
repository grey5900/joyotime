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

class SyncQueue extends AppModel {
    
    public $useDbConfig = 'redis'; // Defined at app/Config/database.php
    
    public $name = 'SyncQueue';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * @var string
 */
    private $key = 'sync';
    
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->redis = $this->getDataSource()->getInstance();
    }
    
/**
 * Push an item into queue
 *
 * @param array $item
 * @return boolean
 */
    public function enqueue(array $item) {
    	return $this->redis->lPush($this->key, json_encode($item));
    }
    
/**
 * Get an first item of queue
 *
 * @return boolean it will return `false` if nothing found
 */
    public function dequeue() {
    	$item = $this->redis->rPop($this->key);
    	return json_decode($item, TRUE);
    }
}