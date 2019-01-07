<?php
/**
 * 用户中心
 * Create by 2012-12-11
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Profile extends AuthController{
	
	public function __construct(){
		parent::__construct();
		//数据模型
		$this->load->model('user_model', 'm_user');
		$this->load->helper('cache');
		$this->load->helper('api');
	}
	
	/**
	 * 设置详情
	 * Create by 2012-12-11
	 * @author liuweijava
	 */
	public function setting(){
		$uid = $this->auth['uid'];
		
		$info = get_data('user', $uid);
		if($info['birthdate']){
			$info['b_year'] = intval(explode("-",$info['birthdate'])[0]);
			$info['b_month'] = intval(explode("-",$info['birthdate'])[1]);
			$info['b_day'] = intval(explode("-",$info['birthdate'])[2]);
		}
		if($this->is_post()){		
			$edit = array();
			$nickname = $this->post('nickname');
			if(!empty($nickname)) {
				$edit['nickname'] = $nickname;
				// 更新COOKIE
				$this->auth['nickname'] = $edit['nickname'];
				$this->load->helper('profile');
				update_auth($this->auth);
			}
			$description = $this->post('description');
			if(!empty($description))
				$edit['description'] = $description;
			$gender = $this->post('gender');
			if(!empty($gender))
				$edit['gender'] = intval($gender)-1;
			$tel = $this->post('tel');
			if(!empty($tel))
				$edit['cellphone_no'] = $tel;
			$invite_uid = trim($this->post('invite_uid'));
			if(isset($invite_uid) && !empty($invite_uid)){
				$edit['invite_uid'] = $invite_uid;
			}
			//保存生日
			$birth_year = intval($this->post('birth_year'));
			$birth_month = intval($this->post('birth_month'));
			$birth_day = intval($this->post('birth_day'));
			$birth_date = '';
			if($birth_year && $birth_month && $birth_day){
				$birth_date = $birth_year . '-' . $birth_month . '-' . $birth_day;
			}
			$data = array('basic'=>$edit, 'birth'=>$birth_date);
			$this->echo_json($this->m_user->edit_basic($uid, $data));
		}else{
			$method = 'setting';
			$this->assign(compact('method', 'info'));
			$this->display('setting');
		}
	}
	
	/**
	 * 修改头像
	 * Create by 2012-12-11
	 * @author liuweijava
	 */
	public function avatar(){
		$uid = $this->auth['uid'];
		
		$info = get_data('user', $uid);
		if($this->is_post()){
			//图片参数
			$this->config->load('config_image');
			$cfg = $this->config->item('image_cfg');
			//裁切图片的坐标
			$x1 = $this->post('x1');
			$y1 = $this->post('y1');
			$x2 = $this->post('x2');
			$y2 = $this->post('y2');
			$src = $this->post('src');
			if(empty($src)){
				$json = array('code'=>-1, 'msg'=>'请选择要上传的头像！');
				$this->echo_json($json);
			}
			//FRAMEWORK_PATH . 'www/'
			$avatar = $cfg['upload_path'] . str_replace('./', '', basename($src));//本地绝对路径
			list($w, $h, $t) = getimagesize($avatar);
			
			$this->load->library('image_factory');
			
			
			$this->image_factory->resizeThumbnailImage($avatar, $avatar, $x2-$x1, $y2-$y1, $x1, $y1,1 );//裁切头像
			//调用接口把头像上传到图片服务器
			$this->load->helper('api');
			$attr = array(
				'uid'=>$uid,
				'@uploaded_file'=>'@'.$avatar
			);
			$result = json_decode(call_api('up_avatar', $attr), true);
			//删除本地文件
			unlink($avatar);
			if(empty($result['result_code'])){
				//更新缓存
				get_data('user', $uid, true);
				$avatar = $result['avatar_uri'];
				// 更新COOKIE
				$this->auth['avatar_uri'] = $avatar;
				$this->load->helper('profile');
				update_auth($this->auth);
				$json = array('code'=>0, 'msg'=>lang_message('edit_avatar_success'), 'avatar'=>$avatar);
			}else{
				$json = array('code'=>1, 'msg'=>lang_message('edit_avatar_fail'));
			}
			$this->echo_json($json);
		}else{
			//本地图片地址
			$this->config->load('config_image');
			$upload_path = $this->config->item('image_cfg')['upload_view'];
			$method = 'avatar';
			$this->assign(compact('method', 'info', 'upload_path'));
			$this->display('avatar');
		}
	}
	
	/**
	 * 修改登录密码
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @return array code:0=修改成功；1=原密码错误；2=修改失败；3=原密码为空；4=新密码为空；5=两次新密码不一致
	 */
	public function revisepassword(){
		$uid = $this->auth['uid'];
		
		$info = $this->m_user->get_info($uid);//get_data('user', $uid);
		if($this->is_post()){
			$old = $this->post('oldpwd');
			$new = $this->post('newpwd');
			$renew = $this->post('renew');
			if(empty($old))
				$this->echo_json(array('code'=>3, 'msg'=>lang_message('modify_pwd_empty', array('原密码'))));
			else if(empty($new))
				$this->echo_json(array('code'=>4, 'msg'=>lang_message('modify_pwd_empty', array('新密码'))));
			else if($renew !== $new)
				$this->echo_json(array('code'=>5, 'msg'=>lang_message('modify_pwd_newpwd_not_identical')));
			else{
				$this->echo_json($this->m_user->modify_pwd($info, $old, $new));
			}
		}else{
			$method = 'revisepassword';
			$this->assign(compact('method', 'info'));
			$this->display('revisepassword');
		}
		
	}
	
	/**
	 * 设置同步
	 * Create by 2012-12-11
	 * @author liuweijava
	 */
	public function sync(){
		$uid = $this->auth['uid'];
		
		$info = get_data('user', $uid);
		$method = 'sync';
		//第三方平台
		$this->config->load('config_oauth');
		$syncs = $this->config->item('sync_platforms');
		foreach($syncs as $k=>&$s){
			switch($s['name']){
				case '新浪微博':
					$s['table'] = $this->_tables['usersinabindinfo'];
					//获取新浪微博的绑定状态
					$this->load->model('usersinabindinfo_model', 'm_sina');
					$count = $this->m_sina->count_by_uid($uid);
					$s['has_bind'] = $count ? 1 : 0;
					break;
				case '腾讯微博':
					$s['table'] = $this->_tables['usertencentbindinfo'];
					//获取腾讯微博的绑定状态
					$this->load->model('usertencentbindinfo_model', 'm_tencent');
					$count = $this->m_tencent->count_by_uid($uid);
					$s['has_bind'] = $count ? 1 : 0;
					break;
			}
			unset($s);
		}
		//API地址
		$this->config->load('config_api');
		$api_cfg = $this->config->item('api');
		$api_uri = $api_cfg['url'].$api_cfg['path']['oauth_bind'];
		$this->assign(compact('method', 'info', 'syncs', 'api_uri', 'uid'));
		$this->display('sync');
	}
	
	/**
	 * 修改邮箱
	 * Create by 2012-12-11
	 * @author liuweijava
	 */
	public function email(){
		$uid = $this->auth['uid'];
		
		$info = $this->m_user->get_info($uid);//get_data('user', $uid);
		if($this->is_post()){
			$pwd = $this->post('password');
			$email = $this->post('email');
			//检查登录密码
			if(strtoupper(md5($pwd)) !== $info['password']){
				$this->echo_json(array('code'=>1, 'msg'=>'用户名或密码不正确'));
			}else{
				//同一个邮箱只能绑定一次。。
				$exists = $this->m_user->select_by_email($email);
				if(!empty($exists)){
					$this->echo_json(array('code'=>-1, 'msg'=>'您输入的邮箱已经被绑定，请更换一个邮箱！'));
				}else{
					$this->m_user->update_by_id($uid, array('email'=>$email));
					$this->echo_json(array('code'=>0, 'msg'=>'邮箱修改成功'));
				}
			}
		}else{
			$method = 'email';
			$this->assign(compact('method', 'info'));
			$this->display('email');
		}
	}
	
	/**
	 * 积分规则
	 * Create by 2012-12-11
	 * @author liuweijava
	 */
	public function score(){
		$uid = $this->auth['uid'];
		
		$info = get_data('user', $uid);
		$method = 'score';
		
		//查询积分规则列表
		$this->load->model('userpointcase_model', 'm_upc');
		$list = $this->m_upc->get_list();
		$this->assign(compact('method', 'info', 'list'));
		$this->display('score');
	}
	
	/**
	 * 第三方注册用户完善资料
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $uid
	 * @return array code:0=操作成功，1=用户名长度错误，2=密码长度错误，3=两次密码不一致，4=用户名重复，5=用户名包含敏感词，6=与API通信失败，其他=API报错
	 */
	public function complete($uid){
		$uid = formatid($uid);
		$user = get_data('user', $uid);
		if($this->is_post()){
			//用户名
			$username = $this->post('username');
			//密码
			$password = $this->post('password');
			$repwd = $this->post('password_again');
			
			if(cstrlen($username) < 2 || cstrlen($username) > 15)//检查用户名长度
				$this->echo_json(array('code'=>1, 'msg'=>lang_message('signup_username_len_fail')));
			elseif(cstrlen($password) < 2 || cstrlen($password) > 15) //检查密码长度
				$this->echo_json(array('code'=>2, 'msg'=>lang_message('signup_password_len_fail')));
			elseif($repwd !== $password) //检查两次输入的密码是否一致
				$this->echo_json(array('code'=>3, 'msg'=>lang_message('signup_password_fail')));
			else{
				//检查用户名是否重复
				$c = $this->m_user->select(array('username'=>$username));
				if(!empty($c))
					$this->echo_json(array('code'=>4, 'msg'=>lang_message('signup_username_used')));
				elseif(!check_taboo($username, 'user'))//检查用户名是否包含敏感词
					$this->echo_json(array('code'=>5, 'msg'=>lang_message('signup_username_taboo')));
				else{
					$info = compact('uid', 'username', 'password');
					//其他属性
					$nickname = $this->post('nickname');
					!empty($nickname) && $info['nickname'] = $nickname;
					$invit_uid = $this->post('recommender');
					!empty($invit_uid) && $info['invite_uid'] = $invit_uid;
					//更新基本资料
					$result = decode_json(call_api('setting', $info), true);
					if(empty($result) || $result['result_code']){
						if(empty($result))
							$rs = array('code'=>6, 'msg'=>'通信失败');
						else 
							$rs = array('code'=>$result['result_code'], 'msg'=>$result['result_msg']);
						$this->echo_json($result);
					}else{
						//更新邮箱
						$email = $this->post('email');
						if(!empty($email)){
							$data = compact('uid', 'password', 'email');
							call_api('up_email', $data);
						}
						//更新缓存
						get_data('user', $uid, true);
						$this->echo_json(array('code'=>0, 'msg'=>lang_message('complete_success')));
					}
				}
			}
		}else{
			$this->assign('info', $user);
			$this->display('complete');
		}
	}
} 
   
 // File end