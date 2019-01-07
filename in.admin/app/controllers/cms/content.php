<?php
/**
 * 文章管理
 * Create by 2012-8-1
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Content extends MY_Controller{
	
	/**
	 * 文章列表
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function index(){
		$cid = $this->post('cid');
		$this->assign('cid', $cid);
		$cid = !empty($cid) ? intval($cid) : false;
		$keyword = $this->post('keyword');
		$this->assign('keyword', $keyword);
		$keyword = !empty($keyword) ? trim($keyword) : false;
		//分类列表
		$cates = $this->_get_categories();
		$this->assign('cates', $cates);
		//总长度
		if($cid)
			$this->db->where('cid', $cid);
		if($keyword){
			$this->db->like('subject', $keyword);
			$this->db->like('content', $keyword);
		}
		$count = $this->db->count_all_results('CmsContent');
		$list = array();
		if($count){
			//分页
			$parr = $this->paginate($count);
			//数据
			if($cid)
				$this->db->where('cid', $cid);
			if($keyword){
				$this->db->like('subject', $keyword);
				$this->db->like('content', $keyword);
			}
			$arr = $this->config->item('web_url');
			$tmp_link = $arr['cms'];
			$query = $this->db->order_by('createDate', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get('CmsContent')->result_array();
			foreach($query as $row){
				$row['createDate'] = substr($row['createDate'], 0, -3);
				//内容地址
				$row['link'] = vsprintf($tmp_link, $row['id']);
				$row['cate'] = $cates[$row['cid']];
				$row['s_status'] = $row['status'] == 1 ? '已发布' : '未发布';
				$list[$row['id']] = $row;
			}
		}
		$this->assign('list', $list);
		$this->display('index', 'cms_content');
	}
	
	/**
	 * 添加文章
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function add(){
		$this->assign('cates', $this->_get_categories());
		if($this->is_post()){
			//POST数据
			$subject = $this->post('subject');
			$content = $this->post('content');
			$source = $this->post('source');
			$jumpLink = $this->post('jumpLink');
			$includeDate = $this->post('includeDate');
			$cid = $this->post('cid');
			//数据完整性检查
			if(empty($subject))
				$this->error($this->lang->line('cms_content_empty_sub'));
			elseif(empty($content))
				$this->error($this->lang->line('cms_content_empty_cont'));
			elseif(empty($source))
				$this->error($this->lang->line('cms_content_empty_source'));
			elseif(empty($jumpLink))
				$this->error($this->lang->line('cms_content_empty_jump'));
			elseif(empty($includeDate))
				$this->error($this->lang->line('cms_content_empty_inc'));
			elseif(empty($cid))
				$this->error($this->lang->line('cms_content_empty_cid'));
			//封装数据
			$status = $this->config->item('cms_content_def_status');
			$createDate = gmdate('Y-m-d H:i:s', time()+8*3600);
			$data = compact('subject', 'content', 'source', 'jumpLink', 'includeDate', 'cid', 'createDate', 'status');
			//保存数据
			$this->db->insert('CmsContent', $data);
			$id = $this->db->insert_id();
			if(!$id)
				$this->error($this->lang->line('cms_content_add_fail'));
			else{ 
				//更新分类文章统计
				$this->db->query('UPDATE CmsCategory SET writingCount=writingCount+1 WHERE cid=?',array($cid));
				$this->success($this->lang->line('cms_content_add_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
		}else{
			$this->display('make', 'cms_content');
		}
	}
	
	/**
	 * 编辑文章
	 * Create by 2012-8-1
	 * @author liuw
	 * @param int $id
	 */
	function edit($id){
		$this->assign('cates', $this->_get_categories());
		//数据
		$info = $this->db->where('id', $id)->get('CmsContent')->first_row('array');
		$info['includeDate'] = substr($info['includeDate'], 0, -3);
		$this->assign('info', $info);
		if($this->is_post()){
			//POST数据
			$subject = $this->post('subject');
			$content = $this->post('content');
			$source = $this->post('source');
			$jumpLink = $this->post('jumpLink');
			$includeDate = $this->post('includeDate');
			$cid = $this->post('cid');
			//数据完整性检查
			if(empty($subject))
				$this->error($this->lang->line('cms_content_empty_sub'));
			elseif(empty($content))
				$this->error($this->lang->line('cms_content_empty_cont'));
			elseif(empty($source))
				$this->error($this->lang->line('cms_content_empty_source'));
			elseif(empty($jumpLink))
				$this->error($this->lang->line('cms_content_empty_jump'));
			elseif(empty($includeDate))
				$this->error($this->lang->line('cms_content_empty_inc'));
			elseif(empty($cid))
				$this->error($this->lang->line('cms_content_empty_cid'));
			//封装数据
			$createDate = gmdate('Y-m-d H:i:s', time()+8*3600);
			$data = compact('subject', 'content', 'source', 'jumpLink', 'includeDate', 'cid', 'createDate');
			//更新数据
			$this->db->where('id', $id)->update('CmsContent', $data);
			$this->success($this->lang->line('cms_content_edit_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
		}else{
			$this->display('make', 'cms_content');
		}
	}
	
	/**
	 * 发布到网站显示
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function examine(){
		if($this->is_post()){
			$ids = $this->post('ids');
			//发布
			$this->db->where('status', '0')->where_in('id', $ids)->update('CmsContent', array('status'=>1));
			$this->success($this->lang->line('cms_content_exa_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 删除文章
	 * Create by 2012-8-1
	 * @author liuw
	 */
	function delete(){
		if($this->is_post()){
			$ids = $this->post('ids');
			//删除
			$this->db->where_in('id', $ids)->delete('CmsContent');
			$this->success($this->lang->line('cms_content_del_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 获得分类列表
	 * Create by 2012-8-2
	 * @author liuw
	 */
	function _get_categories(){
		$query = $this->db->order_by('cid', 'asc')->get('CmsCategory')->result_array();
		$list = array();
		foreach($query as $row){
			$list[$row['cid']] = $row['category'];
		}
		return $list;
	}
	
}  
   
 // File end