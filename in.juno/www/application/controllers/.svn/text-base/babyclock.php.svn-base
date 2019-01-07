<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 宝贝时钟
 * 
 */

class Babyclock extends Controller {
	
	function __construct(){
		parent::__construct();
		$this->assign('in_host',HOST);		
	}
	
	/** 
	 * @param $hour 小时
	 * @param $minute 分钟
	 */
	function index($hour = NULL,$minute = NULL){
		if($hour == NULL && $minute == NULL){
			$hour = date("G");
			$minute = date("i");
		} 
		
		$data = $this->get_baby_data($hour,$minute);
		if(empty($data['current_info'])){
			//show_404();
			redirect(HTTP_SCHEMA_STR . $in_host, '', REDIRECT_CODE);
		}
		
		//热门宝贝
		$hot = $this->db->where("status",1)->order_by("surport","desc")->limit(6)->get("BabyClockInfo")->result_array();
		
		//编辑点击量
		if($data['current_info']){
			$set = array( "hits" => $data['current_info']['hits']+1 );
			$this->db->where("id",$data['current_info']['id'])->update("BabyClockInfo",$set);
		}
		
		$ip = GetClintIp();
		$cache_key = "babyclock_vote_".$data['current_info']['id']."_".$ip;

		$is_supported = xcache_get($cache_key);
		
		$this->title = $data['current_info']['name'];
		
		$channel_id = 2;
		$this->assign($data);
		$this->assign(compact('channel_id','hot','is_supported'));
		$this->display("baby_clock","channel");
	}
	
	function get_baby_data($hour,$minute = NULL){
		$current_info = array();
		$timetable = array();
		if($minute == NULL){
			$id = $hour;
			$where = " id=".$id." and status=1";
			$current_info = $this->db->where($where,null,false)->get("BabyClockInfo")->row_array(0);
		}
		else{
			$where = " hour=".$hour." and minute=".$minute;
			$this->db->where($where,null,false);
			$timetable = $this->db->get("BabyClockTimeTable")->row_array(0);
			
			if(!$timetable){
				$where = " hour <= $hour and minute < $minute";
				$timetable = $this->db->where($where,null,false)->order_by("hour desc,minute desc")->get("BabyClockTimeTable")->row_array(0);
				//$timetable = $list[array_rand($list)];
			}
			$timetable['bid'] && $current_info = $this->db->where("id",$timetable['bid'])->get("BabyClockInfo")->row_array(0);
			
		}
		
		//查出前俩和后俩
		if($id){
			//按ID
			$prev = $this->db->where("id < $id and status=1",null,false)->order_by("id","desc")->limit(2)->get("BabyClockInfo")->result_array();
			$next = $this->db->where("id > $id and status=1",null,false)->order_by("id","asc")->limit(2)->get("BabyClockInfo")->result_array();	
		}
		else{
			$prev = array();
			$next = array();
			//按时间
			
			$sql_prev = "(select t.*,b.* from BabyClockTimeTable t left join BabyClockInfo b on t.bid = b.id where ( minute < {$minute} and  hour = {$hour} )  and status=1 order by minute desc) 
union
(select t.*,b.* from BabyClockTimeTable t left join BabyClockInfo b on t.bid = b.id where hour < {$hour}   and status=1 order by hour desc,minute desc) 
order by hour desc,minute desc
limit 2";
			$sql_next = "(select t.*,b.* from BabyClockTimeTable t left join BabyClockInfo b on t.bid = b.id where ( minute > {$minute} and  hour = {$hour} )  and status=1 order by minute asc) 
union
(select t.*,b.* from BabyClockTimeTable t left join BabyClockInfo b on t.bid = b.id where hour > {$hour}   and status=1 order by hour asc,minute asc) 
order by hour asc,minute asc
limit 2";
			
			
			$prev = $this->db->query($sql_prev)->result_array();
			$next = $this->db->query($sql_next)->result_array();
			$prev = array_reverse($prev);  
		}
		
		return array('prev'=>$prev,'next'=>$next,'current_info'=>array_merge($current_info,$timetable));
	}
	
	function upload(){
		if(!$this->auth){
			show_404();
		}
		$this->load->config("config_app");
		$config = $this->config->item('upload_image_api');
		
		if($this->is_post()){
			
			$name = $this->input->post("name");
			$gender = $this->input->post("gender");
			$status = $this->input->post("status");
			$summary = $this->input->post("summary");
			$birth = $this->input->post("birth");
			$hometown = $this->input->post("hometown");
			$blood = $this->input->post("blood");
			$constellation = $this->input->post("constellation");
			$height = $this->input->post("height");
			
			
			$image = basename($this->input->post("babyphoto"));
			if(empty($name) || empty($summary) || empty($birth) || empty($hometown)
			|| empty($height) || empty($image)){
				$result_js['code'] = -1;
				$result_js['msg'] = "所有宝贝信息都必填，请确认填写完毕再提交";
				$this->echo_json($result_js);
			}
			
			$thumb = image_url($image,"baby","bldp");
			$big = image_url($image,"baby","bhdp");
			$medium = image_url($image,"baby","bmdp");
			
			$data = array(
				'name' => $name,
				'gender' => $gender,
				'birth' => $birth,
				'hometown' => $hometown,
				'blood' => $blood,
				'height' => $height,
				'constellation' => $constellation,
				'summary' => $summary,
				'thumb' => $thumb,
				'big' => $big,
				'medium' => $medium,
				'image' => $image,
				'status' => 0,
				'uid' => $this->auth['uid']
			);
			$res = $this->db->insert("BabyClockInfo",$data);
			if($res){
				$result_js['code'] = 1;
				$result_js['msg'] = "上传宝贝信息成功！";
			}
			else{
				$result_js['code'] = 0;
				$result_js['msg'] = "上传宝贝信息失败，请稍后再试！";
			}
			$this->echo_json($result_js);
		}
		$this->assign(compact('config'));
		$this->display("baby_clock_upload","channel");
	}
	function get_info($hour,$minute = NULL){
		$data = $this->get_baby_data($hour,$minute);
		echo encode_json($data['current_info']);
	}
	
	function support($id){
		if($id){
			$ip = GetClintIp();
			$cache_key = "babyclock_vote_".$id."_".$ip;
			
			
			if(xcache_get($cache_key)){
				$result['code'] = -2;
				$result['msg'] = "您今天已经给Ta投过票了!";
				echo encode_json($result);
				exit;
			}
			
			$result = array();
			$data = $this->get_baby_data($id,NULL);
			if(empty($data['current_info'])){
				$result['code'] = 0;
				$result['msg'] = "不存在的宝贝";
			}else{
				$set = array(
					"surport" => $data['current_info']['surport']+1
				);
				
				$this->db->where("id",$id);
				$res = $this->db->update("BabyClockInfo",$set);
				if($res){
					$result['code'] = 1;
					//投票成功，写入缓存
					$today_end = mktime(23,59,59,date("m"),date("d"),date("Y"));
					$expier_time = $today_end - time();
					$res = xcache_set($cache_key,1,$expier_time);
				}
				else{
					$result['code'] = -1;
					$result['msg'] = "操作失败，请稍后再试!";
				}
			}
			echo encode_json($result);
		}
	}
}