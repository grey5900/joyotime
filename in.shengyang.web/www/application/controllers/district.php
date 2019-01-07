<?php
/**
 * 商圈
 */

// Define and include
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

// Code
class District extends Controller {
	var $district_config; // 商圈配置
	var $district_id; // 当前商圈
	var $cur_district_conf; // 当权商圈配置
	var $device_code; // devicecode

	function __construct() {
		parent::__construct();
		
		$uid = intval($this->get('uid'));
		if ($uid > 0 && empty($this->auth)) {
			$this->auth['uid'] = $uid;
		}
		$this->load->helper('district');
		$this->load->config('config_district');
		
		// 获取传过来的devicecode
		$this->district_id = intval($this->get('district_id'));
		if (empty($this->district_id)) {
			// 空的话，从COOKIE获取
			$this->district_id = $_COOKIE['district_id'];
		} else {
			// 保存到COOKIE
			set_cookie("district_id", $this->district_id);
		}
		
		// 获取传过来的devicecode
		$this->device_code = str_replace(' ', '+', $this->get('devicecode'));
		if (empty($this->device_code)) {
			// 空的话，从COOKIE获取
			$this->device_code = $_COOKIE['device_code'];
		} else {
			// 保存到COOKIE
			set_cookie("device_code", $this->device_code);
		}
// 		var_dump($_SERVER['QUERY_STRING'], $this->device_code);
		// 对设备号。64交换
		$this->device_code = authcode(substr($this->device_code, 0, 4) . $this->device_code{6} . $this->device_code{5} .
		$this->device_code{4} . substr($this->device_code, 7), 'DECODE', 'nil');

		$this->district_config = $this->config->item('district');
		$this->cur_district_conf = $this->district_config[$this->district_id];
		
		if (empty($this->device_code) || empty($this->cur_district_conf)) {
			// 那么这里是非法进入
			header("location:inevents://0");
			exit;
		}

		$this->assign(array(
				'district_id' => $this->district_id,
				'name' => $this->cur_district_conf['name'],
				'brandname' => $this->cur_district_conf['brandname'],
				'conf' => $this->cur_district_conf,
				'uid' => $this->auth['uid']
		));
	}

	/**
	 * 活动页面
	 */
	public function event() {
		$this->assign('list', $this->e_list(1, 10, true));
		
		$this->display('district_event', 'i');
	}
	
	/**
	 * 活动列表
	 * @param number $page
	 * @param number $page_size
	 */
	public function e_list($page = 1, $page_size = 10, $is_return = false) {
		$page = intval($page);
		$page = $page>=0 ? $page : 1;
		$page_size = intval($page_size);
		$page_size = $page_size>0 ? $page_size : 10;
		
		$total_num = $this->db->from('RecommendData')
			->where_in('fid', $this->cur_district_conf['event_fids'])
			->count_all_results();
		$total_page = floor($total_num/$page_size) + (($total_num%$page_size)?1:0);	

		$page = $page > $total_page ? $total_page : $page;
		
		$list = $this->db->from('RecommendData')
			->select('title, titleLink, image')
			->order_by('serialNo', 'asc')
			->where_in('fid', $this->cur_district_conf['event_fids'])
			->limit($page_size, $page_size * ($page - 1))
			->get()->result_array();
		if ($is_return) {
			return $list;
		} else {
			$this->echo_json($list);
		}
	}

