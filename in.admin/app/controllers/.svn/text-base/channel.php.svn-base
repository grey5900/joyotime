<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 渠道商管理
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-10
 * 表ChannelInfo ChannelUser FailedLogin
 * 1.初始化渠道商
 * INSERT INTO ChannelInfo(id, name, parentId, ratio, createDate, startDate, `status`, remark)
    SELECT concat('10', id), name, 0, 1, current_timestamp, null, 0, null FROM MerchantInfo WHERE cid=70
 * 2.初始化渠道 需要特殊处理 万普世纪 这个渠道，之前他的渠道号是他自己，需要改为168
 * INSERT INTO ChannelInfo(id, name, parentId, ratio, createDate, startDate, `status`, remark)
    SELECT mi.code, mi.name, concat('10', mi.parentId), c.custRatio, 
    current_timestamp, c.coopStartTime, 0, null
    FROM MerchantInfo mi, Channel c WHERE mi.code = c.id AND mi.cid=71
 * 以上两步已经完成，根据需求已经手动完成
 * 3.需要去更新统计中的两个表数据StatDeviceInfo StatUserDeviceChannel中的字段channelId及merchantId
 * UPDATE StatDeviceInfo s, MerchantInfo m SET s.channelId = m.code WHERE s.channelId = m.id
 * UPDATE StatDeviceInfo s, ChannelInfo c SET s.merchantId = c.parentId WHERE s.channelId = c.id
 * UPDATE StatUserDeviceChannel s, MerchantInfo m SET s.channelId = m.code WHERE s.channelId = m.id
 * UPDATE StatUserDeviceChannel s, ChannelInfo c SET s.merchantId = c.parentId WHERE s.channelId = c.id
 * 4.需要去修改结算统计及用户统计中的功能
 * 5.需要修改定时任务中的数据库写入程序
 */

class Channel extends MY_Controller {
    /**
     * 渠道管理
     */
    function index() {
        $keywords = $this->post('keywords');
        
        $where_sql = 'c1.parentId > 0 and c1.parentId = c2.id';
        if($keywords !== '') {
            $type = $this->post('type');
            $keytext = daddslashes($keywords);
            if($type < 3) {
                // 用like
                $where_sql .= " and " . ($type==1?'c1.name':'c2.name') . " like '%{$keytext}%'"; 
            } else {
                // 用=
                $where_sql .= " and c1.id = '{$keytext}'";
            }
        }
        
        $total_num = $this->db->where($where_sql)->from('ChannelInfo c1, ChannelInfo c2')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('c1.*, c2.name as merchantName')
                         ->order_by('c1.id', 'asc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ChannelInfo c1, ChannelInfo c2')
                         ->get()->result_array();
        
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('index');
    }

    /**
     * 渠道商
     */
    function merchant() {
        $keywords = $this->post('keywords');
        
        $where_sql = 'parentId = 0';
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " and name like '%{$keytext}%'"; 
        }
        
        $total_num = $this->db->where($where_sql)->from('ChannelInfo')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('id', 'asc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ChannelInfo')
                         ->get()->result_array();
        
        $this->assign(compact('keywords', 'list'));
        
