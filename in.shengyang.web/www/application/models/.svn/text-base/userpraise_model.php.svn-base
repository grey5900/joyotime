<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserPraise表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userpraise_Model extends MY_Model {
	
	/**
	 * 检查是否赞过了
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param int $item_id
	 * @param int $item_type
	 * @return boolean
	 */
	public function check_praise($uid, $item_id, $item_type){
		$c = $this->db->where(array('uid'=>$uid, 'postId'=>$item_id, 'type'=>1))->count_all_results($this->_tables['postappraise']);
		return $c <= 0;
	}
	
	/**
	 * 赞
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param int $item_id
	 * @param int $type 1:顶 -1:踩
	 * @return int 0=成功；1=插入赞表失败；2=更新资源被赞次数失败；3=更新FEED的被赞次数失败；4=推送失败
	 */
	public function praise_item($uid, $item_id, $type){
		//记录主表
		$data = array('uid'=>$uid, 'postId'=>$item_id, 'type'=>$type);
		$this->db->insert($this->_tables['userpraise'], $data);
		$insert_id = $this->db->insert_id();
		$item_type = 19; // post 的itemType
		if(!$insert_id)
			return 1;
		else{
			//更新资源的被赞次数
			$this->db->where('id', $item_id)->set('praiseCount', 'praiseCount+1', false)->update($this->_tables['post']);
			//更新FEED的被赞次数
			$this->db->where(array('itemId'=>$item_id, 'itemType'=>19))->set('praiseCount','praiseCount+1',false)->update($this->_tables['userfeed']);
			//发推送
			$this->db->select($this->_tables['post'].'.*, '.$this->_tables['place'].'.placename');
			$this->db->join($this->_tables['place'], $this->_tables['place'].'.id = '.$this->_tables['post'].'.placeId', 'left');
			$post = $this->db->where($this->_tables['post'].'.id', $item_id)->limit(1)->get($this->_tables['post'])->first_row('array');
			$this->load->helper('api');
			
			//update 2013/1/5 by zr  当uid=自己的时候不发送消息
			if($post['uid']==$this->auth['uid']) return 0;
			else return send_sys_msg(2, $item_type, $item_id, $post) ? 0 : 4;
		}
	}
	/*
	public function praise_item_bak($uid, $item_id, $item_type){
		//记录主表
		$data = array('uid'=>$uid, 'itemId'=>$item_id, 'itemType'=>$item_type);
		$this->db->insert($this->_tables['userpraise'], $data);
		$insert_id = $this->db->insert_id();
		if(!$insert_id)
			return 1;
		else{
			//更新资源的被赞次数
			$this->db->where('id', $item_id)->set('praiseCount', 'praiseCount+1', false)->update($this->_tables['post']);
			//更新FEED的被赞次数
			$this->db->where(array('itemId'=>$item_id, 'itemType'=>$item_type))->set('praiseCount','praiseCount+1',false)->update($this->_tables['userfeed']);
			//发推送
			$this->db->select($this->_tables['post'].'.*, '.$this->_tables['place'].'.placename');
			$this->db->join($this->_tables['place'], $this->_tables['place'].'.id = '.$this->_tables['post'].'.placeId', 'left');
			$post = $this->db->where($this->_tables['post'].'.id', $item_id)->limit(1)->get($this->_tables['post'])->first_row('array');
			$this->load->helper('api');
			
			//update 2013/1/5 by zr  当uid=自己的时候不发送消息
			if($post['uid']==$this->auth['uid']) return 0;
			else return send_sys_msg(2, $item_type, $item_id, $post) ? 0 : 4;
		}
	}*/
	
}