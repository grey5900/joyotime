<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 地点册
 */

class Collection_Place extends Controller {
	
	var $front_page_size = 24;
	var $detail_page_size = 10;
	
	function __construct(){
		parent::__construct();
		$this->load->model("placecollection_model","m_placecollection");
		
	}
		
	function index($sort = 'hot' , $page = 1   /*,$page = 1 */){		
		$page = formatid($page , 1);
		$total = $this->m_placecollection->count_place_collections($sort);
		//if($total && $subpage <= 5){
			$this->paginate_style2('/placecoll/'.$sort,$total,$page,$this->front_page_size);
			$list  = $this->m_placecollection->list_place_collection($page , $this->front_page_size , $sort);
		//}
		$this->assign(compact('list','sort'));
		$this->display('index','placecollection');
	}
	
	
	function detail($id , $page = 1){
		$page = formatid($page , 1);
		if(!intval($id)){
			show_404();
		}
		$detail = $this->m_placecollection->get_placecollection($id , $page , $this->detail_page_size);
		
		if($detail['pcount']){
			$this->paginate_style2('/placecoll/'.$id,$detail['pcount'] , $page ,$this->detail_page_size);
		}
		$is_collectable = true;
		if($this->auth['uid']){
			//是否已经收藏
			$this->load->model('userfaverplacecollection_model','m_userfaverpc');
			$is_collectable = $this->m_userfaverpc->check_favorite($this->auth['uid'],$id);
			//是否已经赞 踩
			$this->load->model('postappraise_model','m_postappraise');
			$isapraised = $this->m_postappraise->check_praise($this->auth['uid'],20,$id,1);
			$isstamped = $this->m_postappraise->check_praise($this->auth['uid'],20,$id,-1);
		}
		$this->title = '地点册：'.$detail['name'].' by '.$detail['user']['name'];
		
		
		$this->assign(compact('detail','is_collectable','isapraised','isstamped'));
		$this->display('detail','placecollection');
	}
}