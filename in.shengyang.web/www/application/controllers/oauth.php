<?php
/**
 * Create by 2012-12-12
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Oauth extends Controller{
	
	public function __construct(){
		parent::__construct();
		$this->load->model('usersinabindinfo_model', 'm_sina');
		$this->load->model('usertencentbindinfo_model', 'm_tencent');
		$this->load->model('usership_model', 'm_uship');
	}
	
	/**
	 * 取消绑定
	 * Create by 2012-12-12
	 * @author liuweijava
	 * @param int $stage_code 0=新浪微博，1=腾讯微博
	 * @param int $uid
	 */
	public function unbind($stage_code, $uid){
		if($stage_code){
			$msg = '已解除绑定腾讯微博';
			$this->m_tencent->delete_by_uid($uid);
		}else{
			$msg = '已解除绑定新浪微博';
			$this->m_sina->delete_by_uid($uid);
		}
		$this->echo_json(array('code'=>0, 'msg'=>$msg));
	}
	
	/**
	 * 检查是否已关注指定的用户
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $follow_id 指定的用户
	 * @return array code:0=未关注，1=未登录；2=已关注
	 */
	public function is_followed($follow_id){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		!$uid && $this->echo_json(array('code'=>1, 'msg'=>'请先登录'));
		//检查
		$is_follow = $this->m_uship->check_ship($uid, $follow_id);
		if($is_follow)
			$this->echo_json(array('code'=>2, 'msg'=>'您已关注过这个用户了'));
		else
			$this->echo_json(array('code'=>0, 'msg'=>'您还没有关注这个用户'));
	}
	
	/**
	 * 第三方绑定
	 * Create by 2012-7-27
	 * @author liuw
	 * @param int $stage_code,0＝新浪微博，1＝腾讯微博
	 */
	public function bind($stage_code, $uid){
		//检查绑定是否成功
		$this->db->where('uid', $uid);
		if($stage_code == 1)//腾讯微博
			$this->db->from('UserTencentBindInfo');
		else
			$this->db->from('UserSinaBindInfo');
		$rs = $this->db->count_all_results();
		$replace = $stage_code==1?'腾讯微博':'新浪微博';
		if($rs>0){
			$code = 1;
			$msg = sprintf('您绑定了%s', $replace);
		}else{
			$code = 0;
			$msg = sprintf('绑定%s失败了', $replace);
		}
		$this->showmessage($msg);
	}
	
	/**
	 * 绑定出错
	 * @param 错误提示 $err_msg
	 */
	public function bind_error($err_msg){
	    $this->showmessage(urldecode($err_msg), '/profile/sync');
	}

	
	/**
	 * 第三方登录,传出参数
	 * Create by 2012-7-27
	 * @author liuw
	 * @param int $uid
	 * @param int $is_new,0是老用户，1是新用户
	 */
	public function login($uid, $is_new) {
		//查询用户
		$user = $this->db->where('id', $uid)->get('User')->first_row('array');
		//头像
		$user['avatar_uri'] = !empty($user['avatar'])?image_url($user['avatar'], 'head', 'hhdp') : 'http://pic-a.out.chengdu.cn/head/hhdp/201207/19/201207_19_155251896_22625.jpg';
		$user['username'] = !empty($user['username']) ? $user['username'] : '';
		$user['nickname'] = !empty($user['nickname']) ? $user['nickname'] : '';
		$user['gender'] = !empty($user['gender']) ? $user['gender'] : 0;
		
		//保存cookie
		$this->_update_cookie($user);
		if($is_new){
			redirect('/complete/'.$uid, 'location', REDIRECT_CODE);
		}else{
			redirect('/index', 'location', REDIRECT_CODE);
		}
	}
	
	public function login_error($err_msg) {
		$this->showmessage(urldecode($err_msg), '/login');
	}
}
   
 // File end