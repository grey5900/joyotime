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
 * The model uses to save regexp, 
 * related with thrid-party interface model.
 *
 * @package       app.Model
 */
class AutoReplyEchoRegexp extends AppModel {
    
    public $name = 'AutoReplyEchoRegexp';
    
    public $actsAs = array('Containable');
    
    public $validate = array(
        'regexp' => array(
            'required' => array(
                'rule' => array(
                    'notEmpty' 
                ),
                'message' => '请填写合法的正则表达式' 
            ),
        ) 
    );
}