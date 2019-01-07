<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 推荐管理
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-9
 */

class Recommend extends MY_Controller {
    /**
     * 推荐碎片管理
     */
    function fragment() {
        $fragment = get_data('morris_fragment');

        $nodes = array();
        foreach ($fragment as $key => $row) {
            $nodes[] = array(
                    'id' => $key,
                    'pId' => $row['parentId'],
                    'name' => $row['name'],
                    'open' => empty($row['parentId'])
            );
        }
        $this->assign('nodes', $nodes);

        $this->display('fragment');
    }

    /**
     * 推荐数据管理
     */
    function data() {
        $fragment = get_data('morris_fragment');
        $tree = array();
        foreach($fragment as $key=>$value) {
            $row = array();
            $row['name'] = $value['name'];
            $row['id'] = $key;
            if($value['parentId'] == '0') {
                $tree[$key]['self'] = $row;
            } else {
                $tree[$value['parentId']]['children'][$key] = $row;
            }
        }
        $this->assign('tree', $tree);       
        $this->display('data');
    }
    
    /**
     * 数据提交页面
     */
    function data_page() {
        // 碎片ID号
        $id = $this->get('id');
        // 读取碎片信息
        $fragments = get_data('morris_fragment');
        $fragment = $fragments[$id];
        $fragment['fields'] = unserialize($fragment['linkId']?$fragments[$fragment['linkId']]['fields']:$fragment['fields']);
        
        // 数据源
        if($fragment['dataSource']) {
            // $this->assign('ds_html', call_user_func(array($this, 'ds_search_'.$fragment['dataSource'])));
            $this->assign('ds_html', call_user_func(array($this, 'ds'), $fragment['dataSource'], 'list'));
        }
        
        $this->assign('fragments', $fragments);
        $this->assign('fragment', $fragment);
        $this->assign('t', now());
                
        $this->display('data_page');
    }

    /**
     * 修改
     */
    function edit_fragment() {
        $this->add_fragment();
    }

    /**
     * 删除
     */
    function del_fragment() {
        $id = $this->get('id');
        // 服务器端判断是否还有子项目
        $row = $this->db->select('count(*) as num')->where('parentId', $id)->get('MorrisRecommendFragment')->row_array();
        if ($row['num'] > 0) {
            $this->error('请先删除下面的碎片才能删除碎片');
        }

        // 删除碎片及数据
        $b = $this->db->delete('MorrisRecommendFragment', array('id' => $id));
        if ($b) {
            $this->_flush_fragment_cache();
            // 删除数据
            $this->db->delete('RecommendData', array('fid' => $id));
            $this->success('删除碎片成功', build_rel(array(
                    'recommend',
                    'fragment'
            )), site_url(array(
                    'recommend',
                    'fragment'
            )));
        } else {
            $this->error('删除碎片失败，请重试');
        }
    }

