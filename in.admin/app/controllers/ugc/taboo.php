<?php
/**
 * 关键词管理
 * Create by 2012-3-22
 * @author liuw
 * @copyright Copyright(c) 2012-2014
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class Taboo extends MY_Controller{

	/**
	 * 列表显示关键词
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function index(){
		//载入敏感词类型列表
		$types = $this->config->item('taboo_types');
		//参数
		$type = $this->post('type');
		$type = isset($type)&&!empty($type)?$type:'';
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		$this->assign(compact('type','keyword'));
		//查询总数
		if($type !== '')
		$this->db->where("FIND_IN_SET('{$type}', types)");
		if($keyword !== '')
		$this->db->like('word', $keyword);
		$count = $this->db->count_all_results('Taboo');
		if($count){
			//分页
			$paginate = $this->paginate($count);
			//查询数据
			if($type !== '')
			$this->db->where("FIND_IN_SET('{$type}', types)");
			if($keyword !== '')
			$this->db->like('word', $keyword);
			$this->db->order_by('id', 'desc');
			$this->db->limit($paginate['per_page_num'], $paginate['offset']);
			$query = $this->db->get('Taboo');
			$list = array();
			foreach($query->result_array() as $row){
				$t_item = explode(',',$row['types']);
				$txt_type = $split = '';
				foreach($t_item as $t){
					$txt_type .= $split.$types[$t];
					$split = ' & ';
				}
				$row['types'] = $txt_type;
				$list[$row['id']] = $row;
			}
			$this->assign('list', $list);
		}else{
			$this->assign(array('total_num'=>0, 'cur_page'=>1));
		}
		$this->assign('types',$types);
		$this->display('taboo','ugc');
	}

	/**
	 * 添加新关键词
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function add(){
		$types = $this->config->item('taboo_types');
		if($this->is_post()){
			$content = trim($this->post('taboos'));
			$taboos = explode("\n", $content);
			$types = $this->post('types');
			$type = implode(',', $types);
			
			$replace = $this->insert_taboo($taboos, $type);
			api_update_cache($this->_tables['taboo']);
			
			//2013.9.27 zr
			/*写入redis*/
			get_redis('taboo','user',true); 
			get_redis('taboo','post',true); 
			/*写入redis*/
			$this->success(formart_tag_msg($this->lang->line('taboo_import_success'), $replace), $this->_index_rel, $this->_index_uri, 'closeCurrent');
		}
		$this->assign('types',$types);
		$this->assign('do','add');
		$this->display('taboo','ugc');
	}

	/**
	 * 编辑关键词
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function edit(){
		$types = $this->config->item('taboo_types');
		$id = $this->get('ids');
		if(!isset($id) || empty($id))
		$this->error($this->lang->line('taboo_check_faild'));
		$id = str_replace('|',',',urldecode($id));
		if($this->is_post()){
			$ids = $this->post('ids');
			$types = $this->post('types');
			$new_type = implode(',', $types);
			$this->db->where("FIND_IN_SET(id, '{$ids}')")->update('Taboo', array('types'=>$new_type));
			//更新缓存
			get_data('taboo', TRUE);
			//更新memcache
			get_data('public_taboo', TRUE);
			api_update_cache($this->_tables['taboo']);
			
			//2013.9.27 zr
			/*写入redis*/
			get_redis('taboo','user',true);
			get_redis('taboo','post',true);
			/*写入redis*/
			$this->success($this->lang->line('do_success'));
		}else{
			//敏感词属性
			$this->assign('info',$info);
				
			$this->assign('types',$types);
			$this->assign('ids', $id);
			$this->assign('do','edit');
			$this->display('taboo','ugc');
		}
	}

	/**
	 * 批量导入
	 * Create by 2012-3-30
	 * @author liuw
	 */
	public function auto_import(){
		$types = $this->config->item('taboo_types');
		if($this->is_post()){
			$cfg = $this->config->item('taboo_file');
			$this->load->library('upload', $cfg);
			if(!$this->upload->do_upload('taboo_file')){
				$this->error('文件解析失败', $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}else{
				$data = $this->upload->data();
				if(file_exists($data['full_path'])){
					//读取内容
					$file = @fopen($data['full_path'],'r');
					$content = @fread($file, filesize($data['full_path']));
					@fclose($file);
					//删除文件
					@unlink($data['full_path']);
					unset($data);
				}
				//解析
				$taboos = explode("\r\n", $content);
				//类型
				$types = $this->post('types');
				$type = implode(',', $types);
				//保存敏感词

				$replace = $this->insert_taboo($taboos, $type);
				$this->success(formart_tag_msg($this->lang->line('taboo_import_success'), $replace), $this->_index_rel, $this->_index_uri, 'closeCurrent');
			}
		}
		$this->assign('types',$types);
		$this->assign('do','import');
		$this->display('taboo','ugc');
	}

	/**
	 * 删除关键词
	 * Create by 2012-3-22
	 * @author liuw
	 */
	public function delete(){
		if($this->is_post()){
			$ids = $this->post('ids');
			$this->db->where_in('id',$ids)->delete('Taboo');
			//更新缓存
			get_data('taboo', TRUE);
			//更新memcache
			get_data('public_taboo', TRUE);
			$this->success($this->lang->line('do_success'));
		}
	}

	/**
	 * 获取敏感词列表
	 * Create by 2012-5-3
	 * @author liuw
	 * @param string $key，敏感词类型，默认为空并返回所有敏感词
	 * @return array
	 */
	public function get_taboo($key=FALSE){
		$caches = get_data('taboo', FALSE);
		return !$key ? $caches : $caches[$key];
	}

	private function insert_taboo($taboos, $type){
		//检查并格式化
		$datas = array();
		//STEP1 统计本次要增加的数量
		$total_num = $suc_num = $fail_num = 0;
		foreach($taboos as $key=>$value){
			$value = str_replace("\r",'',$value);
			//检查是否已存在
			if(!empty($value) && $value != ''){
				if(strlen($value) <= 20){
					$count = $this->db->where(array('word'=>$value,'types'=>$type))->count_all_results('Taboo');
					if(!isset($count) || $count<=0){
						$data = array(
								'word' => $value,
								'types' => $type
						);
						$datas[] = $data;
					}
				}
				$total_num+=1;
			}
		}
		if(!empty($datas)){
			//STEP2 获得添加前数据库中敏感词总数
			$old_count = $this->db->count_all_results('Taboo');
			$this->db->insert_batch('Taboo', $datas);
			//STEP3 获得添加后数据库中敏感词总数
			$now_count = $this->db->count_all_results('Taboo');
			//STEP4 计算本次操作成功添加的敏感词数量
			$suc_num = $now_count - $old_count;
		}
		//STEP5 计算本次操作添加失败的敏感词数量
		$fail_num = $total_num - $suc_num;
		$replace = compact('total_num','suc_num','fail_num');
		//更新缓存
		get_data('taboo', TRUE);
		//更新memcache
		get_data('public_taboo', TRUE);
		return $replace;
	}

}
// File end