        $this->display('merchant');
    }
    
    /**
     * 添加渠道商
     */
    function merchant_add() {
        $this->assign('dialog_id', 'merchant_add');
        $this->_add_channel('');
    }
    
    function merchant_edit() {
        $this->assign('dialog_id', 'merchant_edit');
        $this->_add_channel('');
    }

    /**
     * 添加渠道或渠道商
     */
    function add() {
        $this->assign('dialog_id', 'add');
        $this->_add_channel();
    }
    
    /**
     * 修改渠道
     */
    function edit() {
        $this->assign('dialog_id', 'edit');
        $this->_add_channel();
    }
    
    /**
     * 添加及编辑渠道
     */
    function _add_channel($type = 'channel') {
        $id = $this->get('id');
        
        if($id) {
            // 去获取渠道信息
            $channel = $this->db->get_where('ChannelInfo', array('id'=>$id))->row_array();
            if(empty($channel)) {
                $this->error('错误的渠道信息');
            }
        }
        
        if($this->is_post()) {
            $name = trim($this->post('name'));
            if(empty($name)) {
                $this->error('请输入名称');
            }
            
            $remark = $this->post('remark');
            if(cstrlen($remark) > 255) {
                $this->error('备注不能超过255个字哦，亲');
            }
            $data = array('name' => $name, 'remark' => $remark);
            if($type == 'channel') {
                // 渠道操作
                $parent_id = $this->post('parent_id');
                $parent_id && $data['parentId'] = $parent_id;
                $ratio = $this->post('ratio');
                $ratio && $data['ratio'] = number_format(floatval(abs($ratio))/100, 2);
                $data['startDate'] = $this->post('start_date');
            }
            
            $tip = '修改';
            if($channel) {
                // 修改
                $b = $this->db->update('ChannelInfo', $data, array('id' => $channel['id']));
            } else {
                // 新建
                $b = $this->db->insert('ChannelInfo', $data);
                $id = $this->db->insert_id();
                $tip = '添加';
            }
            $arr = array('channel', 'merchant');
            $b?$this->success($tip . '渠道信息成功啦', build_rel($arr), site_url($arr), 'closeCurrent'):$this->error($tip . '出错');
        }
        if($type == 'channel') {
            $merchant = $this->db->order_by('id', 'asc')->get_where('ChannelInfo', array('parentId' => 0))->result_array();
            $this->assign('merchant', $merchant);
        }
        
        $this->assign('type', $type);
        strlen($channel['startDate']) > 10 && $channel['startDate'] = substr($channel['startDate'], 0, 10);
        $channel && $this->assign('channel', $channel);
        
        $this->display('add');
    }
    
    /**
     * 渠道用户
     */
    function user() {
        $keywords = $this->post('keywords');
        
        $where_sql = 'cu.channelId = c.id';
        if($keywords !== '') {
            $type = $this->post('type');
            $keytext = daddslashes($keywords);
            if($type < 3) {
                // 用like
                $where_sql .= " and " . ($type==1?'cu.username':'c.name') . " like '%{$keytext}%'"; 
            } else {
                // 用=
                $where_sql .= " and c.id = '{$keytext}'";
            }
        }
        
        $total_num = $this->db->where($where_sql)->from('ChannelUser cu, ChannelInfo c')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('cu.*, c.name as channelName')
                         ->order_by('cu.createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ChannelUser cu, ChannelInfo c')
                         ->get()->result_array();
        
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('user');
    }

    /**
     * 添加渠道用户
     */
    function user_add() {
        $id = $this->get('id');
        
        if($id) {
            // 去获取渠道用户信息
            $user = $this->db->get_where('ChannelUser', array('id'=>$id))->row_array();
            if(empty($user)) {
                $this->error('错误的渠道用户信息');
            }
        }
        
        if($this->is_post()) {
            $username = trim($this->post('username'));
            if(empty($username)) {
                $this->error('请输入用户名');
            }
            $username = daddslashes($username);
            // 用户不能重名
            $u = $this->db->get_where('ChannelUser', array('username' => $username, 'id <> ' => $id))->row_array();
            if($u) {
                $this->error('用户名已经存在请重新输入一个，亲');
            }
            
            $remark = $this->post('remark');
            if(cstrlen($remark) > 255) {
                $this->error('备注不能超过255个字哦，亲');
            }
            $data = array('username' => $username, 'remark' => $remark);
            $channel_id = $this->post('channel_id');
            $channel_id && $data['channelId'] = $channel_id;
            $data['status'] = $this->post('status');
            $password = $this->post('password');
            $password && $data['password'] = md5($password);
            
            $tip = '修改';
            if($user) {
                // 修改
                $b = $this->db->update('ChannelUser', $data, array('id' => $user['id']));
            } else {
                // 新建
                $b = $this->db->insert('ChannelUser', $data);
                $id = $this->db->insert_id();
                $tip = '添加';
            }
            $arr = array('channel', 'user');
            $b?$this->success($tip . '渠道用户信息成功啦', build_rel($arr), site_url($arr), 'closeCurrent'):$this->error($tip . '出错');
        }
        $channel = $this->db->order_by('id', 'asc')->get_where('ChannelInfo', array('parentId > ' => 0, 'status' => 0))->result_array();
        $this->assign('channel', $channel);
        
        if($user) {
            $this->assign('user', $user);
            $this->assign('is_edit', true);
        }
        $this->assign('dialog_id', 'user_add');
        
        $this->display('user_add');
    }
    
    /**
     * 编辑渠道用户
     */
    function user_edit() {
        $this->user_add();
    }
    
    /**
     * 删除渠道用户
     */
    function user_del() {
        $id = $this->get('id');
        
        if($id) {
            // 去获取渠道用户信息
            $user = $this->db->get_where('ChannelUser', array('id' => $id))->row_array();
            if(empty($user)) {
                $this->error('错误的渠道用户信息');
            }
        }
        
        $b = $this->db->delete('ChannelUser', array('id' => $id));
        
        $b?$this->success('添加渠道用户成功'):$this->error('删除渠道用户失败');
    }
}
