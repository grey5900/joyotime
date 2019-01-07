<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * WebNews表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Webnews_Model extends MY_Model {
	function count_news($catid){
		return $this->count_by_newscatid($catid);
	}
	
	function news_list($catid,$page=1,$pagesize=20){
		$sql = "select subject,id,newsCatid,linkuri,summary,thumb,dateline from WebNews where newsCatId=".$catid." and status=1 order by dateline desc limit ".($page-1)*$pagesize.",".$pagesize;
		return $this->db->query($sql)->result_array();
	}
	
	function prev_news($catid,$newsid){
		$sql = "select subject,id,newsCatid,linkuri,dateline from WebNews where id<{$newsid} and status in (1) and newsCatId=".$catid." order by id desc limit 1";
		return $this->db->query($sql)->row_array(0);
	}
	
	function next_news($catid,$newsid){
		$sql = "select subject,id,newsCatid,linkuri,dateline from WebNews where id>{$newsid} and status in (1) and newsCatId=".$catid." order by id asc limit 1";
		return $this->db->query($sql)->row_array(0);
	}
	
	function hot_news($catid){
		//本周 7 天
		$start_date = TIMESTAMP - 31*3600*24;
		$sql = "select subject,id,newsCatid,linkuri,dateline from WebNews where newsCatId=".$catid." and ( dateline>{$start_date} and dateline<".time()." ) and status = 1 order by hitCount desc,dateline desc limit 10";
		return $this->db->query($sql)->result_array();
	}
	
	function related_news($catid,$newsid){
		$sql = "select subject,id,newsCatid,linkuri,thumb,dateline from WebNews where newsCatId=".$catid." and status=1 and id!=".$newsid." and thumb!='' limit 6";
		return $this->db->query($sql)->result_array();
	}
	
	function location_info($newsid){
		$this->load->model("webnewsplace_model","m_webnewsplace");
		$this->load->model("place_model","m_place");
		$placeid = $this->m_webnewsplace->select_by_newsid($newsid)['placeId'];
		
		if($placeid){
		return get_data("place",$placeid);//$this->m_place->select_by_id($placeid);
		}
		else{
		return false;
		}
	}
	
	function grouponinfo($newsid){
		$this->load->model("grouponitematplace_model","m_grouponitematplace");
		$this->load->model("grouponsourceitem_model","m_grouponsourceitem");
		$this->load->model("grouponitem_model","m_grouponitem");
		$this->load->model("webnewsplace_model","m_webnewsplace");
		
		$sql = "select DISTINCT g.* from WebNewsPlace n inner join 
				GrouponItemAtPlace gi on n.placeId=gi.placeId right join 
				GrouponItem g on gi.grouponId = g.id where n.newsId=".$newsid." and g.status=0
				order by g.endDate desc";
		
		$grouponinfo = $this->db->query($sql)->row_array(0);
		if($grouponinfo){
		//购买地址
		$buy_link = "";
		
			switch($grouponinfo['sourceName']){
				case "商报买购网":
					$buy_link = "http://mygo.chengdu.cn/index.php?controller=site&action=groupon_detail&id=".$grouponinfo['originalId'];
					break;
				case "章鱼团":
					$buy_link = "http://tg.chengdu.cn/team.php?id=".$grouponinfo['originalId'];
					break;
			}
		
		$grouponinfo['buy_link'] = $buy_link;
		}
		return $grouponinfo;
	}
}