<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 房产管理
 */
	
class House extends MY_Controller {
	function __construct() {
		parent::__construct();
		
		$this->tfdb = $this->load->database('tfdb', true);
		$this->load->config('config_house');
		$this->load->helper('house');
		
		$this->app_type = $this->config->item('app_type');
		$this->assign('app_type', $this->app_type);
		
		$website = $this->config->item('web_site');
		$this->assign("website",$website);
		
		$this->poi_status = $this->config->item('poi_status');
		
		$this->load->language('house');
	}
	
	/**
	 * 版本记录
	 */
	function version_list() {
		$type = $this->post('type');
		$keywords = $this->post('keywords');
	
		$where_sql = array();
		$keywords && $where_sql[] = "version = '{$keywords}'";
		$type && $where_sql[] = "type = '" . ($type - 1) . "'";
		$where_sql && $where_sql = implode(' and ', $where_sql);
	
		$total_num = $this->tfdb->where($where_sql)->from('version')->count_all_results();
		$paginate = $this->paginate($total_num);

		$list = $this->tfdb->order_by('dateline', 'desc')
			->limit($paginate['per_page_num'], $paginate['offset'])
			->where($where_sql)
			->from('version')
			->get()->result_array();
		$keywords = dstripslashes($keywords);
		$this->assign(compact('type', 'keywords', 'list'));
	
		$this->display('version_list');
	}
	
	/**
	 * 修改版本
	 */
	function version_edit() {
		$this->version_add();
	}
	
	/**
	 * 发布版本
	 */
	function version_add() {
		$id = $this->get('id');
	
		$version = $this->tfdb->get_where('version', "id = '{$id}'")->row_array();
	
		if($this->is_post()) {
			$data = array();
			$data['type'] = $this->post('type');
			$data['version'] = trim($this->post('version'));
			$data['download_url'] = trim($this->post('download_url'));
			$data['force_update'] = $this->post('force_update');
			$data['change_log'] = $this->post('change_log');
			$data['dateline'] = now();
	
			if(strpos($data['download_url'], 'http://') !== 0) {
				$data['download_url'] = 'http://' . $data['download_url'];
			}
	
			$tip = '';
			if($id) {
				// 修改
				$tip = '修改';
				unset($data['type'], $data['version']);
				$b = $this->tfdb->update('version', $data, "id = '{$id}'");
			} else {
				// 增加
				// 判断之前是否添加版本一样。类型一样的给与提示
				if($this->tfdb->get_where('version', "type = '{$data['type']}' and version = '{$data['version']}'")->row_array()) {
					// 提示真的要提交么？
					$this->error('已经发布一个[' . $this->client_type[$data['type']] . ']下版本为['.$data['version'].']，不能重复提交');
				}
				$tip = '发布';
				$b = $this->tfdb->insert('version', $data);
			}
	
			$b?$this->success($tip . '版本成功', '', '', 'closeCurrent'):$this->error($tip . '版本失败');
		}
	
		$this->assign('version', $version);
	
		$this->display('version_add');
	}
	
	/**
	 * 房产的setting
	 */
	function setting() {
		if($this->is_post()) {
			// 提交了请求
			$name = $this->post('name');
			$value = $this->post('value');
			$data = array();
			foreach($name as $key => $n) {
				$v = $value[$key];
				$data[] = array(
						'k' => $key,
						'n' => $n,
						'v' => $v,
						'dateline' => TIMESTAMP
						);
			}
			$this->tfdb->update_batch('setting', $data, 'k');			
			clear_third_cache('house', 'inc', 'setting');
			$this->success('更新成功');
		}
		
		$settings = $this->tfdb->where("k not in ('banner', 'web_banner')", null, false)->get('setting')->result_array();
		$this->assign('settings', $settings);
		$this->display('setting');
	}
	