    /**
     * 创建碎片
     */
    function add_fragment() {
        // 编辑的时候传入id号
        $id = $this->get('id');
        $parent_id = $this->get('parent_id')?$this->get('parent_id'):0;
        $conf_field = $this->config->item('fragment_field');
        $conf_default_field = $this->config->item('fragment_default_field');

        // 读取碎片信息
        $fragment = $this->db->where('id', $id)->get('MorrisRecommendFragment')->row_array();
        if ($this->is_post()) {
            // 提交数据重新获取父类ID号
            $parent_id = $this->post('parent_id')?$this->post('parent_id'):0;
            // 提交数据
            $orderNo = $this->post('order_no');
            if ($orderNo) {
                $orderNo = intval($orderNo);
            } else {
                // 查询最大值+10
                $row = $this->db->select('max(orderNo) as num')->where('parentId', $parent_id)->get('MorrisRecommendFragment')->row_array();
                $orderNo = intval($row['num']) + 10;
            }
            $data = array(
                    'name' => $this->post('name'),
                    'description' => $this->post('description'),
                    'updateObject' => $this->post('update_object'),
                    'manager' => $this->post('account_id'),
                    'orderNo' => $orderNo,
                    'dateline' => now(-1)
            );
            
            if($parent_id) {
                $data['parentId'] = $parent_id;
                // 添加碎片
                $fragment_id = $this->post('fragment_id');
                if(empty($fragment_id)) {
                    // 没有关联了一个其他的碎片数据，那么需要字段名和数据来源
                    // 数据来源
                    $data['dataSource'] = $this->post('data_source');
                    // 字段
                    $fields = $this->post('field');
                    $field_arr = array();
                    if($fields) {
                        foreach($fields as $field) {
                            // 获取字段别名
                            $field_arr[$field] = $this->post($field);
                        }
                    }
                    
                    if($data['dataSource']) {
                        // 检查是否有默认提交的没有选中的
                        foreach($conf_default_field[$data['dataSource']] as $f => $v) {
                            if(empty($field_arr['f_'.$f])) {
                                // 没有这个字段那么默认加进去
                                $field_arr['f_'.$f] = $v;
                            }
                        }
                    }
                    
                    $data['fields'] = serialize($field_arr);
                    $data['linkId'] = '0';
                } else {
                    $data['linkId'] = $fragment_id;
                    $data['fields'] = '';
                    $data['dataSource'] = '';
                }
                
                // 自动更新
                $data['autoUpdate'] = intval($this->post('auto_update'));
                // 跳转
                $data['rewrite'] = intval($this->post('rewrite'));
            }

            $b = $fragment ? $this->db->where('id', $fragment['id'])->update('MorrisRecommendFragment', $data) : $this->db->insert('MorrisRecommendFragment', $data);
            if ($b) {
                $this->_flush_fragment_cache();
                $this->success('添加碎片成功', build_rel(array(
                        'recommend',
                        'fragment'
                )), site_url(array(
                        'recommend',
                        'fragment'
                )), 'closeCurrent');
            } else {
                $this->success('添加碎片失败');
            }
        }
        $this->assign('all_fragment', get_data('morris_fragment'));
        $parent_id && $fragment['fields'] = unserialize($fragment['fields']);
        $this->assign('fragment', $fragment);
        $this->assign('parent_id', $parent_id);
        $this->assign('field', $conf_field);
        $this->assign('default_field', $conf_default_field);
        $this->assign('fragment_category', $this->config->item('fragment_category'));

        $this->display('add');
    }

    /**
     * 刷新碎片缓存
     */
    function _flush_fragment_cache() {
        get_data('fragment', true);
    }
    
    /**
     * 数据源访问
     * 注：这里用了CI的路由规则，参数带入URL的参数
     */
    function ds($cc = '', $cm = '') {
        $cc & $cm || die('');
        // 加载数据源类的抽象类
        include_once (FCPATH . APPPATH . 'libraries/ds/DataSource.php');
        // 加载类
        $class_name = ucwords($cc) . 'DataSource';
        include_once (FCPATH . APPPATH . 'libraries/ds/impl/' . $class_name . '.php');
        $instance = new $class_name($this);
        switch($cm) {
            case 'list':
                return $instance->get_list();
                break;
            default:
                return $instance->get_one();
        }
    }
    
