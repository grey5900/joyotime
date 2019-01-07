<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public function beforeFilter() {
        $this->setLanguage();
        $this->chkPlatform();
    }

    /**
     * Set language for current user
     *
     * @return boolean
     */
    private function setLanguage() {
        if($this->Session->check('Config.language')) {
            $lang = $this->Session->read('Config.language');
            Configure::write('Config.language', $lang);
            return true;
        } else {
            $userAgent = $this->request->header('User-Agent');
            if(stristr($userAgent, 'Android')) {
                $lang = $this->request->query('language');
            } else if(stristr($userAgent, 'iPhone') || stristr($userAgent, 'iPod') || stristr($userAgent, 'iPad')) {
                $lang = $this->request->query('language');
            } else {
                $lang = $this->request->header('Accept-Language');
            }
            
            if(!$lang) {
                $lang = $this->request->header('Accept-Language');
            }
            
            if($lang) {
                $langs = explode(',', $lang);
                $primary = $langs[0];
                if(stristr($primary, 'zh')) {
                    Configure::write('Config.language', 'chn');
                    return true;
                }
            }
        }
        Configure::write('Config.language', 'eng');
    }

    private function chkPlatform() {
        $userAgent = $this->request->header('User-Agent');
        
        if(stristr($userAgent, 'MicroMessenger')) {
            Configure::write('Platform.Weixin', true);
        }
    }
    
    protected function fail($message) {
    	return json_encode(array('result' => false, 'message' => $message));
    }
    
    protected function success($data = array()) {
    	$info = array('result' => true);
    	if($data) $info = am($info, $data);
    	return json_encode($info);
    }
}
