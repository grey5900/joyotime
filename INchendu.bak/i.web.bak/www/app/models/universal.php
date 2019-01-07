<?php
/**
 * 一些比较常用的业务逻辑移到这个通用模型里面
 * Create by 2012-8-21
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Universal extends CI_Model{
	
	/**
	 * 获取自己的好友备注列表，如果没有备注则返回FALSE
	 * Create by 2012-8-21
	 * @author liuw
	 * @param int $uid
	 * @return mixed
	 */
	function get_my_desc($uid){
		$cache_id = 'cache_user_desc_'.$uid;
		$cache_arr = $this->config->item('user_desc_conf');
		$cache_type = $cache_arr['cache_type'];
		//先从缓存获取
		$data = get_cache_data($cache_type, $cache_id);
		if(!isset($data) || empty($data)){//没有缓存，从数据库查询相关数据并更新缓存
			//查询数据
			$data = array();
			$query = $this->db->where('uid', $uid)->get('UserMnemonic')->result_array();
			foreach($query as $row){
				$data[$row['mUid']]=$row['mnemonic'];
			}
			//更新缓存
			$cache_arr['cache_id'] = $cache_id;
			$cache_arr['cache_data'] = $data;
			update_cache($cache_arr);
		}
		empty($data) && $data = array();
		return $data;
	}
	
	/**
	 * 更新自己的好友备注数据，先更新数据库，再更新缓存
	 * Create by 2012-8-21
	 * @author liuw
	 * @param int $uid
	 * @param array $new_desc
	 */
	function set_my_desc($uid, $m_uid, $mnemonic){
		$cache_arr = $this->config->item('user_desc_conf');
		//更新数据库
		if(!empty($mnemonic)){
			$count = $this->db->where(array('uid'=>$uid, 'mUid'=>$m_uid))->count_all_results('UserMnemonic');
			if($count)//修改
				$this->db->where(array('uid'=>$uid, 'mUid'=>$m_uid))->update('UserMnemonic', array('mnemonic'=>$mnemonic));
			else//新增
				$this->db->insert('UserMnemonic', array('uid'=>$uid, 'mUid'=>$m_uid, 'mnemonic'=>$mnemonic));
		}else{//取消备注
			$this->db->where(array('uid'=>$uid, 'mUid'=>$m_uid))->delete('UserMnemonic');
		}
		//更新缓存
		$data = array();
		//查询数据 
		$query = $this->db->where('uid', $uid)->get('UserMnemonic')->result_array();
		foreach($query as $row){
			$data[$row['mUid']] = $row['mnemonic'];
		}
		
		$cache_arr['cache_id'] = 'cache_user_desc_'.$uid;
		$cache_arr['cache_dataa'] = $data;
		update_cache($cache_arr);
	}
	
}
   
 // File end