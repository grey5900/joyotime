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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * @package app.Model
 */
class Version extends AppModel {
    
    public $primaryKey = '_id';
    
    public $name = 'Version';
    
    public $mongoSchema = array(
		'version'     => array('type' => 'string'),
		'platform'    => array('type' => 'string'),
		'description' => array('type' => 'string'),
		'created'     => array('type' => 'datetime')
    );
    
    private $version  = '0.0.0';
    
    public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		
		$this->validate = array(
            'version' => array(
                'require' => array(
                    'rule' => array('chkVersion'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid version') 
                ),
            ),
            'platform' => array(
                'require' => array(
                    'rule' => array('chkPlatform'),
                    'required' => 'create',
                    'allowEmpty' => false,
                    'message' => __('Invalid platform') 
                ),
            )
        );
	}
    
    public function getLatest($platform) {
        $item = $this->find('first', array(
            'conditions' => array('platform' => $platform),
            'order' => array('created' => 'desc')
        ));
        return isset($item) ? $item[$this->name] : array();
    }
    
    public function chkVersion($check) {
        $version = $this->getCheck('version', $check);
        if($this->format($version)) return true;
        return false;
    }
    
    public function chkPlatform($check) {
        $platform = $this->getCheck('platform', $check);
        if($platform == 'ios' || $platform == 'android') return true;
        return false;
    }
    
/**
 * Extract version information from user agent
 * 
 * @param string $version
 */
    public function extract($version) {
    	$this->version = $version;
    }
    
/**
 * Whether the version is valid or not
 * 
 * @return boolean
 */
    public function isValid() {
        return $this->format($this->version);
    }
    
/**
 * Compare version
 * 
 * @param string $version
 * @return int 
 *     0: Whether one equals another 
 *     -1:Whether client's less than specific $version
 *     1: Whether client's greater than specific $version
 */
    public function compareTo($version) {
        if(!$this->format($version)) {
            throw new CakeException('Version is invalid');
        }
        $client = $this->toArray($this->version);    // come from user agent...
        $define = $this->toArray($version);          // pre-defined in configure
        
        for($i = 0; $i < count($client); $i++) {
            if($client[$i] == $define[$i]) {
                continue;
            } 
            if($client[$i] > $define[$i]) {
                return 1;
            } else {
                return -1;
            }
        }
        
        return 0;
    }
    
/**
 * Get version by specify type
 * 
 * @param string $type `system` or `fishsaying`
 * @param boolean $toInt
 * @return number|string
 */
    public function getVersion() {
        return $this->version;
    }
    
/**
 * Extract version string into array
 * 
 * @param array $version
 * @return array
 */
    private function toArray($version) {
        return explode('.', $version);
    }
    
/**
 * Check whether version format is right or not
 * 
 * @param string $version
 * @return boolean
 */
    private function format($version) {
        return (bool) preg_match('/[0-9]{1,}\.[0-9]{1,}\.[0-9]{1,}/', $version);
    }
}