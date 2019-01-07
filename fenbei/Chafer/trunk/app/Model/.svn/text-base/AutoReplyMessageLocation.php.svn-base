<?php
class AutoReplyMessageLocation extends AppModel {
    
    public $name = 'AutoReplyMessageLocation';
    
    public $belongsTo = array(
        'AutoReplyMessage', 'AutoReplyLocation'
    );
    
    public $validate = array(
        'auto_reply_location_id' => array(
            'required' => array(
        		'rule' => 'notEmpty',
        		'message' => '请选择附加地址'
            )
        )  
    );
}