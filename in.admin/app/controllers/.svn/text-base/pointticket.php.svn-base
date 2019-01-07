<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
/**
  * 积分票
  * @Author: chenglin.zhu@gmail.com
  * @Date: 2013-2-28
  */

class Pointticket extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->helper('pointticket');
    }
    
    /**
     * 积分票
     */
    function index() {
        
        $this->display('index');
    }
    
    /**
     * 积分票列表
     */
    function pointticket_list() {
        $keywords = trim($this->post('keywords'));
        $where_sql = 'a.placeId = b.id';
        if($keywords !== '') {
            $where_sql .= ' AND b.placename like \'%' . $keywords . '%\'';
            $keywords = dstripslashes($keywords);
        }
        $total_num = $this->db2->from('PointTicketList a, Place b')
                        ->where($where_sql, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db2->select('a.*, b.placename', false)
                        ->order_by('a.createDate', 'desc')
                        ->limit($paginate['per_page_num'], $paginate['offset'])
                        ->where($where_sql, null, false)
                        ->from('PointTicketList a, Place b')
                        ->get()->result_array();
        
        $this->assign(compact('keywords', 'list'));
        
        $this->display('list');
    }
    
    /**
     * 积分票CODE
     */
    function pointticket_code_list() {
        $type = trim($this->post('type'));
        $status = intval($this->post('status'));
        $keywords = trim($this->post('keywords'));
        $where_sql = 'a.listId = b.id AND c.id = b.brandId';
        
        if($status > 0) {
            $where_sql .= ' AND a.status = \'' . ($status - 1) . '\'';
        } else {
            $where_sql .= ' AND a.status >= 0';
        }
        
        if($keywords !== '') {
            if($type == 'brand_id') {
                $where_sql .= ' AND c.name like \'%' . $keywords . '%\'';
            }
            if($type == 'poi') {
            	//intval($keywords) > 0 && $where_sql .= ' AND a.placeId ='.intval($keywords);
                $where_sql .= ' AND (d.placename like \'%' . $keywords . '%\' or d.id ='.intval($keywords).')';
            } 
            else {
                $where_sql .= ' AND a.id = \'' . $keywords . '\'';
            }
            $keywords = dstripslashes($keywords);
        }
        $total_num = $this->db2->from('PointTicket a, PointTicketList b, Brand c ')
        					->join('Place d', 'a.placeId = d.id', 'left')
                            ->where($where_sql, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        //$where_sql .= $poi_where;
        $list = $this->db2->select('a.*, b.activateDate, c.name as brandName, 
                    d.placename, e.username, e.nickname', false)
                    ->order_by('a.useDate', 'desc')
                    ->order_by('b.activateDate', 'desc')
                    ->order_by('a.id', 'desc')
                    ->limit($paginate['per_page_num'], $paginate['offset'])
                    ->where($where_sql, null, false)
                    ->from('PointTicket a, PointTicketList b, Brand c')
                    ->join('Place d', 'a.placeId = d.id', 'left')
                    ->join('User e', 'a.applyUid = e.id', 'left')
                    ->get()->result_array();
        $code_status = $this->config->item('trade_code_status');
        $this->assign(compact('code_status', 'type', 'status', 'keywords', 'list'));

        $this->display('code_list');
    }
    
    /**
     * 生成积分票
     */
    function make() {
        if($this->is_post()) {
            $point = intval($this->post('point'));
            $num = intval($this->post('num'));
            $place_id = intval($this->post('content_id'));
            $expire_date = $this->post('expire_date');
            $expire_timestamp = strtotime($expire_date);
            if($point <= 0) {
                $this->error('请选择需要生成积分票的积分');
            }
            
            if($num <= 0 || $num > 1000) {
                $this->error('请选择需要生成积分票的数量不能小于0且不能大于1000');
            }
            
            if($place_id <= 0) {
                $this->error('请选择需要关联的POI地点');
            }
            
            if(TIMESTAMP > $expire_timestamp) {
                $this->error('过期时间不能小于当前時間');
            }
            
            $brand_id = intval($this->post('brand_id'));
            ($brand_id <= 0) && ($brand_id = 1);
            
            // 这里比较重要所以用了事务
            $this->db2->trans_start();
            
            // 检查积分票表的最大值是好多，以1开头的
            $sql = 'SELECT IFNULL(MAX(id), 1000000000) AS id
                    FROM PointTicket WHERE id < 2000000000';
            $row = $this->db2->query($sql)->row_array();
            if(empty($row)) {
                $id = 1000000000;
            } else {
                $id = intval($row['id']);
            }
            $start_id = $id + 1;
            $end_id = $id + $num;
            $id_range = range($start_id, $end_id);
            
            // 加入积分票表
            $ticket = array(
                    'codeRange' => $start_id . '-' . $end_id,
                    'num' => $num,
                    'point' => $point,
                    'expireDate' => $expire_date,
                    'placeId' => $place_id,
                    'brandId' => $brand_id
            );
            $this->db2->insert($this->_tables['pointticketlist'], $ticket);
            $list_id = $this->db2->insert_id();
            
            $list = array();
            foreach($id_range as $id) {
                $list[] = array(
                        'id' => $id,
                        'code' => get_code(),
                        'point' => $point,
                        'placeId' => $place_id,
                        'expireDate' => $expire_date,
                        'listId' => $list_id
                        );
            }
            // 加入积分票的CODE表
            $this->db2->insert_batch($this->_tables['pointticket'], $list);

            $this->db2->trans_complete();
            
            if ($this->db2->trans_status() === FALSE) {
                // 失败了
                $this->error('生成积分票失败，请重试，亲');
            } else {
                $this->success('生成积分票成功', '', '', 'closeCurrent');
            }
        }
        
        $this->display('make');
    }
    
    /**
     * 修改积分票
     */
    function edit() {
        $id = intval($this->get('id'));
        $ticket = $this->db2->get_where($this->_tables['pointticketlist'], array('id' => $id))->row_array();
        
        if(empty($ticket)) {
            $this->error('错误的积分票');
        }
        
        if($this->is_post()) {
            $place_id = intval($this->post('content_id'));
            $expire_date = $this->post('expire_date');
            $expire_timestamp = strtotime($expire_date);
            
            if(TIMESTAMP > $expire_timestamp) {
                // 已经作废，看来他们要作废这批积分票了
                $is_expire = true;
            }
            
            $data = array();
            if($place_id > 0) {
                $data['placeId'] = $place_id;
            }
            
            if($expire_timestamp > 0) {
                $data['expireDate'] = $expire_date;
            }
            
            if($data) {
                // 用事务
                $this->db2->trans_start();
            
                // 更新列表
                $this->db2->where(array('id' => $id))->update($this->_tables['pointticketlist'], $data);
                
                // 状态为1 -1的不处理状态
                $this->db2->where(array('listId' => $id))->where_in('status', array(-1, 1))
                        ->update($this->_tables['pointticket'],
                                array('expireDate' => $expire_date, 'placeId' => $place_id));
                
                // 状态为0 2的状态的过期了，那么就置为过期，或者设置为0
                $this->db2->where(array('listId' => $id))->where_in('status', array(0, 2))
                        ->update($this->_tables['pointticket'],
                                array('expireDate' => $expire_date, 'placeId' => $place_id, 'status' => $is_expire?2:0));
            
                $this->db2->trans_complete();
            }
            
            if ($this->db2->trans_status() === FALSE) {
                $this->error('修改失败，亲，请重试哈');
            } else {
                $this->success('修改积分票成功', '', '', 'closeCurrent');
            }
        }
        
        $this->assign(array('ticket' => $ticket, 'c_id' => $ticket['placeId']));
        
        $this->display('edit');
    }
    
    /**
     * 导出积分票
     */
    function export_code() {
        $id = intval($this->get('id'));
        $ticket = $this->db2->get_where($this->_tables['pointticketlist'], array('id' => $id))->row_array();
        
        if(empty($ticket)) {
            $this->error('错误的积分票');
        }
        
        if($this->get('do')) {
            // 查询所有的CODE
            $list = $this->db2->get_where($this->_tables['pointticket'], array('listId' => $id))->result_array();
            
            $filename = '积分票：' . $ticket['codeRange'];
            
            header('Content-type: application/octet-stream; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'.txt"');
                    
            $str = "id\turl\tcode\tpoint\n";
            
            foreach($list as $row) {
                $str .= sprintf("%s\t%s/qr/inpt/%s?code=%s\t%s\t%s\n", 
                        $row['id'], $this->config->item('web_site'), $row['id'], $row['code'], $row['code'], $row['point']);
            }
            
            die($str);
        } else {
            $this->success('', '', '', '', array('id' => $id));
        }
    }
    
    /**
     * 标记积分票已打印
     */
    function printed() {
        $id = intval($this->get('id'));
        $ticket = $this->db2->get_where($this->_tables['pointticketlist'], array('id' => $id))->row_array();
        
        if(empty($ticket)) {
            $this->error('错误的积分票');
        }
        
        // 检查是否状态
        if($ticket['status']) {
            $this->error('积分票状态已为打印状态，无法在修改了哦，亲');
        }
        
        $this->db2->where(array('id' => $id))
                    ->update($this->_tables['pointticketlist'], array('status' => 1))?$this->success('成功设定'):$this->error('操作失败');
    }
    
    /**
     * 标记积分票为激活状态
     */
    function activated() {
        $id = intval($this->get('id'));
        $ticket = $this->db2->get_where($this->_tables['pointticketlist'], array('id' => $id))->row_array();
        
        if(empty($ticket)) {
            $this->error('错误的积分票');
        }
        
        // 检查是否状态
        if($ticket['isActivated']) {
            $this->error('积分票已经激活了，亲，不能重复激活哈');
        }
        
        // 用事务
        $this->db2->trans_start();
        // 设定激活标志
        $this->db2->where(array('id' => $id))
                    ->update($this->_tables['pointticketlist'], array('isActivated' => 1, 'activateDate' => now()));
        
        // 激活积分票CODE
        $this->db2->where(array('listId' => $id))
                    ->update($this->_tables['pointticket'], array('status' => 0));
                
        $this->db2->trans_complete();
        
        if ($this->db2->trans_status() === FALSE) {
            $this->error('激活失败，亲，请重试哈');
        } else {
            $this->success('激活积分票成功');
        }
    }
    
    function export_by_date(){
    	$start = $this->post('start');
    	$end = $this->post('end');
    	if($this->is_post()){
    		
    		if(!$start && !$end){
    			//$this->error('请选择导出时间！');
    			die('请选择导出时间！');
    		}
    		if($start){
    			$this->db->where('useDate >=',$start);
    		}
    		if($end){
    			$this->db->where('useDate <=',$end);
    		}
    		$this->db->where('a.listId = b.id AND c.id = b.brandId',null,false);
    		$this->db->where('a.status',1);
    		$this->db->where('applyUid <>','');
    		//a.listId = b.id AND c.id = b.brandId
    		$list = $this->db->select('a.*, b.activateDate, c.name as brandName, 
                    d.placename, e.username, e.nickname', false)
                    ->order_by('a.useDate', 'desc')
                    ->order_by('b.activateDate', 'desc')
                    ->order_by('a.id', 'desc')              
                    ->from('PointTicket a, PointTicketList b, Brand c')
                    ->join('Place d', 'a.placeId = d.id', 'left')
                    ->join('User e', 'a.applyUid = e.id', 'left')
                    ->get()->result_array();
        	//var_dump($list);exit;
        	if(empty($list))
        	{
        		die('你选择的时间段内没有积分票的使用记录');
        	}
    		
    		$filename = sprintf ( '[%s]到[%s]积分票使用情况', $start , $end );
			header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
			header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
			header ( 'Pragma: no-cache' ); // 缓存
	 		header ( 'Expires: 0' );
			$str = "积分票编号\t出票时间\t过期时间\t绑定到POI\t渠道\t面额\t使用用户\t使用时间\n";
			
			foreach ( $list as $row )
			{
				$str .= "{$row['id']}\t{$row['activateDate']}\t{$row['expireDate']}\t{$row['placename']}({$row['placeId']})\t{$row['brandName']}\t{$row['point']}\t".($row['nickname']?$row['nickname']:$row['username']).'('.$row['applyUid'].")\t{$row['useDate']}\n";
				
			}
			echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
			exit;
    	}
    	$this->display("export_by_date");
    }
}