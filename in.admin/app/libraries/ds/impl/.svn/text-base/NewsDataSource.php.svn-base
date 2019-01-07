<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 用户数据源
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-24
 */

class NewsDataSource extends DataSource {
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
            //echo $where_sql;
            $data = $this->c->db->limit($this->num)->get_where('WebNews', $where_sql)->result_array();
            $results = array();
            foreach($data as $row) {
                $results[] = $this->_row($row);
            }
            
            echo json_encode($results);
        }
        
        return $this->c->fetch('ds_search_news', 'ds');
    }
    
    /**
     * 得到一条记录
     */
    function get_one() {
        if($this->c->is_post()) {
            // 提交
            $id = $this->c->post('id');
            $row = $this->c->db->get_where('WebNews', "id = '{$id}'")->row_array();
            
            echo json_encode($this->_row($row));
        }
    }
    
    /**
     * 处理一行数据
     */
    function _row($row) {
        $result = array();
        
        $result['id'] = $row['id'];
        $result['title'] = $row['subject'];
        $result['intro'] = $row['summary'];
        $result['title_link'] = $row['link'];
        $result['image'] = $row['thumb'];
        return $result;
    }
}
