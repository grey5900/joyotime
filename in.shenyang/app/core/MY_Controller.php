<?php
session_start();
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 所有Controller的基类
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-8
 */

class MY_Controller extends CI_Controller {
    // 用户的登录信息
    var $auth = array();
    var $_uri = '';
    var $_rel = '';
    var $_c = '';
    // 类
    var $_m = '';
    // 方法
    var $_d = '';
    // 目录
    var $_index_uri = '';
    var $_index_rel = '';
    var $_self_uri = '';

    function __construct() {
        parent::__construct();
        $this->template->setOptions($this->config->item('template'));
        $this->template->setOptions(array('languages' => $this->lang->language));

        $this->template->assign('current_url', current_url());

        // 获取用户的登录信息
        // 保存 id,name,truename,description,rights,role
        // $this->auth = $this->session->userdata($this->config->item('sess_auth'));
        $this->auth = $_SESSION[$this->config->item('sess_auth')];

        $this->_m = $this->router->method;
        $this->_d = $this->router->directory;
        $this->_c = $this->router->class;

        // 获取地址中键值对应参数
        $arr = $this->uri->uri_to_assoc(3 + ($this->_d ? substr_count($this->_d, '/') : 0));
        $_GET = array_merge($_GET, $arr);
        
        $this->_uri = "/" . $this->_d . $this->_c . '/' . $this->_m . ($this->input->get('do') ? '/do/' . $this->input->get('do') : '');
        $this->_rel = substr(strtr($this->_uri, '/', '_'), 1);
        $this->_index_uri = "/" . $this->_d . $this->_c . '/index';
        $this->_index_rel = substr(strtr($this->_index_uri, '/', '_'), 1);
        $this->template->assign('self_uri', $this->_uri);
        $this->page_id = strtr(substr($this->_uri, 1), '/', '_');
        $this->template->assign('page_id', $this->page_id);
        // var_dump($this->_c);
        
        $this->_tables = $this->config->item('tables');
        $this->db2 = $this->load->database('db2', true);
        
        $this->template->assign('editor_image', "upImgUrl=\"" . site_url(array(
                'main',
                'upload'
        )) . "\" upImgExt=\"jpg,jpeg,gif,png\"");
    }

	
	/**
	 * 切换数据库
	 */
	function change_db() {
	    $tmp = $this->db;
	    $this->db = $this->db2;
	    $this->db2 = $tmp;
	    unset($tmp);
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
        $group = empty($group) ? $this->_c : $group;
        $this->template->display($t_name, $group);
    }

    /**
     * 返回模板内容
     * @param $t_name 模板名称
     * @param $group 模板组
     * @return 返回 string
     */
    function fetch($t_name, $group = '') {
        $group = empty($group) ? $this->_c : $group;
        return $this->template->fetch($t_name, $group);
    }

    /**
     * 获取get数据
     * @param $key
     * @param $default 默认值
     * @return string
     */
    function get($key, $default = false) {
        $value = $this->input->get($key);
        $v = empty($value)?(false===$default?$value:$default):$value;
        return daddslashes($v);
    }

    /**
     * 获取post数据
     * @param $key
     * @param $default 默认值
     * @return string
     */
    function post($key, $default = false) {
        $value = $this->input->post($key);
        $v = empty($value)?(false===$default?$value:$default):$value;
        return daddslashes($v);
    }

    /**
     * 获取cookie数据
     * @param $key
     * @return string
     */
    function cookie($key) {
        $v = $this->input->cookie($key);
        return daddslashes($v);
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
     * 返回成功
     */
    function success($message, $navTabId = '', $forwardUrl = '', $callbackType = '', $value = '') {
        $this->message('200', $message, $navTabId, $forwardUrl, $callbackType, $value);
    }

    /**
     * 错误
     */
    function error($message, $navTabId = '', $forwardUrl = '', $callbackType = '', $value = '') {
        $this->message('300', $message, $navTabId, $forwardUrl, $callbackType, $value);
    }

    /**
     * 超时
     */
    function timeout($message, $navTabId = '', $forwardUrl = '', $callbackType = '', $value = '') {
        $this->message('301', $message, $navTabId, $forwardUrl, $callbackType, $value);
    }

    /**
     * 消息
     */
    function message($statusCode, $message, $navTabId = '', $forwardUrl = '', $callbackType = '', $value = '') {
        $arr = compact('statusCode', 'message');
        $navTabId && $arr['navTabId'] = $navTabId;
        $forwardUrl && $arr['forwardUrl'] = $forwardUrl;
        $callbackType && $arr['callbackType'] = $callbackType;
        $value && $arr['value'] = $value;
        $this->assign('str', json_encode($arr));
        $this->display('return', 'main');
    }

    /**
     * 显示提示消息
     * @param $message 消息内容
     * @param $url_forward 跳转页面
     * @param $timeout 停留秒数
     */
    function showmessage($message, $url_forward = '', $timeout = 3) {
        $this->assign(compact('message', 'url_forward', 'timeout'));
        $this->display('showmessage', 'main');
    }

    /**
     * 初始一些翻页的操作
     */
    function paginate($total_num, $cur_page = 0, $per_page_num = 0, $page_shown = 0) {
        // 导入系统的基本配置信息
        $common_setting = get_data('common_setting');

        if (0 === $per_page_num) {
            $per_page_num = intval($this->post('numPerPage'));
        //    $per_page_num = 5;
            if ($per_page_num <= 0) {
                $per_page_num = $common_setting['per_page_num'] ? $common_setting['per_page_num'] : 20;
            }
        }
        if (0 === $cur_page) {
            $cur_page = intval($this->post('pageNum'));
            if ($cur_page <= 0) {
                $cur_page = 1;
            }
        }
        if (0 === $page_shown) {
            // 去读取配置
            $page_shown = $common_setting['page_shown'] ? $common_setting['page_shown'] : 20;
        }

        $this->assign('total_num', $total_num);
        $this->assign('per_page_num', $per_page_num);
        $this->assign('cur_page', $cur_page);
        $this->assign('page_shown', $page_shown);
        
        $offset = $per_page_num * ($cur_page - 1);
        return compact('total_num', 'per_page_num', 'cur_page', 'page_shown', 'offset');
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
}
