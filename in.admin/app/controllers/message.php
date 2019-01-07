<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 消息中心
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-11
 */
class Message extends MY_Controller {
    var $link_type;
    
    function __construct() {
        parent::__construct();
        
        $this->link_type = $this->config->item('link_type');
        $this->assign('link_type', $this->link_type);
    }
    
    /**
     * 用户反馈
     */
    function feedback() {
        $is_confirm = $this->post('is_confirm');
        $type = $this->post('type');
        $keywords = $this->post('keywords');
        
        $type || $type = 'nickname';
        $where_sql = array();
        if($keywords !== '') {
            switch($type) {
                case 'nickname':
                    $where_sql[] = "u.nickname like '%{$keywords}%'";
                    break;
                case 'uid':
                    $where_sql[] = "f.uid = '{$keywords}'";
                    break;
                default:
                    $where_sql[] = "f.content like '%{$keywords}%'";
            }
        }
        
        $is_confirm && $where_sql[] = "f.status = 1";
        $where_sql && $where_sql = implode(' and ', $where_sql);
        
        $total_num = $this->db->where($where_sql)->from('UserFeedBack f')->join('User u', 'f.uid = u.id', 'left')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('f.*, u.nickname, u.lastIp, u.avatar')
                         ->order_by('f.createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('UserFeedBack f')
                         ->join('User u', 'f.uid = u.id', 'left')->get()->result_array();
        $this->assign(compact('is_confirm', 'type', 'keywords', 'list'));
        
        $this->display('feedback');
    }

    /**
     * 确认反馈
     */
    function confirm_feedback() {
        $id = intval($this->get('id'));
        
        $id || $this->error('错误');
        
        $row = $this->db->get_where('UserFeedBack', "id = '{$id}'")->row_array();
        
        if($row) {
            if($row['status']) {
                $this->error('本条反馈已经处理过了');
            }
            
            if($row['uid']) {
                $this->load->helper('ugc');
                make_point($row['uid'], 'user_feedback', "0", $id);
                // 给用户奖励
                send_message('sm_feedback_confirm' . (rand(0, 1)), $row['uid'], $row['uid'], 4, true);
            }
            
            // 设定处理标志
            $b = $this->db->update('UserFeedBack', array('status'=>1), "id ='{$id}'");
            
            
            $b?$this->success('确认处理成功', '', '', '', array('id'=>$id)):$this->error('确认处理反馈失败');
        } else {
            $this->error('错误');
        }
    }

    /**
     * 发送私信
     */
    function send_user_message() {
        $id = intval($this->get('id'));
        
        $id || $this->error('错误');
        
        $row = $this->db->get_where('UserFeedBack', "id = '{$id}'")->row_array();
        
        if($row['uid']) {
            // $this->success('', '', '', '', array('id'=> $row['uid']));
            forward(site_url(array('ugc', 'reply', 'send', 'reply_type', '2', 'receiver', $row['uid'])));
        } else {
            $this->error('该反馈是匿名用户提交的，不能发送私信', '', '', 'closeCurrent');   
        }
    }
    
    /**
     * 全局消息
     */
    function global_message() {
        $keywords = $this->post('keywords');
        
        $where_sql = array();
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql = "content like '%{$keytext}%'";
        }
        
        $total_num = $this->db->where($where_sql)->from('GlobalMessage')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('a.*', false)
                         ->order_by('createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('GlobalMessage a')
                         ->get()->result_array();
        
        $this->assign(compact('keywords', 'list'));
        
        $this->display('global_message');
    }
    
    /**
     * 发送全局消息
     */
    function send_global_message() {
        if($this->is_post()) {
            // 提交全局消息
            $content = trim($this->post('content'));
            if(strlen(content) <= 0) {
                $this->error('请输入发送内容');
            }
            
            if(strlen($content) > 420) {
                $this->error('内容不能超过420个字符，中文不超过140个字');
            }
            
            $data = array();
            $data['content'] = $content;
            $data['type'] = $this->post('item_type');
            $data['expiredDate'] = $this->post('expired_date');
            if(strtotime($data['expiredDate']) <= (time() + 86400)) {
                $this->error('过期时间必须大于1天，亲，要不然出问题哈。');
            }
            if(in_array($data['type'], array(1, 4))) {
                $data['itemId'] = $this->post('content_id');
            } else {
                $data['itemId'] = $this->post('item_id');
            }
            $protocol = $this->link_type[$data['type']]['key'];
            if($protocol) {
                $pre_url = $protocol . '://';
                if(strpos($data['itemId'], $pre_url) === 0) {
                    // 如果开始部分为协议部分
                    $data['relatedHyperLink'] = $data['itemId'];
                } else {
                    $data['relatedHyperLink'] = $pre_url . $data['itemId'];
                }
            } else {
                $data['relatedHyperLink'] = $data['itemId'];
            }
            $data['type'] = 25;
            $b = $this->db->insert('GlobalMessage', $data);
            $gm_id = $this->db->insert_id();
            
            if($b) {
                // 调用接口发送消息
                $this->lang->load('api');
                send_api_interface($this->lang->line('api_msg_global'), 'POST', array('gm_id' => $gm_id));
                
                $arr = array('message', 'global_message');
                $this->success('发送全局消息成功', build_rel($arr), site_url($arr), 'closeCurrent');
            } else {
                $this->error('发送全局消息失败');
            }
        }
        
        $this->display('add_global_message');
    }
    
    /**
     * 系统消息
     */
    function system_message() {
        $keywords = $this->post('keywords');
        
        $where_sql = 'sm.sendType = 1';
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " and sm.content like '%{$keytext}%'";
        }
        
        $total_num = $this->db->where($where_sql)->from('SystemMessage sm')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select('sm.*, u.nickname')
                         ->order_by('sm.createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql . ' and sm.recieverId = u.id')
                         ->from('SystemMessage sm, User u')
                         ->get()->result_array();
        $this->assign(compact('keywords', 'list'));
        
        $this->display('system_message');
    }
    
    /**
     * 发送系统消息
     */
    function send_system_message() {
        if($this->is_post()) {
            // 提交全局消息
            $content = trim($this->post('content'));
            if(strlen(content) <= 0) {
                $this->error('请输入发送内容');
            }
            
            if(strlen($content) > 420) {
                $this->error('内容不能超过420个字符，中文不超过140个字');
            }
            
            $data = array();
            $data['content'] = $content;
            $data['type'] = $this->post('item_type');
            $data['recieverId'] = $this->post('user_id');
            // 发送类型1为机甲手动发送，默认为0
            $data['sendType'] = 1;
            if(in_array($data['type'], array(1, 4))) {
                $data['itemId'] = $this->post('content_id');
            } else {
                $data['itemId'] = $this->post('item_id');
            }
            $protocol = $this->link_type[$data['type']]['key'];
            if($protocol) {
                $pre_url = $protocol . '://';
                if(strpos($data['itemId'], $pre_url) === 0) {
                    // 如果开始部分为协议部分
                    $data['relatedHyperLink'] = $data['itemId'];
                } else {
                    $data['relatedHyperLink'] = $pre_url . $data['itemId'];
                }
            } else {
                $data['relatedHyperLink'] = $data['itemId'];
            }
            
            $b = $this->db->insert('SystemMessage', $data);
            $sys_msg_id = $this->db->insert_id();
            
            if($b) {
                // 调用接口发送消息
                $this->lang->load('api');
                $data = send_api_interface($this->lang->line('api_msg_system'), 'POST', array('sys_msg_id' => $sys_msg_id));
                
                $arr = array('message', 'system_message');
                $this->success('发送系统消息成功', build_rel($arr), site_url($arr), 'closeCurrent', array('value' => $data));
            } else {
                $this->error('发送系统消息失败');
            }
        }
        
        $this->display('add_system_message');
    }
}
