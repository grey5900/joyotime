<?php
/**
 * poi分类管理
 * Create by 2012-3-19
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Category extends MY_Controller{
	
	/**
	 * poi分类列表
	 * Create by 2012-3-19
	 * @author liuw
	 */
	public function index(){
		//查询当前已有分类
		$list = $this->listCate();
		$this->assign(compact('list'));
		$this->display('cat_index', 'poi');
	}
	
	/**
	 * 查询一个分类的详细信息
	 * Create by 2012-3-23
	 * @author liuw
	 */
	public function get_info(){
		$id = $this->get('id');
		$cate = $this->db->where('id',$id)->get('PlaceCategory')->first_row('array');
		if($cate['icon'] != null && !empty($cate['icon']))
			$cate['image_url'] = image_url($cate['icon'], 'common', 'odp');
		if($cate['parentId'] != 0){
			$p = $this->db->where('id',$cate['parentId'])->get('PlaceCategory')->first_row('array');
			$cate['pcontent'] = $p['content'];
			unset($p);
		}
		exit(json_encode($cate));
	}
	
	/**
	 * 创建poi分类
	 * Create by 2012-3-19
	 * @author liuw
	 */
	public function add(){
		if('POST' === $this->server('REQUEST_METHOD')){
			$content = $this->post('content');
			$parentId = $this->post('parentId');
			$orderValue = $this->post('orderValue');
			$orderValue = isset($orderValue) && !empty($orderValue) ? intval($orderValue) : 0;
			$level = $this->post('level');
			$icon = $this->post('icon');
			$icon = isset($icon) && !empty($icon) && $icon ? $icon[0] : '';
			//分类图标2
			$categoryIcon = $this->post('categoryIcon');
			$categoryIcon = !empty($categoryIcon)?$categoryIcon[0]:'';
			$isBrand = $this->post('isBrand');
			$isBrand = isset($isBrand)&&!empty($isBrand)?intval($isBrand):0;
			//检查分类名是否重复
			$result = $this->db->where('content',$content)->get('PlaceCategory')->first_row('array');
			if(isset($result) && !empty($result)){
				$this->error($this->lang->line('place_cate_name_to_repeat'), $this->_index_rel, $this->_index_uri, 'forward');
			}elseif(!empty($parentId) && $level <= 0){
				$this->error($this->lang->line('place_cate_level_error'), $this->_index_url, $this->_index_uri);
			}else{
				$data = compact('content','icon','orderValue','level','isBrand','categoryIcon');
				$this->db->insert('PlaceCategory', $data);
				$id = $this->db->insert_id();
				if(!isset($id) || !$id)
					$this->error($this->lang->line('do_error'), $this->_index_rel, $this->_index_uri, 'forward');
				else {
					//保存分类关联关系
					if(!isset($parentId) || empty($parentId)){
						$this->db->insert('PlaceCategoryShip', array('parent'=>0,'child'=>$id));
					}else{
						$sql = 'INSERT INTO PlaceCategoryShip (parent, child) VALUES ';
						$split = '';
						foreach($parentId as $pid){
							//检查上级分类的level是否小于新分类，不是的话不能保存关系
							$rs = $this->db->where('id', $pid)->get('PlaceCategory')->first_row('array');
							if($rs['level'] <= $level){
								$sql .= "{$split}({$pid},{$id})";
								$split = ',';
							}
						}
						$this->db->query($sql);
					}
                    // 更新缓存
                    api_update_cache('PlaceCategory', $id);
                    api_update_cache('PlaceOwnCategory');
					$this->success($this->lang->line('place_cate_add_success'), $this->_index_rel, $this->_index_uri, 'forward');
				}
			}
		}else{
			$pid = $this->get('id');
			if(isset($pid)&&!empty($pid))
				$this->assign('parent', intval($pid));
			$title = '添加分类';
			//分类列表
			$cates = $this->db->where('level',0,FALSE)->order_by('level', 'asc')->order_by('orderValue','desc')->get('PlaceCategory')->result_array();
			$post_url = site_url(array('poi','category','add'));
			$this->assign(compact('title','post_url','cates'));
			$this->display('cat_make','poi');
		}
	}
	
	/**
	 * 编辑poi分类
	 * Create by 2012-3-19
	 * @author liuw
	 */
	public function edit(){
		$id = $this->get('id');
		if(empty($id))
			$this->error('请选择一个分类');
		if($this->is_post()){
			$content = $this->post('content');
			$parentId = $this->post('parentId');
			$orderValue = $this->post('orderValue');
			$orderValue = isset($orderValue) && !empty($orderValue) ? intval($orderValue) : 0;
			$level = $this->post('level');
			$icon = $this->post('icon');
            $icon = $icon?array_filter($icon):array();
			$icon = $icon[0];
			//分类图标2
			$categoryIcon = $this->post('categoryIcon');
            $categoryIcon = $categoryIcon?array_filter($categoryIcon):array();
			$categoryIcon = $categoryIcon[0];
			$info = $this->db->where('id',$id)->get('PlaceCategory')->first_row('array');
			$isBrand = $this->post('isBrand');
			$isBrand = isset($isBrand)&&!empty($isBrand)?intval($isBrand):0;
			//检查分类名是否重复
			if($info['content'] != $content){
				$rs = $this->db->where('content',$content)->get('PlaceCategory')->first_row('array');
				if(isset($rs) && !empty($rs)){
					$this->error($this->lang->line('place_cate_name_to_repeat'), $this->_index_rel, $this->_index_uri, 'forward');
				}
			}
			if(!empty($parentId) && $level <= 0){
				$this->error($this->lang->line('place_cate_level_error'), $this->_index_url, $this->_index_uri);
			}
			//格式化修改数据
			$edit = array(
				'level'=>$level,
				'orderValue'=>$orderValue,
				'isBrand' => $isBrand,
			);
			if($icon != '' && $icon != $info['icon'])
				$edit['icon'] = $icon;
			if($info['content'] != $content)
				$edit['content'] = $content;
			if($categoryIcon != '' && $categoryIcon != $info['categoryIcon'])
				$edit['categoryIcon'] = $categoryIcon;
			$edit['lastUpdated'] = now();
			//修改数据
			$this->db->where('id', $id)->update('PlaceCategory', $edit);
			//删除旧的关系
			$this->db->delete('PlaceCategoryShip', array('child'=>$id));
			//添加新的关系
			if(!isset($parentId) || empty($parentId)){
				$this->db->insert('PlaceCategoryShip', array('parent'=>0,'child'=>$id));
			}else{
				$sql = 'INSERT INTO PlaceCategoryShip (parent, child) VALUES ';
				$split = '';
				foreach($parentId as $pid){
					//检查上级分类的level是否小于新分类，不是的话不能保存关系
					$rs = $this->db->where('id', $pid)->get('PlaceCategory')->first_row('array');
					if($rs['level'] <= $level){
						$sql .= "{$split}({$pid},{$id})";
						$split = ',';
					}
				}
				$this->db->query($sql);
			}
			//如果编辑分类为品牌分类，那么需要把其下所有子分类也修改为品牌分类，并取消其除与该分类外其他父分类的关系
			if($isBrand){
				//修改子分类的isBrand
				$query = $this->db->select('child')->where('parent',$id)->get('PlaceCategoryShip');
				foreach($query->result_array() as $row){
					//修改isBrand
					$this->db->where('id', $row['child'])->update('PlaceCategory', array('isBrand'=>1));
					//取消多余的关联关系
					$this->db->where('child',$row['child']);
					$this->db->where('parent<>',$id,FALSE);
					$this->db->delete('PlaceCategoryShip');
				}
			}
			// 更新缓存
            $rtn = api_update_cache('PlaceCategory', $id);
            api_update_cache('PlaceOwnCategory');
			$this->success($this->lang->line('place_cate_edit_success'), $this->_index_rel, $this->_index_uri, 'forward', array('p'=>$rtn));
		}else{
			$title = '编辑分类';
			$post_url = site_url(array('poi','category','edit','id',$id));
			$info = $this->db->where('id',$id)->get('PlaceCategory')->first_row('array');
			$cates = $this->db->where("id <> '{$id}' and level = 0")->order_by('level', 'asc')->order_by('orderValue','desc')->get('PlaceCategory')->result_array();
			//关联关系
			$slist = $this->db->select('parent')->where('child',$id)->get('PlaceCategoryShip');
			$ships = array();
			foreach($slist->result_array() as $ship){
				$ships[] = $ship['parent'];
			}
			$this->assign(compact('title','post_url','cates','info','ships'));
			$this->display('cat_make','poi');
		}
	}
	
	/**
	 * 删除poi分类
	 * Create by 2012-3-19
	 * @author liuw
	 */
	public function delete(){
		$id = $this->get('id');
		if(!isset($id)||empty($id))
			$this->error('请选择一个分类');
		if('POST' === $this->server('REQUEST_METHOD')){
			//分类属性
			$cat = $this->db->where('id',$id)->get('PlaceCategory')->first_row('array');
			//删除分类
			$this->db->delete('PlaceCategory', array('id'=>$id));
			//删除分类关联关系
			$this->db->delete('PlaceCategoryShip', array('child'=>$id));
			$this->db->where('parent',$id)->update('PlaceCategoryShip', array('parent'=>0));
			//将分类下的poi移动到上级分类
			$sql = 'UPDATE PlaceOwnCategory SET placeCategoryId=0 WHERE placeCategoryId='.$id;
			$this->db->query($sql);
            // 更新缓存
            api_update_cache('PlaceCategory');
            api_update_cache('PlaceOwnCategory');
			$this->success($this->lang->line('place_cate_delete_success'), $this->_index_rel, $this->_index_uri,'forward');
		}
	}
	
	/**
	 * 推荐分类
	 * Create by 2012-3-26
	 * @author liuw
	 */
	public function recommend(){
		if('POST' === $this->server('REQUEST_METHOD')){
			//先清理掉原来的推荐
			$this->db->where('recommendedOrder > ', 0, FALSE)->update('PlaceCategory', array('recommendedOrder'=>0));
			//再保存新的推荐
			$ids = $this->post('items_rec_id');
			$names = $this->post('items_rec_name');
			$orders = $this->post('items_order');
			//从名字判断更符合需求
			if(isset($names)&&!empty($names)){
				for($i=0;$i<count($names);$i++){
					$id = $ids[$i];
					if(isset($id)&&!empty($id))
						$this->db->where('id', $id)->update('PlaceCategory', array('recommendedOrder'=>$orders[$i]));
					else{
						$this->db->where('content', $names[$i])->update('PlaceCategory', array('recommendedOrder'=>$orders[$i]));
					}
				}
			}
            api_update_cache('PlaceCategory');
			$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
		}else{
			//查询当前推荐的分类
			$query = $this->db->where('recommendedOrder >',0,FALSE)->order_by('recommendedOrder', 'asc')->get('PlaceCategory');
			$list = array();
			foreach($query->result_array() as $row){
				$list[$row['id']] = $row;
			}
			$bringUrl = site_url(array('poi','category','view_tree'));
			$this->assign(compact('bringUrl','list'));
			$this->display('rec_category','poi');
		}
	}
	
	/**
	 * 显示分类树，给查找带回使用
	 * Create by 2012-3-26
	 * @author liuw
	 */
	public function view_tree(){
		$list = $this->listCate($this->get('is_brand'));
		$this->assign(compact('list'));
		$this->display('cat_tree','poi');
	}
	
	/**
	 * 获取分类的树状列表
	 * Create by 2012-3-26
	 * @author liuw
	 */
	private function listCate($is_brand=''){
	    if($is_brand === '1') {
	        $this->db->where('isBrand', 1);
	    } elseif($is_brand === '0') {
	        $this->db->where('isBrand', 0);
	    }
		$this->db->from('PlaceCategory');
		$this->db->join('PlaceCategoryShip', 'PlaceCategoryShip.child = PlaceCategory.id OR PlaceCategoryShip.parent = PlaceCategory.id','left');
		$this->db->order_by('PlaceCategory.level', 'asc')->order_by('orderValue', 'desc');
		$query = $this->db->get()->result_array();
		$list = array();
		foreach($query as $row){
// 			$row['tvalue'] = sprintf("{id:%s,name:\"%s\"}", $row['id'], $row['content']);
			if($row['level'] == 0) 
				$list[$row['id']] = $row;
			else 
				$list[$row['parent']]['children'][$row['id']] = $row;
		}
		
		return $list;
	}
	
}   
   
 // File end