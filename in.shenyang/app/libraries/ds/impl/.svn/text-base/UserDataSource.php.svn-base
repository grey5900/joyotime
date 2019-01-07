<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 用户数据源
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-24
 */

class UserDataSource extends DataSource {
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
            
            $data = $this->c->db->limit($this->num)->get_where('User', $where_sql)->result_array();
            $results = array();
            foreach($data as $row) {
                $results[] = $this->_row($row);
            }
            
            echo json_encode($results);
        }
        
        return $this->c->fetch('ds_search_user', 'ds');
    }
    
    /**
     * 得到一条记录
     */
    function get_one() {
        if($this->c->is_post()) {
            // 提交
            $id = $this->c->post('id');
            $row = $this->c->db->get_where('User', "id = '{$id}'")->row_array();
            
            echo json_encode($this->_row($row));
        }
    }
    
    /**
     * 处理一行数据
     */
    function _row($row) {
        $result = array();
        $result['id'] = $row['id']; // 唯一标识，用户ID号
        $result['author'] = $row['nickname']?$row['nickname']:$row['username']; // 用户名或者昵称
        $result['author_link'] = get_web_url('user', array($row['id'])); // 用户连接
        $result['author_avatar'] = $row['avatar']; // 用户头像名称
        $result['author_avatar_url'] = image_url($row['avatar'], 'head', 'hdpl'); // 用户图片的绝对地址
        return $result;
    }
}
