<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * 查找带回
 *
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-5-9
 */

class Lookup extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->assign('is_dialog', true);
    }

    /**
     * 返回马甲列表
     * Create by 2012-3-27
     * @author liuw
     * @param mixed $aid,机甲帐户id，如果为假，则查询全部马甲
     * @param mixed $template,模板名称，如果为假，则输出json字符串。此类模板必须放在sys目录下面
     */
    public function listvest() {
        $aid = $this->get('aid');
        $aid = isset($aid) && !empty($aid) ? intval($aid) : $this->auth['id'];
        $template = $this->get('tmp');
        $template = isset($template) && !empty($template) ? $template : FALSE;
        $count = $this->db->where('aid', $aid, FALSE)->count_all_results('MorrisVest');
        if ($count) {
            //分页
            $parr = $this->paginate($count);
            
            $this->db->select('MorrisVest.*,User.username,User.nickname,User.realname,User.avatar');
            $this->db->from('MorrisVest');
            $this->db->join('User', 'User.id=MorrisVest.uid', 'inner');
            if ($aid)
                $this->db->where('MorrisVest.aid', $aid, FALSE);
            $this->db->order_by('MorrisVest.dateline', 'desc');
            $this->db->limit($parr['per_page_num'], $parr['offset']);
            $query = $this->db->get();
            $list = array();
            foreach ($query->result_array() as $row) {
                $list[$row['uid']] = $row;
            }
        }
        if ($template) {
            $this->assign('list', $list);
            $this->display($template);
        } else {
            $list[] = array(
                    'id' => 1,
                    'realname' => 'test'
            );
            exit(json_encode($list));
        }
    }
    
    /**
     * 查找带回
     * @param $type place/user
     */
    function list_lookup() {
        $type = $this->get('type');
        
        if(in_array($type, array('place', 'user'))) {
            $this->$type();
        }
    }
    
    /**
     * 地点的查找带回
     */
    function place() {
        $keywords = trim($this->post('keywords'));
        
        $where_sql = 'status = 0';
        if($keywords !== '') {
            $keywords_text = daddslashes($keywords);
            $where_sql .= " and placename like '%{$keywords_text}%'";
        }
        $total_num = $this->db->from('Place')->where($where_sql)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('Place')
                         ->get()->result_array();
        
        $this->assign(compact('keywords', 'list'));
        
        $this->display('place');
    }
    
    /**
     * 用户的查找带回
     */
    function user() {
        $keywords = trim($this->post('keywords'));
        $where_sql = array();
        // 关键词条件
        if ($keywords !== '') {
            $stype = $this->post('stype');
            $keywords_text = daddslashes($keywords);
            switch($stype) {
                case 'id' :
                    $where_sql = "id = '{$keywords_text}'";
                    break;
                case 'username' :
                    $where_sql = "username like '%{$keywords_text}%'";
                    break;
                case 'nickname' :
                    $where_sql = "nickname like '%{$keywords_text}%'";
                    break;
            }
        }
        
        $total_num = $this->db->from('User')->where($where_sql)->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->order_by('createDate', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('User')
                         ->get()->result_array();
        
        $this->assign(compact('stype', 'keywords', 'list'));
        
        $this->display('user');
    }

    /**
     * 查找带回
     * Create by 2012-5-2
     * @author liuw
     */
    // public function list_lookup() {
        // $type = $this->get('type');
        // $keyword = $this->post('keyword');
        // $keyword = isset($keyword) && !empty($keyword) ? $keyword : '';
        // //确定表和搜索字段
        // $table = $column = $order_col = $label = '';
        // switch($type) {
            // case 'place' :
                // $table = 'Place';
                // $column = 'placename';
                // $label = '地点名称';
                // $order_col = 'createDate';
                // break;
            // case 'user' :
                // $table = 'User';
                // $column = 'username';
                // $label = '用户名';
                // $order_col = 'createDate';
                // break;
            // case 'badge' :
                // $table = '';
                // $column = '';
                // $label = '勋章名称';
                // $order_col = '';
                // break;
        // }
        // $this->assign(compact('type', 'label', 'keyword'));
        // //查询数据总数
        // if ($type === 'place')
            // $this->db->where('status', 0);
        // if (!empty($keyword))
            // $this->db->like($column, $keyword);
        // $count = $this->db->count_all_results($table);
        // if ($count) {
            // //分页
            // $parr = $this->paginate($count);
            // //数据
            // $list = array();
            // if ($type === 'place')
                // $this->db->where('status', 0);
            // if (!empty($keyword))
                // $this->db->like($column, $keyword);
            // $query = $this->db->order_by($order_col, 'desc')->limit($parr['per_page_num'], $parr['offset'])->get($table);
            // foreach ($query->result_array() as $row) {
                // $data = array();
                // //差异化封装数据
                // switch($type) {
                    // case 'place' :
                        // $data['name'] = $row['placename'];
                        // $data['address'] = $row['address'];
                        // $data['createDate'] = $row['createDate'];
                        // break;
                    // case 'user' :
                        // $data['name'] = $row['username'];
                        // $data['realName'] = $row['realName'];
                        // $data['nickname'] = $row['nickname'];
                        // $data['description'] = $row['description'];
                        // break;
                // }
                // $list[$row['id']] = $data;
            // }
            // $this->assign('list', $list);
        // }
        // $this->assign('is_dialog', true);
// 
        // $this->display('lookup');
    // }

    /**
     * 管理员账号查找带回
     */
    function account() {
        $keyword = $this->post('keyword');
        $this->assign('keyword', $keyword);
        $role = $this->post('role');
        $this->assign('role', $role);
        // 查询总的账户数
        $where_str = array();
        if ($keyword) {
            $where_str[] = "name like '%{$keyword}%'";
        }
        if ($role) {
            $where_str[] = "find_in_set('{$role}', role)";
        }
        $where_str && $where_str = implode(' and ', $where_str);
        $total_num = $this->db->where($where_str)->count_all_results('MorrisAdmin');
        $page = $this->paginate($total_num);

        $this->assign('admin', $this->db->where($where_str)->get('MorrisAdmin')->result_array());

        $this->assign('roles', get_data('role'));
        $this->display('account');
    }
    
    /**
     * 推荐碎片的查找带回
     */
    function recommend_fragment() {
        $fragment = get_data('fragment');
        $tree = array();
        foreach($fragment as $key=>$value) {
            $row = array();
            $row['name'] = $value['name'];
            $row['id'] = $key;
            if($value['parentId'] == '0') {
                $tree[$key] = $row;
            } else {
                $tree[$value['parentId']]['children'][$key] = $row;
            }
        }
        $this->assign('tree', $tree);       
        $this->display('recommend_fragment');
    }

}
