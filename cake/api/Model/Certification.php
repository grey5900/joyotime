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
 * @package		app.Model
 */
class Certification extends AppModel {
    
	public $primaryKey = '_id';
	
	public $name = 'Certification';
	
	public $findMethods = array('authorize' =>  true);
	
	public $mongoSchema = array(
	    'user_id' => array('type' => 'string'), 
	    /**
	     * The name of certification
	     */
	    'name' => array('type' => 'string'),    
	    /**
	     * The token value is used to authorize.
	     */  
	    'token' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime')
	);
	
	const NAME_SINA_WEIBO = "sina_weibo";
	const NAME_QZONE = "qzone";
	const NAME_FACEBOOK = "facebook";
	const NAME_TWITTER = "twitter";
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
	
		$this->validate = array(
            'user_id' => array(
                'require' => array(
                    'rule' => array('isMongoId'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('User id must supply') 
                ) 
            ),
            'name' => array(
                'require' => array(
                    'rule' => array('chkName'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Name invalid') 
                ) 
            ),
            'token' => array(
                'require' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Token must supply') 
                ) 
            )
        );
	}

/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        if(!$this->isUpdate()) {
            
        }
    }
    
/**
 * Callback method for authorize user
 * 
 * $conditions = array(
 *     'authorize' => 'unique id for each user',
 *     'certification' => 'facebook'
 * );
 *
 * @param string $state
 * @param array $query
 * @param array $results
 */
    public function _findAuthorize($state, $query, $results = array()) {
    	if ($state === 'before') {
    		$token = $this->gets('token', $query['conditions']);
    		$name = $this->gets('name',  $query['conditions']);
    		
    		$query['conditions'] = array(
				'token' => $token,
				'name' => $name
    		);
    
    		return $query;
    	}
    
    	if ($state === 'after') {
    		if (empty($results[0])) {
    			return array();
    		}
    		return $results[0];
    	}
    }
    
/**
 * Check whether the name of certification is valid or not
 * 
 * @param string|array $check
 * @return boolean
 */
    public function chkName($check) {
        $name = $this->getCheck('name', $check);
        if($name) {
            $name = strtolower($name);
            switch($name) {
                case self::NAME_SINA_WEIBO:
                case self::NAME_QZONE:
                case self::NAME_FACEBOOK:
                case self::NAME_TWITTER:
                    $this->data[$this->name]['name'] = $name;
                    return true;
            }
        }
        return false;
    }
}