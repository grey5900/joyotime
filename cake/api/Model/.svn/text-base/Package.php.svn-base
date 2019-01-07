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
class Package extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Package';
	
	/**
	 * Waiting for check by admin
	 *
	 * @var int
	 */
	const STATUS_PENDING = 0;
	/**
	 * Checked and passed by admin
	 *
	 * @var int
	 */
	const STATUS_APPROVED = 1;
	
	public $mongoSchema = array(
	    'title' => array('type' => 'string'),
	    'cover' => array('type' => 'string'),
	    'status' => array('type' => 'integer'),
	    'location' => array(
		    'lat' => array('type' => 'float'),
		    'lng' => array('type' => 'float'),
		),
	    'voices' => array(),    // voices list, order sensitive
	    'voice_total' => array('type' => 'integer'),
	    'voice_length_total' => array('type' => 'integer'),
	    'favorite_total' => array('type' => 'integer'),
	    'deleted' => array('type' => 'integer'),
	    'language' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime'),
	);
	
	private $voices = array();
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
			'latitude' => array(
				'required' => array(
					'rule' => array('chkLatitude'),
					'required' => 'create',
					'message' => __('Invalid latitude')
				)
			),
			'longitude' => array(
				'required' => array(
					'rule' => array('chkLongitude'),
					'required' => 'create',
					'message' => __('Invalid longitude')
				)
			),
		    'cover' => array(
	    		'require' => array(
    				'rule' => 'chkCover',
    				'required' => 'create',
    				'message' => __('Invalid cover')
	    		)
		    ),
		    'voices' => array(
		        'require' => array(
	        		'rule' => 'chkVoices',
	        		'message' => __('Invalid voices')
		        )
		    ),
		    'title' => array(
		        'require' => array(
	        		'rule' => 'notEmpty',
	        		'required' => 'create',
	        		'message' => __('Invalid title')
		        )
		    ),
		    'status' => array(
		        'format' => array(
	        		'rule' => 'chkStatus'
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
			'Model.Favorite.createByPackage' => 'updateFavoriteTotal',
		    'Model.Voice.afterUpdated.status.from.approved.to.invalid'     => 'updateVoices',
		    'Model.Voice.afterUpdated.status.from.approved.to.unavailable' => 'updateVoices'
		));
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
	    if($created) $evt = 'Model.Package.afterSaved';
	    else {
	        $evt = 'Model.Pacakge.afterUpdated';
	        $this->data = $this->findById($this->id);
	    }
	    $this->getEventManager()->dispatch(new CakeEvent($evt, $this));
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
	public function beforeSave($options = array()) {
	    $id = $this->isUpdate();
	    
	    if(!$id) {    // It's creating...
	        $this->data[$this->name]['voices']   = array();
	        $this->data[$this->name]['status']   = self::STATUS_PENDING;
	        $this->data[$this->name]['deleted']  = 0;
	        $this->data[$this->name]['favorite_total'] = 0;
	        $this->data[$this->name]['language'] = 'zh_CN';
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::beforeDelete()
 */
	public function beforeDelete($cascade = true) {
	    if($this->id) {
	        $row = $this->findById($this->id);
	        if(isset($row[$this->name]) && $row = $row[$this->name]) {
	            $this->data[$this->name]['del'] = $row['voices'];
	        }
	    }
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterDelete()
 */
	public function afterDelete() {
	    if($this->id) $this->getEventManager()->dispatch(new CakeEvent('Model.Package.afterDeleted', $this));
	}
	
/**
 * Implement callback method when voice from approval to invalid or unavailable
 * 
 * @param CakeEvent $event
 */
	public function updateVoices(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
	    foreach(isset($data['packages']) ? $data['packages'] : array() as $pkgId) {
	        $model->updateAll(array(
	            '$pull' => array('packages' => $pkgId)
	        ), array(
	            '_id' => new MongoId($data['_id'])
	        ));
	        $result = $this->updateAll(array(
	            '$inc'  => array(
	                'voice_total' => -1, 
	                'voice_length_total' => -(int)$data['length']
	            ),
	            '$pull' => array('voices' => $data['_id'])
	        ), array(
	            '_id' => new MongoId($pkgId)
	        ));
	    }
	}
	
/**
 * Implement callback method for add `favorite_total`
 * 
 * @param CakeEvent $event
 */
	public function updateFavoriteTotal(CakeEvent $event) {
	    $model = $event->subject();
	    $data = $model->data[$model->name];
	    
	    if(isset($data['package_id'])) {
	        $result = $this->updateAll(array(
	            '$inc' => array('favorite_total' => 1)
	        ), array(
	            '_id' => new MongoId($data['package_id'])
	        ));
	        if(!$result) $this->failEvent($event);
	    }
	}
	
	public function push($pkgId, array $voice, $offset = 0) {
	    $result = $this->updateAll(array(
	        '$push' => array('voices' => $voice['_id']),
	        '$inc' => array(
	            'voice_total' => 1, 
	            'voice_length_total' => $voice['length']
	        )
	    ), array(
	        '_id' => new MongoId($pkgId),
	        'voices' => array('$nin' => array($voice['_id']))
	    ));
	    
	    if($result) {
	        $this->id = $pkgId;
	        $this->data[$this->name]['add'] = array($voice['_id']);
	        $this->getEventManager()->dispatch(new CakeEvent('Model.Package.afterPush', $this));
	        return true;
	    } else {
	        // Sorting...
	        if($offset) {
    	        $offset  = (int) $offset;
    	        $ordered = array();
    	        $len = 0;    // size of voices array...
    	        $pkg = $this->findById($pkgId);
    	        if(!isset($pkg[$this->name]['voices'])) return false;
    	        $voices = $pkg[$this->name]['voices'];
    	        foreach($voices as $idx => $vid) 
    	            if($vid == $voice['_id']) { unset($voices[$idx]); break; }
    	        $size = count($voices);
    	        $pos = $idx + $offset;
    	        
    	        if($pos < $size && $pos > 0) {
        	        foreach(array_values($voices) as $idx => $vid) {
        	            if($idx == $pos) {$ordered[] = $voice['_id']; }
        	            $ordered[] = $vid;
        	        }
    	        } elseif($pos >= $size) {
    	            foreach($voices as $vid) $ordered[] = $vid;
    	            $ordered[] = $voice['_id'];
    	        } elseif($pos <= 0) {
    	            $ordered[] = $voice['_id'];
    	            foreach($voices as $vid) $ordered[] = $vid;
    	        }
    	        return $this->updateAll(array('voices' => $ordered), array('_id' => new MongoId($pkgId)));
	        }
	    }
	    return false;
	}
	
	public function pull($pkgId, array $voice) {
	    $result = $this->updateAll(array(
	        '$pull' => array('voices' => $voice['_id']),
	        '$inc' => array(
	            'voice_total' => -1, 
	            'voice_length_total' => -$voice['length']
	        )
	    ), array(
	        '_id' => new MongoId($pkgId),
	        'voices' => array('$in' => array($voice['_id']))
	    ));
	    
	    if($result) {
	    	$this->id = $pkgId;
	    	$this->data[$this->name]['del'] = array($voice['_id']);
	    	$this->getEventManager()->dispatch(new CakeEvent('Model.Package.afterPull', $this));
	    	return true;
	    }
	    return false;
	}

/**
 * Check whether the location that is consisted with latitude
 * and longitude is valid or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkLatitude($check) {
		$field = '';
		if(isset($check['latitude'])) {
			$field = $check['latitude'];
		} else {
			$field = $check;
		}
		if($field) {
			$latitude = (double) $field;
			if(($latitude >= -90) && ($latitude <= 90)) {
				$this->data[$this->name]['location']['lat'] = $latitude;
				unset($this->data[$this->name]['latitude']);
				return true;
			}
		}
		return false;
	}
	
/**
 * Check whether the location that is consisted with latitude
 * and longitude is valid or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkLongitude($check) {
		$field = '';
		if(isset($check['longitude'])) {
			$field = $check['longitude'];
		} else {
			$field = $check;
		}
		if($field) {
			$longitude = (double) $field;
			if(($longitude >= -180) && ($longitude <= 180)) {
				$this->data[$this->name]['location']['lng'] = $longitude;
				unset($this->data[$this->name]['longitude']);
				return true;
			}
		}
		return false;
	}
	
/**
 * Check whether the location that is consisted with latitude
 * and longitude is valid or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkLocation($check) {
		if(isset($check['location'])) {
			if(isset($check['location']['lat']) && isset($check['location']['lng'])) {
				if(!empty($check['location']['lat']) && !empty($check['location']['lng'])) {
					return true;
				}
			}
		}
		return false;
	}
	
/**
 * Check whether there is uploaded file as cover image or not.
 *
 * @param array $check
 * @return boolean
 */
	public function chkCover($check) {
		$field = '';
		if(isset($check['cover'])) {
			$field = $check['cover'];
		} else {
			$field = $check;
		}
		if($field) {
			$this->data[$this->name]['cover'] = array();
			$this->data[$this->name]['cover']['source'] = $field;
			$this->data[$this->name]['cover']['x80']    = $field;
			$this->data[$this->name]['cover']['x160']   = $field;
			$this->data[$this->name]['cover']['x640']   = $field;
			return true;
		}
		return false;
	}
	
	public function chkVoices($check) {
	    $voices = $this->gets('voices', $check);
	    if($voices) {
	        $voices = explode(',', $voices);
	        $items = array();
    	    foreach($voices as &$voice) {
    	        $voice = trim($voice);
    	        if(!$this->isMongoId($voice)) {
    	            return false;
    	        }
    	        $items[] = $voice;
    	    }
    	    $this->data[$this->name]['voices'] = array_unique($items);
	    }
	    return true;
	}
	
/**
 * Implement validate callback method for formatting status value
 * 
 * @param array $check
 */
	public function chkStatus($check) {
	    foreach($check as $key => $val) 
	        $this->data[$this->name]['status'] = (int) $val;
	    return true;
	}
}