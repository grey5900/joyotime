<?php
/**
 * 话题管理，包括创建和编辑
 * @author bob baohanddd@gmail.com
 * @copyright Copyright(c) 2012-2014 joyotime
 * @since 2013-7-3
 */
class Topic extends MY_Controller {
    
    const HOME_PAGE_DATA_ITEM_TYPE = 26;
    
    function __construct() {
    	parent::__construct();
    }
    
    function index() {
        $pageNum = $this->input->post("pageNum") ? $this->input->post("pageNum") : 1;
        $numPerPage = $this->input->post("numPerPage") ? $this->input->post("numPerPage") : 20;
        
        $orderField = $this->post('orderField');
        $orderField = isset($orderField)&&!empty($orderField)?$orderField:'createDate';
        $orderDirection = $this->post('orderDirection');
        $orderDirection = isset($orderDirection)&&!empty($orderDirection)?$orderDirection:'desc';
        
        if($this->is_post()) {
            $this->db->start_cache();
            if(TRUE == ($keyword = $this->post('keyword'))) {
                $this->db->like('subject', $keyword);
            }
            if(TRUE == ($lastSevenDaysAgo = $this->post('lastSevenDaysAgo'))) {
                $timestamp = strftime('%Y-%m-%d %H:%M:%S', time() - 7 * 24 * 3600);
                $this->db->where('Topic.createDate >=', $timestamp);
            }
            if(TRUE == ($isRecommend = $this->post('isRecommend'))) {
            	$this->db->where('Topic.isOfficial', 1);
            }
            $this->db->join('TopicOwnPost', 'TopicOwnPost.topicId = Topic.id', 'left');
            $this->db->select('Topic.*, COUNT(TopicOwnPost.postId) AS posts, (COUNT(TopicOwnPost.postId) + Topic.rankOrder) AS rank ');
            $this->db->order_by($orderField, $orderDirection);
            $this->db->group_by('Topic.id');
            $this->db->stop_cache();
        } else {
            $this->db->start_cache();
            $this->db->join('TopicOwnPost', 'TopicOwnPost.topicId = Topic.id', 'left');
            $this->db->select('Topic.*, COUNT(TopicOwnPost.postId) AS posts, (COUNT(TopicOwnPost.postId) + Topic.rankOrder) AS rank ');
            $this->db->order_by($orderField, $orderDirection);
            $this->db->group_by('Topic.id');
            $this->db->stop_cache();
        }
        
        $total = $this->db->get('Topic')->num_rows();
        if($total){
        	$parr = $this->paginate($total);
        }
        $list = $this->db->get('Topic', $numPerPage, ($pageNum - 1) * $numPerPage)->result_array();
        $this->db->flush_cache();    // Clean cached sql...
        $this->assign(compact('parr', 'isRecommend', 'keyword', 'lastSevenDaysAgo', 'orderDirection', 'orderField'));
        $this->assign('list', $list);
        $this->display('index', 'ugc/topic');
    }
    
    function add() {
        if($this->is_post()) {
            $this->load->model('topic_model', 'saver');
            $this->db->trans_begin();
            // 标题
            $subject = trim($this->post('subject'));
            if(cstrlen($subject) > 255) {
                return $this->error('话题标题不能大于255个字');
            }
            // 标题是否已存在
            if(!$this->get('id')) {
                if($this->saver->has_exist_subject($subject)) {
                    $this->db->trans_rollback();
                    return $this->error('该话题已存在，如果要推荐该话题，请检索到已有话题，编辑推荐即可');
                }
            }
            // 简介
            $description = trim($this->post('description'));
            if(cstrlen($description) > 500) {
                return $this->error('话题简介不能大于500个字');
            }
            // 封面图片
            $image = trim($this->post('image'));
            if(!$image) {
                return $this->error('请上传话题封面图片');
            }
            // 列表小图片
            $icon = trim($this->post('icon'));
            if(!$icon) {
            	return $this->error('请上传精彩话题列表图片');
            }
            
            if($this->get('id')) {
                // It's process of updating...
                $saved = $this->db->where('id', $this->get('id'))->update('Topic', array(
                    'subject' => $subject,
                    'image' => $image,
                	'icon' => $icon,
                    'description' => $description,
                    'isOfficial' => intval($this->post('is_official')),
                ));
            } else {
                // Create a new one...
                $saved = $this->db->insert('Topic', array(
                    'subject' => $subject,
                    'image' => $image,
                	'icon' => $icon,
                    'description' => $description,
                    'isOfficial' => intval($this->post('is_official')),
                ));
            }
            if($saved) {
                $this->db->trans_commit();
                return $this->success('保存成功', '', '', 'closeCurrent');
            } else {
                $this->db->trans_rollback();
                return $this->error('保存失败，请重试');
            }
        }
        // Do something for initializing before editing...
        if($this->get('id')) {
            $this->db->where('id', $this->get('id'));
            $this->assign('topic', $this->db->get('Topic', 1)->row_array());
        }
        $this->display('add', 'ugc/topic');
    }

    function recommend() {
        $id = $this->get('id');
        
        $item_type = 26;    // The type of topic... 
        
        $this->db->where('id', $id);
        $data = $this->db->get('Topic', 1)->row_array();
        if($data && !empty($data['image'])) {
            $this->load->helper('home');
            if($this->is_post()) {
                $err = recommend_digest_post($item_type, $id);
                $err === 0 ? $this->success('推荐成功', '', '', 'closeCurrent') : $this->error($err);
            }
            recommend_digest($item_type, $id, image_url($data['image'], 'default'));
        } else {
            $this->error('请先编辑话题的封面图片及描述', '', '', 'closeCurrent');
        }
    }
    
    function update_rank() {
        $id = $this->get('id');
        $rank = $this->get('rank');
        if($id && $rank >= 0) {
            $saved = $this->db->where('id', $id)->update('Topic', array(
        		'rankOrder' => $rank,
            ));
        }
    }
}