<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * poi操作先关的一些函数
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-10-15
 */

/**
 * 处理地点的扩展碎片数据
 * @param $b_edit true 新建 false 更新
 */
function handle_block_data($place_id, $page_id, $b_edit) {
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
//                 $hyper_link = base_url("main/place/{$place_id}/{$module_id}");
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

    // 操作属性表
    // 先删除已经删除的ID
    $update_ids && $CI->db->where_not_in('id', $update_ids);
    $b = $CI->db->delete('PlaceOwnSpecialProperty', array('placeId' => $place_id));

    // 更新碎片
    $update_propertis && $CI->db->update_batch('PlaceOwnSpecialProperty', $update_propertis, 'id');
    // 添加新的碎片
    $new_propertis && ($b &= $CI->db->insert_batch('PlaceOwnSpecialProperty', $new_propertis));
    
    // 先删除之前的模型数据
    $b &= $CI->db->delete('PlaceModuleData', array('placeId' => $place_id));
    // 添加模型数据
    $module_data && ($b &= $CI->db->insert_batch('PlaceModuleData', $module_data));
    
    return $b;
}

/**
 * 更新模型模板
 * @param $id 模型ID号
 */
function update_module_template($id) {
    global $CI;
    
    $module = $CI->db->get_where('PlaceModule', array('id' => $id))->row_array();
    $fields = $CI->db->order_by('orderValue', 'asc')->get_where('PlaceModuleField', array('moduleId' => $id))->result_array();
    
    foreach($fields as $row) {
        if($row['fieldType'] == "rewrite") {
            // 如果有跳转的字段
            $has_rewrite = $row['fieldId'];
            break;
        }
    }
    
    if($has_rewrite) {
        $CI->assign('has_rewrite', $has_rewrite);
    } else {
        $CI->assign('fields', $fields);
        $CI->assign('css', $module['css']);
    }
    $html = $CI->fetch($module['template'], 'module_template');

    @file_put_contents($CI->config->item('mod_c_path') . '/module_' . $module['id'] . '.html', str_replace(array(
            '##',
            '__'
    ), array(
            '$',
            ''
    ), $html));
}

