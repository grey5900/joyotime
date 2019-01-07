<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 通信证操作
 *
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-20
 */

class Passport extends Controller {
	function __construct() {
		parent::__construct();

		// 导入语言文件
		$this->load->language('passport');
		// 导入加解密helper
		$this->load->helper('crypt');

		// 加载model
		$this->load->model('failedlogin_model', 'm_failedlogin');
		$this->load->model('user_model', 'm_user');

		$this->changyan_app_id = 'cy2ryR8WnJlz';
		$this->changyan_app_key = '5329c9c5ea0c44e5c4cbf3fc7401d1b5';
	}

	/**
	 * 登录页面
	 */
	function signin() {
		// 先验证访问是否安全
		$sign = $this->get('sign');
		$password = $this->get('password');
		$username = urldecode(trim($this->get('username')));
		$salt = $this->get('salt');
		$rememberme = intval($this->get('rememberme'));

		if($sign && $password && $username && $salt && urldecode(idecrypt($salt, $sign)) == $password) {
			// 解密$password
			$password = idecrypt($salt, $password);
			if(empty($password)) {
				$this->_show('signin_error');
				return;
			}

			if(empty($username)) {
				$this->_show('signin_error');
				return;
			}

			// 去登陆
			// 获取 IP 地址
			$ip_address = ip2long($this->input->ip_address());
			$error_num = $this->config->item('signin_error_num');
			$banned_expire_time = $this->config->item('signin_banned_time');
			// 算出比较的时间
			$banned_time = idate_format(TIMESTAMP - 60*$banned_expire_time, 'Y-m-d H:i:s');

			// 删除已经过期的banned
			$this->m_failedlogin->delete_by_time($banned_time);
			// 检查IP是在登录失败表
			$failedlogin = $this->m_failedlogin->select_by_ip($ip_address);
			if($failedlogin && ($failedlogin['loginCount'] > $error_num)) {
				// 如果存在登录错误的记录及错误次数已经大于设定次数
				$this->_show('signin_num_error', array($error_num, $banned_expire_time));
				return;
			}

			$this->load->helper('api');
			// 去API登录
			$params = array(
					'username' => $username,
					'password' => $password,
			);
			$rtn = decode_json(call_api('signin', $params));
			if(empty($rtn) || $rtn['result_code']) {
				if($failedlogin) {
					$failedlogin['loginCount']++;
				} else {
					$failedlogin = array('ip' => $ip_address, 'loginCount' => 1, 'lastDate' => DATETIME);
				}
				$this->m_failedlogin->replace($failedlogin);
				$this->_show('signin_error');
				return;
			}

			$user = $rtn['user'];
			// 判断用户的状态
			if($user['status'] > 0) {
				$this->_show('user_except');
				return;
			}
			$cookie = array(
					'id' => $user['id'],
					'nickname' => $user['nickname'],
					'username' => $user['username'],
					'avatar_uri' => $user['avatar_uri'],
					'auto_login' => $rememberme,
					'expire' => 0
			);
			$cookie['expire'] = $cookie['auto_login']?(86400 * 365):0;
			$this->_update_auth_cookie($cookie);
			unset($cookie, $user);
			$this->_show('signin_success', array(), 0);
		} else {
			$this->_show('request_error');
		}
	}

	/**
	 * 退出登录
	 * Create by 2012-12-28
	 * @author liuweijava
	 */
	function signout(){
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		delete_cookie('auth');
		$this->_show('signout_success', array(), 0);
	}

	/**
	 * 同步登录
	 */
	function sso() {
		// 去获取登录以后的cookie
		$auth = $this->cookie('auth');
		if($auth) {
			$string = authcode($auth);
			if($string) {
				$apps = $this->config->item('apps');
				$data = array();
				foreach($apps as $host => $salt) {
					$data[$host] = rawurlencode(authcode($string, 'ENCODE', $salt, 60));
				}

				$this->assign('apps', $data);
				$this->display('sso');
			}
		}
	}

	/**
	 * 退出登录
	 */
	function sso_logout() {
		$apps = $this->config->item('apps');
		$data = array();
		foreach($apps as $host => $salt) {
			$data[$host] = rawurlencode(authcode('logout', 'ENCODE', $salt, 60));
		}

		$this->assign('apps', $data);
		$this->display('sso_logout');
	}

