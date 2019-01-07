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
 * The model class of auto reply category.
 *
 * @package       app.Model
 */
class AutoReplyCategory extends AppModel {
    
    public $name = 'AutoReplyCategory';

    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '需要分类名称'
            ),
            'unique' => array(
                'rule' => array('titleIsUnique'),
                'message' => '这个分类已经存在了，无法重复创建'
            )
        ),
        'user_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '需要用户ID'
            )
        )
    ); 
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
        $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    }
    
/**
 * It will update all category_id of
 * message under the category before delete
 *
 * (non-PHPdoc)
 * @see Model::beforeDelete()
 */
    public function beforeDelete($cascade = true) {
    	$AutoReplyMessageNews = ClassRegistry::init('AutoReplyMessageNews');
    	$AutoReplyMessageNews->updateAll(array(
    		'auto_reply_category_id' => null
    	), array(
    		'auto_reply_category_id' => $this->id
    	));
    }
    
/**
 * Increase total of category
 * 
 * @param int $id The primary key of category
 * @return boolean
 */
    public function increase($id) {
        return $this->updateAll(array(
        	'AutoReplyCategory.total' => 'AutoReplyCategory.total + 1'
        ), array(
        	'AutoReplyCategory.id' => $id
        ));
    }
    
/**
 * Increament total of category
 * 
 * @param int $id The primary key of category
 * @return boolean
 */
    public function decrease($id) {
        return $this->updateAll(array(
        	'AutoReplyCategory.total' => 'AutoReplyCategory.total - 1'
        ), array(
        	'AutoReplyCategory.id' => $id
        ));
    }
    
/**
 * Increament total of category by auto reply message id
 * 
 * @param int $auto_reply_message_id The primary key of auto_reply_messages
 * @return boolean
 */
    public function decreaseByAutoReplyMessageId($auto_reply_message_id) {
        if($auto_reply_message_id) {
            $AutoReplyMessageNews = ClassRegistry::init('AutoReplyMessageNews');
        	$news = $AutoReplyMessageNews->find('first', array(
    			'conditions' => array('auto_reply_message_id' => $auto_reply_message_id),
    			'recursive' => -1
        	));
        	if($news && isset($news['AutoReplyMessageNews']['auto_reply_category_id'])) {
        		return $this->decrease(
        		        $news['AutoReplyMessageNews']['auto_reply_category_id']);
        	}
        }
        return false;
    }
    
    
    
/**
 * Implements validate callback
 *  
 * @param string $check
 * @return boolean return true if $check is valid.
 */
    public function titleIsUnique($check) {
        $exist = $this->find('count', array(
            'conditions' => array(
                'title' => $check,
                'user_id' => CakeSession::read('Auth.User.id')
            )
        ));
        if($exist > 0) return false;
        return true;
    }
    
/**
 * Get a list of category according with userid.
 * 
 * @param int $userId
 * @param boolean $nonCate
 * @return array
 */
    public function getList($userId, $nonCate = true) {
        $data =  $this->find('list', array(
    		'fields' => array('AutoReplyCategory.id', 'title'),
    		'conditions' => array(
    			'user_id' => $userId
    		)
        ));
        $cate = array();
        if($nonCate) {
            $cate[-1] = '未分类';
        }
        if($data && is_array($data)) {
            foreach($data as $id => $title) {
                $cate[$id] = $title;
            }
        }
        return $cate;
    }
}