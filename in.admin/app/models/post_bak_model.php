<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Post_bak表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Post_bak_Model extends MY_Model {
/*载入管理员权限，只能管理相关频道的post数据*/
	function __construct(){
		parent::__construct();
		$role_keys = $this->auth['role'];
		$roles = get_data("role");
		$newsRights = array();
		foreach($role_keys as $k=>$row){
			$newsRights = array_merge($newsRights,$roles[$row]['newsRights']);
		}
		$this->newsRight = array_unique(array_filter($newsRights));
		empty($this->newsRight) && ($this->newsRight = array(0));
		unset($newRights);
	}
	
	
	function get_post_data($catid,$page=1,$orderby="new",$pagesize=20,$keywords=''){
		/** 
		 * WebNewsCategoryData w
		 * Post p
		 * Place pl
		 * PostOwnTag pt
		 * Tag t
		 * 
		 */ 
		if($subpage>5){
			return false;
		}
		$data = array();
		
		if(intval($catid)==0){
			$catidwhere = " 1=1 ";
		}else{
			$catidwhere = " (w.catId = ".$catid." or w.channelId=".$catid.") ";
		}
		
		if($keywords){
			$catidwhere.= " and p.content like '%".$keywords."%'";
		}
		
		if($this->auth['role'][0]!=1 && !$catid){ //不是超级管理员，按权限读出数据
			$catidwhere.= " and (w.catId in (".implode(",",$this->newsRight).") or w.channelId in (".implode(",",$this->newsRight).") ) ";
		}
		
		
		
		$P = " ((p.hitCount+1)*0.2+p.replyCount+p.praiseCount+p.shareCount*2) ";
		$G = 1.5;
		$T = " POW(ROUND((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(p.createDate))/3600,1),{$G}) ";
		
		$score = "($P/$T+w.boost) as score";
		
		$sql = "select w.postId,w.channelId,w.catId,w.boost,p.*,".$score." from WebNewsCategoryData w left join 
				Post p on w.postId = p.id  where ".$catidwhere."  "; // GROUP BY w.postId  p.status=1 and p.type in(2,3)
		
		switch($orderby){
			case "new":
				$sql .= " order by p.createDate desc ";
				break;
			case "hot":
				
				$sql .= " order by score desc";
				break;
			default :
				$sql .= " order by p.createDate desc ";
				break;
		}
		
		
		
		$data['total'] =  $this->count_post_data($catid);
		
		//  ($page-1)*$pagesize+($subpage-1)*$subpagesize , $subpagesize
		$offset = ($page-1)*$pagesize;
		
		$sql .= ' limit '.$offset.','.$pagesize;
		
		$query = $this->db->query($sql);
		$data['data'] = $query->result_array();
		$places = array();
		$uids = array();
		//再关联post的tag
		foreach($data['data'] as $k=>$row){
			if(!$row['id']) continue;
			$sql = "select t.id as id,t.content as tagName from Tag t left join 
					PostOwnTag pt on t.id = pt.tagId where pt.postId=".$row['id']; //, pt.tagId,pt.postId
			$tags = $this->db->query($sql)->result_array();
			$places[$k] = $row['placeId'];
			$uids[$k] = $row['uid'];
					
			//$data['data'][$k]['thumb'] = $row['photoName'] ? image_url($row['photoName'], 'user', 'mdp') : "";
			//$data['data'][$k]['head'] = image_url($user['avatar'], 'head', 'mdpl');
			$tag_srting = "";
			foreach($tags as $row){
				$tag_srting .= $row['tagName'].",";
			}
			$data['data'][$k]['tags'] = $tag_srting;
		}
		$placeinfo_array = array();
		$users_array = array();
		$places && $placeinfo_array = $this->db->where_in("id",$places)->get("Place")->result_array();//get_data("place",$places);
		$uids && $users_array = $this->db->where_in("id",$uids)->get("User")->result_array();
		
		$placeInfoById = array();
		$userInfoById = array();
		foreach($placeinfo_array as $row){
			$placeInfoById[$row['id']] = $row;
		}
		foreach($users_array as $row){
			$userInfoById[$row['id']] = $row;
		}
		
		$data['place'] = $placeInfoById;
		$data['user'] = $userInfoById;
		
		return $data;
	}
	
	function count_post_data($catid){
		if(intval($catid)==0){
			$catidwhere = ' 1=1 ';
		}else{
			$catidwhere = ' w.catId = '.$catid;
		}
		if($this->auth['role'][0]!=1 && !$catid){ //不是超级管理员，按权限读出数据
			$catidwhere.= " and (w.catId in (".implode(",",$this->newsRight).") or w.channelId in (".implode(",",$this->newsRight).") ) ";
		}
		$count_sql = 'select count(0) as c from WebNewsCategoryData w left join 
					  Post p on w.postId = p.id where '.$catidwhere;
		$arr = $this->db->query($count_sql)->row_array(0);
		return $arr[c];
	}
}