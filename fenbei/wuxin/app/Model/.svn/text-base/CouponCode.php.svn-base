<?php
class CouponCode extends AppModel {
    
    public $name = 'CouponCode';
    
    public $actsAs = array('Containable');
    
    public $belongsTo = array(
        'Saler', 'Shop'
    );
    
    public $validate = array(
        'saler_id' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少收银员' 
            ),
        ),
        'shop_id' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少店铺' 
            ) 
        ),
        'coupon' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '缺少兑换码' 
            ),
            'repeat' => array(
                'rule' => array('isUnique'),
                'message' => '兑换码重复'
            ) 
        ), 
    );
    
}