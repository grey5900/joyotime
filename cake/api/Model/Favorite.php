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
 * The model of Favorite
 *
 * @package		app.Model
 */
class Favorite extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Favorite';
	
	const DEFAULT_FAVORITE_TITLE = 'The default favorite';
	
	public $mongoSchema = array(
	    'title' => array('type' => 'string'),
	    'size' => array('type' => 'integer'),     // The current size of voices.
	    'user_id' => array('type' => 'string'),
	    'voices' => array(),    // [1, 2, 3, 4] voice_id
	    'thumbnail' => array(),
	    /**
	     * Is default favorite for user?
	     * Just only two available values, 0 and 1.
	     * Each user can hold only one default favorite and DO NOT allow delete it.
	     */
	    'isdefault' => array('type' => 'integer'),    
	    'created'=>array('type'=>'datetime'),     
	    'modified'=>array('type'=>'datetime'),    
	);
	
	public function __construct($id = false, $table = null, $ds = null) {
	    parent::__construct($id, $table, $ds);
	    
	    $this->validate = array(
    		'title' => array(
				'required' => array(
					'rule' => 'notEmpty',
					'required' => 'create'
				)
    		),
    		'user_id' => array(
				'required' => array(
					'rule' => 'isMongoId',
					'required' => 'create'
				)
    		),
    		'isdefault' => array(
				'required' => array(
					'rule' => array('naturalNumber', true),
					'required' => 'create'
				)
    		)
	    );
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
	    if(TRUE == ($id = $this->isUpdate())) {
	        ;
	    } else {
	        // whether it's creating...
	        $this->data[$this->name]['voices'] = array();
	        $this->data[$this->name]['thumbnail'] = array();
	        $this->data[$this->name]['size'] = 0;
	        
	        if(!isset($this->data[$this->name]['isdefault']))
	           $this->data[$this->name]['isdefault'] = 0;
	        
	        if(isset($this->data[$this->name]['package_id'])) {
	            $pkg = ClassRegistry::init('Package')->findById($this->data[$this->name]['package_id']);
	            if($pkg && $pkg = $pkg['Package']) {
	                $this->data[$this->name]['title'] = $pkg['title'];
	                $this->data[$this->name]['thumbnail'] = array($pkg['cover']);
	                $this->data[$this->name]['voices'] = isset($pkg['voices'])?$pkg['voices']:array();
	                $this->data[$this->name]['size'] = isset($pkg['voices'])?count($pkg['voices']):0;
	            } 
	            $this->getEventManager()->dispatch(new CakeEvent('Model.Favorite.createByPackage', $this));
	        } 
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
	public function implementedEvents() {
		$callbacks = parent::implementedEvents();
		return array_merge($callbacks, array(
			'Model.User.afterRegister' => 'createDefault',
		));
	}
	
/**
 * Initial default favorite for user when register successful
 * 
 * @param CakeEvent $event
 * @return boolean
 */
	public function createDefault(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
	    $data = array(
	        'user_id' => $data['_id'],
	        'title' => self::DEFAULT_FAVORITE_TITLE,
	        'isdefault' => 1
	    );
	    
	    $result = (bool) $this->create($data) && $this->save();
	    if(!$result) $this->failEvent($event);
	}
	
/**
 * Check whether the favorite is default.
 * 
 * @param string $favoriteId
 * @return boolean
 */
	public function isDefault($favoriteId) {
	    $favorite = $this->findById($favoriteId);
	    return isset($favorite[$this->name]['isdefault']) 
	        && ($favorite[$this->name]['isdefault']);
	}
	
/**
 * Get fav id by $userId and $title.
 * 
 * @deprecated
 * @param array $data
 * @return Ambigous <mixed, boolean>
 */
	public function getFavoriteId($userId, $title) {
	    $id = $this->isExist($userId, $title);
	    if(!$id) {
	        $this->create(array(
	            'user_id' => $userId,
	            'title' => $title,
	            'size' => 0,
	            'voices' => array(),
	        ));
	        $saved = $this->save();
	        if($saved) {
	            $id = $saved['Favorite']['_id'];
	        }
	    }
	    return $id;
	}
	
/**
 * Push a voice to the end of queue and plus 1 to size.
 *  
 * @param string $favId
 * @param string $voiceId
 * @param string $cover The cover of voice
 * @return boolean
 */
	public function push($favId, $voiceId, $cover = '') {
	    $push = array('voices' => $voiceId);
	    if($cover) {
	        $push = array_merge($push, array('thumbnail' => $cover));
	    }
	    return $this->updateAll(array(
    		    '$push' => $push,
    		    '$inc' => array('size' => 1)
            ), array(
    		    '_id' => new MongoId($favId),
    		    'voices' => array('$nin' => array($voiceId))
            )
        );
	}
	
/**
 * Remove a voiceId from queue.
 * 
 * @param string $favId
 * @param string $voiceId
 * @return boolean
 */
	public function pull($favId, $voiceId) {
	    $cover = false;
	    $fav = $this->findById($favId);
	    $first = array_shift($fav[$this->name]['voices']);
	    if($first && $first == $voiceId) {
	        if(TRUE == ($second = array_shift($fav[$this->name]['voices']))) {
	            $this->Voice = ClassRegistry::init('Voice');
	            $voice = $this->Voice->findById($second);
	            if($voice) {
	                $cover = $voice['Voice']['cover'];
	            }
	        } else {
	            // Store cover to default...
	            $cover = array();
	        }
	    }
	    $update = array('$pull' => array('voices' => $voiceId), 
    		    '$inc' => array('size' => -1));
	    // Set cover to default or replace new cover as thumbnail...
	    if($cover !== false) {
	        if(!$cover) {
	            $update = am($update, array(
	            	'$set' => array('thumbnail' => array())
	            ));
	        } else {
    	        $update = am($update, array(
    	            '$set' => array('thumbnail' => array($cover))
    	        ));
	        }
	    }
	    return $this->updateAll($update,
    		array(
				'_id' => new MongoId($favId),
				'voices' => array('$in' => array($voiceId))
    		)
	    );
	}
	
/**
 * Has the album existed already?
 * 
 * @param string $userId
 * @param string $title
 * @return mixed It will return _id if it is existed or false.
 */
	public function isExist($userId, $title) {
	    $row = $this->find('first', array(
	        'fields' => array('_id'),
	        'conditions' => array(
	            'user_id' => $userId,
	            'title' => $title
	        )
	    ));
	    return isset($row['Favorite']['_id']) ? $row['Favorite']['_id'] : false;
	}
	
/**
 * Get voices from favoriate.
 * 
 * @param string $favId
 * @param number $page
 * @param number $limit
 * @return array
 */
	public function getVoices($favId, $page = 1, $limit = 20) {
	    $skip = ($page - 1) * $limit;
	    return $this->find('first', array(
	        'fields' => array('voices' => array('$slice' => array($skip, $limit))),
	        'conditions' => array(
	            '_id' => new MongoId($favId),
	        )
	    ));
	}
}