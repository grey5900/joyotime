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
 * @package       app.Model
 */
class AutoReplyTag extends AppModel {
    
    public $name = 'AutoReplyTag';
    
    public $hasMany = array(
        'AutoReplyMessageTag'
    );

    public $hasAndBelongsToMany = array(
        'AutoReplyMessage' => array(
            'className' => 'AutoReplyMessage',
            'joinTable' => 'auto_reply_message_tags',
            'foreignKey' => 'auto_reply_tag_id',
            'associationForeignKey' => 'auto_reply_message_id',
            'unique' => true 
        ) 
    );
    
/**
 * (non-PHPdoc)
 * @see Model::beforeValidate()
 */
    public function beforeValidate($options = array()) {
        $this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
        // Try to get tag id if existed already.
        if(!isset($this->data[$this->name]['id']) && empty($this->data[$this->name]['id'])) {
            $this->data[$this->name]['id'] = $this->getIdByName($this->data[$this->name]['name']);
        }
    }
    
/**
 * Exactly equation
 * @param string $name
 * @return number
 */
    public function getIdByName($name) {
        $row = $this->find('first', array(
            'conditions' => array(
                'name' => $name
            ),
            'recursive' => -1
        ));
        if($row) {
            return $row[$this->name]['id'];
        }
        return 0;
    }
}