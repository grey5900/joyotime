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
 * The model class is used to manage all images uploaded by users.
 *
 * @package       app.Model
 */
class ImageAttachment extends AppModel {
    
    public $name = 'ImageAttachment';
    
    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '标题不能为空'
            )
        ),
        'user_id' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '需要用户ID'
            )
        ),
        'size' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '图片大小不能为空'
            )
        ),
        'type' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '图片附件类型不能为空'
            )
        ),
        'original_url' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '原始图片链接不能为空'
            )
        ),
        'thumbnail_url' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '缩略图链接不能为空'
            )
        ),
        'delete_url' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '删除图片链接不能为空'
            )
        ),
        'delete_type' => array(
            'required' => array(
                'rule' => array('notEmpty'),
                'message' => '删除图片类型不能为空'
            )
        ),
    ); 
    
/**
 * (non-PHPdoc)
 * @see Model::beforeSave()
 */
    public function beforeSave($options = array()) {
    	$this->data[$this->name]['user_id'] = CakeSession::read('Auth.User.id');
    }
}