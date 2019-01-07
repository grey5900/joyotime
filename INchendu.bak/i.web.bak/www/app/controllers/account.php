<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 账户相关操作
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-3
 */

class Account extends MY_Controller {

    /**
     * 登录
     */
    function signin() {
    	//回调地址
    	$this->_is_signin();
    	$refer = $_SERVER['HTTP_REFERER'];
    	$refer = strpos($refer, 'reste_pwd') !== FALSE || $this->input->is_ajax_request() ? '' : $refer;
    	$api_uri = $this->config->item('api_serv').$this->config->item('api_folder');
    	$this->assign('api_uri', $api_uri);
        if($this->is_post()){
        	$username = trim($this->post('username'));
        	$password = $this->post('password');
            
            if(empty($username) || empty($password)) {
                // 提示
                $this->echo_json((array('code'=>0,'msg'=>'请输入登录账号和密码')));
            }
            
        	$auto_login = $this->post('auto_login');
        	// $valicode = $this->post('verifycode');
        	//检查验证码是否正确
        	//if(!$this->check_captcha($valicode))
        	//	$this->echo_json(array('code'=>0, 'msg'=>'验证码不正确'));
            $this->load->helper('date');
        	$expire = empty($auto_login) ? 0 : (3600*24*365);
        	//调用api实现登录
        	$params = array(
        		'api' => $this->lang->line('api_signin'),
        		'attr' => array('username'=>$username, 'password'=>$password),
        		'has_return' => TRUE
        	);
        	$return = json_decode($this->call_api($params), TRUE);
        	//本地调试用
        	//$return = array('result_code'=>0);
        	//
        	if($return['result_code'] === 0){//登录成功
        		$user = $return['user'];
        		//本地调试用
        	//	$user = $this->db->where('username',$username)->get('User')->first_row('array');
	        	// $arr['expire'] = $expire;
	        	if($user['status']) {
	        	    $this->showmessage('对不起，亲，您的用户不能登录，请与管理员联系');
	        	}
	        	$this->_update_cookie($user, $expire);
	        	$this->echo_json((array('code'=>1, 'msg'=>$this->lang->line('signin_success'), 'refer'=>$refer)));
        	}else{//登录失败
        		$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signin_fail'))));
        	}
        }else{
    		$this->display('signin');
        }
    }
    
