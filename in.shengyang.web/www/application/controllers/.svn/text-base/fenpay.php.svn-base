<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 分贝
 * @Author: chenglin.zhu@gmail.com
 * @Date: 2013-3-20
 */

class Fenpay extends Controller {
	function __construct() {
		parent::__construct();
		header('Content-type:text/html; charset=utf-8');
	}
	/**
	 * 积分票测试
	 * @param 地点ID $place_id
	 * @param 积分 $point
	 * @param 过期时间 20130228235959 $expire_date
	 */
	function index($place_id = 0, $point = 0, $expire_date = '') {
		$place_id = intval($place_id);
		$point = intval($point);
		$expire_date = trim($expire_date);

		($place_id <= 0 || $point <= 0 || empty($expire_date)) && die('参数错误');

		// 判断时间是否过期
		$expire_time = strtotime($expire_date);
		if(TIMESTAMP > $expire_time) {
			die('过期时间错误');
		}

		// 查询是否有这个地点
		$place = $this->db->get_where($this->_tables['place'], array('id' => $place_id))->row_array();
		empty($place) && die('地点错误');
		$code = $this->_get_code();
		$data = array(
				'code' => $code,
				'point' => $point,
				'placeId' => $place_id,
				'expireDate' => gmdate('Y-m-d H:i:s', $expire_date + 3600*8),
				'listId' => 0,
				'status' => 0
		);
		$this->db->insert($this->_tables['pointticket'], $data);
		$id = $this->db->insert_id();

		exit(sprintf('http://%s/qr/inpt/%s?code=%s', 'in.jin95.com', $id, $code));
	}

	/**
	 * 验证
	 * @param 传入code $code
	 * @param 是否使用 $use 1：使用 其他不使用
	 */
	function verify($code, $use = 0) {
		$code = trim($code);
		$use = intval($use);
		
		if (empty($code)) {
			$this->echo_json(array('status' => 1, 'message' => '请传入正确的code'));
			return;
		}

		$data = $this->db->query(sprintf("SELECT a.*, b.productName
				FROM OrderOwnTradeCode a, OrderInfo b
				WHERE a.orderId = b.id AND a.code = '%s'", $code))->row_array();
		if (empty($data)) {
			$this->echo_json(array('status' => 1, 'message' => '无效的code'));
			return;
		}

		// 去获取关联的地点
		$place = $this->db->query(sprintf("SELECT placename FROM
				ProductAtPlace a, Place b
				WHERE a.placeId = b.id AND a.productId = '%s'
				ORDER BY rand() LIMIT 1", $data['productId']))->row_array();
		
		if ($use === 1) {
			if ($data['status'] == 1) {
				$this->echo_json(array('status' => 1, 'message' => 'code已使用'));
				return;
			} else if ($data['status'] == 2) {
				$this->echo_json(array('status' => 1, 'message' => 'code已过期'));
				return;
			}
			$exchange_time = idate_format(TIMESTAMP, 'Y-m-d H:i:s');
			// 使用
			$this->db->where('code', $code)
					->update('OrderOwnTradeCode', 
							array('status' => 1, 'exchangeDate' => $exchange_time));
		}
		
		$this->echo_json(array('status' => 0, 'message' => '', 'data' => array(
				'productname' => $data['productName'],
				'placename' => $place['placename'],
				'serialnumber' => (rand(1000, 9999)).TIMESTAMP,
				'exchangetime' => $exchange_time?$exchange_time:$data['exchangeDate']
				)));
	}

	/**
	 * 获取code
	 */
	private function _get_code() {
		return dechex(rand(256, 4095)) . uniqid();
	}

}

