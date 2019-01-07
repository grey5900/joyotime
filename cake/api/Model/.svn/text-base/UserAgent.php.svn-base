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
class UserAgent extends AppModel {
    
    public $useTable = false;
    
    public $name = 'UserAgent';
    
    private $platform  = '';
    
    private $systemVer = '';
    
    private $fishsayingVer = '';
    
/**
 * Extract information from user agent
 * 
 * @param string $userAgent
 */
    public function extract($userAgent) {
    	$agent = $this->toArray($userAgent);
		if(isset($agent[0])) {
			$this->platform = $agent[0];
		}
		if(isset($agent[1])) {
			$this->systemVer = $agent[1];
		}
		if(isset($agent[2])) {
			$this->fishsayingVer = $agent[2];
		}
    }
    
/**
 * Extract user agent string into array
 *
 * @param string $userAgent
 * @return array
 */
    private function toArray($userAgent) {
        return explode('-', $userAgent);
    }
    
/**
 * Get system version
 * 
 * @return string
 */
    public function getSystemVersion() {
        return $this->systemVer;
    }
    
/**
 * Get fishsaying version
 * 
 * @return string
 */
    public function getFishsayingVersion() {
        return $this->fishsayingVer;
    }
    
/**
 * Get platform
 * 
 * @return string
 */
    public function getPlatform() {
        return $this->platform;
    }
}