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
 * The model of auto replay fixcode.
 * The message of fixcode is higher priority than AutoReplyMessage.
 *
 * @package       app.Model
 */
class AutoReplyFixcode extends AppModel {

    public $name = 'AutoReplyFixcode';
    
    const EVT_SUBSCRIBE = 'subscribe';
    const EVT_NOANSWER  = 'noanswer';
    
    public $actsAs = array('Containable');
    
    public $hasMany = array(
    	'AutoReplyFixcodeKeyword',
        'AutoReplyFixcodeMessage'
    );

    public $hasAndBelongsToMany = array(
        'AutoReplyKeyword' => array(
            'className' => 'AutoReplyKeyword',
            'joinTable' => 'auto_reply_fixcode_keywords',
            'foreignKey' => 'auto_reply_fixcode_id',
            'associationForeignKey' => 'auto_reply_keyword_id',
        	'with' => 'AutoReplyFixcodeKeyword',
            'unique' => true
        ),
        'AutoReplyMessage' => array(
    		'className' => 'AutoReplyMessage',
    		'joinTable' => 'auto_reply_fixcode_messages',
    		'foreignKey' => 'auto_reply_fixcode_id',
    		'associationForeignKey' => 'auto_reply_message_id',
    		'with' => 'AutoReplyFixcodeMessage',
    		'unique' => true,
        ),
    );
    
    public $validate = array(
        'user_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '需要用户ID'
            )
        ),
        'content' => array(
            'required' => array(
                'rule' => array('contentRequired'),
                'message' => '请填写文本内容或者选择图文消息'
            )
        )
    );

/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        if(CakeSession::read('Auth.User.id')) {
        	$this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
        }
        
        $this->deleteKeywordsByFixcodeId();
        $this->deleteTextMessage();
        
        // Removed relationships with fixcode and message...
        if(isset($this->data[$this->name]['id'])) {
        	$model = ClassRegistry::init('AutoReplyFixcodeMessage');
        	$model->deleteAll(array('auto_reply_fixcode_id' => $this->data[$this->name]['id']), false);
        }
        // Removed relationships with fixcode and keyword...
        if(isset($this->data[$this->name]['id'])) {
        	$model = ClassRegistry::init('AutoReplyFixcodeKeyword');
        	$model->deleteAll(array('auto_reply_fixcode_id' => $this->data[$this->name]['id']), true);
        }
        
        $this->buildKeyword();
        $this->buildSelectedMessage();
        
        $this->resetFlag('subscribe');
        $this->resetFlag('noanswer');
    }

    /**
     * Building structure of tags for AutoReplyMessage to save...
     */
    private function buildKeyword() {
        if(isset($this->data[$this->name]['keywords']) && !empty($this->data[$this->name]['keywords'])) {
            $this->data['AutoReplyFixcodeKeyword'] = array();
            foreach(explode(',', $this->data[$this->name]['keywords']) as $i => $item) {
                $this->data['AutoReplyFixcodeKeyword'][$i] = array(
                    'AutoReplyKeyword' => array(
                        'name' => $item 
                    ) 
                );
            }
        }
    }
    
    /**
     * When multiply related news, should construct 
     * specify array for saving.
     */
    private function buildSelectedMessage() {
        if(isset($this->data['AutoReplyFixcode']['selected_messages'])) {
        	if(is_array($this->data['AutoReplyFixcode']['selected_messages'])) {
        		foreach($this->data['AutoReplyFixcode']['selected_messages'] as $idx => $id) {
        			$this->data['AutoReplyFixcodeMessage'][$idx]['AutoReplyMessage']['id'] = $id;
        		}
        		unset($this->data['AutoReplyFixcode']['selected_messages']);
        	}
        }
    }

    /**
     * While edting, should delete text message first 
     * before save switching to news type.
     */
    private function deleteTextMessage() {
        if(isset($this->data['AutoReplyFixcode']['id']) && !empty($this->data['AutoReplyFixcode']['id'])) {
            $AutoReplyMessage = ClassRegistry::init('AutoReplyMessage');
            $relation = $AutoReplyMessage->AutoReplyFixcodeMessage->find('first', array(
                'conditions' => array(
                    'auto_reply_fixcode_id' => $this->data['AutoReplyFixcode']['id'] 
                ) 
            ));
            if($relation) {
                $message = $AutoReplyMessage->find('first', array(
                    'conditions' => array(
                        'AutoReplyMessage.id' => $relation['AutoReplyFixcodeMessage']['auto_reply_message_id'] 
                    ),
                    'recursive' => -1 
                ));
                if($message && $message['AutoReplyMessage']['type'] == 'text') {
                    $AutoReplyMessage->delete($message['AutoReplyMessage']['id']);
                }
            }
        }
    }

    /**
     * @param AutoReplyFixcodeKeyword $model
     */
    private function deleteKeywordsByFixcodeId() {
        debug($this->data[$this->name]['id']);
        if(isset($this->data[$this->name]['id'])) {
            $model = ClassRegistry::init('AutoReplyFixcodeKeyword');
            $data = $model->find('all', array(
                'conditions' => array(
                    'auto_reply_fixcode_id' => $this->data[$this->name]['id'] 
                ),
                'recursive' => -1 
            ));
            $AutoReplyKeyword = ClassRegistry::init('AutoReplyKeyword');
            $ids = Hash::extract($data, '{n}.AutoReplyFixcodeKeyword.auto_reply_keyword_id');
            debug($data);
            debug($ids);
            foreach($ids as $id) {
                $AutoReplyKeyword->delete($id);
            }
        }
//         exit;
    }

    /**
     * Reset subscribe mark because it allows only one 
     * subscribe message for each account.
     * 
     * @param string $name The flag name or type.
     * The avaliable value are subscribe and noanswer.
     */
    private function resetFlag($name) {
        if(isset($this->data[$this->name][$name]) && $this->data[$this->name][$name]) {
            $this->updateAll(array(
                "AutoReplyFixcode.$name" => 0 
            ), array(
                'AutoReplyFixcode.user_id' => CakeSession::read('Auth.User.id') 
            ));
        }
    }
    
/**
 * Check whether content has been set or not.
 * 
 * @param string $check
 * @return boolean
 */
    public function contentRequired($check) {
        if(!isset($this->data['AutoReplyFixcodeMessage'][0]['AutoReplyMessage']['description'])) {
            return false;
        }
        if(empty($this->data['AutoReplyFixcodeMessage'][0]['AutoReplyMessage']['description'])) {
        	return false;
        }
        return true;
    }

/**
 * Plus 1 on field of request_total
 * 
 * @param array $ids            
 */
    public function increaseRequestTotal($ids) {
        return $this->updateAll(array(
            'AutoReplyFixcode.request_total' => 'AutoReplyFixcode.request_total + 1' 
        ), array(
            'AutoReplyFixcode.id' => $ids 
        ));
    }
}

?>