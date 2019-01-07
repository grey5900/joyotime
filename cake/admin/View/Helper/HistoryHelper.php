<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Receipt', 'Model');
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

class HistoryHelper extends AppHelper {
    
    public $helpers = array('Html', 'Time');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
        if(isset($row['History'])) {
    	    $this->row = $row['History'];
        }
    	return $this;
    }
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function username() {
        return $this->get($this->row, 'username', '');
    }
    
    public function method() {
        return $this->get($this->row, 'method', 'Unknown');
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created->sec)) {
            return strftime('%Y-%m-%d %H:%M:%S', $created->sec);
        }
        return '';
    }

    public function query($field = '') {
        $query = $this->get($this->row, 'query', array());
        if($query) {
            if(!$field) {
                return print_r($query, TRUE);
            } else {
                return $query[$field];
            }
        }
        return '';
    }

    public function data() {
        $data = array();
        if($this->method() == 'POST') {
            $data = $this->get($this->row, 'data', array());
        } else if($this->method() == 'GET') {
            $data = $this->get($this->row, 'query', array());
        }
        return print_r($data, TRUE);
    }
}