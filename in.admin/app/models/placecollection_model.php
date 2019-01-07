<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * PlaceCollection表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Placecollection_Model extends MY_Model {
	
	function __construct(){
		parent::__construct();
		$this->taboo = get_data("taboo_post");
	}
	
	//地点册的itemType=20
	function get_placecollection_list($where = '' , $pagesize = 20 , $offset = 0 , $order_by = 'lastUpdate desc' , $having = ''){
		//$sql = "SELECT pc.* , IFNULL(m.read,0) as isread ,IFNULL(r.itemId,0) as isrecommend from 
		//		MorrisUgcExtraInfo m right join Post p on p.id = m.itemId and p.type = m.itemType
		//		Left join  HomePageData r on r.itemId = p.id and r.itemType = 20"; 
		$sql = "SELECT pc.* , IFNULL(m.read,0) and pc.lastUpdate<m.createDate as isread ,
				IFNULL(r.expireDate,0) as isrecommend, GROUP_CONCAT(CONCAT(p.id,'-',p.placename)) as placeinfos ,
				count(p.id) as place_count,IF(pc.lastUpdate, pc.lastUpdate, pc.createDate) as orderDate  from 
				MorrisUgcExtraInfo m 
				right join PlaceCollection pc on pc.id = m.itemId and m.itemType=20
				left join PlaceCollectionOwnPlace pcop on pcop.pcId = pc.id 
				left join Place p on pcop.placeId = p.id 
				left join HomePageData r on r.itemId = pc.id and r.itemType = 20 
				";
		$sql .= $where ? " where ".$where : "";
		$sql .= $having ? " having " .$having : "";
		$sql .=	" group by pc.id";
		$sql .= " order by ".$order_by;
		$sql .= " limit $offset,$pagesize ";
		
		
		$list = $this->db2->query($sql)->result_array();
		
		$this->load->model("user_model","m_user");
		
		foreach($list as &$row){
			
			
			$row['places'] = $this->get_places($row['id']);
			$row['place_count'] = count($row['places']);
			
			$users = $this->m_user->get_user($row['uid']);
			//var_dump($users);
			$row['user'] = $users[0];
			
			$taboo_replacement = $this->taboo;
			array_walk($taboo_replacement,"highlight");
			
			//如果有屏蔽词，高亮屏蔽词
			if($row['isTaboo']){
				$row['summary'] = str_replace($this->taboo,$taboo_replacement,$row['summary']);
				$row['name'] = str_replace($this->taboo,$taboo_replacement,$row['name']);
			}
		}
		
		return $list;
	}
	function count_placecollection($where = '' ,$having  = '' ){
		$sql = "SELECT count(distinct pc.id) c from 
				MorrisUgcExtraInfo m 
				right join PlaceCollection pc on pc.id = m.itemId and m.itemType=20
				left join PlaceCollectionOwnPlace pcop on pcop.pcId = pc.id 
				left join Place p on pcop.placeId = p.id 
				left join HomePageData r on r.itemId = pc.id and r.itemType = 20 
				 ";
		$sql .= $where ? " where ".$where : "";
		$sql .= $having ? " having " .$having : "";
		
		$row = $this->db2->query($sql)->row_array(0);
		return $row['c'];
	}
	
	function get_places($pcid){
		$pla = $this->db->select("isTaboo")->where("id",$pcid)->get($this->_tables['placecollection'])->row_array(0);
		
		$sql = "SELECT p.id,p.placename,pco.content as des from Place p left join PlaceCollectionOwnPlace pco on
				pco.placeId = p.id where pco.pcId=".$pcid;
		
		$list = $this->db2->query($sql)->result_array();
		
		
		if($pla['isTaboo']){
			$taboo_replacement = $this->taboo;
			array_walk($taboo_replacement,"highlight");

			foreach($list as &$row){
				$row['des'] = str_replace($this->taboo,$taboo_replacement,$row['des']);
			}
		}
		
		return $list;
	}
}