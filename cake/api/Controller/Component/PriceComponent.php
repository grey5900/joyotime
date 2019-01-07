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
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('Component', 'Controller');
/**
 * 
 * @package		app.Controller.Component
 */
class PriceComponent extends Component {
    
/**
 * Return exactly second number by voice length
 * 
 * @param number $length The unit is second.
 * @return int
 */
    public function calc($length) {
        if($length < 0) return 0;
        return $length;
    }
    
/**
 * Calc subsidy for sale
 * 
 * Unit: second
 * 
 * @param int $realPrice
 * @return int The seconds of subsidy
 */
    public function subsidy($realPrice) {
        if(!$realPrice) return 0;
        return ceil($realPrice * Configure::read('Sale.Subsidy.Percent'));
    }
    
/**
 * Return instance of currency
 * 
 * @param string $currency CNY/USD
 * @throws NotFoundException
 * @return Cash
 */
    public function toCash($currency) {
        switch(strtoupper($currency)) {
            case 'CNY':
                $cash = new CNY();
                break;
            case 'USD':
                $cash = new USD();
                break;
            default:
                throw new NotFoundException("Invalid currency");
        }
        return $cash;
    }
    
/**
 * Get fee calculator for payment gateway
 * 
 * @param string $gateway case insensitive,
 *     Available value includes: alipay
 * @throws NotFoundException
 * @return Fee
 */
    public function fee($gateway) {
        $fee = null;
        switch(strtolower($gateway)) {
            case 'alipay': 
                $fee = new AlipayFee();
                break;
            case 'paypal': 
                $fee = new PaypalFee();
                break;
            default:
                throw new NotFoundException("Invalid gateway");
        }
        return $fee;
    }
}

/**
 * All gateways supported.
 *
 */
abstract class Gateway {
    const ALIPAY = 'alipay';
    const PAYPAL = 'paypal';
    
    public static function exist($name) {
        switch(strtolower($name)) {
            case self::ALIPAY:
            case self::PAYPAL:
                return true;
                break;
        }
        return false;
    }
}

/**
 * All handling fee via payment gateway
 *
 */
interface Fee {
    
/**
 * The fee of draw depoite
 * 
 * @param number $amount
 * @return number
 */
    public function draw($amount);
    
/**
 * Get name of gateway
 * 
 * @return string
 */
    public function gateway();
}

class AlipayFee implements Fee {
    
/**
 * (non-PHPdoc)
 * @see Fee::draw()
 */
    public function draw($amount) {
//         $rate = (float) Configure::read('Pay.Fee.Alipay'); // 0.5%
//         $fee = round($amount * $rate, 2);
//         if($fee < 1) {
//             $fee = round(1, 2);
//         } else if($fee > 25) {
//             $fee = round(25, 2);
//         }
//         return $fee;
        return 0;
    }
    
/**
 * (non-PHPdoc)
 * @see Fee::gateway()
 */
    public function gateway() {
        return Gateway::ALIPAY;
    }
}

class PaypalFee implements Fee {
    
/**
 * (non-PHPdoc)
 * @see Fee::draw()
 */
    public function draw($amount) {
        return 0;
    }
    
/**
 * (non-PHPdoc)
 * @see Fee::gateway()
 */
    public function gateway() {
        return Gateway::PAYPAL;
    }
}

/**
 * The class uses to translate available time of fish saying to cash.
 */
interface Cash {
    
    const TYPE_CNY = 'CNY';
    const TYPE_USD = 'USD';
    
/**
 * Get a string as a instance
 * 
 * @return string
 */
    public function toString();
    
/**
 * Get current cash
 * 
 * @return float
 */
    public function calc($second);
    
/**
 * How much for a second?
 * 
 * unit is cent
 * @return float
 */
    public function rate();
    
/**
 * Get signal of currency
 * It looks like `$` as USD or ·￥` as CNY
 * 
 * @return string
 */
    public function currency();
}

class CNY implements Cash {
    
    private $name = 'CNY';
    
    public function toString() {
        return $this->name;
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::calc()
 */
    public function calc($second) {
        $cent = $second * $this->rate();
        return round($cent / 100, 2);
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::rate()
 */
    public function rate() {
        return 0.25;
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::currency()
 */
    public function currency() {
        return '¥';
    }
}

class USD implements Cash {
    
    private $name = 'USD';
    
    public function toString() {
    	return $this->name;
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::calc()
 */
    public function calc($second) {
        $cent = $second * $this->rate();
        return round($cent / 100, 2);
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::rate()
 */
    public function rate() {
        return 0.04125;
    }
    
/**
 * (non-PHPdoc)
 * @see Cash::currency()
 */
    public function currency() {
    	return '$';
    }
}