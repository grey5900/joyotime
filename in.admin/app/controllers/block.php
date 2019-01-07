<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 一些碎片访问
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-20
 */

class Block extends MY_Controller {
    /**
     * 获取用户信息
     */
    function get_user() {
        $id = intval($this->get('id'));
        
        $user = $this->db->get_where($this->_tables["user"], array('id' => $id))->row_array();
        
        $this->echo_json(array('id' => $id, 'name' => $user['nickname']?$user['nickname']:$user['username']));
    }
    
    /**
     * 获取地点信息
     */
    function get_place() {
        $id = intval($this->get('id'));
        
        $place = $this->db->get_where($this->_tables["place"], array('id' => $id))->row_array();
        
        $this->echo_json(array('id' => $id, 'name' => $place['placename']));
    }
    
    /**
     * 获取模型数据
     * @param $id 模型ID，如果没有。那么获取所有的模型
     */
    function get_module() {
        $id = intval($this->get('id'));
        if($id > 0) {
            $result = $this->db->get_where($this->_tables["placemodule"], array('id' => $id))->row_array();
        } else {
            $result = $this->db->order_by('rankOrder', 'asc')->get($this->_tables["placemodule"])->result_array();
        }
        
        $this->echo_json($result);
    }
    
	/**
     * 获取碎片
     * @param $id 模型ID，如果没有。那么获取所有的模型
     */
    function get_fragment() {
    	$id = intval($this->get('id'));
        /*$fragment = get_data('fragment');
        $arr = array();
        foreach($fragment as $row) {
        	$arr[] = $row;
        }
        var_dump($arr);*/
    	$arr = $this->db->get($this->_tables["webrecommendfragment"])->result_array();
        $this->echo_json($arr);
    }
    
    
	function get_tag() {
    	$id = intval($this->get('id'));
        $keywords = urldecode($this->get('keywords'));
        $num = intval($this->get('num'));
        $ext_condition = $this->get('ext_condition');
        
        if(!$ext_condition){
	        $table['Tag'] = $this->_tables["tag"];
	        $table['Place'] = $this->_tables["place"];
	        $table['User'] = $this->_tables["user"];
	        $table['PlaceCategory'] = $this->_tables["placecategory"];
        }
        else{
        	$table[$ext_condition] = $ext_condition;
        }
        
        $tags = $places = $users = $place_cate = array();
        
        foreach($table as $k=>$row){
        	switch($k){
        		case "Tag" :
        			$tags = $this->db->select("id,content")->like("content",$keywords)->limit($num)->get($this->_tables["tag"])->result_array();
        			break;
        		case "Place" :
        			$places = $this->db->select("id,placename")->like("placename",$keywords)->limit($num)->get($this->_tables["place"])->result_array();
        			break;
        		case "User" :
        			$users = $this->db->select("id,username,nickname")->or_like("username",$keywords)->or_like("nickname",$keywords)->limit($num)->get($this->_tables["user"])->result_array();
        			break;
        		case "PlaceCategory" :
        			break;
        			$place_cate = $this->db->select("id,content")->like("content",$keywords)->limit($num)->get($this->_tables["placecategory"])->result_array();
        		default : break;
        	}
        }

    	$data['tags'] = $tags;
    	$data['places'] = $places;
    	$data['users'] = $users;
    	$data['placecate'] = $place_cate;
    	
    	
        $arr = array();
        foreach($data as $k=>$row) {
        	foreach($row as $line){
        		$arr[$k][] = $line;
        	}
        }
        $this->echo_json($arr);
    }
    
    function get_vest() {
    	
    	$aid = $this->auth['id'];
    	//$list = $this->db->select("id,bame")
    	$sql = "select u.username,u.id,u.nickname from ".$this->_tables['user']." u left join MorrisVest v on v.uid = u.id where v.aid=".$aid;
    	$query = $this->db->query($sql)->result_array();
    	
    	/*
    	 * <a onclick="$.bringBack({id:'4',name:'干锅', icon:'201206_13_095513675_59194.png'});" href="javascript:;">干锅</a>
    	 * */
    	
    	
        foreach($query as $k=>$row) {
        	echo "<a onclick=\"$.bringBack({id:'".$row['id']."',name:'".($row['nickname']?$row['nickname']:$row['username'])."'});\" href=\"javascript:;\">".($row['nickname']?$row['nickname']:$row['username'])."</a><br/>";
        }
        exit;
        //$this->echo_json($arr);
    }
    
    function get_news(){
    	$keywords = $this->get('keywords');
    	$num = $this->get('num');
    	$cid = $this->get('cid');
    	
    	
    	$cid && $this->db->where("newsCatid",$cid);
    	$keywords && $this->db->like('subject',$keywords);
    	$num && $this->db->limit($num);
    	
    	
    	$list = $this->db->get('WebNews')->result_array();
    	
    	$this->echo_json($list);
    }
    
