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
class Follow extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Follow';
	
	public $mongoSchema = array(
	    /**
	     * The user id is current user logged in already.
	     */
		'user_id' => array('type'=>'string'),
	    /**
	     * The user id is who I followed.
	     */
		'follower_id' => array('type'=>'string'),
	    /**
	     * The count number of new posts
	     */
	    'new_posts' => array('type' => 'int'),
		'created'=>array('type'=>'datetime'),    
		'modified'=>array('type'=>'datetime')
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'user_id' => array(
				'required' => array(
					'rule' => 'isMongoId',
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('Invalid user id'),
				)
			),
			'follower_id' => array(
				'required' => array(
					'rule' => 'isMongoId',
					'required' => 'create',
					'allowEmpty' => false,
					'message' => __('Invalid follower id')
				),
			    'follow_self' => array(
			        'rule' => 'chkFollowSelf',
			        'message' => __('Don\'t try to follow yourself')
			    )
			)
		);
	}

/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
    public function implementedEvents() {
        $callbacks = parent::implementedEvents();
        return array_merge($callbacks, array(
            'Model.Voice.afterUpdated.status.from.unavailable.to.approved' => 'incrNewPosts',
            'Model.Voice.afterUpdated.status.from.pending.to.approved'     => 'incrNewPosts',
            'Model.Voice.afterUpdated.status.from.approved.to.unavailable' => 'decrNewPosts',
            'Model.Voice.afterUpdated.status.from.approved.to.invalid'     => 'decrNewPosts',
            'Model.Voice.afterDeleted.approve'                             => 'decrNewPosts',
        ));
    }
    
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        if(TRUE == ($id = $this->isUpdate())) {
            ;
        } else {
            if(!isset($this->data[$this->name]['new_posts'])) {
                $this->data[$this->name]['new_posts'] = 0;
            }
        }
    }
    
/**
 * (non-PHPdoc)
 * @see Model::save()
 */
    public function save($data = null, $validate = true, $fieldList = array()) {
        if($data) {
            $this->set($data);
        }
        
        if($validate && !$this->validates()) {
            return false;
        }
        
    	if(TRUE == ($id = $this->exist())) {
    		$this->read(null, $id);
    		return $this->data;
    	} else {
    		return parent::save($data, $validate, $fieldList);
    	}
    }
    
/**
 * Get count of new posts by user id
 * 
 * @param string $userId
 * @return number
 */
    public function countNewPosts($userId) {
        $items = $this->find('all', array(
            'fields' => array(
                'new_posts'
            ),
            'conditions' => array(
                'user_id' => $userId,
                'new_posts' => array('$gt' => 0)
            )
        ));
        
        $count = 0;
        foreach($items as $item) {
            $count += $item[$this->name]['new_posts'];
        }
        return $count;
    }
    
/**
 * Increase 1 to new_posts when voice is approved by admin
 * 
 * @param CakeEvent $event
 * @return boolean
 */
    public function incrNewPosts(CakeEvent $event) {
        $model = $event->subject();
        $data = $model->data[$model->name];
    	
    	$this->updateAll(array(
			'$inc' => array('new_posts' => 1),
			'$set' => array('modified' => new MongoDate()),
    	), array(
    		'follower_id' => $data['user_id']
    	));
    }
    
/**
 * Decrease 1 to new_posts when status of voice is changed 
 * from `approved` to others by admin
 * 
 * @param CakeEvent $event
 * @return boolean
 */
    public function decrNewPosts(CakeEvent $event) {
        $model = $event->subject();
        $data = $model->data[$model->name];

    	$this->updateAll(array(
			'$inc' => array('new_posts' => -1),
			'$set' => array('modified' => new MongoDate()),
    	), array(
    		'follower_id' => $data['user_id'],
    	    'new_posts' => array('$gt' => 0)
    	));
    }
    
/**
 * Check whether the relationship has buit.
 * 
 * @param string $userId
 * @param string $followerId
 * @return boolean|string 
 *     It will return primary id of follow if found something, 
 *     otherwise false will be return.
 */
    private function exist() {
        $row = $this->find('first', array(
            'fields' => array('_id'),
    		'conditions' => array(
				'user_id' => $this->data[$this->name]['user_id'],
				'follower_id' => $this->data[$this->name]['follower_id']
    		)
        ));
        if($row && isset($row[$this->name]['_id'])) {
            return $row[$this->name]['_id'];
        }
        return false;
    }
    
/**
 * Reset count of new posts between user and follower
 * 
 * @param string $userId
 * @param string $followerId
 * @return boolean
 */
    public function resetNewPosts($userId, $followerId) {
        return $this->updateAll(array(
            '$set' => array('new_posts' => 0)
        ), array(
            'user_id' => $userId,
            'follower_id' => $followerId
        ));
    }
    
/**
 * Check whether `follower_id` is valid or not.
 * 
 * @param array|string $check
 * @return boolean
 */
    public function chkFollowSelf($check) {
        if(isset($check['follower_id'])) {
            $fid = $check['follower_id'];
        } else {
            $fid = $check;
        }
        if(!$fid) {
            return false;
        }
        if(isset($this->data[$this->name]['user_id'])) {
            if($fid == $this->data[$this->name]['user_id']) {
                return false;
            }
        }
        return true;
    }
    
    public function getFollowers($userId) {
        return $this->find('all', array(
            'fields' => array('user_id'),
            'conditions' => array(
                'follower_id' => $userId
            )
        ));
    }
    
    public function getFollowed($userId) {
        $follows = $this->find('all', array(
            'field' => array('follower_id'),
            'conditions' => array('user_id' => $userId)
        ));
        return Hash::extract($follows, "{n}.Follow.follower_id");
    }
}