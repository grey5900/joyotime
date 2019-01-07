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
class Tag extends AppModel {
    
	public $primaryKey = '_id';
	
	public $name = 'Tag';
	
	public $mongoSchema = array(
	    'name' => array('type' => 'string'), 
	    'ref_total' => array('type' => 'integer'), 
	    'category' => array('type' => 'string'),
	    'language' => array('type' => 'string'),
	    'created' => array('type' => 'datetime'),
	    'modified' => array('type' => 'datetime')
	);
	
    public function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        
        $this->validate = array_merge($this->validate, array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'required' => 'create'
                ),
            	'unique' => array(
            		'rule' => array('isNameUnique'),
            		'message' => __('Name has existed already')
            	)
            ),
            'category' => array(
                'required' => array(
            		'rule' => array('notEmpty'),
                    'required' => 'create'
                )
            ),
            'language' => array(
                'required' => array(
            		'rule' => array('notEmpty'),
                    'required' => 'create',
                    'message' => __('langauge require')
                )
            )
        ));
    }
	
/**
 * (non-PHPdoc)
 * @see Model::implementedEvents()
 */
// 	public function implementedEvents() {
// 	    $callbacks = parent::implementedEvents();
// 	    return array_merge($callbacks, array(
// 	        'Model.Voice.afterCreated' => 'saveTags',
// 	        'Model.Voice.afterUpdated' => 'updateTags'
// 	    ));
// 	}

/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        if(!$this->isMainModel()) return ;
        
        $id = $this->isUpdate();
        if(!$id) { 
            $this->data[$this->name]['ref_total'] = 0;
        }
    }

/**
 * Check name whether has existed or not.
 *
 * @param array $fields
 * @param string $or
 * @return boolean
 */
    public function isNameUnique($fields, $or = true) {
        $id = $this->isUpdate();
    	$cond = array();
    	foreach($fields as $field => $value) {
    		$cond[$field] = $value;
    	}
    	if($id) $cond['_id'] = array('$ne' => new MongoId($id));
    	return $this->find('count', array('conditions' => $cond)) > 0 ? false : true;
    }
}