	/**
	 * 商圈首页
	 */
	public function index() {
		// 获取推荐的数据
		$list = $this->db->from('RecommendData')
		->order_by('serialNo', 'asc')
		->where_in('fid', $this->cur_district_conf['fids'])
		->get()->result_array();
		
		$slider = $recommend = array();
		foreach ($list as $row) {
			if ($row['fid'] == 30) {
				$recommend[] = $row;
			} else {
				$slider[] = $row;
			}
		}
		
		// 获取礼物
		if ($this->auth['uid']) {
			// 去获取礼物
			// 检查是否为会员
			$member = $this->db->query(sprintf("SELECT * FROM BrandMember WHERE uid='%s' 
					 AND brandId='%s'", $this->auth['uid'], $this->cur_district_conf['brandid']))->row_array();
			if($member) {
				// 是会员
				$gift = get_user_gift($this->auth['uid'],
						$this->cur_district_conf['gift_conf']);
			}
			
			// 获取用户的信息
			$user = $this->db->get_where('User', array('id' => $this->auth['uid']))->row_array();
			// 检查是否已经成为会员
			if (empty($member) && $user['cellphoneNo']) {
				$member = $this->db->get_where('BrandMember', 
						array('brandId' => $this->cur_district_conf['brandid'], 
								'cellphoneNo' => $user['cellphoneNo']))->row_array();
				if ($member) {
					// 那么把当前用户写入到这个对应的品牌里面去
					$this->db->where(array('brandId' => $this->cur_district_conf['brandid'], 
								'cellphoneNo' => $user['cellphoneNo']))->update('BrandMember', array(
										'uid' => $this->auth['uid']
										));
				}
			}
			if ($member) {
				// 检查是否已经参加了游戏了今天
				$date = idate_format(0, 'Y-m-d');
				$game_count = $this->db->query(sprintf("SELECT COUNT(*) AS num FROM LimitDevice
						WHERE itemType = '2'
						AND createDate BETWEEN '%s' AND '%s'
						AND (uid = '%s' OR deviceCode = '%s')",
						$date . ' 00:00:00', $date . ' 23:59:59', $member['uid'],
						$this->device_code))->row_array();
			}
		} else {
			$login_tip = true;
		}
		
		if (empty($member)) {
			$add_point = $this->_get_add_point();
		}
		// 清除cookie里面的showNav值
		$this->load->helper('cookie');
		delete_cookie('show_nav');
		$show_nav = 0;
		// 如果是ANDROID才处理
// 		if (stripos($this->device_code, 'android') !== false) {
// 			$show_nav = $this->get('showNav');
// 			if (empty($show_nav)) {
// 				$show_nav = 1;
// 				set_cookie('show_nav', $show_nav);
// 			} else {
// 				$show_nav = 0;
// 			}
// 		}
		
		$this->assign(array(
				'gift' => $gift,
				'recommend' => $recommend,
				'slider' => $slider,
				'login_tip' => $login_tip,
				'show_game' => $game_count['num'] >= $this->cur_district_conf['game_count']?false:true,
				'member' => $member?true:false,
				'add_point' => $add_point,
				'show_nav' => $show_nav
		));

		$this->display('district', 'i');
	}
	
	// 把道具放入包包
	public function item_bag() {
		// 获取礼物
		if ($this->auth['uid']) {
			// 去获取礼物
			// 检查是否为会员
			$row = $this->db->query(sprintf("SELECT * FROM BrandMember WHERE uid='%s'
					AND brandId='%s'", $this->auth['uid'], $this->cur_district_conf['brandid']))->row_array();
			if($row) {
				$gift = get_user_gift($this->auth['uid'],
						$this->cur_district_conf['gift_conf']);
				
				// 调用接口处理
				$rtn = request_api('/bag/add_item_to_bag', 'POST', 
						array('item_id' => $gift['id'], 'item_type' => 22),
						array('uid' => $this->auth['uid']));
				$rtn = json_decode($rtn, true);
				if ($rtn['result_code'] == 6016) {
					// 有错啦
					$this->echo_json(array('code' => 0, 'message' => '包包已满，现在去整理下包包吧'));
				} else {
					$this->echo_json(array('code' => 0, 'message' => '道具成功放入包包', 'rtn' => $rtn, 'id' => $gift['id']));
				}
			} else {
				$this->echo_json(array('code' => 0, 'message' => 
						sprintf('请先成为%s会员', $this->cur_district_conf['name'])));
			}
		} else {
			$this->echo_json(array('code' => 0, 'message' => '请先登陆IN沈阳'));
		}
	}
	
	public function detail() {
		$image = urldecode($this->get('image'));
		$title = urldecode($this->get('title'));
		
		// 检查image地地址
		if (strpos($image, 'in') === 0) {
			header('location:' . $image);
			exit;
		}
		
		$this->assign(array(
				'image' => $image,
				'mytitle' => $title,
				'show_nav' => $_COOKIE['show_nav']
				));
		
		$this->display('district_detail', 'i');
	}

	private function _get_add_point() {
		// 先检查是规则
		$point_arr = $this->cur_district_conf['gift_conf']['point'];
		$parr = array();
		$prekey = 0;
		foreach ($point_arr as $key => $value) {
			$xarr = range($prekey, $key);
			foreach ($xarr as $v) {
				$parr[$v] = $value;
			}
			$prekey = $key+1;
		}
// 		var_dump($parr);
		// 查询现在已经成为会员的数量
		$row = $this->db->query(sprintf("SELECT COUNT(*) AS num FROM
				UserPointLog WHERE pointCaseId = '%s'", $this->cur_district_conf['point_case_id']))->row_array();
		return $parr[$row['num']]; // 需要加的分数
	}
	
	/**
	 * 申请成为会员
	 */
	public function apply() {
		if (empty($this->auth['uid'])) {
			// 如果没有登陆
			if ($this->is_post()) {
				// 登陆提交
				$this->echo_json(array('code' => 1, 'message' => '请先登陆IN沈阳'));
				return;
			} else {
				// 进入页面，那么返回回去
				header(sprintf('location:/district?district_id=%s&uid=%s', $this->district_id, $this->auth['uid']));
				exit;
			}
		}
		if ($this->is_post()) {
			$name = trim($this->post('name'));
			$cellphone = trim($this->post('cellphone'));
			$idnumber = trim($this->post('idnumber'));
			
			if (empty($name) || empty($cellphone) || empty($idnumber)) {
				// 都是必须填写的
				$this->echo_json(array('code' => 1, 'message' => '请填写完整信息'));
				return;
			}
			
			// 验证手机号是否正确
			if (!check_cellphone($cellphone)) {
				$this->echo_json(array('code' => 1, 'message' => '请输入正确的手机号'));
				return;
			}
			
			// 验证身份证是否正确
			if (!check_idnumber($idnumber)) {
				$this->echo_json(array('code' => 1, 'message' => '请输入正确的身份证号'));
				return;
			}
			
			// 检查身份证号或手机号
			$row = $this->db->query(sprintf("SELECT * from BrandMember
					WHERE (cellphoneNo='%s' OR idNumber='%s') AND brandId='%s'",
					$cellphone, $idnumber, $this->cur_district_conf['brandid']))->row_array();
			if ($row) {
				// 已经帮顶过手机和身份证
				$this->echo_json(array('code' => 1,
						'message' => '您填写的手机号或身份证已经绑定过了'));
				return;
			}
			
			// 检查是否已经是会员
			$row = $this->db->query(sprintf("SELECT * from BrandMember 
					WHERE uid='%s' AND brandId='%s'", 
					$this->auth['uid'], $this->cur_district_conf['brandid']))->row_array();

			if ($row) {
				// 已经是会员了
				$this->echo_json(array('code' => 0, 
						'message' => sprintf('您已成为%s会员', $this->cur_district_conf['name'])));
				return;
			}
			
			// 写入到相关用户表中
			$b = $this->db->insert('BrandMember', array(
					'brandId' => $this->cur_district_conf['brandid'],
					'uid' => $this->auth['uid'],
					'cellphoneNo' => $cellphone,
					'name' => $name,
					'idNumber' => $idnumber
					));
			if ($b) {
				// 给用户加上积分
				$add_point = $this->_get_add_point();
				if ($add_point > 0) {
					// 检查用户和设备是否已经使用领过
					$row = $this->db->query(sprintf("SELECT * FROM UserPointLog
							WHERE pointCaseId = '%s'
							AND (uid = '%s' OR deviceCode = '%s') LIMIT 1",
							$this->cur_district_conf['point_case_id'], $this->auth['uid'], $this->device_code))
							->row_array();
					
					if (empty($row)) {
						// 加分给用户
						$this->db->where('id', $this->auth['uid'])
							->set('point','point+'.$add_point, false)
							->update('User');
						// 写入日志
						$this->db->insert('UserPointLog', array(
			                    'uid' => $this->auth['uid'],
			                    'pointCaseId' => $this->cur_district_conf['point_case_id'],
			                    'point' => $add_point,
			                    'operatorId' => 0,
								'deviceCode' => $this->device_code
			                ));
						// 给用户发送系统消息
						$this->db->insert('SystemMessage', array(
								'recieverId' => $this->auth['uid'],
								'content' => sprintf($this->cur_district_conf['sm_lang'], $add_point)
								));
						$msg_id = $this->db->insert_id();
						if($msg_id){
							//发送系统消息
							$this->load->helper('api');
							$rtn = call_api('msg_push_sys', array('sys_msg_id'=>$msg_id));
						}
					}
				}
				$this->echo_json(array('code' => 0, 'message' => sprintf('您已成为%s会员', $this->cur_district_conf['name']), 'rtn' => $rtn));
			} else {
				$this->echo_json(array('code' => 1, 'message' => '提交失败，请稍后重试'));
			}
			
			return;
		}
		
		// 获取用户的信息
		$user = $this->db->select('realName, cellphoneNo, idNumber')->get_where('User', array('id' => $this->auth['uid']))->row_array();
		
		$this->assign(array(
				'user' => $user,
				'show_nav' => $_COOKIE['show_nav']
				));
		
		$this->display('district_apply', 'i');
	}

	/**
	 * 会员卡商户
	 */
	public function place_list() {
		$lng = floatval($this->get('lng')); // 经度
		$lat = floatval($this->get('lat')); // 纬度
		
		if ($lng > 0 || $lng > 0) {
			$distance = sprintf(",f_distance(a.latitude, a.longitude, %s, %s) AS distance", $lat, $lng);
		}
		
		$sql = sprintf("SELECT a.id, a.placename, a.isRepayPoint, a.atCollectionCount,
				a.productCount, a.eventCount,
				a.`level`, c.icon as c_icon
				%s
				FROM Place a, PlaceOwnCategory b, PlaceCategory c, PlaceCollectionOwnPlace d
				WHERE a.status = 0 AND a.id = d.placeId AND d.pcId in ('%s') AND a.id = b.placeId
				AND b.placeCategoryId = c.id GROUP BY a.id
				ORDER BY %s ASC", 
				$distance, implode('\',\'', $this->cur_district_conf['pcid']), $distance?'distance':'a.id');
		
		$list = $this->db->query($sql)->result_array();
		
		foreach($list as &$row) {
			$row['star'] = intval($row['level']);
			if (isset($row['distance'])) {
				$row['show_distance'] = true;
				$row['distance'] = $row['distance']>2?(intval($row['distance']).'公里'):(intval($row['distance']*1000).'米');
			}
			$row['icon'] = $row['icon']?image_url($row['icon'], 'common'):image_url($row['c_icon'], 'common');
		}
		unset($row);
		
		$this->assign(array(
				'list' => $list,
				'show_nav' => $_COOKIE['show_nav']
				));
		
		$this->display('district_list', 'i');
	}
	
	/**
	 * 抢地主的地点列表
	 */
	public function rob_place_list() {
		$lng = floatval($this->get('lng')); // 经度
		$lat = floatval($this->get('lat')); // 纬度
		
		if ($lng > 0 || $lng > 0) {
			$distance = sprintf(",f_distance(a.latitude, a.longitude, %s, %s) AS distance", $lat, $lng);
		}
		
		$sql = sprintf("SELECT a.id, a.placename, a.point, a.robCount, a.mayorId
				%s
				FROM Place a, PlaceCollectionOwnPlace b
				WHERE a.status = 0 AND a.id = b.placeId AND b.pcId in ('%s')
				ORDER BY %s ASC",
				$distance, implode('\',\'', $this->cur_district_conf['pcid_rob']), $distance?'distance':'a.id');
		
		$list = $this->db->query($sql)->result_array();
		
		foreach($list as &$row) {
			if (isset($row['distance'])) {
				$row['show_distance'] = true;
				$row['distance'] = $row['distance']>2?(intval($row['distance']).'公里'):(intval($row['distance']*1000).'米');
			}
			// 获取用户信息 
			if ($row['mayorId']) {
				$user = get_data('user', $row['mayorId']);
				$row['username'] = $user['nickname']?$user['nickname']:$user['username'];
				$row['userlevel'] = $user['level'];
				$row['avatar_url'] = $user['avatar_url'];
			}
		}
		unset($row);
		
		$this->assign(array(
				'list' => $list,
				'show_nav' => $_COOKIE['show_nav']
				));
		
		$this->display('district_place_list', 'i');
	}

	/**
	 * 活动列表
	 */
	public function event_list() {
		// 获取商家的所有地点关联的活动
		$sql = sprintf("SELECT a.id, a.subject, a.image
				FROM WebEvent a, WebEventOwnPlace b,
				PlaceCollectionOwnPlace c
				WHERE a.id = b.eventId AND b.placeId = c.placeId
				AND a.`status` = 0
				AND c.pcId in ('%s') GROUP BY a.id ORDER BY a.startDate DESC",
				implode('\',\'', $this->cur_district_conf['pcid']));

		$list = $this->db->query($sql)->result_array();

		$this->assign(array(
				'list' => $list,
				'show_nav' => $_COOKIE['show_nav']
				));

		$this->display('district_event_list', 'i');
	}
	
	/**
	 * 翻翻乐
	 */
	public function game() {
		// 进入游戏，初始化3个得分的
		$cards = range(1, 9);
		$indexes = array_rand($cards, $this->cur_district_conf['game_count']);
		$points = array();
		foreach ($cards as $i) {
			if (in_array($i, $indexes)) {
				$points[$i] = rand($this->cur_district_conf['random_point'][0], $this->cur_district_conf['random_point'][1]);
			} else {
				$points[$i] = 0;
			} 
		}
		
		if ($this->auth['uid']) {
			$member = $this->db->get_where('BrandMember',
					array('brandId' => $this->cur_district_conf['brandid'],
							'uid' => $this->auth['uid']))->row_array();
		}
		if ($member) {
			// 如果是会员的话，那么次数需要累计
			// 检查是否已经参加了游戏了今天
			$date = idate_format(0, 'Y-m-d');
			$game_count = $this->db->query(sprintf("SELECT COUNT(*) AS num FROM LimitDevice
					WHERE itemType = '2'
					AND createDate BETWEEN '%s' AND '%s'
					AND (uid = '%s' OR deviceCode = '%s')",
					$date . ' 00:00:00', $date . ' 23:59:59', $member['uid'], $this->device_code))->row_array();
		} else {
			$game_count = 0;
		}
		// 把这个放入到cookie中
		set_cookie('game', authcode(json_encode(array('count' => $game_count['num'], 'point' => 0, 'points' => $points)), 'ENCODE'));
		
		// 随机获取广告图片
		$ad_cards = array_rand($this->cur_district_conf['game_card'], 9);
		
		$this->assign(array(
				'member' => $member?true:false,
				'ad_cards' => $ad_cards,
				'show_nav' => $_COOKIE['show_nav']
				));
		$this->display('district_game', 'i');
	}
	
	// 点击牌的时候
	public function click_card() {
		$click_index = intval($this->get('index')); // 点击的哪一个
		if ($click_index <= 0) {
			$this->echo_json(array('point' => 0, 'message' => "请点击", 'over' => "NO"));
			return;
		}
		// 检查是否为会员
		if ($this->auth['uid']) {
			$member = $this->db->get_where('BrandMember',
					array('brandId' => $this->cur_district_conf['brandid'],
							'uid' => $this->auth['uid']))->row_array();
		}
		$game = json_decode(authcode($_COOKIE['game']), true);
		if ($member) {
			// 检查是否已经参加了游戏了今天
			$date = idate_format(0, 'Y-m-d');
			$game_count = $this->db->query(sprintf("SELECT COUNT(*) AS num FROM LimitDevice
					WHERE itemType = '2'
					AND createDate BETWEEN '%s' AND '%s'
					AND (uid = '%s' OR deviceCode = '%s')",
					$date . ' 00:00:00', $date . ' 23:59:59', $member['uid'], $this->device_code))->row_array();
			$game['count'] = $game_count['num'];
		}
		
		$over = false;
		if ($game['count'] < $this->cur_district_conf['game_count']) {
			if ($member) {
				// 写入限制表
				// 写入限制设备表
				$this->db->insert('LimitDevice', array(
						'itemType' => 2,
						'itemId' => rand(100, 999) . TIMESTAMP . $this->auth['uid'],
						'uid' => $this->auth['uid'],
						'deviceCode' => $this->device_code
				));
			}
			// 去判断这个这个是否有积分
			if($game['points'][$click_index] > 0) {
				// 有积分
				if($member) {
					// 需要加积分
					// 加分给用户
					$this->db->where('id', $member['uid'])
						->set('point','point+'.$game['points'][$click_index], false)
						->update('User');
					// 写入日志
					$this->db->insert('UserPointLog', array(
							'uid' => $this->auth['uid'],
							'pointCaseId' => $this->cur_district_conf['game_point_id'],
							'point' => $game['points'][$click_index],
							'operatorId' => 0,
							'deviceCode' => $this->device_code
					));
					$message = sprintf('恭喜您获得%s积分', $game['points'][$click_index]);
				} else {
					// 不需要加积分
					$message = sprintf('发现了%s积分', $game['points'][$click_index]);
				}
				$game['point'] += $game['points'][$click_index];
			}
		} else {
			$over = true;
		}
		$game['count'] += 1;
		if ($game['count'] >= $this->cur_district_conf['game_count']) {
			$over = true;
		}
		
		set_cookie('game', authcode(json_encode($game), 'ENCODE'));
		
		$this->echo_json(array('point' => $game['points'][$click_index], 'member' => $member?'1':'0',  'total_point' => $game['point'], 'message' => $message, 'over' => $over?"YES":"NO"));
	}
	
	// 特权页面
	public function privilege() {
		$this->assign('show_nav', $_COOKIE['show_nav']);
		$this->display('district_privilege', 'i');
	}
	
	// 章程
	public function rules() {
		$this->assign('show_nav', $_COOKIE['show_nav']);
		$this->display('district_rules', 'i');
	}
}