<?php
class AutoReplyMessageExlink extends AppModel {
    
    public $name = 'AutoReplyMessageExlink';
    
    public $belongsTo = 'AutoReplyMessage';
    
    public $validate = array(
        'exlink' => array(
            'required' => array(
        		'rule' => 'notEmpty',
        		'message' => '请填写外链链接'
            )
        )  
    );
}