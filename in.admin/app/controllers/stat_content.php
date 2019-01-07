<?php
/**
 * 内容统计
 * Create by 2012-9-12
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Stat_content extends MY_Controller{
	
	function index(){
		$begin = $this->post('begin');
		$begin = isset($begin)&&!empty($begin)?$begin:'';
		$end = $this->post('end');
		$end = isset($end)&&!empty($end)?$end:'';
		$page_rel = 'data-list';
		$this->assign(compact('begin', 'end', $page_rel));
		
		$list = $sum = array();
		//数据长度
		if(!empty($begin)&&!empty($end)){
			$where = '';
			$where = "createDate BETWEEN '{$begin}' AND '{$end}'";
			$this->db->where($where);
		}elseif(!empty($begin))
			$this->db->where('createDate >= ', $begin);
		elseif(!empty($end) && empty($begin))
			$this->error('请选择开始时间', $this->_index_rel, $this->_index_uri, 'forward');
		$count = $this->db->count_all_results('StatContentCount');
		if($count){
			//分页
			$parr = $this->paginate($count);
			//数据
			if(!empty($begin)&&!empty($end)){
				$where = '';
				$where = "createDate BETWEEN '{$begin}' AND '{$end}'";
				$this->db->where($where);
			}elseif(!empty($begin))
				$this->db->where('createDate >= ', $begin);
			$query = $this->db->order_by('createDate', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get('StatContentCount')->result_array();
			$list = $query;
			//统计总数
			if(!empty($begin)&&!empty($end)){
				$where = '';
				$where = "createDate BETWEEN '{$begin}' AND '{$end}'";
				$this->db->where($where);
			}elseif(!empty($begin))
				$this->db->where('createDate >= ', $begin);
			$sum = $this->db->select('sum(checkinCount) as sum_checkin, sum(tipCount) as sum_tip, sum(photoCount) as sum_photo, sum(replyCount) as sum_reply, sum(UPMCount) as sum_upm, sum(shareCount) as sum_share')->get('StatContentCount')->first_row('array');
		}
		//统计时间段
		$stat_time = '';
		if(!empty($begin)&&!empty($end))
			$stat_time = $begin.'~'.$end;
		elseif(!empty($begin))
			$stat_time = $begin.'~昨天';
		else 
			$stat_time = '全部数据';
			
		$this->assign(compact('list', 'sum', 'stat_time'));
		$this->display('stat_content', 'stat');
	}
	
	/**
	 * 分析图表
	 * Create by 2012-9-13
	 * @author liuw
	 */
	function chart(){
		$data = $y_title = $series = array();
		if($this->is_post()){
			$items = $this->post('items');
			if(empty($items))
				$this->error('请选择至少一项要分析的数据');
			$begin = $this->post('begin');
			$begin = isset($begin)&&!empty($begin)?$begin:'';
			$end = $this->post('end');
			$end = isset($end)&&!empty($end)?$end:'';
			if(!empty($end)&&empty($begin))
				$this->error('请选择查询数据的开始时间或清除结束时间');
			$this->assign(compact('begin', 'end', 'items'));
			//格式化查询条件
			$select = array();
			foreach($items as $item){
				$select[] = $item.'Count';
				switch($item){
					case 'checkin':$y_title[] = '签到数';break;
					case 'tip':$y_title[] = '点评数';break;
					case 'photo':$y_title[] = '发图数';break;
					case 'reply':$y_title[] = '回复数';break;
					case 'UPM':$y_title[] = '私信数';break;
					case 'share':$y_title[] = '分享次数';break;
					default:$y_title[] = '-';break;
				}
			}
			//查询数据
			$select[] = 'createDate';
			$this->db->select(implode(',', $select));
			if(!empty($begin)&&!empty($end)){
				$this->db->where("createDate between '{$begin}' and '{$end}'");
				$data['subtitle'] = '分析时间段：'.$begin.' ~ '.$end;
			}elseif(!empty($begin)){
				$this->db->where('createDate >= ', $begin);
				$data['subtitle'] = '分析时间段：'.$begin.' ~ 昨天';
			}else 
				$data['subtitle'] = '分析时间段：全部数据';
			$query = $this->db->get('StatContentCount')->result_array();
			if(!empty($query)){
				foreach($query as $row){
					$keys = array_keys($row);
					foreach($keys as $key){
						if($key !== 'createDate'){
							$series[$key][] = '['.implode(',',array(strtotime($row['createDate'])*1000+3600000*8,intval($row[$key]))).']';
						}
					}
				}
				//封装坐标点
				foreach($series as $x_point=>$items){
					switch($x_point){
						case 'checkinCount':$data['series']['签到数'] = implode(',',$items);break;
						case 'tipCount':$data['series']['点评数'] = implode(',',$items);break;
						case 'photoCount':$data['series']['图片数'] = implode(',',$items);break;
						case 'replyCount':$data['series']['回复数'] = implode(',',$items);break;
						case 'UPMCount':$data['series']['私信数'] = implode(',',$items);break;
						case 'shareCount':$data['series']['分享次数'] = implode(',',$items);break;
					}
				}
				//yAxis
				$data['y_axis'] = implode(' & ', $y_title);
			}
		}
		$this->assign('data', $data);
		$this->display('stat_content_chart', 'stat');
	}
	
}
   
 // File end