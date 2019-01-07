<?php
APP::uses('AppModel', 'Model');

class History extends AppModel {
    
    public $primaryKey = '_id';
    
    public $name = 'History';

    public $mongoSchema = array(
		'username' => array('type'=>'string'),
		'query' => array('type'=>'string'),
		'method' => array('type'=>'string'),
		'data' => array('type'=>'string'),
		'controller' => array('type'=>'string'),
		'action' => array('type'=>'string'),
		'created'=>array('type'=>'datetime'),
        'modified'=>array('type'=>'datetime')
    );
}