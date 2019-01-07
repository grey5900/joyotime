<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Voice', 'Model');
require_once(VENDORS."emoji/emoji.php");

define('FORMAT_DATE', '%Y-%m-%d');
define('FORMAT_TIME', '%Y-%m-%d %H:%M:%S');
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

class VoiceHelper extends AppHelper {
    
    public $helpers = array('Html');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
    	$this->row = $row;
    	return $this;
    }
    
    public function shareHash($hash) {
    	return $hash == md5($this->id().$this->author());
    }
    
    public function id() {
    	return $this->get($this->row, '_id');
    }
    
    public function author() {
    	return $this->get($this->row, 'user_id');
    }
    
    public function title() {
        $title = $this->get($this->row, 'title', '');
        if($title) {
            $title = emoji_unified_to_html($title);
        }
        return $title;
    }
    
/**
 * Get cover with different size
 *  
 * @param array $row
 * @param string $demension The $demension available values including,
 * x80, x160, x640, source
 * @return string
 */
    public function cover($demension = 'x80') {
        $cover = $this->get($this->row, 'cover', array());
        $title = $this->get($this->row, 'title');
        switch($demension) {
            case 'x80':
                $width = $height = 80;
                break;
            case 'x160':
                $width = $height = 160;
                break;
            case 'x640':
                $width = $height = 640;
                break;
            case 'source':
                $width = $height = 640;
                break;
            default:
                $width = $height = 80;
        }
        if(isset($cover[$demension])) {
            return $cover[$demension];
        }
        return '';
    }
}