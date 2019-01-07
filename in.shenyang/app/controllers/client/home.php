<?php 
// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class Home extends MY_Controller{
		
	function __construct(){
		parent::__construct();
		$this->digest = $this->config->item('digest_type');
		$this->assign('digest_type', $this->digest);
		$this->load->model("homepagedata_model","m_home");
		
	}
	
	function index(){
		
		$pageNum 	= $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
		
		$keywords = trim($this->post("keywords"));
		$itemtype = $this->post("itemtype");
		
		empty($itemtype) && $itemtype = 1;
		
		$total = $this->m_home->count_all($keywords,$itemtype,$pageNum,$numPerPage);

		if($total){
    		$parr = $this->paginate($total);
    	}
	
		$list = $this->m_home->get_list($keywords,$itemtype,$pageNum,$numPerPage);
		//展示一下网站链接一下链接
		
		$this->config->load('config_home');
		$wap2web = $this->config->item('wap2web');
		foreach($list as &$row){
			$row['weblink'] = $row['hyperLink'];
			foreach($wap2web as $key => $w2w){       		
				
	        	if(stripos($row['hyperLink'],$key)!==false){
	        		$id = str_replace($key,'',$row['hyperLink']);
	        		//$home_data[$k]['link'] = $w2w.'/'.$id; 
	        		$row['weblink'] = "http://in.jin95.com".$w2w.'/'.$id;
	        	}
	        }   
		}  
		
		$this->assign(compact('keywords','itemtype','parr','list'));
		$this->display("home","client");
	}
	function delete(){
		$ids = $this->post("ids");
		
		if(!empty($ids)){
			foreach($ids as $row){
				$itemid = $iemtype = 0;
				list($itemid,$itemtype) = explode("-",$row);
				$b = $this->db->where("itemId=".$itemid." and itemType=".$itemtype,null,false)->delete("HomePageData");
				if(!$b){ 
					break; 
				}
				
			}
			
			if(!$b){
				$this->error("在删除ID为:".$itemid."类型为:".$this->digest[$itemtype]);
			}
			else{
			    $this->load->helper('poi');
			    api_update_cache($this->_tables['homepagedata']);
				$this->success("ok");
			}
		}
		
	}
}
?>