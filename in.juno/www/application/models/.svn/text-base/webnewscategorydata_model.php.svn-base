<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * WebNewsCategoryData表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Webnewscategorydata_Model extends MY_Model {
	
	var $ugc_need_check;
	function __construct(){
		parent::__construct();
		$this->categories = get_inc('newscategory');
		$this->ugc_need_check = $this->config->item('ugc_need_check');
	}
		
	function get_post_data($catid,$subpage=1,$page=1,$orderby="new",$pagesize=50,$subpagesize=10,$where=''){
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
		
		if($this->categories[$catid]['parentId']==0){
			$w_cat = 'w.channelId='.$catid;
		}
		else{
			$w_cat = 'w.catId='.$catid;
		}
		
		/*$sql = "select w.postId,w.boost,p.* from WebNewsCategoryData w left join 
				Post p on w.postId = p.id  where {$w_cat} and p.type in(2,3,4) and status<2  GROUP BY p.id"; //p.status=1*/
	
		$sql = "select a.*,b.*
from
( select w.postId , max( w.boost ) as boost , w.channelId , w.catId
    from WebNewsCategoryData w 
   inner join Post p on w.postId = p.id  ";
		$where && $sql .= " left join PostOwnTag pot on pot.postId = p.id";
   
		$sql .= " where {$w_cat} {$where}
     and p.type < 5 ";
		
		if($this->ugc_need_check ==1 ){
			$sql .= "   and status = 1 ";
		}
		else{
  			$sql .= "   and status < 2 ";
		}
		
 		$sql .= " group by w.postId "; 
		
		$data['total'] =  $this->count_post_data($catid);
		$offset = (($subpage + ($page-1)*($pagesize/$subpagesize)) - 1)*$subpagesize;//($page-1)*$pagesize+($subpage-1)*$subpagesize;
		
		switch($orderby){
			case "new":
				$sql .= " order by w.boost desc,p.createDate desc ";
				break;
			case "hot": 
				$P = " ((p.viewCount+1)*0.2+p.replyCount+p.praiseCount+p.shareCount*2) ";
				$G = 1.5;
				$T = " POW(ROUND((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(p.createDate))/3600,1) + 2,{$G}) ";
				$sql .= " order by $P/$T+w.boost desc ";
				break;
			default :
				$sql .= " order by p.createDate desc ";
				break;
		}
		$sql .= "limit {$offset},{$subpagesize} ) a
inner join Post b
   on a.postId = b.id ";
			
		
		$query = $this->db->query($sql);
		$data['data'] = $query->result_array();
		$places = array();
		$this->load->model("postreply_model","m_postreply");
		$this->load->model("user_model","m_user");
		
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		//再关联post的tag
		foreach($data['data'] as $k=>$row){
			$sql = "select t.id as id,t.content as tagName from Tag t left join 
					PostOwnTag pt on t.id = pt.tagId where pt.postId=".$row['id']." limit 5"; //, pt.tagId,pt.postId
			$tags = $this->db->query($sql)->result_array();
			$places[$k] = $row['placeId'];
			//format_html
			$data['data'][$k]['content'] = format_html($row['content']);
			
			if($uid) {
				$user_mnemonic = get_data('mnemonic', $uid . '-' . $row['uid'],true);
				$data['data'][$k]['nickname'] = $user_mnemonic['mnemonic'];
			}
			
			
			
			//$data['data'][$k]['placeinfo'] = get_data("place",$row['placeid']);
			$user = get_data("user",$row['uid']);
			empty($data['data'][$k]['nickname']) && ($data['data'][$k]['nickname'] = ($user['nickname']?$user['nickname']:$user['username']));
			if($row['photo'])
			{
				$data['data'][$k]['thumb'] = $row['photo'] ? image_url($row['photo'], 'user', 'hdp') : "";
				$data['data'][$k]['photoSize'] = get_data('imagesize', $row['photo'].'||hdp');//$this->image_wh($row['photoName'],'mdp');
			}
			$data['data'][$k]['head'] = image_url($user['avatar'], 'head', 'mdpl');
			$data['data'][$k]['tags'] = $tags;
			
			
			//update 2013/1/6
			//是否已经赞过
			$this->load->model("userpraise_model","m_userpraise");
			$is_praised = $this->m_userpraise->check_praise($this->auth['uid'],$row['id'],$row['type']);
			$data['data'][$k]['is_praised'] = $is_praised;
			
			//取post的回复 3条
			
			//$this->m_postreply->list_by_postid_limit_3_order_createdate_desc(); //and status=1
			$sql = "select * from Reply where itemId=".$row['id']." and itemType=19 and status=0 order by createDate desc limit 10";
			$data['data'][$k]['reply'] = $this->db->query($sql)->result_array();
			//$data['data'][$k]['reply']['head'] = image_url($data['data'][$k]['reply']['uid'], 'head', 'mdpl');
			//$data['data'][$k]['reply']['username'] = $this->m_user->info($data['data'][$k]['reply']['uid'])['username'];
			foreach($data['data'][$k]['reply'] as $key=>$value){
				$user = get_data("user",$value['uid']);
				$re_replay_string = "";
				if($value['replyUid'] && $value['replyId']){
					//回复别人的回复
					$re_user = get_data("user",$value['replyUid']);
					$nick = $re_user['nickname'] ? $re_user['nickname'] : $re_user['username'];
					$re_replay_string = "回复<a href='/user/".$value['replyUid']."' class='name'>".$nick."</a>";
				}
				$data['data'][$k]['reply'][$key]['content'] = $value['content']; 
				$data['data'][$k]['reply'][$key]['head'] = image_url($user['avatar'], 'head', 'mdpl');
				$data['data'][$k]['reply'][$key]['nickname'] = $user['nickname'] ? $user['nickname'] : $user['username'];
				$data['data'][$k]['reply'][$key]['re_string'] = $re_replay_string;
			}
		}
		
		$placeinfo_array = get_data("place",$places);
		
		$data['place'] = $placeinfo_array;
		
		return $data;
	}
	
	function count_post_data($catid,$where = ''){
		if($this->categories[$catid]['parentId']==0){
			$w_cat = 'w.channelId='.$catid;
		}
		else{
			$w_cat = 'w.catId='.$catid;
		}
		
		$count_sql = 'select COUNT(b.id) as c
from
( select w.postId , max( w.boost ) as boost 
    from WebNewsCategoryData w 
   inner join Post p on w.postId = p.id  ';
		$where && $count_sql .= ' left join PostOwnTag pot on pot.postId = p.id';
   
		$count_sql .= ' 
   where '.$w_cat.' '.$where.'
     and p.type < 5 ';
		
		if($this->ugc_need_check==1){
			$count_sql .= '  and status = 1 ';
		}else{
     		$count_sql .= '  and status < 2 ';
		}
   
     	$count_sql .= '
     	group by w.postId  order by w.boost desc,p.createDate desc ) a
inner join Post b
   on a.postId = b.id'; // status<2
		//$count_sql = 'select count(*) as c from WebNewsCategoryData w right join 
		//			  Post p on w.postId = p.id where '.$w_cat;
		//echo $count_sql;
		return $this->db->query($count_sql)->row_array(0)[c];
	}
}