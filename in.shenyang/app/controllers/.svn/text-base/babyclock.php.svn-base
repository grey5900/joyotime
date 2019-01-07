<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 新闻频道管理
 */

class Babyclock extends MY_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model("user_model","m_user");
	}
	
	function index(){
		
		$list = array();
		for($i=0;$i<=23;$i++){
			$list[$i]['hour'] = $i;
			$count = $this->db->where("hour",$i)->select("count(0) as c")->get($this->_tables["babyclocktimetable"])->row_array(0);
			$list[$i]['count'] = $count['c'];
		}
		
		$this->assign("list",$list);
		$this->display("index");
	}
	
	function status(){
    	$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
			$statusCode = 200;
    		$set = array("status"=>"ABS(status-1)");
    		$this->db->set($set,'',false);
    		$this->db->where("id in (".$ids.")");
    		$res = $this->db->update("BabyClockInfo");
    		$this->clearBabyTimeTable($ids);
    		$this->message($statusCode, "批量修改状态成功!", $this->_index_rel, $this->_prev_uri, 'forward');
		}
    }
	function delete(){
		$id = $this->get('id');
    	if ('POST' == $this->server('REQUEST_METHOD')) {
    		$this->db->where("id",$id);
    		$this->db->delete("BabyClockInfo");
    		//还要删除timetable
    		$this->clearBabyTimeTable($id);
    		$this->success( "删除成功!", $this->_babylist_rel, $this->_babylist_uri, 'forward');
    	}
	}
	function delthem(){
		$ids = $this->input->post('ids');
		if ('POST' == $this->server('REQUEST_METHOD')) {
			$id_arr = explode(",",$ids);
			$this->db->where_in("id",$id_arr);
    		$this->db->delete("BabyClockInfo");
    		$this->clearBabyTimeTable($id_arr);
			$this->success( "批量删除成功!", $this->_babylist_rel, $this->_babylist_uri, 'forward');
		}
	}
	function clearBabyTimeTable($ids){
		if(is_array($ids)){
			$ids = implode(",",$ids);
		}
		$sql = "delete t from BabyClockTimeTable t,BabyClockInfo i where i.id=t.bid and i.status=0 and i.id in (".$ids.")";
		$this->db->query($sql);
	}
	function babylist(){
		
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
		$keywords = $name = $this->input->post("keywords");
		$status = $name = $this->input->post("status");
		
		
		$where = " status in (1,0) ";
		if($status || $status===0 || $status==='0'){
			$where = "  status=".$status;
		}
		
		if($keywords){
			$where .= " and (name like '%".$keywords."%' or summary like '%".$keywords."%')";
		}
		
		$q = $this->db->where($where,null,false)->get($this->_tables["babyclockinfo"]);
		$total = $q->num_rows();
		$this->db->limit(($pageNum-1)*$numPerPage,$numPerPage);
		$list = $q->result_array();
					 
		foreach($list as &$row){
			
			$row['uid'] && $row['user'] =  $this->db->where("id",$row['uid'])->get($this->_tables["user"])->row_array();
			// = $user[0];
		}
		unset($row);
		
		if($total){
    		$parr = $this->paginate($total);
    	}
    	
    	$this->assign(compact('parr','list','numPerPage','keywords','status'));
		$this->display("list");
	}
	function babyinfo(){
		
		$pageNum = 1;$this->input->get("pageNum") ? $this->input->get("pageNum") : 1;
    	//$numPerPage = $this->input->get("numPerPage") ? $this->input->get("numPerPage") : 20;
		$keywords = $name = $this->input->get("keywords");
		$status = $name = $this->input->get("status");
		$numPerPage = $num = $name = $this->input->get("num");
		$keywords = urldecode($keywords);
		$where = " status = 1 ";
		
		
		if($keywords){
			$where .= " and (name like '%".$keywords."%' or summary like '%".$keywords."%' or id = ".intval($keywords).")";
		}
		
		$q = $this->db->where($where,null,false)->get($this->_tables["babyclockinfo"]);
		$total = $q->num_rows();
		$this->db->limit(($pageNum-1)*$numPerPage,$numPerPage);
		$list = $q->result_array();
					 
		/*if($total){
    		$parr = $this->paginate($total);
    	}
    	
    	$this->assign(compact('parr','list','numPerPage','keywords','status'));
		$this->display("babyinfo");*/
		echo  json_encode($list);
	}
	
	function addbaby(){
		
		$id = $this->get("id");
		if($id){
			$info = $this->db->where("id",$id)->get($this->_tables["babyclockinfo"])->row_array(0);
			$this->assign("info",$info);
		}
		
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
			
			
			$image = basename($this->input->post("newsimg"));
			
			$thumb = image_url($image,"baby","bldp");
			$big = image_url($image,"baby","bhdp");
			$medium = image_url($image,"baby","bmdp");
			
			//var_dump($image,$thumb,$big,$medium);exit;
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
				'status' => $status
			);
			$tip = "添加";
			if($id>0){
				$tip = "修改";
				$this->db->where("id",$id);
				$res = $this->db->update($this->_tables["babyclockinfo"],$data);
				if($data['status']==0){
					$this->clearBabyTimeTable($id);
				}
			}
			else{
				//$data['createDate'] = time();
				$res = $this->db->insert("BabyClockInfo",$data);
			}
			if($res){
				$this->success($tip . '宝贝信息成功', $this->_babylist_rel, $this->_babylist_uri, 'closeCurrent');
			}
			else{
				$this->error($tip . '宝贝信息失败，请稍后再试');
			}
			
		}
		$this->display("addbaby");
	}
	function edit(){
		$hour = $this->input->get("hour");
		$list = $this->db->where("hour",$hour)->get($this->_tables["babyclocktimetable"])->result_array();
		$data = array();
		
		foreach($list as $row){
			$info = $this->db->where("id",$row['bid'])->get($this->_tables["babyclockinfo"])->row_array(0);
			$arr = array_merge($row,$info);
			$data[$row['minute']] = $arr;
		}
		$timeTable = array();
		
		for($i=0;$i<=59;$i++){
			$timeTable[$i]['minute'] = $i;
			$timeTable[$i]['data'] = $data[$i];
		}
		
		$this->assign(compact('hour','timeTable'));
		$this->display("edit");
	}
	
	function specific(){ 
		$hour =  $this->input->get("hour");
		$minute =  $this->input->get("minute");
		
		$where = " hour=".$hour." and minute=".$minute;
		
		$this->db->where($where,null,false);
		$timetable = $this->db->get($this->_tables["babyclocktimetable"])->row_array(0);
		
		if($timetable){
			$info = $this->db->where("id",$timetable['bid'])->get($this->_tables["babyclockinfo"])->row_array(0);
		}
		
		if($this->is_post()){
			$hour =  $this->input->post("hour");
			$minute =  $this->input->post("minute");
			$bid =  intval($this->input->post("baby"));
			$delete = intval($this->input->post("delete"));
			
			
			if($delete){
				$res = $this->db->delete($this->_tables["babyclocktimetable"],array('hour'=>$hour,'minute'=>$minute));
				if($res){
					$this->success('删除成功',null,null,'forward');
				}
				else{
					$this->error('删除失败，请稍后再试');
				}
			}
			
			if(!$bid){
				$this->error("请先选择宝贝再提交");
			}
			
			$data = array(
				'hour' => $hour,
				'minute' => $minute,
				'bid' => $bid
			);
			
			if($timetable){
				$where = " hour=".$hour." and minute=".$minute;
				$this->db->where($where,null,false);
				$res = $this->db->update($this->_tables["babyclocktimetable"],$data);
			}
			else{
				$res = $this->db->insert($this->_tables["babyclocktimetable"],$data);
			}
			if($res){
				$this->success('指定宝贝时钟成功',null,null,'forward');
			}
			else{
				$this->error('指定宝贝时钟失败，请稍后再试');
			}
		}
		
		$this->assign(compact('hour','minute','info'));
		$this->display("specific");
	}
}