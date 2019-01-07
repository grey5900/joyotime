<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 基类Controller
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-4-28
 */

class MY_Controller extends CI_Controller {
    // 类
    var $_c = '';
    // 方法
    var $_m = '';
    // 目录
    var $_d = '';
    // 当前用户
    var $auth = array();
    // ajax 请求
    var $ajax = false;

    function __construct() {
        parent::__construct();
        
        $this->_m = $this->router->method;
        $this->_d = $this->router->directory;
        $this->_c = $this->router->class;
        
        $this->ajax = $this->input->is_ajax_request();
        
        // 如果是AJAX访问。那么不需要模板，及JS。CSS
        $this->template->setOptions($this->config->item('template'));
        $this->template->assign('current_url', current_url());
        $this->template->assign('current', $this->_c.'_'.$this->_m);
        $this->template->assign('css_version', strtoupper(md5($this->config->item('css_version'))));
            
        if(!$this->ajax) {
            $this->template->setOptions(array('languages' => $this->lang->language));
            // 获取页面的meta信息
            $title = $this->lang->line($this->_c . '_' . $this->_m . '_title');
            $keywords = $this->lang->line($this->_c . '_' . $this->_m . '_keywords');
            $desc = $this->lang->line($this->_c . '_' . $this->_m . '_description');
    
            // 设置默认的meta信息
            $this->template->assign('site_title', ($title ? ($title . ' - ') : '') . $this->lang->line('site_title'));
            $this->template->assign('site_keywords', $title ? $title : $this->lang->line('site_keywords'));
            $this->template->assign('site_description', $title ? $title : $this->lang->line('site_description'));
    
            // 加载css
            $this->load_css_js();
        }
        
        //检查cookie
        // $str_cookie = authcode($this->cookie($this->cookie_name), 'DECODE');
        // $this->auth = empty($str_cookie)?array():unserialize($str_cookie);
        // if(empty($this->auth) && in_array($this->_c.'/'.$this->_m, $this->config->item('check_signins'))) {
            // // 如果需要验证权限，切没有登录
            // // 没有登录
            // if($this->ajax) {
                // // AJAX访问
                // $this->echo_json(array('code'=>0));
            // } else {
                // // 
                // $this->showmessage('没有权限，请登录', '/signin');
            // }
        // }
        
        // 获取用户的COOKIE 获取的时候需要自己拼COOKIE的NAME。我XX
        $cookie = $this->cookie($this->config->item('cookie_prefix').$this->config->item('auth_cookie'));
        if($cookie) {
            // 有cookie，反序列化
            list($this->auth['id'], $this->auth['username'], $this->auth['nickname'], $this->auth['avatar'], $this->auth['gender'], $this->auth['name'], $this->auth['is_sync_sina'], $this->auth['is_sync_tencent']) = explode("\t", authcode($cookie));
            //更新cookie
            $acc = $this->db->where('id', $this->auth['id'])->get('User')->first_row('array');
            unset($acc['password']);
            $acc['avatar'] = $this->auth['avatar'];//替换头像成可访问地址
            $acc['name'] = !empty($acc['nickname']) ? $acc['nickname'] : $acc['username'];
            //是否绑定了新浪微博
            $count = $this->db->where('uid', $acc['id'])->count_all_results('UserSinaBindInfo');
            $acc['is_sync_sina'] = $count > 0 ? 1 : 0;
            //是否绑定了腾讯微博
            $count = $this->db->where('uid', $acc['id'])->count_all_results('UserTencentBindInfo');
            $acc['is_sync_tencent'] = $count > 0 ? 1 : 0;
            $this->auth = $acc;
            //获取用户的好友备注列表
     //       $this->load->model('universal', 'uni');
     //       $this->my_desc = $this->uni->get_my_desc($this->auth['id']);
        } elseif(in_array($this->_c.'/'.$this->_m, $this->config->item('check_signins'))) {
            // 没有cookie，并且访问的页面需要检查权限的
            if($this->ajax) {
                // AJAX访问
                $this->echo_json(array('code'=>0));
            } else {
                // 
                $this->showmessage('没有权限，请登录', '/signin');
            }
        }
        
        // 设置到模板中
        $this->assign('auth', $this->auth);
        
    	$api_uri = $this->config->item('api_serv').$this->config->item('api_folder');
    	$this->assign('api_uri', $api_uri);
    }
    
