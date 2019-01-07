<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 新闻频道管理
 */

class Tags extends MY_Controller {
	
	
    /**
     * 标签列表
     */
    function index() {
    	
    	$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	
    	$keywords = $this->input->post("keywords");
    	
    	$where = " 1=1  ";
    	if($keywords){
    		$where .= " and content like '%".$keywords."%' ";
    	}
    	
    	$q_total = $this->db->where($where,null,false)->get("Tag");
    	$total = $q_total->num_rows();
    	$this->db->limit($numPerPage , ($pageNum-1)*$numPerPage);
		$list = $this->db->where($where,null,false)->order_by("id","desc")->get("Tag")->result_array();
		
		if($total){
			$parr = $this->paginate($total);
    	}
    	
		$this->assign("parr",$parr);
		
    	$this->assign("list",$list);
    	$this->assign("pageNum",$pageNum);
		$this->assign("numPerPage",$numPerPage);
		$this->assign("keywords",$keywords);
     	$this->display("index");   
    }
    
    //删除父分类，子分类也删除
    function delete(){
    	$page = $this->get("page");
    	$id = $this->get('id');
    	if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		$this->db->where("id",$id);
    		$res = $this->db->delete("Tag");
    		// 去更新缓存
            $api_rtn = api_update_cache('Tag');
    		$this->message($statusCode, "删除成功!", $this->_index_rel, $this->_index_uri, 'forward',array('rtn' => $api_rtn,'pageNum'=>$page));
    	}
    }
    
	function delthem_tags(){
    	
		$page = $this->get("page");
		$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
    		$statusCode = 200;
    		
    		
    		$this->db->where_in("id",explode(",",$ids));
    		
    		$res = $this->db->delete("Tag");
 			
    		// 去更新缓存
    		$api_rtn = api_update_cache('Tag');
    		$this->message($statusCode, "批量删除成功!", $this->_index_rel, $this->_index_uri, 'forward', array('rtn' => $api_rtn,'pageNum'=>$page));
    	}
    }
    
  
	function add(){
		$id = intval($this->get('id'));
		$news_category_selected = 0;
		$cate_self = 0;
		if($id>0){
			//读数据
			$info = $this->db->where(array("id"=>$id))->get('Tag')->row(0,"array");
			
			$this->assign("info",$info);
			
		}
		
		if($this->is_post()) {
		 	
		 	$id = $this->input->post("id");
		 	$content = $this->input->post("content");
			$data = array(
		 		'content' => $content
		 	);
		 	
		 	if($id){ //update
		 		$tip = "修改";
		 		$res = $this->db->where(array("id"=>$id))->update("Tag",$data);
		 	}else{
		 		//先查看是否存在
		 		$ex = $this->db->where(array("content"=>$content))->get("Tag")->row_array(0);
		 		if(!empty($ex)) $this->error("您添加的标签已经存在了");
		 		$tip = "添加";
		 		$res = $this->db->insert("Tag",$data);
		 	}
		 	if($res){
		 		// 去更新缓存
		 		$api_rtn = api_update_cache('Tag');
		 		$this->success($tip."标签成功!", $this->_index_rel, $this->_index_uri, 'forward', array('rtn' => $api_rtn));
		 	}else{
		 		$this->error($tip."标签失败!", $this->_index_rel, $this->_index_uri, 'forward');
		 	}
		}
		 
	
    	$this->display("add");
    }
   
    
   
}
