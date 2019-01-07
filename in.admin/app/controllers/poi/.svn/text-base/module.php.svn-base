<?php
/**
 * poi模型管理
 * Create by 2012-3-19
 * @author liuw
 * @copyright Copyright(c) 2012-2014
 */

// Define and include
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Code
class Module extends MY_Controller {

    /**
     * poi模型列表
     * Create by 2012-3-19
     * @author liuw
     */
    public function index() {
        // 获取关键词
        $keywords = trim($this->post('keywords'));

        if ($keywords) {
            $where_sql = "name like '%{$keywords}%'";
        }
        $query = $this->db->select('count(*) as num')->get_where('PlaceModule', $where_sql);
        $row = $query->row_array();
        $total_num = $row['num'];

        // 返回获得每页显示，当前页等等参数
        $paginate = $this->paginate($total_num);

        $query = $this->db->order_by('rankOrder', 'asc')->get_where('PlaceModule', $where_sql, $paginate['per_page_num'], $paginate['offset']);
        $list = $query->result_array();
        //查询使用数
        foreach($list as &$row){
        	$c = $this->db->select('count(*) as num')->where('moduleId',$row['id'])->get('PlaceOwnSpecialProperty')->row_array(0);
        	$row['placeNum'] = $c['num'];
        	unset($c);
        }

        $this->assign(compact('list', 'keywords'));
        $this->display('index');
    }

    /**
     * 创建poi模型
     * Create by 2012-3-19
     * @author liuw
     */
    public function add() {
        $id = $this->get('id');
        if ('POST' == $this->server('REQUEST_METHOD')) {
            $name = trim($this->post('name'));
            // 去判断是否名称重复
            if ($id) {
                // ID号不是提交的这个，但是名称一样的
                $where = "id <> '$id' and name = '$name'";
            } else {
                // 名称是否存在
                $where = "name = '$name'";
            }
            $query = $this->db->select('count(*) as num')->get_where('PlaceModule', $where);
            $row = $query->row_array();
            if ($row['num'] > 0) {
                $this->error('模型名称已经存在，请另输入一个');
            }
            $data = array(
                    'name' => $name,
                    'description' => $this->post('description'),
                    'css' => $this->post('css'),
                    'template' => $this->post('template'),
                    'icon' => $this->post('icon'),
                    'rankOrder' => intval($this->post('rank_order'))
            );

            if ($id) {
                // 更新
                if ($this->db->where('id', $id)->update('PlaceModule', $data)) {
                    $this->_update_module($id);
                    $this->success('修改模型成功', $this->_index_rel, $this->_index_uri, 'closeCurrent');
                } else {
                    $this->error('修改模型失败，请重试');
                }
            } else {
                // 新增
                if ($this->db->insert('PlaceModule', $data)) {
                    $this->success('添加模型成功', $this->_index_rel, $this->_index_uri, 'closeCurrent');
                } else {
                    $this->error('添加模型失败，请重试');
                }
            }
        }

        // 读取模板
        $query = $this->db->where('id', $id)->get('PlaceModule');
        $module = $query->row_array();

        // 读取模板数据
        $temps = array();
        $d = dir($this->config->item('mod_temp_path'));
        while (false !== ($line = $d->read())) {
            if (false !== strrpos($line, '.html')) {
                $temps[] = substr($line, 0, -5);
            }
        }
        $d->close();
        $this->assign(compact('temps', 'module'));
        $this->display('add');
    }

    /**
     * 编辑poi模型
     * Create by 2012-3-19
     * @author liuw
     */
    public function edit() {
        $this->add();
    }

    /**
     * 删除poi模型
     * Create by 2012-3-19
     * @author liuw
     */
    public function delete() {
        $id = $this->get('id');
        $query = $this->db->where('id', $id)->get('PlaceModule');
        $module = $query->row_array();

        if ($module && $module['placeNum'] > 0) {
            $this->error('您要删除的模型已关联地点，请先删除地点关联');
        }

        if ($this->db->delete('PlaceModule', array('id' => $id)) && $this->db->delete('PlaceModuleField', array('moduleId' => $id))) {
            $this->success('删除模型成功');
        } else {
            $this->error('删除模型失败，请重试');
        }
    }

