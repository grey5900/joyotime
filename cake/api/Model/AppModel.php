<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {
    
/**
 * For mongo
 * (non-PHPdoc)
 * @see Model::isUnique()
 */
    public function isUnique($fields, $or = true) {
    	$conditions = array();
    	foreach($fields as $field => $value) {
    		$conditions[$this->name.'.'.$field] = $value;
    	}
    	$exist = $this->find('count', array(
    		'conditions' => $conditions,
    	));
    	return $exist > 0 ? false : true;
    }
    
/**
 * For mongo
 * Check whether the string is valid mongo id or not.
 * 
 * @return boolean
 */
    public function isMongoId($check) {
    	if(!$check) return false;
    	
    	$id = NULL;
    	if(is_array($check)) {
        	$values = array_values($check);
        	if(count($values) < 1) return false;
        	$id = $values[0]; 
    	} else {
    	    $id = $check;
    	}
        return (bool) (is_a($id, 'MongoId') || preg_match('/[0-9a-f]{24}/i', $id));
    }
    
/**
 * Check whether the process is creating or updating...
 * 
 * @return string|boolean id or false if it's not...
 */
    public function isUpdate() {
        $id = false;
        if(isset($this->data[$this->name]['_id'])) {
        	$id = $this->data[$this->name]['_id'];
        }
        if(!empty($this->id)) {
        	$id = $this->id;
        }
        return $id;
    }
    
    public function isMainModel() {
        return isset($this->data[$this->name]);
    }
    
/**
 * Check whether the amount is valid or not
 *
 * @param array $check
 * @return boolean
 */
    public function chkAmount($check) {
    	$amount = array();
    	$time = 0;
    	if(isset($check['amount'])) {
    		$amount = $check['amount'];
    	} else {
    		$amount = $check;
    	}
    	if(!isset($amount['time'])) {
    		return false;
    	}
    	$time = $amount['time'];
    	if(!is_numeric($time) || $time < 1) {
    		return false;
    	}
    	return true;
    }
    
/**
 * Initialize validation rules
 */
    public function initValidates() {
        
    }
    
/**
 * Get value from array safely
 * 
 * @param string $field
 * @param array $data
 * @param string $default
 * @return string
 */
    public function gets($field, $data = array(), $default = '') {
        if(is_array($data) && isset($data[$field])) {
            return $data[$field];
        }
        return $default;
    }
    
/**
 * Get value from $check paramter in validate method
 * 
 * @param string $field
 * @param mixed $check
 * @return mixed
 */
    protected function getCheck($field, &$check) {
    	$item = '';
        if(!($item = $this->gets($field, $check))) {
        	$item = $check;
        }
        return $item;
    }
    
/**
 * Log failed event
 * 
 * @param CakeEvent $event
 */
    public function failEvent(CakeEvent $event) {
        $this->log($this->name.' invoked '.$event->name().', but fails...', 'event_invoke_debug');
        $this->log($event->subject()->data, 'event_invoke_debug');
    }
}
