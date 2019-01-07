<?php
/**
 * 新网站推荐相关
 * Create by 2012-11-22
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class New_fragment extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		//导入模型
		$this->load->model('fragment_model', 'frag');
		//导入公共函数
		$this->load->helper('recommend_helper');
		$role_keys = $this->auth['role'];
		$roles = get_data("role");
		$newsRights = array();
		foreach($role_keys as $k=>$row){
			$newsRights = array_merge($newsRights,$roles[$row]['newsRights']);
		}
		$this->newsRight = array_unique(array_filter($newsRights));
		empty($this->newsRight) && ($this->newsRight = array(0));
		unset($newRights);
		
	}
	
	/**
	 * 推荐碎片管理
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param int $categoryId，分类ID，如果=0，查询全部碎片
	 * @param string keyword，搜索碎片的关键词
	 */
	function index(){
		$list = array();
		$categoryId = $this->post('categoryId');
		empty($categoryId) && $categoryId = 0;
		$keyword = $this->post('keyword');
		$this->assign(compact('categoryId', 'keyword'));
		empty($keyword) && $keyword = false;
		//查询碎片数量
		$count = $this->frag->count_frag($categoryId, $keyword);
		if($count){
			//分页属性
			$parr = $this->paginate($count);
			//查询碎片列表
			$list = $this->frag->search_frag($parr['per_page_num'], $parr['offset'], $categoryId, $keyword);
			foreach($list as $k=>&$frag){
				!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
				!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
			}
			unset($frag);
		}
		//频道列表
		$cates = get_categorys(0, 1 ,0);
		$this->assign('cats', $cates);
		$this->assign('list', $list);
		unset($cates, $list);
		$this->display('index', 'fragment');
	}
	
	/**
	 * 碎片详情
	 * Create by 2012-11-23
	 * @author liuweijava
	 */
	function info(){
		$fid = $this->get('fid');
		empty($fid) && $this->error($this->lang->line('rec_frag_no_fid'));
		$info = $this->frag->get_frag($fid);
		//扩展属性
		!empty($info['extraProperty']) && $info['extraProperty'] = json_decode($info['extraProperty'], true);
		if(!empty($info['extraProperty'])){
			$extras = array();
			foreach($info['extraProperty'] as $k=>$extra){
				$str = array();
				foreach($extra as $id=>$ext){
					$str[] = $id.'='.$ext;
				}
				$extras[] = implode('|', $str);
				unset($str);
			}
			$info['extra'] = implode("\n", $extras);
			unset($extras);
		}
		//碎片规则
		!empty($info['rule']) && $info['rule'] = json_decode($info['rule'], true);
		!empty($info['rule']['pic_size']) && $info['rule']['pic_size'] = implode("\n", $info['rule']['pic_size']);
		empty($info) && $this->error($this->lang->line('rec_frag_is_null'));
		$this->assign('info', $info);
		$this->display('info', 'fragment');
	}
	
	/**
	 * 查找带回-频道
	 * Create by 2012-11-23
	 * @author liuweijava
	 */
	function look_categories(){
		$fid = $this->get('fid');
		$page = $this->post('pageNum');
		empty($fid) && $fid = 0;
		//$list = get_categorys(0, -1);//全部频道列表
		$list = get_categorys(0, 1);
		if($fid){
			$checks = get_categorys($fid);
			$cls = array();
			if(!empty($checks)){
				foreach($checks as $k=>$cs){
					$cls[] = $cs['id'];
				}
			}
			foreach($list as $k=>&$r){
				if(in_array($r['id'], $cls))
					$r['checked'] = 1;
				else 
					$r['checked'] = 0;
				unset($r);
			}
		}
		
		
		
		$this->assign('parr', $parr);
		$this->assign('list', $list);
		$this->display('look_cat', 'fragment');
	}
	
	/**
	 * 关联频道和碎片
	 * Create by 2012-11-23
	 * @author liuweijava
	 */
	function link_frag_to_category(){
		$fid = $this->get('fid');
		empty($fid) && $this->error($this->lang->line('rec_freg_no_fid'));
		$frag = $this->frag->get_frag($fid);

		//频道列表
		$cat_list = get_categorys();
		if($fid){
			$checks = get_categorys($fid);
			//var_dump($cat_list,$checks);
			$cls = array();
			if(!empty($checks)){
				foreach($checks as $k=>$cs){
					$cls[] = $cs['id'];
				}
			}
			foreach($cat_list as $k=>&$r){
				if(in_array($r['id'], $cls))
					$r['checked'] = 1;
				else 
					$r['checked'] = 0;
				unset($r);
			}
		}
		
		$isUnlink = $this->get('is_unlink');
		empty($isUnlink) && $isUnlink = 0;
		$this->assign(compact('frag', 'cat_list', 'isUnlink'));
		
		
		if($this->is_post()){
			$cids = $this->post('cids');
			$isUnlink = $this->post('isUnlink');
			$flag = false;
			
			if(empty($isUnlink) || intval($isUnlink) == 0)
				$flag = true;
			
			$this->frag->update_category_frag($fid, $cids, $flag);	
			update_cache("web","data","fragmentdata",$fid);
			
			if(is_array($cids)){
			foreach($cids as $row){
				update_cache("web","inc","newscategory",$row);
			}
			}else{
				update_cache("web","inc","newscategory",$cids);
			}
			$msg = $isUnlink ? $this->lang->line('rec_frag_unlink_success') : $this->lang->line('rec_frag_link_success');
			$this->success($msg, $this->_index_rel, $this->_index_uri, 'closeCurrent');
		}else{
			$this->display('link_frag_cat', 'fragment');
		}
	}
	
	/**
	 * 添加碎片
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param int $categoryId
	 * @param array $_POST
	 */
	function add_frag(){
		//模板列表
		$tmps = frag_tmp_list();
		if($this->is_post()){
			$flag = $this->frag->make_frag();
			if(!$flag){
				//获取CID ...
				update_cache("web","inc","newscategory",0);
				update_cache("web","inc","fragment",0);
				$this->success($this->lang->line('rec_frag_make_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent'); /*$this->_index_uri*/
			}
			elseif($flag == 1)
				$this->error($this->lang->line('rec_frag_name_used'));
			elseif($flag == 2)
				$this->error($this->lang->line('do_error'));
		}else{
			$info = array();
			$this->assign(compact('tmps', 'info'));
			$this->display('make', 'fragment');
		}
	}
	
	/**
	 * 编辑碎片
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param int $fregmentId
	 * @param array $_POST
	 */
	function edit_frag(){
		$fid = $this->get('fid');
		empty($fid) && $this->error($this->lang->line('rec_freg_no_fid'));
		if($this->is_post()){
			$flag = $this->frag->make_frag();
			if(!$flag){
				update_cache("web","inc","fragment",0);
				update_cache("web","inc","newscategory",0);
				update_cache("web","data","fragmentdata",$fid);
				$this->success($this->lang->line('rec_frag_make_success'), $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
			elseif($flag == 1)
				$this->error($this->lang->line('rec_frag_name_used'));
			elseif($flag == 2)
				$this->error($this->lang->line('do_error'));
		}else{
			//模板列表
			$tmps = frag_tmp_list();
			$info = $this->frag->get_frag($fid);
			if(!empty($info['cates'])){
				$cids = $cnames = array();
				foreach($info['cates'] as $k=>$cate){
					$cids[] = $cate['id'];
					$cnames[] = $cate['catName'];
				}
				$info['cids'] = implode(',', $cids);
				$info['cnames'] = implode(',', $cnames);
				unset($cids, $cnames);
			}
			//扩展属性
			!empty($info['extraProperty']) && $info['extraProperty'] = json_decode($info['extraProperty'], true);
			if(!empty($info['extraProperty'])){
				$extras = array();
				foreach($info['extraProperty'] as $k=>$extra){
					$str = array();
					foreach($extra as $id=>$ext){
						$str[] = $id.'='.$ext;
					}
					$extras[] = implode('|', $str);
					unset($str);
				}
				$info['extra'] = implode("\n", $extras);
				unset($extras);
			}
			//碎片规则
			!empty($info['rule']) && $info['rule'] = json_decode($info['rule'], true);
		//	!empty($info['rule']['pic_size']) && $info['rule']['pic_size'] = implode("\n", $info['rule']['pic_size']);
			empty($info) && $this->error($this->lang->line('rec_frag_is_null'));
			$this->assign(compact('info', 'tmps'));
			$this->display('make', 'fragment');
		}
		
	}
	
	/**
	 * 删除碎片
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param mixed $fregmentIds
	 */
	function delete_frag(){
		if($this->is_post()){
			$this->frag->del_frags();
			$fids = $this->input->post('fids');
			foreach($fids as $row){
				update_cache("web","inc","fragment",$row);
				update_cache("web","data","fragmentdata",$row);
			}
			update_cache("web","inc","fragment",0);
			$this->success($this->lang->line('rec_frag_del_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}
	}
	
	/**
	 * 更新碎片数据
	 * Create by 2012-11-27
	 * @author liuweijava
	 */
	function update_frag(){
		$fid = $this->get('fid');
		update_cache("web","inc","fragment",$fid);
		flush_rec_data($fid) && $this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
	}
	
	/**
	 * 复制碎片
	 * Create by 2012-12-6
	 * @author liuweijava
	 */
	function copy_frag(){
		$fid = $this->get('fid');
		$code = $this->frag->clone_fragment($fid);
		$msg = '';
		switch($code){
			case 1:$msg = '碎片名重复了';break;
			case 2:$msg = '复制碎片失败了';break;
			case 0:$msg = '碎片复制成功';break;
		}
		if(!$code){
			update_cache("web","inc","fragment",$fid);
			$this->success($msg, $this->_index_rel, $this->_index_uri, 'forward');
		}
		else 
			$this->error($msg);
	}
	
	/**
	 * 查看碎片数据
	 * Create by 2012-12-4
	 * @author liuweijava
	 */
	function show(){
		$fid = $this->get('fid');
		if(empty($fid))
			die('请选择碎片');
		else{
			$cache_id = 'fragment_'.$fid;
			$cache = get_cache($cache_id);
			if(!isset($cache) || empty($cache)){
				$cache = $this->frag->flush_data($fid);
			}
			echo $cache;
			exit;
		}
	}
}   
   
 // File end