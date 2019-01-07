<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 订单操作的一些公用函数
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-10-30
 */

/**
 * 订单当前配送状态
 * @param $is_payed 是否支付 0：未支付 1：已支付
 * @param $ship_type 配送类型 0：电子券 1：自提 2：配送
 * @param $ship_status 配送状态  配送类型为1和2的时候才有效
 * 为1  0-备货中 1-等待自提 2-已自提(完成)
 * 为2  0-备货中 2-已发货(完成) 
 * @param $status 0：正常 1：已作废 2：未发货 (掉接口失败)
 */
function order_ship_status($is_payed, $ship_type, $ship_status, $status = 0) {
    global $CI;
    if(empty($is_payed)) {
        $order_status = $CI->config->item('order_status');
        return $order_status[$status];
    }
    
    static $ship_status_name = null;
    empty($ship_status_name) && ($ship_status_name = $CI->config->item('ship_status'));    
    
    return $ship_status_name[$ship_type][$ship_type?$ship_status:$status];
}
