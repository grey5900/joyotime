<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 积分管理
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-4
 */

class Point extends MY_Controller {
    /**
     * 积分派发列表
     */
    function index() {
        $keywords = trim($this->post('keywords'));
        /**
        $where_sql = 'u.id = up.uid AND ma.id = up.operatorId 
                        AND up.pointCaseId = upc.id AND upc.type = 666';
        // 关键词条件
        if ($keywords !== '') {
            $type = $this->post('type');
            switch($type) {
                case 'uid' :
                    $where_sql .= " AND u.id = '{$keywords}'";
                    break;
                case 'oper_name' :
                    $where_sql .= " AND ma.name like '%{$keywords}%'";
                    break;
                case 'oper_realname' :
                    $where_sql .= " AND ma.truename like '%{$keywords}%'";
                    break;
                case 'nickname' :
                    $where_sql .= " AND u.nickname like '%{$keywords}%'";
                    break;
                case 'username' :
                    $where_sql .= " AND u.username like '%{$keywords}%'";
                    break;
            }
        }
        
        $total_num = $this->db->from('User u, UserPointLog up, 
                MorrisAdmin ma, UserPointCase upc')
                ->where($where_sql, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('up.*, u.nickname, u.username, ma.*', false)
                         ->order_by('up.id', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql, null, false)
                         ->from('User u, UserPointLog up, MorrisAdmin ma, UserPointCase upc')
                         ->get()->result_array();
        */
    	
    	// 2013.07.30 优化
    	$where_sql = 'u.id = up.uid AND ma.id = up.operatorId
    	AND up.pointCaseId = upc.id';
    	// 关键词条件
    	$result_where_sql = '';
    	if ($keywords !== '') {
    		$type = $this->post('type');
    		switch($type) {
    			case 'uid' :
    				$result_where_sql = " AND u.id = '{$keywords}'";
    				break;
    			case 'oper_name' :
    				$result_where_sql = " AND ma.name like '%{$keywords}%'";
    				break;
    			case 'oper_realname' :
    				$result_where_sql = " AND ma.truename like '%{$keywords}%'";
    				break;
    			case 'nickname' :
    				$result_where_sql = " AND u.nickname like '%{$keywords}%'";
    				break;
    			case 'username' :
    				$result_where_sql = " AND u.username like '%{$keywords}%'";
    				break;
    		}
    		$where_sql .= $result_where_sql;
    	}
    	
    	$total_num = $this->db->from('User u, UserPointLog up,
    			MorrisAdmin ma, UserPointCase upc')
    			->where($where_sql, null, false)
    			->where('upc.type',666)->count_all_results();
    	$paginate = $this->paginate($total_num);
    	
    	$sql = "SELECT up.*, u.nickname, u.username, ma.*
				from ( select b.id 
				from UserPointCase upc 
				inner join UserPointLog b
				on upc.type = 666 and upc.id = b.pointCaseId 
				order by b.id desc limit {$paginate['per_page_num']} offset {$paginate['offset']}) m, 
				UserPointLog up, User u, MorrisAdmin ma
				where m.id = up.id
				and up.uid = u.id
				and up.operatorId = ma.id {$result_where_sql}";
    	//echo $sql;
    	$list = $this->db->query($sql)->result_array();
    	
    	//echo '<!--', $this->db->last_query(), '-->';
    	
        // 积分操作的对应ID号
//         $config['point_case'] = array(
//                 'banned_tip' => 17,
//                 'banned_photo' => 18,
//                 'banned_reply' => 19,
//                 'banned_user' => 20,
//                 'poi_report' => 21,
//                 'poi_create' => 22,
//                 'user_feedback' => 23,
//                 'manual_point' => 24,
//                 'digest' => 48,
//                 'order_refund' => 55,
//                 'manual_point_minus' => 58
//         );
        $actions = array(
                'banned_tip' => '屏蔽点评',
                'banned_photo' => '屏蔽照片',
                'banned_reply' => '屏蔽回复',
                'banned_user' => '屏蔽用户',
                'poi_report' => 'POI报错',
                'poi_create' => '创建POI',
                'user_feedback' => '用户反馈',
                'manual_point' => '手动增加积分',
                'digest' => '加精',
                'order_refund' => '退款',
                'manual_point_minus' => '手动减少积分'
                );
        
//         $types = array(
//                 '1' => '地点',
//                 '5' => '活动',
//                 '18' => 'YY',
//                 '19' => 'POST',
//                 '20' => '地点册',
//                 '23' => '商品'
//                 );
        $item_type = $this->config->item('item_type');
        $types = array();
        if($item_type) {
            foreach($item_type as $k => $v) {
                $types[$k] = $v['value'];
            }
        }
        
        foreach($list as &$row) {
            $remark = json_decode($row['remark'], true);
            if(empty($remark)) {
                // 老数据
                $remark = array('remark' => $row['remark'], 'id' => '', 'action' => '');
            }
            $row['reason'] = $remark;
        }
        unset($row);
        
        // 查询累计的分数
        $row = $this->db2->select_sum('a.point', 'total')
                ->where("a.pointCaseId = b.id AND a.point > 0 AND b.type = 666")
                ->get($this->_tables['userpointlog'] . ' a, ' . $this->_tables['userpointcase'] . ' b')
                ->row_array();
        $total_plus = $row['total'];
        $row = $this->db2->select_sum('a.point', 'total')
                ->where("a.pointCaseId = b.id AND a.point < 0 AND b.type = 666")
                ->get($this->_tables['userpointlog'] . ' a, ' . $this->_tables['userpointcase'] . ' b')
                ->row_array();
        $total_minus = $row['total'];
        $this->assign(compact('total_plus', 'total_minus', 'actions', 'types', 'type', 'keywords', 'list'));
        
        $this->display('index');
    }
    
    function detail(){
    	if($this->is_post()){
    		$start_date = $this->post('start_date');
    		$end_date = $this->post('end_date');
    		
    		if(!empty($start_date) && !empty($end_date)){
	    		$base_where = " upl.createDate >= '{$start_date}' and upl.createDate <= '{$end_date}' ";
	    		//管理员操作积分 24
	    		//选择日期内出现过的管理员
	    		$admins = $this->db->select('distinct operatorId,sa.name,sa.truename',false)
	    				 ->from('UserPointLog upl')
	    				 ->join('MorrisAdmin sa','sa.id=upl.operatorId','inner')
	    				 ->where('pointCaseId',24)
	    				 ->where($base_where,null,false)
	    				 ->get()->result_array();
	    		$ad_ids = array();
	    		foreach($admins as $ad_row){
	    			$ad_ids[] = $ad_row['operatorId'];
	    		}
	    		
	    		if(!empty($ad_ids)){
	    				 
		    		$res = $this->db->select('operatorId,sum(point) as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
		    						->from('UserPointLog upl')
		    						->where_in('operatorId',$ad_ids)
		    						->where($base_where,null,false)
		    						->where('pointCaseId',24)
					 				->group_by('date,operatorId')
					 				->get($this->_tables['userpointcate'])->result_array();
					
					/*$dates = $this->db->select('DATE_FORMAT(createDate, \'%Y-%m-%d\') as date',false)
								      ->from('UserPointLog upl')
									  ->where_in('operatorId',$ad_ids)
		    						  ->where($base_where,null,false)
		    						  ->where('pointCaseId',24)
					 				  ->group_by('date')					 				  
					 				  ->get($this->_tables['userpointcate'])->result_array();*/
					$results = array();
					
					foreach($res as $row){
						$results[$row['date']][$row['operatorId']] = $row;
					}
					
					 				
		    		$this->assign(compact('start_date','end_date','admins','results','dates'));	
	    		}	 
    		}
    	}
    	$this->display('detail');
    }

    /**
     * 操作积分
     *
     */
    function add() {
        if ($this->is_post()) {
            // 添加提交
            // 判断备注长度
            $remark = trim($this->post('remark'));
            if (cstrlen($remark) > 255) {
                $this->error('备注不能超过255个字');
            }

            $is_send_message = $this->post('send_message');
            if ($is_send_message) {
                // 需要发送系统消息
                $message = trim($this->post('message'));
                if (strlen($message) > 420) {
                    $this->error('发送消息内容不能超过420个字符，中文不超过140个字');
                }
            }

            $point = intval($this->post('point')) * intval($this->post('oper'));
            if (empty($point)) {
                //
                $this->error('请输入操作的积分');
            }

            $uid = $this->post('user_id');
            $user = $this->db->get_where('User', array('id' => $uid))->row_array();
            if (empty($user)) {
                //
                $this->error('错误的用户，改用户不存在');
            }
            if ($point < 0) {
                // 需要去判断用户的积分是否够减
                if ($user['point'] < abs($point)) {
                    $this->error('该用户积分不足！（积分：' . $user['point'] . '）');
                }
            }

//             // 插入日志表
//             $b = $this->db->insert('UserPointLog', array(
//                     'uid' => $uid,
//                     'pointCaseId' => 24,
//                     'point' => $point,
//                     'operatorId' => $this->auth['id'],
//                     'remark' => $remark
//             ));
//             // 给用户加减积分
//             $b &= $this->db->query("UPDATE User SET point = point + ({$point}) WHERE id='{$uid}'");
            
            // 2013.02.28 修改为调用函数
            $this->load->helper('ugc');
            $b = make_point($uid, $point>0?'manual_point':'manual_point_minus', $point, $uid, $remark);
            
            if ($is_send_message) {
                // 需要发送系统消息
                $data = array(
                        'recieverId' => $uid,
                        'content' => $message,
                        'type' => 0,
                        'itemId' => 0,
                        'sendType' => 1 // 1.手动发送  0.系统发送
                );

                $b &= $this->db->insert('SystemMessage', $data);
                $sys_msg_id = $this->db->insert_id();

                if ($b) {
                    // 调用接口发送消息
                    $this->lang->load('api');
                    send_api_interface($this->lang->line('api_msg_system'), 'POST', array('sys_msg_id' => $sys_msg_id));
                }
            }

            $b ? $this->success('操作用户积分成功', $this->_index_rel, $this->_index_uri, 'closeCurrent') : $this->error('操作用户积分失败');
        }

        $this->display('add');
    }

    /**
     * 设定积分配额
     */
    function quota() {
        // 去刷新今天没有领取的用户的积分
        $today = dt(TIMESTAMP, 'Y-m-d');
        $this->db->query("UPDATE {$this->_tables['morrisadmin']} 
                            SET todayPoint = 0, autoGiveDate = '{$today}' 
                            WHERE autoGiveDate <> '{$today}'");
        
        $keywords = trim($this->post('keywords'));
        $where_sql = '';
        // 关键词条件
        if ($keywords !== '') {
            $type = $this->post('type');
            switch($type) {
                case 'oper_name' :
                    $where_sql = "a.name like '%{$keywords}%'";
                    break;
                case 'oper_realname' :
                    $where_sql = "a.truename like '%{$keywords}%'";
                    break;
            }
            $keywords = dstripslashes($keywords);
        }
        
        $total_num = $this->db->from('MorrisAdmin a')
                                ->where($where_sql?$where_sql:array())->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('a.lastQuotaDate', 'desc')
                    ->limit($paginate['per_page_num'], $paginate['offset'])
                    ->where($where_sql?$where_sql:array())
                    ->from('MorrisAdmin a')
                    ->get()->result_array();
        
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('point_quota');
    }
}
