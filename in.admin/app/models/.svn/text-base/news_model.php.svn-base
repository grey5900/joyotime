<?php
/**
 * Create by 2012-12-3
 * @author 
 * @copyright
 * @desc 新闻相关 
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class News_model extends MY_Model{
	
	var $c_table = "WebNewsCategory";
	var $news_table = "WebNews";
	
	function __construct(){
		parent::__construct();
		
		//$this->load->helper('news_helper');//公共函数库
	}
	
	function getChannelList($where="",$page=1,$limit=20,$order=""){
		//var_dump($this->table);exit;
		 if(!$where){
		 	$query = $this->db->order_by("parentId asc,orderValue desc")->limit($limit,($page-1)*$limit)->get($this->c_table);
		 	$total = $this->db->count_all_results($this->c_table);
		 }
		 else{
		 	//$q = $this->db->order_by("parentId asc,orderValue desc")->where($where,null,false)->get($this->table);
		 	$this->db->where($where,null,false);
		 	$total = $this->db->count_all_results($this->c_table);//$q->num_rows();
		 	$query = $this->db->order_by("parentId asc,orderValue desc")->where($where,null,false)->limit($limit,($page-1)*$limit)->get($this->c_table);
		 }
		 
		 return array("result"=>$query->result_array(),"total"=>$total);
	}
	
	function update($tablename="",$set = NULL, $where = NULL, $limit = NULL){
		$tablename = $tablename ? $tablename : $this->c_table;
		return $this->db->update($tablename,$set,$where,$limit);
	}
	
	function get_cateinfo_by_id($id){
		if(!empty($id))
		return $this->db->where(array('id'=>$id))->get($this->c_table)->row_array(0);
		else
		return array();
	}
	
	function getNewsList($where="",$page=1,$limit=20,$order=""){
		 
		 if(empty($where)){
		 	$query = $this->db->order_by("id asc")->limit($limit,($page-1)*$limit)->get($this->news_table);
		 	$total = $this->db->count_all_results($query);
		 }
		 else{
		 	$this->db->where($where);
		 	$total = $this->db->count_all_results($this->news_table);
		 	$query = $this->db->order_by("id asc")->where($where)->limit($limit,($page-1)*$limit)->get($this->news_table);
		 }
		 
		 return array("result"=>$query->result_array(),"total"=>$total);
		
	}
}   
   
 // File end