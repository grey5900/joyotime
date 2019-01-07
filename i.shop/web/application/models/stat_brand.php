<?php
/**
 * 用来生成品牌统计图表的模型
 * Create by 2012-11-6
 * @author liuweijava
 * @copyright Copyright(c) 2012- joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Stat_brand extends CI_Model{
	
	var $charts;
	var $stat;
	var $brandId;
	
	function __construct(){
		parent::__construct();		
		$this->config->load('chart_config');
		$this->charts = $this->config->item('charts');
	}	

	/**
	 * 品牌统计接口
	 * Create by 2012-11-5
	 * @author liuweijava
	 * @param int $item_id,统计项ID
	 */
	function chart($brand_id){
		$stat_id = $this->input->post('stat_id');
		$item_id = $this->input->post('item_id');
		$this->brandId = $brand_id;
		
		$this->stat = $this->charts[$stat_id];
		$item = $this->stat['items'][$item_id];
		$arr = array();
		//处理生成图表的逻辑
		
		//END
		return $arr;
	}
	
	/**
	 * 统计指定店铺的日PV数据(线图)
	 * Create by 2012-11-6
	 * @author liuweijava
	 * @param int $placeId
	 */
	function _stat_by_place($placeId){
		
	}
	
	/**
	 * 统计品牌下所有店铺的日PV比例(饼图)
	 * Create by 2012-11-6
	 * @author liuweijava
	 */
	function _stat_by_brand(){
		
	}
}   
   
 // File end