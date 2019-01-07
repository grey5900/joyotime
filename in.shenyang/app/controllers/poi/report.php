<?php
/**
 * POI报错处理
 * Create by 2012-3-26
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Code
class Report extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->assign('poi_status', $this->config->item('poi_status'));
        $this->assign('poi_handle', $this->config->item('poi_handle'));
        $this->assign('poi_report', $this->config->item('poi_report'));
    }

    /**
     * 显示报错列表
     * Create by 2012-3-26
     * @author liuw
     */
    public function index() {
        $keywords = trim($this->post('keywords'));
        $where_sql = array();
        // 关键词条件
        if ($keywords) {
            $type = $this->post('type');
            switch($type) {
                case 'id' :
                    $where_sql[] = "p.id = '$keywords'";
                    break;
                case 'placename' :
                    $where_sql[] = "p.placename like '%$keywords%'";
                    break;
                case 'address' :
                    $where_sql[] = "p.address like '%$keywords%'";
                    break;
            }
        }
        // 处理状态
        $status = $this->post('status')===false?'0':$this->post('status');
        if ($status !== false && $status >= 0) {
            $where_sql[] = "p.isConfirm = '{$status}'";
        }
        $where_sql[] = 'p.id = per.placeId';
        
        $where_sql && $where_sql = implode(' and ', $where_sql);
        $row = $this->db->select('count(distinct(p.id)) as num')
                              ->from('Place p, PlaceErrorReport per')
                              ->where($where_sql)
                              ->get()->row_array();
        $total_num = $row['num'];

        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        // 排序字段
        $order_field = $this->post('orderField')?$this->post('orderField'):'reportTime';
        // 排序方式
        $order_direction = $this->post('orderDirection')?$this->post('orderDirection'):'desc';
        // if ($order_field && $order_direction) {
            $order_by = ' order by ' . $order_field . ' ' . $order_direction;
        // } else {
            // $order_by = ' order by per.createDate desc ';
        // }
        $query = $this->db->select('p.*, max(per.createDate) as reportTime, group_concat(DISTINCT per.`type`) as reportType')
                          ->from('Place p, PlaceErrorReport per')
                          ->limit($paginate['per_page_num'], $paginate['offset'])->order_by($order_field, $order_direction)
                          ->where($where_sql)->group_by('p.id')->get();
        $list = $query->result_array();
        $poi_report = $this->config->item('poi_report');
        foreach ($list as &$row) {
            $reportType = explode(',', $row['reportType']);
            $report = array();
            foreach ($reportType as $k) {
                $report[] = $poi_report[$k];
            }
            $row['report'] = implode("\n", $report);
        }
        unset($row);

        $this->assign(compact('list', 'keywords', 'type', 'status', 'order_field', 'order_direction'));

        $this->display('poi_report', 'poi');
    }

    /**
     * 编辑报错
     */
    function edit() {
        // placeId
        $id = $this->get('id');
        $fresh_type = $this->get("fresh_type");
        if ($this->is_post() && $fresh_type!=="get") {
            $placename = trim($this->post('placename'));
            // $place_module = $this->post('place_module');
            $poi_status = $this->post('poi_status');
            $poi_icon = $this->post('poi_icon');
            $poi_icon = $poi_icon?array_filter($poi_icon):array();
            $data = array(
                    'placename' => $placename,
                    'address' => $this->post('address'),
                    'latitude' => floatval($this->post('latitude')),
                    'longitude' => floatval($this->post('longitude')),
                    'tel' => $this->post('tel'),
                    'pcc' => $this->post('is_business')?intval($this->post('pcc')):0,
                    'icon' => $poi_icon[0],
                    'isBusiness' => $this->post('is_business'),
                    // 'placeModule' => $place_module,
                    'status' => $poi_status,
            		'isRepayPoint' => $this->post("isRepayPoint")
            );
            
            // 确认标志
            $status = $this->post('status');
            // 处理勾选确定的
            $ids = $status; // 所有的勾选的ID号
            
            // 选出提交了确认的用户
            $credit_uid = $credit_p = array();
            if($ids) {
                $reports = $this->db->select('id, uid')->get_where('PlaceErrorReport', "id in ('" . implode("','", $ids) . "')")->result_array();
                foreach ($reports as $r) {
                    $uid = intval($r['uid']);
                    if($uid <= 0) {
                        continue;
                    }
                    $credit_uid[] = $uid;
                    $credit_p[] = array('place'=>$placename);
                }
            }
            
            // 发送消息
            $this->load->helper('ugc');
            // 给用户奖励金币
            $credit_uid && make_point($credit_uid, 'poi_report', "0", $id);
            
            if (empty($id)) {
                // 新建
                // 查询地点名称是否重复
                // $query = $this->db->select('count(*) as num')->get_where('Place', array('placename' => $placename));
                // $row = $query->row_array();
                // if ($row['num'] > 0) {
                    // $this->error('POI地点名称重复，请重新填写一个');
                // }

                $query = $this->db->insert('Place', $data);
                $id = $this->db->insert_id();
            } else {
                // 编辑
                $this->db->where('id', $id)->update('Place', $data);
                // 删除之前的分类关系
                $this->db->delete('PlaceOwnCategory', array('placeId' => $id));
                // 删除之前的地点模型数据
                // $this->db->delete('PlaceModuleData', array('placeId' => $id));
            }

            // 添加关系
            $cats = array();
//             $cats[0]['placeId'] = $id;
//             $cats[0]['placeCategoryId'] = $this->post('placeCategory_id');
//             $brand_cat_id = $this->post('placeBrand_id');
//             if ($brand_cat_id) {
//                 $cats[1]['placeId'] = $id;
//                 $cats[1]['placeCategoryId'] = $brand_cat_id;
//             }
            $pcids = explode(',', $this->post('placeCategory_id'));
            foreach($pcids as $pcid) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $pcid
            	);
            }
            $brand_cat_id = $this->post('placeBrand_id');
            if ($brand_cat_id) {
            	$cats[] = array(
            			'placeId' => $id,
            			'placeCategoryId' => $brand_cat_id
            	);
            }
            $this->db->insert_batch('PlaceOwnCategory', $cats);

            // 获取地点模型字段
            // $query = $this->db->order_by('orderValue', 'desc')->get_where('PlaceModuleField', array('moduleId' => $place_module));
            // $fields = $query->result_array();
            // 添加模型数据
            // $is_visible = $this->post('is_visible');
            // $module_data = array();
            // foreach ($fields as $row) {
                // $value = $this->post($row['fieldId']);
                // $module_data[] = array(
                        // 'fieldId' => $row['fieldId'],
                        // 'placeId' => $id,
                        // 'mValue' => is_array($value) ? implode(',', $value) : $value,
                        // 'isVisible' => intval($is_visible[$row['fieldId']])
                // );
            // }
            // if ($module_data) {
                // $this->db->insert_batch('PlaceModuleData', $module_data);
            // }

            // 提交了。处理报错信息
            // 处理标志
            $is_confirm = $this->post('is_confirm');
            

            if ($is_confirm) {
                // 确认了。那么所有未确认的标志为已删除
                $this->db->where(array('id' => $id))->update('Place', array('isConfirm' => 1));
            }
            // 处理所有的
            $all_ids = explode('_', $this->post('all_ids'));
            
            // $ids = array();
            // if ($status) {
                // 点击了确认的
                // 用于分别保存不同报错类型的报错ID号的数组
                // $r_ids = array(); // 2012.07.17这个地方改了，就不用了
                // foreach ($status as $k) {
                    // 处理给用户积分
                    // $i_ids = explode('_', $k);
                    // $r_ids[] = $i_ids;
                    // $ids = array_merge($ids, $i_ids);
                // }
            // }
            // 选出所有的用户
            // $reports = $this->db->select('id, uid')->get_where('PlaceErrorReport', "id in ('" . implode("','", $all_ids) . "')")->result_array();
            // $id_uid = array();
            // foreach ($reports as $r) {
                // $id_uid[$r['id']] = $r['uid'];
            // }
            // $credit_uid = $none_uid = $credit_p = $none_p = $_credit_uid = $_credit_p = array();
            // foreach ($id_uid as $_id => $uid) {
                // if(intval($uid)<=0) continue;
                // if (in_array($_id, $ids)) {
                    // // 在确认的里面，需要送积分
                    // // 分别去不同的报错类型数组中判断
                    // foreach($r_ids as $i=>$r) {
                        // empty($_credit_uid[$i]) && $_credit_uid[$i] = array();
                        // if(!in_array($uid, $_credit_uid[$i])) {
                            // $_credit_uid[$i][] = $uid;
                            // $_credit_p[$i][] = array('place'=>$placename);
                        // }
                    // }
                    // // if(!in_array($uid, $credit_uid)) {
                        // // $credit_uid[] = $uid;
                        // // $credit_p[] = array('place'=>$placename);
                    // // }
                // } else {
                    // if(!in_array($uid, $none_uid)) {
                        // // 不在确认的里面
                        // $none_uid[] = $uid;
                        // $none_p[] = array('place'=>$placename);
                    // }
                // }
            // }
            // // 组合
            // foreach($_credit_uid as $_r) {
                // $credit_uid = array_merge($credit_uid, $_r);
            // }
            // foreach($_credit_p as $_r) {
                // $credit_p = array_merge($credit_p, $_r);
            // }
            
            // 2012.07.17修改为新的报错处理方法。所有的报错都列出来
            
            // $none_uid && send_message('sm_poi_report', $none_uid, $id, 1, true, $none_p);
            $credit_uid && send_message('sm_poi_report_confirm', $credit_uid, $poi_status==2?0:$id, $poi_status==2?0:1, true, $credit_p);

            // 更新记录
            if ($is_confirm) {
                // 更新所有状态为删除
                $all_ids && $this->db->where("id in ('" . implode("','", $all_ids) . "')")->update('PlaceErrorReport', array('status' => 2));
            } else {
                // 只更新提交了的
                $ids && $this->db->where("id in ('" . implode("','", $ids) . "')")->update('PlaceErrorReport', array('status' => 1));
            }

            // 更新地点缓存
            api_update_cache('Place', $id);
            // 更新分类关系
            api_update_cache('PlaceCategoryShip');
            
            send_api_interface('/private_api/place/update_solr', 'POST', array('place_id' => $id));
            
            $this->success('处理报错POI成功', $this->_index_rel, $this->_index_uri, 'closeCurrent');
        }

        // 编辑的时候，取出place信息
        if ($id) {
            $query = $this->db->get_where('Place', array('id' => $id));
            $place = $query->row_array();
            $this->assign('poi', $place);

            // 选出地点分类
            $query = $this->db->select('id, content, isBrand')->from('PlaceOwnCategory poc, PlaceCategory pc')->where("poc.placeId = '{$id}' and poc.placeCategoryId = pc.id")->get();
            $category = $query->result_array();
            $cate = array();
            foreach ($category as $row) {
                $isBrand = $row['isBrand'];
                unset($row['isBrand']);
                if ($isBrand) {
                    // 品牌分类
                    $cate['brand'] = $row;
                } else {
                    // 普通分类
                    $cate['common'] = $row;
                }
            }
            $this->assign('category', $cate);
        }

        // 获取模型数据
        // $query = $this->db->get('PlaceModule');
        // $modules = $query->result_array();
        // $this->assign('modules', $modules);

        // 获取报错信息
        // if (empty($place['isConfirm'])) {
            // $query = $this->db->select("per.*, group_concat(per.id separator '_') as ids, group_concat(distinct u.username) as username, group_concat(distinct u.id) as uid")->from('PlaceErrorReport per')->join('User u', 'per.uid = u.id', 'left')->group_by("per.`type`")->where("per.placeId = '{$id}' and per.status = 0")->get();
            // $reports = $query->result_array();
            // $all_ids = array();
            // foreach ($reports as &$r) {
                // $r['usernames'] = explode(',', $r['username']);
                // $r['uids'] = explode(',', $r['uid']);
                // $all_ids[] = $r['ids'];
            // }
            // unset($r);
            // $this->assign('all_ids', implode('_', $all_ids));
            // $this->assign('reports', $reports);
        // }
        if (empty($place['isConfirm'])) {
            $reports = $this->db->select('per.*, u.username, u.nickname, u.cellphoneNo')->from('PlaceErrorReport per')
                     ->join('User u', 'per.uid = u.id', 'left')
                     ->where("per.status = 0 and per.placeId = '{$id}'")
                     ->get()->result_array();
            $all_ids = array();
            foreach ($reports as &$r) {
                // $r['usernames'] = explode(',', $r['username']);
                // $r['uids'] = explode(',', $r['uid']);
                $all_ids[] = $r['id'];
            }
            unset($r);
            $this->assign('all_ids', implode('_', $all_ids));
            $this->assign('reports', $reports);
        }

        $this->display('poi_report_edit', 'poi');
    }

    /**
     * 报告历史记录
     */
    function report_history() {
        // 地点ID号
        $id = $this->get('id');

        // $list_data = $this->db->select('per.`type`, per.`status`, group_concat(distinct u.username) as username')->from('PlaceErrorReport per')->join('User u', 'per.uid = u.id', 'left')->where("per.placeId = '{$id}' and per.`status` in (1, 2)")->group_by('per.`type`, per.`status`')->get()->result_array();
        $list_data = $this->db->select('per.*, u.username, u.nickname, u.cellphoneNo')
                              ->from('PlaceErrorReport per')
                              ->join('User u', 'per.uid = u.id', 'left')
                              ->where("per.placeId = '{$id}' and per.`status` in (1, 2)")->get()->result_array();
        
        // $list = array();
        // foreach ($list_data as $row) {
            // $list[$row['type']][$row['status']] = $row['username'];
        // }

        $this->assign('list', $list_data);

        $this->display('poi_report_history', 'poi');
    }
    
    /**
     * 报错详情
     */
    function detail() {
        // 报错信息ID
        $id = $this->get('id');
        
        $report = $this->db->get_where('PlaceErrorReport', array('id'=>$id))->row_array();
        
        if(empty($report)) {
            $this->error('错误');
        }
        
        // 获取地点信息
        $place = $this->db->get_where('Place', array('id'=>$report['placeId']))->row_array();

        if(empty($place)) {
            $this->error('地点错误');
        }
        
        $this->assign(compact('place', 'report'));
        
        $this->display('poi_report_detail', 'poi');
    }
}

// File end
