<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 用户管理
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-4-17
 */

class User extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->assign('gender_state', $this->config->item('gender_state'));
        $this->assign('method', $this->_m);
        $this->load->model('user_model', 'm_user');
    }

    /**
     * 用户管理首页
     */
    function index() {
        //$keywords = trim($this->post('keywords'));
        //$keywords = trim($this->get('keywords')) ?  trim($this->get('keywords')) : $keywords;
        $keywords = $this->post('keywords');
        $keywords = $this->get('keywords') ?  $this->get('keywords') : $keywords;
        $where_sql = array();
        // 关键词条件
        if ($keywords) {
            $type = $this->post('type');
            $type = trim($this->get('type')) ?  trim($this->get('type')) : $type;
            switch($type) {
                case 'id' :
                    $where_sql[] = "id = '$keywords'";
                    break;
                case 'username' :
                    $where_sql[] = "username like '%$keywords%'";
                    break;
                case 'nickname' :
                    $where_sql[] = "nickname like '%$keywords%'";
                    break;
                case 'deviceCode' :
                    $where_sql[] = "deviceCode = '$keywords'";
                    break;
            }
        }

        // 状态
        $status = intval($this->post('status'));
        if ($status > 0) {
            $where_sql[] = "status = '" . ($status - 1) . "'";

            $this->assign('status', $status);
        }

        // 手机
        $cellphone = intval($this->post('cellphone'));
        if ($cellphone > 0) {
            $where_sql[] = ($cellphone - 1) ? "cellphoneNo <> ''" : "cellphoneNo is null";

            $this->assign('cellphone', $cellphone);
        }

        // 邮箱
        $email = intval($this->post('email'));
        if ($email > 0) {
            $where_sql[] = ($email - 1) ? "email <> ''" : "email is null";

            $this->assign('email', $email);
        }

        // 性别
        $gender = intval($this->post('gender'));
        if ($gender > 0) {
            if ($gender < 3) {
                $where_sql[] = "gender = '" . ($gender - 1) . "'";
            } else {
                $where_sql[] = "gender is null";
            }

            $this->assign('gender', $gender);
        }

        // 注册时间
        $reg_time1 = $this->post('reg_time1');
        if ($reg_time1) {
            $where_sql[] = "createDate >= '{$reg_time1}'";
            $this->assign('reg_time1', $reg_time1);
        }
        $reg_time2 = $this->post('reg_time2');
        if ($reg_time2) {
            $where_sql[] = "createDate <= '{$reg_time2}'";
            $this->assign('reg_time2', $reg_time2);
        }

        // 最后登录时间
        $last_time1 = $this->post('last_time1');
        if ($last_time1) {
            $where_sql[] = "lastSigninDate >= '{$last_time1}'";
            $this->assign('last_time1', $last_time1);
        }
        $last_time2 = $this->post('last_time2');
        if ($last_time2) {
            $where_sql[] = "lastSigninDate <= '{$last_time2}'";
            $this->assign('last_time2', $last_time2);
        }

        $where_str = $where_sql ? (implode(' and ', $where_sql)) : array();
        $query = $this->db->select('count(*) as num')->from('User')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];

        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        $this->db->select('a.*, IFNULL((SELECT a.id FROM  ' . $this->_tables['homepagedata'] . ' c 
                            WHERE c.itemType=4 AND c.itemId=a.id), 0) AS digest', false)
        				->from('User a')
        				->where($where_str);
        // 排序字段
        $order_field = $this->post('orderField');
        // 排序方式
        $order_direction = $this->post('orderDirection');
        if ($order_field && $order_direction) {
            $this->db->order_by($order_field, $order_direction);
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->limit($paginate['per_page_num'], $paginate['offset'])->get();
        /*$sql = "SELECT a.*
  from ( select id from User ";
        
        $where_str && $sql .= " where {$where_str} ";
       $sql.="  limit {$paginate['per_page_num']} offset {$paginate['offset']} ) b
 inner join User a
    on b.id = a.id ";
       //echo $sql;exit;
        $query = $this->db->query($sql);*/
        //exit;
        //$query = mysql_query($sql);
        
        $list = $query->result_array();
        
        

        $this->assign(compact('list', 'keywords', 'type', 'order_field', 'order_direction'));

        $this->display('index');
    }

    /**
     * 高级搜索
     */
    function advsearch() {
        $this->display('adv_search');
    }

    /**
     * 隐私信息
     */
    function private_info() {
        // 用户ID号
        $id = $this->get('id');

        $user = $this->db->get_where('User', array('id' => $id))->row_array();
        $user['birthdate'] = substr($user['birthdate'], 0, 10);
        $this->assign('user', $user);

        $this->display('private_info');
    }

    /**
     * 详细信息
     */
    function detail() {
        $keywords = $this->get('keywords') ? $this->get('keywords') : $this->post('keywords');
        $type = $this->get('type') ? $this->get('type') : $this->post('type');
        $this->assign(array(
                'type' => $type,
                'keywords' => $keywords
        ));

        // 获取用户的信息
        $user = $this->db->get_where('User', array($type => $keywords))->row_array();
        if (empty($user)) {
            $this->error('没有该用户信息，请检查');
        }
        
        // 获取用户是否被屏蔽
        $row = $this->db->get_where('BannedDeviceCode', array('deviceCode' => $user['deviceCode']))->row_array();
        if($row) {
            $user['banned'] = 1;
        } else {
            $user['banned'] = 0;
        }
        
        //取得用户等级
        
        $level = get_user_level($user['exp']);
		
        $this->assign(compact('user','level'));

        $this->display('detail');
    }

    /**
     * 签到
     */
    function find_checkin() {
        $id = $this->get('id');

        $where_str = "po.placeId = p.id and po.type = 1 and po.uid = '{$id}'";
        $query = $this->db->select('count(*) as num')->from('Post po, Place p')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }
        $query = $this->db->select('po.*, p.id as pid, p.placename')->from('Post po, Place p')->order_by('po.id', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('checkin');
    }

    /**
     * 点评
     */
    function find_tip() {
        $id = $this->get('id');

        $where_str = "po.placeId = p.id and po.type = 2 and po.uid = '{$id}' and po.status < 2";
        $query = $this->db->select('count(*) as num')->from('Post po, Place p')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }
        $query = $this->db->select('po.*, p.id as pid, p.placename')->from('Post po, Place p')->order_by('po.id', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('tip');
    }

    /**
     * 图片
     */
    function find_photo() {
        $id = $this->get('id');

        $where_str = "po.placeId = p.id and po.type = 3 and po.uid = '{$id}' and po.status < 2";
        $query = $this->db->select('count(*) as num')->from('Post po, Place p')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }
        $query = $this->db->select('po.*, p.id as pid, p.placename')->from('Post po, Place p')->order_by('po.id', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('photo');
    }

    /**
     * 回复
     */
    function find_reply() {
        $id = $this->get('id');

        $where_str = "p.id = pr.postId and pr.uid = '{$id}' and pr.status < 2";
        $query = $this->db->select('count(*) as num')->from('Post p, PostReply pr')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'pcreateDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }
        $query = $this->db->select('p.*, pr.id as pid, pr.uid as puid, 
                                    pr.createDate as pcreateDate, pr.content as pcontent,
                                    pr.status as pstatus')->from('Post p, PostReply pr')->order_by('pr.id', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('reply');
    }

    /**
     * 勋章
     */
    function find_badge() {

    }

    /**
     * 地主
     */
    function find_mayor() {
        $id = $this->get('id');

        $where_str = "mayorId = '{$id}'";
        $query = $this->db->select('count(*) as num')->from('Place p')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'mayorDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }
        $query = $this->db->select('*')->from('Place')->order_by('mayorDate', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('mayor');
    }

    /**
     * 关注
     */
    function find_follow() {
        $id = $this->get('id');

        $where_str = "follower = '{$id}' and beFollower = id";
        $query = $this->db->select('count(*) as num')->from('UserShip us, User u')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'ucreateDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }

        $query = $this->db->select('*, us.createDate as ucreateDate')->from('UserShip us, User u')->order_by('us.createDate', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('follow');
    }

    /**
     * 粉丝
     */
    function find_befollow() {
        $id = $this->get('id');

        $where_str = "beFollower = '{$id}' and follower = id";
        $query = $this->db->select('count(*) as num')->from('UserShip us, User u')->where($where_str)->get();
        $row = $query->row_array();
        $total_num = $row['num'];
        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'ucreateDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
        $this->assign(compact('order_field', 'order_direction'));
        // if ($order_field && $order_direction) {
        $this->db->order_by($order_field, $order_direction);
        // }

        $query = $this->db->select('*, us.createDate as ucreateDate')->from('UserShip us, User u')->order_by('us.createDate', 'desc')->where($where_str)->limit($paginate['per_page_num'], $paginate['offset'])->get();
        $list = $query->result_array();

        $this->assign('list', $list);

        $this->display('befollow');
    }

    /**
     * 改变状态
     */
    function change_status() {
        $id = $this->get('id');
        $status = $this->get('status');

        // 如果状态为1，那么为屏蔽用户
        if($status == 1) {
            // 扣除用户500分
            $this->load->helper('ugc');
			make_point($id, 'banned_user', "0", $id);
        }
        
        // 修改状态
        if ($this->db->where('id', $id)->update('User', array('status' => $status))) {
            
            // 更新缓存
            api_update_cache('User', $id);
            
            $this->load->helper('search');
            // 更新用户
            @update_index(30, $id, $status==1?'delete':'update');
            
            $this->success('修改用户状态成功', '', '', '', array(
                    'id' => $id,
                    'key' => $status
            ));
        } else {
            $this->error('修改用户状态失败');
        }
    }

    /**
     * 改变设备状态
     */
    function change_sensor() {
        $id = $this->get('id');
        $status = $this->get('status');

        // 查询出用户的信息
        $user = $this->db->get_where('User', array('id' => $id))->row_array();
        
        if(empty($status)) {
            // 删除屏蔽设备
            $this->db->delete('BannedDeviceCode', array('deviceCode' => $user['deviceCode']));
        } else {
            // 需要去扣除用户的分数
            $list = $this->db->where('deviceCode', $user['deviceCode'])
                             ->select('id')->get('User')->result_array();
            $ids = array($id);
            foreach($list as $row) {
                if(!in_array($row['id'], $ids)) {
                    $ids[] = $row['id'];
                }
            }
            $this->load->helper('ugc');
			make_point($ids, 'banned_user', "0", $id);
        }
        if($user['deviceCode']) {
            $b = $this->db->where('deviceCode', $user['deviceCode'])->update('User', array('status' => $status));
            $row = $this->db->get_where('BannedDeviceCode', array('deviceCode' => $user['deviceCode']))->row_array();
            if(empty($row) && $status) {
                $b &= $this->db->insert('BannedDeviceCode', array('deviceCode' => $user['deviceCode']));
            }
        } else {
            $b = $this->db->where('id', $id)->update('User', array('status' => $status));
        }
        // 修改状态
        if ($b) {
            // 更新缓存
            api_update_cache('User', $id);
            $rtn = api_update_cache('BannedDeviceCode');
            $this->success('修改用户设备状态成功', '', '', '', array(
                    'id' => $id,
                    'key' => $status,
                    'ban' => $rtn
            ));
        } else {
            $this->error('修改用户设备状态失败');
        }
    }

    /**
     * 用户主页
     */
    function user_home() {
        $id = $this->get('id');
        
        $url = get_web_url('user', array($id));
        
        $this->success('', '', '', '', array('url'=>$url));
    }
    
    
    function reset_pwd(){
    	$uid = $this->get('uid');
    	
    	if($this->is_post()){
    		//var_dump($uid,$_POST);exit;
    		$password = $this->post("password");
    		
    		$set = array(
    			'password' => strtoupper(md5($password))
    		);
    		$b = $this->db->where("id",$uid)->update($this->_tables['user'],$set);
    		if($b){
    			api_update_cache($this->_tables['user'],array($id));
    			$this->success("修改成功",null,null,"closeCurrent");
    		}
    		else{
    			$this->error("修改失败，请稍后再试");
    		}
    		//$b ? $this->success("修改成功",null,null,"closeCurrent") : 
    	}
    	
    	$this->display("reset_pwd");
    }

    /**
     * 用户的积分日志
     */
    function point_log() {
        $id = intval($this->get('id'));
        
        /*
         * 按日期
         * */
        $start_date = $this->get('start_date');
        $start_date && $start_date = date('Y-m-d H:i:s',$start_date);
        if($id <= 0) {
            $this->error('错误的ID号');
        }
       
        $where_str = "a.uid = '{$id}' AND a.pointCaseId = b.id";
        
        $start_date && $this->db2->where("a.createDate >  '".$start_date."' ",null,false);
        $start_date && $this->db2->where("a.point > 0 ",null,false);
        $total_num = $this->db2->from('UserPointLog a, UserPointCase b')
                        ->where($where_str, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
                
        $start_date && $this->db2->where("a.createDate >  '".$start_date."' ",null,false);
        $start_date && $this->db2->where("a.point > 0 ",null,false);
        						
        $list = $this->db2->select('a.*, b.type, b.caseName')
                            ->from('UserPointLog a, UserPointCase b')
                            ->order_by('a.' . $order_field, $order_direction)->where($where_str, null, false)
                            ->limit($paginate['per_page_num'], $paginate['offset'])
                            ->get()->result_array();
        
        foreach($list as &$row) {
            $remark = json_decode($row['remark'], true);
            if(empty($remark)) {
                // 老数据
                $remark = array('remark' => $row['remark'], 'id' => '', 'action' => '');
            }
            $row['reason'] = $remark;
        }
        unset($row);
        
        $start_date && $this->db2->where("createDate >  '".$start_date."' ",null,false);
        // 计算+合计 -合计
        $row = $this->db2->select_sum('point')->where(array('uid' => $id))
                            ->where('point >', 0, false)->get($this->_tables['userpointlog'])->row_array();
        $sum_plus = intval($row['point']);
        $start_date && $this->db2->where("createDate >  '".$start_date."' ",null,false);
        $row = $this->db2->select_sum('point')->where(array('uid' => $id))
                            ->where('point <', 0, false)->get($this->_tables['userpointlog'])->row_array();
        $sum_minus = intval($row['point']);
        
        $item_type = $this->config->item('item_type');
        $types = array();
        if($item_type) {
            foreach($item_type as $k => $v) {
                $types[$k] = $v['value'];
            }
        }
        $is_dialog = true;
        $this->assign(compact('is_dialog', 'types', 'order_field', 'order_direction', 'list', 'sum_plus', 'sum_minus','start_date'));
        
        $this->display('point_log');
    }
    
    /**
     * 
     * 用户经验
     */
	function exp_log() {
        $id = intval($this->get('id'));
        
        /*
         * 按日期
         * */
        $start_date = $this->get('start_date');
        $start_date && $start_date = date('Y-m-d H:i:s',$start_date);
        if($id <= 0) {
            $this->error('错误的ID号');
        }
       
        $where_str = "a.uid = '{$id}' AND a.expCaseId = b.id";
        
        $start_date && $this->db2->where("a.createDate >  '".$start_date."' ",null,false);
        $start_date && $this->db2->where("a.exp > 0 ",null,false);
        $total_num = $this->db2->from('UserExpLog a, UserExpCase b')
                        ->where($where_str, null, false)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        // 排序字段
        $order_field = $this->post('orderField');
        $order_field || $order_field = 'createDate';
        // 排序方式
        $order_direction = $this->post('orderDirection');
        $order_direction || $order_direction = 'desc';
                
        $start_date && $this->db2->where("a.createDate >  '".$start_date."' ",null,false);
        $start_date && $this->db2->where("a.exp > 0 ",null,false);
        						
        $list = $this->db2->select('a.*, b.type, b.caseName')
                            ->from('UserExpLog a, UserExpCase b')
                            ->order_by('a.' . $order_field, $order_direction)->where($where_str, null, false)
                            ->limit($paginate['per_page_num'], $paginate['offset'])
                            ->get()->result_array();
        
        foreach($list as &$row) {
            $remark = json_decode($row['remark'], true);
            if(empty($remark)) {
                // 老数据
                $remark = array('remark' => $row['remark'], 'id' => '', 'action' => '');
            }
            $row['reason'] = $remark;
        }
        unset($row);
        
        $start_date && $this->db2->where("createDate >  '".$start_date."' ",null,false);
        // 计算+合计 -合计
        $row = $this->db2->select_sum('exp')->where(array('uid' => $id))
                           ->get($this->_tables['userexplog'])->row_array();
        $sum_plus = intval($row['exp']);
        /*$start_date && $this->db2->where(
        							array("createDate > " =>" '".$start_date."' "));
        $row = $this->db2->select_sum('exp')->where(array('uid' => $id))
                            ->where('exp <', 0, false)->get($this->_tables['userexplog'])->row_array();
        $sum_minus = intval($row['exp']);*/
        
        $item_type = $this->config->item('item_type');
        $types = array();
        if($item_type) {
            foreach($item_type as $k => $v) {
                $types[$k] = $v['value'];
            }
        }
        $is_dialog = true;
        $this->assign(compact('is_dialog', 'types', 'order_field', 'order_direction', 'list', 'sum_plus', 'sum_minus','start_date'));
        
        $this->display('exp_log');
    }
    
    
    /**
     * 推荐到首页
     */
    function recommend() {
        $id = $this->get('id');
    
        $user = $this->m_user->select(array('id' => $id));

        if($user && $user['status'] != 1) {
            // 正常状态
            $this->load->helper('home');
            if($this->is_post()) {
                // 提交数据过来
                $b = recommend_digest_post(4, $id);
                $b===0?$this->success('推荐成功', '', '', 'closeCurrent'):$this->error($b);
            }
    
            recommend_digest(4, $id, image_url($user['avatar'], 'head', 'odp'), false, true, 'avatar');
        } else {
            $this->error('用户状态非正常状态，不能推荐');
        }
    }
    /*
     * 异常管理
     * */
    function unusual() {
    	/* 7 days */
    	$this->display("unusual");
    }
    
	function unusual_point() {
    	/* 7 days */
		
		$start_date =  TIMESTAMP - 7*24*3600 ;
		
		//echo $start_date;
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
		
    	
    	$st = date("Y-m-d H:i:s",$start_date);
    	$sql = 'select count(DISTINCT uid) as total from '.$this->_tables['userpointlog'].' where point > 0 and createDate > \''.$st.'\' ';
    	
    	$total = $this->db->query($sql)->row_array();
    	
		if($total['total']){
    		$parr = $this->paginate($total['total']);
    	}
    	
		$this->db->select('uid,username,nickname,avatar,sum(upl.point) as total');
		$this->db->from('User u');
		$this->db->join('UserPointLog upl','upl.uid=u.id','right');
		$this->db->where('upl.point > 0',null,false);
		$this->db->where('upl.createDate > \''.$st.'\'',null,false);
		$this->db->group_by('uid');
		//$this->db->having('total > 0'); 不需要的样子
		$this->db->order_by('total','desc');
		$this->db->limit($parr['per_page_num'],$parr['offset']);
		$list = $this->db->get()->result_array();
		
		
		$this->assign(compact('parr','list','start_date','numPerPage'));
    	$this->display("unusual_point");
    }
    
	function unusual_exp() {
    	/* 7 days */
		
		$start_date =  TIMESTAMP - 7*24*3600 ;
		
		//echo $start_date;
		$pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
    	$numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
		
    	
    	$st = date("Y-m-d H:i:s",$start_date);
    	$sql = 'select count(DISTINCT uid) as total from '.$this->_tables['userexplog'].' where exp > 0 and createDate > \''.$st.'\' ';
    	
    	$total = $this->db->query($sql)->row_array();
    	
		if($total['total']){
    		$parr = $this->paginate($total['total']);
    	}
    	
		$this->db->select('uid,username,nickname,avatar,sum(upl.exp) as total');
		$this->db->from('User u');
		$this->db->join('UserExpLog upl','upl.uid=u.id','right');
		$this->db->where('upl.exp > 0',null,false);
		$this->db->where('upl.createDate > \''.$st.'\'',null,false);
		$this->db->group_by('uid');
		//$this->db->having('total > 0'); 不需要的样子
		$this->db->order_by('total','desc');
		$this->db->limit($parr['per_page_num'],$parr['offset']);
		$list = $this->db->get()->result_array();
		
		$this->assign(compact('parr','list','start_date','numPerPage'));
    	$this->display("unusual_exp");
    }
}
