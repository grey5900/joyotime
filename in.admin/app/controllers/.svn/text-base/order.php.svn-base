<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 订单管理
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-8
 */

class Order extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->helper('order');
    }
    
    /**    
     * 已支付
     */
    function paid() {
        $this->_list(1);
    }
    
    /**
     * 未支付
     */
    function non_payment() {
        $this->_list(0);
    }
    
    /**
     * 列表
     * @param $is_payed  0 未支付 1 已支付
     */
    private function _list($is_payed) {
        $item_type = $this->post('item_type');
        $type = $this->post('type');
        $keywords = $this->post('keywords');

        $item_type || $item_type = 0;
        $type || $type = 'id';

        $where_sql = 'o.uid = u.id and o.isPayed = ' . $is_payed;

        if ($keywords) {
            $keytext = addslashes($keywords);
            switch($type) {
                case 'itemTitle' :
                    $where_sql .= " and o.itemTitle like '%{$keytext}%'";
                    break;
                case 'nickname' :
                    $where_sql .= " and u.nickname = '{$keytext}'";
                    break;
                default :
                    $where_sql .= " and o.id = '{$keytext}'";
            }
        }

        $item_type && $where_sql .= " and o.itemType = '{$item_type}'";

        $total_num = $this->db->where($where_sql)->from('OrderInfo o, User u')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select('o.*, u.nickname, p.price, p.additionalFee, gi.grouponPrice, gi.sourceType')
                     ->order_by('o.createDate', 'desc')
                     ->limit($paginate['per_page_num'], $paginate['offset'])
                     ->where($where_sql)
                     ->join('Product p', 'p.id = o.itemId and o.itemType = 13', 'left')
                     ->join('GrouponItem gi', 'gi.id = o.itemId and o.itemType = 12', 'left')
                     ->get('OrderInfo o, User u')->result_array();
        $ship_type = $this->config->item('ship_type');
        $source_type_name = $this->config->item('source_type_name');
        foreach ($list as &$row) {
            // if ($row['itemType'] == 12) {
                // // 团购
                // $row['goodsMoney'] = $row['quantity'] * $row['grouponPrice'];
                // $row['extMoney'] = 0;
            // } elseif ($row['itemType'] == 13) {
                // // 电影票
                // $row['goodsMoney'] = $row['quantity'] * $row['price'];
                // $row['extMoney'] = $row['quantity'] * $row['additionalFee'];
            // }
            $row['shipTypeName'] = $ship_type[$row['shipType']];
            $source_name = $source_type_name[$row['sourceType']];
            $row['sourceName'] = empty($row['price'])?($source_name?$source_name:'章鱼团'):'电影票';
            $row['shipStatusName'] = order_ship_status($row['isPayed'], $row['shipType'], $row['shipStatus'], $row['status']);
        }
        unset($row);
        $this->assign('order_status', $this->config->item('order_status'));
        $this->assign(compact('item_type', 'type', 'keywords', 'list'));

        $this->display($is_payed?'paid':'non_payment');
    }

    /**
     * 查看订单详情
     */
    function detail() {
        $id = intval($this->get('id'));

        ($id <= 0) && $this->error('错误');
        
        // 查询订单
        $order = $this->db->select('o.*, u.id as uid, u.username, u.cellphoneNo as cellphone, 
                        u.nickname, p.price, p.additionalFee, gi.grouponPrice,
                        gi.sourceName, gi.originalId, pl.placename as pickupPlaceName')
                      ->join('Product p', 'p.id = o.itemId and o.itemType = 13', 'left')
                      ->join('GrouponItem gi', 'gi.id = o.itemId and o.itemType = 12', 'left')
                      ->join('Place pl', 'o.pickupPlace = pl.id', 'left')
                      ->get_where('OrderInfo o, User u', "o.uid = u.id and o.id = '{$id}'")
                      ->row_array();
                      
        if(empty($order)) {
            // 错误的信息
            $this->error('错误订单信息');
        }

        $pay_way = $this->config->item('pay_way');
        $order['payWayName'] = $pay_way[$order['payWay']];

        // 如果订单状态为付款。那么去获取付款时间
        // if($order['isPayed']) {
        // $row = $this->db->get_where('OrderPayLog', "orderId = '{$id}'")->row_array();
        // $order['payDate'] = $row['createDate'];
        // }
        
        $ship_type = $this->config->item('ship_type');
        $order['shipTypeName'] = $ship_type[$order['shipType']];

        // 查询商品的来源信息
        if ($order['itemType'] == 12) {
            // 团购
            $order['sourceName'] = $order['sourceName'];
            $order['sourceId'] = $order['originalId'];
            
            // 商品总金额
            $order['goodsMoney'] = $order['quantity'] * $order['grouponPrice'];
            // 附加费用
            $order['extMoney'] = 0;
            // 单价
            $order['price'] = $order['grouponPrice'];
        } elseif ($order['itemType'] == 13) {
            // 电影票
            $order['sourceName'] = $this->lang->line('goods_source_name');
            // 商品金额
            $order['goodsMoney'] = $order['quantity'] * $order['price'];
            // 附加费用
            $order['extMoney'] = $order['quantity'] * $order['additionalFee'];
        }
        $this->assign('ship_status', $this->config->item('ship_status'));
        $this->assign('order_status', $this->config->item('order_status'));
        $this->assign('is_payed', $order['isPayed']);
        $this->assign('order', $order);

        $this->display('new_detail');
    }

    /**
     * 关闭订单，作废订单
     */
    function destory() {
        $id = $this->get('id');

        $id || $this->error('错误');

        $b = $this->db->where("id = '{$id}'")->update('OrderInfo', array('status' => 1));
        $b ? $this->success('作废订单成功', build_rel(array(
                'order',
                'non_payment'
        )), site_url(array(
                'order',
                'non_payment'
        ))) : $this->error('作废订单出错');
    }

    /**
     * 添加备注
     */
    function add_remark() {
        $id = $this->get('id');
        $order = $this->db->get_where('OrderInfo', array('id' => $id))->row_array();

        if (empty($order)) {
            $this->error('错误的订单');
        }

        if ($this->is_post()) {
            // 添加备注提交
            $remark = $this->post('remark');
            $b = $this->db->where(array('id' => $id))->update('OrderInfo', array('remark' => $remark));

            $b ? $this->success('更新订单备注成功', '', '', 'closeCurrent') : $this->error('提交订单备注失败');
        }

        $this->assign('order', $order);

        $this->display('add_remark');
    }
    
    /**
     * 删除订单
     */
    function del() {
        $id = intval($this->get('id'));
        $type = $this->get('type');

        ($id <=0) && $this->error('错误');

        $b = $this->db->where("id = '{$id}'")->update('OrderInfo', array('isDelete' => 1));
        $b ? $this->success('删除订单成功', build_rel(array(
                'order',
                $type?'paid':'non_payment'
        )), site_url(array(
                'order',
                $type?'paid':'non_payment'
        ))) : $this->error('删除订单出错');
    } 
}