	/**
	 * 注册
	 * @return code:0=注册成功；1=注册失败；2=登录名长度错误，3=密码长度错误，4=两次密码不一致，5=登录名重复，6=用户名包含敏感词
	 */
	function signup() {
		//POST数据
		$username = urldecode(trim($this->post('username')));
		$password = $this->post('password');
		$repwd = $this->post('repwd');
		$email = urldecode(trim($this->post('email')));
		$nickname = urldecode(trim($this->post('nickname')));
		
		// 检查邮箱
		if($email) {
			$this->load->helper('email');
			if(!valid_email($email)) {
				$this->_show('请输入正确的邮箱地址', array(), 2);
			}
		}
		
		if(empty($email)) {
			$reg_username = "/^[a-zA-Z0-9_]{2,15}$/";
	
			//检查登录名长度
			if(cstrlen($username) < 2 || cstrlen($username) > 15){
				$this->_show('signup_username_len_fail', array(), 2);
			}
			if(!preg_match($reg_username,$username)){
				//检查用户名规则
				$this->_show('signup_username_fail', array(), 2);
			}
	
			//检查登录名是否重复
			$c = $this->m_user->select(array('username'=>$username));
			if(!empty($c)) {
				$this->_show('signup_username_used', array(), 5);
			}
			
			if(!check_taboo($username, 'user')) {
				//检查是否包含敏感词
				$this->_show('signup_username_taboo', array(), 6);
			}
		} else {
			//检查昵称长度
			if(cstrlen($nickname) < 2 || cstrlen($nickname) > 15){
				$this->_show('昵称不能少于1个字或多于15个字', array(), 2);
			}
			
			if(!check_taboo($nickname, 'user')) {
				//检查是否包含敏感词
				$this->_show('昵称含有敏感词，请重新选择一个', array(), 6);
			}
		}
		
		if(strlen($password) < 2 || strlen($password) > 15) {
			//检查密码长度
			$this->_show('signup_password_len_fail', array(), 3);
		}
		if($password !== $repwd) {
			//检查两次密码是否有一致
			$this->_show('signup_password_fail', array(), 4);
		}
		//使用接口注册新账号
		$this->load->helper('api');
		$this->config->load('config_api');
		$this->config->load('config_common');
		//渠道号
		$channel_id = $this->config->item('web_channel_id');
		//封装数据
		$nick_name = empty($nickname)?$username:$nickname;
		$arr = compact('nick_name', 'password', 'channel_id');
		if ($email) {
			$arr['email'] = $email;
		} else {
			$arr['username'] = $username;
		}

		//调用接口注册
		$result = decode_json(call_api('signup', $arr), true);
		if(empty($result) || $result['result_code']){//注册失败
			empty($result) && $result = array('result_code'=>-1, 'msg'=>'API通信失败');
			$this->_show($result['result_msg'], array(), $result['result_code']);
		}else{//执行登录
			$user = $result['user'];
			$cookie = array(
					'id' => $user['id'],
					'nickname' => $user['nickname'],
					'username' => $user['username'],
					'avatar_uri' => $user['avatar_uri'],
					'expire' => 0,
					'auto_login' => 0
			);
			// 在passport站点下保存COOKIE
			$this->_update_auth_cookie($cookie);
			unset($cookie, $user);
			$this->_show('signup_success', array(), 0);
		}
	}

	/**
	 * 第三方登录回调
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $uid
	 * @param int $is_new,0是老用户，1是新用户
	 */
	public function oauth_login($uid, $is_new){
		$user = get_data('user', $uid);
		//保存COOKIE
		$cookie = array(
				'id' => $user['id'],
				'nickname' => $user['nickname'] ? $user['nickname'] : '',
				'username' => $user['username'] ? $user['username'] : '',
				'avatar_uri' => $user['avatar_url'],
				'auto_login' => 0,
				'expire' => 0
		);
		$this->_update_auth_cookie($cookie);
		if($is_new){//新注册用户，跳转到完善资料页面
			redirect('', 'location', 301);
		}else{//非新用户，跳转到首页
			redirect(base_url(), 'location', 301);
		}
	}

	/**
	 * 更新登录用户COOKIE
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param array $auth
	 */
	private function _update_auth_cookie($cookie){
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
		// 保存登录成功的COOKIE
		// 获取自动录的标识
		$cookie_expire = $cookie['expire'];
		$auto_login = $cookie['auto_login'];

		$passport_config = $this->config->item('passport');
		// 在passport站点下保存COOKIE
		$cookie['nickname'] = $cookie['nickname'] ? $cookie['nickname'] : $cookie['username'];

		$auth = $cookie['id'] . "\t" . $cookie['nickname'] . "\t" . $cookie['avatar_uri'] . "\t" . $auto_login;

		set_cookie('auth', authcode($auth, 'ENCODE'), $cookie_expire);
	}

	/**
	 * 返回
	 */
	private function _show($string, $args = array(), $code = 1) {
		$message = lang_message($string, $args);
		$arr = array(
				'code' => $code,
				'message' => $message?$message:$string
		);
		$callback = $this->get('callback');
		$this->assign('str', $callback . '(' . encode_json($arr) . ')');
		$this->display('return', 'common');
	}

	/**
	 * 敏感词审核
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function check_taboo() {
		$content = trim($this->get('content'));
		if($content && !check_taboo($content, 'user')) {
			$this->_show('signup_username_taboo', array(), 6);
		} else {
			$this->_show('', array(), 0);
		}
	}

	function changyan($action){
		$data = array();
		if($action == "info"){
			if($this->auth){
				$data['is_login'] = 1;
			}
			else{
				$data['is_login'] = 0;
			}
			$user = get_data($this->auth['uid']);
			$sign = $this->hmacsha1($this->changyan_app_key,"cy_user_id=".$user['id']."&");
			$data['user'] = array(
					'img_url' => $user['avatar_m'],
					'nickname' => $user['nickname']?$user['nickname']:$user['username'],
					'profile_url' => "http://in.jin95.com/user/".$user['id'],
					'user_id' => $user['id'],
					'cy_user_id' => $this->changyan_app_id,
					'sign' => $sign,
			);
		}
		echo $this->encode_json($data);
		exit;
	}

	function hmacsha1($key,$data) {
		$blocksize=64;
		$hashfunc='sha1';
		if (strlen($key)>$blocksize)
			$key=pack('H*', $hashfunc($key));
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack(
				'H*',$hashfunc(
						($key^$opad).pack(
								'H*',$hashfunc(
										($key^$ipad).$data
								)
						)
				)
		);
		return $hmac;
	}
}
