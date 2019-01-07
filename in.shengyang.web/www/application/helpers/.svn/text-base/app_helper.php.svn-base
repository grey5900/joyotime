<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
  * 应用的helper
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-2-25
  */

/**
 * 返回rank order
 * @param int $item_type
 * @param int $item_id
 */
function get_ext_rank_order($item_type, $item_id) {
    $CI = &get_instance();
    // 1	活动	权重加成 + 报名人数 x 10
    // 2	图片POST	权重加成 + (赞+踩) x 3 + 分享 x 7
    // 3	商品	权重加成 + 购买数 x 7 + 分享 x 3
    // 4	地点	权重加成 + 点评数 x 4 + 地点册数 x 2 + 地主抢劫数 x 2
    // 5	地点册	权重加成 + (赞+踩) x 3 + 分享数 x 5 + 收藏数 x 2
    $rank_order = 0;
    switch($item_type) {
        case 1:
            // 地点
            $row = $CI->db->get_where($CI->_tables['place'],
                                array('id' => $item_id))->row_array();
            $rank_order = intval($row['tipCount'])*4 +
            intval($row['atCollectionCount'])*2 +
            intval($row['robCount'])*2;
            break;
        case 5:
            // 活动 活动参与人数
            $num = $CI->db->from($CI->_tables['webeventapply'])
                            ->where(array('eventId' => $item_id))->count_all_results();
            $rank_order = $num * 10;
            break;
        case 18:
        case 19:
            // YY和POST
            $row = $CI->db->get_where($CI->_tables['post'],
                                    array('id' => $item_id))->row_array();
            $rank_order = intval($row['praiseCount'])*3 +
            intval($row['stampCount'])*3 +
            intval($row['shareCount'])*7;
            break;
        case 20:
            // 地点册
            $row = $CI->db2->get_where($CI->_tables['placecollection'],
                            array('id' => $item_id))->row_array();
            $rank_order = intval($row['praiseCount'])*3 +
            intval($row['stampCount'])*3 +
            intval($row['shareCount'])*5 +
            intval($row['beFavorCount'])*2;
            break;
        case 23:
            // 商品
            // 购买数
            $row = $CI->db->get_where($CI->_tables['product'],
                            array('id' => $item_id))->row_array();
            $rank_order = intval($row['buyerCount'])*7 +
            intval($row['shareCount'])*3;
            break;
    }

    return $rank_order;
}