    /**
     * 
     */
    function showmessage($message, $url_forward = '', $timeout = 3) {
        $this->assign(compact('message', 'url_forward', 'timeout'));
        $this->display('showmessage', 'web'); 
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
     * @return string
     */
    function get($key, $xss_clean = TRUE) {
        return $this->input->get($key, $xss_clean);
    }

    /**
     * 获取post数据
     * @param $key
     * @return string
     */
    function post($key, $xss_clean = TRUE) {
        return $this->input->post($key, $xss_clean);
    }

    /**
     * 获取server数据
     * @param $key
     * @return string
     */
    function server($key, $xss_clean = TRUE) {
        return $this->input->server($key, $xss_clean);
    }

    /**
     * 获取cookie数据
     * @param $key
     * @return string
     */
    function cookie($key, $xss_clean = TRUE) {
        return $this->input->cookie($key, $xss_clean);
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
     * 页面加载css和js
     * @param $keys 需要加载的css js的内容KEY值
     */
    function load_css_js() {
        $map = $this->config->item('loader_map');
        $css = $js = array();

        // 控制器需要导入的CSS及JS
        $map[$this->_c]['css'] && $css = $map[$this->_c]['css'];
        $map[$this->_c]['js'] && $js = $map[$this->_c]['js'];
        // 控制器的方法需要导入的CSS及JS
        $map[$this->_c][$this->_m]['css'] && $css = array_merge($css, $map[$this->_c][$this->_m]['css']);
        $map[$this->_c][$this->_m]['js'] && $js = array_merge($js, $map[$this->_c][$this->_m]['js']);
        
        if(empty($css) && empty($js)) return;
        
        // 读取信息
        $css_str = $js_str = '';
        if ($css) {
            foreach ($css as $c => $b) {
                $css_str .= $b ? ('<style>' . file_get_contents(FCPATH . $c) . '</style>') : '<link rel="stylesheet" href="' . $c . '?v=' . strtoupper(md5($this->config->item('css_version'))) . '" type="text/css"/>';
            }
        }
        if ($js) {
            foreach ($js as $j => $b) {
                $js_str .= ($b ? ('<script type="text/javascript">' . file_get_contents(FCPATH . $j)) : ('<script type="text/javascript" src="' . $j . '?v=' . strtoupper(md5($this->config->item('css_version'))) . '">')) . '</script>';
            }
        }
        
        $css_str && $this->template->assign('css', $css_str);
        $js_str && $this->template->assign('script', $js_str);
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
        // $this->load->library('api_sender');
        // if($method == 'get') {
            // return $this->api_sender->get_api($params);
        // } else {
            // return $this->api_sender->post_api($params);
        // }
    }
    
    /**
     * 是否登录判断
     * @param $redirect 直接登录么 
     */
    function is_login($redirect = false) {
        if(empty($this->auth['id'])) {
            // 请先登录哦。
            if(!$redirect)
        	    $this->showmessage('未登录或登录已过期，请先登录', '/signin/', 3);
        	else
        		redirect('/signin/', 'location', 301);
        }
    }
    
    /**
     * 保存用户cookie
     * Create by 2012-5-4
     * @author liuw
     * @param array $data
     */
    function _update_cookie($data, $expire = 0){
    	// $value = authcode(serialize($data),'ENCODE');
    	// //先清理原来的cookie
    	// $cookie = array('name'=>$this->config->item('auth_cookie'),'value'=>'','expire'=>-1);
    	// $this->input->set_cookie($cookie);
    	// //重新写cookie
    	// $cookie = array(
    		// 'name'=>$this->config->item('auth_cookie'),
    		// 'value'=>$value,
    		// 'expire'=> $expire,
    		// 'domain'=>$this->config->item('cookie_domain'),
    		// 'path'=>$this->config->item('cookie_path'),
    	// );
    	// $this->input->set_cookie($cookie);
    	
    	if($expire >= 0 && $data) {
        	// 保存用户登录信息
        	$auth_data = '';
            // ID
            $auth_data['id'] = $data['id'];
            // 用户名
            $auth_data['username'] = $data['username'];
            // 昵称
            $auth_data['nickname'] = $data['nickname'];
            // VATAR
            $auth_data['avatar'] = $data['avatar_uri'];
            // 性别
            $auth_data['gender'] = $data['gender'];
            // 名字。有昵称显示昵称，没有显示用户名
            $auth_data['name'] = $data['nickname']?$data['nickname']:$data['username'];
            //是否绑定了sina
            $auth_data['is_sync_sina'] = !empty($data['is_sync_sina']) ? $data['is_sync_sina'] : 0;
            //是否绑定了腾讯
            $auth_data['is_sync_tencent'] = !empty($data['is_sync_tencent']) ? $data['is_sync_tencent'] : 0;
            $cookie_auth = implode("\t", array_values($auth_data));
        }
    	$this->input->set_cookie($this->config->item('auth_cookie'), $expire<0?null:authcode($cookie_auth, 'ENCODE'), $expire<0?-1:$expire);
    }
    
    /**
     * 获取好友备注
     * Create by 2012-8-21
     * @author liuw
     * @param mixed $fuid
     * @return mixed
     */
   	function _get_friend_desc($fuid){
   		$this->is_login();
   		if(empty($this->my_desc))
   			return false;
   		else{
   			if(!is_array($fuid))
   				return $this->my_desc[$fuid];
   			else{
   				$descs = array();
   				foreach($fuid as $v){
   					$descs[$v] = $this->my_desc[$v];
   				}
   				return $descs;
   			}
   		}
   	}
}
