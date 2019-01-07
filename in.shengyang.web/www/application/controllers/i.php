<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
  * 提供客户端的一些访问
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-11
  */

class I extends Controller {
	function __construct() {
		parent::__construct();
		
		$uid = intval($this->get('uid'));
		if ($uid > 0 && empty($this->auth)) {
			$this->auth['uid'] = $uid;
		}
	}
    /**
     * 用户引导
     */
    function guide() {
        $uid = $this->auth['uid'];
    
        // 获取用户任务完程度
        $this->load->model('userpointlog_model', 'm_userpointlog');
        $task_list = $this->m_userpointlog->list_user_task($uid);
    
        $this->assign(compact('task_list','uid'));
    
        $this->display('guide');
    }
    
	function g_1() {
        $uid = $this->auth['uid'];
    
        // 获取用户任务完程度
        $this->load->model('userpointlog_model', 'm_userpointlog');
        $task_list = $this->m_userpointlog->list_user_task($uid);
    
        $this->assign(compact('task_list','uid'));
    
        $this->display('g_1');
    }
    function g_2() {
    	$uid = $this->auth['uid'];
    	
    	$user = $this->db->where('id',$uid)->get($this->_tables['user'])->row_array(0);
    	
    	$this->assign(compact('uid','user'));
        $this->display('g_2');
    }
	function g_3() {
		$uid = $this->auth['uid'];
    	$this->assign(compact('uid'));
        $this->display('g_3');
    }
	function g_4() {
		$uid = $this->auth['uid'];
    	$this->assign(compact('uid'));
        $this->display('g_4');
    }
	function g_5() {
		$uid = $this->auth['uid'];
    	$this->assign(compact('uid'));
        $this->display('g_5');
    }
	function g_6() {
		$uid = $this->auth['uid'];
    	$this->assign(compact('uid'));
        $this->display('g_6');
    }
    function g_7() {
    	$uid = $this->auth['uid'];
    	$this->assign(compact('uid'));
        $this->display('g_7');
    }
	function google_map() {
        $this->display('google_map');
    }
	function district() {
        $this->display('district');
    }
	function district_apply() {
        $this->display('district_apply');
    }
	function district_list() {
        $this->display('district_list');
    }
	function district_privilege() {
        $this->display('district_privilege');
    }
	function district_game() {
        $this->display('district_game');
    }
	function district_download(){
		$this->display('district_download');
	}
	function district_place_list(){
		$this->display('district_place_list');
	}
	function app_list(){
		$this->display('app_list');
	}
	
    /**
     * 电子报
     * @param int $id 电子报的ID号
     */
    function enewspaper($id = 0) {
        $id = intval($id);
        if($id < 0) {
            die('错误');
        }
        $this->load->model('enewspaper_model', 'm_enewspaper');
        // 获取当前
        $paper = $this->m_enewspaper->find_enewspaper($id);
    
        if($paper) {
            // 获取前一条
            $prev = $this->m_enewspaper->find_prev_enewspaper($paper['publishDate']);
    
            // 获取后一条
            $next = $this->m_enewspaper->find_next_enewspaper($paper['publishDate']);
    
            $content = json_decode($paper['content'], true);
    
            $this->assign(compact('content', 'paper', 'prev', 'next'));
        }
    
        $this->display('enewspaper');
    }
    
    /**
     * 显示商品
     * @param int $id
     */
    function product($id) {
        $id = intval($id);
        ($id <= 0) && die('');
    
        $this->load->model('product_model', 'm_product');
        $product = $this->m_product->select_by_id($id);
    
        $this->assign(array('title' => $product['name'], 'content' => $product['introduce']));
        $this->display('product');
    }
    
    /**
     * 免责声明
     */
    function disclaimer() {
        $this->display('disclaimer');
    }
    
    /**
     * faq
     */
    function faq() {
        $this->display('faq');
    }
    /**
     * club
     */
    function club() {
        $this->display('club');
    }
    
