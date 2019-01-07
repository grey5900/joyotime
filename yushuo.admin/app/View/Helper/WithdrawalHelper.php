<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Withdrawal', 'Model');
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

class WithdrawalHelper extends AppHelper {
    
    public $helpers = array('Html', 'Time', 'Currency', 'User');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return WithdrawalHelper
     */
    public function init(&$row = array()) {
        $this->row = $row;
        return $this;
    }
    
    public function amount() {
        $amount = $this->get($this->row, 'amount');
        if($amount && is_array($amount) && isset($amount['time'])) {
            return $this->Time->format($amount['time']);
        }
        return 0;
    }
    
    public function money() {
        $amount = $this->get($this->row, 'amount');
        if($amount && is_array($amount) && isset($amount['money']) && isset($amount['currency'])) {
            return $this->Currency->sign($amount['currency']).$amount['money'];
        }
        return '';
    }
    
    public function account() {
        $amount = $this->get($this->row, 'amount');
        if($amount && is_array($amount) 
            && isset($amount['account']) 
            && isset($amount['gateway'])) {
            if($amount['gateway'] == Withdrawal::GATEWAY_ALIPAY) {
                return $amount['gateway'].'<br />'.$amount['account'].'<br />'.$this->get($amount, 'realname');
            } else {
                return $amount['gateway'].'<br />'.$amount['account'];
            }
        }
        return '';
    }
    
    public function user() {
        $user = $this->get($this->row, 'user');
        return $this->User->init($user);
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
    
    public function modified() {
        $modified = $this->get($this->row, 'modified', array());
        if($modified && isset($modified['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $modified['sec']);
        }
        return '';
    }
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function status() {
    	$processed = $this->get($this->row, 'processed', 0);
    	switch(intval($processed)) {
    		case Withdrawal::PROCESSED:
    			return '<span class="processed-color">'.__('转账成功').'</span>';
    			break;
    		case Withdrawal::REVERTED:
    			return '<span class="reverted-color">'.__('已驳回').'</span>';
    			break;
    	}
    	return '';
    }
    
    public function revert() {
        $id = $this->id();
        return $this->Html->link(__('驳回'), "#withdrawal-revert-modal", array(
    		'data-url' => "/withdrawals/revert/{$id}",
    		'class' => 'withdrawal-revert-link',
    		'data-toggle' => "modal",
        ));
    }
}