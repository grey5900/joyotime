<?php
APP::uses('AppModel', 'Model');
APP::uses('Security', 'Utility');
APP::uses('Validation', 'Utility');
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
 * @copyright Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link http://fishsaying.com FishSaying(tm) Project
 * @since FishSaying(tm) v 0.0.1
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 *
 * @package app.Model
 */
class Partner extends AppModel {
	var $useDbConfig = 'fishsaying_partner';
	public $primaryKey = '_id';
	public $name = 'Partner';
	public $mongoSchema = array(
		'name'=>array(
			'type'=>'string' 
		), 
		'user_id'=>array(), 
		'user_total' => array('type' => 'integer'),
		'created'=>array(
			'type'=>'datetime' 
		), 
		'modified'=>array(
			'type'=>'datetime' 
		) 
	);
	public function paginate() {
		return $this->results;
	}
	
	public function paginateCount() {
		return $this->count;
	}
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'name'=>array(
				'require'=>array(
					'rule'=>'notEmpty', 
					'required'=>'create', 
					'message'=>__('Invalid title') 
				) 
			) 
		)
		;
	}
	
	/**
	 * (non-PHPdoc)
	 * 
	 * @see Model::beforeSave()
	 */
	public function beforeSave($options = array()) {
		// $id = $this->isUpdate();
		// if(!$id) {
		$this->data[$this->name]['user_id'] = array();
		$this->data[$this->name]['user_total'] = 0;
		// }
	

	}
	/**
	 * 增加关联用户
	 * @param string $partnerId
	 * @param array $user
	 * @return boolean
	 */
	public function push($partnerId, array $user) {
		$result = $this->updateAll(array(
				'$push' => array('user_id' => $user['_id']),
				'$inc' => array(
						'user_total' => 1
				)
		), array(
				'_id' => new MongoId($partnerId),
				'user_id' => array('$nin' => array($user['_id']))
		));
		return $result;
	}
	
	/**
	 * 取消关联用户
	 * @param string $partnerId
	 * @param array $user
	 * @return boolean
	 */
	public function pull($partnerId, array $user) {
		  $result = $this->updateAll(array(
	        '$pull' => array('user_id' => $user['_id']),
	        '$inc' => array(
	            'user_total' => -1
	        )
	    ), array(
	        '_id' => new MongoId($partnerId),
	        'user_id' => array('$in' => array($user['_id']))
	    ));
		return $result;
	}
	/**
	 * 按partnerId 取得说详细
	 * @param unknown $userId
	 * @return Ambigous <multitype:, NULL>
	 */
	public function getById($partnerId) {
		$partner = $this->find('first', array(
				'conditions' => array(
						'_id' => $partnerId
				)
		));
		return $partner['Partner'] ? $partner['Partner']:array();
	}
	/**
	 * 检查名称是否存在
	 * @param string $name
	 * @return boolean
	 */
	public function isNameUnique($name) {
		return $this->find('count', array('conditions' => array('name'=>$name))) > 0 ? true : false;
	}
}