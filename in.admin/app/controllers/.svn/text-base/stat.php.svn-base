<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 统计
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-7-12
 */

class Stat extends MY_Controller {
    // 日志路径
    var $log_path = '';
    // 执行日志
    var $log_execute = '';
    
    var $deal_analysis_item;
    
    var $user_analysis_item;
    
    var $client_type;
    
    function __construct() {
        parent::__construct();

        $this->log_path = FCPATH . 'forbid/stat/';
        $this->log_execute = $this->log_path . 'execute.log';
        
        $this->deal_analysis_item = $this->config->item('deal_analysis_item');
        $this->user_analysis_item = $this->config->item('user_analysis_item');
        $this->client_type = $this->config->item('client_type');
        
        $this->assign('client_type', $this->client_type);
        $this->assign('deal_analysis_item', $this->deal_analysis_item);
        $this->assign('user_analysis_item', $this->user_analysis_item);
    }

    /**
     * 数据导入，需要手动执行
     */
    function index() {
        // 2012.09.07已废弃
        // if ($this->is_post()) {
            // // 提交执行
            // // 去读取这个执行日志文件数据
            // $data = $this->_read_exec_log($this->log_execute);
// 
            // if ('init' == $this->get('do')) {
                // $this->_echo_json($data);
            // }
// 
            // // 检查是否已经结束
            // if ($data['status'] == 2) {
                // // 已结束
                // // 那么开始执行一个最新的
                // // 检查是否有保存执行日志
                // $log_file = $this->log_path . strtotime($data['start_time']);
                // if (!file_exists($log_file)) {
                    // // 如果执行日志文件不存在，那么保存一个
                    // file_put_contents($log_file, json_encode($data));
                // }
                // // 初始化
                // $data = $this->_init_execute_log($data);
            // }
// 
            // // 具体去执行
            // $this->_echo_json($data['total_num'] ? $this->_execute($data) : $data);
        // }
// 
        // $this->display('index');
    }

    /**
     * 执行操作
     *
     * @param 带入执行日志内容
     */
    // function _execute($data) {
        // $t1 = microtime(true);
        // // 计算现在还有多少没有执行
        // $range_num = $data['per_exec_num'];
        // $num = $data['total_num'] - $data['executed_num'];
        // // 如果剩余执行的数量小于每次执行数
        // ($num < $data['per_exec_num']) && $range_num = $num;
        // for ($i = 0; $i < $range_num; $i++) {
            // // 先执行
            // // 出去一条connectlog日志
            // $log = $this->db->get_where('ConnectLog', array('id' => $data['current_id']))->row_array();
            // if ($log && $log['deviceCode']) {
                // $arr = array();
                // // if(strpos($log['deviceCode'], 'android') !== 0) {
                // // // 不是android的code处理下
                // // $splits = explode('-', $log['deviceCode']);
                // // $count = count($splits);
                // // if($count > 1) {
                // // // 那么
                // // $arr['deviceCode'] = $splits[$count - 1];
                // // } else {
                // // $arr['deviceCode'] = $log['deviceCode'];
                // // }
                // // } else {
                // // $arr['deviceCode'] = $log['deviceCode'];
                // // }
                // // 之前处理了想统一下设备码，最后发现原来很多没法统一，就换成和原来一直了
                // $arr['deviceCode'] = strtolower($log['deviceCode']);
                // $arr['deviceType'] = $log['deviceType'];
// 
                // // 判断这个deviceCode是否已经存在与表中
                // $stat_device = $this->db->where(array('deviceCode' => $arr['deviceCode']))->from('StatDevice')->get()->row_array();
                // if (empty($stat_device)) {
                    // // 如果没有的话执行
                    // $arr['createDate'] = $log['createDate'];
                    // // 第一次加入所以用连接的时间作为最后的时间
                    // $arr['lastDate'] = $log['createDate'];
                    // if ($log['ip']) {
                        // $arr['ip'] = $log['ip'];
                    // } else {
                        // // 如果不存在，那么去获取这个设备的一个用户ID中的IP
                        // $user = $this->db->order_by('id', 'asc')->get_where('User', "deviceCode = '{$log['deviceCode']}' and lastIp != ''")->row_array();
                        // $user && $arr['ip'] = $user['lastIp'];
                    // }
// 
                    // // IP的区域获取
                    // if ($arr['ip']) {
                        // // 如果IP存在，那么去获取IP地区
                        // $area = $this->_get_area($arr['ip']);
                        // $arr['province'] = $area['province'];
                        // $arr['city'] = $area['city'];
                        // $arr['ipArea'] = $area['name'];
                    // }
// 
                    // // 去获取渠道及渠道商
                    // $merchant = $this->db->get_where('MerchantInfo', array('code' => $log['channelId']))->row_array();
                    // if ($merchant) {
                        // $arr['channelId'] = $merchant['id'];
                        // $arr['merchantId'] = $merchant['parentId'];
                    // }
// 
                    // // 插入StatDevice表
                    // $this->db->insert('StatDevice', $arr);
                // } else {
                    // // 如果已经存在了该DEVICE
                    // $this->db->where(array('deviceCode' => $log['deviceCode']))->update('StatDevice', array('lastDate' => $log['createDate']));
                // }
            // }
// 
            // // 执行了一个
            // $data['executed_num'] += 1;
            // // 当前要执行的ID
            // $data['current_id'] += 1;
// 
            // if ($data['current_id'] >= $data['end_id']) {
                // $data['current_id'] = $data['end_id'];
                // break;
            // }
        // }
        // $t2 = microtime(true);
// 
        // // 执行时间加上本次执行时间
        // $t = number_format($t2 - $t1, 3);
        // $data['executed_time'] += $t;
        // // 剩余条数除以每次执行条数 ，然后乘上执行的时间
        // $data['remainning_time'] = number_format($t * ceil(($data['total_num'] - $data['executed_num']) / $data['per_exec_num']), 3);
        // // 执行百分比
        // $data['executed_percent'] = intval(100 * ($data['executed_num'] / $data['total_num']));
        // // 如果执行数大于等于总数那么执行完成
        // if ($data['executed_num'] >= $data['total_num']) {
            // $data['status'] = 2;
            // $log_file = $this->log_path . strtotime($data['start_time']);
            // file_put_contents($log_file, json_encode($data));
        // }
// 
        // // 写入文件
        // file_put_contents($this->log_execute, json_encode($data));
// 
        // return $data;
    // }

    /**
     * 初始化执行日志文件
     */
    // function _init_execute_log($data) {
        // // 获取这次需要处理的条数
        // $total_num = $this->db->where("id > '{$data['end_id']}'")->from('ConnectLog')->count_all_results();
// 
        // // 重置执行日志内容
        // $data['start_time'] = date('Y-m-d H:i:s');
        // $data['executed_time'] = 0;
        // // 默认一秒钟一条
        // $data['remainning_time'] = $total_num;
        // $data['total_num'] = $total_num;
        // $data['executed_num'] = 0;
        // $data['executed_percent'] = 0;
        // $data['start_id'] = $data['end_id'] + 1;
        // $data['end_id'] += $total_num;
        // $data['current_id'] = $data['start_id'];
        // $data['status'] = 1;
// 
        // // 写入文件
        // file_put_contents($this->log_execute, json_encode($data));
// 
        // return $data;
    // }

    // function _echo_json($array = array()) {
        // die(json_encode($array));
    // }

