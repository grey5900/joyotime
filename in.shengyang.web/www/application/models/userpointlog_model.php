<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * UserPointLog表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Userpointlog_Model extends MY_Model {
    /**
     * 获取用户的任务列表
     * @param int $uid
     */
    function list_user_task($uid) {
        $sql = "SELECT a.id, a.caseName, a.`point`,
        IF((SELECT b.uid FROM {$this->_tables['userpointlog']} b
        WHERE a.id = b.pointCaseId AND b.uid = '{$uid}'
        GROUP BY b.pointCaseId), 1, 0) as isCompleted
        FROM {$this->_tables['userpointcase']} a
        WHERE a.isOnly = 1 GROUP BY a.id";
    
        return $this->db->query($sql)->result_array();
    }
    
    /**
     * 包租婆收租排行
     * 
     * */
    function list_rent(){
    	//昨天 征收userpintcaseid 29
    	$yesterday = date('Y-m-d',strtotime("-1 day"));
    	
    	$list = $this->db->select('uid,sum(point) as total')
    			 ->where('pointCaseId',29)
    			 ->where("createDate BETWEEN '{$yesterday} 00:00:00' and '{$yesterday} 23:59:59'",null,false)
    			 ->group_by('uid')
    			 ->order_by('total','desc')
    			 ->limit(25)
    			 ->get($this->_tables['userpointlog'])
    			 ->result_array();
    	foreach($list as &$row){
    		$row['user'] = get_data('user',$row['uid']);
    	}
    	return $list;	 
    }
    
    function get_usePointTicket_list($limit = 3){
    	$pointCaseId = 47;
    	$list = $this->db->where('pointCaseId',$pointCaseId)
    			 ->order_by('createDate','desc')
    			 ->limit($limit)
    			 ->get($this->_tables['userpointlog'])
    			 ->result_array();
    	$data = array();
    	foreach($list as $row){
    		$temp = array();
    		$remark = json_decode($row['remark'],true);
    		$ticket = $this->db->select('placeId')->where('id',$remark['item_id'])->get($this->_tables['pointticket'])->row_array();
    		$placeinfo = get_data('place',$ticket['placeId']);
    		$user = get_data('user',$row['uid']);
    		$temp ['placeid'] = $placeinfo['id'];
    		$temp ['placename'] = $placeinfo['placename'];
    		$temp ['nickname'] = $user['name'];
    		$temp ['uid'] = $row['uid'];
    		$temp ['avatar'] = $user['avatar_m'];
    		$temp ['point'] = $row['point'];
    		
    		$data [] = $temp;
    		unset($temp,$user,$remark,$ticket,$placeinfo);
    	}
    	return $data;		
    }
    
}