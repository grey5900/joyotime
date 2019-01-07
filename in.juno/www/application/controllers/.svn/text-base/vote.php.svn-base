<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   


class Vote extends Controller {
	
	function vote(){
		parent::__construct();
		header("cache-control:no-cache,must-revalidate");
		
		$this->load->model('webeventapply_model', 'm_webeventapply');
		$this->load->helper ( 'user_helper' );
		$this->config->load ('config_vote');
		
		$point_case = get_data("point_case");
		
		$option_id = $this->input->post('oid');
		$item_type = $this->input->post('itemtype');
		$item_id =   $this->input->post('itemid');
		
				
		if(empty($this->auth['uid'])){
			$this->auth['uid'] = $this->input->post('uid');
		}
		$data = array();
		$data['point'] = 0;
		$data['over'] = 0;
		if(empty($this->auth['uid'])){
			$data['code'] = -1;
			$data['msg'] = "请先登录IN成都！";
			$this->echo_json($data);
		}
		
		//投票规则要先出来
		$vote_info = $this->db->where(array('itemId'=>$item_id,'itemType'=>$item_type))
						->get($this->_tables['vote'])
						->row_array(0);
		/*判断时间*/
		if($vote_info['startDate'] > date("Y-m-d H:i:s") && $vote_info['startDate']!='0000-00-00 00:00:00'){
			$data['code'] = -2;
			$data['msg'] = "投票还没有开始！";
			$data['over'] = 1;
			$this->echo_json($data);
		}
		elseif($vote_info['endDate'] < date("Y-m-d H:i:s") && $vote_info['endDate']!='0000-00-00 00:00:00'){
			$data['code'] = -3;
			$data['msg'] = "投票已经结束了！";
			$data['over'] = 1;
			$this->echo_json($data);
		}	

		//规则恶心了。 M天N票
		$days = $vote_info['during'];
		$today = date('Y-m-d');
		if($days==1){
			$this->db->like("createDate",date("Y-m-d"));
		}
		else if($days > 1){
			$diffdays = $days - 1;
			$start = date('Y-m-d',strtotime('-'.$diffdays.'days',strtotime($today)));
			$end = $today;
			
			$this->db->where("createDate >'$start' and createDate<='$end 23:59:59'",null,false);
		}
		//已经投票的次数
		$count = $this->db->select("count(id) as num")
							->where(array('voteId'=>$vote_info['id'],'uid'=>$this->auth['uid']))
							
							->get($this->_tables['votelog'])->row_array(0);
		
		if($count['num'] >= $vote_info['votePerDay']){
			$data['code'] = 0;
			$data['msg'] = "失败，已达到投票数限制";
			$data['over'] = 1;
			$this->echo_json($data);
		}
		
		/*if($vote_info['point']>0){
			//看用户积分够不够！
			$user_point = $this->db->select('point')->where('id',$this->auth['uid'])->get($this->_tables['user'])->row_array(0);
			if($user_point['point']<$vote_info['point']){
				$data['code'] = -4;
				$data['msg'] = "您的积分不够了，不能投票！";
				$this->echo_json($data);
			}
		}*/
		$user_point = 0 - $vote_info['point'];
		$remark = json_encode(array('item_id'=>$item_id,'item_type'=>$item_type));
		$point_case = $this->config->item('vote')['point_case'];
		
		$point_result = change_point($this->auth['uid'] , $point_case , $user_point , $remark);
		if($point_result == 0){
			$data['code'] = -4;
			$data['msg'] = "失败，您的积分不足";
			$this->echo_json($data);
		}
		if($point_result == -3){
			$data['code'] = -5;
			$data['msg'] = "更新积分出错，请稍后再试！";
			$this->echo_json($data);
		}
		if($point_result == -4){
			$data['code'] = -5;
			$data['msg'] = "记录日志出错，请稍后再试！";
			$this->echo_json($data);
		}
		
		
		//开始投票  记录log，追加票数，发放道具
		$vote_log = array(
			'voteId' => $vote_info['id'],
			'optionId' => $option_id,
			'uid' => $this->auth['uid'],
			'point' => $vote_info['point'],
			'ip' => GetClintIp(),
		);
		
		$vb = $this->db->insert($this->_tables['votelog'],$vote_log);
		if(!$vb){
			$this->echo_json(array('code'=>-1,'msg'=>'创建投票日志出错，请稍后再试！'));
		}
		$vb && $vb = $this->db->where('defineId',$option_id)->where('voteId',$vote_info['id'])->set('votes','votes+1',false)->update($this->_tables['voteoptions'],$vote_data);
		if(!$vb){
			$this->echo_json(array('code'=>-1,'msg'=>'更新票数出错，请稍后再试！'));
		}
		$apply = $this->m_webeventapply->select(array('uid' => $this->auth['uid'], 'eventId' => $id));
        if(!empty($apply['signInfo'])) {
            //$this->echo_json(array('code' => 1, 'message' => '您已经填写过报过名表啦'));
            //return False;
        }else{
			include "event_auth.php";
			$e_auth = new Event_Auth();
			$e_auth -> auth['uid'] = $this->auth['uid'];
			$e_auth -> signup(0,$item_id,1);
        }
		
		//是否发道具。。。
		$result['result_code'] = 0 ;
		if($vote_info['relatedItem']>0){
			$api_name = "/props/present/";
			$attrs = array(
				'id' => $vote_info['relatedItem'],
				'uid' => $this->auth['uid']
			);
			$result = decode_json ( request_api( $api_name,'POST',$attrs,array('uid'=>56)) );
			//var_dump($result);
		}
		
		$data['code'] = 1;
		$data['point'] = $user_point>0?"+":""  .$user_point;
		$data['msg'] = "投票成功";
		$data['msg'] .= ($user_point!=0 ? ($user_point>=0 ?" 获得".abs($user_point)."积分":" 消耗".abs($user_point)."积分") : '').
		($result['result_code']!=0 ? "，但奖励的道具在途中丢失了":"！");
		
		
		$this->echo_json($data);
	}
}