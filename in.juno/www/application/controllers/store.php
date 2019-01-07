<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 积分商城
 */

class Store extends Controller {
	
	function index($page = 1){
		$page = formatid($page,1);
		$pagesize = 21;
		
		$where = " status = 0 and ( endDate > '".date('Y-m-d H:i:s',TIMESTAMP)."' and startDate <='".date('Y-m-d H:i:s',TIMESTAMP)."' )";
		
		//top3 单独列出来
		
		$this->db->where($where,null,false);
		$top3 = $this->db->limit(3)->order_by('rankOrder','desc')->get($this->_tables['product'])->result_array();
		
		
		$this->db->where($where,null,false);
		$total = $this->db->count_all_results($this->_tables['product']);
		$total = $total-3;
		//echo $total;
		if($total>=1){
			$this->paginate_style2('/store/index',$total,$page,$pagesize);
			
		}
		$this->db->where($where,null,false);
		$list = $this->db->limit($pagesize,($page-1)*$pagesize+3)->order_by('rankOrder','desc')->get($this->_tables['product'])->result_array();
		
		$this->assign(compact('top3','list'));
		$this->display('index');
	}
	
	
}