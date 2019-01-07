<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 地点数据源
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-24
 */

class PoiDataSource extends DataSource {
    /**
     * 得到列表记录
     */
    function get_list() {
        if($this->c->is_post()) {
            // 提交查询
            $type = $this->c->post('type');
            $name = $this->c->post('name');
            if(empty($name)) {
                $where_sql = array();
            } else {
                // 不为空那么需要判断那种字段查询
                switch($type) {
                    case 'id':
                        $where_sql = $type . ' = ' . $name;
                        break;
                    default:
                        $where_sql = "{$type} like '%{$name}%'";
                }
            }
            
            $data = $this->c->db->limit($this->num)->get_where('Place', $where_sql)->result_array();
            $results = array();
            foreach($data as $row) {
                $results[] = $this->_row($row);
            }
            
            echo json_encode($results);
        }
        
        return $this->c->fetch('ds_search_poi', 'ds');
    }
    
    /**
     * 得到一条记录
     */
    function get_one() {
        if($this->c->is_post()) {
            // 提交
            $id = $this->c->post('id');
            $row = $this->c->db->get_where('Place', "id = '{$id}'")->row_array();
            
            echo json_encode($this->_row($row));
        }
    }
    
    /**
     * 处理一行数据
     */
    function _row($row) {
        $result = array();
        // 唯一标识
        $result['id'] = $row['id'];
        // 地点名
        $result['title'] = $row['placename'];
        // 地点连接 
        $result['title_link'] = get_web_url('poi', array($row['id'])); 
        return $result;
    }
}
