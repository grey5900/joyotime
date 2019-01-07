<?php
/**
 * 简单的cms前台展示
 * Create by 2012-8-6
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Cms extends MY_Controller{
	
	function index(){
		
	}
	
	/**
	 * 搜索
	 * Create by 2012-8-6
	 * @author liuw
	 */
	function search($keyword='', $page=1){
		
		$list = array();
		//查询文章总数
		$url_arr = array();
		if(!empty($keyword)){
			$keyword = urldecode($keyword);
			$this->db->like('CmsContent.subject', $keyword)->or_like('CmsContent.content', $keyword)->or_like('CmsCategory.category', $keyword);
			$url_arr['keyword'] = $keyword;
		}
		$count = $this->db->join('CmsCategory', 'CmsCategory.cid=CmsContent.cid', 'left')->count_all_results('CmsContent');
		if($count){
			//分页
			$parr = paginate('/cms_search', $count, $page, $url_arr, 20, 7);
			//列表
			if(!empty($keyword)){
				$this->db->like('CmsContent.subject', $keyword)->or_like('CmsContent.content', $keyword)->or_like('CmsCategory.category', $keyword);
			}
			$query = $this->db->join('CmsCategory', 'CmsCategory.cid=CmsContent.cid', 'left')->select('CmsContent.*')->get('CmsContent')->result_array();
			foreach($query as $row){
				//转换日期
				$row['create'] = gmdate('Y年m月d日 H:i', strtotime($row['createDate']));
				$title = '['.$row['source'].'] '.$row['subject'];
				//切标题
				$title = cut_string($title, 48, '');
				//高亮关键词
				$title = str_replace($keyword, '<span style="font-size:16px;font-weight:bold;color:#f00;">'.$keyword.'</span>', $title);
				$row['title'] = $title;
				$list[] = $row;
			}
		}
		$this->assign('list', $list);
		$this->display('search');
	}
	
	/**
	 * 显示分类文章列表
	 * Create by 2012-8-6
	 * @author liuw
	 * @param int $cid，分类ID，不可为空
	 * @param string $source，来源
	 * @param int $page，当前页码，默认为1
	 * @param int $size，页长，默认为20条
	 */
	function list_news($cid, $source='', $page=1){
		
		//分类信息
		$cate = $this->db->where('cid', $cid)->get('CmsCategory')->first_row('array');
		$this->assign('cate', $cate);
		//文章总数
		if(!empty($source))
			$this->db->like('source', $source);
		$count = $this->db->where('cid', $cid)->where('status', $this->config->item('cms_post_tag'))->count_all_results('CmsContent');
		$list = array();
		if($count){
			//分页
			$url_arr = array('cid'=>$cid);
			if(!empty($source)){
				$url_arr['source'] = $source;
				$this->db->like('source', $source);
			}
			$parr = paginate('/cms_list/', $count, $page, $url_arr, 20, 7);
			//列表
			$query = $this->db->where('cid', $cid)->where('status', $this->config->item('cms_post_tag'))->order_by('createDate', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get('CmsContent')->result_array();
			foreach($query as $row){
				//转换日期
				$row['create'] = gmdate('Y年m月d日 H:i', strtotime($row['createDate']));
				//切标题
				$title = '['.$row['source'].'] '.$row['subject'];
				$row['title'] = cut_string($title, 48, '');
				$list[] = $row;
			}
		}
		$this->assign('list', $list);
		$this->display('default');
	}
	
	/**
	 * 显示文章内容
	 * Create by 2012-8-6
	 * @author liuw
	 * @param int $id，文章ID
	 */
	function detail($id){
		//查询文章
		$info = $this->db->where('CmsContent.id', $id)->where('CmsContent.status', $this->config->item('cms_post_tag'))->join('CmsCategory', 'CmsCategory.cid=CmsContent.cid')->get('CmsContent')->first_row('array');
		if(!isset($info)||empty($info))
			$this->showmesssage($this->lang->line('cms_writing_empty'), '/index');
		else{
			
			$this->assign('info', $info);
			//更新浏览统计数
			$this->db->query('UPDATE CmsContent SET viewCount=viewCount+1 WHERE id=?', array($id));
			//上一篇
			$arr = array($id, $info['cid']);
			$sql = 'SELECT id FROM CmsContent WHERE id<? AND cid=? ORDER BY createDate DESC LIMIT 1';
			$prev = $this->db->query($sql, $arr)->first_row('array');
			$this->assign('prev_id', !empty($prev)?$prev['id']:'over');
			//下一篇
			$sql = 'SELECT id FROM CmsContent WHERE id>? AND cid=? ORDER BY createDate ASC LIMIT 1';
			$next = $this->db->query($sql, $arr)->first_row('array');
			$this->assign('next_id', !empty($next)?$next['id']:'over');
			//TOP 10
			$tops = array();
			$query = $this->db->where('id != ', $id)->where('cid', $info['cid'])->order_by('viewCount', 'desc')->order_by('createDate', 'desc')->limit(10)->get('CmsContent')->result_array();
			foreach($query as $row){
				//转换日期
				$row['create'] = trim(substr($row['createDate'], 0, -8));
				//切标题
				$row['title'] = cut_string($row['subject'], 33, '');
				$tops[] = $row;
			}
			$this->assign('tops', $tops);
			
			$this->display('detail');
		}
	}
	
}   
   
 // File end