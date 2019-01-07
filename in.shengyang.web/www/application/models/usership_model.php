<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserShip表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Usership_Model extends MY_Model {
	
	/**
	 * 检查关注状态
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 * @param mixed $fuids
	 * @return mixed
	 */
	public function check_ship($uid, $fuids){
		if(is_array($fuids)){
			$list = array();
			$query = $this->db->where('follower', $uid)->where_in('beFollower', $fuids)->get($this->_tables['usership'])->result_array();
			foreach($query as $row){
				$list[$row['beFollower']] = true;
			}
			unset($query);
			return $list;
		}else{
			$count = $this->db->where(array('follower'=>$uid, 'beFollower'=>$fuids))->count_all_results($this->_tables['usership']);
			return $count > 0;
		}
	}
	
	/**
	 * 取消关注
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 * @param int $fuid
	 */
	public function un_follow($uid, $fuid){
		//先判断是否已经关注了对方，才能取消关注
		$is_followed = $this->check_ship($uid,$fuid);
		if($is_followed){
			$this->db->where(array('follower'=>$uid, 'beFollower'=>$fuid))->delete($this->_tables['usership']);
			//粉丝数-1
			$this->db->where('id', $fuid)->set('beFollowCount', 'beFollowCount-1', false)->update($this->_tables['user']);
			//关注数-1
			$this->db->where('id', $uid)->set('followCount', 'followCount - 1', false)->update($this->_tables['user']);
			return true;
		}
		else{
			return false;
		}
	}
	
	/**
	 * 添加一条关注记录
	 * Create by 2012-12-28
	 * @author liuweijava
	 * @param int $uid
	 * @param int $fuid
	 */
	public function follow($uid, $fuid){
		//先判断是否已经关注了对方，已经关注不能再关注
		$is_followed = $this->check_ship($uid,$fuid);
		if(!$is_followed){
			$arr = array('follower'=>$uid, 'beFollower'=>$fuid);
			$this->db->insert($this->_tables['usership'], $arr);
			//粉丝数+1
			$this->db->where('id', $fuid)->set('beFollowCount', 'beFollowCount+1', false)->update($this->_tables['user']);
			//关注数+1
			$this->db->where('id', $uid)->set('followCount', 'followCount+1', false)->update($this->_tables['user']);
			return true;
		}
		else{
			return false;
		}
	}
	
}