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
class Theme extends AppModel {
	
	public $primaryKey = '_id';
	
	public $name = 'Theme';
	
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
		'description' => array('type' => 'string'),
	    'cover' => array('type' => 'string'),
	    'status' => array('type' => 'integer'),
	    'voices' => array(
			array(
	    		'voice_id' => array('type' => 'string'),
				'reason' => array('type' => 'string')
	    	)
		),    // voices list, order sensitive
	    'voice_total' => array('type' => 'integer'),
	    'deleted' => array('type' => 'integer'),
	    'language' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime')
	);
	
	private $voices = array();
	
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->validate = array(
		    'cover' => array(
	    		'require' => array(
    				'rule' => 'chkCover',
    				'required' => 'create'
	    		)
		    ),
		    'title' => array(
		        'require' => array(
	        		'rule' => 'notEmpty',
	        		'required' => 'create'
		        )
		    ),
		    'description' => array(
		        'require' => array(
	        		'rule' => 'notEmpty',
	        		'required' => 'create'
		        )
		    )
		);
	}
	
/**
 * (non-PHPdoc)
 * @see Model::afterSave()
 */
	public function afterSave($created, $options = array()) {
	    if($created) $evt = 'Model.Theme.afterSaved';
	    else {
	        $evt = 'Model.Theme.afterUpdated';
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
	        //$this->data[$this->name]['language'] = 'zh_CN';
	    }
	}
	
	public function beforeDelete($cascade = true) {
	    if($this->id) {
	        $row = $this->findById($this->id);
	        if(isset($row[$this->name]) && $row = $row[$this->name]) {
	        	$ids = array();
	        	foreach($row['voices'] as $item) $ids[] = $item['voice_id'];
	            $this->data[$this->name]['del'] = $ids;
	        }
	    }
	}
	
	public function afterDelete() {
	    $this->getEventManager()->dispatch(new CakeEvent('Model.Theme.afterDeleted', $this));
	}
	
	public function push($themeId, $voiceId, $reason = '') {
		// pull voice from list first...
		$pull = $this->updateAll(array(
			'$pull' => array('voices' => array('voice_id' => $voiceId))
		), array(
			'_id' => new MongoId($themeId),
			'voices.voice_id' => array('$in' => array($voiceId))
		));
		
		$theme = $this->findById($themeId);
		if(!$theme) return false;
		
		
	    $result = $this->updateAll(array(
	        '$push' => array('voices' => array('voice_id' => $voiceId, 'reason' => $reason)),
	        '$inc'  => array('voice_total' => 1)
	    ), array(
	        '_id' => new MongoId($themeId),
	        'voices.voice_id' => array('$nin' => array($voiceId))
	    ));
	    
	    if($result && !$pull) {
	        $this->id = $themeId;
	        $this->data[$this->name]['add'] = array($voiceId);
	        $this->getEventManager()->dispatch(new CakeEvent('Model.Theme.afterPush', $this));
	    }
	    return (bool)$result;
	}
	
	public function pull($themeId, $voiceId) {
	    $result = $this->updateAll(array(
	        '$pull' => array('voices' => array('voice_id' => $voiceId)),
	        '$inc'  => array('voice_total' => -1)
	    ), array(
	        '_id' => new MongoId($themeId),
	        'voices.voice_id' => array('$in' => array($voiceId))
	    ));
	    
	    if($result) {
	    	$this->id = $themeId;
	    	$this->data[$this->name]['del'] = array($voiceId);
	    	$this->getEventManager()->dispatch(new CakeEvent('Model.Theme.afterPull', $this));
	    	return true;
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
	
}