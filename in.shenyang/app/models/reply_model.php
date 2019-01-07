<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Reply表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Reply_Model extends MY_Model {
	
	public $post_type = array(
							 1 => '签到',
							 2 => '点评',
							 //3 => '照片',  不再有3这个类型了，判断是否有图片，用photo字段来判定
							 4 => 'YY', 
							 5 => '评价POST', 
							 6 => '购买商品', 
							 7 => '分享', 
							 8 => '成为会员', 
							 9 => '关注好友',
							 /*道具*/
							 10=> '接受赠送的道具：',
							 11=> '获得道具：',
							 12=> '购买道具：',
							 13=> '使用道具：' 
							 /*道具*/
						);
	
	function __construct(){
		parent::__construct();
		$this->taboo = get_data("taboo_post");
	}
	
	//ugcExtraInfo 回复 itemtype=100  可以（回复YY 4  点评2  分享7 ）：19 POST 表   地点册20 placeCollection
	function get_reply_list($where = '' , $pagesize = 20 , $offset = 0 , $order_by = 'createDate desc' , $having = ''){
		//回复的内容繁多。。暂时不知道怎么处理，还是先取出回去，再到关联的其他表去取标题/内容神马的。
		$sql = " SELECT r.*, IFNULL(m.read,0) as isread from Reply r left join 
		         MorrisUgcExtraInfo m on r.id=m.itemId and m.itemType = 100 where 1=1 ";
		$sql .= $where ? " and ".$where : "";
		$sql .= $having ? " having " .$having : "";
		$sql .= " order by ".$order_by;
		$sql .= " limit $offset,$pagesize ";
		
		$list = $this->db2->query($sql)->result_array();
		foreach($list as $key=>$row){
			$id = $row['itemId'];
			switch($row['itemType']){
				case 19:  // Post
					$post = $this->db2->where("id",$id)->get("Post")->row_array(0);
					$place = $this->db2->where("id",$post['placeId'])->get("Place")->row_array(0);
					// 2 4 7 这样表示 。。其他的。。。
					if(in_array($post['type'],array(2,4,7))){
						$list[$key]['orignal_title'] = empty($post['id']) ? 
					                               ' <font color=red>原"点评"内容已经消失 - ID:'.$id.'</font> '
					                               :
					                               (
						                               '@'.($post['type']!=7 ? ($place['placename']?$place['placename']." ":"- ") : "").
													   "[".$this->post_type[$post['type']]."]:".
						                               ($post['photo']?"<img src='".image_url($post['photo'],'user')."' width='100' />":"").
						                               cut_string($post['content'],100)
					                               );
					}
					else if(in_array($post['type'],array(10,11,12,13))){ //道具相关
						$itemid = $post['relatedItemId'] ;
						$item = $this->db2->where("id",$itemid)->get("Item")->row_array(0);
						$list[$key]['orignal_title'] = $this->post_type[$post['type']]."<".$item['name'].">";
					}
					else if(in_array($post['type'],array(9))){ //关注好友
						$r_uids = $post['extension'];
						$uid_array = array_filter(explode(",",$r_uids));
						$r_user = $this->db2->where_in("id",$uid_array)->get("User")->result_array();
						$usernames = "";
						foreach($r_user as $k=>$row){
							$usernames .= $row['username'] ? $row['username'] : $row['nickname'];
							if(!empty($r_user[$k+1])){
								$usernames .= ",";
							}
						}
						$list[$key]['orignal_title'] = "关注了用户：".$usernames;
					}
					else{
						$list[$key]['orignal_title'] = empty($post['id']) ?' <font color=red>原"点评"内容已经消失 - ID:'.$id.'</font> ' :"暂时未知的类型，Post 的  Type = ".$post['type'];
					}
					break;
				case 20:  // placeCollection
					$placeCollection = $this->db2->where("id",$id)->get("PlaceCollection")->row_array(0);
					$list[$key]['orignal_title'] = $placeCollection['name'] || $placeCollection['id']? '@地点册:《'.$placeCollection['name'].'》':'<font color=red>原"地点册"内容已经消失 - ID:'.$id.'</font>';
					break;
				default :
					$list[$key]['orignal_title'] = "<font color=blue>这条数据来自外太空，无视。</font>";
					break;
			}
			//用户信息
			$user = $this->db2->select('username,avatar')->where("id",$row['uid'])->get("User")->row_array(0);
			$list[$key]['username'] = $user['username'];
			$list[$key]['avatar'] = $user['avatar'];
			
			//如果是回复回复的话
			if(!empty($row['replyTo']) && !empty($row['replyId'])){
				$ruser = $this->db2->select('username,avatar')->where("id",$row['replyTo'])->get("User")->row_array(0);
				$list[$key]['content'] = "回复 ".$ruser['username']." : ".$row['content'];
			}
			
			$taboo_replacement = $this->taboo;
			array_walk($taboo_replacement,"highlight");
			
			//如果有屏蔽词，高亮屏蔽词
			if($row['isTaboo']){
				$list[$key]['content'] = str_replace($this->taboo,$taboo_replacement,$list[$key]['content']);
				
			}
		}
		return $list;
	}
	
	function count_reply($where = '',$having = ''){
		$sql = " SELECT count(r.id) as count, IFNULL(m.read,0) as isread from Reply r left join 
		         MorrisUgcExtraInfo m on r.id=m.itemId and m.itemType = 100 where 1=1";
		$sql .= $where ? " and ".$where : "";
		$sql .= $having ? " having " .$having : "";
		
		
		$result = $this->db2->query($sql)->row_array(0);
		return $result['count'];
	}
	
	function get_one_reply($id){
		$sql = "SELECT r.*,p.content as pcontent,p.photo,p.uid as puid,p.type,u.nickname,u.username
				From User u right join Reply r on r.uid=u.id left join Post p on p.id = r.itemId and r.itemType=19
				Where r.id=".$id;
		//return $this->db2->where("id",$id)->get("Reply")->row_array(0);
		return $this->db2->query($sql)->row_array(0);
	}
}