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
 * The model of auto replay message music.
 *
 * @package       app.Model
 */
class AutoReplyMessageMusic extends AppModel {
    
    public $name = 'AutoReplyMessageMusic';
    public $tableName = 'auto_reply_message_musics';
    
    public $belongsTo = array(
        'AutoReplyMessage'
    );

    public $validate = array(
        'title' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '标题不能为空' 
            ) 
        ),
        'music_url' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '请填写音乐链接' 
            ) 
        ),
        'description' => array(
            'required' => array(
                'rule' => 'notEmpty',
                'message' => '简介不能为空' 
            ) 
        )
    );
}