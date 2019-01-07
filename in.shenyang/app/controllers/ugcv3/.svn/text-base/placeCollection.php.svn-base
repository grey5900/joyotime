<?php

 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class placeCollection extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		$this->load->model("placecollection_model","m_placecollection");
	}
	
	function index(){
		$pageNum 	= $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
    	$keywords 	= $this->input->post("keywords");
		$field 		= $this->input->post("field");
		
		$orderField = $this->input->post("orderField") ;
		$orderDirection = $this->input->post("orderDirection") ;
		
		$isread = $this->input->post("isread") ? $this->input->post("isread"):0;
    	$isEssence = $this->input->post("isEssence") ? $this->input->post("isEssence") : 0;
    	$isTaboo = $this->input->post("isTaboo") ? $this->input->post("isTaboo"): 0;
    	
    	$status    = $this->input->post("status");
    	
    	$isrecommend = $this->input->post("isrecommend") ? $this->input->post("isrecommend"):0;
    	
    	$where = " 1=1 ";
    	
    	$where .= $status > 0 || $status==='0' ? " and  pc.status = ".$status  : "";
    	$where .= $isEssence ? " and isEssence = 1" : "";
    	$where .= $isTaboo ? " and pc.isTaboo = 1" : "";
    	$where .= $photo ? " and pc.photo IS NOT NULL " : ""; 
    	$where .= $isread ? " and m.read is null" : ""; 
    	$where .= $isrecommend ? " and r.itemId is not null" : "";
    	
    	if($field && $keywords){
    		
    		if($field == "pc.id" || $field == "p.id"){
    			$keywords_array = explode(",",$keywords);
    			foreach($keywords_array as &$ka){
    				$ka = intval($ka);
    			}
    			$keywords = implode(",",$keywords_array);
    			$where .=  " and $field in (".$keywords.") " ; 
    		}
    		else if($field == "pc.name" || $field == "p.placename"){
    			$where .=  " and $field like '%".$keywords."%'"  ;
    		}
    	}
    	
    	
    	$order = ($orderField && $orderDirection) ?  " $orderField $orderDirection" : " orderDate desc ";
    	
		$list = $this->m_placecollection->get_placecollection_list($where,$numPerPage,($pageNum-1)*$numPerPage , $order);
		$total = $this->m_placecollection->count_placecollection($where);
		
		if($total){
    		$parr = $this->paginate($total);
    	}
		$current_date = date("Y-m-d H:i:s");
		$this->assign(compact('status','current_date','list','isread','isEssence','isrecommend','isTaboo','parr','field','keywords','orderField','orderDirection','pageNum','numPerPage'));
		$this->display("placecollection","ugcv3");
	}
	
	function view_places($id){
		$places = $this->m_placecollection->get_places($id);
		
		$place_ids_string = '';
		foreach($places as $k=>$row){
			$place_ids_string .= $row['id'];
			$place_ids_string .= !empty($places[$k+1]) ?  "-" : "";
		}
		
		$this->assign(compact('places','place_ids_string'));
		$this->display("view_places","ugcv3");
	}
	
	function change_status(){
		
		$this->load->helper('ugc');
		
		$id = $this->input->get("id");
		$status = $this->input->get("status");
		$target = $this->input->get("target");
		
		
		
		$row = $this->db->where("id",$id)->get("PlaceCollection")->row_array(0);
		$message = $target == "dialog" ? 
										$this->input->post("message") : 
										"您的地点册《".$row['name']."》已经被管理员恢复显示！";
		if($this->is_post()){
			
			if($row['status'] == $status){
				$this->error("你指定的状态和本身的状态没有区别。");
			}
			
				
					
			$b = $this->db->where("id",$id)->set(array("status"=>$status))->update("PlaceCollection");
			if($b){
				send_message($message, array($row['uid']), array($row['id']), array(20), false);
				$this->success("OK",'','','',array(
                    'id' => $id,
                    'key' => $status,
                    'value' => $status
				));
			}
			else{
				$this->error("操作失败");
			}
			
			
		}
		else{ //木有提交

			if($row['status']==2){
				$this->error("这个地点册已经被用户删除了，无法操作！");
			}
			
			$this->assign(compact('id','status','target'));
			$this->display("ban_place_collection","ugcv3");
		}
		
	}
	
	function edit(){
		$id = intval($this->get('id'));
		if($this->is_post()){
			$image = $this->post('pc_image');
			if(!empty($image)){
				$data = array(
					'image' => basename($image)
				);
				$b = $this->db->where('id',$id)->update($this->_tables['placecollection'],$data);
				if($b){
					$this->success("OK",'','','closeCurrent');
				}
				else{
					$this->error("编辑出错，请稍后再试!");
				}
			}
			else{
				$this->error("请上传封面!");
			}
		}
		$info = $this->db->where('id',$id)->get($this->_tables['placecollection'])->row_array();
		
		$this->assign(compact('info'));
		$this->display("pc_edit","ugcv3");
	}
}