<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 房产管理
 */

include APPPATH . 'controllers/house.php';

class House_I extends House {
	function index() {
		echo 'House_I index';
	}
	
	function groupon_list(){
		$this->orders(0);
	}
	
	function discount_list(){
		$this->orders(1);
	}
	
	function commision_list(){
		//我猜是user表
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	
    	$keywords = $this->input->post("keywords") ;
    	
    	$orderField = $this->post('orderField');
        $orderField = isset($orderField)&&!empty($orderField)?$orderField:'dateline';
        $orderDirection = $this->post('orderDirection');
        $orderDirection = isset($orderDirection)&&!empty($orderDirection)?$orderDirection:'desc';
    	
    	$where = " 1=1 ";
    	$keywords && $where .= " and cellphone_no like '%{$keywords}%'";
    	$this->tfdb->where($where,null,false);
    	$total = $this->tfdb->count_all_results('user');
    	
    	$paginate = $this->paginate($total,$pageNum,$numPerPage);
    	$users = $this->tfdb->select('* , withdraw+commision as total_commision',false)->where($where ,null ,false)
    						->order_by("{$orderField} {$orderDirection}")
    						->limit($paginate['per_page_num'],$paginate['offset'])   						
    						->get('user')
    						->result_array();
    	
    	$this->assign(compact('users','keywords','orderField','orderDirection'));
    	$this->display('commision');
	}
	
	function withdraw(){
		$id = $this->get('id');
		$user = $this->tfdb->where('id',$id)->get('user')->row_array();
		if($this->is_post()){
			$amount = $this->post('amount');
			
			if($amount > $user['commision'] || $amount <= 0){
				$this->error('提现金额必须大于0并且不能超过余额');
			}
			
			
			$data = array(
				'order_id' => 0,
				'handler' => $this->auth['id'] ,
				'money' => 0 - $amount ,
				'name' => $user['name'] ,
				'cellphone_no' => $user['cellphone_no'] ,
				'is_new' => 1
			);
			
			$b = $this->tfdb->insert('commision',$data);
			$b && $b = $this->tfdb
							->where('id',$id)
							->set('commision','commision - '.$amount,false)
							->set('withdraw','withdraw +'.$amount ,false)
							->update('user',$set);
			
			if($b){
				$this->success('提现成功！','','','closeCurrent');
			}
			else{
				$this->error('扯拐了，等一哈再试。');
			}
		}
		
		$this->assign(compact('user','id'));
		$this->display('withdraw');
	}
	
	function commision(){
		//commision
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	
    	$keywords = $this->input->post("keywords") ;
    	$cell = $this->input->post('cellphone');
    	
    	$startDate = $this->input->post('startDate');
    	$endDate = $this->input->post('endDate');
    	$where = " 1 = 1 ";
    	
		if($keywords)
		{
			$where .= " and id = '{$keywords}'";
		}
		
		$cell && $where .= " and cellphone_no like '%{$cell}%'";
		$startDate && $where .= " and dateline >= '{$startDate}'";
		$endDate && $where .= " and dateline <= '{$endDate}'";
		
		$this->tfdb->where($where ,null ,false);
		$total = $this->tfdb->count_all_results('commision');
		$paginate = $this->paginate($total,$pageNum,$numPerPage);
		$list = $this->tfdb->where($where ,null ,false)->limit($paginate['per_page_num'],$paginate['offset'])
			->order_by('dateline', 'desc')->get('commision')->result_array();
		
		//sum 佣金 sum 提现
		$total_commision = $this->tfdb->select('sum(money) as total')
									  ->where($where,null,false)
									  ->where(' money > 0',null,false)
									  ->get('commision')
									  ->row_array();
		$total_withdraw = $this->tfdb->select('sum(money) as total')
									  ->where($where,null,false)
									  ->where(' money < 0',null,false)
									  ->get('commision')
									  ->row_array();
		
		$this->assign(compact('list','total','total_commision','total_withdraw','keywords','cell','startDate','endDate'));
		$this->display('commision_detail');
	}
	
	function orders($type){
		
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	
    	$s_type = $this->input->post("type") ;
    	$keywords = $this->input->post("keywords") ;
    	$cell = $this->input->post('cellphone');
    	
    	$startDate = $this->input->post('startDate');
    	$endDate = $this->input->post('endDate');
    	
    	$where = "type = {$type}";
    	
		if($keywords)
		{
			$where .= " and {$s_type} like '%{$keywords}%'";
		}
		$cell && $where .= " and cellphone_no like '%{$cell}%'";
		$startDate && $where .= " and dateline >= '{$startDate}'";
		$endDate && $where .= " and dateline <= '{$endDate}'";
		$this->tfdb->where($where ,null ,false);
		$total = $this->tfdb->count_all_results('order');
		$paginate = $this->paginate($total,$pageNum,$numPerPage);
		
		$orders = $this->tfdb->where($where ,null ,false)->limit($paginate['per_page_num'],$paginate['offset'])->order_by('dateline','desc')->get('order')->result_array();
		$this->assign(compact('orders','type','s_type','keywords','cell','startDate','endDate'));
		$this->display('orders');
	}
}