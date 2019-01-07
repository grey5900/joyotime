<?php
class Shop extends AppModel {
    
    public $name = 'Shop';
    
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少店铺名字' 
            ),
        ),
    );
    
/**
 * Plus 1 on field of saler_total
 * @param int $shop_id
 */
    public function increaseSalerTotal($shop_id) {
    	return $this->updateAll(array(
    		'Shop.saler_total' => 'Shop.saler_total + 1'
    	), array(
    		'Shop.id' => $shop_id
    	));
    }
    
/**
 * minus 1 on field of saler_total
 * @param int $shop_id
 */
    public function decreaseSalerTotal($shop_id) {
    	return $this->updateAll(array(
    		'Shop.saler_total' => 'Shop.saler_total - 1'
    	), array(
    		'Shop.id' => $shop_id
    	));
    }
    
/**
 * Plus 1 on field of coupon_total
 * @param int $shop_id
 */
    public function increaseCouponTotal($shop_id) {
    	return $this->updateAll(array(
    			'Shop.coupon_total' => 'Shop.coupon_total + 1'
    	), array(
    			'Shop.id' => $shop_id
    	));
    }
    
/**
 * Plus 1 on field of invalid_coupon_total
 * @param int $shop_id
 */
    public function increaseInvalidCouponTotal($shop_id) {
    	return $this->updateAll(array(
    		'Shop.invalid_coupon_total' => 'Shop.invalid_coupon_total + 1'
    	), array(
    		'Shop.id' => $shop_id
    	));
    }
    
/**
 * minus 1 on field of invalid_coupon_total
 * @param int $shop_id
 */
    public function decreaseInvalidCouponTotal($shop_id) {
    	return $this->updateAll(array(
    		'Shop.invalid_coupon_total' => 'Shop.invalid_coupon_total - 1'
    	), array(
    		'Shop.id' => $shop_id
    	));
    }
}