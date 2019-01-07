<?php
/**
 * 华侨城接口
 */

if (!defined('BASEPATH'))
	exit('No direct script access allowed');


class Hqc extends Controller {
	var $district_config; // 商圈配置
	var $district_id = 1; // 当前商圈
	var $cur_district_conf; // 当权商圈配置
	var $brand_id; // 商圈的品牌ID号
	
	function __construct() {
		parent::__construct();
	
		$this->load->helper('district');
		$this->load->config('config_district');
		
		$this->district_config = $this->config->item('district');
		$this->cur_district_conf = $this->district_config[$this->district_id];
		$this->brand_id = $this->cur_district_conf['brandid'];
		
		// 权限检查
		// 规则 sign = md5(md5(t) . md5(encrypt_key)) 
		$sign = $this->get('sign');
		$t = $this->get('t');
// 		var_dump($sign, $t, $this->cur_district_conf);
		if (md5(md5($t) . md5($this->cur_district_conf['encrypt_key'])) != $sign) {
			// 错误的请求
			$this->echo_json(array('code' => 1, 'data' => '请求不安全'));
		}
	}
	
	/**
	 * 接口返回，最新的会员
	 * @methd GET
	 * 返回 status=> 0：未同步 type=>0：IN沈阳会员
	 * @return {"code":0, "data":{"name":"", "cellphoneNo":"", "idNumber":"", "createDate":""}}
	 * 失败：code != 0   data：错误信息
	 * @param $status 0：返回没有同步的数据 1：返回已同步，但没有回填华侨城会员ID的记录
	 */
	public function get_member($status = 0) {
		if ($status) {
			$sql = sprintf("SELECT name, cellphoneNo, idNumber, createDate FROM BrandMember
					WHERE status = 1 AND brandId='%s' AND brandMemberId = 0 ORDER BY createDate LIMIT 1", $this->brand_id);
		} else {
			$sql = sprintf("SELECT name, cellphoneNo, idNumber, createDate FROM BrandMember
					WHERE status = 0 AND brandId='%s' ORDER BY createDate LIMIT 1", $this->brand_id);
		}
		
		$member = $this->db->query($sql)->row_array();
		$b = true;
		if($member && empty($status)) {
			// 修改状态
			$b = $this->db->where(array('brandId' => $this->brand_id,
						'cellphoneNo' => $member['cellphoneNo']))->update('BrandMember', array('status' => 1));
		}
		
		$this->echo_json($b?array('code' => 0, 'data' => $member?$member:array()):array('code' => 1, 'data' => '更新状态失败，请重试'));
	}
	
	/**
	 * 接口增加新的会员
	 * @methd POST
	 * @param {"cellphoneNo": "", "memberId": ""}
	 * @return {"code":0, data:""}
	 */
	public function set_member() {
		$cellphone_no = trim($this->post('cellphoneNo'));
		$member_id = trim($this->post('memberId'));
		
		if(empty($cellphone_no) || empty($member_id)) {
			$this->echo_json(array('code' => 2, 'data' => '提交数据不正确'));
		}
		
		// 检查是否存在该会员，已经存在那么update，否则insert
		$member = $this->db->get_where('BrandMember',
				array('brandId' => $this->brand_id,
						'cellphoneNo' => $cellphone_no))->row_array();
		if (empty($member)) {
			// 不存在该会员
			$b = $this->db->where(array('brandId' => $this->brand_id,
						'cellphoneNo' => $cellphone_no))->update('BrandMember', array('brandMemberId' => $member_id, 'status' => 1));
		} else {
			$b = $this->db->insert('BrandMember', array(
					'brandId' => $this->brand_id,
					'uid' => 0,
					'cellphoneNo' => $cellphone_no,
					'name' => '',
					'idNumber' => '',
					'brandMemberId' => $member_id,
					'type' => 1,
					'status' => 1
					));
		}
		
		$this->echo_json($b?array('code' => 0, 'data' => '成功'):array('code' => 1, 'data' => '失败'));
	}
}