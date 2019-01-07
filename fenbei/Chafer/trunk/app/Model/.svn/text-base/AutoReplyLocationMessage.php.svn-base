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
 * The model of auto replay message.
 *
 * @package app.Model
 */
class AutoReplyLocationMessage extends AppModel {

    public $name = 'AutoReplyLocationMessage';

    public $actsAs = array(
        'Containable' 
    );

    public $belongsTo = array(
        'AutoReplyLocation',
        'AutoReplyMessage' 
    );

    public $validate = array(
        'auto_reply_message_id' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '请选择一条关联消息' 
            ) 
        ) 
    );
    
    public function beforeSave($options = array()) {
        $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    }
}