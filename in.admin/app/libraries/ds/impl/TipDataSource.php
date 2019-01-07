<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 点评数据源
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-24
 */

class TipDataSource extends DataSource {
    /**
     * 得到列表记录
     */
    function get_list() {
        if($this->c->is_post()) {
            // 提交查询
            $type = $this->c->post('type');
            $name = $this->c->post('name');
            $where_sql = 'p.id = po.placeId and u.id = po.uid and po.type=2';
            if($name) {
                // 不为空那么需要判断那种字段查询
                $where_sql .= " and {$type} like '%{$name}%'";
            }
            
            $data = $this->c->db->select('po.*,u.username,u.nickname,p.placename,u.avatar')
            ->limit($this->num)->from('Place p, Post po, User u')->order_by('po.createDate', 'desc')
            ->where($where_sql)->get()->result_array();
            $results = array();
            foreach($data as $row) {
                $results[] = $this->_row($row);
            }
            
            echo json_encode($results);
        }
        
        return $this->c->fetch('ds_search_tip', 'ds');
    }
    
    /**
     * 得到一条记录
     */
    function get_one() {
        if($this->c->is_post()) {
            // 提交
            $id = $this->c->post('id');
            $row = $this->c->db->select('po.*,u.username,u.nickname,p.placename,u.avatar')
            ->from('Place p, Post po, User u')
            ->where("p.id = po.placeId and u.id = po.uid and po.type=2 and po.id='{$id}'")
            ->get()->row_array();
            
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
        // 点评
        $result['intro'] = $row['content'];
        // 点评连接 
        $result['title_link'] = get_web_url('tip', array($row['id']));
        // 用户昵称
        $result['author'] = $row['nickname']?$row['nickname']:$row['username']; // 用户名或者昵称
        $result['author_link'] = get_web_url('user', array($row['uid'])); // 用户连接
        $result['author_avatar'] = $row['avatar']; // 用户头像名称
        $result['author_avatar_url'] = image_url($row['avatar'], 'head', 'mdpl'); // 用户图片的绝对地址
        // 地点名
        $result['category'] = $row['placename'];
        // 地点连接 
        $result['category_link'] = get_web_url('poi', array($row['placeId'])); 
        
        return $result;
    }
}