    /**
     * 修改排序值
     */
    function rank() {
        $id = intval($this->get('id'));
        $table = trim($this->get('table'));
        $field = trim($this->get('field'));
        $order = intval($this->get('order'));
        
        $update_cache = trim($this->get('cache'));
        
        empty($field) && $field = 'rankOrder';
        
        if($id <= 0 && empty($table) && empty($field)) {
            $this->echo_json(array('status' => 1));
        }
        $data = array($field => $order);
        if($table == 'MorrisAdmin') {
            $data['lastQuotaDate'] = now(); 
        }
        
        $b = $this->db->where(array('id' => $id))->update($table, $data);
        $rtn = $b?array('status' => 0):array('status' => 1);
        
        if($table=="WebNewsCategory"){
        	update_cache("web","inc","newscategory",0);
            update_cache("web","inc","domains",0);
            get_data("newscategory",true);
        }
        
        if($update_cache){   
        	api_update_cache($table);
        }
        
        $this->echo_json($rtn);
    }
    
	function rank_by_where() {
        $where = trim($this->post('where'));
        $table = trim($this->post('table'));
        $field = trim($this->post('field'));
        $order = intval($this->post('order'));
        
        $update_cache = trim($this->post('cache'));
        
        empty($field) && $field = 'rankOrder';
        
        if(empty($where)&& empty($table) && empty($field)) {
            $this->echo_json(array('status' => 1));
        }
        $data = array($field => $order);
        if($table == 'MorrisAdmin') {
            $data['lastQuotaDate'] = now(); 
        }
        
        $b = $this->db->where($where,null,false)->update($table, $data);
        $rtn = $b?array('status' => 0):array('status' => 1);
        
                
        if($update_cache){   
        	api_update_cache($table);
        }
        
        $this->echo_json($rtn);
    }
    
    function editBoost(){
    	$table = trim($this->post('table'));
        $field = trim($this->post('field'));
        $boost = intval($this->post('boost'));

        $catid = intval($this->post('catid'));
        $postid = intval($this->post('postid'));
        $channelid = intval($this->post('channelid'));
        
        if(empty($table) || empty($field) || empty($catid) || empty($postid) || empty($channelid)) $this->echo_json(array('status' => 1));
        
        $set = array(
        	"{$field}" => $boost
        );
        $b = $this->db->where(array('catId'=>$catid,'postId'=>$postid,'channelId'=>$channelid))->update($table,$set);
    	
        $rtn = $b?array('status' => 0):array('status' => 1);
        
        $this->echo_json($rtn);
    }
    
    function ban_reply($type, $table, $id) {
        $this->ban_things($type, $table, $id);
    }
    
    function ban_things($type, $table, $id){
    	//屏蔽东西的东西。。
    	$format_tips = "你于%s发布的一条%s含有不适宜内容，可能会危害IN成都，已经被管理员删除了%s。";
    	$ids = $this->get('ids');
    	
    	if($id && !$ids){
	    	$info = $this->db->where("id",$id)->get($table)->row_array(0);
	    	if(empty($info)){
	    		$this->error("操作的数据不存在!");
	    	}
    	}
    	
    	$key = $type == 'post' ? "banned_tip" : "banned_reply" ;
    	
    	$point_case_conf = $this->config->item('point_case');
        $point_id = $point_case_conf[$key];
        $point_case = get_data('point_case',true);
        $case = $point_case[$point_id];
        
    	
        $point_string = $case['point'] <0 ? "扣除" : "奖励" ;
        $point_string .= abs($case['point']);
        
    	switch($type){
    		case 'post' : //屏蔽post 
    			$typename = "YY/点评/分享";
    			$score_setting = array(
    				array('value'=>0 ,'title'=>'不扣分', 'tip' => sprintf($format_tips,$info['createDate'],$typename,'') ),
    				array('value'=>abs($case['point']) ,'title'=>$point_string.'积分' , 'tip' => sprintf($format_tips,$info['createDate'],$typename,'，并且被扣除了50积分') )
    			);
    			break;
    		case 'reply' :
    			$typename = "回复";
    			$score_setting = array(
    				array('value'=>0 ,'title'=>'不扣分', 'tip' => sprintf($format_tips,$info['createDate'],$typename,'') ),
    				array('value'=>abs($case['point']) ,'title'=>$point_string.'积分' , 'tip' => sprintf($format_tips,$info['createDate'],$typename,'，并且被扣除了50积分') )
    			);
    			break;
    	}
    	
    	if($info){
    		$type = empty($info['type']) ?  100 : $info['type'];
    	}
    	
    	$this->assign(compact('score_setting','info','type','table','ids'));
    	$this->display("ban_things");
    }
}
