<?php
/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {
    
/**
 * Output rel tag if request come from weixin in the time
 * 
 * @return string
 */
    public function relWeixin() {
        if(Configure::read('Platform.Weixin')) {
            return 'rel="weixin"';
        } 
        return '';
    }
    
/**
 * Output language what is using now.
 * All name is consisted with lowercase
 * 
 * @return string
 */
    public function language() {
        return strtolower(Configure::read('Config.language'));
    }
    
    public function get(&$row = array(), $key, $default = '') {
    	if(isset($row[$key])) {
    		return $row[$key];
    	}
    	return $default;
    }
}
