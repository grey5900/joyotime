<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * API
 * @author piggy
 * @date 2013-08-03
 */

use Swagger\Annotations as SWG;

/**
 * @SWG\Resource(
 *     apiVersion="0.1",
 *     resourcePath="api"
 * )
 */

class Api extends MY_Controller {
	function __construct() {
		parent::__construct();
	}

	/**
	 * @SWG\Api(
	 * 		path="/index",
	 *		description="API首页，可以用来测试API是否连接成功",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="name",
	 * 						description="名称",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="world"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function index($name = '') {
		$name = $name?$name:trim($this->get('name'));
		if ($name === '') {
			$this->http_404();
		} else {
			$this->http_200('hello ' . $name);
		}
	}

	/**
	 * @SWG\Api(
	 * 		path="/get_config",
	 *		description="获取配置信息，如果传入的时间戳小于配置最后修改时间，那么不更新返回304，否则返回结果",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="t",
	 * 						description="时间戳",
	 * 						paramType="query",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="dt",
	 * 						description="测试使用 设备类型 0：IOS 1：ANDROID",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="av",
	 * 						description="测试使用 客户端版本号 如： 0.1",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="0.1"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cel",
	 * 						description="测试使用 手机号",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="304",
	 * 						reason="Not Modified"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function get_config($t = 0) {
		$t = $t?$t:$this->get('t', 0);
		$setting = get_inc('setting');

		if(empty($setting)) {
			$this->http_500('msg_get_config_5001');
		}

		$is_new = false;
		$data = array();
		$data['t'] = intval($t);
		foreach($setting as $key => $value) {
			if (in_array($key, $this->config->item('setting_keys'))) {
				if ($data['t'] < $value['t']) {
					$is_new = true;
					$data['t'] = $value['t'];
				}
				$data[$key] = $value['v'];
			}
		}
		
		// 去获取系统版本
		$version = $this->db->limit(1)->order_by('dateline', 'desc')
			->get_where('version', array('type' => $this->dt))->row_array();
		$v1 = explode('.', $version['version']);
		$v2 = explode('.', $this->av);
		foreach(count($v1)>count($v2)?$v1:$v2 as $k => $v) {
			if(intval($v1[$k]) > intval($v2[$k])) {
				$is_new = true;
				$data['version'] = $version;
			} elseif (intval($v1[$k]) < intval($v2[$k])) {
				break;
			}
		}
		
		$data['new_recommend_num'] = 0;
		$data['new_commision_num'] = 0;
		if($this->cel) {
			// 已经登陆了
			// 去获取是否有新的推荐
			$data['new_recommend_num'] = $this->db->where(array(
					'type' => 2, 
					'recommend_cellphone_no' => $this->cel, 
					'is_new' => 1)
					)->count_all_results('order');
			
			// 去获取是否有新的佣金
			$data['new_commision_num'] = $this->db->where(array(
					'cellphone_no' => $this->cel,
					'is_new' => 1)
					)->count_all_results('commision');
			
			if($data['new_recommend_num'] || $data['new_commision_num']) {
				$is_new = true;
			}
		}

		if ($is_new) {
			// 有更新
			$this->http_200($data);
		} else {
			// 无更新
			$this->http_304();
		}
	}

	/**
	 * @SWG\Api(
	 * 		path="/house_list",
	 *		description="获取楼盘列表",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="lat",
	 * 						description="纬度",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="float",
	 * 						defaultValue="30.6594620"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="lng",
	 * 						description="经度",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="float",
	 * 						defaultValue="104.0657350"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page",
	 * 						description="页码",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="1"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="area",
	 * 						description="区域，不限0 第一个1... ...",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="price",
	 * 						description="价格，不限0 第一个1... ...",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="direction",
	 * 						description="方位，不限0 第一个1... ...",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="loopline",
	 * 						description="环线，不限0 第一个1... ...",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="order",
	 * 						description="排序，默认0... ...",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page_size",
	 * 						description="每页条数",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="20"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="trends",
	 * 						description="是否需要动态",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="group",
	 * 						description="是否只获取团购信息 非空只获取团购",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="key",
	 * 						description="搜索关键词",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue=""
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function house_list($lat = 0, $lng = 0, $page = 0, $area = 0, $price = 0,
	$direction = 0, $loopline = 0, $order = 0, $page_size = 0, $trends = 0, $group = 0, $key = '', $on_group = 0) {
		// 获取参数
		$lat = floatval($lat?$lat:$this->get('lat', 0)); // 纬度
		$lng = floatval($lng?$lng:$this->get('lng', 0));	 // 经度
		$page = intval($page?$page:$this->get('page', 1)); // 页码
		$area = intval($area?$area:$this->get('area', 0)); // 区域
		$price = intval($price?$price:$this->get('price', 0)); // 价格
		$direction = intval($direction?$direction:$this->get('direction', 0)); // 方向
		$loopline = intval($loopline?$loopline:$this->get('loopline', 0)); // 环线
		$order = intval($order?$order:$this->get('order', 0)); // 排序
		$trends = intval($trends?$trends:$this->get('trends', 0)); // 是否有动态
		$group = intval($group?$group:$this->get('group', 0)); // 是否只获取团购
		$key = urldecode($key?$key:$this->get('key', '')); //关键词
		$page_size = intval($page_size?$page_size:$this->get('page_size', $this->config->item('default_page_size')));
		$on_group = intval($on_group?$on_group:$this->get('on_group', 0)); // 是否只要正在进行的团购

		if ($this->config->item('distance_order') == $order && ($lat <= 0 || $lng <=0)) {
			$this->http_500('msg_house_list_5001');
		}

		$where_arr = array('a.status = 0', 'a.sale_status < 2');
		if($area) {
			$config_area = $this->config->item('area');
			$where_arr[] = sprintf("area = '%s'", $config_area[$area]);
		}

		if($price) {
			$config_price = $this->config->item('price');
			$price_range = $config_price[$price];
			$where_arr[] = sprintf("price BETWEEN %d AND %d", $price_range[0], $price_range[1]);
		}

		if($direction) {
			$config_direction = $this->config->item('direction');
			$where_arr[] = sprintf("direction = '%s'", $config_direction[$direction]);
		}

		if($loopline) {
			$config_loopline = $this->config->item('loopline');
			$loopline_range = $config_loopline[$loopline];
			if(is_array($loopline_range)) {
				$where_arr[] = sprintf("loopline in ('%s')", join("', '", $loopline_range));
			} else {
				$where_arr[] = sprintf("loopline = '%s'", $loopline_range);
			}
		}

		if($key) {
			$where_arr[] = sprintf("name like '%%%s%%'", daddslashes($key));
		}
		
		if($group) {
			$where_arr[] = "has_group = '1'";
		}
		
		if($on_group) {
			$where_arr[] = 'group_status = 0';
		}

		$data = array();
		$sql = 'SELECT COUNT(a.id) AS num FROM house a' .
				($where_arr?(' WHERE ' . join(' AND ', $where_arr)):'');
		$row = $this->db->query($sql)->row_array();
		$total_num = intval($row['num']);
		$data = paginate($total_num, $page, $page_size);

		$config_order = $this->config->item('order');
		$sql = sprintf('SELECT a.id, a.house_id, a.name, a.address, a.lat, a.lng, a.area,
				a.price, a.direction, a.loopline, a.cover, a.has_group,
				a.group_expire_date, a.group_title, a.group_count, a.group_status,
				%s AS distance,
				IF(DATEDIFF(a.group_expire_date, now()) >= 0, 1, 0) AS group_valid,
				IF(a.price = 0, %s, a.price) AS order_price
				FROM house a %s ORDER BY %s LIMIT %d OFFSET %d',
				($lat > 0 && $lng > 0)?sprintf('f_distance(a.lat, a.lng, %s, %s)', $lat, $lng):'0',
				$this->config->item('order_price'),
				($where_arr?(' WHERE ' . join(' AND ', $where_arr)):''),
				$config_order[$order]?$config_order[$order]:'a.rank_order DESC', 
				$data['page_size'], $data['offset']);
		$rows = $this->db->query($sql)->result_array();

		if($rows) {
			foreach($rows as &$row) {
				unset($row['group_valid']);
				if ($row['has_group'] && $row['group_status'] < 2) {
					$d1 = new DateTime($row['group_expire_date']);
					$d2 = new DateTime(now(TIMESTAMP, 'Y-m-d'));
					$row['group'] = array(
							'title' => $row['group_title'],
							'count' => $row['group_count'],
							'expire_day' => $d1->diff($d2)->days + 1,
							'status' => $row['group_status']
					);
				}
				unset($row['has_group'], $row['group_title'],
						$row['group_count'], $row['group_expire_date'],
						$row['group_status']);
				$row['label_price'] = sprintf($this->lang->line('label_avg_price'),
						$row['price']?($row['price'].$this->lang->line('label_price_unit')):$this->lang->line('label_price_empty'));
				$label_area = $row['area']?$row['area']:'';
				$row['distance'] = floatval($row['distance']);
				if ($row['distance'] > 0) {
					if($row['distance'] > 1) {
						$distance = number_format($row['distance'], 1) . $this->lang->line('label_km');
					} else {
						$distance = number_format($row['distance']*1000, 0, '', '') . $this->lang->line('label_m');
					}
				} else {
					$distance = '';
				}
				$row['label_area'] = $label_area . ($label_area?' ':'') . $distance;
				$row['cover'] = cover_url($row['cover'], $this->dp);
				$data['list'][] = $row;
			}
			unset($row);
		}

		if ($trends && $page == 1) {
			// 获取最新的团购成功信息
			$rows = $this->db->select('name, cellphone_no, house_id, house_name', false)
				->where('type', 0)->order_by('dateline', 'desc')
				->limit($this->config->item('home_trends_num'), 0)
				->from('order')->get()->result_array();
			foreach($rows as $row) {
				$data['trends'][] = sprintf($this->lang->line('label_trends'),
						cut_string($row['name'], 2, ''), substr($row['cellphone_no'], 0, 3),
						substr($row['cellphone_no'], 8), $row['house_name']);
			}
		}

		$this->http_200($data);
	}

	/**
	 * @SWG\Api(
	 * 		path="/house_detail",
	 *		description="楼盘详情",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="id",
	 * 						description="楼盘ID号",
	 * 						paramType="query",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="1162"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function house_detail($id = 0) {
		$id = intval($id?$id:$this->get('id'));
		if($id <= 0) {
			$this->http_500('msg_house_detail_5001');
		}

		// 获取楼盘信息
		$house = $this->db->where(array('house_id' => $id, 'status' => 0))->get('house')->row_array();

		if (empty($house)) {
			$this->http_500('msg_house_detail_5002');
		}

		$this->load->helper('indb');
		// 获取IN成都的楼盘详情
		$house_info = get_house_detail($id);
		$house_info['detail'] || ($house_info['detail'] = array());
		array_unshift($house_info['detail'], array(
				'name' => $this->lang->line('label_house_avgprice'),
				'value' => $house['price']?sprintf($this->lang->line('label_house_avgprice_format'), $house['price']):$this->lang->line('label_price_empty')
			));
		array_unshift($house_info['detail'], array(
				'name' => $this->lang->line('label_house_name'), 
				'value' => $house['name']
				));
		$data = array(
				'house_id' => $house['house_id'],
				'name' => $house['name'],
				'address' => $house['address'],
				'cover' => cover_url($house['cover'], $this->dp),
				'lat' => $house['lat'],
				'lng' => $house['lng'],
				'detail' => $house_info['detail'],
				'trends' => $house_info['trends'],
				'more' => $house_info['more'],
				'album' => $house_info['album'],
				'price' => $house['price']
		);

		if($house['has_group'] && $house['group_status'] < 2) {
			// 有团购信息
			$d1 = new DateTime($house['group_expire_date']);
			$d2 = new DateTime(now(TIMESTAMP, 'Y-m-d'));

			$data['group'] = array(
					'title' => $house['group_title'],
					'count' => $house['group_count'],
					'expire_day' => $d1->diff($d2)->days + 1,
					'status' => $house['group_status'],
					'detail' => $house['group_detail']
			);
		}

		if($house['has_recommend'] && $house['recommend_status'] < 2) {
			// 有推荐
			$data['recommend'] = array(
					'title' => $house['recommend_title'],
					'detail' => $house['recommend_detail'],
					'status' => $house['recommend_status'],
					'expire_date' => $house['recommend_expire_date']
			);
		}

		$this->http_200($data);
	}

	/**
	 * @SWG\Api(
	 * 		path="/house_same_price",
	 *		description="相同价位楼盘",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="price",
	 * 						description="楼盘价格",
	 * 						paramType="query",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="7000"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page",
	 * 						description="页码",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="1"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page_size",
	 * 						description="每页条数",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="20"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="lat",
	 * 						description="纬度",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="float",
	 * 						defaultValue="30.6594620"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="lng",
	 * 						description="经度",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="float",
	 * 						defaultValue="104.0657350"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function house_same_price($price = 0, $page = 0, $page_size = 0, $lat = 0, $lng = 0) {
		$price = intval($price?$price:$this->get('price'));
		($price < 0) && ($price = 0);
		$page = intval($page?$page:$this->get('page', 1)); // 页码
		$page_size = intval($page_size?$page_size:$this->get('page_size', $this->config->item('default_page_size')));
		
		$lat = floatval($lat?$lat:$this->get('lat', 0)); // 纬度
		$lng = floatval($lng?$lng:$this->get('lng', 0));	 // 经度

		$price_range = $this->config->item('price');

		$p = 0;
		foreach($price_range as $k => $v) {
			// 比较价格是否在范围内
			if($price >= $v[0] && $price <= $v[1]) {
				$p = $k;
				break;
			}
		}

		$this->house_list($lat, $lng, $page, 0, $p, '', '', 2, $page_size);
	}

	/**
	 * @SWG\Api(
	 * 		path="/apply",
	 *		description="团购、优惠、推荐（需要验证的token）",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="POST",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="id",
	 * 						description="楼盘ID号",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="1162"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="name",
	 * 						description="报名人姓名",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="张三"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cellphone_no",
	 * 						description="报名人手机号",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="type",
	 * 						description="团购类型 0：团购 1：优惠 2：推荐",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="0"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cel",
	 * 						description="测试用，登陆用户的手机号 推荐需要",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function apply() {
		// 类型
		$type = intval($this->post('type'));
		if(!in_array($type, $this->config->item('apply_types'))) {
			$this->http_500('msg_signup_5004');
		}

		// 验证用户名
		$name = trim($this->post('name'));
		if(empty($name) || dstrlen($name) > $this->config->item('name_check_num')) {
			$this->http_500('msg_signup_5002');
		}

		// 检查手机号
		$cellphone_no = trim($this->post('cellphone_no'));
		if(!check_cellphone($cellphone_no)) {
			$this->http_500('msg_signup_5003');
		}


		// 非注册，那么需要去判断楼盘是否存在
		$id = intval($this->post('id'));
		if($id <= 0) {
			$this->http_500('msg_signup_5001');
		}

		// 获取楼盘信息
		$house = $this->db->where(array('house_id' => $id, 'status' => 0))->get('house')->row_array();

		if (empty($house)) {
			$this->http_500('msg_signup_5006');
		}

		switch($type) {
			case 0:
				if(empty($house['has_group'])) {
					// 没有团购信息
					$this->http_500('msg_signup_5014');
				}
				if($house['group_status'] == 1 || TIMESTAMP > strtotime($house['group_expire_date'] . ' 23:59:59')) {
					// 已经过期了
					$this->http_500('msg_signup_5012');
				}
				break;
			case 2:
				if(empty($house['has_recommend'])) {
					// 没有推荐信息
					$this->http_500('msg_signup_5015');
				}
				if($house['recommend_status'] == 1 || TIMESTAMP > strtotime($house['recommend_expire_date'] . ' 23:59:59')) {
					// 已经过期了
					$this->http_500('msg_signup_5013');
				}

				if($this->cel == $cellphone_no) {
					// 推荐自己，那是不行的
					$this->http_500('msg_signup_5010');
				}
				
				$today = now(TIMESTAMP, 'Y-m-d');
				// 检查推荐数 recommend_limit
				$today_num =$this->db->where(sprintf("type=2 and 
						recommend_cellphone_no='%s' and dateline between '%s' and '%s'", 
						$this->cel, $today . ' 00:00:00', $today . ' 23:59:59'), null, false)
						->count_all_results('order');
				$recommend_limit = $this->config->item('recommend_limit');
				if($today_num >= $recommend_limit) {
					// 超过限制条数了
					$this->http_500('msg_signup_5016', array($recommend_limit));
				}
				break;
		}

		// 检查是否已经参加了
		$order = $this->db->where(array(
				'type' => $type,
				'cellphone_no' => $cellphone_no,
				'house_id' => $id
		))->get('order')->row_array();

		if ($order) {
			if ($type == 2) {
				if($order['recommend_cellphone_no'] == $this->cel) {
					// 推荐人是自己，已经推荐过了
					$this->http_500('msg_signup_5008');
				} else {
					// 推荐人是别人。已经被别人推荐了
					$this->http_500('msg_signup_5009');
				}
			} else {
				$this->http_500('msg_signup_5007' . $type);
			}
		}

		$data = array(
				'house_id' => $id,
				'house_name' => $house['name'],
				'type' => $type,
				'name' => $name,
				'cellphone_no' => $cellphone_no,
				'last_dateline' => now(TIMESTAMP, 'Y-m-d H:i:s')
		);
		$update_data = array();
		switch($type) {
			case 0:
				$data['title'] = $house['group_title'];
				$data['detail'] = $house['group_detail'];
				$data['expire_date'] = $house['group_expire_date'];
				$update_data['group_count'] = $house['group_count'] + 1;
				$update_data['group_actual_num'] = $house['group_actual_num'] + 1;
				break;
			case 1:
				$data['title'] = sprintf($this->lang->line('msg_signup_preferential_title'), $house['name']);
				$update_data['preferential_count'] = $house['preferential_count'] + 1;
				break;
			case 2:
				$data['title'] = $house['recommend_title'];
				$data['detail'] = $house['recommend_detail'];
				$data['expire_date'] = $house['recommend_expire_date'];
				$data['recommend_name'] = strval($this->user['name']);
				$data['recommend_cellphone_no'] = strval($this->cel);
				$update_data['recommend_count'] = $house['recommend_count'] + 1;
				break;
		}
		empty($data['title']) && ($data['title'] = '');
		empty($data['detail']) && ($data['detail'] = '');

		// 添加order信息
		$this->db->insert('order', $data);
		$order_id = $this->db->insert_id();
		if($order_id) {
			// 添加成功，那么去更新house表里面的数据
			$this->db->where('house_id', $id)->update('house', $update_data);
			if($type == 2) {
				// 计算用户今日推荐数
				$today_recommend_num = $this->db->where(array('type' => 2, 'recommend_cellphone_no' => $this->cel))
					->where(sprintf("dateline between '%s' and '%s'", $today . ' 00:00:00', $today . ' 23:59:59'), null, false)
					->count_all_results('order');
				$this->db->set('recommend_count', 'recommend_count+1', false)
					->set('today_recommend_count', $today_recommend_num)
					->where('cellphone_no', $this->cel)->update('user');
			}
			$this->http_200('msg_signup_201' . $type);
		} else {
			$this->http_500('msg_signup_5011' . $type);
		}
	}

	/**
	 * @SWG\Api(
	 * 		path="/signup",
	 *		description="注册、登陆获取验证码",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="POST",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="cellphone_no",
	 * 						description="报名人手机号",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function signup() {
		// 检查手机号
		$cellphone_no = trim($this->post('cellphone_no'));
		if(!check_cellphone($cellphone_no)) {
			$this->http_500('msg_signup_5003');
		}

		// 登陆或注册，那么去数据库看是不是有这个用户
		$user = $this->db->where('cellphone_no', $cellphone_no)->get('user')->row_array();
		// 生成一个验证码
		$this->load->helper('string');
		$verify_code = generator_verify_code($cellphone_no);
		$data = array(
				'verify_code' => $verify_code['encode'],
				'cellphone_no' => $cellphone_no,
				'device_code' => $this->dc
		);
		if(empty($user)) {
			// 添加用户进去
			$data['name'] = $this->lang->line('default_name');
			$this->db->insert('user', $data);
		} else {
			// 更新用户信息
			$this->db->where('cellphone_no', $cellphone_no)->update('user', $data);
		}
		$msg = sprintf($this->lang->line('sms_verify_code'), $verify_code['code']);
		$rtn_obj = send_sms($cellphone_no, $msg);
		if($rtn_obj->resultCode) {
			$this->http_500('msg_signup_5005');
		} else {
			$this->http_200('msg_signup_202');
		}
	}

	/**
	 * @SWG\Api(
	 * 		path="/signin",
	 *		description="验证登陆",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="POST",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="name",
	 * 						description="报名人名字",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="张三"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cellphone_no",
	 * 						description="报名人手机号",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="code",
	 * 						description="手机收到的验证码",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="123456"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function signin() {
		// 检查手机号
		$cellphone_no = trim($this->post('cellphone_no'));
		if(!check_cellphone($cellphone_no)) {
			$this->http_500('msg_signin_5001');
		}

		// 验证用户名
		$name = trim($this->post('name'));
		if(empty($name) || dstrlen($name) > $this->config->item('name_check_num')) {
			$this->http_500('msg_signin_5006');
		}

		$code = trim($this->post('code'));
		if(empty($code) || strlen($code) != $this->config->item('verify_code_lenth')) {
			// 验证码错误
			$this->http_500('msg_signin_5005');
		}

		$user = $this->db->where('cellphone_no', $cellphone_no)->get('user')->row_array();
		if(empty($user)) {
			// 没有获取到用户信息
			$this->http_500('msg_signin_5003');
		}

		// 比较验证码
		if(verify_code($user['verify_code'], $code, $cellphone_no)) {
			// 成功
			$token = generator_token($cellphone_no);
			$this->db->where('cellphone_no', $cellphone_no)->update('user', 
					array(
							'name' => $name, 
							'verify_code' => '', 
							'verify_num' => 0,
							'token' => $token
							));
			$this->http_200(array('token' => $token));
		} else {
			// 失败
			// 检查失败次数
			$user['verify_num'] += 1;
			if($user['verify_num'] >= $this->config->item('verify_num_limit')) {
				$this->db->where('cellphone_no', $cellphone_no)->update('user', array('verify_code' => '', 'verify_num' => 0));
				$this->http_500('msg_signin_5004', array($user['verify_num']));
			}
			// 去更新次数
			$this->db->where('cellphone_no', $cellphone_no)->update('user', array('verify_num' => $user['verify_num']));
			$this->http_500('msg_signin_5002');
		}
	}

	/**
	 * @SWG\Api(
	 * 		path="/my_recommend",
	 *		description="我的推荐（需要验证的token）",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="status",
	 * 						description="0：待审核  1：有效推荐  2：失败推荐 3：推荐过期 4：成功成交的推荐 其他：所有",
	 * 						paramType="query",
	 * 						required="true",
	 * 						dataType="int",
	 * 						defaultValue="99"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page",
	 * 						description="页码",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="1"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page_size",
	 * 						description="每页条数",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="20"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cel",
	 * 						description="测试用，登陆用户的手机号",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function my_recommend($status = 0, $page = 0, $page_size = 0) {
		$page = intval($page?$page:$this->get('page', 1)); // 页码
		$page_size = intval($page_size?$page_size:$this->get('page_size', $this->config->item('default_page_size')));
		$status = intval($status?$status:$this->get('status' ,0));

		$recommend_status = $this->config->item('recommend_status');

		$where_arr = array('type' => 2, 'recommend_cellphone_no' => $this->cel);
		if ($recommend_status[$status]) {
			// 需要过滤状态
			$where_arr['status'] = $status;
		}

		$total_num = $this->db->where($where_arr)->count_all_results('order');
		$data = paginate($total_num, $page, $page_size);

		$rows = $this->db->where($where_arr)->order_by('is_new', 'desc')
		->order_by('last_dateline', 'desc')
		->limit($data['page_size'], $data['offset'])
		->get('order')->result_array();
		foreach($rows as $row) {
			$params = array($this->lang->line('msg_my_recommend_100' . $row['status']), $row['last_dateline']);
			if($row['status'] == 4) {
				$comments = decode_json($row['comments']);
				$params[] = strval($comments['commision']['money']);
				$params[] = strval($comments['commision']['serial_number']);
				
			} elseif(2 == $row['status']) {
				// 驳回
				$comments = decode_json($row['comments']);
				$params[0] = $comments['reject']['comment'];
			}
			$detail = call_user_func_array('sprintf', $params);
			
			$data['list'][] = array(
					'id' => $row['id'],
					'status' => $row['status'],
					'detail' => $detail,
					'recommend' => array(
							'name' => $row['name'],
							'cellphone_no' => $row['cellphone_no'],
							'house_name' => $row['house_name'],
							'house_id' => $row['house_id'],
							'dateline' => now(strtotime($row['dateline']), 'Y-m-d H:i'),
							'is_new' => $row['is_new']?true:false
							)
			);
		}
		
		$where_arr['is_new'] = 1;
		// 更新状态为已读
		$this->db->where($where_arr)->update('order', array('is_new' => 0));

		$this->http_200($data);
	}

	/**
	 * @SWG\Api(
	 * 		path="/my_commision",
	 *		description="我的佣金（需要验证的token）",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="GET",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="page",
	 * 						description="页码",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="1"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="page_size",
	 * 						description="每页条数",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="int",
	 * 						defaultValue="20"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cel",
	 * 						description="测试用，登陆用户的手机号",
	 * 						paramType="query",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function my_commision($page = 0, $page_size = 0) {
		$page = intval($page?$page:$this->get('page', 1)); // 页码
		$page_size = intval($page_size?$page_size:$this->get('page_size', $this->config->item('default_page_size')));
		$where_arr = array('cellphone_no' => $this->cel);
		$total_num = $this->db->where($where_arr)->count_all_results('commision');
		$data = paginate($total_num, $page, $page_size);
		
		$rows = $this->db->order_by('dateline', 'desc')->limit($data['page_size'], $data['offset'])
			->where($where_arr)->get('commision')->result_array();
		foreach($rows as $row) {
			$r = array(
					'money' => ($row['money']>=0?'+':'') . number_format($row['money'])  . $this->lang->line('label_price_unit'),
					'dateline' => now(strtotime($row['dateline']), 'Y-m-d H:i'),
					'serial_number' => $row['id'],
					'is_new' => $row['is_new']?true:false,
					'action_label' => $this->config->item('commision_actions')[$row['money']>=0?0:1]
					);
			if($row['order_id']) {
				$r['recommend_number'] = $row['order_id'];
			}
			$data['list'][] = $r;
		}
		$data['commision'] = $this->lang->line('label_currency_symbol') . number_format($this->user['commision'], 0) . $this->lang->line('label_price_unit');
		
		$where_arr['is_new'] = 1;
		// 更新状态为已读
		$this->db->where($where_arr)->update('commision', array('is_new' => 0));
		
		$this->http_200($data);
	}
	
	/**
	 * @SWG\Api(
	 * 		path="/update_user",
	 *		description="更新用户信息 需要token验证",
	 * 		@SWG\Operations(
	 * 			@SWG\Operation(
	 * 				httpMethod="POST",
	 * 				@SWG\Parameters(
	 * 					@SWG\Parameter(
	 * 						name="name",
	 * 						description="用户名",
	 * 						paramType="form",
	 * 						required="true",
	 * 						dataType="string",
	 * 						defaultValue="飞天猪"
	 * 					),
	 * 					@SWG\Parameter(
	 * 						name="cel",
	 * 						description="测试使用 用户的手机号",
	 * 						paramType="form",
	 * 						required="false",
	 * 						dataType="string",
	 * 						defaultValue="13550009575"
	 * 					)
	 * 				),
	 * 				@SWG\ErrorResponses(
	 * 					@SWG\ErrorResponse(
	 * 						code="404",
	 * 						reason="File not found"
	 * 					),
	 * 					@SWG\ErrorResponse(
	 * 						code="500",
	 * 						reason="Internal Server Error"
	 * 					)
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function update_user() {
		// 验证用户名
		$name = trim($this->post('name'));
		if(empty($name) || dstrlen($name) > $this->config->item('name_check_num')) {
			$this->http_500('msg_update_user_5001');
		}
		
		$this->db->where('cellphone_no', $this->cel)->update('user', array('name' => $name));
		
		$this->http_200('msg_update_user_2001');
	}
	
	/**
	 * 返回所有的详情，给网站用的
	 */
	function house_detail_all($id = 0) {
		$id = intval($id?$id:$this->get('id'));
		if($id <= 0) {
			$this->http_500('msg_house_detail_5001');
		}
		
		// 获取楼盘信息
		$house = $this->db->where(array('house_id' => $id, 'status' => 0))->get('house')->row_array();
		
		if (empty($house)) {
			$this->http_500('msg_house_detail_5002');
		}
		
		$house['cover'] = cover_url($house['cover'], $this->dp);
		
		$this->load->helper('indb');
		// 获取IN成都的楼盘详情
		$house_info = get_house_detail_all($id);
		
		$house['ext_detail'] = $house_info;
		
		$this->http_200($house);
	}
}