	/**
	 * 添加 banner
	 */
	function banner_add($id = -1) {
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'banner'))->row_array();
		$list = json_decode($v['v'], true);
		$link_type = $this->config->item('house_link_type');
		$intf_type = $this->config->item('intf_type');
		
		if($this->is_post()) {
			// 添加
			$title = trim($this->post('title'));
			$subtitle = trim($this->post('subtitle'));
			$link = trim($this->post('link'));
			$index = intval(trim($this->post('index')));
			$url = trim($this->post('url'));
			$image = trim($this->post('image'));
			
			if(empty($image)) {
				$this->error('请上传图片');
			}
			
			$image = image_url($image, 'default');
			
			if($url) {
				if($intf_type[$link]) {
					$url = sprintf($intf_type[$link], $url);
				} else {
					if(strpos($url, 'http://') !== 0) {
						$url = 'http://' . $url;
					}
				}
			}
			
			$data = compact('image', 'url', 'title', 'subtitle');
			
			$datas = array();
			// 插入位置
			if(count($list)) {
				if($id >= 0) {
					// 修改
					// 把原来那一条设置为''
					$list[$id] = 'delete';
				}
				// 添加
				foreach($list as $k => $row) {
					if($k == $index) {
						$datas[] = $data;
					}
					$datas[] = $row;
				}
				if($k + 1 == $index) {
					$datas[] = $data;
				}
			} else {
				$datas[] = $data;
			}
			
			foreach($datas as $k => $row) {
				if($row == 'delete') {
					unset($datas[$k]);
				}
			}
			
			$data = array();
			foreach($datas as $row) {
				$data[] = $row;
			}
			
			// 写入数据
			$b = $this->tfdb->where(array('k' => 'banner'))->update('setting', 
					array('v' => encode_json($data), 'dateline' => TIMESTAMP));
			if($b) {
				clear_third_cache('house', 'inc', 'setting');
				$this->success('添加banner成功', 'house_banner_list', '/house/banner_list', 'closeCurrent');
			} else {
				$this->error('添加banner出错');
			}
		}
		
		$banner = $list[$id];
		
		if($banner) {
			$strs = explode('://', $banner['url'], 2);
			if('intf' == $strs[0]) {
				// intf里面的
				$strs = explode('/', $strs[1]);
				if(strpos($strs[1], 'detail') === 0) {
					$pstr = str_replace(array('%s', "/", "?"), array('', "\/", "\?"), $intf_type['detail']);
					preg_match("/{$pstr}(\d*)/i", $banner['url'], $arr);
					$url = $arr[1];
					$link = 'detail';
				}
			} else {
				$link = 'http';
				$url = $banner['url'];
			}
			$this->assign(compact('link', 'url'));
		}
		
		$this->assign(compact('list', 'link_type', 'banner', 'id'));
		
		$this->display('banner_add');
	}
	
	/**
	 * banner 列表
	 */
	function banner_list() {
		// 获取banner信息
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'banner'))->row_array();
		
		$this->assign('list', json_decode($v['v'], true));
		
		$this->display('banner_list');
	}
	
	/**
	 * 修改 banner
	 */
	function banner_edit() {
		if(isset($_GET['id'])) {
			$this->banner_add(intval($this->get('id')));
		}
		$this->banner_add();
	}
	
	/**
	 * 删除
	 */
	function banner_del() {
		$id = intval($this->get('id'));
		
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'banner'))->row_array();
		$list = json_decode($v['v'], true);
		
		$data = array();
		foreach($list as $k => $row) {
			if($k == $id) {
				continue;
			}
			
			$data[] = $row;
		}
		
		// 写入数据
		$b = $this->tfdb->where(array('k' => 'banner'))->update('setting', array('v' => encode_json($data), 'dateline' => TIMESTAMP));
		if($b) {
			clear_third_cache('house', 'inc', 'setting');
			$this->success('删除banner成功', 'house_banner_list', '/house/banner_list');
		} else {
			$this->error('删除banner出错');
		}
	}
	
	/**
	 * 添加 banner
	 */
	function web_banner_add($id = -1) {
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'web_banner'))->row_array();
		$list = json_decode($v['v'], true);
	
		if($this->is_post()) {
			// 添加
			$title = trim($this->post('title'));
			$index = intval(trim($this->post('index')));
			$url = trim($this->post('url'));
			$image = trim($this->post('image'));
				
			if(empty($image)) {
				$this->error('请上传图片');
			}
				
			$image = image_url($image, 'common');
			
			if($url) {
				if(strpos($url, 'http://') !== 0) {
					$url = 'http://' . $url;
				}
			}
				
			$data = compact('image', 'url', 'title');
				
			$datas = array();
			// 插入位置
			if(count($list)) {
				if($id >= 0) {
					// 修改
					// 把原来那一条设置为''
					$list[$id] = 'delete';
				}
				// 添加
				foreach($list as $k => $row) {
					if($k == $index) {
						$datas[] = $data;
					}
					$datas[] = $row;
				}
				if($k + 1 == $index) {
					$datas[] = $data;
				}
			} else {
				$datas[] = $data;
			}
				
			foreach($datas as $k => $row) {
				if($row == 'delete') {
					unset($datas[$k]);
				}
			}
			
			$data = array();
			foreach($datas as $row) {
				$data[] = $row;
			}
				
			// 写入数据
			$b = $this->tfdb->where(array('k' => 'web_banner'))->update('setting',
					array('v' => encode_json($data), 'dateline' => TIMESTAMP));
			if($b) {
				clear_third_cache('house', 'inc', 'setting');
				$this->success('添加网站banner成功', 'house_web_banner_list', '/house/web_banner_list', 'closeCurrent');
			} else {
				$this->error('添加网站banner出错');
			}
		}
	
		$banner = $list[$id];
	
		$this->assign(compact('list', 'banner', 'id'));
	
		$this->display('web_banner_add');
	}
	
	/**
	 * banner 列表
	 */
	function web_banner_list() {
		// 获取banner信息
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'web_banner'))->row_array();
	
		$this->assign('list', json_decode($v['v'], true));
	
		$this->display('web_banner_list');
	}
	
	/**
	 * 修改 banner
	 */
	function web_banner_edit() {
		if(isset($_GET['id'])) {
			$this->web_banner_add(intval($this->get('id')));
		}
		$this->web_banner_add();
	}
	
	/**
	 * 删除
	 */
	function web_banner_del() {
		$id = intval($this->get('id'));
	
		$v = $this->tfdb->select('v')->get_where('setting', array('k' => 'web_banner'))->row_array();
		$list = json_decode($v['v'], true);
	
		$data = array();
		foreach($list as $k => $row) {
			if($k == $id) {
				continue;
			}
				
			$data[] = $row;
		}
	
		// 写入数据
		$b = $this->tfdb->where(array('k' => 'web_banner'))->update('setting', array('v' => encode_json($data), 'dateline' => TIMESTAMP));
		if($b) {
			clear_third_cache('house', 'inc', 'setting');
			$this->success('删除banner成功', 'house_web_banner_list', '/house/web_banner_list');
		} else {
			$this->error('删除banner出错');
		}
	}
	
	/**
	 * 楼盘列表
	 */
	function index() {
		flush_status(); // 刷新团购、推荐状态
		$keywords = trim($this->post('keywords'));
		$where_arr = array();
		if($keywords !== '') {
			$type = $this->post('type');
			switch($type) {
				case 'id' :
					$where_arr[] = "a.house_id = '".intval($keywords)."'";
					break;
				case 'placename' :
					$where_arr[] = "a.name like '%$keywords%'";
					break;
				case 'address' :
					$where_arr[] = "a.address like '%$keywords%'";
					break;
			}
			
			$keywords = dstripslashes($keywords);
		}
		
		// 状态
		$status = intval(trim($this->post('status')));
		$where_arr[] = "a.status = '{$status}'";		
		
		// 团购
		$has_group = intval(trim($this->post('has_group')));
		if($has_group) {
			$where_arr[] = "a.has_group = '1'";
		}
		
		// 返利
		$has_recommend = intval(trim($this->post('has_recommend')));
		if($has_recommend) {
			$where_arr[] = "a.has_recommend = '1'";
		}
		$where_sql = join(' and ', $where_arr);
		$total_num = $this->tfdb->where($where_sql, null ,false)->count_all_results('house a');
		$paginate = $this->paginate($total_num);
		
		$list = $this->tfdb->where($where_sql, null ,false)->order_by($has_group?'a.rank_order':'a.house_id', 'desc')
			->limit($paginate['per_page_num'], $paginate['offset'])->get('house a')->result_array();
		$this->assign('poi_status', $this->config->item('poi_status'));
		$this->assign(compact('list', 'keywords', 'has_group', 'has_recommend', 'status', 'type'));
		$this->display('house_list');
	}
	
	/**
	 * 添加楼盘
	 */
	function add() {
		// placeId
		$id = $this->get('id');
		$fresh_type = $this->get('fresh_type');
		if ($id) {
			// 取出地点信息
			$query = $this->db->get_where('Place', array('id' => $id));
			$place = $query->row_array();
			$this->assign('poi', $place);
		
			// 取出地点的碎片数据
			$list = $this->db->select('*, ifnull(moduleId, 0) as moduleId', false)
				->order_by('rankOrder', 'desc')->from('PlaceOwnSpecialProperty')
				->where(array('placeId' => $id))->where_in('moduleId', $this->config->item('house_module_id'))->get()->result_array();
			$this->assign('properties', $list);
		
			// 选出地点分类
			$query = $this->db->select('id, content, isBrand')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$id}' and poc.placeCategoryId = pc.id")->get();
			$category = $query->result_array();
			$cate = array();
			$split_str = '';
			foreach ($category as $row) {
				$isBrand = $row['isBrand'];
				unset($row['isBrand']);
				if ($isBrand) {
					// 品牌分类
					$cate['brand'] = $row;
				} else {
					// 普通分类
					$cate['common'] = array(
							'id' => $cate['common']['id'] . $split_str . $row['id'],
							'content' => $cate['common']['content'] . $split_str . $row['content']
					);
					$split_str = ',';
				}
			}
			$this->assign('category', $cate);
		}
		
		if ($this->is_post() && $fresh_type!=="get") {
			$placename = trim($this->post('placename'));
			$poi_icon = $this->post('poi_icon');
			$poi_icon = $poi_icon?array_filter($poi_icon):array();
		
			$data = array(
					'placename' => $placename,
					'address' => $this->post('address'),
					'latitude' => floatval($this->post('latitude')),
					'longitude' => floatval($this->post('longitude')),
					'tel' => $this->post('tel'),
					'pcc' => $this->post('is_business') ? intval($this->post('pcc')) : 0,
					'isBusiness' => $this->post('is_business'),
					'icon' => $poi_icon[0],
					'isChecked' => 1,
					'isRepayPoint' => $this->post("isRepayPoint")
			);
		
			if($this->post("background")){
				$data['background'] = $this->post("background");
			}

			$b = true;
			// 修改
			$b_tip = false;
			if (empty($id)) {
				// 新建
				$b &= $this->db->insert('Place', $data);
				$id = $this->db->insert_id();
				$b_tip = true;
			} else {
				// 编辑
				$b &= $this->db->where('id', $id)->update('Place', $data);
				// 删除之前的分类关系
				$b &= $this->db->delete('PlaceOwnCategory', array('placeId' => $id));
			}

			// 添加关系
			$cats = array();
			$pcids = explode(',', $this->post('placeCategory_id'));
			foreach($pcids as $pcid) {
				$cats[] = array(
						'placeId' => $id,
						'placeCategoryId' => $pcid
				);
			}
			$brand_cat_id = $this->post('placeBrand_id');
			if ($brand_cat_id) {
				$cats[] = array(
						'placeId' => $id,
						'placeCategoryId' => $brand_cat_id
				);
			}
			$b &= $this->db->insert_batch('PlaceOwnCategory', $cats);
		
			// 处理碎片模型的数据
			$this->load->helper('poi');
			$b &= handle_house_block_data($id, $this->page_id, $b_tip);

			if ($b) {
				// 更新缓存
				// 更新地点缓存
				$api_rtn1 = api_update_cache('Place', $id);
				// 更新分类关系
				$api_rtn2 = api_update_cache('PlaceCategoryShip');
				// 更新网站的缓存
				update_cache('web', 'data', 'place', $id);
				get_data("loupan",true);
		
				send_api_interface('/private_api/place/update_solr', 'POST', array('place_id' => $id));
		
				$this->load->helper('search');
				// 更新地点
				@update_index(20, $id);
				// 看是否为楼盘
				if(100 == $cats[0]['placeCategoryId']) {
					@update_index(10, $id);
				}
				
				// 去更新团房数据 
				$tf_id = sync_house_info($id);
		
				// 通过最后一次判断是否成功
				$this->success(($b_tip ? '新建' : '修改') . '楼盘成功', '', '', 'closeCurrent', array('p1'=>$api_rtn1, 'p2'=>$api_rtn2, 'tf_id' => $tf_id));
			} else {
				$this->success(($b_tip ? '新建' : '修改') . '楼盘失败,请重试');
			}
		}
				
		$this->display('house_add');
	}
	
	function edit() {
		$this->add();
	}
	
	/**
	 * 地点的碎片数据添加
	 */
	function block() {
		// 属性的ID号
		$id = $this->get('id');
		$page_id = $this->get('page_id');
		$place_id = $this->get('place_id');
		$module_id = $this->get('module_id');
		$property_id = $this->get('property_id');
		$block_id = $this->get('block_id');
	
		// 获取所有的可以用的模型
		$module_list = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceModule')->result_array();
	
		// 获取碎片数据
		$property = $this->db->get_where('PlaceOwnSpecialProperty', array('id' => $property_id))->row_array();
		$property['images'] = explode(',', $property['images']);
	
		$this->assign(compact('id', 'page_id', 'place_id', 'module_id', 'property_id', 'block_id', 'module_list', 'property'));
	
		$this->display('house_module_block');
	}
	
	/**
	 * 地点碎片模型数据
	 */
	function block_module() {
		$page_id = $this->get('page_id');
		$place_id = $this->get('place_id');
		$module_id = $this->get('module_id');
		$block_id = $this->get('block_id');
		$property_id = $this->get('property_id');
	
		if($module_id > 0) {
			// 获取模型
			$module = $this->db->get_where('PlaceModule', array('id' => $module_id))
			->row_array();
			if(empty($module)) {
				$this->error('错误');
			}
	
			// 选出模型的字段数据
			$query = $this->db->order_by('orderValue', 'asc')->get_where('PlaceModuleField', array('moduleId' => $module_id));
			$fields = $query->result_array();
	
			if ($place_id) {
				// 获取模型的排序
				$row = $this->db->get_where('PlaceOwnModule', array('placeModuleId' => $module_id, 'placeId' => $place_id))
				->row_array();
				$module['rankOrder'] = $row['rankOrder'];
				// 如果传递了POI的ID号，那么去获取POI的模型数据
				$query = $this->db->get_where('PlaceModuleData', array('placeId' => $place_id, 'moduleId' => $module_id));
				$module_data = $query->result_array();
				$data = array();
				foreach ($module_data as $row) {
					$data[$row['fieldId']]['value'] = $row['mValue'];
					$data[$row['fieldId']]['isVisible'] = $row['isVisible'];
				}
				$this->assign('data', $data);
			}
		} else {
			$module = $fields = array();
			// 根据property_id
			if($property_id > 0) {
				$place_property = $this->db->get_where('PlaceOwnSpecialProperty', array('id' => $property_id))->row_array();
				$this->assign('place_property', $place_property);
			}
		}
		$rich_image_fields = $this->config->item('rich_image');
		array_shift($rich_image_fields);
		$rich_image_fields = json_encode($rich_image_fields);
		$this->assign(compact('page_id', 'place_id', 'module_id', 'block_id', 'module', 'fields', 'rich_image_fields'));
	
		$this->display('house_block_module');
	}
	
	/**
	 * 改变状态
	 */
	function change_status() {
		$id = $this->get('id');
		$status = $this->get('status');
		$type = $this->get('type');
	
		if (!in_array($status, array(
				0,
				1,
				2
		))) {
			$this->error('错误的状态');
		}
	
		// 修改状态
		if ($this->db->where('id', $id)->update('Place', array('status' => $status))) {
			// 更新缓存
			// 更新地点缓存
			api_update_cache('Place', $id);
	
			$this->load->helper('search');
			// 更新地点
			@update_index(20, $id, $status<2?'update':'delete');
			if($type == 100){
				@update_index(10, $id, $status<2?'update':'delete');
			}
			
			// 去更新团房数据
			$tf_id = sync_house_info($id);
	
			$this->success('更新楼盘状态成功', '', '', '', array(
					'id' => $id,
					'key' => $status,
					'value' => $this->poi_status[$status],
					'tf_id' => $tf_id
			));
		} else {
			$this->error('更新POI状态失败');
		}
	}
	
	/**
	 * 添加团购
	 */
	function add_group() {
		$house_id = intval($this->get('id'));
		$house = $this->tfdb->get_where('house', array('house_id' => $house_id))->row_array();
		
		if(empty($house)) {
			$this->error('错误的楼盘');
		}
		
		if($this->is_post()) {
			// 提交
			$data = array(
					'has_group' => intval($this->post('status')),
					'group_title' => trim($this->post('title')),
					'group_detail' => $this->post('detail'),
					'group_expire_date' => $this->post('expire_date'),
					'group_dateline' => now(),
					'group_count' => intval($this->post('count'))
					);
			if($data['has_group']) {
				if(strtotime($data['group_expire_date']) <= TIMESTAMP) {
					// 过期时间到了，那么过期
					$data['group_status'] = 1;
				} else {
					$data['group_status'] = 0;
				}
			} else {
				$data['group_status'] = 2;
			}
			
			if($this->tfdb->where(array('house_id' => $house_id))->update('house', $data)) {
				$this->success('提交成功', '', '', 'closeCurrent');
			} else {
				$this->error('提交失败');
			}
		}
		
		foreach($house as $k=>$v) {
			$house[str_replace('group_', 'k_', $k)] = $v;
		}
		$this->assign('data', $house);
		
		$this->display('add_group');
	}
	
	/**
	 * 添加推荐
	 */
	function add_recommend() {
		$house_id = intval($this->get('id'));
		$house = $this->tfdb->get_where('house', array('house_id' => $house_id))->row_array();
		
		if(empty($house)) {
			$this->error('错误的楼盘');
		}
		
		if($this->is_post()) {
			// 提交
			$data = array(
					'has_recommend' => intval($this->post('status')),
					'recommend_title' => trim($this->post('title')),
					'recommend_detail' => $this->post('detail'),
					'recommend_expire_date' => $this->post('expire_date'),
					'recommend_dateline' => now()
			);
			if($data['has_recommend']) {
				if(strtotime($data['recommend_expire_date']) <= TIMESTAMP) {
					// 过期时间到了，那么过期
					$data['recommend_status'] = 1;
				} else {
					$data['recommend_status'] = 0;
				}
			} else {
				$data['recommend_status'] = 2;
			}
				
			if($this->tfdb->where(array('house_id' => $house_id))->update('house', $data)) {
				$this->success('提交成功', '', '', 'closeCurrent');
			} else {
				$this->error('提交失败');
			}
		}
		
		foreach($house as $k=>$v) {
			$house[str_replace('recommend_', 'k_', $k)] = $v;
		}
		$this->assign('data', $house);
		
		$this->display('add_recommend');
	}
	
	/**
	 * 推荐列表 
	 */
	function recommend() {
		
		
		$this->display('recommend_list');
	}
	
	/**
	 * 待审核
	 */
	function recommend_precheck() {
		$keywords = trim($this->post('keywords'));
		
		$where_arr = array(
			'status = 0',
			'type = 2'
		);
		if($keywords !== '') {
			$where_arr[] = "id = '{$keywords}'";
			$keywords = dstripslashes($keywords);
		}
		$where_sql = join(' and ', $where_arr);
		$total_num = $this->tfdb->where($where_sql)->count_all_results('order');
		$paginate = $this->paginate($total_num);
		
		$list = $this->tfdb->where($where_sql, null ,false)->order_by('dateline', 'desc')
			->limit($paginate['per_page_num'], $paginate['offset'])->get('order')->result_array();
		
		$this->assign(array(
			'page_rel' => $this->page_id,
			'list' => $list,
			'keywords' => $keywords
		));
		$this->display('recommend_precheck');
	}
	
	/**
	 * 已审核推荐成功的
	 */
	function recommend_checked() {
		$keywords = trim($this->post('keywords'));
		
		$where_arr = array(
				'status = 1',
				'type = 2'
		);
		if($keywords !== '') {
			$where_arr[] = "id = '{$keywords}'";
			$keywords = dstripslashes($keywords);
		}
		$where_sql = join(' and ', $where_arr);
		$total_num = $this->tfdb->where($where_sql)->count_all_results('order');
		$paginate = $this->paginate($total_num);
		
		$list = $this->tfdb->where($where_sql, null ,false)->order_by('last_dateline', 'desc')
		->limit($paginate['per_page_num'], $paginate['offset'])->get('order')->result_array();
		
		$this->assign(array(
				'page_rel' => $this->page_id,
				'list' => $list,
				'keywords' => $keywords
		));
		$this->display('recommend_checked');
	}
	
	/**
	 * 处理为已成交
	 */
	function recommend_todeal($id = 0) {
		$order = $this->tfdb->get_where('order', array('id' => $id))->row_array();
		if(empty($order) || $order['status'] != 1 || $order['type'] != 2) {
			$return?false:$this->error('错误的推荐订单');
		}
		
		// 处理提交给佣金
		if($this->is_post() || $return) {
			$commision = intval(trim($this->post('commision')));
			if($commision <= 0) {
				$this->error('错误的佣金，必须大于0');
			}
			$dateline = now();
			$order['comments'] = json_decode($order['comments'], true);
			if(!is_array($order['comments'])) {
				$order['comments'] = array();
			}
			// 去处理
			$b = $this->tfdb->insert('commision', array(
					'order_id' => $id,
					'handler' => $this->auth['id'],
					'money' => $commision,
					'name' => $order['recommend_name'],
					'cellphone_no' => $order['recommend_cellphone_no'],
					'is_new' => 1
			));
			
			$serial_number = $this->tfdb->insert_id();
			$order['comments']['commision'] = array(
					'comment' => sprintf($this->lang->line('recommend_commision'),
							$order['name'], $order['cellphone_no'], $order['house_name'],
							$commision),
					'dateline' => $dateline,
					'handler' => $this->auth['id'],
					'handler_name' => $this->auth['name'],
					'money' => $commision,
					'serial_number' => $serial_number
			);
			$data = array(
					'is_new' => 1,
					'last_dateline' => $dateline,
					'status' => 4,
					'comments' => encode_json($order['comments'])
			);

			if($b) {
				$b = $this->tfdb->where(array('id' => $id))->update('order', $data);
				if($b) {
					// 添加佣金记录及加用户佣金
					if($b) {
						// 更新用户的佣金
						$b = $this->tfdb->where(array('cellphone_no' => 
								$order['recommend_cellphone_no']))
								->set('commision', "commision+".$commision, false)->update('user');
					}
				}
			}
			
			$b?$this->success('操作奖励佣金成功', '', '', 'closeCurrent', array('id' => $id)):$this->error('操作奖励佣金失败');
		}
		
		$this->assign(array('order' => $order));
		
		$this->display('recommend_todeal');
	}
	
	/**
	 * 成交
	 */
	function recommend_deal() {
		$keywords = trim($this->post('keywords'));
		
		$where_arr = array(
				'a.status = 4',
				'a.type = 2',
				'a.id = b.order_id'
		);
		if($keywords !== '') {
			$where_arr[] = "a.id = '{$keywords}'";
			$keywords = dstripslashes($keywords);
		}
		$where_sql = join(' and ', $where_arr);
		$total_num = $this->tfdb->where($where_sql)->count_all_results('order a, commision b');
		$paginate = $this->paginate($total_num);
		
		$list = $this->tfdb->where($where_sql, null ,false)
			->select('a.*, b.id as commision_id, b.money', false)
			->order_by('a.last_dateline', 'desc')
			->limit($paginate['per_page_num'], $paginate['offset'])
			->get('order a, commision b')->result_array();
		
		$this->assign(array(
				'page_rel' => $this->page_id,
				'list' => $list,
				'keywords' => $keywords
		));
		$this->display('recommend_deal');
	}
	
	/**
	 * 所有的推荐
	 */
	function recommend_all() {
		$house_order_status = $this->config->item('house_order_status');
		$this->assign('house_order_status', $house_order_status);
		if($this->is_post()) {
			$keywords = trim($this->post('keywords'));
			$type = $this->post('type');
			$house = trim($this->post('house'));
			$start_date = $this->post('start_date');
			$end_date = $this->post('end_date');
			$status = $this->post('status');
			$recommend_cellphone_no = trim($this->post('recommend_cellphone_no'));
			$cellphone_no = trim($this->post('cellphone_no'));
			
			$where_arr = array(
				'type = 2'
			);
			if($keywords !== '') {
				$where_arr[] = "id = '{$keywords}'";
				$keywords = dstripslashes($keywords);
			}
			if($house !== '') {
				if($type == 1) {
					// ID
					$where_arr[] = "house_id = '{$house}'";
				} else {
					$where_arr[] = "house_name like '%{$house}%'";
				}
				$house = dstripslashes($house);
			}
			if($start_date) {
				$where_arr[] = "dateline >= '{$start_date}'";
			}
			if($end_date) {
				$where_arr[] = "dateline <= '{$end_date}'";
			}
			if($recommend_cellphone_no) {
				$where_arr[] = "recommend_cellphone_no = '{$recommend_cellphone_no}'";
			}
			if($cellphone_no) {
				$where_arr[] = "cellphone_no = '{$cellphone_no}'";
			}
			if($status) {
				$where_arr[] = "status = '".($status - 1)."'";
			}
			
			$where_sql = join(' and ', $where_arr);
			$total_num = $this->tfdb->where($where_sql)->count_all_results('order');
			$paginate = $this->paginate($total_num);
			
			$list = $this->tfdb->where($where_sql, null ,false)->order_by('last_dateline', 'desc')
				->order_by('dateline', 'desc')
				->limit($paginate['per_page_num'], $paginate['offset'])->get('order')->result_array();
			
			$this->assign('page_rel', $this->page_id);
			$this->assign(compact('keywords', 'status', 'type', 'house', 
					'start_date', 'end_date', 'recommend_cellphone_no', 'cellphone_no', 'list'));
		}
		$this->display('recommend_all');
	}
	
	/**
	 * 批量通过审核
	 */
	function recommend_batchpass() {
		$id = $this->post('id');
		$ids = explode(':', $id);
		$ids = array_unique(array_filter($ids));
		
		if(empty($ids)) {
			$this->error('提交错误');
		}
		
		$success_id = array();
		foreach($ids as $id) {
			// 一条一条执行
			$b = $this->recommend_pass($id, 1);
			if($b) {
				$success_id[] = $b;
			}
		}
		
		$success_id?$this->success('成功批量审核通过', '', '', '', array('id' => $success_id)):$this->error('批量审核失败');
	}
	
	/**
	 * 通过审核
	 */
	function recommend_pass($id = 0, $return = '') {
		$order = $this->tfdb->get_where('order', array('id' => $id))->row_array();
		if(empty($order) || $order['status'] != 0 || $order['type'] != 2) {
			$return?false:$this->error('错误的推荐订单');
		}
		
		// 查询楼盘信息
		$house = $this->tfdb->get_where('house', array('house_id' => $order['house_id']))->row_array();
		$comment = sprintf($this->lang->line('recommend_pass'), $order['recommend_name'], 
				$order['house_name'], $house['price'], $house['area'], $house['loopline'], 
				$house['has_group']?sprintf($this->lang->line('recommend_group'), $house['group_title']):'');
		// 发送短信息
		$rtn_obj = send_sms($order['cellphone_no'], $comment);
		$b = true;
		if($rtn_obj->resultCode) {
			// 出错了
			$b = false;
		} else {
			$dateline = now();
			// 去处理
			$data = array(
					'is_new' => 1,
					'last_dateline' => $dateline,
					'status' => 1,
					'comments' => encode_json(array(
							'pass' => array(
									'comment' => $comment,
									'dateline' => $dateline,
									'handler' => $this->auth['id'],
									'handler_name' => $this->auth['name']
							)))
			);
			$b = $this->tfdb->where(array('id' => $id))->update('order', $data);
		}
		if($return) {
			return $b?$id:false;
		} else {
			$b?$this->success('审核成功', '', '', 'closeCurrent', array('id' => $id)):$this->error('审核失败');
		}
	}
	
	/**
	 * 驳回推荐
	 */
	function recommend_reject($id = 0, $return = '') {
		$order = $this->tfdb->get_where('order', array('id' => $id))->row_array();
		if(empty($order) || $order['status'] != 0 || $order['type'] != 2) {
			$return?false:$this->error('错误的推荐订单');
		}
		
		// 处理驳回
		if($this->is_post() || $return) {
			$comment = trim($this->post('comment'));
			$dateline = now();
			// 去处理
			$data = array(
				'is_new' => 1,
				'last_dateline' => $dateline,
				'status' => 2,
				'comments' => encode_json(array(
				'reject' => array(
				'comment' => $comment?$comment:'你推荐的朋友不是该楼盘的有效新客户',
				'dateline' => $dateline,
				'handler' => $this->auth['id'],
				'handler_name' => $this->auth['name']
			)))
			);
			$b = $this->tfdb->where(array('id' => $id))->update('order', $data);
			if($return) {
				return $b?$id:false;
			} else {
				$b?$this->success('驳回成功', '', '', 'closeCurrent', array('id' => $id)):$this->error('驳回失败');
			}
		}
		
		$this->display('recommend_reject');
	}
	
	/**
	 * 导出订单
	 */
	function recommend_export() {
		if($this->is_post()) {
			$start_date = $this->post('start_date');
			$end_date = $this->post('end_date');
			$t1 = strtotime($start_date);
			$t2 = strtotime($end_date);
			
			if($t2 <= $t1) {
				$this->error('请选择正确的时间');
			}
			
			$this->success('', '', '', '', array('start_date' => $t1, 
					'end_date' => $t2, 'status' => $status));
		} else {
			$start_date = $this->get('start_date');
			$end_date = $this->get('end_date');
			if($start_date && $end_date) {
				$start_date = now(0, $start_date);
				$end_date = now(0, $end_date);
				
				$list = $this->tfdb->where("status='0' and type='2' and dateline 
					between '{$start_date}' and '{$end_date}'", null, false)
					->order_by('house_id', 'desc')->order_by('dateline', 'desc')->get('order')->result_array();
				
				$filename = "{$start_date}~{$end_date} 的所有等待审核的订单";
				
				header('Content-type: application/vnd.ms-excel; charset=utf-8');
				header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
				
				$str = "推荐订单号\t创建时间\t推荐楼盘\t推荐用户\r\n";
				
				foreach ($list as $row) {
					$str .= sprintf("%s\t%s\t%s(%s)\t%s(%s)\r\n", $row['id'], $row['dateline'], $row['house_name'], 
					$row['house_id'], $row['cellphone_no'], $row['name']);
				}
				
				$str .= $filename;
				
// 				echo mb_convert_encoding($str, 'GBK', 'utf-8');
				echo $str;
				
				exit();
			}
		}
		
		$this->display('recommend_export');
	}
}
