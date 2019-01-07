<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 上墙的接口
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-11-9
 */

class Wall extends MY_Controller {
    private $_redis;
    // 最大的源数据条数
    private $_max_source = 200;
    // 最大的显示条数
    private $_max_post = 200;
    
    function __construct() {
        parent::__construct();
        $this->load->driver('cache', array('adapter' => 'redis'));
        
        $this->_redis = $this->cache->redis;
        
        $this->_callback = $this->get('callback');
    }
    
    function index() {
        
    }
    
    /**
     * 获取列表
     * @param string 名称
     * @param string 格式 1001-1002-1003-1004
     * @param int 条数
     * @return json array
     */
    function post_list_place($name = '', $pids = '', $num = 20) {
        $num = intval($num);
        ($num <= 0) && ($num = 20);
        ($num > $this->_max_source) && ($num = $this->_max_source);
        
        $pids = array_filter(explode('-', $pids));
        if($pids) {
            $this->db->where_in('placeId', $pids);
        }
        
        $list = $uids = $pids = array();
        $this->db->where_in('type', array(2, 3));
        $this->db->order_by('createDate', 'desc');
        $this->db->limit($num);
        $rows = $this->db->get('Post')->result_array();
        foreach($rows as $row) {
            $list[] = $row;
            empty($uids[$row['uid']]) && ($uids[$row['uid']] = 1);
            empty($pids[$row['placeId']]) && ($pids[$row['placeId']] = 1);
        }
        unset($rows);
        
        // 去获取用户数据
        if($uids) {
            $rows = $this->db->where_in('id', array_keys($uids))->get('User')->result_array();
            $users = array();
            foreach($rows as $row) {
                $users[$row['id']] = array(
                    'name' => $row['nickname']?$row['nickname']:$row['username'],
                    'avatar' => image_url($row['avatar'], 'head')
                );
            }
            unset($rows);
        }
        
        // 去获取地点数据
        if($pids) {
            $rows = $this->db->where_in('id', array_keys($pids))->get('Place')->result_array();
            $places = array();
            foreach($rows as $row) {
                $places[$row['id']] = $row['placename'];
            }
            unset($rows);
        }
        
        $data = array();
        foreach($list as $row) {
            $data[] = array(
                'postId' => $row['id'],
                'type' => $row['type'],
                'placename' => $places[$row['placeId']],
                'content' => $row['content'],
                'photo' => $row['photo']?image_url($row['photo'], 'user', 'thweb'):'',
                'createDate' => $row['createDate'],
                'username' => $users[$row['uid']]['name'],
                'avatar' => $users[$row['uid']]['avatar']
            );
        }
        unset($list);
        
        $this->echo_json(array('status' => 0, 'data' => $data), $this->_callback);
    }

    /**
     * 获取列表
     * @param string 名称
     * @param int 品牌ID号
     * @param int 条数
     * @return json array
     */
    function post_list_brand($name = '', $band_id = 0, $num = 20) {
        
    }
    
    /**
     * 获取列表
     * @param string 名称
     * @param float 最后一次排序的大小
     * @return json array
     */
    function list_post($name = '', $score = 0) {
        $name = trim($name);
        empty($name) && $this->echo_json(array('status' => 500, 'message' => '错误，没有name'), $this->_callback);
        
        ($score <= 0) && ($score = 0);
        
        $key = $this->_get_key($name);
        $count = $this->_redis->zSize($key);
        $list = $this->_redis->zRangeByScore($key, $score, $this->_get_score(), array('withscores' => true));
        $data = array();
        foreach($list as $key => $row) {
            $data[] = json_decode($key, true);
        }
        unset($list);
        
        $this->echo_json(array('status' => 0, 'data' => array_reverse($data)), $this->_callback);
    }
    
    /**
     * 获取列表
     * @param string 名称
     * @param int POST 的ID号
     * @return json array
     */
    function checked($name = '', $id = 0) {
        $name = trim($name);
        $id = intval($id);
        (empty($name) || $id <= 0) && $this->echo_json(array('status' => 500, 'message' => '错误，没有name'), $this->_callback);
        
        $key = $this->_get_key($name);
        
        $score = $this->_get_score();
        
        $num = $this->_redis->zSize($key);
        if($num >= $this->_max_post) {
            // 删除一条 第一条
            $this->_redis->zDeleteRangeByRank($key, 0, 0);
        }
        $data = $this->_get_post($id);
        $data['score'] = $score;
        $this->_redis->zRangeByScore();
        $b = $this->_redis->zAdd($key, $score, json_encode($data));
        
        $b?$this->echo_json(array('status' => 0, 'message' => '成功'), $this->_callback):$this->echo_json(array('status' => 500, 'message' => '失败'), $this->_callback);
    }
    
    /**
     * 清空活动
     */
    function clean($name = '') {
        $name = trim($name);
        empty($name) && $this->echo_json(array('status' => 500, 'message' => '错误，没有name'), $this->_callback);
        
        $key = $this->_get_key($name);
        $b = $this->_redis->zRemRangeByScore($key, 0, $this->_get_score()); 
        
        $b?$this->echo_json(array('status' => 0, 'message' => '成功'), $this->_callback):$this->echo_json(array('status' => 500, 'message' => '失败'), $this->_callback);
    }
    
    /**
     * 获取一条POST数据
     * @param int POST的ID号
     */
    private function _get_post($id) {
        $id = intval($id);
        
        if($id <= 0) {
            return array();
        }
        
        $data = array();
        $post = $this->db->get_where('Post', array('id' => $id))->row_array();
        if($post) {
            $data = array(
                'postId' => $post['id'],
                'type' => $post['type'],
                'content' => $post['content'],
                'photo' => $post['photo']?image_url($post['photo'], 'user', 'thweb'):'',
                'createDate' => $post['createDate']
            );
            
            $place = $this->db->get_where('Place', array('id' => $post['placeId']))->row_array();
            $data['placename'] = $place['placename'];
            
            $user = $this->db->get_where('User', array('id' => $post['uid']))->row_array();
            $data['username'] = $user['nickname']?$user['nickname']:$user['username'];
            $data['avatar'] = image_url($user['avatar'], 'head');
        }
        
        return $data;
    }
    
    /**
     * 获取保存数据的键值
     * @param 
     * @return 
     */
    private function _get_key($name) {
        return md5($name);
    }
    
    /**
     * 获取score
     */
    private function _get_score() {
        $m = substr(microtime(true), 5)/100;
        return sprintf('%.6f', $m);
    }
}