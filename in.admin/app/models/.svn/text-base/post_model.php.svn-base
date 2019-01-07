<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Post表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Post_Model extends MY_Model {
	// 1:签到,2:点评,3:照片,4:YY,5:评价POST,6购买商品,7:分享,8:成为会员
    //        9:关注好友
	public $post_type = array(
							 1 => '签到',
							 2 => '点评',
							 //3 => '照片',  不再有3这个类型了，判断是否有图片，用photo字段来判定
							 4 => 'YY', 
							 5 => '评价POST', 
							 6 => '购买商品', 
							 7 => '分享', 
							 8 => '成为会员', 
							 9 => '关注好友' 
						);
						
	
	function __construct(){
		parent::__construct();
		$this->taboo = get_data("taboo_post");
		
		$this->load->model("placecollection_model","m_placecollection");
		
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
	
	/**
	 * 
	 * @param string $where
	 * @param number $pagesize
	 * @param number $offset
	 * @param string $order_by
	 * @param string $having
	 * @param string $join_read
	 * @param string $join_rec
	 * @param string $join_topic    It would left join TopicOwnPost table if sets true.
	 * @return unknown
	 */
	function get_post_list($where = '' , $pagesize = 20 , $offset = 0 , $order_by = 'createDate desc' , $having = '' , $join_read = false , $join_rec = false, $join_topic = false){
		
		//左连接 MorrisUgcExtraInfo 获得管理员管理状态
		$sql = "SELECT p.* ";
		$join_read && $sql .= ", IFNULL(m.read,0) as isread ";
		$join_rec && $sql .= ",IFNULL(r.expireDate,0) as isrecommend ";
		$join_topic && $sql .= ",top.topicId as topicId ";
		$sql .=" from Post p ";
		$join_read && $sql .= " Left join MorrisUgcExtraInfo m on p.id = m.itemId and m.itemType = 19 ";
		$join_rec  && $sql .= "	Left join  HomePageData r on r.itemId = p.id and r.itemType = 19 "; //post的内容的itemType是19
		$join_topic  && $sql .= " Left join  TopicOwnPost top on top.postId = p.id "; //
		$sql .= $where ? " where ".$where : "";
		$sql .= $having ? " having " .$having : "";
		$sql .= " order by ".$order_by;
		$sql .= " limit $offset,$pagesize ";
		//echo $sql;exit;
		$list = $this->db2->query($sql)->result_array();
		
		//查询出标签和其他需要的东西
		foreach($list as &$row){
			$user = $this->db2->select("username,nickname,avatar")->where("id",$row['uid'])->get("User")->row_array(0);
			$row['username'] = $user['nickname']?$user['nickname']:$user['username'];
			$row['avatar'] = $user['avatar'];
			
			$place  = $this->db2->select("placename")->where("id",$row['placeId'])->get("Place")->row_array(0);
			$row['placename'] = $place['placename'];
			
			$row['typename'] = $this->post_type[$row['type']];
			
			$taboo_replacement = $this->taboo;
			array_walk($taboo_replacement,"highlight");
			
			//如果有屏蔽词，高亮屏蔽词
			if($row['isTaboo']){
				$row['content'] = str_replace($this->taboo,$taboo_replacement,$row['content']);
			}
			
			//<a target="navTab" href="/ugcv3/post/index/topic_id/57/from/topic">POST(1)</a>
			
			if($row['topicId']){
				$topic = $this->db->where('id',$row['topicId'])->get("Topic")->row_array();
				//$row['content'] = str_replace($topic['subject'],'<a target="navTab" href="/ugcv3/post/index/topic_id/'.$row['topicId'].'/from/topic">'.$topic['subject'].'</a>',$row['content']);
				//(\\#|＃)([^\\#＃]+)(\\#|＃)
				$pattern = "/(\\#|＃)(".$topic['subject'].")(\\#|＃)/";
				$row['content'] = @preg_replace($pattern,'<a target="navTab" href="/ugcv3/post/index/topic_id/'.$row['topicId'].'/from/topic">$1$2$3</a>',$row['content']);
			}
			else{ //没有topicId 然后匹配一下，看是否有对应话题
				/*$pattern = "/(\\#|＃)([^\\#＃]+)(\\#|＃)/";
				preg_match_all($pattern,$row['content'],$matches);
				*/
				if((strpos($row['content'],"#")>=0 || strpos($row['content'],"＃")>=0) && $row['status']<2/*!empty($matches)*/){
					
					//查找话题
					$this->db->select("t.subject,t.id");
					$this->db->from("Topic t");
					$this->db->join("TopicOwnPost top","top.topicId=t.id","right");
					$this->db->where("top.postId",$row['id']);
					$topics = $this->db->get()->result_array();
					
					if($topics){
						foreach($topics as $topic){
							$pattern = "/(\\#|＃)(".$topic['subject'].")(\\#|＃)/";
							$row['content'] = @preg_replace($pattern,'<a target="navTab" href="/ugcv3/post/index/topic_id/'.$topic['id'].'/from/topic">$1$2$3</a>',$row['content']);
							
							//$row['content'] = str_replace($topic['subject'],'<a target="navTab" href="/ugcv3/post/index/topic_id/'.$row['topicId'].'/from/topic">'.$topic['subject'].'</a>',$row['content']);
						}
					}
				}
			}
			
			
			
			/*根据relatedItemType 获取原数据*/
			/*switch($row['relatedItemType']){
				case 19:
					$relate = $this->get_one_post($row['relatedItemId']);
					$relate['typename'] = $this->post_type[$relate['type']];
					$r_user = $this->db2->select("username,avatar")->where("id",$relate['uid'])->get($this->_tables['user'])->row_array(0);
					$relate['username'] = $r_user['username'];
					$relate['avatar'] = $r_user['avatar'];
					break;
				case 20:
					$relate = $this->db->where("id",$row['relatedItemId'])->get($this->_tables['placecollection'])->row_array();
					$relate['typename'] = "地点册";
					$relate['content'] = $relate['name'];
					$r_user = $this->db2->select("username,avatar")->where("id",$relate['uid'])->get($this->_tables['user'])->row_array(0);
					$relate['username'] = $r_user['username'];
					$relate['avatar'] = $r_user['avatar'];
					break;
			}
			
			$row['relateItem'] = $relate;
			unset($relate);*/
			
			$tag = $this->get_post_tags($row['id']);
			$row['tags'] = implode(",",$tag);
			
		}
		
		return $list;
	}
	
	/**
	 * 
	 * @param string $where
	 * @param string $having
	 * @param string $join_read
	 * @param string $join_rec
	 * @param string $join_topic    It would left join TopicOwnPost table if sets true.
	 * @return number
	 */
	function count_post($where = '',$having = '',$join_read = false , $join_rec = false, $join_topic = false){
		
		$sql = " SELECT COUNT(p.id) as count ";
		$join_read && $sql .= ",IFNULL(m.read,0) as isread ";
		$join_rec && $sql .= ",IFNULL(r.itemId,0) as isrecommend ";
		$sql .= " from Post p ";
		$join_read && $sql .= " Left join MorrisUgcExtraInfo m on p.id = m.itemId and m.itemType = 19 ";
		$join_rec  && $sql .= "	Left join  HomePageData r on r.itemId = p.id and r.itemType = 19 "; //post的内容的itemType是19
		$join_topic  && $sql .= " Left join  TopicOwnPost top on top.postId = p.id "; //
		
		$sql .= $where ? " where ".$where : "";
		$sql .= $having ? " having " .$having : "";
		//echo $sql;exit;
		$result = $this->db2->query($sql)->row_array(0);
		return $result['count'];
	}
	
	function get_one_post($id){
		$this->db2->select('Post.*, Place.placename, User.username, User.nickname, User.avatar');
		$this->db2->join('Place', 'Place.id = Post.placeId', 'left');
    	$this->db2->join('User', 'User.id = Post.uid','inner');
    	
    	$post = $this->db2->where('Post.id', $id)->limit(1)->get('Post')->first_row('array');
    	return $post;
	}
	
	function get_post_tags($postid){
		$sql = "SELECT t.content from Tag t left join PostOwnTag pt on pt.tagId = t.id 
					where pt.postId = ".$postid;
			
		$tag_list = $this->db2->query($sql)->result_array();
		$tag = array();
			
		foreach($tag_list as $k => $row){
			$tag[$k] = $row['content'];
		}
		//$row['tags'] = implode(",",$tag);
		return $tag;
	}
	
	function do_read($itemId  , $itemType  ){
		$have_read = $this->db2->where("itemId = ".$itemId ." and itemType = ".$itemType)
							   ->get("MorrisUgcExtraInfo")->row_array(0);
		$set = array(
				'itemId' => $itemId,
				'itemType' => $itemType
				
		);
        if(empty($have_read)){
        	//同时修改审核状态
        	
			$ib = $this->db2->insert("MorrisUgcExtraInfo",$set);
		}
		else{
			//if($have_read['read'] != 1){
				$ib = $this->db2->where($set)->update("MorrisUgcExtraInfo",
										array("read"=>1,"createDate"=> date("Y-m-d H:i:s"))
				);
			/*}
			else{
				$ib = true;
			}*/
		}
		if($ib){
			$st_set = array('status'=>1);
			switch($itemType){
				case 19:
					$this->db2->where('status',0)->where('id',$itemId)->update($this->_tables['post'],$st_set);
					break;
				/*case 100: 坑爹！回复没有审核状态了。
					$this->db2->where('status',0)->where('id',$itemId)->update($this->_tables['reply'],$st_set);
					break;*/
        	}
		}
		return $ib;
	}
	
	function _get_cat_tags(){
		$this->db2->select('WebNewsCategoryOwnTag.channelId, Tag.content, WebNewsCategory.catName');
		$this->db2->join('Tag', 'Tag.id = WebNewsCategoryOwnTag.tagId', 'inner');
		$this->db2->join('WebNewsCategory', 'WebNewsCategory.id = WebNewsCategoryOwnTag.channelId', 'inner');
		//$this->db2->join('WebNewsCategory', 'WebNewsCategory.id = WebNewsCategoryOwnTag.catId', 'inner');
		$list = array();
		if($this->auth['role'][0]==1){
		$query = $this->db2->where(array('WebNewsCategoryOwnTag.tagType'=>0))->order_by('WebNewsCategory.orderValue', 'desc')->order_by('Tag.id', 'desc')->get('WebNewsCategoryOwnTag')->result_array();
		}else{
		$query = $this->db2->where('WebNewsCategoryOwnTag.tagType=0 and WebNewsCategory.id in ('.implode(",",$this->newsRight).')',null,false)->order_by('WebNewsCategory.orderValue', 'desc')->order_by('Tag.id', 'desc')->get('WebNewsCategoryOwnTag')->result_array();
		}
		foreach($query as &$row){
			$list[$row['channelId']]['channel'] = $row['catName'];
			$list[$row['channelId']]['tags'][] = $row;
			unset($row);
		}
		unset($query);
		return $list;
	}
	
	function get_one_post_or_replay($table,$id){
		return $this->db2->where("id",$id)->get($table)->row_array(0);
	}
	
	function ban_a_thing($table,$id){
		$info = $this->get_one_post_or_replay($table,$id);
		
		$b = $this->db2->where(array('id' => $id))->update($table, array('status' => 3));
	}
	
	
	/* copy from post_back_model.php - old Post things !!*/
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
		
		
		
		$P = " ((p.viewCount+1)*0.2+p.replyCount+p.praiseCount+p.shareCount*2) ";
		$G = 1.5;
		$T = " POW(ROUND((UNIX_TIMESTAMP() - UNIX_TIMESTAMP(p.createDate))/3600,1),{$G}) ";
		
		$score = "($P/$T+w.boost) as score";
		
		/*$sql = "select w.postId,w.channelId,w.catId,w.boost,p.*,".$score." from WebNewsCategoryData w left join 
				Post p on w.postId = p.id  where ".$catidwhere."  "; // GROUP BY w.postId  p.status=1 and p.type in(2,3)*/
		
		$data['total'] =  $this->count_post_data($catid);
		
		$offset = ($page-1)*$pagesize;
		
		$sql = "select b.*,a.*
from
( select w.postId , max( w.boost ) as boost ,".$score." ,w.channelId,w.catId
    from WebNewsCategoryData w 
   inner join Post p on w.postId = p.id  
   where {$catidwhere} 
     and p.type in(2,3,4) 
     and status<2
   group by w.postId ";
		if($orderby && !in_array($orderby,array('new','hot'))){
			$sql .= $orderby;
		}
		switch($orderby){
			case "new":
				$sql .= " order by p.createDate desc ";
				break;
			case "hot":
				
				$sql .= " order by score desc";
				break;
			/*default :
				$sql .= " order by p.createDate desc ";
				break;*/
		}
		if(!$orderby){
			$sql .= " order by p.createDate desc ";
		}
	$sql .= "limit {$offset},{$pagesize} ) a
inner join Post b
   on a.postId = b.id ";	
		
		
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
			$catidwhere = ' (w.catId = '.$catid.' or w.channelId ='.$catid.')';
		}
		if($this->auth['role'][0]!=1 && !$catid){ //不是超级管理员，按权限读出数据
			$catidwhere.= " and (w.catId in (".implode(",",$this->newsRight).") or w.channelId in (".implode(",",$this->newsRight).") ) ";
		}
		
		$count_sql = 'select COUNT(b.id) as c
from
( select w.postId , max( w.boost ) as boost 
    from WebNewsCategoryData w 
   inner join Post p on w.postId = p.id  
   where '.$catidwhere.'
     and p.type in(2,3,4) 
     and status<2
   group by w.postId   ) a
inner join Post b
   on a.postId = b.id';
		/*$count_sql = 'select count(0) as c from WebNewsCategoryData w left join 
					  Post p on w.postId = p.id where '.$catidwhere;*/
		$arr = $this->db->query($count_sql)->row_array(0);
		return $arr[c];
	}
	/* copy from post_back_model.php  - old Post things  */
}