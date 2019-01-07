<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 团购数据
 * 需要获取那个站外团购信息，添加function
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-28
 */

class Goods_Api extends MY_Controller {
    function get_teams($team_name = '') {
        $source_types = $this->config->item('source_type');
        $source_type = $source_types[$team_name];
        if($source_type) {
            $this->load->helper('goods');
            list($name, $key, $url) = get_tg_setting($team_name);
            
            $orz = time();
            $sign = md5($orz . $key);
            $get_url = sprintf($url, 'get_teams', $orz, $sign);
            $json = http_request($get_url, array(), array(), 'GET', true);
    
            empty($json) && $this->error('获取[' . $name . ']数据错误', '', '', '', array('value' => $get_url));
            $data = json_decode($json, true);
            $data = daddslashes($data);
            // 哎，这里不想改下面的代码了。只好做个处理。还是处理成对象
            $data = json_decode(json_encode($data));
            $batch_data = array();
            $b = true;
            if ($data->flag) {
                foreach ($data->data as $row) {
                    // 先判断记录是否已经存在
                    $r = $this->db->get_where('GrouponSourceItem', "originalId = '{$row->id}' and grouponName = '{$name}'")->row_array();
                    if(empty($r)) {
                        // 不存在
                        $row_data = array();
                        
                        $row_data['originalId'] = $row->id;
                        $row_data['title'] = $row->title;
                        $row_data['area'] = $row->area;
                        $row_data['category'] = $row->group;
                        $row_data['partnerTitle'] = $row->partner_title;
                        $row_data['partnerLocation'] = $row->partner_location;
                        $row_data['partnerHomepage'] = $row->partner_homepage;
                        $row_data['teamPrice'] = $row->team_price;
                        $row_data['marketPrice'] = $row->market_price;
                        $row_data['product'] = $row->product;
                        $row_data['perNumber'] = $row->per_number?$row->per_number:999;
                        $row_data['minNumber'] = $row->min_number;
                        $row_data['maxNumber'] = $row->max_number;
                        $row_data['nowNumber'] = $row->now_number;
                        $row_data['image'] = $row->image;
                        $row_data['imageThumb'] = $row->image_thumb;
                        $row_data['detail'] = $row->detail;
                        $row_data['expireTime'] = standard_date('YmdHis', $row->expire_time);
                        $row_data['beginTime'] = standard_date('YmdHis', $row->begin_time);
                        $row_data['endTime'] = standard_date('YmdHis', $row->end_time);
                        $row_data['closeTime'] = $row->close_time?$row->close_time:0;
                        $row_data['grouponName'] = $name;
                        $row_data['status'] = 0;
                        $row_data['teamUrl'] = $row->team_url;
                        $row_data['notice'] = strip_tags($row->notice);
                        
                        // 新加入的
                        $row_data['sourceType'] = intval($source_type);
                        $row_data['teamType'] = intval($row->team_type);
                        $row_data['stock'] = intval($row->stock);
                        $row_data['pickupLocation'] = $row->pickup_location;
                        $row_data['soldLimit'] = intval($row->sold_limit);
                        
                        $batch_data[] = $row_data;
                    }
                }
            } else {
                $b = false;
            }
    
            if($batch_data) {
                $b = $this->db->insert_batch('GrouponSourceItem', $batch_data);
            }
            
            $b?$this->success("获取[{$name}]数据成功", build_rel(array('goods', 'groupon')), site_url(array('goods', 'groupon')), '', array('url' => $get_url)):$this->error("获取[{$name}]数据出错啦，我猜应该是进入数据出错啦", '', '', '', array('url' => $get_url));
        }
    }
}
