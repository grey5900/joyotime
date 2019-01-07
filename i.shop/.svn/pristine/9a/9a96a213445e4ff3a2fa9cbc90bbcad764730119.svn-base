<?php
/**
 * 用来生成会员统计图表的模型
 * Create by 2012-11-6
 * @author liuweijava
 * @copyright Copyright(c) 2012- joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Stat_member extends CI_Model{

	var $charts;
	var $stat;
	var $brandId;
	
	function __construct(){
		parent::__construct();			
		$this->config->load('chart_config');
		$this->charts = $this->config->item('charts');
	}

	/**
	 * 会员数量统计
	 * Create by 2012-11-5
	 * @author liuweijava
	 * @param int $item_id,统计项ID
	 */
	function chart($brand_id){
		$this->brandId = $brand_id;
		$stat_id = $this->input->post('stat_id');
		$item_id = $this->input->post('item_id');
		$this->stat = $this->charts[$stat_id];
		$arr = array();
		$item = $this->stat['items'][$item_id];		
		//查询品牌
		$brand = $this->db->where('id', $this->auth['brand_id'])->get('Brand')->first_row('array');
		if(!empty($brand)){
			$arr['title'] = $this->stat['title'].' - '.$item['item'];
			$arr['y_title'] = $item['item'];
			$arr['type'] = $item['chart'];
			$b_createDate = gmdate('Y-m-d', strtotime($brand['createDate'])+8*3600);//品牌建立时间，作为统计的基准时间
			$this->db->select("COUNT(uid) AS members, DATE_FORMAT(createDate, '%Y-%m-%d') AS created");
			$this->db->where(array('brandId'=>$this->brandId, 'isBasic'=>1));
			$sql = "SELECT COUNT(uid) AS members, DATE_FORMAT(createDate , '%Y-%m-%d') AS created FROM UserOwnMemberCard WHERE brandId = ? AND isBasic = 1 GROUP BY created ORDER BY created ASC";
			$query = $this->db->query($sql, array($brand['id']))->result_array();
			$series = $list = array();
			switch($item_id){
				case 2://增长趋势
					$start = 0;
					$start_time = strtotime($b_createDate.' 00:00:00')*1000+3600000*8;
					$end_row = strtotime($query[count($query)-1]['created'])*1000+3600000*8;
					foreach($query as $row){
						$list[strtotime($row['created'].' 00:00:00')*1000+3600000*8] = intval($row['members']);
					}
					for($i=$start_time;$i<=$end_row;$i+=3600000*24){
						if(isset($list[$i])){
							$start += $list[$i];
						}
						$series[] = '['.implode(',',array($i, $start)).']';
					}
					break;
				default://日增长量
					//统计数据
					foreach($query as $row){
						$series[] = '['.implode(',',array(strtotime($row['created'].' 00:00:00')*1000+3600000*8, intval($row['members']))).']';
					}
					break;
			}
		}
		
		$arr['series'][$item['item']] = implode(',', $series);
		return $arr;
	}
	
}  
   
 // File end