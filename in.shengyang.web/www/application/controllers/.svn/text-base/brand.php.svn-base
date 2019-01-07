<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 积分商户
 */

class Brand extends Controller {
	
	function index($page = 1){
		
		$pagesize = 10;
		
		$where = " isRepayPoint = 1 and status = 0";
		$this->db->where($where,null,false);
		$total = $this->db->count_all_results($this->_tables['place']);
		if($total){
			$this->paginate_style2('/brand/index',$total,$page,$pagesize);
		}
		$this->db->where($where,null,false);
		$this->db->order_by('tipCount','desc');
		$this->db->limit($pagesize,($page-1)*$pagesize <0 ? 0 : ($page-1)*$pagesize);
		$list = $this->db->select('id')->get($this->_tables['place'])->result_array();
		foreach($list as &$row){
			$row = get_data('place',$row['id']);
		}
		$top_place = get_data('toplist',0);
		
		
		//美食频道
		$meishi_cate = $this->db->select('id,catName')->where('catName','美食')
								->where('parentId',0)
								->get($this->_tables['webnewscategory'])
								->row_array();
		$cate_list = $this->db->select('id')->where('parentId',$meishi_cate['id'])->get($this->_tables['webnewscategory'])->result_array();
		$cates = array();
		$cates [] = $meishi_cate['id'];
		foreach($cate_list as &$c_l){
			$cates [] = $c_l['id'];
		}
		
		$now = date("Y-m-d H:i:s");
		$event = $this->db->where_in('newsCatId',$cates)
						  ->where('status',0)
						  ->where_in('type',array(0,2))
						  ->where("( startDate < '{$now}' and endDate > '{$now}' )",null,false)
						  ->limit(1)
						  ->order_by('createDate','desc')
						  ->get($this->_tables['webevent'])
						  ->row_array();
		//var_dump($event);
		$this->assign(compact('total','list','top_place','event'));
		$this->display('index');
	}
	
	function delicious_places(){
		$d = get_data_ttl('toplist',0,3600*8);
		echo 'var status = '.count($d).';';
		exit;
	}
}