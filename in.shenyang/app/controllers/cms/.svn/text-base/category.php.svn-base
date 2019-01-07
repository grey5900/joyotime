<?php
/**
 * 文章分类管理
 * Create by 2012-8-1
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Category extends MY_Controller{
	
	/**
	 * 显示所有分类
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function index(){
		$keyword = $this->post('keyword');
		$this->assign('keyword', $keyword);
		$keyword = !empty($keyword) ? $keyword : FALSE;
		$order_field = $this->post('orderField');
		$order_field = !empty($order_field) ? $order_field : 'cid';
		$order_dire = $this->post('orderDirectory');
		$order_dire = !empty($order_dire) ? $order_dire : 'asc';
		$this->assign('order_field', $order_field);
		$this->assign('order_dire', $order_dire);
		
		$list = array();
		//查询总长度
		if($keyword)
			$this->db->like('category', $keyword);
		$count = $this->db->count_all_results('CmsCategory');
		if($count){
			//分页
			$parr = $this->paginate($count);
			//列表
			if($keyword)
				$this->db->like('category', $keyword);
			$query = $this->db->order_by($order_field, $order_dire)->limit($parr['per_page_num'], $parr['offset'])->get('CmsCategory')->result_array();
			foreach($query as $row){
				$row['template'] = $row['template'] === 'default' ? '默认模板' : $row['template'];
				$list[$row['cid']] = $row;
			}
		}
		$this->assign('list', $list);	
		$this->display('index', 'cms_category');
	}
	
	/**
	 * 添加分类
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function add(){
		$this->assign('info', array());
		//模板列表
		$this->assign('tmps', $this->_get_tmp_list());
		if($this->is_post()){
			//POST参数
			$category = $this->post('category');
			$template = $this->post('template');
			$intro = $this->post('intro');
			//检查分类名是否重复
			$c = $this->db->where('category', $category)->count_all_results('CmsCategory');
			if($c)
				$this->error($this->lang->line('cms_cat_name_used'));
			else{
				$data = compact('category', 'template');
				if(!empty($intro))
					$data['intro'] = trim($intro);
				$this->db->insert('CmsCategory', $data);
				$cid = $this->db->insert_id();
				if(!$cid)
					$this->error($this->lang->line('cms_cat_add_fail'));
				else 
					$this->success($this->lang->line('cms_cat_add_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
		}else{
			$this->display('make', 'cms_category');
		}
	}
	
	/**
	 * 编辑分类
	 * Create by 2012-8-1
	 * @author liuw
	 * @param int $cid
	 */
	function edit($cid){
		$info = $this->db->where('cid', $cid)->get('CmsCategory')->first_row('array');
		$this->assign('info', $info);
		//模板列表
		$this->assign('tmps', $this->_get_tmp_list());
		if($this->is_post()){
			$data = array();
			//POST参数
			$category = $this->post('category');
			$template = $this->post('template');
			$intro = $this->post('intro');
			//如果修改了分类名称，则检查名称是否重复
			if($category !== $info['category']){
				$c = $this->db->where('category', $category)->count_all_results('CmsCategory');
				if($c)
					$this->error($this->lang->line('cms_cat_name_used'));
				else 
					$data['category'] = $category;
			}
			$data['template'] = $template;
			if(!empty($intro))
				$data['intro'] = trim($intro);
			//修改
			$this->db->where('cid', $cid)->update('CmsCategory', $data);
			$this->success($this->lang->line('cms_cat_edit_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
		}else{
			$this->display('make', 'cms_category');
		}
	}
	
	/**
	 * 删除分类
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function delete(){
		if($this->is_post()){
			$cids = $this->post('cids');
			//清空相关文章
			$this->db->where_in('cid', $cids)->delete('CmsContent');
			//删除分类
			$this->db->where_in('cid', $cids)->delete('CmsCategory');
			$this->success($this->lang->line('cms_cat_delete_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}else{
			$this->error($this->lang->line('post_type_error'));
		}
	}
	
	/**
	 * 获得分类列表页的模板列表
	 * Create by 2012-8-2
	 * @author liuw
	 * @return array
	 */
	function _get_tmp_list(){
		//从指定的目录获取CMS列表模板
		$tmps = array();
		$exs = array('.', '..');
		$tmp_folder = $this->config->item('cms_list_tmp_folder');
		//获得目录句柄 
		$folder = @opendir($tmp_folder);
		//获得文件列表
		if($folder){
			while(($file = @readdir($folder))){
				if(!in_array($file, $exs)){
					$tmps[] = substr($file, 0, -5);
				}
			}
		}
		return $tmps;
	}
	
}
   
 // File end