    /**
     * 注册
     */
    function signup() {
        $this->_is_signin();
    	//回调地址
    	$refer = $_SERVER['HTTP_REFERER'];
    	$refer = strpos($refer, 'reste_pwd') !== FALSE || $this->input->is_ajax_request() ? '' : $refer;
        
    	if($this->is_post()){
    		//获取表单数据
        	$valicode = $this->post('verifycode');
        	//检查验证码是否正确
        	if(!$this->check_captcha($valicode)) {
        	    $this->echo_json(array('code'=>0, 'msg'=>'验证码不正确'));
        	}
        		
    		$username = trim($this->post('username'));
    		$password = $this->post('password');
            $password_again = $this->post('password_again');
            
            if(empty($username) || empty($password) || empty($password_again)) {
                // 提示
                $this->echo_json((array('code'=>0,'msg'=>'请输入注册账号和密码')));
            }
            
            if($password !== $password_again) {
                $this->echo_json(array('code'=>0, 'msg'=>'两次输入密码不一致，请检查'));
            }
            
    		//调用api注册
    		$params = array(
    			'api'=>$this->lang->line('api_signup'),
    			'attr'=>array('username'=>$username,'password'=>$password, 'channel_id'=>10000),
    			'has_return'=>TRUE
    		);
    		$content = $this->call_api($params, 'POST');
    		$result = json_decode($content, TRUE);
    		if($result['result_code'] > 0){//注册失败了
    			$this->echo_json(array('code'=>1, 'msg'=>$result['result_msg'], 'refer'=>'/signup'));
    		}else{
    			$uid = $result['user']['id'];
    			
	    		$email = trim($this->post('email'));
	    		if(!empty($email)){//修改邮箱
	    			$this->db->where('id', $uid)->update('User', array('email'=>$email));
	    		}
	    		$nickname = $this->post('nickname');
	    		$birth_year = $this->post('year');
	    		$birth_month = $this->post('month');
	    		$birth_day = $this->post('day');
	    		$gender = $this->post('gender');
	    		//昵称
	    		if(isset($nickname) && !empty($nickname)){
	    			if(strlen($nickname) < 1 || strlen($nickname) > 15)
	    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_nickname_has_long'))));
	    		}
	    		//生日
	    		$birthday = '';
	    		if((isset($birth_year)&&!empty($birth_year))||(isset($birth_month)&&!empty($birth_month))||(isset($birth_day)&&!empty($birth_day))){
	    			if(!isset($birth_month)||empty($birth_month))
	    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_month_null'))));
	    			elseif(!isset($birth_day)||empty($birth_day))
	    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_day_null'))));
	    			elseif(!isset($birth_year)||empty($birth_year))
	    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_year_null'))));
	    			else{
	    				$birthday = $birth_year.'-'.$birth_month.'-'.$birth_day;
	    				//更新
	    				$this->db->where('id', $uid)->update('User', array('birthdate'=>$birthday));
	    			}
	    		}
	    	      
                $arr = array('uid'=>$uid);
	    		//性别
	    		if(isset($gender)&&!empty($gender)) {
	    		    $gender = intval($gender)-1;
	    		    $this->db->where('id', $uid)->update('User', array('gender'=>$gender));
	    		//	$arr['gender'] = $gender;
	    		}
	    		if(isset($nickname) && !empty($nickname))
	    			$this->db->where('id', $uid)->update('User', array('nickname'=>$nickname));
	    		//保存cookie
	    		$user = $result['user'];
	    		if(!empty($nickname))
	    			$user['nickname'] = $user;
	    		$this->_update_cookie($user);
	    		$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('signup_success'), 'refer'=>$refer));
    		}
    	}else{
    		$this->display('signup');
    	}
    }
    
    /**
     * 退出登录 
     * Create by 2012-5-7
     * @author liuw
     */
    function signout(){
    	//清空cookie
    	// $this->input->set_cookie(array(
    		// 'name' => $this->config->item('auth_cookie'),
    		// 'value' => '',
    		// 'expire' => -1
    	// ));
    	$this->_update_cookie(null, -1);
    	
    	$this->echo_json((array('code'=>1, 'msg'=>$this->lang->line('signout_success'))));
    }
    
    /**
     * 完善帐号信息
     * Create by 2012-5-7
     * @author liuw
     */
    function complete($uid){
    	$info = $this->db->where('id', $uid)->get('User')->first_row('array');
    	$this->assign('uid', $uid);
    	$info['avatar'] = !empty($info['avatar'])?image_url($info['avatar'], 'head', 'hhdp'):'';
    	$this->assign('info', $info);
    	//检查请求的UID和COOKIE中的UID是否一样
    	if($uid != $this->auth['id'])
    		$this->showmessage($this->lang->line('complete_request_fail'), '/index');
    	//查询详细信息
    	if($this->is_post()){
	    	//回调地址
	    	$refer = $_SERVER['HTTP_REFERER'];
	    	$refer = strpos($refer, 'reste_pwd') !== FALSE || $this->input->is_ajax_request() ? '' : $refer;
    		//POST数据
    		$username = trim($this->post('username'));
    		$password = $this->post('password');
            $password_again = $this->post('password_again');
    		$nickname = $this->post('nickname');
    		$birth_year = $this->post('year');
    		$birth_month = $this->post('month');
    		$birth_day = $this->post('day');
    		$gender = $this->post('gender');
    		$invite_uid = $this->post('recommender');
            
            if(empty($username) || empty($password) || empty($password_again)) {
                // 提示
                $this->echo_json((array('code'=>0,'msg'=>'请输入注册账号和密码')));
            }
            
            if($password !== $password_again) {
                $this->echo_json(array('code'=>0, 'msg'=>'两次输入密码不一致，请检查'));
            }
            $password = strtoupper(md5($password));
    		//更新基本资料
    		$data = compact('username', 'password');
    		$email = trim($this->post('email'));
    		if(!empty($email)){//修改邮箱
    			$data['email'] = $email;
    		}
    		$this->db->where('id', $uid)->update('User', $data);
    		//昵称
    		if(isset($nickname) && !empty($nickname)){
    			if(strlen($nickname) < 1 || strlen($nickname) > 15)
    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_nickname_has_long'))));
    		}
    		//生日
    		$birthday = '';
    		if((isset($birth_year)&&!empty($birth_year))||(isset($birth_month)&&!empty($birth_month))||(isset($birth_day)&&!empty($birth_day))){
    			if(!isset($birth_month)||empty($birth_month))
    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_month_null'))));
    			elseif(!isset($birth_day)||empty($birth_day))
    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_day_null'))));
    			elseif(!isset($birth_year)||empty($birth_year))
    				$this->echo_json((array('code'=>0,'msg'=>$this->lang->line('signup_year_null'))));
    			else{
    				$birthday = $birth_year.'-'.$birth_month.'-'.$birth_day;
    				//更新
    				$this->db->where('id', $uid)->update('User', array('birthdate'=>$birthday));
    			}
    		}
    		//性别
    		if(isset($gender)&&!empty($gender)) {
    		    $gender = intval($gender);
    		}
	    	      
            $arr = array();
            $nickname && ($arr['nickname'] = $nickname);
          //  $birthday && ($arr['birthdate'] = $birthday);
            $gender && ($arr['gender'] = $gender);
            $invite_uid && ($arr['invite_uid'] = $invite_uid);
            if(!empty($arr)) {
               $data['api'] = $this->lang->line('api_user_edit_base');
               $data['attr'] = $arr;
               $data['uid'] = $uid;
               $data['has_return'] = TRUE;
               $result = json_decode($this->call_api($data, 'post'),true);
            }
            $user = $this->db->where('id', $uid)->get('User')->first_row('array');
            unset($user['password']);
            $user['avatar'] = !empty($user['avatar'])?image_url($user['avatar'], 'head', 'hhdp'):'';
            //更新账户状态
            $this->db->where('id', $uid)->update('User', array('status'=>0));
            //更新COOKIE
    		$this->_update_cookie($user);
    		$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('complete_edit_success'), 'refer'=>'/index'));
    	}else{    		
    		$this->display('complete');
    	}
    }
    
    /**
     * 验证用户名
     * Create by 2012-5-4
     * @author liuw
     * @param string $username
     */
    function check_username($username=''){
    	$count = $this->db->where('username', $username)->count_all_results('User');
    	$result['code'] = isset($count)&&$count > 0 ? 0 : 1;
    	$this->echo_json($result);
    }
    
    /**
     * 敏感词审核
     * Create by 2012-8-1
     * @author liuw
     */
    function check_taboo(){
    	if($this->is_post()){
    		$content = $this->post('content');
    		if(empty($content))
    			$this->echo_json(array('code'=>1));
    		else{
    			//检查敏感词
    			if(check_taboo($content, 'user'))
    				$result = array('code'=>0, 'msg'=>$this->lang->line('user_content_has_taboo'));
    			else 
    				$result = array('code'=>1);
    			$this->echo_json($result);
    		}
    	}
    }
    
    /**
     * 验证邮箱
     * Create by 2012-5-4
     * @author liuw
     * @param string $email
     */
    function check_email(){
    	if($this->is_post()){
	    	$email = $this->post('email');
	    	if(!isset($email)||empty($email))
	    		$result['code'] = 1;
	    	else{
		    	$count = $this->db->where('email', $email)->count_all_results('User');
		    	$result['code'] = isset($count)&&$count > 0 ? 0 : 1;
	    	}
	    	$this->echo_json($result);
    	}
    }
    
    /**
     * 检查是否已登录
     * Create by 2012-5-16
     * @author liuw
     */
    function is_signin(){
    	$return['code'] = !empty($this->auth) ? 1 : 0;
    	$this->echo_json(($return));
    }
    
    /**
     * 找回密码
     * Create by 2012-6-19
     * @author liuw
     */
    function reset_pwd($step=0, $code=''){
    	$this->assign('step', $step);
    	switch($step){
    		case 1://重置密码
    			if($this->is_post()){
    				$rp_id = $this->post('rp_id');
    				$email = $this->post('email');
                    $password = $this->post('newpwd');
                    $repassword = $this->post('repwd');
                    if($password !== $repassword) {
                        $this->echo_json(array('code'=>0, 'msg'=>'输入密码不一致'));
                    }
    				$edit = array('password'=>strtoupper(md5($password)));
    				//修改密码
    				$this->db->where('email', $email)->update('User', $edit);
    				//更新请求状态
    				$this->db->where('id', $rp_id)->update('UserResetPwd', array('isUse'=>1));
    				$this->_update_cookie(null, -1);
    				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('reset_pwd_suc')));
    			}else{
    				//检查code是否过期
    				$rs = $this->db->query('SELECT * FROM UserResetPwd WHERE secretCode=? AND isUse=0', array($code))->first_row('array');
    				if(empty($rs)){
    					$this->assign('err', 1);
    					$this->assign('msg', '连接已过期或失效，请重新申请重置密码');
    				}else{
    					$now = time();
    					$create = strtotime($rs['applyDate']);
    					$minus = abs($create - $now);
    					if($minus >= 24*60*60){//超时了
	    					$this->assign('err', 1);
	    					$this->assign('msg', $this->lang->line('reset_timeout'));
    					}else{
    						$this->assign('email', $rs['email']);
    						$this->assign('rpid', $rs['id']);
    					}
    				}
   				 	$this->display('reset_pwd');
    			}
    			break;
    		default://查询邮箱
    			if($this->is_post()){
    				$email = $this->post('email');
    				if(empty($email)) {
    					$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('reset_email_null')));
    				} else {
    					//检查邮箱
    					$rs = $this->db->query('SELECT * FROM User WHERE email=?', array($email))->first_row('array');
    					if(empty($rs)) {//用户不存在
    						$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('signin_user_null')));
    					} else {
    						//构造邮件链接
    						$query_string = strtoupper(md5('email:'.$email.'-'.microtime(true)));
    						//保存
    						$data = array(
    							'email' => $email,
    							'secretCode' => $query_string,
    							'applyDate' => date('Y-m-d H:i:s'),
    							'isUse' => 0
    						);
                            // 检查邮箱是否已经存在
                            $row = $this->db->select('email')->from('UserResetPwd')->where("email = '{$email}'")->get()->row_array();
    						if($row) {
    						    $this->db->where("email = '{$email}'")->update('UserResetPwd', $data);
    						} else {
    						    $this->db->insert('UserResetPwd', $data);
    						}
    						$id = $this->db->insert_id();
    						if($id < 0) {
    							$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_error')));
    						} else {
    							//邮件内容
    							$link = base_url() . 'reset_pwd_1/' . $query_string;
    							$replies = array('reset_url'=>$link);
    							$content = format_msg($this->lang->line('reset_mail_content'), $replies);
    							//发邮件
    							$emails = array(
    								array('mail'=>$email),
    							);
    							$this->load->library('mail');
    							$result = $this->mail->send($emails, $this->lang->line('reset_mail_subject'), $content);
    							if($result['code'] == 1){// 邮件发送成功
    								$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('reset_mail_send_suc')));
    							}else{
    								$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('reset_mail_send_err')));
    							}
    						}
    					}
    				}
    			}else{
   				 	$this->display('reset_pwd');
    			}
    			break;
    	}
    }

    private function check_captcha($valicode){
    	$sess_captcha = $this->session->userdata($this->config->item('sess_captcha'));
   	//	exit(md5(strtolower($valicode)).':'.$sess_captcha);
    	if(md5(strtolower($valicode)) != $sess_captcha)
    		return FALSE;
    	else 
    		return TRUE;
    }
    
    /**
     * 是否已经登陆过了
     * 需要调用这个函数的页面是，已登录不能访问的页面。需要调转到用户页面
     */
    function _is_signin() {
        if($this->auth['id']) {
            // 已经登录了，那么跳转到用户页面，
            forward('/user/' . $this->auth['id']);
        }
    }
}