    /**
     * 预览
     */
    function view() {
        $id = $this->get('id');
        $module = $this->db->get_where('PlaceModule', array('id' => $id))->row_array();
        $uri = build_uri(array(
                'static',
                'template',
                'place',
                'module_' . $id . '.html'
        ));

        execjs("navTab.closeCurrentTab();window.open('{$uri}');");
    }

    /**
     * 编辑模型字段
     */
    function edit_fields() {
        $id = $this->get('id');
        $query = $this->db->where('id', $id)->get('PlaceModule');
        $module = $query->row_array();
        $this->assign('module', $module);

        // 选择出所有的字段
        $query = $this->db->where('moduleId', $id)->order_by('orderValue', 'asc')->get('PlaceModuleField');
        $list = $query->result_array();
        $this->assign('list', $list);

        $this->display('field');
    }

    /**
     * 添加字段
     */
    function add_field() {
        $id = $this->get('id');
        // 分解$id为fieldId和moduleId号
        list($field_id, $module_id) = explode('_', $id);
        if ($this->is_post()) {
            // 提交数据处理
            // 为字段的ID添加一个前缀
            $new_field_id = $this->config->item('field_prefix') . $this->post('id');
            if (empty($field_id)) {
                // 判断field_id是否重复
                $query = $this->db->select('count(*) as num')->get_where('PlaceModuleField', array(
                        'fieldId' => $new_field_id,
                        'moduleId' => $module_id
                ));
                $row = $query->row_array();
                if ($row['num'] > 0) {
                    // 存在重复
                    $this->error('字段ID重复，请重新填写一个');
                }
            }
            $data = array(
                    'fieldName' => $this->post('name'),
                    'fieldType' => $this->post('type'),
                    'fieldSize' => $this->post('size'),
                    'defaultValue' => $this->post('defaultValue'),
                    'defaultSelect' => $this->post('defaultSelect'),
                    'orderValue' => intval($this->post('orderValue'))
            );

            $rel_uri = build_rel(array(
                    'poi',
                    'module',
                    'edit_fields'
            ));
            $uri = build_uri(array(
                    'poi',
                    'module',
                    'edit_fields',
                    'id',
                    $module_id
            ));
            if (empty($field_id)) {
                // 添加
                $data['fieldId'] = $new_field_id;
                $data['moduleId'] = $module_id;
                if ($this->db->insert('PlaceModuleField', $data)) {
                    $this->_update_module($module_id);
                    $this->success('添加模型字段成功', $rel_uri, $uri, 'closeCurrent');
                } else {
                    $this->error('添加模型字段失败，请重试');
                }
            } else {
                // 更新
                if ($this->db->where(array(
                        'fieldId' => $field_id,
                        'moduleId' => $module_id
                ))->update('PlaceModuleField', $data)) {
                    $this->_update_module($module_id);
                    $this->success('修改模型字段成功', $rel_uri, $uri, 'closeCurrent');
                } else {
                    $this->error('修改模型字段失败，请重试');
                }
            }
        }
        if ($field_id) {
            $query = $this->db->get_where('PlaceModuleField', array(
                    'fieldId' => $field_id,
                    'moduleId' => $module_id
            ));
            $this->assign('f', $query->row_array());
        }
        $this->assign('field_id', $field_id);
        $this->display('add_field');
    }

    /**
     * 删除字段
     */
    function delete_field() {
        $id = $this->get('id');
        // 分解$id为fieldId和moduleId号
        list($field_id, $module_id) = explode('_', $id);
        $rel_uri = build_rel(array(
                'poi',
                'module',
                'edit_fields'
        ));
        $uri = build_uri(array(
                'poi',
                'module',
                'edit_fields',
                'id',
                $module_id
        ));
        if ($this->db->delete('PlaceModuleField', array(
                'fieldId' => $field_id,
                'moduleId' => $module_id
        ))) {
            $this->_update_module($module_id);
            $this->success('删除模型字段成功', $rel_uri, $uri);
        } else {
            $this->error('删除模型字段失败，请重试');
        }
    }

