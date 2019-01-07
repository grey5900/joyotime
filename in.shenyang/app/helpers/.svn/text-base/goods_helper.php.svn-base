<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 团购的helper
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-7-3
 */

/**
 * 获取指定团购的信息
 * 
 */
function get_team($id, $source_name) {
    global $CI;
    // 获取配置列面对应的配置地址
    $groupon_source = $CI->config->item('groupon_source');
    $data_name = $groupon_source[$source_name];
    list($name, $key, $url) = get_tg_setting($data_name);
    
    // 需要的参数
    $orz = time();
    $sign = md5($orz . $key);
    $get_url = sprintf($url, 'get_team', $orz, $sign);
    $json = http_request($get_url, array('id'=>$id), array(), 'GET', true);
    
    if($json) {
        $data = json_decode($json, true);
        if($data['flag']) {
            $data = $data['data'];
            // 去更新数据表中的数据 GrouponItem
            $update_data = array(
                'expireDate' => dt($data['expire_time']),
                'startDate' => dt($data['begin_time']),
                'endDate' => dt($data['end_time']),
                'sourceStatus' => $data['close_time']?0:1
            );
            
            $CI->db->where(array('originalId' => $id, 'sourceName' => $source_name))->update('GrouponItem', $update_data);
            return $update_data;
        }
    }
}

/**
 * 获取配置
 */
function get_tg_setting($data_name) {
    $setting = get_data('common_setting');
    
    $name = $setting['name_' . $data_name];
    $url = $setting['url_' . $data_name];
    $key = $setting['key_' . $data_name];
    
    return array($name, $key, $url);
}



/**
 * 生成静态页面
 * @param $id ID号
 * @param $t groupon 团购 product 商品
 */
function generate_static_html($id, $t = 'groupon') {
    global $CI;
    $dir = intval($id / 1000);
    $path = $CI->config->item('html_path') . $t . '/' . $dir;

    $b = true;
    if (!file_exists($path)) {
        $b = mkdirs($path);
    }

    if ($b) {
        // 生成文件
        // 获取详细信息
        $html = @file_get_contents(site_url(array(
                'main',
                'goods',
                $id,
                $t == 'groupon' ? '1' : '0'
        )));
        $b = @file_put_contents($path . '/' . $id . '.html', $html);
    }

    return $b;
}

