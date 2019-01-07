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

class Comment extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Comment';
	
	public $mongoSchema = array(
		'voice_id' => array('type'=>'string'),
        'user_id' => array('type' => 'string'),
        'score' => array('type' => 'integer'),
        'prev_score' => array('type' => 'integer'),
        'content' => array('type' => 'string'),
	    /**
	     * It shouldn't be displayed on client side if hide is true
	     */
	    'hide' => array('type' => 'boolean'),
	    'voice_title' => array('type' => 'string'),
	    'voice_user_id' => array('type' => 'string'),
		'created' => array('type'  => 'datetime'),     
	    'modified' => array('type' => 'datetime'),
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    
        $this->validate = array(
            'score' => array(
                'required' => array(
                    'rule' => array('chkScore'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid score supplied') 
                ),
            ),
            'user_id' => array(
                'required' => array(
                    'rule' => array('isMongoId'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid user_id supplied') 
                ),
            ),
            'content' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'allowEmpty' => true,
                    'message' => __('Invalid content supplied') 
                ),
            ),
            'voice_id' => array(
                'required' => array(
                    'rule' => array('isMongoId'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid voice_id supplied') 
                ),
            ),
            'voice_title' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid voice title supplied') 
                ),
            ),
            'voice_user_id' => array(
                'required' => array(
                    'rule' => array('isMongoId'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid user id of voice supplied') 
                ),
            ),
        );
    }
    
/**
 * (non-PHPdoc)
 * @see Model::afterValidate()
 */
    public function afterValidate() {
        // Whether it is updating...
        if(TRUE == ($id = $this->isUpdate())) {
            $original = $this->findById($id)[$this->name];
            // Whether `score` is updated...
            $this->data[$this->name]['prev_score'] = $original['score'];
        } 
    }
    
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
    public function afterSave($created, $options = array()) {
        if($created && !empty($this->id)) {
            // While creating...
            $this->getEventManager()->dispatch(new CakeEvent('Model.Comment.afterCreated', $this));
            CakeResque::enqueue('notification', 'NotificationShell',
                array('newComment', $this->data[$this->name])
            );
        } else {
            // While editing...
            $this->read(null, $this->id);
            $this->getEventManager()->dispatch(new CakeEvent('Model.Comment.afterUpdated', $this));
        }
    }
    
/**
 * (non-PHPdoc)
 * @see Model::beforeDelete()
 */
//     public function beforeDelete($cascade = true) {
//         parent::beforeDelete($cascade);
//         if(!empty($this->id)) {
//         	// Get detail information before delete...
//         	$this->read(null, $this->id);
//         }
//     }
    
/**
 * (non-PHPdoc)
 * @see Model::afterDelete()
 */
//     public function afterDelete() {
//     	parent::afterDelete();
//     	$this->getEventManager()->dispatch(new CakeEvent(
//     			'Model.Comment.afterDeleted', $this));
//     	CakeResque::enqueue('notification', 'NotificationShell',
//     	    array('hideComment', $this->data[$this->name])
//     	);
//     }
    
/**
 * Check whether score is valid
 * 
 * @param array|string $check
 * @return boolean
 */
    public function chkScore($check) {
    	$score = false;
    	if(isset($check['score'])) {
    		$score = (float)$check['score'];
    	} else {
    		$score = (float)$check;
    	}
    	
    	if(!$score && !is_numeric($score)) {
    		return false;
    	}
    	
    	if($score > 5 || $score < 0) {
    		return false;
    	}
    	
    	$score = intval($score);
    	$this->data[$this->name]['score'] = $score;
    	
    	return true;
    }
    
/**
 * (non-PHPdoc)
 * @see Model::delete()
 */
    public function delete($id = null, $cascade = true) {
        $result = $this->updateAll(array(
        	'$set' => array('hide' => true)
        ), array(
            '_id' => new MongoId($id)
        ));
        
        if($result) {
        	$this->read(null, $id);
        	$this->getEventManager()->dispatch(new CakeEvent('Model.Comment.afterDeleted', $this));
        	CakeResque::enqueue('notification', 'NotificationShell',
        	    array('hideComment', $this->data[$this->name])
        	);
        	return true;
        } 
        
        return false;
    }
    
/**
 * Delete comment by author
 * 
 * @param string $id
 * @param string $userId The ID of author
 * @return boolean
 */
    public function deleteByAuthor($id, $userId) {
        $comment = $this->findById($id);
        if($comment[$this->name]['user_id'] == $userId) {
            return $this->delete($id);
        }
        return false;
    }
    
/**
 * Whether comment id has existed
 * 
 * @return boolean
 */
    public function chkId() {
        $userId = isset($this->data[$this->name]['user_id']) 
            ? $this->data[$this->name]['user_id'] : NULL;
        $voiceId = isset($this->data[$this->name]['voice_id']) 
            ? $this->data[$this->name]['voice_id'] : NULL;
        
        if(!$userId || !$voiceId) {
            return false;
        }
        
        $comment = $this->find('first', array(
            'fields' => array('_id'),
            'conditions' => array(
                'user_id' => $userId,
                'voice_id' => $voiceId
            )
        ));
        
        if(isset($comment[$this->name]['_id'])) {
//             $this->id = new MongoId($comment[$this->name]['_id']);
            $this->data[$this->name]['_id'] = $comment[$this->name]['_id'];
        }
    }
}