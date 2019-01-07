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

class ReceiptHelper extends AppHelper {
    
    public $helpers = array('Html', 'User', 'Time', 'Currency');
    
    private $row = array();
    
    const STATUS_PENDING = 0;
    const STATUS_DONE = 1;
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
    	$this->row = $row;
    	$this->User->init($row['user']);
    	return $this;
    }
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function amount() {
        $amount = $this->get($this->row, 'amount', array());
        $time = $this->get($amount, 'time', 0);
        return $this->Time->format($time);
    }
    
    public function price() {
        $price = $this->get($this->row, 'price', 0);
        if($price) {
            return $this->Currency->sign('CNY').$price;
        }
        return '';
    }
    
    public function exception() {
        $status = $this->get($this->row, 'status', 0);
        if($status == Receipt::STATUS_PRICE_EXCEPTION) {
            return '<span class="price_exception isError ml5">'.__('异常').'</span>';
        }
        return '';
    }
    
    public function type() {
        $type = $this->get($this->row, 'type', '');
        switch($type) {
            case Receipt::TYPE_ALIPAY:
                return __('支付宝');
                break;
            case Receipt::TYPE_IOS:
                return __('APP Store');
                break;
            case Receipt::RECHARGE:
            	return __('二维码充值');
            	break;
            default:
                return __('未知来源');
        }
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
}