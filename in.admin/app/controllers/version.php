<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 版本控制
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-6-12
 */

class Version extends MY_Controller {
    var $client_type;
    function __construct() {
        parent::__construct();
        $this->client_type = $this->config->item('client_type');
        $this->assign('client_type', $this->client_type);
    }
    
    /**
     * 版本记录
     */
    function index() {
        $type = $this->post('type');
        $keywords = $this->post('keywords');
        
        $where_sql = array();
        $keywords && $where_sql[] = "version = '{$keywords}'";
        $type && $where_sql[] = "type = '" . ($type - 1) . "'";
        $where_sql && $where_sql = implode(' and ', $where_sql);
        
        $total_num = $this->db->where($where_sql)->from('ClientVersion')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('ClientVersion')
                         ->get()->result_array();
        $this->assign(compact('type', 'keywords', 'list'));
        
        $this->display('index');
    }
    
    
    /**
     * 发布版本
     */
    function add() {
        $id = $this->get('id');
        
        $version = $this->db->get_where('ClientVersion', "id = '{$id}'")->row_array();
        
        if($this->is_post()) {
            $data = array();
            $data['type'] = $this->post('type');
            $data['version'] = trim($this->post('version'));
            $data['downloadUri'] = trim($this->post('download_uri'));
            $data['isUpdateRequired'] = $this->post('is_update_required');
            $data['changeLog'] = $this->post('change_log');
            
            if(strpos($data['downloadUri'], 'http://') !== 0 && strpos($data['downloadUri'], 'https://') !== 0) {
            	$data['downloadUri'] = 'http://' . $data['downloadUri'];
            }
            
            $tip = '';
            if($id) {
                // 修改
                $tip = '修改';
                $b = $this->db->update('ClientVersion', $data, "id = '{$id}'");
            } else {
                // 增加
                // 判断之前是否添加版本一样。类型一样的给与提示
                if($this->db->get_where('ClientVersion', "type = '{$data['type']}' and version = '{$data['version']}'")->row_array()) {
                    // 提示真的要提交么？
                    $this->error('已经发布一个[' . $this->client_type[$data['type']] . ']下版本为['.$data['version'].']，不能重复提交');
                }
                $tip = '发布';
                $b = $this->db->insert('ClientVersion', $data);
            }
            
            // 更新版本缓存
            api_update_cache('ClientVersion');

            $b?$this->success($tip . '版本成功', '', '', 'closeCurrent'):$this->error($tip . '版本失败');
        }
        
        $this->assign('version', $version);
        
        $this->display('add');
    }
    
    /**
     * 修改版本
     */
    function edit() {
        $this->add();
    }
}
