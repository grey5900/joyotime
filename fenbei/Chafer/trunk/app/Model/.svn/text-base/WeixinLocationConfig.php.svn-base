<?php
class WeixinLocationConfig extends AppModel {
    
    public $name = 'WeixinLocationConfig';
    
    const TYPE_MULTIPLY = 'multiply';
    const TYPE_SINGLE = 'single';
    
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请填写多地点标题'
            )
        ),
        'weixin_config_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请先填写并保存基本资料'
            )
        ),
        'image_attachment_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请上传多地点封面图片'
            )
        ),
        'type' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请选择地理位置的回复形式'
            )
        ),
    );
    
    public $belongsTo = array(
        'WeixinConfig', 
        'ImageAttachment'
    );
    
    public function beforeSave($options = array()) {
        $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    }
}