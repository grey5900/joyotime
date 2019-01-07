<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 地点模型
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-10-19
 */

class Poi_Block extends MY_Controller {
    var $lan;
    function __construct() {
        parent::__construct();
        
        $this->lang->load('brand');
        $this->lan = $this->lang->line('block');
    }
    
    /**
     * 地点的碎片数据管理列表
     */
    function index() {
        
        
        $this->display('index');
    }
    
    /**
     * 待审批
     */
    function apply() {
        // 搜索关键词
        $keywords = trim($this->post('keywords'));
        // 搜索条件
        $type = trim($this->post('type'));

        $where_sql = 'a.status = 0 AND a.brandId = b.id';
        if($keywords !== '') {
            $keytxt = daddslashes($keywords);
            $where_sql .= " AND " . ($type?'a.title':'b.name') . " like '%{$keytxt}%'";
        }
        
        $total_num = $this->db->where($where_sql)
                              ->from('ApplyPlaceSpecialProperty a, Brand b')
                              ->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select("a.*, b.name as brand", false)
                         ->order_by('createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ApplyPlaceSpecialProperty a, Brand b')
                         ->get()->result_array();

        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('apply');
    }
    
    /**
     * 已审核
     */
    function checked() {
        if($this->is_post()) {
            // 搜索关键词
            $keywords = trim($this->post('keywords'));
            // 搜索条件
            $type = trim($this->post('type'));
    
            $where_sql = 'a.status = 20 AND a.brandId = b.id';
            if($keywords !== '') {
                $keytxt = daddslashes($keywords);
                $where_sql .= " AND " . ($type?'a.title':'b.name') . " like '%{$keytxt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyPlaceSpecialProperty a, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("a.*, b.name as brand", false)
                             ->order_by('checkDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyPlaceSpecialProperty a, Brand b')
                             ->get()->result_array();
    
            $this->assign(compact('type', 'keywords', 'list'));

            $this->assign(compact('title', 'list'));
        }
        
        $this->display('checked');
    }
    
    /**
     * 驳回
     */
    function reject() {
        if($this->is_post()) {
            // 搜索关键词
            $keywords = trim($this->post('keywords'));
            // 搜索条件
            $type = trim($this->post('type'));
    
            $where_sql = 'a.status = 10 AND a.brandId = b.id';
            if($keywords !== '') {
                $keytxt = daddslashes($keywords);
                $where_sql .= " AND " . ($type?'a.title':'b.name') . " like '%{$keytxt}%'";
            }
            
            $total_num = $this->db->where($where_sql)
                                  ->from('ApplyPlaceSpecialProperty a, Brand b')
                                  ->count_all_results();
            $paginate = $this->paginate($total_num);
            
            $list = $this->db->select("a.*, b.name as brand", false)
                             ->order_by('checkDate', 'desc')
                             ->limit($paginate['per_page_num'], $paginate['offset'])
                             ->where($where_sql)
                             ->from('ApplyPlaceSpecialProperty a, Brand b')
                             ->get()->result_array();
    
            $this->assign(compact('type', 'keywords', 'list'));

            $this->assign(compact('title', 'list'));
        }
        
        $this->display('reject');
    }
    
    /**
     * 编辑页面
     */
    function edit() {
        // ID号
        $id = intval($this->get('id'));
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取碎片信息
        $property = $this->db->get_where('ApplyPlaceSpecialProperty', array('id' => $id))->row_array();
        if(empty($property)) {
            $this->error('错误的碎片数据');
        }
        
        if($property['status'] != 0) {
            // 碎片不在待审核状态
            $this->error('碎片' . ($property['status']==10?'已驳回':'已通过'));
        }
        
        $page_id = 'poi_block_apply_edit';
        
        if($this->is_post()) {
            $status = $this->post('status');
            if($status == 10) {
                // 驳回
                // 直接处理信息返回给用户
                $remark = trim($this->post('remark'));
                if($remark === '') {
                    $this->error('请输入驳回备注');
                }
                
                $b = $this->db->update('ApplyPlaceSpecialProperty', array('status' => 10, 'remark' => $remark, 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                
                // 给品牌商家发消息
                $this->db->insert('BrandMessage', array('brandId' => $property['brandId'], 'title' => sprintf($this->lan['reject']['title'], $property['title']), 'content' => $remark));
                
                $b?$this->success('驳回碎片成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('驳回碎片失败');
            } elseif(20 == $status) {
                // 通过
                // 写入正式碎片
                
                // 获取地点的扩展属性数据
                $post = $this->post(null);
                // 排序值
                // $orders = $post['order'][$page_id];
                // 模型ID号
                $module_ids = $post['module_id'][$page_id];
                // 标题
                $titles = $post['title'][$page_id];
                // 内容
                $contents = $post['content'][$page_id];
                // 图片
                $images = $post['image'][$page_id];
                // // 样式
                // $styles = $post['style'][$page_id];
                
                // 循环处理所有的扩展碎片数据
                $module_data = $new_propertis = $update_properties = $update_ids = array();
                
                // 检查数据
                $title = $titles[0];
                $content = $contents[0];
                $image = $images[0];
                if(empty($title) && empty($content) && empty($image)) {
                    // 如果都是空的话。跳过
                    $this->error('请至少输入标题、内容、图片中的一个，亲');
                }
                $module_id = $module_ids[0];
                
                // 根据样式类型保存图片
                $style = $property['style'];
                $img = array_filter(array_values($image));
                $imgs = array();
                if($style > 1) {
                    // 4张图
                    $imgs = $img;
                } else {
                    // 1张图
                    $img[0] && $imgs[] = $img[0];
                }
                
                $_property = array(
                    'title' => $title,
                    'content' => $content,
                    'rankOrder' => $property['rankOrder'],
                    'moduleId' => $module_id,
                    'images' => implode(',', $imgs),
                    'style' => $style,
                    'applyId' => $id
                );
                
                if($module_id > 0) {
                    // 获取模型
                    $fields = $this->db->get_where('PlaceModuleField', array('moduleId' => $module_id))->result_array();
                    foreach($fields as $field) {
                        // 去获取每个字段的信息
                        $value = $post['data'][$page_id][0][$field['fieldId']];
                        $value_depth = array_depth($value);
                        if($value_depth == 1) {
                            $value = implode(',', $value);
                        } elseif($value_depth > 1) {
                            $value = json_encode($value);
                            if($field['fieldType'] == 'rich_image') {
                                $value = json_encode(json2json($value));
                            }
                        }
                        empty($value) && ($value = '');
                        
                        $is_visible = $post['visiable'][$page_id][0][$field['fieldId']];
                        $module_data[] = array(
                                'fieldId' => $field['fieldId'],
                                'moduleId' => $module_id,
                                'mValue' => $value,
                                'isVisible' => intval($is_visible)
                        );
                    }
                    $hyper_link = base_url("main/place/%s/{$module_id}");
                } elseif($module_id < 0) {
                    // 获取连接
                    $hyper_link = $post['hyper_link'][$page_id][0];
                }
                
                $_property['hyperLink'] = $hyper_link?$hyper_link:null;
                
                $b = 1;
                // 更新的属性添加数据 old用于更新的 new用于添加的
                $old_property = $new_property = $data = array();
                
                // 查询出所有需要提交的地点
                $places = $this->db->get_where('ApplyPropertyAtPlace', array('applyPropertyId' => $id))->result_array();
                // 记录需要更新的地点的数组
                $pids = array();
                foreach($places as $row) {
                    $pids[$row['placeId']] = 1;
                    $_property['placeId'] = $row['placeId'];
                    $_property['moduleId'] > 0 && ($_property['hyperLink'] = sprintf($_property['hyperLink'], $row['placeId']));
                    // 先把所有数据当做新加入数据 不能有重复的数据
                    $new_property[$row['placeId']] = $_property;
                    
                    foreach($module_data as $r) {
                        $r['placeId'] = $row['placeId'];
                        $data[] = $r;
                    }
                }
                unset($module_data);
                
                // 选出所有地点的扩展信息
                if($_property['moduleId'] && $pids) {
                    // 如果存在模型ID，那么去判断是否地点已经添加过该模型了
                    $properties = $this->db->where_in('placeId', array_keys($pids))
                                                    ->get('PlaceOwnSpecialProperty')->result_array();
                    if($properties) {
                        foreach($properties as $row) {
                            if($row['moduleId'] == $_property['moduleId']) {
                                // 相同的模型，那么只是更新数据
                                $r = $new_property[$row['placeId']];
                                $r['id'] = $row['id'];
                                $old_property[$row['placeId']] = $r;
                                unset($new_property[$row['placeId']]);
                            }
                        }
                    }
                }
                
                // 再去获取如果applyId相同的
                $properties = $this->db->where('applyId', $id)
                                       ->get('PlaceOwnSpecialProperty')->result_array();
                foreach($properties as $row) {
                    if($new_property[$row['placeId']]) {
                        // 如果新加入的里面还有需要更新的
                        $r = $new_property[$row['placeId']];
                        $r['id'] = $row['id'];
                        $old_property[$row['placeId']] = $r;
                        unset($new_property[$row['placeId']]);
                    }
                }
                
                // 提交更新碎片数据
                $old_property && $this->db->update_batch('PlaceOwnSpecialProperty', array_values($old_property), 'id');
                // 添加新的碎片数据
                $new_property && ($b &= $this->db->insert_batch('PlaceOwnSpecialProperty', array_values($new_property)));            
                // 更新所有的模型数据
                // 先删除
                $pids && ($b &= $this->db->where_in('placeId', array_keys($pids))
                                ->where('moduleId', $module_id)->delete('PlaceModuleData'));
                $data && ($b &= $this->db->insert_batch('PlaceModuleData', $data));
                
                // 更新审核
                $b &= $this->db->update('ApplyPlaceSpecialProperty', array('status' => 20, 'remark' => $remark, 'checkId' => $this->auth['id'], 'checkDate' => now()), array('id' => $id));
                // 给商家用户发信息
                $this->db->insert('BrandMessage', array('brandId' => $property['brandId'], 'title' => sprintf($this->lan['pass']['title'], $_property['title']), 'content' => ''));
            
                $b?$this->success('通过碎片成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('通过碎片失败');
            }
        }
        
        $this->assign(compact('page_id', 'property'));
        
        $this->display('edit');
    }
    
    /**
     * 浏览碎片
     * 
     */
    function view() {
       // 碎片ID号
        $id = intval($this->get('id'));
        // 选出碎片
        if($id <= 0) {
            $this->error('错误');
        }
        
        // 获取碎片信息
        $property = $this->db->get_where('ApplyPlaceSpecialProperty', array('id' => $id))->row_array();
        if(empty($property)) {
            $this->error('错误的碎片数据');
        }

        $page_id = 'poi_block_view';
        $this->assign(compact('page_id', 'property'));
        
        $this->display('view');
    }


    /**
     * 地点的碎片数据添加
     */
    function block() {
        // 申请的ID号
        $id = $this->get('id');
        $page_id = $this->get('page_id');
        $module_id = $this->get('module_id');
        $block_id = $this->get('block_id');
        
        // 获取所有的可以用的模型
        $module_list = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceModule')->result_array();
        
        // 获取碎片数据
        $property = $this->db->get_where('ApplyPlaceSpecialProperty', array('id' => $id))->row_array();
        $property['images'] = explode(',', $property['images']);
        
        $this->assign(compact('id', 'page_id', 'module_id', 'block_id', 'module_list', 'property'));
        
        $this->display('poi_block');
    }
    
    /**
     * 地点碎片模型数据
     */
    function block_module() {
        $page_id = $this->get('page_id');
        $id = $this->get('id');
        $module_id = intval($this->get('module_id'));
        $block_id = $this->get('block_id');
        
        if($module_id > 0) {
            // 获取模型
            $module = $this->db->get_where('PlaceModule', array('id' => $module_id))
                               ->row_array();
            if(empty($module)) {
                $this->error('错误');
            }
            
            // 选出模型的字段数据
            $query = $this->db->order_by('orderValue', 'asc')->get_where('PlaceModuleField', array('moduleId' => $module_id));
            $fields = $query->result_array();
       
            // 如果传递了POI的ID号，那么去获取POI的模型数据
            $query = $this->db->get_where('ApplyPlaceModuleData', array('applyPropertyId' => $id, 'moduleId' => $module_id));
            $module_data = $query->result_array();
            $data = array();
            foreach ($module_data as $row) {
                $data[$row['fieldId']]['value'] = $row['mValue'];
                $data[$row['fieldId']]['isVisible'] = $row['isVisible'];
            }
            $this->assign('data', $data);
        } else {
            $module = $fields = array();
            // 根据id
            if($id > 0) {
                $place_property = $this->db->get_where('ApplyPlaceSpecialProperty', array('id' => $id))->row_array();
                $this->assign('place_property', $place_property);
            }
        }
        
        $rich_image_fields = $this->config->item('rich_image');
        array_shift($rich_image_fields);
        $rich_image_fields = json_encode($rich_image_fields);
        $this->assign(compact('page_id', 'id', 'module_id', 'block_id', 'module', 'fields', 'rich_image_fields'));
        
        $this->display('poi_block_module');
    }
}
