<?php
class User extends AppModel {
    
    public $name = 'User';
    
    public $validate = array(
        'username' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请填写多地点标题' 
            ),
            'unique' => array(
                'rule' => array(
                    'isUnique' 
                ),
                'message' => '用户名已存在' 
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请输入密码' 
            ) 
        ),
        'name' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请填写账号名称' 
            ) 
        ), 
    );
    
}