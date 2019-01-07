<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 拦截器
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-2-13
 */

class Interceptor {
    // 当前访问的uri
    var $_uri = '';
    private $_nothing;
    /**
     * 构造函数
     */
    function __construct() {
        // list($c, $m) = array_values($GLOBALS['CI']->uri->rsegments);
        // $this->_uri = "/" . $GLOBALS['CI']->uri->uri_string . ($GLOBALS['CI']->get('do') ? '/do/' . $GLOBALS['CI']->get('do') : '');
        // $GLOBALS['CI']->_uri = $this->_uri;
        // $GLOBALS['CI']->_c = $c;
        // $GLOBALS['CI']->_m = $m;      
        // $GLOBALS['CI']->_index_uri = "/{$c}/index";  
        // $GLOBALS['CI']->template->assign('self_uri', $this->_uri);
        $this->_uri = $GLOBALS['CI']->_uri;
        
        $this->_nothing = (in_array($this->_uri, $GLOBALS['CI']->config->item('uncheck_rights')) || 'www' == $GLOBALS['CI']->router->class);
    }

    /**
     * 权限判断
     */
    function rights() {
        if($this->_nothing) {
            // 如果在不检查权限的数组中 或则 访问web controller里面的连接 直接返回不用去检查
            return;
        }
        // 得到所有权限
        $rights_uri = get_data('rights_uri');
        // 获取当前连接的权限ID
        $id = $rights_uri[$this->_uri];

        // 特殊处理，如果用户通过授权签名访问，那么可以跳过权限判断
        $in_sign = $GLOBALS['CI']->get('in_sign');
        if ($in_sign && substr(in_sign(), 14, 18) === substr($in_sign, 14, 18)) {
            // 如果传入了in_sign变量，而且正确那么直接return
            return;
        }

        // 获取用户的登录信息
        $sess_auth = $GLOBALS['CI']->auth;
        if (empty($sess_auth)) {
            // 没有登录，跳转到登录页面
            $GLOBALS['CI']->showmessage($GLOBALS['CI']->lang->line('no_login'), site_url(array(
                    'main',
                    'login'
            )));
        }
        
        if(empty($id)) {
        	// 如果没有需要判断的权限那么跳出
        	return;
        }
        
        // $sess_auth里面保存 id,name,truename,description,rights,vest,role
        // 判断roleid是否为超级管理员的组ID
        if (!in_array($GLOBALS['CI']->config->item('superadmin'), $sess_auth['role'])) {
            // 判断权限ID是否在用户的权限里面
            // $r_exists = in_array($id, $sess_auth['rights']);
            // 去获取用户所在角色的所有权限
            $roles = get_data('role');
            $r_exists = false;
            foreach($sess_auth['role'] as $role_id) {
                $role = $roles[$role_id];
                $rights = array_values($role['rights']);
                
                $r_exists = in_array($id, $rights);
                if($r_exists) {
                	// 如果判断到一个权限存在了，那么就跳出去，那没有的话继续判断。知道最后一个
                	break;
                }
            }
            if ($r_exists === false) {
            	// 判断请求的类型
            	if ($GLOBALS['CI']->input->is_ajax_request()) {
            		// AJAX请求 返回JSON数据
            		$GLOBALS['CI']->error($GLOBALS['CI']->lang->line('no_rights'));
            	} else {
            		// 其他请求 直接弹出提示
            		$GLOBALS['CI']->showmessage($GLOBALS['CI']->lang->line('no_rights'), site_url(array('main', 'index')), 0);
            	}
            }
        }
    }
    
    /**
     * 操作日志
     */
    function actlog() {
        if($this->_nothing) {
            // 如果在不检查权限的数组中 或则 访问web controller里面的连接 直接返回不用去记录
            return;
        }
        $CI = &get_instance();
        $act_log = get_data('act_log');
        $sess_auth = $CI->auth;
        $act_name = $act_log[$this->_uri];
        if ($sess_auth && $act_name) {
            $CI->load->helper('log');
            // 存在，那么才记录日志
            $log_arr = array(
                    'admin' => $sess_auth['name'],
                    'atruename' => $sess_auth['truename'],
                    'dateline' => time(),
                    'role' => implode(',', $sess_auth['role']),
                    'actdesc' => clearlogstring(implodearray(array(
                            'GET' => $CI->input->get(null),
                            'POST' => $CI->input->post(null)
                    ))),
                    'acturi' => $this->_uri
            );
            
            $CI->db->insert('MorrisAdminLog', $log_arr);
        }
    }
}