    /**
     * 地点扩展模型
     * @param 地点ID $id
     * @param 模型ID $mid
     */
    function place($id = 0, $mid = 0) {
        // 地点ID号
        $id = intval($id);
        $mid = intval($mid);

        ($id <= 0) && die('');

        // 地点信息
        $place = $this->db->get_where('Place', array('id' => $id))->row_array();
        $this->assign('title', $place['placename']);

        $place || die('');
        
        if(empty($mid)) {
            // 获取地点的第一个模型，兼容之前的版本
            $row = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceOwnModule', array('placeId' => $id))->row_array();
            $mid = $row['placeModuleId'];
        }
        
        if($mid > 0) {
            // 去获取碎片
            $block = $this->db->get_where('PlaceOwnSpecialProperty', array('placeId' => $id, 'moduleId' => $mid))->row_array();
            
            $block['title'] && $this->assign('title', $block['title']);
            
            // 去获取模型字段信息
            $rows = $this->db->get_where('PlaceModuleField', array('moduleId' => $mid))->result_array();
            $fields = array();
            foreach($rows as $row) {
                $fields[$row['fieldId']] = $row;
            }
            unset($row, $rows);
            
            // 地点扩展信息
            $ext_info = $this->db->get_where('PlaceModuleData', array(
                    'placeId' => $id,
                    'isVisible' => 1,
                    'moduleId' => $mid
            ))->result_array();
    
            $result = array();
            if ($ext_info) {
                foreach ($ext_info as $row) {
                    if($fields[$row['fieldId']]['fieldType'] == 'rich_image') {
                        // 对rich_image里面的元素排序
                        $m_value = json_decode($row['mValue'], true);
                        $rich_image_fields = $this->config->item('rich_image');
                        $value = $sort_field = array();
                        if($m_value) {
                            foreach($rich_image_fields as $k => $val) {
                                $sort_field[$k] = $val['key'];
                            }
                            foreach($m_value as $val) {
                                $arr = array();
                                foreach($sort_field as $k => $v) {
                                    $arr[$v] = $val[$v];
                                }
                                $value[] = $arr;
                            }
                        }
                        $result[$row['fieldId']] = $value;
                    } else {
                        $result[$row['fieldId']] = $row['mValue'];
                    }
                }
                unset($ext_info, $fields);
                
                $this->assign('value', $result);
            }
            $this->display('module_' . $mid, 'i_place');
        }
    }
    
	public function anniversary(){
    	$uid = $this->auth['uid'];
    	$user = get_data('user',$uid);
    	
    	$deviceCode = $user['deviceCode'];
    	$this->assign(compact('uid','deviceCode'));
    	$this->display('page_template/anniversary');
    }
	public function zztl(){
    	$uid = $this->auth['uid'];
    	$user = get_data('user',$uid);
    	
    	$deviceCode = $user['deviceCode'];
    	$start = strtotime("2013-07-12 14:30:00");
    	$end = strtotime("2022-07-15 16:30:00");
    	
    	$over = xcache_get("zztl_prize_runout");
    	$status = "尚未开始";
    	if(TIMESTAMP >= $start && TIMESTAMP <= $end){
    		$status = "立即兑换";
    	}
    	if(TIMESTAMP > $end || $over){
    		$status = "兑换已结束";
    	}
		
    	$this->assign(compact('uid','deviceCode','status','over'));
    	$this->display('page_template/zztl');
    }
    
