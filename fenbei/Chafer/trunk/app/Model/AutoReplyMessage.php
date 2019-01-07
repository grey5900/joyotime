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
 * @package       app.Model
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * The model of auto replay message.
 *
 * @package       app.Model
 */
class AutoReplyMessage extends AppModel {

    public $name = 'AutoReplyMessage';
    
    public $actsAs = array('Containable');
    
    public $hasOne = array(
        'AutoReplyMessageCustom',
        'AutoReplyMessageNews',
        'AutoReplyMessageMusic',
        'AutoReplyMessageExlink',
        'AutoReplyMessageLocation',
    );
    
    public $hasMany = array(
    	'AutoReplyMessageTag',
        'AutoReplyFixcodeMessage',
        'AutoReplyLocationMessage',
    );

    public $hasAndBelongsToMany = array(
        'AutoReplyTag' => array(
            'className' => 'AutoReplyTag',
            'joinTable' => 'auto_reply_message_tags',
            'foreignKey' => 'auto_reply_message_id',
            'associationForeignKey' => 'auto_reply_tag_id',
        	'with' => 'AutoReplyMessageTag',
            'unique' => true
        ),
        'AutoReplyLocation' => array(
    		'className' => 'AutoReplyLocation',
    		'joinTable' => 'auto_reply_location_messages',
    		'foreignKey' => 'auto_reply_message_id',
    		'associationForeignKey' => 'auto_reply_location_id',
    		'with' => 'AutoReplyLocationMessage',
    		'unique' => true
        ),
    );
    
    public $validate = array(
        'user_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '需要用户ID'
            )
        ),
        'type' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '请选择消息类型'
            )
        )
    );

/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        // Building structure of tags for AutoReplyMessage to save...
        if(isset($this->data[$this->name]['keywords']) && !empty($this->data[$this->name]['keywords'])) {
            $this->data['AutoReplyMessageTag'] = array();
            foreach(explode(',', $this->data[$this->name]['keywords']) as $i => $item) {
                $this->data['AutoReplyMessageTag'][$i] = array(
                    'AutoReplyTag' => array(
                        'name' => $item 
                    ) 
                );
            }
        }
    }
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
    	$this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    	// Deleting all relationships with tag and message before updating...
    	if(isset($this->data[$this->name]['id']) && isset($this->data[$this->name]['type'])) {
    		$AutoReplyMessageTag = ClassRegistry::init('AutoReplyMessageTag');
    		$AutoReplyMessageTag->deleteAll(array('auto_reply_message_id' => $this->data[$this->name]['id']), false);
    	}
    }
        
/**
 * Plus 1 on field of request_total
 * @param array $auto_reply_message_ids
 */
    public function increaseRequestTotal($auto_reply_message_ids) {
        return $this->updateAll(array(
            'AutoReplyMessage.request_total' => 'AutoReplyMessage.request_total + 1'
        ), array(
            'AutoReplyMessage.id' => $auto_reply_message_ids
        ));
    }
}

?>