    /**
     * 编辑字段
     */
    function edit_field() {
        $this->add_field();
    }

    /**
     * 更新模型
     * 用于更新模型生成的缓存模板文件，便于页面实现的时候生成内容
     */
    function _update_module($id) {
        $this->load->helper('poi');
        
        update_module_template($id);
    }

    /**
     * 调用添加模型数据页面
     */
    function add_module_data() {
        $id = $this->get('id');
        
        // 获取模型
        $module = $this->db->get_where('PlaceModule', array('id' => $id))
                           ->row_array();
        if(empty($module)) {
            $this->error('错误');
        }
        
        // 选出模型的字段数据
        $query = $this->db->order_by('orderValue', 'asc')->get_where('PlaceModuleField', array('moduleId' => $id));
        $fields = $query->result_array();
        $this->assign('fields', $fields);

        //
        $place_id = $this->get('place_id');
        if ($place_id) {
            // 获取模型的排序
            $row = $this->db->get_where('PlaceOwnModule', array('placeModuleId' => $id, 'placeId' => $place_id))
                           ->row_array();
            $module['rankOrder'] = $row['rankOrder'];              
            // 如果传递了POI的ID号，那么去获取POI的模型数据
            $query = $this->db->get_where('PlaceModuleData', array('placeId' => $place_id, 'moduleId' => $id));
            $module_data = $query->result_array();
            $data = array();
            foreach ($module_data as $row) {
                $data[$row['fieldId']]['value'] = $row['mValue'];
                $data[$row['fieldId']]['isVisible'] = $row['isVisible'];
            }
            $this->assign('data', $data);
        }
        if(empty($module['rankOrder'])) {
            // 没有获取到排序值，导入默认的模型排序值
            $row = $this->db->get_where('PlaceModule', array('id' => $id))->row_array();
            $module['rankOrder'] = $row['rankOrder'];  
        }
        $this->assign('module', $module);
        
        $this->assign('id', $id);

        $this->display('add_module_data');
    }

    /**
     * 指定品牌
     */
    function relation_brand() {
        $id = intval($this->get('id'));
        // 获取模型信息 
        $module = $this->db->get_where('PlaceModule', array('id' => $id))->row_array();
        if(empty($module)) {
            $this->error('错误的模型');
        }
        
        $do = $this->post('do');
        if('submit' == $do && $this->is_post()) {
            // 提交了
            $ids = $this->post('ids');
            empty($ids) && $ids = array();
            // 清空之前的，
            $b = $this->db->delete('BrandOwnModule', array('moduleId' => $id));
            $ids = array_filter($ids);
            $data = array();
            foreach($ids as $i) {
                $data[] = array('brandId' => $i, 'moduleId' => $id);
            }
            
            $data && ($b &= $this->db->insert_batch('BrandOwnModule', $data));
            $b?$this->success('关联品牌成功', '', '', 'closeCurrent'):$this->error('关联出错啦');
        }
        
        $keywords = trim($this->post('keywords'));
        $where_sql = 'status = 0';
        if($keywords !== '') {
            $keytext = daddslashes($keywords);
            $where_sql .= " AND name like '%{$keytext}%'";
            $this->assign('keywords', $keywords);
        }
        
        $list = $this->db->order_by('id', 'asc')
                         ->where($where_sql)
                         ->from('Brand')
                         ->get()->result_array();
        $this->assign('list', $list);
        
        // 获取之前已经关联的品牌
        $mbrand = $this->db->get_where('BrandOwnModule', array('moduleId' => $id))->result_array();
        $module_brand = array();
        foreach($mbrand as $row) {
            $module_brand[$row['brandId']] = 1;
        }
        $this->assign('module_brand', $module_brand);
        
        $this->display('relation_brand');
    }

}

// File end
