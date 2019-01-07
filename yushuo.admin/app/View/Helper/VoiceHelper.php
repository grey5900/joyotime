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
    
    const STATUS_PENDING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_INVALID = 2;
    const STATUS_UNAVAILABLE = 3; 
    
    public $helpers = array('Html', 'Time');
    
    private $row = array();
    
/**
 * @var UserHelper
 */
    private $user;
    
    public $rows = array();
    
    public function setUser(UserHelper $user) {
        $this->user = $user;
    }
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
    	$this->row = $row;
    	return $this;
    }
    
    /**
     * @param int $type status of voice
     * @return string
     */
    public function listLink($status) {
        $link = '/voices/index/'.$status;
        if($this->user) {
            return $link.'/'.$this->user->id();
        }
        return $link;
    }
    
    public function subTitle($status) {
        $text = $author = '';
        switch(intval($status)) {
        	case self::STATUS_PENDING:
        		$text = '<span class="pending-color">'.__('Pending').'</span>';
        		break;
        	case self::STATUS_APPROVED:
        		$text = '<span class="approved-color">'.__('Approved').'</span>';
        		break;
        	case self::STATUS_INVALID:
        		$text = '<span class="invalid-color">'.__('Invalid').'</span>';
        		break;
        	case self::STATUS_UNAVAILABLE:
        		$text = '<span class="unavailable-color">'.__('Unavailable').'</span>';
        		break;
        }
        
        $info = '<em>'.__("（共".$this->user->voiceTotal()."条，总时长".$this->user->voiceLengthTotal()."）").'</em>';
        
        if(count($this->rows) > 0) {
            return __("“".$this->user->username()."”".$text."的解说".$info);
        }
    }
    
    public function status() {
        $status = $this->get($this->row, 'status', 0);
        switch(intval($status)) {
            case self::STATUS_PENDING:
                return '<span class="pending-color">'.__('Pending').'</span>';
            break;
            case self::STATUS_APPROVED:
                return '<span class="approved-color">'.__('Approved').'</span>';
            break;
            case self::STATUS_INVALID:
                return '<span class="invalid-color">'.__('Invalid').'</span>';
            break;
            case self::STATUS_UNAVAILABLE:
                return '<span class="unavailable-color">'.__('Unavailable').'</span>';
            break;
        }
        return '<span class="pending-color">'.__('Pending').'</span>';
    }
    
    public function title() {
        $title = $this->get($this->row, 'title', '');
        if($title) {
            $title = emoji_unified_to_html($title);
        }
        return $title;
    }
    
    public function edit() {
        $status = $this->get($this->row, 'status', 0);
        $id = $this->get($this->row, '_id');
        if($status == self::STATUS_INVALID && $id) {
            return $this->Html->link(__('Edit'), "/voices/edit/{$id}");
        }
    }
    public function remove() {
        $status = $this->get($this->row, 'status', 0);
        $id = $this->get($this->row, '_id');
        if($status == self::STATUS_INVALID && $id) {
            return $this->Html->link(__(''), "#remove", array('class' => 'icon-delete','data-toggle' => 'modal','data-remove' => '/voices/remove/'.$id));
        }
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
    
    public function address() {
        $voice = $this->get($this->row, 'voice');
        if($voice) {
            return $voice;
        }
        return '';
    }
    
    public function point() {
        $lat = $this->get($this->row['location'], 'lat');
        $lng = $this->get($this->row['location'], 'lng');
        if($lat && $lng) {
            return new Point($lat, $lng);
        }
        return new Point();
    }
    
    public function isfree() {
        $isfree = $this->get($this->row, 'isfree');
        if($isfree) {
            return $this->Html->link(__('Free'), 'javascript:;',array('class' => 'isfree'));
        }
        return '';
    }
    
    public function language() {
        return $this->get($this->row, 'language');
    }
    
    public function author($row = array()) {
        if($row) {
            $username = $this->get($row['user'], 'username');
        } else {
            $username = $this->get($this->row['user'], 'username');
        }
        if($username) {
            return emoji_unified_to_html($username);
        } else {
            return __('anonymous');
        }
    }
    
    public function invalid() {
        $id = $this->get($this->row, '_id');
        $status = $this->get($this->row, 'status', 0);
        if($status != self::STATUS_INVALID && $id) {
            return $this->Html->link(__('Reject'), '#invalidmodal', array(
                'data-url' => "/voices/invalid/{$id}",
                'data-toggle' => "modal",
                'class' => 'invalid-link',
            ));
        }
    }
    
    public function approved($title = 'Approve') {
        $id = $this->get($this->row, '_id');
        $status = $this->get($this->row, 'status', 0);
        if($status != self::STATUS_APPROVED && $id) {
            return $this->Html->link($title, "javascript:void(0);", array(
                'data-url' => "/voices/approved/{$id}",
                'class' => 'approved-link',
                'data-loading-text' =>'正在上架...'
            ));
        }
    }
    
    public function unavailable() {
        $id = $this->get($this->row, '_id');
        $status = $this->get($this->row, 'status', 0);
        if($status != self::STATUS_UNAVAILABLE && $id) {
            return $this->Html->link(__('Off shelf'), "#unavailable-modal", array(
                'data-url' => "/voices/unavailable/{$id}",
                'class' => 'unavailable-link',
                'data-toggle' => "modal",
            ));
        }
    }
    
    /**
     * @deprecated
     * @see VoiceHelper::time()
     */
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime(FORMAT_TIME, $created['sec']);
        }
        return '';
    }
    
    public function time($name = 'created') {
        $time = $this->get($this->row, $name, array());
        if(isset($time['sec'])) {
            return strftime(FORMAT_TIME, $time['sec']);
        } elseif(isset($time)) {
            return strftime(FORMAT_TIME, $time);
        }
        return '';
    }
    
    public function approvedDate() {
        $time = $this->get($this->row, 'modified', array());
        if($time && isset($time['sec'])) {
            return strftime(FORMAT_TIME, $time['sec']);
        }
        return '';
    }
    
    public function invalidDate() {
        $time = $this->get($this->row, 'modified', array());
        if($time && isset($time['sec'])) {
            return strftime(FORMAT_TIME, $time['sec']);
        }
        return '';
    }
    
    public function unavailableDate() {
        $time = $this->get($this->row, 'modified', array());
        if($time && isset($time['sec'])) {
            return strftime(FORMAT_TIME, $time['sec']);
        }
        return '';
    }
    
    public function invalidComment() {
        return $this->get($this->row, 'comment', '');
    }
    
    public function unavailableComment () {
        return $this->get($this->row, 'comment', '');
    }
    
    public function checkoutTotal() {
        return $this->get($this->row, 'checkout_total', 0);
    }
    
    /**
     * Get earn total of voice.
     * 
     * @param array $row
     * @return float The unit is minute. e.g. 23.5 min
     */
    public function earnTotal() {
        $earnTotal = $this->get($this->row, 'earn_total', 0);
        return $this->Time->format($earnTotal);
    }
    
    public function length() {
        $length = $this->get($this->row, 'length', 0);
        return $this->Time->format($length);
    }
    
    public function commentTotal() {
        return $this->get($this->row, 'comment_total', 0);
    }
    
    public function score() {
        $score = $this->get($this->row, 'score', 0);
        if($score) {
            return round($score * 2, 1);
        }
        return round($score, 1);
    }
}

/**
 * The model of coordinate
 *
 */
class Point {
    
    private $lat = 0;
    private $lng = 0;
    
    public function __construct($lat = '', $lng = '') {
        $this->lat = $lat;
        $this->lng = $lng;
    }
    
    public function latitude() {
        return $this->lat;
    }
    
    public function longitude() {
        return $this->lng;
    }
}