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

/**
 * @package		app.Model
 */
class Error extends AppModel {
	
	public $useDbConfig = 'redis'; // Defined at app/Config/database.php
	
	public $name = 'Error';
	
	private $keyQueue = 'errors';
	private $keyHash  = 'unique_error';
	
/**
 * @var Redis
 */
	protected $redis;
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    $this->redis = $this->getDataSource()->getInstance();
	}
	
/**
 * Push an item into queue
 *
 * @param Array $save
 * @return boolean
 */
	public function enqueue(array $save) {
	    $count = 0;
	    $subject = $this->getSubject($save);
	    if($subject && ($count = $this->getCount($subject))) {
	        return $this->updateCount($subject, ++$count);
	    } else {
	        $this->addHash($subject);
		    return $this->redis->lPush($this->keyQueue, json_encode($save));
	    }
	}
	
/**
 * Pop an Array from queue
 * 
 * @return mixed|boolean
 */
	public function dequeue() {
	    $save = $this->redis->rPop($this->keyQueue);
	    return json_decode($save, TRUE);
	}
	
/**
 * Use first line of message as subject
 * 
 * @param array $save
 * @return string
 *     The subject might empty
 */
	private function getSubject(array $save) {
	    $subject = '';
	    if(isset($save['message'])) {
	        $msgs = explode("\n", $save['message']);
	        if(count($msgs) > 0) {
	            $subject = $msgs[0];
	        }
	    }
	    return $subject;
	}
	
/**
 * Get count number for $subject
 * 
 * @param string $subject
 * @return int
 */
	private function getCount($subject) {
	    return $this->redis->hGet($this->keyHash, $subject);
	}
	
/**
 * Add subject into Hash
 * Initial count number is 1 for item
 * 
 * @param string $subject
 * @return boolean
 */
	private function addHash($subject) {
	    $count = 1;    // initial count number...
	    return $this->redis->hSet($this->keyHash, $subject, $count);
	}
	
/**
 * Update count number for item
 * 
 * @param string $subject
 * @param number $count
 * @return boolean
 */
	private function updateCount($subject, $count) {
	    return $this->redis->hSet($this->keyHash, $subject, $count);
	}
}