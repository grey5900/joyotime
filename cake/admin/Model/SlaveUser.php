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
class SlaveUser extends AppModel {
	var $useDbConfig = 'fishsaying_bak';
	public $primaryKey = '_id';
	public $name = 'User';
	protected $tableName = 'users';
	public $mongoSchema = array(
		'username'=>array(
			'type'=>'string' 
		), 
		'email'=>array(
			'type'=>'string' 
		), 
		'password'=>array(
			'type'=>'string' 
		), 
		'avatar'=>array(
			'source'=>array(
				'type'=>'string' 
			), 
			'x80'=>array(
				'type'=>'string' 
			), 
			'x180'=>array(
				'type'=>'string' 
			) 
		), 
		'money'=>array(
			'type'=>'integer' 
		),  // 账户总额
		'earn'=>array(
			'type'=>'integer' 
		),  // voice销售所得
		'cost'=>array(
			'type'=>'integer' 
		),  // 支出总计
		'income'=>array(
			'type'=>'integer' 
		),  // 收入总计，包括转账，赠送，销售
		'latest_voice_posts'=>array(
			'type'=>'datetime' 
		),  // 最新一条语音发布的时间
		'voice_total'=>array(
			'type'=>'integer' 
		),  // 发布的语音总数
		'favorite_size'=>array(
			'type'=>'integer' 
		), 
		'purchase_total'=>array(
			'type'=>'integer' 
		),  // 总购买voice的数量
		'voice_income_total'=>array(
			'type'=>'integer' 
		),  // 总卖出voice的数量
		'voice_length_total'=>array(
			'type'=>'integer' 
		),  // 总voice的时长统计数量
		'locale'=>array(
			'type'=>'string' 
		),  // client本地化所属地区
		/*
		 * Records of gift sent
		 */
		'gift'=>array(
			'register'=>array(
	            /*
	             * Record sent gift to who
	             */
	            'device_code'=>array(
					'type'=>'string' 
				) 
			) 
		), 
		'role'=>array(
			'type'=>'string' 
		),
	    /*
	     * The list of device code which is used by user...
	     */
		'device_code'=>array(), 
		'reg_source'=>array(
			'type'=>'string' 
		), 
		/**
		 * Credential data...
		 */
		'certified'=>array(
			'sina_weibo'=>array(
				'open_id'=>array(
					'type'=>'string' 
				) 
			), 
			'qzone'=>array(
				'open_id'=>array(
					'type'=>'string' 
				) 
			), 
			'twitter'=>array(
				'open_id'=>array(
					'type'=>'string' 
				) 
			), 
			'facebook'=>array(
				'open_id'=>array(
					'type'=>'string' 
				) 
			) 
		), 
		'is_contributor'=>array(
			'type'=>'boolean' 
		), 
		/**
		 * Whether verified or not.
		 */
		'is_verified'=>array(
			'type'=>'boolean' 
		), 
		/**
		 * Verify information
		 */
		'verified_description'=>array(
			'type'=>'string' 
		), 
		/**
		 * Personalized signature
		 */
		'description'=>array(
			'type'=>'string' 
		), 
		/**
		 * VIP user background
		 *
		 * It includes:
		 * 1. the path of source.
		 * 2. the path of scaled 80 * 80
		 * 3. the path of scaled 160 * 160
		 * 4. the path of scaled 640 * 640
		 */
		'cover'=>array(
			'source'=>array(
				'type'=>'string' 
			), 
			'x80'=>array(
				'type'=>'string' 
			), 
			'x160'=>array(
				'type'=>'string' 
			), 
			'x640'=>array(
				'type'=>'string' 
			) 
		), 
		'recommend'=>array(
			'type'=>'integer' 
		), 
		'recommend_reason'=>array(
			'type'=>'string' 
		), 
		'created'=>array(
			'type'=>'datetime' 
		), 
		'modified'=>array(
			'type'=>'datetime' 
		) 
	);
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->databaseConfig = $this->getDataSource()->config;
		$this->validate = array(
			'name'=>array(
				'require'=>array(
					'rule'=>'notEmpty', 
					'required'=>'create', 
					'message'=>__('Invalid title') 
				) 
			) 
		);
	}
	/**
	 * 根据相关ID数组，统计相关字段 这个方法有循环实连数据库的情况，可以改进
	 * 
	 * @param array $userIds        	
	 * @param string $field        	
	 */
	public function getRelationStatistics($inIds, $field) {
		foreach ($inIds as $key=>$val) {
			$inIds[$key] = new mongoId($val);
		}
		$m = new MongoClient($this->databaseConfig['replicaset']['host']);
		$c = $m->selectDB($this->databaseConfig['database'])->selectCollection($this->tableName);
		$ops = array(
			array(
				'$match'=>array(
					"_id"=>array(
						'$in'=>$inIds 
					) 
				) 
			), 
			array(
				'$group'=>array(
					'_id'=>null, 
					'total'=>array(
						'$sum'=>'$' . $field 
					) 
				) 
			) 
		);

		$results = $c->aggregate($ops);

		return isset($results['result'][0]['total'])?$results['result'][0]['total']:0 ;
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
		// }
	}
	public function getById($userId) {
		$user = $this->find('first', array(
				'fields' => array('password' => 0),
				'conditions' => array(
						'_id' => $userId
				)
		));
		return $user['SlaveUser'];
	}
}