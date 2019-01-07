<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
  * IN商城
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-1-29
  */

class Inmall extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->model('product_model', 'm_product');
        $this->load->model('productowntradecode_model', 'm_productowntradecode');
        $this->load->model('productatplace_model', 'm_productatplace');
    }
    
    /**
     * 商品管理首页
     */
    function index() {
        
        
        $this->display('index');
    }
    
    /**
     * 待上架
     */
    function product_waiting() {
        $keywords = trim($this->post('keywords'));
        $search_type = $this->post('search_type');
                
        $where_sql = 'status = 1';
        if($keywords !== '') {
            if($search_type) {
                $where_sql .= " AND id = '{$keywords}'";
            } else {
                $where_sql .= " AND name like '%{$keywords}%'";
            }
        }
        
        $this->change_db();
        
        $total_num = $this->db->where($where_sql)->from($this->m_product->table)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('createDate', 'desc')
                ->limit($paginate['per_page_num'], $paginate['offset'])
                ->where($where_sql)->get($this->m_product->table)->result_array();
        $keywords = dstripslashes($keywords);
        $this->assign(compact('search_type', 'keywords', 'list'));
        
        $this->display('product_waiting');
    }
    
    /**
     * 上架中
     */
    function product_onsale() {
        $keywords = trim($this->post('keywords'));
        $search_type = $this->post('search_type');
        
        // 正常 售罄 结束
        $where_sql = 'a.status in (0, 2, 5)';
        if($keywords !== '') {
            if($search_type) {
                $where_sql .= " AND a.id = '{$keywords}'";
            } else {
                $where_sql .= " AND a.name like '%{$keywords}%'";
            }
        }
        
        $this->change_db();
        
        $total_num = $this->db->where($where_sql)
                            ->from($this->m_product->table . ' a')
                            ->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('a.rankOrder', 'desc')
                    ->limit($paginate['per_page_num'], $paginate['offset'])
                    ->select('a.*, IFNULL((SELECT SUM(quantity) 
                            FROM ' . $this->_tables['orderinfo'] . ' b WHERE b.isPayed=1 AND a.id=b.productId), 0) AS saleCount, 
                            IFNULL((SELECT c.expireDate FROM  ' . $this->_tables['homepagedata'] . ' c 
                            WHERE c.itemType=23 AND c.itemId=a.id), 0) AS digest', false)
                    ->where($where_sql)->get($this->m_product->table . ' a')->result_array();
        
        $keywords = dstripslashes($keywords);
        $this->assign(compact('search_type', 'keywords', 'list'));
        
        $this->display('product_onsale');
    }
    
    /**
     * 下架
     */
    function product_offtheshelf() {
        $keywords = trim($this->post('keywords'));
        $search_type = $this->post('search_type');
        
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');
        
        // 自动下架 手动下架
        $where_sql = 'a.status in (3, 4)';
        if($keywords !== '') {
            if($search_type) {
                $where_sql .= " AND a.id = '{$keywords}'";
            } else {
                $where_sql .= " AND a.name like '%{$keywords}%'";
            }
        }
        
        $startDate && $where_sql .= " and a.startDate >= '{$startDate}' ";
        $endDate && $where_sql .= " and a.startDate <= '{$endDate}' ";
        
    	$submit = $this->post('submit');
        if($submit == 'export'){
        	 $list = $this->db->order_by('a.endDate', 'desc')
                    //->limit($paginate['per_page_num'], $paginate['offset'])
                    ->select('a.*, IFNULL((SELECT SUM(quantity) FROM ' . $this->_tables['orderinfo'] . ' b WHERE b.isPayed=1 AND a.id=b.productId), 0) AS saleCount', false)
                    ->where($where_sql)->get($this->m_product->table . ' a')->result_array();
            
           	$filename = sprintf ( '商品列表[%s TO %s]', $startDate , $endDate );
			header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
			header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
			
			$str = "ID\t商品名\t价格(积分)\t库存(实际 _ 销售)\t销量\t购买人数\t开始时间\t结束时间\t下架原因\n";
			
			foreach ( $list as $row )
			{
				$str .= "{$row['id']}\t{$row['name']}\t{$row['price']}\t{$row['stock']}"." _ "."{$row['quantity']}\t{$row['saleCount']}\t{$row['buyerCount']} + {$row['inventedBuyerCount']}\t".dt2($row['startDate'], 'Y-m-d H:i')."\t".dt2($row['endDate'], 'Y-m-d H:i')."\t".($row['status']==3?'自动下架':'手动下架')."\n";
				
			}
			echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
           	exit;
        }
        
        $this->change_db();
        
        $total_num = $this->db->where($where_sql)
                            ->from($this->m_product->table . ' a')
                            ->count_all_results();
        $paginate = $this->paginate($total_num);
        
    	
        
        
        
        $list = $this->db->order_by('a.endDate', 'desc')
                    ->limit($paginate['per_page_num'], $paginate['offset'])
                    ->select('a.*, IFNULL((SELECT SUM(quantity) FROM ' . $this->_tables['orderinfo'] . ' b WHERE b.isPayed=1 AND a.id=b.productId), 0) AS saleCount', false)
                    ->where($where_sql)->get($this->m_product->table . ' a')->result_array();
        
        $keywords = dstripslashes($keywords);
        $this->assign(compact('search_type', 'keywords', 'list','startDate','endDate'));
        
        $this->display('product_offtheshelf');
    }
    
    /**
     * 添加商品
     */
    function product_add() {
        $id = $this->get('id');
        
        if($id) {
            // 去获取编辑商品
            $product = $this->m_product->get($id);
        }
        
        if($this->is_post()) {
            // 标题
            $name = trim($this->post('name'));
            if(cstrlen($name) > 255) {
                $this->error('商品名不能大于255个字');
            }
            // 商品简介
            $description = trim($this->post('description'));
            if(cstrlen($description) > 500) {
                $this->error('商品简介不能大于500个字');
            }
            // 使用提示
            $notice = trim($this->post('notice'));
            if(cstrlen($notice) > 500) {
                $this->error('商品提示不能大于500个字');
            }
            // 电子券到期时间要大于结束时间
            $expire_date = $this->post('expireDate');
            $start_date = $this->post('startDate');
            $end_date = $this->post('endDate');
            if(strtotime($expire_date) < strtotime($end_date)) {
                $this->error('电子券过期时间不能小于商品结束时间');
            }
            if(strtotime($end_date) < strtotime($start_date)) {
                $this->error('商品结束时间不能小于商品开始时间');
            }
            
            // 电子码生成类型
            $trade_code_type = $this->post('tradeCodeType');
            if($trade_code_type) {
                $codes = array();
                $code_file = $_FILES['code_file'];
                if($code_file['tmp_name']) {
                    if($code_file['type'] != 'text/plain') {
                        $this->error('请上传正确的电子券文件');
                    }
                    // 分析文件内容得到CODE
                    $codes_arr = file($code_file['tmp_name']);
                    $codes_arr = array_unique(array_filter($codes_arr));
                    // 去数据库判断是否有重复的code
                    foreach($codes_arr as $code) {
                        $code = trim($code);
                        
                        // 检查编码
                        $co = check_text($code);
                        if(strtolower($code) != 'utf-8') {
                            // 转换编码
                            $r = iconv($co, 'utf-8', $code);
                        }
                        if (empty($code) || in_array($code, $codes)) {
                            continue;
                        }
                        
                        if($code) {
                            $codes[] = $code;
                            if($this->m_product->get_code_info($code)) {
                                $this->error(sprintf('请检查电子码导入文件，[%s]已经存在', $code));
                            }
                        }
                        
                    }
                }
                $num = 0;
                if($id) {
                    // 去统计之前的数量
                    $num = $this->db2->from($this->_tables['productowntradecode'])
                                ->where(array('productId' => $id, 'status' => 0))->count_all_results();
                }
                $stock = $num + count($codes);
            } else {
                $stock = intval($this->post('stock'));
            }
            
            // 销售数量小于或等于实际库存
            $quantity = intval($this->post('quantity'));
            if(($trade_code_type || $stock > 0) && $quantity > $stock) {
                $this->error('销售库存不能大于实际库存');
            }
            
            $introduce = $this->post('introduce');
            $limit = intval($this->post('deviceLimit'));
            $limit_days = intval($this->post('deviceLimitDays'));
            // 添加\修改商品
            $data = array(
                    'name' => $name,
                    'image' => $this->post('image'),
                    'description' => $description,
                    'notice' => $notice,
                    'introduce' => $introduce,
                    'price' => intval($this->post('price')),
                    'originalPrice' => intval($this->post('originalPrice')) * 100,
                    'originalPriceType' => 1,
                    'quantity' => $quantity,
                    'stock' => $stock,
                    'tradeCodeType' => $trade_code_type,
                    'startDate' => $start_date,
                    'endDate' => $end_date,
            		'displayNewExpireDate' => $this->post('displayNewExpireDate'),
                    'deviceLimit' => $limit,
                    'deviceLimitDays' => $limit_days,
                    'accountLimit' => $limit,
                    'accountLimitDays' => $limit_days,
                    'phoneNumLimit' => $limit,
                    'phoneNumLimitDays' => $limit_days,
            		'permissionLevel' => intval($this->post('permissionLevel')),
                    'expireDate' => $expire_date,
                    'hasIntro' => $introduce?1:0,
                    'maxQuantity' => intval($this->post('maxQuantity')),
                    'isLimit' => (empty($trade_code_type) && $stock == 0)?0:1,
            		'defaultComment' => $this->post('comment')
                    );
            
            if($id) {
                // 修改
                $this->m_product->update($id, $data);
                
                // 去判断是否有修改到期时间
                if(strtotime($expire_date) != strtotime($product['expireDate'])) {
                    // 如果到期时间修改了
                    // 去修改兑换码时间
                    $this->db2->where(array('productId' => $id))
                                ->set(array('expireDate' => $expire_date))
                                ->update($this->_tables['productowntradecode']);
                    // 去修改已经卖出去的兑换码的时间
                    $this->db2->where("productId='{$id}' AND status in (0, 2)", null, false)
                                    ->update($this->_tables['orderowntradecode'], 
                                            array('expireDate' => $expire_date, 'status' => 0));
                }
            } else {
                // 添加
                $data['status'] = 1;
                $id = $this->m_product->add($data);
            }
            
            if($id) {
                if($codes) {
                    // 如果有电子券
                    $code_data = array();
                    foreach($codes as $code) {
                        $code_data[] = array(
                                'productId' => $id,
                                'code' => $code,
                                'expireDate' => $expire_date
                        );
                    }
                    $this->m_productowntradecode->add_batch($code_data);
                }
                
                // 修改已经导入的电子码的过期时间
                $this->m_productowntradecode->update($id, array('expireDate' => $expire_date));
                
                // 如果$trade_code_type为0，自动，删除所有的电子码
                $trade_code_type || $this->m_productowntradecode->detete_by_productid($id);
                
                $this->m_productatplace->change_db();
                // 写入地点
                $place = $this->post('place');
                $batch_data = array();
                if ($place) {
                    foreach ($place as $p) {
                        $row = array();
                        $row['placeId'] = $p;
                        $row['productId'] = $id;
                
                        $batch_data[] = $row;
                    }
                }
                
                if($product && empty($product['status'])) {
                    // 查询出所有的商品关联地点，先减去之前关联地点的活动数
                    $placeid_list = $this->db2->select('placeId')
                                        ->get_where($this->_tables['productatplace'], array('productId'=>$id))
                                        ->result_array();
                    $pids = array();
                    foreach($placeid_list as $row) {
                        $pids[] = $row['placeId'];
                    }
                    $pids && update_count('place', 'productCount', 'id in (\'' . implode('\',\'', $pids) . '\')', false);
                    
                    // 添加现在商品的关联数
                    $place && update_count('place', 'productCount', 'id in (\'' . implode('\',\'', $place) . '\')');
                }
                
                // 先删除之前的地点
                $this->m_productatplace->delete(array('productId' => $id));
                $batch_data && $this->m_productatplace->binsert($batch_data);
                
                $this->success('提交成功', '', '', 'closeCurrent');
            } else {
                $this->error('提交失败，请检查');
            }
        }
        
        if($id) {
            // 查询选中的地点
            $place = $this->m_productatplace->find_relation_place($id);
            $product['originalPrice'] = intval($product['originalPrice']/100);
            $this->assign(array(
                    'product' => $product,
                    'place' => $place
                    ));
        }
        
        $this->display('add');
    }
    
    /**
     * 清空电子码
     */
    function clear_code() {
        $id = $this->get('id');
        $b = $this->m_productowntradecode->detete_by_productid($id);
        $this->echo_json(array('code' => $b?0:1, 'message' => $b?'清空成功':'清空失败'));
    }
    
    /**
     * 删除商品
     */
    function product_del() {
        $id = $this->get('id');
        // 
        $product = $this->m_product->get($id);
        if($product['status'] == 1) {
            // 只有在待上架状态，才能删除
            // 删除code
            $this->m_productowntradecode->detete_by_productid($id);
            // 删除商品地点
            $this->m_productatplace->delete_by_productid($id);
            // 删除商品 
            $this->m_product->delete($id);
        }
        $this->success('删除商品成功', $this->_index_rel, $this->_index_uri);
    }
    
    /**
     * 上架商品
     */
    function onsale() {
        $id = $this->get('id');
        // 获取商品信息
        $product = $this->m_product->get($id);
        if(in_array($product['status'], array(1, 3, 4))) {
            // 判断是否已经过期
            if(TIMESTAMP > strtotime($product['endDate'])) {
                $this->error('商品已过期，不能上架');
            }
            // 判断是否有quantity
            // if($product['isLimit'] && $product['quantity'] <= 0) {
            // 2013.02.25这里有人说要改成必须填写销售库存
            // 哎，我只能叹息声。
            if($product['quantity'] <= 0) {
                $this->error('销售库存为0,不能上架');
            }
            
            // 地点的商品关联书
            $placeid_list = $this->db2->select('placeId')
                            ->get_where($this->_tables['productatplace'], array('productId'=>$id))
                            ->result_array();
            $pids = array();
            foreach($placeid_list as $row) {
                $pids[] = $row['placeId'];
            }
            $pids && update_count('place', 'productCount', 'id in (\'' . implode('\',\'', $pids) . '\')');
            
            // 未上架 已下架 手动下架 三个状态才处理 清空备份的电子码
            $b = $this->m_product->update($id, array('status' => 0, 'exportTradeCode' => ''));
        } else {
            $this->error('商品状态不正确，不能上架');
        }
        
        $b?$this->success('上架商品成功', $this->_index_rel, $this->_index_uri):$this->error('上架商品失败');
    }
    
    /**
     * 下架商品
     */
    function offtheshelf() {
        $id = $this->get('id');
        // 获取商品信息
        $product = $this->m_product->get($id);
        if(in_array($product['status'], array(0, 2, 5))) {
            // 正常 售罄 已结束 三个状态才处理
            $b = $this->m_product->update($id, array('status' => 4));
            // 去取消推荐
            $this->db2->delete($this->_tables['homepagedata'], array(
                    'itemType' => '23',
                    'itemId' => $id 
                    ));
            
            // 地点的商品关联书
            $placeid_list = $this->db2->select('placeId')
                            ->get_where($this->_tables['productatplace'], array('productId'=>$id))
                            ->result_array();
            $pids = array();
            foreach($placeid_list as $row) {
                $pids[] = $row['placeId'];
            }
            $pids && update_count('place', 'productCount', 'id in (\'' . implode('\',\'', $pids) . '\')', false);
            
            if($b) {
                // 去刷新首页缓存
                $this->load->helper('poi');
                api_update_cache($this->_tables['homepagedata']);
            }
        } else {
            $this->error('商品状态不正确，不能下架');
        }
        
        $b?$this->success('下架商品成功', $this->_index_rel, $this->_index_uri):$this->error('下架商品失败');
    }
    
    /**
     * 订单管理
     */
    function order() {
        
        $this->display('order_index');
    }
    
    /**
     * 已付款订单
     */
    function order_paid() {
       $this->list_order(1);
    }
    
    /**
     * 未付款订单
     */
    function order_non_payment() {
        $this->list_order(0);
    }
    
    /**
     * 订单详情
     */
    function order_detail() {
        $id = intval($this->get('id'));
        
        ($id <= 0) && $this->error('错误');
        
        // 查询订单
        $order = $this->db2->get_where($this->_tables['orderinfo'],
                    array('id' => $id))->row_array();
        
        if(empty($order)) {
            // 错误的信息
            $this->error('错误订单信息');
        }
        
        // 获取商品信息
        $product = $this->db2->get_where($this->_tables['product'],
                        array('id' => $order['productId']))->row_array();
        
        $remark = json_decode($order['remark'], true);
        if($remark) {
            $order['refund'] = $remark['refund'];
        }
        
        // 如果商品已付款去获取码
        if($order['isPayed']) {
            // OrderOwnTradeCode
            $code_list = $this->db2->get_where($this->_tables['orderowntradecode'],
                                    array('orderId' => $order['id']))->result_array();
            $trade_code_status = $this->config->item('trade_code_status');
            
            foreach($code_list as &$row) {
                $row['code'] = str_repeat('*', strlen($row['code'])-4) . substr($row['code'], -4);
            }
            unset($row);
            
            $this->assign(compact('code_list', 'trade_code_status'));
        }
        
        // 获取用户信息
        $user = $this->db2->get_where($this->_tables['user'],
                array('id' => $order['uid']))->row_array();
        
        $this->assign(compact('order', 'product', 'user'));
        
        $this->display('order_detail');
    }
    
    /**
     * 恢复删除的订单
     */
    function order_resume() {
        $id = intval($this->get('id'));
        $order = $this->db2->get_where($this->_tables['orderinfo'], 
                            array('id' => $id))->row_array();
        if(empty($order['isDelete'])) {
            $this->error('只有已被删除的订单才能被恢复');
        }
        
        ($this->db2->where('id', $id)->update($this->_tables['orderinfo'], 
                array('isDelete' => 0)))?$this->success('恢复订单成功'):$this->error('恢复订单失败');
    }
    
    /**
     * 退款
     */
    function order_refund() {
        $id = intval($this->get('id'));
        $order = $this->db2->get_where($this->_tables['orderinfo'],
                array('id' => $id))->row_array();
        if(empty($order) || $order['isRefund'] || empty($order['isPayed'])) {
            $this->error('订单已经退过款了或者订单为付款，无法退款');
        }
        
        $this->load->helper('ugc');
        // 退款 修改用户积分
        make_point($order['uid'], 'order_refund', $order['totalAmount'], $id);
        // 给用户发消息
        $this->lang->load('premessage','chinese');
        $message = sprintf($this->lang->line('sm_order_refund'), $order['productName'], $id);
        $message && send_message($message, $order['uid'], $id, 16, false);
        
        $remark = json_decode($order['remark'], true);
        $refunder = $this->auth['name'] . ($this->auth['truename']?"({$this->auth['truename']})":'');
        $refund = array('oper' => $refunder, 'oper_date' => now());
        if($remark) {
            $remark['refund'] = $refund;
        } else {
            $remark = array('comment' => $order['remark'], 'refund' => $refund);
        }
        
        if($this->db2->where('id', $id)->update($this->_tables['orderinfo'], 
                array('isRefund' => 1, 'remark' => encode_json($remark)))) {
            $this->success('退款成功');    
        } else {
            $this->success('退款失败');
        }
    }
    
    /**
     * 列表
     * @param $is_payed  0 未支付 1 已支付
     */
    private function list_order($is_payed) {
        $type = $this->post('type');
        $keywords = trim($this->post('keywords'));
		
        $startDate = $this->post('startDate');
        $endDate = $this->post('endDate');
        
        $type || $type = 'id';

        $where_sql = 'o.uid = u.id and o.isPayed = ' . $is_payed;

        if ($keywords !== '') {
            switch($type) {
                case 'product_id' :
                    $where_sql .= " and o.productId = '{$keywords}'";
                    break;
                case 'product_name' :
                    $where_sql .= " and o.productName like '%{$keywords}%'";
                    break;
                case 'nickname' :
                    $where_sql .= " and u.nickname like '%{$keywords}%'";
                    break;
                default :
                    $where_sql .= " and o.id = '{$keywords}'";
            }
            $keywords = dstripslashes($keywords);
        }
        
        if($is_payed == 1){
        	$startDate && $where_sql .= " and o.payDate >= '{$startDate}' ";
        	$endDate && $where_sql .= " and o.payDate <= '{$endDate}' ";
        }else{
        	$startDate && $where_sql .= " and o.createDate >= '{$startDate}' ";
        	$endDate && $where_sql .= " and o.createDate <= '{$endDate}' ";
        }
        
        $submit = $this->post('submit');
        if($submit == 'export'){
        	$list = $this->db->select('o.*, u.nickname')
                     ->order_by($is_payed?'o.payDate':'o.createDate', 'desc')
                     //->limit($paginate['per_page_num'], $paginate['offset'])
                     ->where($where_sql)
                     ->get('OrderInfo o, User u')->result_array();
            
           	$filename = sprintf ( '订单列表', $startDate , $endDate );
			header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
			header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
			
			/*<th width="5%">订单号</th>
                <th width="30%">订单商品</th>
                <th width="5%">商品数量</th>
                <th width="10%">订单金额(积分)</th>
                <th width="20%">下单用户</th>
                <th width="15%">支付时间</th>
                <th width="5%">状态</th>
                <th width="5%">操作</th>*/
			$str = "订单号\t订单商品\t商品数量\t订单金额(积分)\t下单用户\t用户备注\t下单时间\t支付时间\n";
			
			foreach ( $list as $row )
			{
				$str .= ($row['isDelete'] ? '删-':'')."{$row['id']}\t{$row['productName']}\t{$row['quantity']}\t{$row['totalAmount']}".($row['isRefund'] ? '退':'')."\t{$row['nickname']} {$row['cellphoneNo']}\t{$row['defaultComment']}\t{$row['createDate']}\t{$row['payDate']}\n";
				
			}
			echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
           	exit;
        }

        $total_num = $this->db->where($where_sql)->from('OrderInfo o, User u')->count_all_results();
        $paginate = $this->paginate($total_num);

        $list = $this->db->select('o.*, u.nickname')
                     ->order_by($is_payed?'o.payDate':'o.createDate', 'desc')
                     ->limit($paginate['per_page_num'], $paginate['offset'])
                     ->where($where_sql)
                     ->get('OrderInfo o, User u')->result_array();
        
        foreach($list as &$row) {
            $remark = json_decode($row['remark'], true);
            $row['comment'] = $remark?$remark['comment']:$row['remark'];
        }
        unset($row);
        
        $this->assign(compact('type', 'keywords', 'list','startDate','endDate'));
        $this->display($is_payed?'order_paid':'order_non_payment');
    }
    
    /**
     * 推荐到首页
     */
    function recommend() {
        $id = $this->get('id');
        
        $product = $this->m_product->get($id);
        
        if($product && empty($product['status'])) {
            // 正常状态
            // 判断是否已经过期
            if(TIMESTAMP > strtotime($product['endDate'])) {
                $this->error('商品已过期，不能推荐');
            }
            // 判断是否有quantity
            if($product['isLimit'] && $product['quantity'] <= 0) {
                $this->error('销售库存为0,不能推荐');
            }
            
            $this->load->helper('home');
            if($this->is_post()) {
                // 提交数据过来
                $b = recommend_digest_post(23, $id);
                $b===0?$this->success('推荐成功', '', '', 'closeCurrent'):$this->error($b);
            }
            
            recommend_digest(23, $id, image_url($product['image'], 'product', 'odp'));
        } else {
            $this->error('商品只有在未开始和售卖中，才能推荐到首页');
        }
    }
    
    /**
     * 导出电子码
     */
    function export_trade_code() {
        $id = intval($this->get('id'));
        // 真的导出
        $product = $this->db2->get_where($this->_tables['product'], 
                                  array('id' => $id))->row_array();
        
        if(empty($product['tradeCodeType'])) {
            $this->error('自动发码商品不能导出电子码');
        }
        if($this->get('do')) {
            // 检查商品状态
            if(in_array($product['status'], array(3, 4))) {
                // 电子码标获取剩余电子码 没有发出去的
                $list = $this->db2
                            ->get_where($this->_tables['productowntradecode'], array('productId' => $id, 'status' => 0))
                            ->result_array();
                if($list) {
                    $data = array();
                    foreach($list as $row) {
                        $data[] = $row['code'];
                    }
                    $list = $data;
                    unset($data);
                    
                    // 写入到商品字段并删除之前的
                    $this->db2->where(array('id' => $id))
                              ->update($this->_tables['product'], 
                                      array('exportTradeCode' => json_encode($list), 'stock' => 0, 'quantity' => 0));
                    $this->db2->delete($this->_tables['productowntradecode'], 
                            array('productId' => $id, 'status' => 0));
                } else {
                    // 去获取导出字段的
                    $list = json_decode($product['exportTradeCode'], true);
                }
                
                Header('Content-type: application/octet-stream');
                Header('Content-Disposition: attachment; filename=trade_code_' . now() . '.txt');

                if($list) {
                    foreach ($list as $code) {
                        echo $code, "\r\n";
                    }
                }
                
                exit();
            } else {
                die('商品状态不为下架，不能导出');
            }
        } else {
            $this->success('', '', '', '', array('id' => $id));
        }
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
            $comment = $this->post('comment');
            $remark = json_decode($order['remark'], true);
            if($remark) {
                $remark['comment'] = $comment;
            } else {
                $remark = array('comment' => $comment);
            }
            $b = $this->db->where(array('id' => $id))->update('OrderInfo', array('remark' => encode_json($remark)));
    
            $b ? $this->success('更新订单备注成功', '', '', 'closeCurrent') : $this->error('提交订单备注失败');
        }
        $remark = json_decode($order['remark'], true);
        if($remark) {
            // 解析出来了 
            $comment = $remark['comment'];
        } else {
            // 兼容之前的
            $comment = $order['remark'];
        }
        $this->assign('comment', $comment);
    
        $this->display('add_remark');
    }
    
    /**
     * 兑换券
     */
    function trade_code() {
        
        
        $this->display('trade_code');
    }
    
    /**
     * 兑换码
     */
    function code_exchange() {
        
        
        $this->display('code_exchange');
    }
    
    /**
     * 已兑换列表
     */
    function code_list() {
        $type = $this->post('type');
        $keywords = trim($this->post('keywords'));
        
        $type || $type = 'id';
        
        $where_sql = " a.status in (1,2) AND a.exchangeDate<> '' AND a.orderId = b.id AND b.uid = c.id";
        
        if ($keywords !== '') {
            switch($type) {
                case 'product_id' :
                    $where_sql .= " AND b.productId = '{$keywords}'";
                    break;
                case 'product_name' :
                    $where_sql .= " AND b.productName like '%{$keywords}%'";
                    break;
                default :
                    $where_sql .= " AND a.orderId = '{$keywords}'";
            }
            $keywords = dstripslashes($keywords);
        }
        
        $total_num = $this->db->where($where_sql,null,false)
                        ->from('OrderOwnTradeCode a, OrderInfo b, User c')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('a.*, b.*, IF(c.nickname, c.nickname, c.username) AS nickname', false)
                    ->order_by('a.exchangeDate', 'desc')
                    ->limit($paginate['per_page_num'], $paginate['offset'])
                    ->where($where_sql,null,false)
                    ->get('OrderOwnTradeCode a, OrderInfo b, User c')->result_array();
        
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('code_list');
    }
    
    /**
     * 兑换
     */
    function exchange() {
        $code = trim($this->get('code'));
        
        // 获取码和订单信息
        $info = $this->db2->get_where($this->_tables['orderowntradecode'], 
                    array('code' => $code))->row_array();
        
        if(empty($info)) {
            $this->echo_json(array('code' => 1, 'message' => '亲，无效的兑换码！'));
        }
        
        // 这里需要去判断下是否为自动生成的码的验证。所以要判断商品是否为自动生成
        // 2013.02.27 个人觉得这个比较坑。应该不需要的。以后可能我们可能也需要手动导入的码在我们这里验证，
        // 但是，好吧，要改就改嘛。
        
        // 去获取订单信息
        $order = $this->db2->get_where($this->_tables['orderinfo'],
                    array('id' => $info['orderId']))->row_array();
        
        // 去获取商品信息 
        $product = $this->db2->get_where($this->_tables['product'],
                        array('id' => $order['productId']))->row_array();
            
        if($this->get('do')) {
            if($product['tradeCodeType']) {
                $this->echo_json(array('code' => 1, 'message' => '亲，这个码是手动导入的，不能兑换哦！'));
            }
            
            // 提交兑换
            if($info['status'] != 0) {
                // 不是未兑换状态，已经过期
                $this->echo_json(array('code' => 1, 'message' => '兑换码已兑换或已过期'));
            }
            
            if(TIMESTAMP >= strtotime($info['expireDate'])) {
                // 过期了，那么
                $info['status'] = 2;
                // 修改数据库的状态
                $this->db2->where(array('code' => $code))->update($this->_tables['orderowntradecode'],
                        array('status' => 2));
                $this->echo_json(array('code' => 1, 'message' => '兑换码已过期'));
            }
            
            // 兑换
            $rtn = $this->db2->where(array('code' => $code))->update($this->_tables['orderowntradecode'],
                    array('status' => 1, 'exchangeDate' => now()))?array('code' => 0, 'message' => '兑换成功'):array('code' => 1, 'message' => '兑换失败，请重试');
            $this->echo_json($rtn);
        } else {
            if(TIMESTAMP >= strtotime($info['expireDate'])) {
                // 过期了，那么
                $info['status'] = 2;
                // 修改数据库的状态
                $this->db2->where(array('code' => $code))->update($this->_tables['orderowntradecode'], 
                            array('status' => 2));
            }
            
            // 去获取用户信息
            $user = $this->db2->get_where($this->_tables['user'],
                        array('id' => $order['uid']))->row_array();
            
            $this->echo_json(array('code' => 0, 'data' => array(
                    'code' => $code,
                    'status' => intval($info['status']),
                    'date' => $info['exchangeDate']?$info['exchangeDate']:$info['expireDate'],
                    'product_id' => $product['id'],
                    'product_name' => $product['name'],
                    'product_price' => $product['price'],
                    'order_id' => $order['id'],
                    'uid' => $order['uid'],
                    'cellphoneNo' => $order['cellphoneNo'],
                    'nickname' => $user['nickname']?$user['nickname']:$user['username'],
                    'code_type' => $product['tradeCodeType']
                    )));
        }
    }
    
}