<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 网站主Controller
 * 一些公用的碎片页面
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-4-28
 */

class Download extends MY_Controller {
    
    function index() {
        
        
        $this->display('index');
    }
    
    function test($id = '', $name = '') {
    	
        $this->load->library('api_sender');
        
     /*   $str = 'http://192.168.1.40/private_api/oauth/bind?stage_code=0&uid=83';
        $str = $this->api_sender->des_encrypt($str);
        echo $str.'<p/><p/>';
        echo $this->api_sender->des_decrypt($str);
        exit; */
        
        $params = array(
        	'api'=>'oauth/bind',
        	'attr'=>array('stage_code'=>0,'uid'=>83),
        	'has_return'=>TRUE
        );
        echo 'POST REQUEST<p/>';
        $this->api_sender->post_api($params);
        echo 'GET REQUEST<p/>';
        $this->api_sender->get_api($params);
    }
    
    /**
     * Android
     * Create by 2012-5-21
     * @author liuw
     */
    public function android(){
    	$this->assign('op', 'android');
    	$this->display('android');
    }
    
    /**
     * iPhone
     * Create by 2012-5-21
     * @author liuw
     */
    public function iphone(){
    	$this->assign('op', 'iphone');
    	$this->display('iphone');
    }
    
    /**
     * Windows Phone  
     * Create by 2012-5-21
     * @author liuw
     */
    public function wp(){
    	$this->assign('op', 'wp');
    	$this->display('wp');
    }
        
    /**
     * 跳转转向，便于记录点击数量等等
     */
    function redirect($url = '/') {
        // 暂时
        $url = urldecode($url);
        
        strpos($url, 'http://') === 0 && forward($url);
    }
}
