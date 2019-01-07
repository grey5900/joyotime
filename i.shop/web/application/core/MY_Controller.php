<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 所有Controller基类
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-12
 */

date_default_timezone_set('Asia/Chongqing');
 
class MY_Controller extends CI_Controller {
    // 模板变量
    var $template;
    // 时间戳
    var $timestamp;
    // 用户登录的COOKIE
    var $auth;
    // 类
    var $_c = '';
    // 方法
    var $_m = '';
    // 目录
    var $_d = '';
    //商家信息
    var $brand;

    function __construct() {
        parent::__construct();
        // 初始化模板
        $this->template->setOptions($this->config->item('template'));
        $this->template->assign('current_url', current_url());
        $this->template->assign('current', $this->_c.'_'.$this->_m);
        $this->timestamp = now(-1);
        
        list($id, $name, $brand_id, $brand_name) = explode("\t", authcode(dgetcookie($this->config->item('cookie_name')), 'DECODE', $this->config->item('authcode_key')));
        if(!(('cache' == $this->router->class && 'clear_cache' == $this->router->method) || ('web' == $this->router->class && strpos($this->router->method, 'upload') !== false))) {
            if('web' == $this->router->class && 'login' == $this->router->method) {
                // 登录页面在
                // 已登录直接跳进INDEX
                $id > 0 && redirect('/');
            } else {
                empty($id) && redirect('/login'); 
            }
        }
        
        $this->auth = compact('id', 'name', 'brand_id', 'brand_name');
        $this->assign('auth', $this->auth);
        // 当前页面的ID 由CLASS和METHOD组成        
        $this->assign('page_id', $this->router->class . '_' . $this->router->method);
        //商家信息
        $this->brand = $this->db->where('id', $this->auth['brand_id'])->get('Brand')->first_row('array');
        //检查商家是否过期
        if('web' != $this->router->class && 'login' != $this->router->method && $this->brand['status'] != 0){
        	//清理COOKIE
        	dsetcookie($this->config->item('cookie_name'), '', -1);
        	if(!$this->input->is_ajax_request())
        		redirect('/login', 'location', 301);
        	else 
        		$this->echo_json(array('code'=>0, 'msg'=>'对不起！非认证商家不能使用商家平台', 'refer'=>'/login'));
        }
        $this->assign('brand', $this->brand);
        $this->assign('css_v', $this->config->item('css_version'));
		$cfg = $this->config->item('image_cfg');
		$this->assign('upload_path', $cfg['upload_view']);
		//日期控件使用的参数，最小可选日期
		$now = time()+32*3600;
		$min_date = gmdate('Y-m-d', $now);
		list($year, $month, $day) = explode('-', $min_date);
		$month = intval($month)-1;
		
		$this->assign(compact('year', 'month', 'day'));
    }
    
    /**
     * 检查是否登录，未登录的跳转到登录页
     * Create by 2012-9-26
     * @author liuw
     */
    function is_login(){
        list($id, $name, $brand_id, $brand_name) = explode("\t", authcode(dgetcookie($this->config->item('cookie_name')), 'DECODE', $this->config->item('authcode_key')));
        if(!isset($id) || empty($id) || !$id)
        	redirect('/login');
    }

    /**
     * 设置模板的变量内容
     * @param $key mixed
     * @param $value
     */
    function assign($key, $value = null) {
        $this->template->assign($key, $value);
    }

    /**
     * 显示模板
     * @param $t_name 模板名称
     * @param $group 模板组
     */
    function display($t_name, $group = '') {
        $group = empty($group) ? $this->router->class : $group;
        $this->template->display($t_name, $group);
        exit(0);
    }

    /**
     * 返回模板内容
     * @param $t_name 模板名称
     * @param $group 模板组
     * @return 返回 string
     */
    function fetch($t_name, $group = '') {
        $group = empty($group) ? $this->router->class : $group;
        return $this->template->fetch($t_name, $group);
    }
    
    /**
     * 获取get数据
     * @param $key
     * @return string
     */
    function get($key) {
        return $this->input->get($key);
    }

    /**
     * 获取post数据
     * @param $key
     * @return string
     */
    function post($key) {
        return $this->input->post($key);
    }

    /**
     * 获取server数据
     * @param $key
     * @return string
     */
    function server($key) {
        return $this->input->server($key);
    }

    /**
     * 获取cookie数据
     * @param $key
     * @return string
     */
    function cookie($key) {
        return $this->input->cookie($key);
    }

    /**
     * 是否为POST数据
     */
    function is_post() {
        return 'POST' == $this->server('REQUEST_METHOD');
    }

    /**
     * 输出json数据
     */
    function echo_json($arr = array(), $callback = '') {
        die($callback?($callback . '(' . json_encode($arr) . ')'):(json_encode($arr)));
    }
    
	/**
	 * 向api发送请求并接收返回值
	 * Create by 2012-6-18
	 * @author liuw
	 * @param array $params
	 * @param string $method
	 * @return string 
	 */
	function call_api($params, $method = 'post'){
	   $api_uri = $this->config->item('api_serv') . $this->config->item('api_folder') . $params['api'];
	   $content = send_api_interface($api_uri, $method, $params['attr']);
	   return $params['has_return'] ? $content : TRUE;
	}  

	/**
	 * 提示消息
	 * 
	 */
	function showmessage($message, $url_forward = '', $timeout = 3, $inajax = false) {
	    $this->assign(compact('message', 'url_forward'));
	    $this->display('showmessage', 'web');
	}
}