    /**
     * 读取执行日志文件
     * @param $log_file 日志文件
     * @return 返回一个数组
     */
    // function _read_exec_log($log_file) {
        // $return = array(
            // // 开始执行时间
                // 'start_time' => 0,
            // // 已执行时间
                // 'executed_time' => 0,
            // // 预计剩余时间
                // 'remainning_time' => 0,
            // // 总数量
                // 'total_num' => 0,
            // // 已执行数据量
                // 'executed_num' => 0,
            // // 执行百分比
                // 'executed_percent' => 0,
            // // 开始执行的ID号
                // 'start_id' => 0,
            // // 执行结束结束ID号
                // 'end_id' => 0,
            // // 当前已执行的ID号
                // 'current_id' => 0,
            // // 状态 1：正在执行 2：已完成
                // 'status' => 2,
            // // 每次执行条数
                // 'per_exec_num' => 30
        // );
// 
        // if (!is_dir($log_file)) {
            // // 如果不是目录
            // clearstatcache();
            // if (is_file($log_file)) {
                // // 如果文件存在
                // $content = file_get_contents($log_file);
                // $return = json_decode($content, true);
            // } else {
                // file_put_contents($log_file, json_encode($return));
            // }
        // }
// 
        // return $return;
    // }

    /**
     * 结算
     */
    function settle() {
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $keywords = trim($this->post('keywords'));
        $type = $this->post('type');

        $where_sql = array('c1.parentId > 0');

        if ($keywords !== '') {
            $keytext = daddslashes($keywords);
            switch($type) {
                case 'channel_name' :
                    $where_sql[] = "c1.name like '%{$keytext}%'";
                    break;
                case 'merchant_name' :
                    $where_sql[] = "c2.name like '%{$keytext}%'";
                    break;
                case 'channel_id' :
                    $where_sql[] = "c1.id = '{$keytext}'";
                    break;
                case 'merchant_id' :
                    $where_sql[] = "c1.parentId = '{$keytext}'";
                    break;
            }
        }

        $where_sql && $where_sql = implode(' and ', $where_sql);
        $total_num = $this->db->from('ChannelInfo c1')->join('ChannelInfo c2', 'c1.parentId = c2.id', 'inner')->where($where_sql)->count_all_results();

        $paginate = $this->paginate($total_num);

        $list = $this->db->select('c1.*, c2.name as merchantName')->from('ChannelInfo c1')
                         ->join('ChannelInfo c2', 'c1.parentId = c2.id', 'inner')->where($where_sql)
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->order_by('c1.parentId', 'asc')->get()->result_array();
        $data = $ids = array();
        foreach ($list as $row) {
            $row['totalCount'] = 0;
            $row['scCount'] = 0;
            $row['cdCount'] = 0;
            $row['custRatio'] = $row['ratio'];
            $row['coopStartTime'] = substr($row['startDate'], 0, 10);
            $data[$row['id']] = $row;
            $ids[] = $row['id'];
        }
        
        // 总的统计
        // 总的激活设备数
        $this->db->from('StatDeviceInfo sd');
        if('merchant_name' == $type) {
            $this->db->join('ChannelInfo c2', 'sd.merchantId = c2.id', 'inner');
        } else {
            $this->db->join('ChannelInfo c1', 'sd.channelId = c1.id', 'inner');
        }
        $total = $this->db->where($where_sql)->count_all_results();
        
        // 四川数
        $_where_sql = ($where_sql?($where_sql.' and '):'')."sd.province = '四川'";
        $this->db->from('StatDeviceInfo sd');
        if('merchant_name' == $type) {
            $this->db->join('ChannelInfo c2', 'sd.merchantId = c2.id', 'inner');
        } else {
            $this->db->join('ChannelInfo c1', 'sd.channelId = c1.id', 'inner');
        }
        $sc_num = $this->db->where($_where_sql)->count_all_results();
        
        // 成都数
        $this->db->from('StatDeviceInfo sd');
        if('merchant_name' == $type) {
            $this->db->join('ChannelInfo c2', 'sd.merchantId = c2.id', 'inner');
        } else {
            $this->db->join('ChannelInfo c1', 'sd.channelId = c1.id', 'inner');
        }
        $cd_num = $this->db->where(($where_sql?($where_sql.' and '):'')."sd.city = '成都市'")->count_all_results();
        
        // 结算数
        $sql = "SELECT SUM(num*ratio) AS num FROM (
                SELECT 
                COUNT(*) as num, c.ratio
                FROM StatDeviceInfo sd
                ".('merchant_name' == $type ? "INNER JOIN ChannelInfo c2 ON sd.merchantId = c2.id" : 
                "INNER JOIN ChannelInfo c1 ON sd.channelId = c1.id")."
                INNER JOIN ChannelInfo c ON sd.channelId = c.id
                WHERE {$_where_sql}
                GROUP BY sd.channelId
                ) a";
        $row = $this->db->query($sql)->row_array();
        $settle_num = $row['num'];

        if ($ids) {
            $where_sql = 'channelId in (' . implode(',', $ids) . ')';
            if ($start_date && $end_date) {
                $where_sql .= " and createDate between '{$start_date} 00:00:00' and '{$end_date} 23:59:59'";
            } elseif ($start_date) {
                $where_sql .= " and createDate >= '{$start_date} 00:00:00'";
            } elseif ($end_date) {
                $where_sql .= " and createDate <= '{$end_date} 23:59:59'";
            }

            // 计算总数
            $list = $this->db->select('channelId, count(*) as num')->from('StatDeviceInfo')->group_by('channelId')->where($where_sql)->get()->result_array();
            foreach ($list as $row) {
                $data[$row['channelId']]['totalCount'] = $row['num'];
            }

            // 计算四川数
            $list = $this->db->select('channelId, count(*) as num')->from('StatDeviceInfo')->group_by('channelId')->where($where_sql . " and province = '四川'")->get()->result_array();
            foreach ($list as $row) {
                $data[$row['channelId']]['scCount'] = $row['num'];
            }

            // 计算成都数
            $list = $this->db->select('channelId, count(*) as num')->from('StatDeviceInfo')->group_by('channelId')->where($where_sql . " and city = '成都市'")->get()->result_array();
            foreach ($list as $row) {
                $data[$row['channelId']]['cdCount'] = $row['num'];
            }
        }

        $this->assign(compact('keywords', 'start_date', 'end_date', 'type', 'data', 'total', 'sc_num', 'cd_num', 'settle_num'));

        $this->display('settle');
    }

    /**
     * 渠道统计详情
     */
    function channel_detail() {
        $id = $this->get('id');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');

        $channel = $this->db->select('c1.*, c2.name as merchantName')
                            ->from('ChannelInfo c1')
                            ->join('ChannelInfo c2', 'c1.parentId = c2.id', 'inner')
                            ->where(array('c1.id' => $id))->get()->row_array();
        $channel || $this->error('错误');
        
        strlen($channel['startDate']) && $channel['startDate'] = substr($channel['startDate'], 0, 10);
        
        $channel['totalCount'] = 0;
        $channel['scCount'] = 0;
        $channel['cdCount'] = 0;

        $where_sql = 'channelId = ' . $id;
        if ($start_date && $end_date) {
            $where_sql .= " and createDate between '{$start_date} 00:00:00' and '{$end_date} 23:59:59'";
        } elseif ($start_date) {
            $where_sql .= " and createDate >= '{$start_date} 00:00:00'";
        } elseif ($end_date) {
            $where_sql .= " and createDate <= '{$end_date} 23:59:59'";
        }

        // 计算总数
        $channel['totalCount'] = $this->db->from('StatDeviceInfo')->where($where_sql)->count_all_results();

        // 计算四川数
        $channel['scCount'] = $this->db->from('StatDeviceInfo')->where($where_sql . " and province = '四川'")->count_all_results();

        // 计算成都数
        $channel['cdCount'] = $this->db->from('StatDeviceInfo')->where($where_sql . " and city = '成都市'")->count_all_results();

        $paginate = $this->paginate($channel['totalCount']);

        $list = $this->db->limit($paginate['per_page_num'], $paginate['offset'])->order_by('createDate', 'desc')->get_where('StatDeviceInfo', $where_sql)->result_array();

        $this->assign(compact('channel', 'start_date', 'end_date', 'list'));

        $this->display('channel_detail');
    }

    function test() {

        var_dump($this->_get_area('118.113.57.56'));
        var_dump($this->_get_area('159.106.121.75'));
        var_dump($this->_get_area('221.212.177.97'));
        var_dump($this->_get_area('222.182.90.99'));
        var_dump($this->_get_area('202.175.101.99'));
        var_dump($this->_get_area('59.149.255.202'));
        var_dump($this->_get_area('163.19.9.247'));
        var_dump($this->_get_area('58.207.67.109'));
        var_dump($this->_get_area('124.31.4.88'));
        var_dump($this->_get_area('127.0.0.1'));

    }

    /**
     * 获取地区信息
     */
    // function _get_area($ip) {
        // // 获取名称
        // $name = convertip($ip);
