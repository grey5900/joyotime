<?php
class AutoReplyMessageCustom extends AppModel {
    
    public $name = 'AutoReplyMessageCustom';
    
    public $belongsTo = 'AutoReplyMessage';
    
    public $validate = array(
        'custom_content' => array(
            'required' => array(
        		'rule' => 'notEmpty',
        		'message' => '自定义内容不能为空'
            )
        )  
    );
}