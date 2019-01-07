<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-26
 */

/**
 * 获取碎片数据根据频道ID号
 * @param unknown_type $cid
 */
function get_fragment_by_cid($cid) {
    $CI = &get_instance();
    
    $newscategory = get_inc('newscategory');
    $category = $newscategory[$cid];
   
    $CI->load->model('webrecommenddata_model', 'm_webrecommenddata');
    $fids = explode(',', $category['fragmentId']);
    $list = $CI->m_webrecommenddata->list_in_fragmentid_order_ordervalue($fids);
    
    $data = array();
    foreach($list as $row) {
        $row['extraData'] = decode_json($row['extraData']);
        $data[$row['fragmentId']][$row['orderValue']] = $row;
    }
    unset($list);

    return $data;
}