    /**
     * 保存数据
     */
    function save_data() {
        if($this->is_post()) {
            $fid = $this->get('fid');
            $ids = explode('┆', urldecode($this->post('id')));
            $titles = explode('┆', urldecode($this->post('title')));
            $title_links = explode('┆', urldecode($this->post('title_link')));
            $categorys = explode('┆', urldecode($this->post('category')));
            $category_links = explode('┆', urldecode($this->post('category_link')));
            $images = explode('┆', urldecode($this->post('image')));
            $image_links = explode('┆', urldecode($this->post('image_link')));
            $authors = explode('┆', urldecode($this->post('author')));
            $author_links = explode('┆', urldecode($this->post('author_link')));
            $author_avatars = explode('┆', urldecode($this->post('author_avatar')));
            $intros = explode('┆', urldecode($this->post('intro')));
            $start_times = explode('┆', urldecode($this->post('start_time')));
            $end_times = explode('┆', urldecode($this->post('end_time')));
            
            $length = count($ids);
            $data = array();
            for($i = 0; $i < $length; $i++) {
                if(empty($ids[$i]) && empty($titles[$i]) && empty($title_links[$i])
                   && empty($categorys[$i]) && empty($category_links[$i]) && empty($images[$i])
                   && empty($image_links[$i]) && empty($authors[$i]) && empty($author_links[$i]) 
                   && empty($author_avatars[$i]) && empty($intros[$i]) && empty($start_times[$i])
                   && empty($end_times[$i])) {
                    continue;
                }
                
                $data[$i]['dataId'] = $ids[$i];
                $data[$i]['title'] = $titles[$i];
                $data[$i]['titleLink'] = $title_links[$i];
                $data[$i]['category'] = $categorys[$i];
                $data[$i]['categoryLink'] = $category_links[$i];
                $data[$i]['image'] = $images[$i];
                $data[$i]['imageLink'] = $image_links[$i];
                $data[$i]['author'] = $authors[$i];
                $data[$i]['authorLink'] = $author_links[$i];
                $data[$i]['authorAvatar'] = $author_avatars[$i];            
                $data[$i]['intro'] = $intros[$i];
                $data[$i]['startTime'] = strtotime($start_times[$i]);
                $data[$i]['endTime'] = strtotime($end_times[$i]);
                $data[$i]['serialNo'] = ($i+1);
                $data[$i]['fid'] = $fid;
            }
            
            // 先删除原有数据
            $b = $this->db->delete('RecommendData', "fid = '{$fid}'");
            // 添加新数据
            $b &= $this->db->insert_batch('RecommendData', $data);
            
            if($b & $this->_update_data($fid)) {
                // 更新碎片数据
                die('保存成功');
            } else {
                die('保存失败');
            }
        }
    }

    /**
     * 获取已保存数据
     */
    function get_saved_data() {
        // 碎片ID号
        $fid = $this->get('fid');
        // 
        $fragments = get_data('morris_fragment');
        $fragment = $fragments[$fid];
        $fragment['linkId'] && $fid = $fragment['linkId'];
        
        $data = array();
        $result = $this->db->order_by('serialNo', 'asc')->get_where('RecommendData', "fid = '{$fid}'")->result_array();
        foreach($result as $row) {
            $arr = array();
            
            $arr['id'] = $row['dataId'];
            $arr['title'] = $row['title'];
            $arr['title_link'] = $row['titleLink'];
            $arr['category'] = $row['category'];
            $arr['category_link'] = $row['categoryLink'];
            $arr['image'] = substr($row['image'], 0, strrpos($row['image'], '/'));
            $arr['image_url'] = $row['image'];
            $arr['image_link'] = $row['imageLink'];
            $arr['author'] = $row['author'];
            $arr['author_link'] = $row['authorLink'];
            $arr['author_avatar'] = substr($row['authorAvatar'], 0, strrpos($row['authorAvatar'], '/'));
            $arr['author_avatar_url'] = $row['authorAvatar'];
            $arr['intro'] = $row['intro'];
            $arr['start_time'] = date('Y-m-d H:i:s', $row['startTime']);
            $arr['end_time'] = date('Y-m-d H:i:s', $row['endTime']);
            $arr['fid'] = $row['fid'];
            $arr['serial_no'] = $row['serialNo'];
            
            $data[] = $arr;
            unset($arr);
        }
        
        die(json_encode($data));
    }
    
    /**
     * 更新碎片
     */
    function update_data() {
        $fid = $this->get('fid');
        
        if($this->_update_data($fid)) {
            die('更新成功');
        } else {
            die('更新失败');
        }
    }

    /**
     * 更新模块数据
     * @param $fid 模块ID号
     */
    function _update_data($fid) {
        $b = true;
        $fragments = get_data('morris_fragment');
        $fragment = $fragments[$fid];
        if($fragment['updateObject']) {
            // 
            
            $html = @file_get_contents($this->config->item('web_site') . '/' . $fragment['updateObject'] . $fragment['description']);
            
            // 写入到HTML目录下
            $b = @file_put_contents($this->config->item('html_path') . $fragment['updateObject'] . '.html', $html);
        } 
        
        return $b;
    }
}
