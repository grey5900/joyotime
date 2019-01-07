<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * controller基类
 * @author piggy
 *
 */
class MY_Controller extends CI_Controller {
	function __construct($web = false) {
		parent::__construct();
		
		if (!$web) {
			if (ENVIRONMENT != 'production') {
				// 跨域访问
				header("Access-Control-Allow-Headers:Origin, X-Atmosphere-tracking-id, X-Atmosphere-Framework, X-Cache-Date, Content-Type, X-Atmosphere-Transport, X-Remote, api_key, auth_token, *");
				header("Access-Control-Allow-Methods:POST, GET, OPTIONS, PUT, DELETE");
				header("Access-Control-Allow-Origin:*");
				header("Access-Control-Request-Headers:Origin, X-Atmosphere-tracking-id, X-Atmosphere-Framework, X-Cache-Date, Content-Type, X-Atmosphere-Transport,  X-Remote, api_key, *");
			}
			
			$header = $this->input->request_headers(true);
			$h = array();
			$config = get_config(array('header_keys', 'testing', 'rights'));
			foreach($header as $key => $value) {
				$k = strtolower($key);
				if (in_array($k, $config['header_keys'])) {
						$this->$k = $value;
				}
			}
			
			if($config['testing']) {
				// 测试
				foreach($config['header_keys'] as $key) {
					if(!isset($this->$key)) {
						$value = trim($this->get($key));
						$this->$key = $value?$value:trim($this->post($key));
					}
				}
				
				if ($this->cel) {
					$this->user = $this->db->select('name, cellphone_no, commision, token')
						->get_where('user', array('cellphone_no' => $this->cel))->row_array();
				}
			}
			
			if(!$config['testing'] || $this->t) {
				// 如果正是环境 或则 header中有值
				$this->t = strval($this->t);
				if (strlen($this->t) != 10) {
					$this->http_403('msg_api_forbid');
				}
				// 接口请求验证
				$k = md5(md5($this->dc) . md5($this->t));
				$k = $k . ($k{$this->t{$this->t{9}}});
				
				$this->k = strtolower($this->k);
				if($this->k != $k) {
					$this->http_403('msg_api_forbid');
				}
				
				// 判断是否需要用户登陆验证的接口访问
				$this->cur_uri = join('/', array($this->router->directory, $this->router->class, $this->router->method));
				($this->cur_uri{0} === '/') || ($this->cur_uri = '/' . $this->cur_uri);
				$rights_params = $config['rights'][$this->cur_uri];
				if($rights_params) {
					// 需要权限的地方
					if ($rights_params['method'] != $_SERVER['REQUEST_METHOD']) {
						// 请求方法错误
						$this->http_403('msg_api_forbid');
					}
					
					$this->user = $this->db->select('name, cellphone_no, commision, token')
						->get_where('user', array('cellphone_no' => $this->cel))->row_array();
					
					$b = true; // 是否需要做验证
					// 判断请求参数
					if ($rights_params['params']) {
						foreach($rights_params['params'] as $key => $value) {
							if($_REQUEST[$key] != $value) {
								$b = false;
								break;
							}
						}
					}
					
					if ($b) {
						// 做登陆验证
						$token = md5(md5($this->k) . $this->user['token'] . md5($this->cel));
						if($token != strtolower($this->token)) {
							$this->http_403('msg_auth_error');
						}
						
						if(empty($this->user)) {
							$this->http_403('msg_api_need_signin');
						}
					}
				}
			
			}
			
			if(empty($this->dp)) {
				$this->dp = 'odp';
			}
		}
	}

	/**
	 * 输出JSON数据
	 * @param mixed $data 输出数据 成功输出数据结果集，失败输出失败原因
	 * @param int $code http头输出的状态 200：成功 304：数据不变 500：数据错误 403：验证失败不允许访问 404：NOT FOUND
	 */
	function echo_json($data, $code = 200, $params = array()) {
		if ($code == 200) {
			$msg = is_string($data)?($this->lang->line($data)?$this->lang->line($data):$data):$this->lang->line('msg_' . $code);
		} else {
			$msg = strval($data);
			if($params) {
				array_unshift($params, $msg);
				$msg = call_user_func_array('sprintf', $params);
			}
		}
		$msg = urlencode($msg);
		if ($this->config->item('fastcgi')) {
			header(sprintf($this->lang->line('header_status_fastcgi_' . $code), $msg));
		} else {
			header(sprintf($this->lang->line('header_status_' . $code), $msg));
		}
		header('content-type: application/json; charset=utf-8');
		if($code == 200) {
			echo encode_json(is_array($data)?$data:array('message' => urldecode($msg)));
		}
		exit(0);
	}

	/**
	 * 成功
	 * @param mixed $data
	 */
	function http_200($data = '') {
		$this->echo_json($data);
	}
	
	/**
	 * 获取相应语言的文案信息
	 * @param mixed $key
	 * @param int $code
	 */
	private function _get_message($key, $code) {
		if (is_string($key)) {
			if($key) {
				$v = $this->lang->line($key);
				if ($v) {
					$data = $v;
				} else {
					$data = $key;
				}
			} else {
				$data = $this->lang->line('http_' . $code);
			}
			
			return $data;
		}
		
		return $key;
	}
	
	/**
	 * 数据未变化
	 * @param mixed $data
	 */
	function http_304($data = '', $params = array()) {
		$this->echo_json($this->_get_message($data, 304), 304, $params);
	}

	/**
	 * 禁止访问
	 * @param mixed $data
	 */
	function http_403($data = '', $params = array()) {
		$this->echo_json($this->_get_message($data, 403), 403, $params);
	}
	
	/**
	 * 没有找到
	 * @param mixed $data
	 */
	function http_404($data = '', $params = array()) {
		$this->echo_json($this->_get_message($data, 404), 404, $params);
	}

	/**
	 * 错误
	 * @param mixed $data
	 */
	function http_500($data = '', $params = array()) {
		$this->echo_json($this->_get_message($data, 500), 500, $params);
	}
	
	/**
	 * 获取get数据
	 * @param $key
	 * @param $default 默认值
	 * @param bool
	 * @return string
	 */
	function get($key, $default = false, $xss_clean = true) {
		$value = $this->input->get($key, $this->_enable_xss?false:$xss_clean);
		return empty($value)?(false===$default?$value:$default):$value;
	}
	
	/**
	 * 获取post数据
	 * @param $key
	 * @param $default 默认值
	 * @param bool
	 * @return string
	 */
	function post($key, $default = false, $xss_clean = true) {
		$value = $this->input->post($key, $this->_enable_xss?false:$xss_clean);
		return empty($value)?(false===$default?$value:$default):$value;
	}
	
	/**
	 * 获取post数据
	 * @param $key
	 * @param $default 默认值
	 * @param bool
	 * @return string
	 */
	function get_post($key, $default = false, $xss_clean = true) {
		$value = $this->input->get_post($key, $this->_enable_xss?false:$xss_clean);
		return empty($value)?(false===$default?$value:$default):$value;
	}
	
}

class WebController extends MY_Controller {
	var $template;
	function __construct() {
		parent::__construct(true);
		$this->load->config('config_web');
		// 初始化模板
		$this->template->setOptions($this->config->item('template'));
		$this->load->helper('web');
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
}


