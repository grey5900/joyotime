<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 推荐的一些方法
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-19
 */

/**
 * 获取数据
 * @param $fid 可以是一串数字用,隔开
 */
function get_recommend_data($fids = array(), $auto_update = false) {
    global $CI;

    $list = $CI->db->order_by('fid', 'asc')->order_by('serialNo', 'asc')
                   ->where("fid in ('".implode("','", $fids)."')" . ($auto_update?' and unix_timestamp() between startTime and endTime ':''))
                   ->get('RecommendData')
                   ->result_array();
    $data = array();
    if($list) {
        $fragment = get_data('fragment');
        foreach($list as $row) {
            if($fragment[$row['fid']]['rewrite']) {
                // 得到转向的地址
                $row['titleLink'] && $row['titleLink'] = site_url(array('redirect', urlencode($row['titleLink'])));
                $row['imageLink'] && $row['imageLink'] = site_url(array('redirect', urlencode($row['imageLink'])));
                $row['categoryLink'] && $row['categoryLink'] = site_url(array('redirect', urlencode($row['categoryLink'])));
                $row['authorLink'] && $row['authorLink'] = site_url(array('redirect', urlencode($row['authorLink'])));
            }
            $row['title'] = preg_replace("/\[color=(.+?)\](.+?)\[\/color\]/is", "<font color=\\1>\\2</font>", $row['title']);
            $row['title'] = preg_replace("/\[b\](.+?)\[\/b\]/is", "<b>\\1</b>", $row['title']);
            $data[$row['fid']][$row['serialNo']] = $row;
        }
    }
    
    return $data;
}
