<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 团房的一些函数
 */
	
/**
 * 数据操作IN成都数据对象
 */
function get_tfdb() {
	$CI = &get_instance();
	return $CI->load->database('tfdb', true);
}

/**
 * 获取楼盘的所有信息
 * @param int $house_id
 */
function get_house_detail_all($house_id) {
	$CI = &get_instance();
	// 获取所有的楼盘的详情fields
	$rows = $CI->db->query("SELECT fieldId, fieldName, fieldType
			FROM PlaceModuleField WHERE moduleId in (28, 29)")->result_array();
	$data = $fields = array();
	foreach($rows as $row) {
		$fields[] = $row['fieldId'];
		$data[str_replace('v-', '', $row['fieldId'])]['field'] = array('fieldType' => $row['fieldType'], 'fieldName' => $row['fieldName']);
	}

	$rows = $CI->db->select('fieldId, mValue')->where(array('placeId' => $house_id))
		->where_in('fieldId', $fields)
		->get('PlaceModuleData')->result_array();
	foreach($rows as $row) {
		$value = json_decode($row['mValue'], true);
		$data[str_replace('v-', '', $row['fieldId'])]['value'] = $value?$value:$row['mValue'];
	}

	return $data;
}


/**
 * 同步楼盘数据
 * @param int $house_id
 */
function sync_house_info($house_id) {
	$db = get_tfdb();
	$CI = &get_instance();
	
	// 查询POI数据
	$poi = $CI->db->get_where('Place', array('id' => $house_id))->row_array();
	// 查询POI的扩展数据
	$ext_info = get_house_detail_all($house_id);
	
	$sale = $ext_info['indoor']['value'];
	$data = array(
			'house_id' => $house_id,
			'name' => $poi['placename'],
			'address' => $poi['address'],
			'lat' => $poi['latitude'],
			'lng' => $poi['longitude'],
			'area' => strval($ext_info['ownarea']['value']),
			'price' => intval($ext_info['avgprice']['value']),
			'direction' => strval($ext_info['biz']['value']),
			'loopline' => strval($ext_info['towards']['value']),
			'cover' => image_url(strval($ext_info['cover']['value']), 'default'),
			'status' => $poi['status'],
			'sale_status' => $sale?($sale=='待售'?0:($sale=='在售'?1:2)):1
			);
	
	// 查询是否已经有了
	$house = $db->get_where('house', array('house_id' => $house_id))->row_array();
	if($house) {
		// 修改
		$id = $house['id'];
		$db->where(array('id' => $house['id']))->update('house', $data);
	} else {
		// 添加
		$db->insert('house', $data);
		$id = $db->insert_id();
	}
	
	return $id;
}

/**
 * 处理地点的扩展碎片数据
 * @param $b_edit true 新建 false 更新
 */
function handle_house_block_data($place_id, $page_id, $b_edit) {
	global $CI;
	// 获取地点的扩展属性数据
	$post = $CI->post(null);
	// 排序值
	$orders = $post['order'][$page_id];
	// 模型ID号
	$module_ids = $post['module_id'][$page_id];
	// 标题
	$titles = $post['title'][$page_id];
	// 内容
	$contents = $post['content'][$page_id];
	// 图片
	$images = $post['image'][$page_id];
	// 样式
	$styles = $post['style'][$page_id];

	// 循环处理所有的扩展碎片数据
	$module_data = $new_propertis = $update_properties = $update_ids = array();
	if($orders) {
		foreach($orders as $key => $order) {
			// 检查数据
			$title = $titles[$key];
			$content = $contents[$key];
			$image = $images[$key];
			$module_id = intval($module_ids[$key]);
			
			if(empty($title) && empty($content) && empty($image)) {
				// 如果都是空的话。跳过
				continue;
			}

			// 根据样式类型保存图片
			$style = intval($styles[$key]);
			$img = array_filter(array_values($image));
			$imgs = array();
			if($style > 1) {
				// 4张图
				$imgs = $img;
			} else {
				// 1张图
				$img[0] && $imgs[] = $img[0];
			}

			$property = array(
					'placeId' => $place_id,
					'title' => $title,
					'content' => $content,
					'rankOrder' => intval($orders[$key]),
					'moduleId' => $module_id,
					'images' => implode(',', $imgs),
					'style' => $style
			);

			if($module_id > 0) {
				// 获取模型
				$fields = $CI->db->get_where('PlaceModuleField', array('moduleId' => $module_id))->result_array();
				foreach($fields as $field) {
					// 去获取每个字段的信息
					$value = $post['data'][$page_id][$key][$field['fieldId']];
					$value_depth = array_depth($value);
					if($value_depth == 1) {
						$value = implode(',', $value);
					} elseif($value_depth > 1) {
						$value = encode_json($value);
						if($field['fieldType'] == 'rich_image') {
							$value = encode_json(json2json($value));
						}
					}
					empty($value) && ($value = '');

					$is_visible = $post['visiable'][$page_id][$key][$field['fieldId']];
					$module_data[] = array(
							'fieldId' => $field['fieldId'],
							'placeId' => $place_id,
							'moduleId' => $module_id,
							'mValue' => $value,
							'isVisible' => intval($is_visible)
					);
				}
				
				$hyper_link = sprintf($CI->config->item('web_site').'/i/place/%s/%s', $place_id, $module_id);
			} elseif($module_id < 0) {
				// 获取连接
				$hyper_link = $post['hyper_link'][$page_id][$key];
			} else {
				$hyper_link = '';
			}

			$property_id = intval($post['property_id'][$page_id][$key]);
			$property['hyperLink'] = $hyper_link?$hyper_link:'';

			if($property_id) {
				// 有属性ID那么，放入更新的数组
				$property['id'] = $property_id;
				$update_ids[] = $property_id;
				$update_propertis[] = $property;
			} else {
				$new_propertis[] = $property;
			}
		}
	}
	$b = true;

	// 更新碎片
	$update_propertis && $CI->db->update_batch('PlaceOwnSpecialProperty', $update_propertis, 'id');
	// 添加新的碎片
	$new_propertis && ($b &= $CI->db->insert_batch('PlaceOwnSpecialProperty', $new_propertis));

	// 先删除之前的模型数据
	$b &= $CI->db->where_in('moduleId', $CI->config->item('house_module_id'))->delete('PlaceModuleData', array('placeId' => $place_id));
	// 添加模型数据
	$module_data && ($b &= $CI->db->insert_batch('PlaceModuleData', $module_data));

	return $b;
}

/**
 * 去刷新状态
 */
function flush_status($house_id = 0) {
	// 那么去更新状态吧，这里暂时不需要去缓存什么的，因为现在量不大，去更新吧，不影响的
	$where_arr = array();
	if($house_id) {
		// 只更新一个楼盘
		$where_arr[] = "house_id = '{$house_id}'";
	}
	
	$today_date = now(1, TIMESTAMP);
	$db = get_tfdb();
	foreach(array('group', 'recommend') as $type) {
		foreach(array(0, 1) as $status) {
			$arr = $where_arr;
			$arr[] = "has_{$type} = 1";
			$arr[] = "{$type}_status = {$status}";
			$arr[] = "{$type}_expire_date ".($status?'>=':'<')." '{$today_date}'";
	
			$db->where(join(" and ", $arr))
				->update('house', array("{$type}_status" => $status?0:1));
		}
	}
}

/**
 * 发送短信息
 */
function send_sms($cellphone_no, $msg) {
	$CI = &get_instance();
	$config_sms = $CI->config->item('sms');
	$soap = new SoapClient($config_sms['url']);
	return $soap->__soapCall('SendSmS', array(
			'UserName' => $config_sms['user'],
			'UserPwd' => $config_sms['pass'],
			'TimeStamp' => now(3),
			'SendMobile' => $cellphone_no,
			'SendMsg' => $msg
	));
}

