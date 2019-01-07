<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/**
 * 电子报
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-27
 */

class Enewspaper extends MY_Controller {
    function __construct() {
        parent::__construct();
        
        $this->load->model('enewspaper_model', 'model');
    } 
    
    /**
     * 电子报
     */
    function index() {
        
        $this->display('index');
    }
    
    /**
     * 草稿箱
     */
    function draft() {
        $total_num = $this->model->get_counts(1);
        $paginate = $this->paginate($total_num);
        $list = $this->model->get_list(1, '', $paginate['per_page_num'], $paginate['offset']);
        $this->assign('list', $list);
        $this->display('draft');
    }
    
	/**
	 * 删除
	 */
	function del() {
		$id = intval($this->get('id'));
		
		$b = $this->model->update_data($id, array('status' => 2));

		$b?$this->success('删除成功', $this->_index_rel, $this->_index_uri):$this->error('删除失败');
	}

	/**
	 * 发布
	 */
	function pub() {
		$id = intval($this->get('id'));
		
		$b = $this->model->update_data($id, array('status' => 0, 'publishDate' => now()));

		$b?$this->success('发布成功', $this->_index_rel, $this->_index_uri):$this->error('发布失败');
	}

    /**
     * 已发布
     */
    function published() {
        $keywords = trim($this->post('keywords'));
        
        $this->assign('keywords', addslashes($keywords));
        
        $total_num = $this->model->get_counts(0);
        $paginate = $this->paginate($total_num);
        
        $list = $this->model->get_list(0, $keywords, $paginate['per_page_num'], $paginate['offset']);
        $this->assign('list', $list);
        
        $this->display('published');
    }
    
    /**
     * 添加 编辑 
     */
    function add() {
        $id = intval($this->get('id'));

        if($id) {
            $paper = $this->model->get_data($id);
            if(empty($paper)) {
                $this->error('错误');
            }
        }
        
		if($this->is_post()) {
			$subject = trim($this->post('subject'));
			if(empty($subject)) {
				$this->error('请输入标题');
			}

			$contents = $this->post('content');
			$images = $this->post('image');

			$content_arr = array();
			if($contents) {
				foreach($contents as $i => $content) {
					$content = trim($content);
					$image = $images[$i];
	
					if($content || $image) {
						$content_arr[] = array(
							'content' => $content,
							'image' => $image
						);
					}
				}
			}

			$data = array(
				'subject' => $subject,
				'content' => encode_json($content_arr)
			);
			$b = true;
			if($id) {
				// 修改
				$b = $this->model->update_data($id, $data);
			} else {
				// 新建
				$data['status'] = 1;
				$b = $this->model->insert_data($data);
			}

			$b?$this->success('提交成功', $this->_index_rel, $this->_index_uri, 'closeCurrent'):$this->error('添加失败');
        }

		if($id) {
			$this->assign(array('id' => $id, 'paper' => $paper, 'content' => json_decode($paper['content'], true)));
		}
        
        $this->display('add');
    }
    
    /**
     * 编辑
     */
    function edit() {
        $this->add();
    }
    
}