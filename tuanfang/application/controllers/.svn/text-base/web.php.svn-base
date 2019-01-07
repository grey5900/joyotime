<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API
 * @author piggy
 * @date 2013-08-13
 */

class Web extends WebController {
    function mobile_download() {
        $this->display('mobile_download');
    }
	function __construct(){
		parent::__construct();
		$this->load->helper("url");
	}
	function index($page = 1 ,$keywords = '') {
		
		//$keywords = $this->input->post('keywords');
		
		$pagesize = 10;
		$attrs = array(
			'page' => $page,
			'page_size' => $pagesize,
			'group' => 1,
			'order' => 99,
			'on_group' => 1
		);
		
		$keywords && $attrs['key'] = $keywords;
		
		if($keywords){
			$search = 1;
		}
		
		$list = $this->get_data($attrs,$page,$pagesize,$keywords);
		
		if(!$list['list'] && $keywords){
			unset($attrs['key']);
			$no_record = 1;
			$list = $this->get_data($attrs,$page,$pagesize,$keywords);
		}
		
		$this->right_side();
		$this->banner();
		
		$this->assign(compact('list','keywords','search','no_record','page'));
		$this->display('index');
	}
	
	function detail($house_id = 0){
		$attrs = array(
			'id' => $house_id
		);
		if(!$house_id){
			show_404();
		}
		$info = decode_json(request_api("/house_detail_all",$attrs,"get"));
		$house_pic_domain = $this->config->item('house_pic_domain');
		$info['end_sc'] = $this->get_countdown($info['group_expire_date']);
		$config_detail = $this->config->item('ext_detail');
		$this->right_side();
		$this->banner();
		
		$this->assign(compact('info','config_detail','house_pic_domain'));
		$this->display('building');
	}
	
	function apply(){
		$type = $this->post('type');
		$name = $this->post('name');
		$cell = $this->post('cell');
		$house_id = $this->post('id');
		
		$attrs = array(
			'id' => $house_id ,
			'name' => $name ,
			'cellphone_no' => $cell ,
			'type' => $type
		);
		
		
		$info = request_api("/apply",$attrs,'POST',true);
		$info = decode_json($info);
		if($info['status'] == 200){
			echo "{code:0}";
		}else{						
			echo "{code:1,msg:'{$info['message']}'}";
		}
		
	}
	
	function download(){
		$this->display('download');
	}
	
	function banner(){
		$banner = $this->db->where('k','web_banner')->get('setting')->row_array();
		$banner['v'] && ($banners = decode_json($banner['v']));
		
		$this->assign('banners',$banners);
	}
	
	function right_side(){
		//右侧栏
		//有团购的楼盘
		//参加过团购的人 order  => type = 0
		$this->db->where('has_group',1);
		$lp_count = $this->db->count_all_results('house');
		
// 		$this->db->where('type',0);
// 		$join_count = $this->db->count_all_results('order');
		$join_count = $this->db->select_sum('group_count')->where('has_group', 1)->from('house')->get()->row_array()['group_count'];
		
		//20条最新的参团信息
		$latest_join = $this->db->where('type',0)->order_by('last_dateline','desc')->group_by('cellphone_no')->limit(20)->get('order')->result_array();
		foreach($latest_join as &$lj){
			$lj['cellphone_no'] = substr_replace($lj['cellphone_no'],'****',3,4);
			$lj['name'] = mb_substr($lj['name'],0,1,"utf-8")."**";//substr_replace($lj['name'],'**',1,2);
		}
		
		//楼盘排行前10
		$top7 = $this->db->where('has_group',1)->order_by('group_count','desc')->limit(10)->get('house')->result_array();
			
		
		$this->assign(compact('lp_count','join_count','latest_join','top7'));
	}
	
	function get_countdown($date){
		/*$day_end = strtotime(date("Y-m-d")." 23:59:59");
		$offset = $day_end - time();
		if(strpos($date,'-')>0 ){
			$days = ceil((strtotime($date)-time())/3600*24);
		}else{
			$days = $date;
		}
		
		$seconds = $offset+($days-1)*3600*24;

		$hours = floor($seconds/3600);
		$mins = floor(($seconds - $hours*3600)/60);
		$scs = $seconds - $hours*3600 - $mins*60;
		return compact('hours','mins','scs');*/
		//额，返回天小时分
		if(strpos($date,'-')>0 ){
			$endtime = strtotime($date." 23:59:59");
		}else{
			$day_end = strtotime(date("Y-m-d")." 23:59:59");
			$endtime = $day_end + ($date-1)*3600*24;
		}
		$diff = $endtime - time();
		
		$days = floor ( $diff / (3600*24) );
		$diff = $diff - $days*3600*24;
		$hours = floor ( $diff  / 3600 );  
		$diff = $diff - $hours*3600;
		$mins = floor ( $diff / 60 );  
		return compact('days','hours','mins');
	}
	
	function get_data($attrs,$page = 1,$pagesize = 10,$keywords = 0){
		$list = decode_json(request_api('/house_list', $attrs, 'GET'));
		
		if($list){
			$total = $list['total_num'];
			//$paginate = paginate($total,$page,$pagesize);
			if($list['list']){
				foreach($list['list'] as &$row){	
					$row['end_sc'] = $this->get_countdown($row['group']['expire_day']);
				}
			}
			$paginate = paginate_style2('/web/index',$total,$page,$pagesize,5,array('keywords'=>$keywords));
			if($page > $paginate['total_page']){
				redirect("http://".$this->config->item('doamin')."/web/index");
			}
		}
		
		return $list;
	}
	
	function api(){
		$keywords = $this->get('keyword');
		$callback = $this->get('callback');
		
		//{image,title,market_price,youhui,fanli,tenper,now_number}
		
		$h = $this->db->like('name',$keywords)->order_by('group_dateline','desc')->limit(1)->get('house')->row_array();
		
		$data['image'] = $h['cover'];
		$data['title'] = $h['name'];
		$data['market_price'] = $h['price'];
		$data['youhui'] = $h['group_title'];
		$data['fanli'] = $h['recommend_title'] ? $h['recommend_title'] : '0' ;
		$data['tenper'] = '';
		$data['now_number'] = $h['group_count'];
		$data['end_time'] = strtotime($h['group_expire_date']);
		$data['url'] = "http://tf.chengdu.cn/web/detail/".$h['house_id'];
		
		if($callback){
			echo $callback."(".json_encode($data).")";
		}
		else{
			echo json_encode($data);
		}
		exit;
	}
}