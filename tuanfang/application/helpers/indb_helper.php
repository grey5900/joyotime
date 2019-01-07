<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * 操作IN成都数据的助手
 */

/**
 * 数据操作IN成都数据对象
 */
function get_indb() {
	$CI = &get_instance();
	return $CI->load->database('indb', true);
}

/**
 * 获取扩展信息
 * @param 字段 $fields
 */
function get_module_data($house_id, $fields) {
	$db = get_indb();
	$field_str = join("', '", $fields);
	$rows = $db->query(sprintf("SELECT fieldId, fieldName FROM 
			PlaceModuleField WHERE fieldId IN ('%s') ORDER BY %s", 
			$field_str, sprintf("field(fieldId, '%s')", $field_str)))->result_array();
	$data = array();
	foreach($rows as $row) {
		$data[$row['fieldId']]['name'] = $row['fieldName'];
	}

	$rows = $db->select('fieldId, mValue')->where(array('placeId' => $house_id))
	->where_in('fieldId', $fields)
	->get('PlaceModuleData')->result_array();
	foreach($rows as $row) {
		$data[$row['fieldId']]['value'] = $row['mValue'];
	}
	
	return $data;
}

/**
 * 获取楼盘的所有信息
 * @param int $house_id
 */
function get_house_detail_all($house_id) {
	$db = get_indb();
	// 获取所有的楼盘的详情fields
	$rows = $db->query("SELECT fieldId, fieldName, fieldType 
			FROM PlaceModuleField WHERE moduleId in (28, 29)")->result_array();
	$data = $fields = array();
	foreach($rows as $row) {
		$fields[] = $row['fieldId'];
		$data[str_replace('v-', '', $row['fieldId'])]['field'] = array('fieldType' => $row['fieldType'], 'fieldName' => $row['fieldName']);
	}
	
	$rows = $db->select('fieldId, mValue')->where(array('placeId' => $house_id))
		->where_in('fieldId', $fields)
		->get('PlaceModuleData')->result_array();
	foreach($rows as $row) {
		$data[str_replace('v-', '', $row['fieldId'])]['value'] = $row['mValue'];
	}
	
	// 去获取楼盘的扩展信息ID号
	$rows = $db->select('id, moduleId')->where_in('moduleId', array(28, 29))
		->where('placeId', $house_id)->get('PlaceOwnSpecialProperty')
		->result_array();
	foreach($rows as $row) {
		if($row['moduleId'] == 28) {
			$data['detail_ext_id'] = $row['id'];
		} else {
			$data['pic_ext_id'] = $row['id'];
		}
	}
	
	return $data;
}

/**
 * 获取楼盘详细信息
 * @param int $house_id 楼盘ID placeId
 */
function get_house_detail($house_id) {
	$CI = &get_instance();
	$config_detail = $CI->config->item('detail');
	$config_album = $CI->config->item('album');
	$config_more = $CI->config->item('detail_more');
	$fields = array_merge($config_detail, $config_album, $config_more);
	
	$rows = get_module_data($house_id, $fields);
	$data = array();
	foreach($rows as $key => $row) {
		$row['value'] = trim($row['value']);
		if ($row['value'] == '') {
			continue;
		}
		
		if(in_array($key, $config_detail)) {
			if($key == 'v-saletele') {
				$row['is_tel'] = true;
			}
			
			if ($key == 'v-dongtai') {
				$data['trends'] = $row;
			} elseif($key == 'v-avgprice') {
				$data['price'] = intval($row['value']);
			} else {
				$data['detail'][] = $row;
			}
		} else if(in_array($key, $config_album)) {
			$v = decode_json($row['value']);
			if ($v) {
				foreach ($v as $r) {
					$data['album'][str_replace('v-', '', $key)][] = image_url($r['image'], 'common');
				}
			}
		} else if(in_array($key, $config_more)) {
			$data['more'][] = $row;
		}
	}

	return $data;
}

