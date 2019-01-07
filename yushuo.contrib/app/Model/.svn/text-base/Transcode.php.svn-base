<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('AppModel', 'Model');

/**
 * @package app.Model
 */
class Transcode extends AppModel {

    const STATUS_PENDING = 100;
    const STATUS_FAIL  = 111;
    
    public $name = 'Transcode';
    
/**
 * @var Redis
 */
    protected $redis;
    
/**
 * %s: user_id
 * @var string
 */
    private $key = 'transcodes:%s';
    
/**
 * @var string
 */
    private $userId = '';
    
    public function __construct($id = false, $table = null, $ds = null) {
    	parent::__construct($id, $table, $ds);
    	$this->redis = $this->getDataSource()->getInstance();
    	$this->userId = CakeSession::read('Auth.User._id');
    }

/**
 * (non-PHPdoc)
 * @see Model::save()
 */
    public function save($data = null, $validate = true, $fieldList = array()) {
        if(!isset($data['transcode']['uniqid'])) {
            throw new CakeException(__('Miss found `uniqid` within data in Transcode::save()'));
        }
    	return $this->redis->hSet($this->key(), $data['transcode']['uniqid'], serialize($data));
    }
    
/**
 * (non-PHPdoc)
 * @see Model::delete()
 */
    public function delete($id = null, $cascade = true) {
        return $this->redis->hDel($this->key(), $id);
    }
    
/**
 * (non-PHPdoc)
 * @see Model::find()
 */
    public function find($type = 'first', $query = array()) {
        $rows = $this->redis->hGetAll($this->key());
        foreach($rows as $id => &$item) {
            $item = unserialize($item);
        }
        return $rows;
    }
    
/**
 * Set update
 * 
 * @param string $id
 * @param int $status
 * @return boolean
 */
    public function setStatus($id, $status) {
        $item = $this->redis->hGet($this->key(), $id);
        if($item) {
            $item = unserialize($item);
            $item['transcode']['status'] = $status;
            return $this->save($item);
        }        
        return false;
    }
    
    private function key() {
        return sprintf($this->key, $this->userId);
    }
}