    /**
     * 找回密码
     * Create by 2012-12-30
     * @author liuweijava
     * @param int $step
     * @param string $code
     */
    public function reset_pwd($step=0, $code=''){
        $this->load->model('user_model', 'm_user');
        $this->load->model('userresetpwd_model', 'm_resetpwd');
        
        $this->assign('step', $step);
        if($step == 1){//重置密码
            if($this->is_post()){
                $rp_id = $this->input->post('rp_id');
                $email = $this->input->post('email');
                $password = $this->input->post('newpwd');
                $repwd = $this->input->post('repwd');
                $code = $this->post('code');
    			
                //检查CODE是否过期
                $rs = $this->m_resetpwd->select(array('secretCode'=>$code, 'isUse'=>0));
                if(empty($rs) || $rs['email'] != $email){
                	$this->echo_json(array('code'=>1, 'msg'=>'提交的重置已过期或错误的访问'));
                	return;
                }
                
                //检查密码长度
                if(cstrlen($password) < 2 || cstrlen($password) > 15)
                    $this->echo_json(array('code'=>1, 'msg'=>lang_message('signup_password_len_fail')));
                elseif($password !== $repwd)
                $this->echo_json(array('code'=>2, 'msg'=>lang_message('signup_password_fail')));
                else{
                    $code = $this->m_user->reset_pwd($email, strtoupper(md5($password)));
                    switch($code){
                        case 1:$msg = '邮箱未关联到账户，重置密码失败了';break;
                        default:
                            //更新请求状态
                            $this->m_resetpwd->used_reset($rp_id);
                            $msg = lang_message('reset_pwd_suc');
                            break;
                    }
                    $this->echo_json(compact('code', 'msg'));
                }
            }else{
                //检查CODE是否过期
                $rs = $this->m_resetpwd->select(array('secretCode'=>$code, 'isUse'=>0));
                if(empty($rs)){
                    $this->assign('err', 1);
                    $this->assign('msg', '连接已过期或失效，请重新申请重置密码');
                }else{
                    $now = time();
                    $create = strtotime($rs['applyDate']);
                    $minus = abs($create-$now);
                    if($minus >= 24*3600){
                        $this->assign('err', 1);
                        $this->assign('msg', lang_message('reset_timeout'));
                    }else{
                        $this->assign('email', $rs['email']);
                        $this->assign('rpid', $rs['id']);
                    }
                }
                $this->assign('code', $code);
                $this->display('reset_pwd');
            }
        }else{//发邮件
            if($this->is_post()){
                $email = $this->post('email');
                if(empty($email))
                    $this->echo_json(array('code'=>1, 'msg'=>'请填写您注册的邮箱地址'));
                else{
                    //检查邮箱是否合法
                    $c = $this->m_user->select(array('email'=>$email));
                    if(empty($c))
                        $this->echo_json(array('code'=>2, 'msg'=>'该邮箱未被注册过'));
                    else{
                        //邮件链接
                        $query_string = strtoupper(md5('email:'.$email.'-'.microtime(true)));
                        $data = array(
                                'email' => $email,
                                'secretCode' => $query_string,
                                'applyDate' => date('Y-m-d H:i:s'),
                                'isUse' => 0
                        );
                        //检查是否有过找回密码的请求
                        $r = $this->m_resetpwd->select(array('email'=>$email));
                        if($r){//编辑老数据
                            $this->m_resetpwd->update($data);
                        }else{
                            $this->m_resetpwd->insert($data);
                        }
                        //邮件
                        $link = base_url() . 'reset_pwd_1/'.$query_string;
                        $replies = array('reset_url'=>$link);
                        $content = lang_message('reset_mail_content', array($link, $link));
                        $emails = array(
                                array('mail'=>$email)
                        );
                        $this->load->library('mail');
                        //发邮件
                        $rs = $this->mail->send($emails, lang_message('reset_mail_subject'), $content);
                        if($rs['code'] == 1)
                            $this->echo_json(array('code'=>0, 'msg'=>lang_message('reset_mail_send_suc')));
                        else
                            $this->echo_json(array('code'=>3, 'msg'=>lang_message('reset_mail_send_err')));
                    }
                }
            }else{
                $this->display('reset_pwd');
            }
        }
    }
    
    
    function wmfile($page) {
    	$page = abs(intval($page));
    	
    	$this->assign(array(
    			'prev' => $page > 1 ?$page - 1:1,
    			'next' => $page + 1 < 40 ? $page + 1 : 40,
    			'page' => $page
    			));
    	
    	$this->display($page, 'i/page_template/wmfile');
    }
}