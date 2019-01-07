<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
  * 提供客户端的一些访问
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-3-11
  */

define('M_HOST', 'm.' . HOST);

class Mobile_Site extends Controller {
	function __construct() {
		parent::__construct();
		$this->load->helper('mobilesite');
		$this->load->config('config_mobilesite');
	}
    
    function signin(){
    	$referer = urldecode(trim($this->get('referer')));
    	if($referer) {
    		$this->assign('referer', $referer);
    	}
    	
    	$passport_config = $this->config->item('passport');
    	get_salt();
    	$this->assign('passport_signin_url', $passport_config['signin_url']);
    	$this->assign('passport_sso_url', $passport_config['sso_url']);
    	$this->assign('passport_signup_url', $passport_config['signup_url']);
    	$this->assign('passport_taboo_url', $passport_config['taboo_url']);
        $this->display('/signin');
    }
    
    function signup(){
    	$referer = urldecode(trim($this->get('referer')));
    	if($referer) {
    		$this->assign('referer', $referer);
    	}
    	
    	$passport_config = $this->config->item('passport');
    	get_salt();
    	$this->assign('passport_signin_url', $passport_config['signin_url']);
    	$this->assign('passport_sso_url', $passport_config['sso_url']);
    	$this->assign('passport_signup_url', $passport_config['signup_url']);
    	$this->assign('passport_taboo_url', $passport_config['taboo_url']);
        $this->display('/signup');
    }
    
    function signup_success(){
        $this->display('/signup_success');
    }
    
    function conversion(){
        $this->display('/conversion');
    }
    
    function conversion_success(){
    	$p = intval($this->get('point'));
    	if($p) {
    		$type = 'point';
    		$item = serialize(array('point' => $p));
    		$show_detail = true;
    	} else {
    		$type = $_COOKIE['gift_type'];
    		$item = $_COOKIE['gift_item'];
    		$show_detail = $_COOKIE['show_detail'];
    	}
    	
    	delete_cookie('gift_type');
    	delete_cookie('gift_item');
    	delete_cookie('show_detail');
    	delete_cookie('auth');
    	 
    	switch($type) {
    		case 'story':
    			$this->assign('story', unserialize($item));
    			break;
    		case 'point':
    			$this->assign('point', unserialize($item));
    			break;
    		case 'item':
    			$this->assign('item', unserialize($item));
    			break;
    	}
    	$this->assign('show_detail', $show_detail == 'false'?false:true);
        $this->display('/conversion_success');
    }
    
    // 领取积分
    function pull_point() {
    	$code = $this->get('code');
    	$result = request_api('/point_ticket/validate', 'POST', array('code' => $code), array('uid' => $this->auth['uid']), false);
    	$callback = $this->get('callback');
    	die($callback . '(' . $result . ')');
    }
    
    // 幸运的活动
    function lucky() {
    	$d = trim($this->get('id')); // 加密的id
    	$t = intval($this->get('t')); // 用于解密的参数
    	$id =id_decode($d, $t);
    	
    	// 去查询是否有活动
    	$lucky = $this->db->query("SELECT * FROM WebLucky WHERE id='{$id}'")->row_array();
    	if(empty($lucky)) {
    		$this->auth['uid']?$this->message("访问的活动不存在"):redirect(sprintf('http://%s/signin', M_HOST));
    	}
    	
    	$this->assign('gift', array('name' => $lucky['name'], 'url' => '/gift?id='.$d.'&t='.$t));
    	// 检查用户是否登陆
    	if ($this->auth['uid']) {
    		$start_time = strtotime($lucky['startTime']);
    		// 检查是否互动已经结束
    		if(TIMESTAMP < $start_time) {
    			// 还没有开始
    			$this->message('活动还没有开始');
    		}
    		 
    		$end_time = strtotime($lucky['endTime']);
    		if(TIMESTAMP > $end_time) {
    			// 已结束
    			$this->message('活动已经结束');
    		}
    		
    		// 点击奖品页面
    		$this->gift();
    	} else {
    		$this->assign('referer', 'http://' . M_HOST . '/lucky?' . $_SERVER['QUERY_STRING']);
    		
    		// 取出规则，随机一个故事
    		$rules = decode_json($lucky['rules']);
    		$this->assign('story', rand_array($rules['story']));
    		
    		$this->display('/lucky_story');
    	}
    }
    
