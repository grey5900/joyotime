<?php
/**
 * 第三方登录、绑定、同步、解绑相关
 * Create by 2012-7-27
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Oauth extends MY_Controller{
	
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
		$replace = array('platform'=>$stage_code==1?'腾讯微博':'新浪微博');
		if($rs>0){
			$code = 1;
			$msg = format_msg($this->lang->line('user_bind_success'), $replace);
		}else{
			$code = 0;
			$msg = format_msg($this->lang->line('user_bind_faild'), $replace);
		}
		//从SESSION中获取跳转路径
		$jump = $this->session->flashdata('bind_jump');
		$jump = isset($jump)&&!empty($jump)?$jump:'/user_sync';
		$this->showmessage($msg, $jump);
	}
	
	/**
	 * 取消第三方绑定
	 * Create by 2012-7-27
	 * @author liuw
	 * @param int $stage_code,0＝新浪微博，1＝腾讯微博
	 */
	public function unbind($stage_code, $uid){		
		//叫兽说解绑只是删除对应绑定表的数据，所以改成直接操作数据库，避免调用接口失败的情况		
		$table = $stage_code ? 'UserTencentBindInfo' : 'UserSinaBindInfo';//确定绑定表
		//删除记录
		$this->db->where('uid', $uid)->delete($table);
		$out['code'] = 1;
		$replace = array('platform'=>$stage_code == 1 ? '腾讯微博' : '新浪微博');
		$out['msg'] = format_msg($this->lang->line('user_unbind_success'), $replace);
		$this->echo_json($out);
	}
	
	/**
	 * 第三方登录,传出参数
	 * Create by 2012-7-27
	 * @author liuw
	 * @param int $uid
	 * @param int $is_new,0是老用户，1是新用户
	 */
	public function login($uid, $is_new){
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
			redirect('/complete/'.$uid, 'location', '301');
		}else{
			redirect('/index', 'location', '301');
		}
	}
	
	public function login_error($err_msg){
		$this->showmessage(urldecode($err_msg), '/login');
	}
	
	public function bind_error($err_msg){
		$this->showmessage(urldecode($err_msg), '/user_sync');
	}
	
}   
   
 // File end