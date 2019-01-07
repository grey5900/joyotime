<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 频道POST管理
 */

class Post extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->model("post_model","m_post");
		$this->load->helper("news");
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
	function index(){
		
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords = $this->input->post("keywords");
    	$orderby = $this->input->post("orderby") ? $this->input->post("orderby") : "new";
    	$catid = $this->input->post("catId") || $this->input->post("catId")==='0' ?  $this->input->post("catId") : $this->get("catId");
    	
    	
		$list = $this->m_post->get_post_data($catid,$pageNum,$orderby,$numPerPage,$keywords);
		
		$data['data'] = $list['data'];
		$data['total'] = $list['total'];
		$data['place'] = $list['place'];
		$data['user'] = $list['user'];
		if($list['total']){
    		$parr = $this->paginate($list['total']);
    	}
    	$data['parr'] = $parr;
    	
    	
    	if($this->auth['role'][0]==1){
    		$cates = get_data("newscategory");
    	}
    	else{
	    	$where = " id in (".implode(",",$this->newsRight).") and status=1 ";
	    	//$category = $this->m_post->getChannelList('',1,1000);
	    	//$cates = $category['result'];
	    	//var_dump($category);
	    	$list = $this->db->where($where,null,false)->get("WebNewsCategory")->result_array();
	    	$cates = array();
	    	foreach($list as $row){
	    		$cates[$row['id']] = $row;
	    	}
	    	//get_cate_list_by_class1($cates,$cates);
	    	//echo "<pre>";
	    	//var_dump($cates);
    	}
    	//var_dump(get_data("newscategory"));
    	
		$tt = build_news_category($cates,$catid);
		
		$this->assign("orderby",$orderby);
		$this->assign("keywords",$keywords);
		$this->assign("cates",get_data("newscategory"));
    	$this->assign("category",$tt);
    	$this->assign($data);
    	$this->display("index");
	}
	
	function delthem(){
		$delete_arr = array_filter(explode(",",$this->input->post("ids")));
		if(empty($delete_arr)) $this->error('请选择要删除的内容！');
		foreach($delete_arr as $k=>$row){
			list($t_postid,$t_catid,$t_channelid) = explode("-",$row);
			$res = $this->delete($t_postid,$t_catid,$t_channelid,true);
			if($res == false){
				$this->error('删除ID为'.$t_postid.'的POST数据失败，删除终止！');
				exit;
			}
			unset($t_postid,$t_catid,$t_channelid);
		}
		$this->success('批量删除成功！');
	}
	function edit(){
		$postId = $this->get("postId");
		$score = $this->get("score");
		$catId = $this->get("catId");
		$channelId = $this->get("channelId");
		
		if($this->is_post()) {
			
			$postId = $this->input->post("postId");
			$channelId = $this->input->post("channelId");
			$catId = $this->input->post("catId");
			$boost = $this->input->post("boost");
			
			$where = " postId=".$postId." and catId=".$catId." and channelId=".$channelId;
			$data = array(
				'boost' => $boost
			);
			
			$res = $this->db->where($where,null,false)->update("WebNewsCategoryData",$data);
			
			if($res){
				$this->success('修改权重成功');
			}else{
				$this->error('修改权重失败');
			}
		}
		
		$where = " postId=".$postId." and catId=".$catId." and channelId=".$channelId;
		$info = $this->db->where($where,null,false)->get("WebNewsCategoryData")->row_array(0);

		$this->assign(compact('info','score'));
		$this->display("edit");
	}
	function delete($postId = 0,$catId = 0,$channelId = 0,$return = false){
		!$postId && $postId = $this->get("postId");
		!$catId && $catId = $this->get("catId");
		!$channelId && $channelId = $this->get("channelId");
		
		$where = " postId=".$postId." and catId=".$catId." and channelId=".$channelId;
		
		$res = $this->db->where($where,null,false)->delete("WebNewsCategoryData");
		//var_dump($res);
		if(!$return){
			if($res){
				$this->success("删除成功");
			}else{
				$this->success("删除失败，请稍后再试", $this->_index_rel, $this->_index_uri, 'forward');
			}
		}
		else{
			if($res){
				return true;
			}else{
				return false;
			}
		}
	}
}