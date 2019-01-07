<?php
/**
 * 权限管理
 * Create by 2012-3-1
 * @author Liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Code
class Rights extends MY_Controller {

    /**
     * 功能首页，显示权限列表
     * Create by 2012-3-1
     * @author Liuw
     * @param string $keyword
     */
    public function index() {
        $list = array();
        //数据查询
        $this->db->order_by('LENGTH(path)', 'asc');
        $this->db->order_by('serialno', 'asc');
        $this->db->order_by('id', 'asc');
        $query = $this->db->get('MorrisRights');
        if (isset($query) && !empty($query)) {
            foreach ($query->result_array() as $row) {
                $list[$row['id']] = $row;
            }
        }
        $this->assign('list', $list);
        $this->display('rights');
    }

    /**
     * 添加新权限
     * Create by 2012-3-1
     * @author Liuw
     */
    public function add_new() {
        if ('POST' == $this->server('REQUEST_METHOD')) {
            //获取表单数据
            $names = $this->post('name');
            $parentids = $this->post('parentid');
            $ismenus = $this->post('ismenu');
            $uris = $this->post('uri');
            $serialnos = $this->post('serialno');
            $islogs = $this->post('islog');
            $depths = $this->post('depth');
            $paths = $this->post('path');
            //获取上级权限的depth和path
            //封装数据
            $inserts = array();
            for ($i = 0; $i < count($names); $i++) {
                $item = array(
                        'name' => $names[$i],
                        'uri' => $uris[$i],
                        'ismenu' => $ismenus[$i],
                        'parentid' => $parentids[$i],
                        'depth' => -1, //插入数据后需要修改
                        'path' => $paths[$i], //插入数据后需要修改
                        'serialno' => $serialnos[$i],
                        'islog' => $islogs[$i]
                );
                $inserts[] = $item;
            }
            //保存数据
            $this->db->insert_batch('MorrisRights', $inserts);
            //更新depth和path,STEP1：获得depth为－1的纪录id和pid
            $this->db->select('id');
            $this->db->where('depth', -1);
            $query = $this->db->get('MorrisRights');
            $ids = array();
            foreach ($query->result() as $row) {
                $ids[] = $row->id;
            }
            //STEP2：更新depth和path
            $this->_update_depth_path($ids);

            $message = $this->lang->line('rights_add_success');
            //更新缓存
            get_data('menu', TRUE);
            $statusCode = 200;
            $navTabId = $this->_uri;
            $this->message($statusCode, $message, $this->_index_rel, $this->_index_uri, 'forward');
        }
    }

    /**
     * 编辑权限
     * Create by 2012-3-1
     * @author Liuw
     */
    public function edit() {
    	$id = $this->get('id');
        if (!isset($id) || empty($id))
            exit(json_encode(array(
                    'errcode' => 1,
                    'msg' => $this->lang->line('rights_edit_not_chang')
            )));
        else {
            if ('POST' == $this->server('REQUEST_METHOD')) {

                //执行修改
                $edit = array(
                        'name' => $this->post('name'),
                        'parentid' => intval($this->post('parentid')),
                        'ismenu' => intval($this->post('ismenu')),
                        'uri' => $this->post('uri'),
                        'serialno' => intval($this->post('serialno')),
                        'islog' => $this->post('islog')
                );
                $this->db->where('id', $id);
                $r = $this->db->get('MorrisRights')->first_row('array');
                if (isset($r) && !empty($r)) {
                    $oldpid = $r['parentid'];
                    $this->db->where('id', $id);
                    $this->db->update('MorrisRights', $edit);
                    if ($oldpid != $edit['parentid']) {
                        $ids = array($id);
                        $this->_update_depth_path($ids);
                        //更新depth和path
                        $query = $this->db->query("SELECT id FROM MorrisRights WHERE id<>'{$id}' AND path LIKE '%{$id}%' order by depth asc");
                        foreach ($query->result() as $row) {
                            $this->_update_depth_path(array($row->id));
                        }
                    }
                    //更新缓存
                    get_data('menu', TRUE);
                    $this->success($this->lang->line('rights_edit_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
                }
            }
            //查询要修改的数据
            $this->db->where('id', $id);
            $query = $this->db->get('MorrisRights');
            $right = $query->first_row('array');
            if (!isset($right) || empty($right))
            	$this->error($this->lang->line('rights_edit_not_here'));                
            else{
            	$edit = $right;
            	$this->assign(compact('edit','id'));
            	$this->display('rights_edit', 'rights');
            }                
        }
    }

    /**
     * 删除权限
     * Create by 2012-3-1
     * @author Liuw
     */
    public function delete() {
    	$id = $this->get('id');
        if ('POST' == $this->server('REQUEST_METHOD')) {
            $statusCode = 200;
            $message = $this->lang->line('rights_del_success');
            $navTabId = $this->_uri;
            //删除权限
            $this->db->where('id', $id);
            $delright = $this->db->get('MorrisRights')->first_row('array');
            $parentid = $delright['parentid'];
            if (isset($delright) && !empty($delright)) {

                //移动子权限到根路径
                $this->db->where('parentid', $id);
                $query = $this->db->get('MorrisRights');
                $ids = array();
                foreach ($query->result_array() as $row) {
                    $ids[] = $row['id'];
                }
                if (!empty($ids)) {
                    $this->db->where('parentid', $id);
                    $this->db->update('MorrisRights', array('parentid' => $parentid));
                    //重新计算子权限的depth和path
                    $this->_update_depth_path($ids);
                }
                //删除指定的权限
                $this->db->where('id', $id);
                $this->db->delete('MorrisRights');
                //更新缓存
                get_data('menu', TRUE);
            }
            $this->message($statusCode, $message, $this->_index_rel, $this->_index_uri, 'forward');
        }
    }

    /**
     * 更新权限的depth和path属性
     * Create by 2012-3-2
     * @author Liuw
     * @param array $ids
     */
    private function _update_depth_path($ids) {
        if (isset($ids) && !empty($ids)) {
            $sql = 'SELECT a.id,b.depth,b.path FROM MorrisRights a INNER JOIN (SELECT id,depth,path FROM MorrisRights) b ON b.id=a.parentid WHERE a.id IN (\'' . implode("','", $ids) . '\')';
            $query = $this->db->query($sql);
            $row = $query->first_row();
            if (!isset($row) || empty($row)) {
                //上级权限为空的情况，只有创建根节点菜单时才会出现
                foreach ($ids as $id) {
                    $edit = array(
                            'depth' => 2,
                            'path' => '0,' . $id
                    );
                    $this->db->where('id', $id);
                    $this->db->update('MorrisRights', $edit);
                }
            } else {
                //一般情况
                foreach ($query->result() as $row) {
                    $edit = array(
                            'depth' => $row->depth + 1,
                            'path' => $row->path . ',' . $row->id,
                    );
                    $this->db->where('id', $row->id);
                    $this->db->update('MorrisRights', $edit);
                }
            }
        }
    }

}

// File end
