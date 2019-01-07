<?php
/**
 * 
 * 专题什么的
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Topic extends Controller{
	
	function __construct(){
		parent::__construct();
	}
	
	function second_ring(){
		$class = 'active';
		$this->assign(compact('class'));
		$this->display("sr_index");
	} 
	function second_ring2(){
		$class = 'active';
		$this->assign(compact('class'));
		$this->display("sr_index2");
	} 
	function second_ring_story(){
		$class = 'active';
		$this->assign(compact('class'));
		$this->display("sr_story");
	}
	function second_ring_story2(){
		$class = 'active';
		$this->assign(compact('class'));
		$this->display("sr_story2");
	}
	function second_ring_places($placeCategory){
		//$placeCategory = intval($this->get('cat'));
		$categorys = array(1,32,17,18,16,50);
		$placeCollectionIds = array(51828,51829,51830,51831,51832,51833,51834,51835,51836,51837);/*,51838*/
		
		$placeCollections = $this->db->where_in("id",$placeCollectionIds)
								 ->get($this->_tables['placecollection'])
								 ->result_array();
		
		foreach($placeCollections as &$row){
			$this->db->select('p.*');
			$this->db->from($this->_tables['place'].' p');
			$this->db->join($this->_tables['placeowncategory'].' poc','poc.placeId=p.id','left');
			$this->db->join($this->_tables['placecategoryship'].' pcs','poc.placeCategoryId = pcs.child','left');
			$this->db->join($this->_tables['placecollectionownplace'].' pcop','p.id = pcop.placeId','left');
			$this->db->where('pcop.pcid',$row['id']);
			$placeCategory ? $this->db->where('(poc.placeCategoryId='.$placeCategory.' or pcs.parent='.$placeCategory.')',null,false)
						   : $this->db->where('(poc.placeCategoryId not in ('.implode(',',$categorys).') and pcs.parent not in ('.implode(',',$categorys).') )',null,false);
			$p_list = $this->db->get()->result_array();
			$row['places'] = $p_list;
		}
		
		$this->assign(compact('placeCollections','placeCategory'));
		$this->display("sr_album");
	}
	function second_ring_places2($placeCategory){
		//$placeCategory = intval($this->get('cat'));
		$categorys = array(1,32,17,18,16,50);
		$placeCollectionIds = array(51828,51829,51830,51831,51832,51833,51834,51835,51836,51837);/*,51838*/
		
		$placeCollections = $this->db->where_in("id",$placeCollectionIds)
								 ->get($this->_tables['placecollection'])
								 ->result_array();
		
		foreach($placeCollections as &$row){
			$this->db->select('p.*');
			$this->db->from($this->_tables['place'].' p');
			$this->db->join($this->_tables['placeowncategory'].' poc','poc.placeId=p.id','left');
			$this->db->join($this->_tables['placecategoryship'].' pcs','poc.placeCategoryId = pcs.child','left');
			$this->db->join($this->_tables['placecollectionownplace'].' pcop','p.id = pcop.placeId','left');
			$this->db->where('pcop.pcid',$row['id']);
			$placeCategory ? $this->db->where('(poc.placeCategoryId='.$placeCategory.' or pcs.parent='.$placeCategory.')',null,false)
						   : $this->db->where('(poc.placeCategoryId not in ('.implode(',',$categorys).') and pcs.parent not in ('.implode(',',$categorys).') )',null,false);
			$p_list = $this->db->get()->result_array();
			$row['places'] = $p_list;
		}
		
		$this->assign(compact('placeCollections','placeCategory'));
		$this->display("sr_album2");
	}
}
?>