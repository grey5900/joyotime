<?php
App::uses('AppHelper', 'View/Helper');
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

class VersionHelper extends AppHelper {
    
    public $helpers = array('Html', 'Time');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return VersionHelper
     */
    public function init(&$row = array()) {
        $this->row = $row;
        return $this;
    }
    
    public function version() {
        return $this->get($this->row, 'version', '');
    } 
    
    public function platform() {
        return $this->get($this->row, 'platform', '');
    }
    
    public function description() {
        return $this->get($this->row, 'description', '');
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
}