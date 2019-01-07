<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * SSO
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-4
 */

class Sso extends Controller {
    function __construct() {
        parent::__construct();
        
    }
    
    /**
     * 设置sso cookie
     */
    function setcookie() {
        header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
         
        $s = rawurldecode($this->get('s'));
        $apps = $this->config->item('apps');
        $salt = $apps[HOST];
        $auth = authcode($s, 'DECODE', $salt);
         
        if($auth) {
            // 去设置用户的COOKIE
            list($id, $nickname, $avatar, $auto_login) = explode("\t", $auth);
            if($id && $nickname) {
                $cookie_expire = $auto_login?(86400 * 365):0;
                $auth = $id . "\t" . $nickname . "\t" . $avatar . "\t" . $auto_login;
                set_cookie('auth', authcode($auth, 'ENCODE'), $cookie_expire);
                if(empty($avatar)){ $avatar = image_url('','head');}
                $this->assign('b', 1);
                $this->assign(compact('id', 'nickname', 'avatar'));
            } else {
                $this->assign('b', 0);
            }
        } else {
            $this->assign('b', 0);
        }
         
        $this->display('setcookie');
    }
    
	/**
	 * 清除sso cookie
	 */
	function clearcookie() {
	    header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	    
	    $s = rawurldecode($this->get('s'));
	    $apps = $this->config->item('apps');
	    $salt = $apps[HOST];
	    $auth = authcode($s, 'DECODE', $salt);
	    
	    if($auth && 'logout' == $auth) {
	        delete_cookie('auth');
	        $this->assign('b', 1);
	    } else {
	        $this->assign('b', 0);
	    }
	    
	    $this->display('clearcookie');
	}
}
