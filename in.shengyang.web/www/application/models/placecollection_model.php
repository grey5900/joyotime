<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PlaceCollection表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Placecollection_Model extends MY_Model {
	
	function list_place_collection($page = 1,$pagesize = 6,$order='new',$c_where = '',$show_place = 3){
		//地点册的status = 0
		
		$where = ' pc.status = 0 ';
		if($order == 'essence'){
			$where .= ' AND pc.isEssence = 1';
		}
		$c_where && $where .= $c_where;
		$this->db->select('pc.*');//诶，好像不用关联任何东西了
		$this->db->where($where,null,false);
		$this->db->from($this->_tables['placecollection'].' pc');
		$order == 'wen' && $this->db->order_by('createDate','asc');
		$order == 'new' && $this->db->order_by('createDate','desc');
		$order == 'hot' && $this->db->order_by('praiseCount','desc'); 
		$order == 'essence' && $this->db->order_by('essenceScore','desc')->order_by('createDate','desc'); 
		$this->db->limit($pagesize,($page-1)*$pagesize);
		$list = $this->db->get()->result_array();
		
		foreach($list as &$row){
			//取用户
			$user = get_data('user',$row['uid']);
			
			if($this->auth){
				$user_mnemonic = get_data('mnemonic', $this->auth['uid'] . '-' . $row['uid']);
				$row['nickname'] = $user_mnemonic['mnemonic'];
			}
			$row['nickname'] = $row['nickname'] ? $row['nickname'] : $user['name'];
			$row['avatar_m'] = $user['avatar_m'];
			
			if($show_place){
				//获取$show_place个地点
				$plist = $this->db->select('placeId,content')->where('pcId',$row['id'])->limit($show_place)->get($this->_tables['placecollectionownplace'])->result_array();
				$places = array();
				foreach($plist as $r){
					//$places[] = get_data('place',$r['placeId']);
					$p = get_data('place',$r['placeId']);
					$p['content'] = $r['content'];
					$places[] = $p;
					unset($p);
				}
				$row['places'] = $places;
			}
			
		}
		return $list;
	}
	
	function count_place_collections($sort = 'new' , $where = ''){
		// sort = new / hot 都无所谓 essence 时 增加where条件
		$this->db->where('status',0);
		$sort == 'essence' && $this->db->where('isEssence',1);
		$where && $this->db->where($where,null,false);
		$total = $this->db->count_all_results($this->_tables['placecollection']);
		return $total;
	}
	
	function get_placecollection($id , $page =1 , $pagesize = 3){
		if(!intval($id)){
			return array();
		}
		else{
			$pc = $this->db->where('id',$id)->get($this->_tables['placecollection'])->row_array();
			$user = get_data('user',$pc['uid']);
			if($this->auth){
				$user_mnemonic = get_data('mnemonic', $this->auth['uid'] . '-' . $pc['uid']);
				$user['name'] = $user_mnemonic['mnemonic'];
			}
			$plist = $this->db->select('placeId,content')->where('pcId',$pc['id'])->limit($pagesize,($page-1)*$pagesize)->get($this->_tables['placecollectionownplace'])->result_array();
			$place_count = $this->db->where('pcId',$pc['id'])->count_all_results($this->_tables['placecollectionownplace']);
			$places = array();
			foreach($plist as $r){
				$p = get_data('place',$r['placeId']);
				$p['content'] = $r['content'];
				$places[] = $p;
			}
			
			$pc['user'] = $user;
			$pc['places'] = $places;
			$pc['pcount'] = $place_count;
			//还要取赞踩详情
			
			$p_s_list = $this->db->where('pcId',$id)->order_by('createDate','desc')->limit(18)->get($this->_tables['placecollectionappraiser'])->result_array();
			foreach($p_s_list as &$ps){
				$t_user = get_data('user',$ps['uid']);
				$ps['avatar'] = $t_user['avatar_m'];
				$ps['nickname'] = $t_user['name'];
			}
			$pc['ps_list'] = $p_s_list;
			
			return $pc;
		}
	}
	
	public function get_collection_timeline($id){
		$month_cn = $this->config->item('month_CN');
		$col_id = 'uid';
		$this->db->select('YEAR(createDate) AS c_year, MONTH(createDate) AS c_month');
		$this->db->distinct();
		$this->db->where(array($col_id=>$id, 'status'=>0 ));  // status < 2
		//$this->db->where_in("type",array(2,3,4));
		$list = array();
		$query = $this->db->group_by('createDate')->order_by('createDate', 'desc')->get($this->_tables['placecollection'])->result_array();
		foreach($query as $k=>$row){
			$list[$row['c_year']][$k] = array($row['c_month'], $month_cn[intval($row['c_month'])]);	
		}
		unset($query);
		return $list;
	}
}