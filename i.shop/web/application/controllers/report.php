<?php
/**
 * 各种统计
 * Create by 2012-11-5
 * @author liuweijava
 * @copyright Copyright(c) 2012- joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Report extends MY_Controller{
	
	var $charts;
	var $stat;
	
	function __construct(){
		parent::__construct();
		$this->config->load('chart_config');
		$this->charts = $this->config->item('charts');
	}
	
	/**
	 * 
	 * Create by 2012-11-5
	 * @author liuweijava
	 */
	function index(){
		if($this->is_post()){
			$stat_id = $this->post('stat_id');//统计类型ID
			$item_id = $this->post('item_id');
			empty($stat_id) && $this->echo_json(array('code'=>2,'msg'=>'您还没有选择统计种类'));
			empty($item_id) && $this->echo_json(array('code'=>3,'msg'=>'您还没有选择统计项目'));
			$this->stat = $this->charts[$stat_id];
			$arr = array();//图表数据
			
			//载入MODEL
			$this->load->model($stat_id, 'chart');
			$arr = $this->chart->chart($this->auth['brand_id']);
			
			!empty($arr) && $code = 0;
			empty($arr) && $code = 1;
			$this->assign('arr', $arr);
			$html = $this->fetch('chart', 'web');
			$this->echo_json(compact('code', 'html'));
		}
	}
}   
   
 // File end