    /**
     * 奖品
     */
    function gift() {
    	$d = trim($this->get('id')); // 加密的id
    	$t = intval($this->get('t')); // 用于解密的参数
    	$id =id_decode($d, $t);
    	
    	// 去查询是否有活动
    	$lucky = $this->db->query("SELECT * FROM WebLucky WHERE id='{$id}'")->row_array();
    	if(empty($lucky)) {
    		$this->auth['uid']?$this->message("访问的活动不存在"):redirect(sprintf('http://%s/signin', M_HOST));
    	}
    	
    	if(empty($this->auth['uid'])) {
    		// 登陆失效或则没有登陆
    		// 那么过去登陆吧
    		redirect(sprintf('http://%s/signin?referer=%s', M_HOST, sprintf('http://%s/lucky?id=%s&t=%s', M_HOST, $d, $t)));
    	}
    	
    	$start_time = strtotime($lucky['startTime']);
    	// 检查是否互动已经结束
    	if(TIMESTAMP < $start_time) {
    		// 还没有开始
    		$this->message('活动还没有开始');
    	}
    	
    	$end_time = strtotime($lucky['endTime']);
    	if(TIMESTAMP > $end_time) {
    		// 已结束
    		$this->message('活动已经结束');
    	}
    	
    	// 那么去计算中奖信息
    	$rules = decode_json($lucky['rules']);
    	// 计算所有奖品的数量
    	$gift_num = 0;
    	foreach($rules as $key => $value) {
    		if ($key == 'story') {
    			continue;
    		}
    		
    		foreach($value as $v) {
    			$gift_num += $v['limit'];
    		}
    	}
    	$present = false;
    	// 统计已经发放数量
    	$present_num = $this->db->where(array('luckyId' => $id))->count_all_results('WebLuckyLog');
    	$today_date = idate_format(TIMESTAMP, 'Y-m-d');
    	// 统计用户今天是否中奖
    	$user_today_gift_num = $this->db->where(sprintf("dateline BETWEEN '%s 00:00:00'
    			AND '%s 23:59:59'", $today_date, $today_date), null, false)
    			->where(array('luckyId' => $id, 'uid' => $this->auth['uid']))->count_all_results('WebLuckyLog');
    	if($user_today_gift_num < $this->config->item('gift_num_everyday') && $present_num < $gift_num && wine_gift()) {
    		// 中奖了，那么去看分配那个奖品
    		$d1 = idate_format($start_time, 'Y-m-d');
    		$d2 = idate_format($end_time, 'Y-m-d');
    		// 计算今天的奖品数
    		if($today_date != $d2) {
    			$d1 = new DateTime($d1);
    			$d2 = new DateTime($d2);
    			$days = $d1->diff($d2)->d + 1;
    			$today_num = $gift_num/$days;
    			
    			// 看今天的奖品是否已经发出去了
    			$today_present_num = $this->db->where(sprintf("dateline BETWEEN '%s 00:00:00' 
    					AND '%s 23:59:59'", $today_date, $today_date), null, false)
    				->where(array('luckyId' => $id))->count_all_results('WebLuckyLog');
    			if($today_present_num < $today_num) {
    				// 随即一个礼物
    				$present = true;
    			}
    		} else {
    			// 最后一天了
    			$present = true;
    		}
    	}
    	
    	if($present) {
    		// 去随机一个礼物
    		$key = array('item', 'point');
    		shuffle($key);
    		foreach($key as $k) {
    			$rule = $rules[$k];
    			shuffle($rule);
    			foreach($rule as $v) {
    				// 检查这个礼品还有没有
    				$cur_present_num = $this->db->where(array('luckyId' => $id, 'type' => $k, 'typeId' => $v['id']))
    					->count_all_results('WebLuckyLog');
    				if($cur_present_num >= $v['limit']) {
    					continue;
    				} else {
    					// 那么分配这个礼物
    					$gift_b = false;
    					switch($k) {
    						case 'point':
    							// 积分
    							$this->load->helper('user');
    							if(1 == change_point($this->auth['uid'], $v['id'], $v['point'])) {
    								// 成功
    								$gift_b = true;
    								
    								$point = array(
    										'point' => $v['point']
    										);
    							}
    							break;
    						case 'item':
    							// 道具
    							// 调用派发道具
    							$rtn = json_decode(request_api('/props/present', 'POST',
    									array(
    									'id' => $v['id'],
    									'uid' => $this->auth['uid']
    									), array('uid' => 56)), true);
    							
    							if(empty($rtn['result_code'])) {
    								// 配发成功
    								$gift_b = true;
    								$item = $this->db->where(array('id' => $v['id']))
    									->select('name, description, notice, image')->get('Item')->row_array();
    								$image_conf = get_data('imagesetting');
    								$item['image'] = sprintf('%s/common/odp/%s',
    										$image_conf['image_base_uri'], $item['image']);
    							}
    							break;
    					}
    					
    					if ($gift_b) {
    						// 写入日志
    						$log_data = array(
    								'luckyId' => $id,
    								'type' => $k,
    								'typeId' => $v['id'],
    								'uid' => $this->auth['uid']
    						);
    						$this->db->insert('WebLuckyLog', $log_data);
    						goto OK;
    					}
    				}
    			}
    		}
    	}
    	
    	OK:
    	if($point) {
    		$type = 'point';
    		$str = serialize($point);
    	} elseif($item) {
    		$type = 'item';
    		$str = serialize($item);
    	} else {
    		$type = 'story';
    		$story = array('content' => rand_array($rules['story']));
    		$str = serialize($story);
    	}
    	set_cookie('gift_type', $type);
    	set_cookie('gift_item', $str);
    	set_cookie('show_detail', 'false'); // 是否显示具体的内容
    	
    	redirect('http://'. M_HOST . '/conversion_success/');
    }
    
    function ooxx($id) {
    	$t = strlen($id);
    	echo sprintf('http://%s/lucky?id=%s&t=%s', M_HOST, id_encode($id, $t), $t), '<br/>', $_SERVER['HTTP_HOST'];
    }
    
    /**
     * 提示信息
     */
    function message($msg = '', $url = '') {
    	$this->assign(array('msg' => urldecode($msg), 'url' =>urldecode($url)));
    	$this->display('message');
    }
}