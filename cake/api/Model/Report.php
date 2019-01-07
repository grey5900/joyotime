<?php
APP::uses('AppModel', 'Model');
class Report extends AppModel {
	
	public $primaryKey = '_id';
	
	const STATUS_PENDING = 0;
	const STATUS_DONE = 1;
	
	public $mongoSchema = array(
		'user_id' => array('type'=>'string'),
		'voice_id' => array('type'=>'string'),
		'content' => array('type'=>'string'),
	    'status' => array('type' => 'integer'), // pending:0(未处理)/done:1(已处理)
		'created' => array('type'=>'datetime'),     
		'modified' => array('type'=>'datetime'),    
	);
	
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
	public function beforeValidate($options = array()) {
        $this->validate = array(
            'user_id' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'allowEmpty' => false,
                    'message' => __('Invalid user_id supplied') 
                ),
            ),
            'voice_id' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'allowEmpty' => false,
                    'message' => __('Invalid voice_id supplied') 
                ),
            ),
            'content' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'required' => true,
                    'allowEmpty' => false,
                    'message' => __('Invalid content supplied') 
                ),
            ),
            'status' => array(
                'required' => array(
                    'rule' => 'notEmpty',
                    'allowEmpty' => false,
                    'message' => __('Invalid status supplied') 
                ),
            ),
        );
    }
}