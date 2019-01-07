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
APP::uses('AppModel', 'Model');
/**
 * The Package of voice
 *
 * @package		app.Model
 */
class Coupon extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Coupon';
	
	public $mongoSchema = array(
	    'codes'  => array(
	        array(
	            'code'    => array('type' => 'string'), 
	            'status'  => array('type' => 'integer'),
	            'user_id' => array('type' => 'string'),
	            'used'    => array('type' => 'integer')
	        )
	    ),
	    'number' => array('type' => 'integer'),
	    'used'   => array('type' => 'integer'),
	    'length' => array('type' => 'integer'),
	    'expire' => array('type' => 'integer'),
	    'description' => array('type' => 'string'),
	    'deleted'  => array('type' => 'integer'),    // 0: not deleted yet, 1: deleted already
	    'created'  => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime')
	);
	
	const STATUS_PENDING = 20100;
	const STATUS_USED    = 20101;
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
		    'length' => array(
		        'format' => array(
	        		'rule' => 'formatInt',
	        		'required' => 'create',
		            'message' => __('Invalid length')
		        )
		    ),
		    'expire' => array(
		        'format' => array(
	        		'rule' => 'formatInt',
	        		'required' => 'create',
		            'message' => __('Invalid expire')
		        )
		    ),
		    'number' => array(
		        'format' => array(
	        		'rule' => 'formatInt',
	        		'required' => 'create',
		            'message' => __('Invalid number')
		        )
		    )
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Model::implementedEvents()
	 */
// 	public function implementedEvents() {
// 		$callbacks = parent::implementedEvents();
// 		return array_merge($callbacks, array(
// 			'Model.Favorite.createByPackage' => 'updateFavoriteTotal',
// 		));
// 	}
	
	
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
	public function beforeSave($options = array()) {
	    $id = $this->isUpdate();
	    
	    if(!$id) {    // It's creating...
	        $this->data[$this->name]['codes']  = $this->geneBatch($this->data[$this->name]['number']);
	        $this->data[$this->name]['expire'] = (int)$this->data[$this->name]['expire'];
	        $this->data[$this->name]['length'] = (int)$this->data[$this->name]['length'];
	        $this->data[$this->name]['number'] = (int)$this->data[$this->name]['number'];
	        $this->data[$this->name]['used']   = 0;
	    } else {
	        if(isset($this->data[$this->name]['expire'])) 
	            $this->data[$this->name]['expire'] = (int)$this->data[$this->name]['expire'];
	    }
	}
	
	/**
	 * 
	 * @param string $code
	 * @return number
	 */
    public function available($code, $userId) {
        $row = $this->find('first', array(
    		'modify' => array(
//     		    '$inc' => array('used' => 1),
				'codes.$.status'  => self::STATUS_USED,
    		    'codes.$.user_id' => $userId,
    		    'codes.$.used'    => time(),
    		),
    		'conditions' => array(
    		    'codes' => array('$elemMatch' => array(
    		        'code' => $code,
    		        'status' => self::STATUS_PENDING
    		    )),
    		    'expire'  => array('$gt' => time()),
    		    'deleted' => array('$ne' => 1)
    		)
        ));
        return isset($row[$this->name]['length']) ? $row[$this->name]['length'] : 0;
    }
    
    private function geneBatch($num) {
        $now = time();
        $codes = array();
        for($i = 0; $i < $num; $i++) 
            $codes[] = array('code' => $this->geneCode($now), 'status' => self::STATUS_PENDING);
        return $codes;
    }
	
    private function geneCode($now) {
        $now.= uniqid();
        return md5(sha1($now));
    }
    
/**
 * 
 * @param array $check
 * @return boolean
 */
    public function formatInt($check) {
        foreach($check as $key => $val) {
            $val = (int) $val;
            if($val <= 0) return false;
            $this->data[$this->name][$key] = $val;
            return true;
        }
        return false;
    }
    
    
}