// 
        // $area = array(
                // 'name' => $name,
                // 'province' => '',
                // 'city' => ''
        // );
// 
        // if (strpos($name, '-') === 0) {
            // // 错误的IP地址或没有查询到
            // return $area;
        // }
// 
        // $GLOBALS['district'] || $GLOBALS['district'] =
        // include_once (FCPATH . './forbid/stat/district.inc.php');
        // foreach ($GLOBALS['district'] as $key => $value) {
            // if (strpos($name, $key) === 0 || strpos($name . '省', $key) === 0 || strpos($name . '市', $key) === 0 || strpos($name . '区', $key) === 0) {
                // // 取到了省份
                // $area['province'] = $key;
                // // 然后获取市
                // if (strlen($name) > strlen($key)) {
                    // // 如果整个名字里面还包含市信息
                    // foreach ($value as $val) {
                        // if (strpos($name, $key . $val) === 0 || strpos($name, $key . '市' . $val) === 0 || strpos($name, $key . '省' . $val) === 0 || strpos($name, $key . '区' . $val) === 0) {
                            // $area['city'] = $val;
                            // break;
                        // }
                    // }
                // }
                // break;
            // }
        // }
// 
        // return $area;
    // }

    // 交易统计

    /**
     * 执行统计计算
     */
    function deal_stat_exec($d = '') {
        $date = $this->get('date');

        (empty($date) && empty($d)) && $this->error('错误');

        $dd = false;
        $dd = empty($date) ? $d : $date;

        $data0 = $data1 = array('statDate' => $dd);
        $data0['type'] = 0;
        $data1['type'] = 1;

        $date1 = $dd . ' 00:00:00';
        $date2 = $dd . ' 23:59:59';

        // $where_date = "ifnull(OrderPayLog.createDate, OrderInfo.createDate) between '{$date1}' and '{$date2}'";
        $where_date = "ifnull(payDate, createDate) between '{$date1}' and '{$date2}'";
        
        // 总订单数
        // $data0['orderCount'] = $this->db->where("$where_date and itemType = 12")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['orderCount'] = $this->db->where("$where_date and itemType = 13")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['orderCount'] = $this->db->where("$where_date and itemType = 12")->from('OrderInfo')->count_all_results();
        $data1['orderCount'] = $this->db->where("$where_date and itemType = 13")->from('OrderInfo')->count_all_results();

        // 统计非0元订单
        // $data0['orderCount1'] = $this->db->where("$where_date and itemType = 12 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['orderCount1'] = $this->db->where("$where_date and itemType = 13 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['orderCount1'] = $this->db->where("$where_date and itemType = 12 and money > 0")->from('OrderInfo')->count_all_results();
        $data1['orderCount1'] = $this->db->where("$where_date and itemType = 13 and money > 0")->from('OrderInfo')->count_all_results();
        
        // 统计未付款订单
        // $data0['nonPaymentCount'] = $this->db->where("$where_date and isPayed = 0 and itemType = 12")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['nonPaymentCount'] = $this->db->where("$where_date and isPayed = 0 and itemType = 13")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['nonPaymentCount'] = $this->db->where("$where_date and isPayed = 0 and itemType = 12")->from('OrderInfo')->count_all_results();
        $data1['nonPaymentCount'] = $this->db->where("$where_date and isPayed = 0 and itemType = 13")->from('OrderInfo')->count_all_results();

        // 统计非0未付款订单
        // $data0['nonPaymentCount1'] = $this->db->where("$where_date and isPayed = 0 and itemType = 12 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['nonPaymentCount1'] = $this->db->where("$where_date and isPayed = 0 and itemType = 13 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['nonPaymentCount1'] = $this->db->where("$where_date and isPayed = 0 and itemType = 12 and money > 0")->from('OrderInfo')->count_all_results();
        $data1['nonPaymentCount1'] = $this->db->where("$where_date and isPayed = 0 and itemType = 13 and money > 0")->from('OrderInfo')->count_all_results();

        // 统计已付款订单
        // $data0['paidCount'] = $this->db->where("$where_date and isPayed = 1 and itemType = 12")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['paidCount'] = $this->db->where("$where_date and isPayed = 1 and itemType = 13")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['paidCount'] = $this->db->where("$where_date and isPayed = 1 and itemType = 12")->from('OrderInfo')->count_all_results();
        $data1['paidCount'] = $this->db->where("$where_date and isPayed = 1 and itemType = 13")->from('OrderInfo')->count_all_results();

        // 统计非0已付款订单
        // $data0['paidCount1'] = $this->db->where("$where_date and isPayed = 1 and itemType = 12 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        // $data1['paidCount1'] = $this->db->where("$where_date and isPayed = 1 and itemType = 13 and money > 0")->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->count_all_results();
        $data0['paidCount1'] = $this->db->where("$where_date and isPayed = 1 and itemType = 12 and money > 0")->from('OrderInfo')->count_all_results();
        $data1['paidCount1'] = $this->db->where("$where_date and isPayed = 1 and itemType = 13 and money > 0")->from('OrderInfo')->count_all_results();

        // 统计售出数及金额 已付款的
        // $row = $this->db->select_sum('quantity')->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 12 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('quantity')->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 12 and isPayed = 1")->get()->row_array();
        $data0['saleCount'] = intval($row['quantity']);
        $data0['dealAmount'] = floatval($row['money']);
        // $row = $this->db->select_sum('quantity')->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 13 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('quantity')->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 13 and isPayed = 1")->get()->row_array();
        $data1['saleCount'] = intval($row['quantity']);
        $data1['dealAmount'] = floatval($row['money']);

        // 统计非0出售数 已付款的
        // $row = $this->db->select_sum('quantity')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 12 and money > 0 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('quantity')->from('OrderInfo')->where("$where_date and itemType = 12 and money > 0 and isPayed = 1")->get()->row_array();
        $data0['saleCount1'] = intval($row['quantity']);
        // $row = $this->db->select_sum('quantity')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 13 and money > 0 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('quantity')->from('OrderInfo')->where("$where_date and itemType = 13 and money > 0 and isPayed = 1")->get()->row_array();
        $data1['saleCount1'] = intval($row['quantity']);

        // payWay 0：支付宝 1：银联
        // FUCK 。哪个在数据库comment里面写的1：支付宝 2：银联。。。最后才发现不对

        // 统计支付宝金额 已付款的
        // $row = $this->db->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 12 and OrderInfo.payWay = 0 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 12 and OrderInfo.payWay = 0 and isPayed = 1")->get()->row_array();
        $data0['alipayAmount'] = floatval($row['money']);
        // $row = $this->db->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 13 and OrderInfo.payWay = 0 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 13 and OrderInfo.payWay = 0 and isPayed = 1")->get()->row_array();
        $data1['alipayAmount'] = floatval($row['money']);

        // 统计银联金额 已付款的
        // $row = $this->db->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 12 and OrderInfo.payWay = 1 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 12 and OrderInfo.payWay = 1 and isPayed = 1")->get()->row_array();
        $data0['chinapayAmount'] = floatval($row['money']);
        // $row = $this->db->select_sum('money')->from('OrderInfo')->join('OrderPayLog', 'OrderInfo.id = OrderPayLog.orderId', 'left')->where("$where_date and itemType = 13 and OrderInfo.payWay = 1 and isPayed = 1")->get()->row_array();
        $row = $this->db->select_sum('money')->from('OrderInfo')->where("$where_date and itemType = 13 and OrderInfo.payWay = 1 and isPayed = 1")->get()->row_array();
        $data1['chinapayAmount'] = floatval($row['money']);

        // 计算付款率 付款单数除以总单数
        // $data0['paymentRate'] = $data0['orderCount']?intval(10000*$data0['paidCount']/$data0['orderCount']):10000;
        // $data1['paymentRate'] = $data1['orderCount']?intval(10000*$data1['paidCount']/$data1['orderCount']):10000;

        $b = false;

        // 判断是insert还是update
        $row = $this->db->get_where('StatDeal', array(
                'statDate' => $dd,
                'type' => 0
        ))->row_array();
        if ($row) {
            $b = $this->db->where(array('id' => $row['id']))->update('StatDeal', $data0);
        } else {
            $b = $this->db->insert('StatDeal', $data0);
        }

        $row = $this->db->get_where('StatDeal', array(
                'statDate' => $dd,
                'type' => 1
        ))->row_array();
        if ($row) {
            $b &= $this->db->where(array('id' => $row['id']))->update('StatDeal', $data1);
        } else {
            $b &= $this->db->insert('StatDeal', $data1);
        }

        if ($date) {
            $b ? $this->success('成功') : $this->success('失败');
        } else {
            return $b;
        }
    }

    /**
     * 生成交易的统计数据
     */
    function deal_generate_data() {
        if ($this->is_post()) {
            $d = trim($this->get('d'));

            if (empty($d)) {
                $this->echo_json(array(
                        'code' => 1,
                        'message' => '请输入开始的年月日'
                ));
            }

            if (time() < strtotime($d)) {
                $this->echo_json(array(
                        'code' => 1,
                        'message' => '统计时间已经大于当前时间'
                ));
            }

            $b = $this->deal_stat_exec($d);

            if ($b) {
                $date = new DateTime($d);
                $date->modify('+1 day');
                $this->echo_json(array('date' => $date->format('Y-m-d')));
            } else {
                $this->echo_json(array(
                        'code' => 1,
                        'message' => '执行失败'
                ));
            }
        }

        $this->display('deal_generate');
    }

    /**
     * 交易统计
     */
    function deal_stat() {
        $today = date('Y-m-d');
        $date = new DateTime($today);
        $date->modify("-1 day");
        $yesterday = $date->format('Y-m-d');
        // 计算昨天和今天的
        $b = $this->deal_stat_exec($today);
        $b &= $this->deal_stat_exec($yesterday);

        $b || $this->error('统计出错啦');

        $this->display('deal_stat');
    }

    /**
     * 交易统计概况
     */
    function deal_stat_general() {
        $today = date('Y-m-d');
        $date = new DateTime($today);
        $date->modify("-1 day");
        $yesterday = $date->format('Y-m-d');

        // 查询出今天昨天数据
        $list = $this->db->where(array('statDate' => $today))->or_where(array('statDate' => $yesterday))->get('StatDeal')->result_array();

        $data = array();
        foreach ($list as $row) {
            $data[$row['statDate']][$row['type']] = $row;
        }

        // 统计所有的交易额
        $row = $this->db->select_sum('dealAmount')->get('StatDeal')->row_array();
        $amount = $row['dealAmount'];

        // 取出
        $total_num = $this->db->from('StatDeal')->where(array('type' => 0))->count_all_results();

        $paginate = $this->paginate($total_num);

        $arr = $this->db->from('StatDeal')->limit($paginate['per_page_num'] * 2, $paginate['offset'] * 2)->order_by('statDate', 'desc')->order_by('type', 'asc')->get()->result_array();

        $list = $tmp_list = array();
        foreach ($arr as $row) {
            $tmp_list[$row['statDate']][$row['type']] = $row;
        }

        foreach ($tmp_list as $d => $row) {
            $list[$d] || $list[$d] = array('date' => $d);
            foreach ($row as $t => $r) {
                foreach ($r as $k => $v) {
                    switch($k) {
                        case 'orderCount' :
                        case 'nonPaymentCount' :
                        case 'paidCount' :
                        case 'saleCount' :
                        case 'orderCount1' :
                        case 'nonPaymentCount1' :
                        case 'paidCount1' :
                        case 'saleCount1' :
                        case 'dealAmount' :
                        case 'alipayAmount' :
                        case 'chinapayAmount' :
                            $list[$d][$k] += $v;
                            break;
                    }
                }
            }

            $list[$d]['paidRate'] = $list[$d]['orderCount'] ? number_format(100 * $list[$d]['paidCount'] / $list[$d]['orderCount'], 2) : 0;
            $list[$d]['paidRate1'] = $list[$d]['orderCount1'] ? number_format(100 * $list[$d]['paidCount1'] / $list[$d]['orderCount1'], 2) : 0;
        }

        if ($this->is_post()) {
            $this->assign('no_zero', $this->post('no_zero'));
        } else {
            $this->assign('no_zero', 1);
        }

        $this->assign(compact('today', 'yesterday', 'data', 'amount', 'list'));

        $this->display('deal_general');
    }

    /**
     * 章鱼团每日明细
     */
    function deal_stat_groupon() {
        // 取出团购的信息
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $no_zero = $this->is_post() ? $this->post('no_zero') : 1;

        if ($this->is_post()) {
            $where_sql = 'type = 0';
            if ($start_date && $end_date) {
                $where_sql .= " and statDate between '{$start_date}' and '{$end_date}'";
            } elseif ($start_date) {
                $where_sql .= " and statDate >= '{$start_date}'";
            } elseif ($end_date) {
                $where_sql .= " and statDate <= '{$end_date}'";
            }

            $total_num = $this->db->from('StatDeal')->where($where_sql)->count_all_results();

            $paginate = $this->paginate($total_num);

            $list = $this->db->where($where_sql)->limit($paginate['per_page_num'], $paginate['offset'])->order_by('statDate', 'desc')->get('StatDeal')->result_array();

            foreach ($list as &$row) {
                $row['paidRate'] = $row['orderCount'] ? number_format(100 * $row['paidCount'] / $row['orderCount'], 2) : 0;
                $row['paidRate1'] = $row['orderCount1'] ? number_format(100 * $row['paidCount1'] / $row['orderCount1'], 2) : 0;
            }

            // 统计
            $stat = $this->db->select_sum('orderCount')->select_sum('orderCount1')->select_sum('paidCount')->select_sum('paidCount1')->select_sum('saleCount')->select_sum('saleCount1')->select_sum('dealAmount')->select_sum('alipayAmount')->select_sum('chinapayAmount')->where($where_sql)->get('StatDeal')->row_array();
        }

        $this->assign(compact('start_date', 'end_date', 'no_zero', 'list', 'stat'));

        $this->display('deal_groupon');
    }

    /**
     * 电影票每日明细
     */
    function deal_stat_cinema_ticket() {
        // 取出电影票的信息
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $no_zero = $this->is_post() ? $this->post('no_zero') : 1;

        if ($this->is_post()) {
            $where_sql = 'type = 1';
            if ($start_date && $end_date) {
                $where_sql .= " and statDate between '{$start_date}' and '{$end_date}'";
            } elseif ($start_date) {
                $where_sql .= " and statDate >= '{$start_date}'";
            } elseif ($end_date) {
                $where_sql .= " and statDate <= '{$end_date}'";
            }

            $total_num = $this->db->from('StatDeal')->where($where_sql)->count_all_results();

            $paginate = $this->paginate($total_num);

            $list = $this->db->where($where_sql)->limit($paginate['per_page_num'], $paginate['offset'])->order_by('statDate', 'desc')->get('StatDeal')->result_array();

            foreach ($list as &$row) {
                $row['paidRate'] = $row['orderCount'] ? number_format(100 * $row['paidCount'] / $row['orderCount'], 2) : 0;
                $row['paidRate1'] = $row['orderCount1'] ? number_format(100 * $row['paidCount1'] / $row['orderCount1'], 2) : 0;
            }

            // 统计
            $stat = $this->db->select_sum('orderCount')->select_sum('orderCount1')->select_sum('paidCount')->select_sum('paidCount1')->select_sum('saleCount')->select_sum('saleCount1')->select_sum('dealAmount')->select_sum('alipayAmount')->select_sum('chinapayAmount')->where($where_sql)->get('StatDeal')->row_array();

        }
        $this->assign(compact('start_date', 'end_date', 'no_zero', 'list', 'stat'));

        $this->display('deal_cinema_ticket');
    }

    /**
     * 回头客
     */
    function deal_stat_customer() {
        if ($this->is_post()) {
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');

            // 查询已付款的交易订单
            $where_sql = 'isPayed = 1';
            if ($start_date && $end_date) {
                $where_sql .= " and createDate between '{$start_date} 00:00:00' and '{$end_date} 23:59:59'";
            } elseif ($start_date) {
                $where_sql .= " and createDate >= '{$start_date} 00:00:00'";
            } elseif ($end_date) {
                $where_sql .= " and createDate <= '{$end_date}' 23:59:59";
            }
                        
            $data = $cates = array();
            for($i = 1; $i<=10; $i++) {
                if($i < 10) {
                    $having = "num = $i";
                    $name = $i . '次';
                } else {
                    $having = 'num >= 10';
                    $name = $i . '次及以上';
                }
                
                $sql = "SELECT COUNT(*) AS num FROM (SELECT COUNT(*) AS num FROM OrderInfo WHERE {$where_sql} GROUP BY uid HAVING {$having}) a";
                
                $row = $this->db->query($sql)->row_array();
                
                $cates[] = $name;
                $data[] = $row['num'];
            }
            $cates_str = "\"" . implode("\",\"", $cates) . "\"";
            $num_str = implode(',', $data);
            $this->assign(compact('start_date', 'end_date', 'cates_str', 'num_str'));
        }
        
        // if ('draw' == $this->get('do')) {
            // // 查询数据
            // $start_date = $this->get('start_date');
            // $end_date = $this->get('end_date');
//             
            // // 查询已付款的交易订单
            // $where_sql = 'isPayed = 1';
            // if ($start_date && $end_date) {
                // $where_sql .= " and createDate between '{$start_date}' and '{$end_date}'";
            // } elseif ($start_date) {
                // $where_sql .= " and createDate > '{$start_date}'";
            // } elseif ($end_date) {
                // $where_sql .= " and createDate < '{$end_date}'";
            // }
//             
            // // select count(*) from (select count(*) as num from OrderInfo where isPayed=1 group by uid having num = 1) a
//             
            // $data = array();
            // for($i = 1; $i<=10; $i++) {
                // if($i < 10) {
                    // $having = "num = $i";
                    // $name = $i . '次';
                // } else {
                    // $having = 'num >= 10';
                    // $name = $i . '次及以上';
                // }
//                 
                // $sql = "SELECT COUNT(*) AS num FROM (SELECT COUNT(*) AS num FROM OrderInfo WHERE {$where_sql} GROUP BY uid HAVING {$having}) a";
//                 
                // $row = $this->db->query($sql)->row_array();
//                 
                // $data[] = array($name, $row['num']);
            // }
            
            // $plot = get_phplot();
            // $plot->SetImageBorderType('plain');
            // $plot->SetPlotType('bars');
            // $plot->SetDataType('text-data');
            // $plot->SetDataValues($data);
            // $plot->SetTitle("{$start_date} 到 {$end_date}");
            // $plot->SetYTitle('人数');
            // $plot->SetXTitle('购买次数');
            // $plot->SetYTickLabelPos('none');
            // $plot->SetYTickPos('none');
            // $plot->SetYDataLabelPos('plotin');
//             
            // $plot->DrawGraph();
            // exit;
        // }

        $this->display('deal_return_customer');
    }

    /**
     * 趋势分析
     */
    function deal_stat_trend() {
        $no_zero = $this->is_post() ? $this->post('no_zero') : 1;
        
        if ($this->is_post()) {
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');
            $goods_type = $this->post('goods_type');
            $item = $this->post('item');
            $no_zero = $this->post('no_zero');
            
            if(empty($goods_type) || empty($item)) {
                // 如果类型为空 或者 项目为空
                $this->error('请选择商品类别和分析项目');
            }
            
            if($goods_type) {
                $goods_type_str = $split_str = '';
                foreach($goods_type as $t) {
                    $goods_type_str .= $split_str . 'goods_type[]=' . $t;
                    $split_str = '&';
                }
            }
            
            $where_sql = array();
            if ($start_date && $end_date) {
                $where_sql = "statDate between '{$start_date}' and '{$end_date}'";
            } elseif ($start_date) {
                $where_sql = "statDate >= '{$start_date}'";
            } elseif ($end_date) {
                $where_sql = "statDate <= '{$end_date}'";
            }
            
            $tmp_list = $this->db->where($where_sql)->order_by('statDate', 'asc')->order_by('type', 'asc')->get('StatDeal')->result_array();
            
            $list = $cates = array();
            $i = 0;
            $cates_str = '';
            foreach($tmp_list as $row) {
                if($i++ == 0) {
                    $t1 = $row['statDate'];
                } else {
                    $t2 = $row['statDate'];
                }
                
                // 计算 付款率
                $row['paidRate'] = $row['orderCount']?number_format(100*$row['paidCount']/$row['orderCount'], 2):0;
                $row['paidRate1'] = $row['orderCount1']?number_format(100*$row['paidCount1']/$row['orderCount1'], 2):0;
                
                $list[$row['statDate']][$row['type']] = $row;
                $cates[$row['statDate']] = 1;
            }
            $cates_str = "\"" . implode("\",\"", array_keys($cates)) . "\"";
            
            // $data = $date_list = array();
            // 是分析交易金额
            $is_amount = $item == 'dealAmount'?true:false;
            // 查询字段
            // 
            $field_ext = $is_amount?'':'1';
            $field = $item . $field_ext;
            // 是否分析付款率
            $is_rate = $item == 'paidRate'?true:false;
            $series = array();
            foreach($list as $date => $row) {
                // $r = array();
                foreach($goods_type as $type) {
                    // 判断选择了几个数据
                    switch($type) {
                        case '1':
                            // 章鱼团
                            $r = $row[0][$field];
                            $name = '章鱼团';
                            break;
                        case '2':
                            // 电影票
                            $r = $row[1][$field];
                            $name  = '电影票';
                            break;
                        case '10':
                            $name  = '总和';
                            // 总和
                            if($is_rate) {
                                // 重新计算
                                $order_count = $row[0]['orderCount'.$field_ext] + $row[1]['orderCount'.$field_ext];
                                if($order_count) {
                                    $paid_count = $row[0]['paidCount'.$field_ext] + $row[1]['paidCount'.$field_ext];
                                    $r = number_format(100*$paid_count/$order_count, 2);
                                } else {
                                    $r = 0;
                                }
                            } else {
                                $r = $row[0][$field] + $row[1][$field];
                            }
                            break;
                    }
                    $series[$name][$date]['timestamp'] = strtotime($date) * 1000 + 3600000*8;
                    $series[$name][$date]['value'] = $r;
                }
                
                // $data[] = $r;
            }
        }       
        $this->assign(compact('no_zero', 'start_date', 'end_date', 'goods_type', 'item', 'cates_str', 'series'));

        // if ('draw' == $this->get('do')) {
            // $start_date = $this->get('start_date');
            // $end_date = $this->get('end_date');
            // $goods_type = $this->get('goods_type');
            // $item = $this->get('item');
            // $no_zero = $this->get('no_zero');
//             
            // if(empty($goods_type) || empty($item)) {
                // // 如果类型为空 或者 项目为空
                // $this->error('请选择商品类别和分析项目');
            // }
//             
            // $where_sql = array();
            // if ($start_date && $end_date) {
                // $where_sql = "statDate between '{$start_date}' and '{$end_date}'";
            // } elseif ($start_date) {
                // $where_sql = "statDate > '{$start_date}'";
            // } elseif ($end_date) {
                // $where_sql = "statDate < '{$end_date}'";
            // }
//             
            // $tmp_list = $this->db->where($where_sql)->order_by('statDate', 'asc')->order_by('type', 'asc')->get('StatDeal')->result_array();
//             
            // $list = array();
            // $i = 0;
            // foreach($tmp_list as $row) {
                // if($i++ == 0) {
                    // $t1 = $row['statDate'];
                // } else {
                    // $t2 = $row['statDate'];
                // }
//                 
                // // 计算 付款率
                // $row['paidRate'] = $row['orderCount']?number_format(100*$row['paidCount']/$row['orderCount'], 2):0;
                // $row['paidRate1'] = $row['orderCount1']?number_format(100*$row['paidCount1']/$row['orderCount1'], 2):0;
//                 
                // $list[$row['statDate']][$row['type']] = $row;
            // }
//             
            // $data = $date_list = array();
            // // 是分析交易金额
            // $is_amount = $item == 'dealAmount'?true:false;
            // // 查询字段
            // // 
            // $field_ext = $is_amount?'':'1';
            // $field = $item . $field_ext;
            // // 是否分析付款率
            // $is_rate = $item == 'paidRate'?true:false;
            // $legend = array();
            // foreach($list as $date => $row) {
                // $r = array($date);
                // foreach($goods_type as $type) {
                    // // 判断选择了几个数据
                    // switch($type) {
                        // case '1':
                            // // 章鱼团
                            // $r[] = $row[0][$field];
                            // $legend[0] = '章鱼团';
                            // break;
                        // case '2':
                            // // 电影票
                            // $r[] = $row[1][$field];
                            // $legend[1] = '电影票';
                            // break;
                        // case '10':
                            // $legend[2] = '总和';
                            // // 总和
                            // if($is_rate) {
                                // // 重新计算
                                // $order_count = $row[0]['orderCount'.$field_ext] + $row[1]['orderCount'.$field_ext];
                                // if($order_count) {
                                    // $paid_count = $row[0]['paidCount'.$field_ext] + $row[1]['paidCount'.$field_ext];
                                    // $r[] = number_format(100*$paid_count/$order_count, 2);
                                // } else {
                                    // $r[] = 0;
                                // }
                            // } else {
                                // $r[] = $row[0][$field] + $row[1][$field];
                            // }
                            // break;
                    // }
                // }
//                 
                // $data[] = $r;
            // }
//             
            // $plot = get_phplot(800, 450);
            // $plot->SetImageBorderType('plain');
            // $plot->SetDataType('text-data');
            // $plot->SetYTitle('数量');
            // $plot->SetXTitle('日期');
            // $plot->SetDataValues($data);
            // $plot->SetYDataLabelPos('plotin');
            // // $plot->SetXDataLabelPos('none');
            // $plot->SetXDataLabelType('custom', 'plot_x_format');
            // $plot->SetLegend($legend);
            // $plot->SetTitle(($start_date?$start_date:$t1) . " 到 " . ($end_date?$end_date:$t2));
//             
            // $plot->DrawGraph();
//                         
            // exit;
        // }
        
        
        $this->display('deal_trend_analysis');
    }

    /**
     * 用户统计
     */
    function user() {

        $this->display('user_stat');
    }
    
    /**
     * 
     */
    function user_stat_general() {
        // 查询概况
        $today = date('Y-m-d');
        $date = new DateTime($today);
        $date->modify("-1 day");
        $yesterday = $date->format('Y-m-d');
        
        // 查询昨日数据。 分设备
        $list_yesterday = $this->db->get_where('StatUserDevice', array('statDate'=>$yesterday))->result_array();
        // 昨天的数据
        $yesterday_stat = $this->db->get_where('StatUserDeviceDay', array('statDate'=>$yesterday))->row_array();
        // 昨天的累计终端数
        $yesterday_stat['clientCount'] = $this->db->where("createDate <= '{$yesterday} 23:59:59'", null, false)
                                                  ->from('StatDeviceInfo')->count_all_results();
        
        // 查询所有数据
        // $list_all = $this->db->select('deviceType')->select_sum('newCount')
                         // ->select_sum('newUserCount')->select_sum('connectCount')
                         // ->select_sum('activeCount')->from('StatUserDevice')
                         // ->group_by('deviceType')
                         // ->get()->result_array();

        $list_stat = array();
        foreach($list_yesterday as $key => $row) {
            // 计算累计用户和累计客户端数
            $row['userCount'] = $this->db->from('User u, StatDeviceInfo sd')
                                  ->where("u.createDate <= '{$yesterday} 23:59:59' AND sd.deviceType = '{$row['deviceType']}' AND SUBSTRING_INDEX(u.deviceCode, '-', 3) = sd.deviceCode", null, false)
                                  ->count_all_results();
            $row['clientCount'] = $this->db->from('StatDeviceInfo')
                                  ->where("createDate <= '{$yesterday} 23:59:59' and deviceType = '{$row['deviceType']}'", null, false)
                                  ->count_all_results();
            
            $row['activeCount'] = $row['activeCount'];
            $row['connectCount'] = $row['connectCount'];
            
            $list_stat[$row['deviceType']] = $row;
            
            $yesterday_active_count += $row['activeCount'];
            $yesterday_connect_count += $row['connectCount'];
        }
        
        $yesterday_stat['activeCount'] = $yesterday_active_count;
        $yesterday_stat['connectCount'] = $yesterday_connect_count;
        
        $this->assign(array('stat'=>$yesterday_stat, 'list'=>$list_stat));
        
        $this->display('user_general');
    }

    /**
     * 用户统计明细
     */
    function user_stat_detail() {
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        
        $submit = $this->post('submit');

        if ($this->is_post()) {
            $where_sql = array();
            if ($start_date && $end_date) {
                $where_sql = "statDate between '{$start_date}' and '{$end_date}'";
            } elseif ($start_date) {
                $where_sql = "statDate >= '{$start_date}'";
            } elseif ($end_date) {
                $where_sql = "statDate <= '{$end_date}'";
            }
            $total_num = $this->db->from('StatUserDeviceDay')->where($where_sql)->count_all_results();

            $paginate = $this->paginate($total_num);

            $this->db->select('statDate')->select_sum('newCount')
                     ->select_sum('newUserCount')->select_sum('connectCount')
                     ->select_sum('activeCount')->select_sum('activeCount7')
                     ->select_sum('activeCount15')
                     ->where($where_sql)->group_by('statDate');
            ($submit != "export") && $this->db->limit($paginate['per_page_num'], $paginate['offset']);
            
            $list =  $this->db->order_by('statDate', 'desc')->get('StatUserDevice')->result_array();
            
            if(empty($end_date)) {
                // 去获取开始时间
                $row = $this->db->select_max('statDate')->from('StatUserDeviceDay')->get()->row_array();
                $end_date = $row['statDate'];
            }
            if(empty($start_date)) {
                // 去获取结束时间
                $row = $this->db->select_min('statDate')->from('StatUserDeviceDay')->get()->row_array();
                $start_date = $row['statDate'];
            }

            foreach($list as &$row) {
                $row['connectCount'] = $row['connectCount'];
                $row['activeCount'] = $row['activeCount'];
                $row['activeCount7'] = $row['activeCount7'];
                $row['activeCount15'] = $row['activeCount15'];
            }
            unset($row);
            
            // 去统计 启动次数 新增终端 新增注册用户
            $row = $this->db->query("SELECT SUM(connectCount) AS connectCount, 
                    SUM(newCount) AS newCount, SUM(newUserCount) AS newUserCount
                    FROM StatUserDevice 
                    WHERE statDate BETWEEN '" . dt2($start_date, 'Y-m-d') . "' 
                    AND '" . dt2($end_date, 'Y-m-d') . "'")->row_array();
            $stat['connectCount'] = $row['connectCount'];
            $stat['newCount'] = $row['newCount'];
            $stat['newUserCount'] = $row['newUserCount'];
            
            if($submit == "export"){
            		$filename = sprintf ( '用户统计-每日明细[%s TO %s]', $start_date , $end_date );
					header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
					header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
					
					$str = "{$start_date} 到 {$end_date}：启动次数：{$stat['connectCount']} 新增终端：{$stat['newCount']} 新增注册用户：{$stat['newUserCount']}\n";
					$str .= "日期\t活跃终端\t7日活跃终端\t15日活跃终端\t新终端\t新注册用户\n";
					
					foreach ( $list as $row )
					{
						$str .= "{$row['statDate']}\t{$row['activeCount']}\t{$row['activeCount7']}\t{$row['activeCount15']}\t{$row['newCount']}\t{$row['newUserCount']}\n";
						
					}
					
					echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
		           	exit;
            }
        }

        $this->assign(compact('start_date', 'end_date', 'list', 'stat'));
        
        $this->display('user_detail');
    }
    
    /**
     * 用户趋势分析
     */
    function user_stat_analysis() {
        if ($this->is_post()) {
            $start_date = $this->post('start_date');
            $end_date = $this->post('end_date');
            $item = $this->post('item');
            
            empty($item) && $this->error('请选择分析项目');
            
            $where_sql = array();
            if ($start_date && $end_date) {
                $where_sql = "statDate between '{$start_date}' and '{$end_date}'";
            } elseif ($start_date) {
                $where_sql = "statDate >= '{$start_date}'";
            } elseif ($end_date) {
                $where_sql = "statDate <= '{$end_date}'";
            }
            
            // $list = $this->db->select('statDate')->select_sum('newCount')
                                 // ->select_sum('newUserCount')->select_sum('connectCount')
                                 // ->select_sum('activeCount')->select_sum('activeCount7')
                                 // ->select_sum('activeCount15')
                                 // ->where($where_sql)->order_by('statDate', 'asc')
                                 // ->group_by('statDate')->get('StatUserDevice')->result_array();
            
            $list = $this->db->from('StatUserDeviceDay')->where($where_sql)->get()->result_array();
            
            $is_stat_count = false;
            if($start_date) {
                list($client_count, $user_count) = $this->_stat_total_count($start_date);
                $is_stat_count = true;
            }
            
            $series = array();
            $name = $this->user_analysis_item[$item];
            $len = count($list);
            for($i=0; $i<$len; $i++) {
                $row = $list[$i];
                $start_date || ($i==0 && $start_date = $row['statDate']);
                
                if($i === 0 && !$is_stat_count) {
                    // 如果第一次进入循环，并且没有执行过统计
                    list($client_count, $user_count) = $this->_stat_total_count($start_date);
                }
                
                $end_date || (($i+1)==$len && $end_date = $row['statDate']);
                // 计算总计终端
                $client_count += $row['newCount'];
                $row['clientCount'] = $client_count;
                
                // connectCount activeCount7
                $row['connectCount'] = $row['connectCount'];
                $row['activeCount7'] = $row['connectCount'];
                
                // 计算7天活跃率
                $row['activeRate7'] = $row['clientCount']?number_format(100*$row['activeCount7']/$row['clientCount'], 2):0;
                
                $series[$name][$row['statDate']] = array('timestamp' => strtotime($row['statDate'])*1000 + 3600000*8, 'value' => $row[$item]);
            }
        }
        $this->assign(compact('start_date', 'end_date', 'item', 'series'));
        
        $this->display('user_trend_analysis');
    }
    
    /**
     * 统计累计数
     */
    function _stat_total_count($stat_date) {
        $client_count = $this->db->from('StatDeviceInfo')
                                           ->where("createDate < '{$stat_date} 23:59:59'", null, false)
                                           ->count_all_results();
        $user_count = $this->db->from('User')->where("createDate < '{$stat_date} 23:59:59' and deviceCode is not null", null, false)
                                           ->count_all_results();
        
        return array($client_count, $user_count);
    }
    
    // /**
     // * 执行用户统计
     // * 格式 2012-12-12，可以从函数参数和GET中传递
     // */
    // function _user_stat_execute($d = '') {
        // $date = $this->get('date');
// 
        // (empty($date) && empty($d)) && $this->error('错误');
// 
        // $dd = false;
        // $dd = empty($date) ? $d : $date;
//         
        // $list = array();
        // $hours = range(0, 23);
        // foreach($hours as $hour) {
            // $tt = '0' . $hour;
            // $tt = strlen($tt) > 2?substr($tt, 1):$tt;
            // $t1 = "{$dd} {$tt}:00:00";
            // $t2 = "{$dd} {$tt}:59:59";
            // // 统计不同的平台 0：android 1：ios 2：wp
            // foreach(array(0, 1, 2) as $device_type) {
                // $row = array();
                // $row['statDate'] = $dd;
                // $row['hour'] = $hour;
                // $row['deviceType'] = $device_type;
                // // 查询新用户
            // }
        // }
    // }
    
    
    function user_device(){
    	if($this->is_post()){
    		$startDate = $this->post('startDate');
    		$endDate = $this->post('endDate');
    		//ConnectLog
    		if(!empty($startDate) || !empty($endDate)) 
    		{
	    		$where = "1=1 ";
	    		if($startDate){
	    			$where .= " and  createDate >= '{$startDate}'";
	    		}
	    		if($endDate){
	    			$where .= " and createDate <= '{$endDate}'";
	    		}
	    		//deviceCount
	    		$deviceCount = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(DISTINCT deviceCode) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)
		    					  ->get('ConnectLog')
		    					  ->result_array();	 		
		 		$userCount = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(DISTINCT uid) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)
		    					  ->where(" uid >0 ",null,false)
		    					  ->get('ConnectLog')
		    					  ->result_array();
		    	//新增设备号
		    	$newCount = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(DISTINCT id) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)			
		    					  ->get('StatDeviceInfo')
		    					  ->result_array();
		    	//新增用户
		    	$newUser = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(DISTINCT id) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)	
		    					  ->where('deviceType<3',null,false)			
		    					  ->get('User')
		    					  ->result_array();
		    	$newInviteUser = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(DISTINCT id) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)	
		    					  ->where('deviceType<3 and inviter >0',null,false)			
		    					  ->get('User')
		    					  ->result_array();
		   		$deviceLoginCount = $this->db->select('deviceType,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date,count(deviceCode) as total',false)
		    					  ->group_by('deviceType,date')
		    					  ->where($where,null,false)
		    					  ->where('uid>0',null,false)
		    					  ->get('ConnectLog')
		    					  ->result_array();
		  		
		    	$result = array(
		    		'deviceCount' 		=> $deviceCount,
		    		'userCount' 		=> $userCount,
		    		'newCount' 			=> $newCount,
		    		'newUser' 			=> $newUser, 
		    		'newInviteUser' 	=> $newInviteUser,
		    		'deviceLoginCount' 	=> $deviceLoginCount,
		    	);
		    	
		    	$data = array();
	    		foreach($result as $key => &$row){
		    		foreach($row as $k=>$r){
		    			/*if($k!=$r['deviceType']){
		    				unset($row[$k]);
		    			}*/
		    			//$key = $k;
		    			//$row[$r['date']] = $r;
		    			//unset($row[$key]);
		    			/*if(!$row[$r['date']]) { $row[$r['date']] = array() ;}
		    			array_push($row[$r['date']],$r);
		    			unset($row[$key]);*/
		    			//$data[$key]
		    			//if(!$data[$r['date']][$key][$r['deviceType']]) { $data[$r['date']][$key][$r['deviceType']] = array() ;}
		    			//$temp_arr['']
		    			//$k = rand(0,10);
		    			//array_push($data[$r['date']][$key][$r['deviceType']],$r['total']);
		    			//$data[$r['date']][$key][] = $r;
		    			$data[$r['date']][$key][$r['deviceType']] = $r['total'];
		    		}
		    	}
		    	unset($result);
		    	//echo "<pre>";
		    	//var_dump($data);exit;
    		}
    		
    	}else{
    		$result = array();
	    	//累积用户数
    		if(!$_COOKIE['userCount']){
		    	$userCount = $this->db->select('deviceType,count(id) as total')
		    					  ->group_by('deviceType')
		    					  ->get('User')
		    					  ->result_array();
		   		setcookie('userCount',serialize($userCount),time()+1800);
    		}
    		else{
    			
    			$userCount = unserialize(stripslashes($_COOKIE['userCount']));
    		}
    		
    		
	   		//累积设备数
    		if(!$_COOKIE['deviceCount']){
		   		$deviceCount = $this->db->select('deviceType,count(id) as total')
		    					  ->group_by('deviceType')
		    					  ->get('StatDeviceInfo')
		    					  ->result_array();		
		    	setcookie('deviceCount',serialize($deviceCount),time()+1800);
    		}	
    		else{
    			
    			$deviceCount = unserialize(stripslashes($_COOKIE['deviceCount']));
    		}	
    		
    		
	   		//更新了3.0的设备/用户数 暂时这样
	   		if(!$_COOKIE['device3']){
		   		$device3 = $this->db->select('deviceType,count(distinct deviceCode) total')
		    					  ->where("appVersion like '3.%'",null,false)
		    					  ->group_by('deviceType')
		    					  ->get('ConnectLog')
		    					  ->result_array();
		   		setcookie('device3',serialize($device3),time()+1800);
	   		}
	   		else{
	   			
	   			$device3 = unserialize(stripslashes($_COOKIE['device3']));
	   		}
	   		
	   		
	   		if(!$_COOKIE['user3']){
	   			$user3 = $this->db->select('deviceType,count(id) total')
	    					  //->where("appVersion like '3.%'",null,false)
	    					  ->where("(appVersion like '3.%' or appVersion like '%-3.%')",null,false)
	    					  ->group_by('deviceType')
	    					  ->get('User')
	    					  ->result_array();
	    		setcookie('user3',serialize($user3),time()+1800);
    		}	
    		else{
    			
    			$user3 = unserialize(stripslashes($_COOKIE['user3']));
    		}	
	 		
    		
    		if(!$_COOKIE['device2013']){
	   			$device2013 = $this->db->select('deviceType,count(id) total')
	    					  ->where("lastDate > '2013-01-01 00:00:00'",null,false)   		
	    					  ->group_by('deviceType')
	    					  ->get('StatDeviceInfo')
	    					  ->result_array();
	    		setcookie('device2013',serialize($device2013),time()+1800);
    		}	
    		else{
    			
    			$device2013 = unserialize(stripslashes($_COOKIE['device2013']));
    		}	
	 		//2013-01-01之后链接过的终端数/用户数
    		if(!$_COOKIE['user2013']){
	 			$user2013 = $this->db->select('deviceType,count(id) total')
	    					  ->where("lastSigninDate > '2013-01-01 00:00:00'",null,false)  					  
	    					  ->group_by('deviceType')
	    					  ->get('User')
	    					  ->result_array();
	    		setcookie('user2013',serialize($user2013),time()+1800);
	    	}	
    		else{
    			
    			$user2013 = unserialize(stripslashes($_COOKIE['user2013']));
    		}					  
	    	$result = array(
	    		'user_count' => $userCount,
	    		'device_count' => $deviceCount,
	    		'device3' => $device3,
	    		'user3' => $user3,
	    		'device2013' => $device2013,
	    		'user2013' => $user2013,
	    	);				
	    	foreach($result as &$row){
	    		foreach($row as $k=>$r){
	    			if($k!=$r['deviceType']){
	    				unset($row[$k]);
	    			}
	    			$row[$r['deviceType']] = $r['total'];
	    		}
	    	}
	 		
    	}
    	
    	
   		$this->assign(compact('result','startDate','endDate','data'));
   		$this->display("user_device");
    }
    
    function pointChange(){
    	//系统总有效积分
    	$total = $this->db->select("sum(point) total",false)
    					  ->where("lastSigninDate > '2013-01-01 00:00:00'",null,false)
    					  ->get($this->_tables['user'])
    					  ->row_array();
    	//排除重复设备号的积分存量
    	$sql = " select sum( a.maxPoint ) total from 
				 ( select deviceCode , max( point) as maxPoint 
				     from User
				   where deviceCode is not null
				   and lastSigninDate > '2013-01-01 00:00:00'
				   group by deviceCode ) a " ; 
    	$distinctDeviceTotal = $this->db->query($sql)->row_array();
    	
    	//没有设备号的积分量
    	$noDeviceCount = $this->db->select("sum(point) as total")
    							  ->where("deviceCode is null and lastSigninDate > '2013-01-01 00:00:00'",null,false)
    							  ->get($this->_tables['user'])
    							  ->row_array();
   	
    	//系统外发的积分票存量
    	$pointTicketTotal = $this->db->select(" sum(point) total")
    			 ->where('status',0)
    			 ->where("expireDate > '".date("Y-m-d H:i:s")."'",null,false)
    			 ->get($this->_tables['pointticket'])
    			 ->row_array();
    	$placePoint = $this->db->select(" sum(point) total")
    							->where('status',0)
    							->get($this->_tables['place'])
    							->row_array();
    	if($this->is_post()){
    		$startDate = $this->post('startDate');
    		$endDate   = $this->post('endDate');
    		
    		if($startDate || $endDate){
	    		//系统从抢地主抽水的积分数量
	    		$where = "1=1 ";
		    	if($startDate){
		    		$where .= " and  createDate >= '{$startDate}'";
		    	}
		    	if($endDate){
		    		$where .= " and createDate <= '{$endDate}'";
		    	}
	    		//placerob
	    		$placeRobCut = $this->db->select('sum(usePoint)*0.2 as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    				 ->where($where,null,false)
	    				 ->group_by('date')
	    				 ->get($this->_tables['placerob'])
	    				 ->result_array();
	    		//发放到地点的积分数量		
				$placeGrowPoint = $this->db->select('sum(point) as total, DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
										   ->where($where,null,false)
										   ->group_by('date')
										   ->get('PlaceGrowPointLog')
										   ->result_array();
	    		//56  使用道具获得积分
	    		$useLotteryPoint = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=56",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();
	    		//47 用户扫描积分票
	    		$userPointTicket = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=47",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();
				//61 系统回收道具用户获得积分
	    		$itemRecyclePoint = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=61",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();						   	
				//50 用户购买道具
	    		$userBuyItem = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=50",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();	
				//49 用户购买积分商品
	    		$userBuyGoods = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=49",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();	
				//29 系统从抢地主抽水的积分量
	    		$systemRobUser = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=29",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();		
				//60 用户参加投票活动支出
	    		$userVoteCost = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=60",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();	
				//11 每日启动客户端
	    		$dailyActive = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=11",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();		
										   	
				//24 管理员每日发积分量
				$dailySAPoint = $this->db->select('sum(point) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
	    									->where($where." and pointCaseId=24",null,false)
										   	->group_by('date')
										   	->get('UserPointLog')
										   	->result_array();							   
										   						   	
	    		$result = array(
	    			'placeRobCut' => $placeRobCut , 
	    			'placeGrowPoint' => $placeGrowPoint ,
	    			'useLotteryPoint' => $useLotteryPoint ,
	    			'userPointTicket' => $userPointTicket,
	    			'itemRecyclePoint' => $itemRecyclePoint,
	    			'userBuyItem' => $userBuyItem,
	    			'userBuyGoods' => $userBuyGoods,
	    			'systemRobUser' => $systemRobUser,
	    			'userVoteCost' => $userVoteCost,
	    			'dailyActive' => $dailyActive,
	    			'dailySAPoint' => $dailySAPoint
	    		);
	    		
	    		$data = array();
		    	foreach($result as $key => &$row){
			    	foreach($row as $k=>$r){
			    		$data[$r['date']][$key] = $r['total'];
			    	}
			    }
			    $ispost = 1;
    		}
    	}
    					
    	$this->assign(compact('startDate','endDate','total','distinctDeviceTotal','pointTicketTotal','placePoint','data','ispost'));
    	$this->display('pointChange');
    }
}
