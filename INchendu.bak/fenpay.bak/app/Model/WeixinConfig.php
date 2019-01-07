<?php
class WeixinConfig extends AppModel {
    
    public $name = 'WeixinConfig';
    
    public $actsAs = array('Containable');
    
    public $validate = array(
        'name' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请填写公众号名称'
            )
        ),
        'weixin_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请填写微信号'
            ),
            'unique' => array(
                'rule' => array('isUniqueWeixinId'),
                'message' => '此微信号已存在',
            ),
        ),
    );
    
    public $hasOne = array(
        'WeixinLocationConfig'        
    );
    
    public function beforeSave($options = array()) {
        if(CakeSession::read('Auth.User.id')) {
            $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
        }
        if(!empty($this->data['WeixinConfig']['weixin_id'])) {
            // create interface url
            $this->data[$this->name]['interface'] = 
                Configure::read('Domain.main').'/api/'.
                    Inflector::underscore($this->data['WeixinConfig']['weixin_id']);
            // create token
            $this->data[$this->name]['token'] = 
                Inflector::underscore($this->data['WeixinConfig']['weixin_id']);
        }
    }
    
/**
 * Increase 1 to user num 
 * 
 * @param int $userId
 * @return boolean
 */
    public function increaseUserNum($userId) {
        $config = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId
            ),
            'recursive' => -1
        ));
        if(!empty($config)) {
            $config['WeixinConfig']['initial_user_num'] += 1;
            return (boolean) $this->save($config);
        }
        return false;
    }
    
/**
 * Decrease 1 to user num 
 * 
 * @param int $userId
 * @return boolean
 */
    public function decreaseUserNum($userId) {
        $config = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId
            ),
            'recursive' => -1
        ));
        if($config && $config['WeixinConfig']['initial_user_num'] > 0) {
            $config['WeixinConfig']['initial_user_num'] -= 1;
            return (boolean) $this->save($config);
        }
        return false;
    }
    
/**
 * Implement callback of validate
 * 
 * @param string $check
 */
    public function isUniqueWeixinId($check) {
        $userId = CakeSession::read('Auth.User.id');
        if($userId) {
            $config = $this->find('first', array(
                'conditions' => array(
                    'user_id' => $userId,
                    'weixin_id' => $check
                ),
                'recursive' => -1
            ));
            if($config) {
                return true;
            } else {
                $config = $this->find('first', array(
                    'conditions' => array(
                        'weixin_id' => $check 
                    ),
                    'recursive' => -1 
                ));
                if($config) return false;
                else return true;
            }
        }
        return false;
    } 
}