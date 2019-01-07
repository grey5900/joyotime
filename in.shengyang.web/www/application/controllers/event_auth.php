<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-27
 */

// class Event_Auth extends AuthController {
class Event_Auth extends Controller {
    function __construct() {
        parent::__construct();
        
        empty($this->auth) && ($this->auth['uid'] = $this->get('uid'));
        
        $this->load->model('webevent_model', 'm_webevent');
        $this->load->model('webeventapply_model', 'm_webeventapply');
        $this->load->model('webeventownplace_model', 'm_webeventownplace');
    }
    
    /**
     * 报名
     * @param 报名类型 $action
     * @param int $id
     */
    function signup($action = '0', $id , $return = false) {
        if(empty($this->auth['uid'])) {
            $this->echo_json(array('code' => 1, 'message' => '请先登陆'));
        }
        
        $id = formatid($id);
    
        // 获取活动内容
        $event = $this->m_webevent->select_by_id($id);
        
        if(empty($event)) {
            $this->echo_json(array('code' => 1, 'message' => '错误的活动访问'));
            return;
        }
    	
        //报名方式取消 fc_lamp
        $action = 1;
        if($event['applyType'] !== $action) {
            //$this->echo_json(array('code' => 1, 'message' => '错误的报名方式'));
            //return;
        }
        if(!$return){
        if(TIMESTAMP < strtotime($event['startDate'])) {
        	$this->echo_json(array('code' => 1, 'message' => '活动还未开始'));
        }
        if(TIMESTAMP > strtotime($event['endDate'])) {
            $this->echo_json(array('code' => 1, 'message' => '活动已经结束'));
        }
        }
        // 查询用户是否已经报过名了(fc_lamp:由于用户只要发布了POST也算用户参与，所以进一歩确认用户是否填写了报名表)
        $apply = $this->m_webeventapply->select(array('uid' => $this->auth['uid'], 'eventId' => $id));
        if(!empty($apply['signInfo'])) {
        	 if(!$return){
	            $this->echo_json(array('code' => 1, 'message' => '您已经填写过报过名表啦'));
	            return False;
        	 }
        }
        $data = array('uid' => $this->auth['uid'], 'eventId' => $id);
        switch($action) {
            case 1:
            	$post_data = $this->post('data');
            	// 先用:分开
            	$post_data = substr($post_data, 0, -3);
            	$datas = explode("^_^", $post_data);
            	$sign_info = array();
            	foreach($datas as $line) {
            		$strs = explode('：', $line);
            		$sign_info[$strs[0]] = $strs[1];
            	}
                $data['signInfo'] = encode_json($sign_info);
                $data['latitude'] = $this->get('latitude');
                $data['longitude'] = $this->get('longitude');
                break;
            case 2:
                
                break;
            case 3:
                
                break;
        }
       
        if($apply)
        {
        	unset($data['uid'],$data['eventId']);
        	$b = $this->m_webeventapply->update(array('uid' => $this->auth['uid'], 'eventId' => $id),$data);
        }else{
        	$b = $this->m_webeventapply->insert($data);
        }
        
        
        if($b) {
            // 成功，那么去更新首页权重
            // 先检查是否在首页推荐数据中
            $this->load->model('homepagedata_model', 'm_homepagedata');
            $home_data = $this->m_homepagedata
                            ->select(array('itemType' => 5, 'itemId' => $id));
            if($home_data) {
                $this->load->helper('app');
                $ext_rank_order = get_ext_rank_order(5, $id);
                $this->m_homepagedata
                        ->update(array('itemType' => 5, 'itemId' => $id), 
                        array('rankOrder' => ($home_data['baseRankOrder']+$ext_rank_order)));
            }
            
            $json = array('code' => 0, 'message' => '报名成功啦');
        } else {
            $json = array('code' => 1, 'message' => '报名失败，请重试');
        }
        if(!$return){
        	$this->echo_json($json);
        }
    }
}