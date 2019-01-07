<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 优惠券
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-25
 */

class Coupon extends MY_Controller {
    var $lan;
    function __construct() {
        parent::__construct();
        
        $this->lang->load('brand');
        $this->lan = $this->lang->line('coupon');
    }
    
    /**
     * 优惠管理列表
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

            $where_sql = 'ap.status = 0 AND ap.brandId = b.id';
            if($title !== '') {
                $title_txt = daddslashes($title);
                $where_sql .= " AND ap.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyPreference ap, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("ap.*, b.name as brand, date_format(ap.endDate, '%Y-%m-%d') as expireDate", false)
                             ->order_by('createDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyPreference ap, Brand b')
                             ->get()->result_array();
            
            // 获取本月发布次数
            $ids = array();
            foreach($list as $row) {
                $ids[] = $row['brandId'];
            }
            if($ids) {
                $nums = $this->db->select('COUNT(*) AS num, brandId')
                                 ->group_by('brandId')
                                 ->where_in('brandId', $ids)
                                 ->where("status = 20 AND date_format(now(), '%m') = date_format(checkDate, '%m')", null, false)
                                 ->from('ApplyPreference')->get()->result_array();
                                 
                $data = array();
                foreach($nums as $row) {
                    $data[$row['brandId']] = $row['num'];
                }
                
                foreach($list as &$row) {
                    $row['monthCount'] = $data[$row['brandId']];
                }
                unset($row);
            }

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
                $where_sql = "p.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('Preference p')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("p.*, max(ap.checkDate) as checkDate, b.name as brand, date_format(p.endDate, '%Y-%m-%d') as expireDate", false)
                             ->order_by('max(ap.checkDate)', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->group_by('p.id')
                             ->from('Preference p')
                             ->join('ApplyPreference ap', 'p.id = ap.preferId', 'left')
                             ->join('Brand b', 'p.brandId = b.id', 'left')
                             ->get()->result_array();
            
            // 获取获得人数及审核时间
            $ids = array();
            foreach($list as $row) {
                $ids[] = $row['id'];
            }
            if($ids) {
                // 获得人数
                $nums = $this->db->select('COUNT(distinct(uid)) AS num, preferId')
                                 ->group_by('preferId')
                                 ->where_in('preferId', $ids)
                                 ->from('UserOwnPrefer')->get()->result_array();
                $data = array();
                foreach($nums as $row) {
                    $data[$row['preferId']] = $row['num'];
                }
                
                foreach($list as &$row) {
                    $row['expire'] = (now(-1) > strtotime($row['expireDate'] . ' 23:59:59'));
                    $row['userCount'] = $data[$row['id']];
                }
                unset($row);
            }

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

            $where_sql = 'ap.status = 10 AND ap.brandId = b.id';
            if($title !== '') {
                $title_txt = daddslashes($title);
                $where_sql .= " AND ap.title like '%{$title_txt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyPreference ap, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("ap.*, b.name as brand, date_format(ap.endDate, '%Y-%m-%d') as expireDate", false)
                             ->order_by('checkDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyPreference ap, Brand b')
                             ->get()->result_array();

            $this->assign(compact('title', 'list'));
        }
        
        $this->display('reject');
    }
    
    /**
     * 优惠编辑页面
     */
    function edit() {
        // 优惠ID号
        $id = intval($this->get('id'));
        // 选出优惠券
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取优惠券信息
        $prefer = $this->db->select("*, DATE_FORMAT(endDate, '%Y-%m-%d') as end_date", false)
                           ->get_where('ApplyPreference', array('id' => $id))->row_array();
        if(empty($prefer)) {
            $this->error('错误的优惠券');
        }
        
        if($prefer['status'] != 0) {
            // 优惠券不在待审核状态
            $this->error('优惠券' . ($prefer['status']==10?'已驳回':'已通过'));
        }
        
        // 获取关联地点信息
        $places = $this->db->where("a.applyPreferId = '{$id}' AND a.placeId = p.id", null, false)
                           ->get('ApplyPreferAtPlace a, Place p')->result_array();
        
        if($this->is_post()) {
            $status = $this->post('status');
            if($status == 10) {
                // 驳回
                // 直接处理信息返回给用户
                $remark = trim($this->post('remark'));
                if($remark === '') {
                    $this->error('请输入驳回备注');
                }
                
                $b = $this->db->update('ApplyPreference', array('status' => 10, 'remark' => $remark, 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                
                // 给品牌商家发消息
                $this->db->insert('BrandMessage', array('brandId' => $prefer['brandId'], 'title' => sprintf($this->lan['reject']['title'], $prefer['title']), 'content' => $remark));
                
                $b?$this->success('驳回优惠券成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('驳回优惠券失败');
            } elseif(20 == $status) {
                // 通过
                // 写入正式优惠表
                $title = trim($this->post('title'));
                $desc = trim($this->post('description'));
                $detail = trim($this->post('detail'));
                $icon = trim($this->post('icon'));
                $image = trim($this->post('image'));
                if(empty($title) || empty($icon) || empty($image) || empty($detail)) {
                    $this->error('请检查数据完整性');
                }
                // $is_unique = $prefer['isUnique'];
                $data = array(
                    'title' => $title,
                    'description' => $desc,
                    'descTitle' => trim($this->post('desc_title')),
                    'detail' => $detail,
                    'icon' => $icon,
                    'image' => $image,
                    'beginDate' => now(),
                    'endDate' => $prefer['endDate'],
                    'type' => $prefer['type'],
                    'conditions' => $prefer['conditions'],
                    'brandId' => $prefer['brandId'],
                    'isUsable' => $prefer['isUsable'],
                    // 'isUnique' => $is_unique,
                    'frequencyLimit' => $prefer['frequencyLimit']
                );
                $prefer_id = $prefer['preferId'];
                if($prefer_id) {
                    // 修改
                    $b = $this->db->update('Preference', $data, array('id' => $prefer_id));
                    // 去获取正式表中的地点信息
                    $place_own = $this->db->get_where('PlaceOwnPrefer', array('preferId' => $prefer_id))->result_array();
                    $place_ids = array();
                    foreach($place_own as $row) {
                        $place_ids[] = $row['id'];
                    }
                    // 先删除之前的地点信息
                    $b &= $this->db->where(array('preferId' => $prefer_id))->delete('PlaceOwnPrefer');
                    // 更新地点的优惠数
                    $b &= $this->db->set('preferCount', 'preferCount - 1', false)
                                   ->where('preferCount > 0', null, false)
                                   ->where_in('id', $place_ids)
                                   ->update('Place');
                } else {
                    // 新建
                    $data['status'] = 1;
                    $b = $this->db->insert('Preference', $data);
                    $prefer_id = $this->db->insert_id();
                }
                
                // 写入地点表
                $place_ids = $place_own_prefer = array();
                foreach($places as $row) {
                    $place_ids[] = $row['id'];
                    $place_own_prefer[] = array('placeId' => $row['id'], 'preferId' => $prefer_id);
                }
                // 加入新的
                $place_own_prefer && ($b &= $this->db->insert_batch('PlaceOwnPrefer', $place_own_prefer));
                // 地点的优惠数+1
                $place_ids && ($b &= $this->db->set('preferCount', 'preferCount + 1', false)
                               ->where_in('id', $place_ids)
                               ->update('Place'));
                
                // 写入申请表审核信息
                $b &= $this->db->update('ApplyPreference', array('preferId' => $prefer_id,'status' => 20, 'remark' => $this->post('remark'), 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                
                // 给商家用户发信息
                $this->db->insert('BrandMessage', array('brandId' => $prefer['brandId'], 'title' => sprintf($this->lan['pass']['title'], $prefer['title']), 'content' => ''));
            
                $b?$this->success('通过优惠券成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('通过优惠券失败');
            }
        }
        
        $this->assign(compact('prefer', 'places'));
        
        $this->display('edit');
    }
    
    /**
     * 浏览优惠
     * 
     */
    function view() {
        $type = trim($this->get('type'));
        
        if($type && $type != 'apply') {
            $this->error('错误');
        }
        $type && $type = ucwords($type);
        
        // 优惠ID号
        $id = intval($this->get('id'));
        // 选出优惠券
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取优惠券信息
        $prefer = $this->db->select("*, DATE_FORMAT(endDate, '%Y-%m-%d') as end_date", false)
                           ->get_where($type . 'Preference', array('id' => $id))->row_array();
        if(empty($prefer)) {
            $this->error('错误的优惠券');
        }
        
        $prefer['icon_url'] = image_url($prefer['icon'], 'common');
        $prefer['image_url'] = image_url($prefer['image'], 'common');
        
        // 获取关联地点信息
        $places = $this->db->where("a.".($type?'applyPreferId':'preferId')." = '{$id}' AND a.placeId = p.id", null, false)
                           ->get(($type?'ApplyPreferAtPlace':'PlaceOwnPrefer') . ' a, Place p')->result_array();
        
        $this->assign(compact('prefer', 'places'));
        
        $this->display('view');
    }
}
