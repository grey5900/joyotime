<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The thrid-party interface model.
 *
 * @package       app.Model
 */
class AutoReplyEcho extends AppModel {
    
    public $name = 'AutoReplyEcho';
    
    public $actsAs = array('Containable');
    
    public $hasMany = array(
        'AutoReplyEchoRegexp' => array(
            'className'     => 'AutoReplyEchoRegexp',
            'foreignKey'    => 'auto_reply_echo_id',
            'dependent'     => true
        )
    );
    
    public $validate = array(
        'user_id' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '需要用户ID' 
            ) 
        ),
        'url' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请填写接口地址' 
            ),
            'link' => array(
                'rule' => array('url'),
                'message' => '请填写合法的链接地址'
            )
        ),
        'enabled_location' => array(
            'rule' => array('isLocationUnique'),
            'message' => '只能有一个转发地址选择地理位置选项'
        )
    );
    
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        // Remove all regexp first before saving...
        if(isset($this->data[$this->name]['id'])) {
            $this->AutoReplyEchoRegexp->deleteAll(
                    array('auto_reply_echo_id' => $this->data[$this->name]['id']));
            // Reset tags before saving/updating...
            $this->updateAll(array('enabled_regexp' => 0, 'enabled_location' => 0), 
                    array('id' => $this->data[$this->name]['id']));
        }
    } 
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
    	if(CakeSession::read('Auth.User.id')){
    		$this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    	}
    }
    
/**
 * Check whether someone has already enabled location or not.
 * @param int $userId
 * @return number
 */
    public function isLocationEnabled($userId, $auto_reply_echo_id = 0) {
        $exist = $this->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'enabled_location' => 1
            )
        ));
        return $exist && $exist[$this->name]['id'] != $auto_reply_echo_id ? true : false;
    }
    
/**
 * Implemented validate callback
 * 
 * @param string $check
 * @return boolean returns true if none of location checkboxes is selected.
 */
    public function isLocationUnique($check = '') {
        if(isset($this->data[$this->name]['id'])) {
            return !$this->isLocationEnabled(
                    CakeSession::read('Auth.User.id'), $this->data[$this->name]['id']);
        } else {
            return !$this->isLocationEnabled(
            		CakeSession::read('Auth.User.id'));
        }
    }
    
/**
 * Plus 1 on sent_num
 *
 * @param array $ids
 */
    public function increaseSentNum($ids) {
    	return $this->updateAll(array(
    		'AutoReplyEcho.sent_num' => 'AutoReplyEcho.sent_num + 1'
    	), array(
    		'AutoReplyEcho.id' => $ids
    	));
    }
}