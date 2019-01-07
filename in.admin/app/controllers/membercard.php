<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 会员卡
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-25
 */

class Membercard extends MY_Controller {
    var $lan;
    function __construct() {
        parent::__construct();
        
        $this->lang->load('brand');
        $this->lan = $this->lang->line('card');
    }
    
    /**
     * 会员管理列表
     */    
    function index() {
        
        
        $this->display('index');
    }
    
    /**
     * 待审批
     */
    function apply() {
        // if($this->is_post()) {
            $title = trim($this->post('title'));

            $where_sql = 'a.status = 0 AND a.brandId = b.id';
            if($title !== '') {
                $title_txt = daddslashes($title);
                $where_sql .= " AND a.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyBrandMemberCard a, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("a.*, b.name as brand", false)
                             ->order_by('createDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyBrandMemberCard a, Brand b')
                             ->get()->result_array();

            $this->assign(compact('title', 'list'));
        // }
        
        $this->display('apply');
    }
    
    /**
     * 已审核
     */
    function checked() {
        if($this->is_post()) {
            $title = trim($this->post('title'));

            $where_sql = array();
            if($title !== '') {
                $title_txt = daddslashes($title);
                $where_sql = "a.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('BrandMemberCard a')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("a.*, max(c.checkDate) as checkDate, b.name as brand", false)
                             ->order_by('a.rankOrder', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->group_by('a.id')
                             ->from('BrandMemberCard a')
                             ->join('Brand b', 'a.brandId = b.id', 'left')
                             ->join('ApplyBrandMemberCard c', 'a.id = c.cardId', 'left')
                             ->get()->result_array();

            $this->assign(compact('title', 'list'));
        }
        
        $this->display('checked');
    }
    
    /**
     * 驳回
     */
    function reject() {
        if($this->is_post()) {
            $title = trim($this->post('title'));

            $where_sql = 'a.status = 10 AND a.brandId = b.id';
            if($title !== '') {
                $title_txt = daddslashes($title);
                $where_sql .= " AND a.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyBrandMemberCard a, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("a.*, b.name as brand", false)
                             ->order_by('checkDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyBrandMemberCard a, Brand b')
                             ->get()->result_array();

            $this->assign(compact('title', 'list'));
        }
        
        $this->display('reject');
    }
    
    /**
     * 会员卡编辑页面
     */
    function edit() {
        // 会员卡ID号
        $id = intval($this->get('id'));
        // 选出会员卡
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取会员卡信息
        $card = $this->db->get_where('ApplyBrandMemberCard', array('id' => $id))->row_array();
        if(empty($card)) {
            $this->error('错误的会员卡');
        }
        
        if($card['status'] != 0) {
            // 会员卡不在待审核状态
            $this->error('会员卡' . ($card['status']==10?'已驳回':'已通过'));
        }
        
        if($this->is_post()) {
            $status = $this->post('status');
            if($status == 10) {
                // 驳回
                // 直接处理信息返回给用户
                $remark = trim($this->post('remark'));
                if($remark === '') {
                    $this->error('请输入驳回备注');
                }
                
                $b = $this->db->update('ApplyBrandMemberCard', array('status' => 10, 'remark' => $remark, 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                
                // 给品牌商家发消息
                $this->db->insert('BrandMessage', array('brandId' => $card['brandId'], 'title' => sprintf($this->lan['reject']['title'], $card['title']), 'content' => $remark));
                
                $b?$this->success('驳回会员卡成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('驳回会员卡失败');
            } elseif(20 == $status) {
                // 通过
                // 写入正式会员卡
                $is_basic = $card['isBasic'];
                $card_id = $card['cardId'];
                if($is_basic && empty($card_id)) {
                    // 如果是基础会员卡，会员卡号为空，错误数据
                    $this->error('基础会员卡信息错误');
                }
                $title = $is_basic?$card['title']:trim($this->post('title'));
                $content = trim($this->post('content'));
                $summary = trim($this->post('summary'));
                $image = array_filter($this->post('image'));
                if(($is_basic?false:empty($title)) || empty($content) || empty($image) || empty($summary)) {
                    $this->error('请检查数据完整性');
                }

                if(dstrlen($summary) > 60) {
                    $this->error('特权摘要不超过60个英文，30个中文');
                }
                
                $data = array(
                    'title' => $title,
                    'content' => $content,
                    'summary' => $summary,
                    'image' => $card['image'],
                    'isBasic' => $is_basic,
                    'brandId' => $card['brandId'],
                    'conditions' => '{}'
                );
                if($card_id) {
                    // 修改
                    $b = $this->db->update('BrandMemberCard', $data, array('id' => $card_id));
                } else {
                    // 新建
                    $b = $this->db->insert('BrandMemberCard', $data);
                    $card_id = $this->db->insert_id();
                }
                                
                // 写入申请表审核信息
                $b &= $this->db->update('ApplyBrandMemberCard', array('cardId' => $card_id, 'status' => 20, 'remark' => $this->post('remark'), 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                
                // 给商家用户发信息
                $this->db->insert('BrandMessage', array('brandId' => $card['brandId'], 'title' => sprintf($this->lan['pass']['title'], $card['title']), 'content' => ''));
            
                $b?$this->success('通过会员卡成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('通过会员卡失败');
            }
        }
        
        $this->assign(compact('card'));
        
        $this->display('edit');
    }
    
    /**
     * 浏览会员卡
     * 
     */
    function view() {
        $type = trim($this->get('type'));
        
        if($type && $type != 'apply') {
            $this->error('错误');
        }
        $type && $type = ucwords($type);
        
        // 会员卡ID号
        $id = intval($this->get('id'));
        // 选出会员卡
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取会员卡信息
        $card = $this->db->get_where($type . 'BrandMemberCard', array('id' => $id))->row_array();
        if(empty($card)) {
            $this->error('错误的会员卡');
        }
        
        $view = true;
        $this->assign(compact('card', 'view'));
        
        $this->display('edit');
    }
}
