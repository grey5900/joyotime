<?php
App::uses('AppHelper', 'View/Helper');
require_once(VENDORS."emoji/emoji.php");
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

class UserHelper extends AppHelper {
    
    public $helpers = array('Html', 'Time');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(&$row = array()) {
        $this->row = $row;
        return $this;
    }
    
    public function username() {
        return emoji_unified_to_html($this->get($this->row, 'username', ''));
    } 
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function avatar() {
        $avatar = $this->get($this->row, 'avatar', array());
        $avatar = $this->get($avatar, 'x80', Configure::read('Default.Avatar'));
        return $this->Html->image($avatar);
    }
    
    public function locale() {
        return $this->get($this->row, 'locale', 'zh_CN');
    }
    
    public function email() {
        return $this->get($this->row, 'email', '');
    }
    
    public function money() {
        $time = $this->get($this->row, 'money', '');
        return $this->Time->format($time);
    }
    
    public function income() {
        $time = $this->get($this->row, 'income', '');
        return $this->Time->format($time);
    }
    
    public function earn() {
        $time = $this->get($this->row, 'earn', '');
        return $this->Time->format($time);
    }
    
    public function cost() {
        $time = $this->get($this->row, 'cost', '');
        return $this->Time->format($time);
    }
    
    public function purchaseTotal() {
        return $this->get($this->row, 'purchase_total', 0);
    }
    
    public function voiceTotal() {
        return $this->get($this->row, 'voice_total', 0);
    }
    
    public function voiceLengthTotal() {
        $time = $this->get($this->row, 'voice_length_total', 0);
        return $this->Time->format($time);
    }
    
    public function sendMessage() {
        return $this->Html->link(__('推送消息'), "#send-message-modal", array(
        		'data-url' => "/users/send_message/".$this->id(),
        		'username' => $this->username(),
        		'class' => 'send-message-link',
        		'data-toggle' => "modal",
        ));
    }
    
    public function sendGift() {
        return $this->Html->link(__('赠送时长'), "#send-gift-modal", array(
        		'data-url' => "/users/send_gift/".$this->id(),
                'username' => $this->username(),
        		'class' => 'send-gift-link',
        		'data-toggle' => "modal",
        ));
    }
}