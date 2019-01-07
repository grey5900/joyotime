<?php
/**
 * 扩展管理
 * Create by 2012-9-26
 * @author liuw
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Profile extends MY_Controller{
	
	/**
	 * 新建扩展
	 * Create by 2012-10-10
	 * @author liuw
	 * @return 
	 */
	function index(){
		//模型列表
		$modules = $this->_get_modules();
		//已添加的碎片要排除
	/*	$query = $this->db->where('brandId', $this->auth['brand_id'])->select('moduleId')->get('ApplyPlaceSpecialProperty')->result_array();
		$checkeds = array();
		foreach($query as $row){
			$checkeds[] = $row['moduleId'];
		}
		foreach($modules as $k=>$v){
			if(in_array($k, $checkeds))
				unset($modules[$k]);
		}*/
		$places = $this->_get_places();
		$this->assign(compact('modules', 'places'));
		if($this->is_post()){
			$this->_make_datas();
		}else{
			$this->assign('active', 'profile');
			$this->assign('menu', 'add');
			$this->assign('info', null);
			$this->display('index');
		}
	}
	
	/**
	 * 删除还没有上传到图片服务器的封面图片
	 * Create by 2012-10-16
	 * @author liuw
	 * @param string $cover_name
	 */
	function del_cover($cover_name){
		$cfg = $this->config->item('image_cfg');
    	$upload_path = $cfg['upload_path'];
    	$del_file = rtrim($upload_path,'/') . '/' . $cover_name;
    	@unlink($del_file);
    	$this->echo_json(array('code'=>1));
	}
	
	/**
	 * 编辑扩展
	 * Create by 2012-10-10
	 * @author liuw
	 * @param int $app_id
	 * @return 
	 */
	function edit($app_id){
		$info = $this->db->where(array('id'=>$app_id, 'status < '=>20))->get('ApplyPlaceSpecialProperty')->first_row('array');
		empty($info) && $this->showmessage('申请不存在或已删除或已通过审核', '/profile/checks');
		$info['moduleId'] == -1 && $info['moduleId'] = 0;
		//模型列表
		$modules = $this->_get_modules();
		($info['moduleId'] == 0 || !empty($modules[$info['moduleId']])) && $flag = true;
		if(!$flag)
			$this->assign(array('error'=>1, 'msg'=>'您的权限不够，无法操作该扩展模型。请联系管理员'));
		else{
			$sel_mod = $info['moduleId'] ? $modules[$info['moduleId']]['name'] : '链接到页面';
			$this->assign('sel_mod', $sel_mod);
			//封面图片
			if(!empty($info['images'])){
				$covers = array();
				$cs = explode(',', $info['images']);
				if(count($cs) > 1){
					foreach($cs as $img){
						$covers[] = image_url($img, 'common', 'odp');
					}
				}else{
					$covers = image_url($cs[0], 'common', 'odp');
				}
				$info['covers'] = $covers;
			}
			$title = '正在编辑商家['.$this->auth['brand_name'].']的扩展模型['.$sel_mod.']';
			$this->session->set_flashdata('info', $info);
			$this->assign(compact('modules', 'info', 'title'));
		}
		if($this->is_post()){
			$this->_make_datas($app_id);
		}else{
			$this->assign('active', 'profile');
			$this->assign('menu', 'edit');
			$this->display('edit');
		}
	}
	
	/**
	 * 编辑指定地点的碎片，需要在相关申请表中增加新记录
	 * Create by 2012-10-23
	 * @author liuw
	 * @param int $id
	 */
	function edit_publish($id){
		//查询数据
		$info = $this->db->where(array('id'=>$id))->get('PlaceOwnSpecialProperty')->first_row('array');
		$p = $this->db->select('placename')->where('id', $info['placeId'])->limit(1)->get('Place')->first_row('array');
		empty($info) && $this->showmessage('碎片不存在或已删除', '/profile/publish');
		$info['moduleId'] < 0 && $info['moduleId'] = 0;
		//模型列表
		if(!$flag)
			$this->assign(array('error'=>1, 'msg'=>'您的权限不够，无法操作该扩展模型。请联系管理员'));
		else{
			$modules = $this->_get_modules();
			$sel_mod = $info['moduleId'] ? $modules[$info['moduleId']]['name'] : '链接到页面';
			$this->assign('sel_mod', $sel_mod);
			$this->assign('title', '正在编辑地点['.$p['placename'].']的扩展模型['.$sel_mod.']');
			//封面图片
			if(!empty($info['images'])){
				$covers = array();
				$cs = explode(',', $info['images']);
				foreach($cs as $img){
					$covers[] = image_url($img, 'common', 'odp');
				}
				$info['covers'] = $covers;
			}
			$this->session->set_flashdata('info', $info);
			$this->session->set_flashdata('edit_publish', 1);
			$this->assign('edit_publish', 1);
			$this->assign(compact('modules', 'info'));
			$this->assign('edit_publish', 1);
		}
		if($this->is_post()){
			$this->_make_datas(0, $info['placeId']);
		}else{
			$this->assign('active', 'profile');
			$this->assign('menu', 'edit');
			$this->display('edit');
		}
	}
	
	/**
	 * 删除扩展
	 * Create by 2012-10-10
	 * @author liuw
	 * @param int $app_id
	 * @return 
	 */
	function delete(){
		if(!$this->is_post())
			$this->echo_json(array('code'=>1, 'msg'=>'非法请求'));
		$id = $this->post('id');
		//清楚地点关联
		$this->db->where('applyPropertyId', $id)->delete('ApplyPropertyAtPlace');
		//删除扩展属性数据
		$this->db->where('applyPropertyId', $id)->delete('ApplyPlaceModuleData');
		//删除扩展属性主表数据
		$this->db->where('id', $id)->delete('ApplyPlaceSpecialProperty');
		$this->echo_json(array('code'=>0, 'msg'=>'操作已完成，扩展属性已完全删除'));
	}
	
	/**
	 * 待审核列表
	 * Create by 2012-10-10
	 * @author liuw
	 * @param int $page
	 */
	function checks($page=1){
		$brandId = $this->auth['brand_id'];
		$list = array();
		//查询总数
		$count = $this->db->where(array('brandId'=>$brandId, 'status < '=>20))->count_all_results('ApplyPlaceSpecialProperty');
		if($count){
			//分页
			$parr = paginate('/profile/checks', $count, $page);
			//列表
			$query = $this->db->where(array('brandId'=>$brandId, 'status < '=>20))->order_by('createDate', 'desc')->limit($parr['size'], $parr['offset'])->get('ApplyPlaceSpecialProperty')->result_array();
			foreach($query as $row){
				if(!empty($row['images'])){
					switch($row['style']){
						case 2:
							$imgs = explode(',', $row['images']);
							foreach($imgs as $img){
								$row['imgs'][] = image_url($img, 'common', 'odp');
							}
							break;
						default:
							$row['imgs'] = image_url($row['images'], 'common', 'odp');
							break;
					}
					$row['has_img'] = 1;
				}else 
					$row['has_img'] = 0;
				$row['createDate'] = gmdate('Y/m-d H:i', strtotime($row['createDate'])+8*3600);
				//适用商家数量
				$row['places'] = $this->db->where('applyPropertyId', $row['id'])->count_all_results('ApplyPropertyAtPlace');
				$list[] = $row;
			}
		}
		$this->assign('list', $list);
		$this->assign('active', 'profile');
		$this->assign('menu', 'check');
		$this->display('check');
	}
	
	/**
	 * 已审核列表
	 * Create by 2012-10-10
	 * @author liuw
	 * @param int $place_id
	 * @param int $page
	 */
	function publish($place_id='', $page=1){
		$brandId = $this->auth['brand_id'];
		//全部店铺
		$places = $this->_get_places();
		foreach($places as &$p){
			!empty($place_id) && $p['id'] == $place_id && $p['selected'] = 1;
			unset($p);
		}
		
		$where = $list = array();
		
		if($place_id){
			//查询指定地点关联的碎片总数
			$count = $this->db->where('placeId', $place_id)->count_all_results('PlaceOwnSpecialProperty');
		//	echo $this->db->last_query().'<p/>';
			if($count){
				//分页
				$parr = paginate('/profile/publish', $count, $page, array($place_id));
				//查询数据
				$query = $this->db->where('placeId', $place_id)->order_by('rankOrder', 'desc')->limit($parr['size'], $parr['offset'])->get('PlaceOwnSpecialProperty')->result_array();
			//	echo $this->db->last_query();
				foreach($query as $row){
					//审核时间
					$row['createDate'] = substr($row['createDate'], 0, -9);
					//图片
					if(!empty($row['images'])){
						$row['has_img'] = 1;
						switch($row['style']){
							case 2:
								$imgs = explode(',', $row['images']);
								foreach($imgs as $img){
									$row['imgs'][] = image_url($img, 'common', 'odp');
								}
								break;
							default:
								$row['imgs'] = image_url($row['images'], 'common', 'odp');
						}
					}else 
						$row['has_img'] = 0;
					$list[$row['id']] = $row;
				}
			}
		}
		$this->assign(compact('places', 'list'));
		
		$this->assign('active', 'profile');
		$this->assign('menu', 'publish');
		$this->display('publish');
	}
	
	/**
	 * 删除已通过的碎片
	 * Create by 2012-10-23
	 * @author liuw
	 * @param int $id
	 */
	function del_publish(){
		if(!$this->is_post())
			$this->echo_json(array('code'=>1, 'msg'=>'非法请求'));			
		$id = $this->post('id');
		$query = $this->db->where('id', $id)->limit(1)->get('PlaceOwnSpecialProperty')->first_row('array');
		//PlaceOwnSpecialProperty
		$this->db->where(array('id'=>$id))->delete('PlaceOwnSpecialProperty');
		//PlaceModuleData
		$this->db->where(array('placeId'=>$query['placeId'], 'moduleId'=>$query['moduleId']))->delete('PlaceModuleData');
		//删除申请数据
		$brandId = $this->auth['brand_id'];
		$moduleId = $query['moduleId'];
		$placeId = $query['placeId'];
		//查询模型+品牌的申请ID
		$query = $this->db->select('id')->where(array('moduleId'=>$moduleId, 'brandId'=>$brandId))->get('ApplyPlaceSpecialProperty')->result_array();
		foreach($query as $row){
			$applyPropertyId = $row['id'];
			//检查关联地点
			$count = $this->db->where('applyPropertyId', $applyPropertyId)->count_all_results('ApplyPropertyAtPlace');
			$this->db->where(compact('applyPropertyId', 'placeId'))->delete('ApplyPropertyAtPlace');//删除关联地点
			if($count == 1){//只有一个关联地点的，删除地点关联数据、申请主数据及字段内容数据
				$this->db->where('id', $applyPropertyId)->delete('ApplyPlaceSpecialProperty');//删除主数据
				$this->db->where('applyPropertyId', $applyPropertyId)->delete('ApplyPlaceModuleData');//删除字段内容数据
			}
		}
		$this->echo_json(array('code'=>0, 'msg'=>'操作已完成，扩展属性已完全删除'));
	}
	
	/**
	 * 添加/编辑扩展信息的步骤
	 * Create by 2012-10-15
	 * @author liuw
	 * @param int $mid
	 * @param int $step
	 */
	function make_step($mid, $publish=0){
		//获取闪存数据
		$info = $this->session->flashdata('info');
		$flag = $this->session->flashdata('edit_publish');
		isset($flag) && !empty($flag) && $this->assign('edit_publish', $flag);
		
		$places = $this->_get_places();
		$s_plist = array();
		if(!empty($info)){
			//封面图片
			if(!empty($info['images'])){
				$covers = array();
				$cs = explode(',', $info['images']);
				if(count($cs) > 1){
					foreach($cs as $img){
						$covers[] = image_url($img, 'common', 'odp');
					}
				}else{
					$covers = image_url($cs[0], 'common', 'odp');
				}
				$info['covers'] = $covers;
			}
			//关联的地点ID
			$query = $this->db->where('applyPropertyId', $info['id'])->select('placeId')->get('ApplyPropertyAtPlace')->result_array();
			foreach($query as $row){
				$s_plist[] = $row['placeId'];
			}
			$this->assign('info', $info);
		}else{//检查是否已经有关联此模型的地点，需要排除掉
			//检查是否创建过这个模型
			$marr = array($mid);
			if($mid > 0)
				$marr[] = -1;
			$this->db->select('ApplyPropertyAtPlace.placeId');
			$this->db->join('ApplyPlaceSpecialProperty', 'ApplyPlaceSpecialProperty.id = ApplyPropertyAtPlace.applyPropertyId', 'inner');
			$this->db->where('ApplyPlaceSpecialProperty.brandId', $this->auth['brand_id']);
			$query = $this->db->where_in('moduleId',$marr)->get('ApplyPropertyAtPlace')->result_array();
			if(!empty($query)){//创建过的要排除已关联的地点
				$sels = array();
				foreach($query as $row){
					$sels[] = $row['placeId'];
				}
				foreach($places as $k=>$v)
					if(in_array($v['id'], $sels))
						unset($places[$k]);
				$s_plist = $sels;
			}
		}
		$this->assign('list', $places);
		$this->assign('sels', $s_plist);
		if(!$mid){
			$this->display('make_places');
		}else{
			$modules = $this->_get_modules();
			$mod = $modules[$mid];
			if(!empty($info)){
				$fs = $mod['fieldes'];
				$fields = array();
				foreach($fs as $k=>$v){
					$fields[$v['fieldId']] = $v;
				}
				$a_fields = array();
				if($publish <= 0){
					$query = $this->db->where(array('applyPropertyId'=>$info['id'], 'moduleId'=>$info['moduleId']))->get('ApplyPlaceModuleData')->result_array();
				}else{
					$query = $this->db->where(array('placeId'=>$info['placeId'], 'moduleId'=>$info['moduleId']))->get('PlaceModuleData')->result_array();
				}
				foreach($query as $row){
					$a_fields[$row['fieldId']] = $row;
				}
				foreach($fields as $fid=>&$field){
					if(in_array($fid, array_keys($a_fields))){
						$row = $a_fields[$fid];
						$field['selected'] = 1;
						$field['app_value'] = $row['mValue'];
						if($field['fieldType'] === 'image'){//单张图片
							$field['img'] = image_url($row['mValue'], 'common', 'odp');
						}elseif($field['fieldType'] === 'rich_image'){//多图
							$list = array();
							if(!empty($row['mValue'])){
								$rich = json_decode($row['mValue'], true);
								foreach($rich as $k=>$r){
									$r['img'] = image_url($r['image'], 'common', 'odp');
									$list[$k] = $r;
								}
							}
							$field['riches'] = $list;
						}
					}
					unset($field);
				}
				$mod['fieldes'] = $fields;
			}
				
			$fields = $mod['fieldes'];
			$this->assign('fields', $fields);
			$this->assign('module', $mod);		
			$this->display('make_input');
		}
	}
	
	/**
	 * 获取商家关联的扩展模型列表
	 * Create by 2012-10-10
	 * @author liuw
	 * @return array
	 */
	function _get_modules(){
		$brand_id = $this->auth['brand_id'];
		$modules = array();
		//模型基本信息
		$query = $this->db->join('BrandOwnModule', 'BrandOwnModule.moduleId = PlaceModule.id', 'inner')->where('BrandOwnModule.brandId', $brand_id)->order_by('PlaceModule.id', 'asc')->get('PlaceModule')->result_array();
		foreach($query as $row){
			//模型字段列表
			$row['fieldes'] = $this->db->where('moduleId', $row['id'])->order_by('orderValue', 'asc')->get('PlaceModuleField')->result_array();
			$modules[$row['id']] = $row;
		}
		return $modules;
	}
	
	/**
	 * 编辑扩展模型数据
	 * Create by 2012-10-10
	 * @author liuw
	 * @param mixed $editId，如果不为假，则表示修改模型数据，否则表示添加新模型数据
	 * @param int $place_id,是否修改单个地点的扩展信息，如果是那么这个参数是地点ID
	 * @return array,code:0=操作成功，1=保存数据失败，2=未选择模型，3=扩展信息标题为空，4=未关联地点，5=批量图片的标题或说明有为空的
	 */
	function _make_datas($editId=0, $place_id=0){
		//模型列表
		$modules = $this->_get_modules();
		$brandId = $this->auth['brand_id'];
    	$cfg = $this->config->item('image_cfg');
    	$upload_path = $cfg['upload_path'];
    	$upload_path = rtrim($upload_path,'/') . '/';
    //	$this->echo_json(array('code'=>1, 'msg'=>$_POST));
		if(!$editId){//添加新扩展信息
			if(!$place_id){
				$pids = $this->post('places');
				(!isset($pids) || empty($pids)) && $this->echo_json(array('code'=>4, 'msg'=>$this->lang->line('module_place_empty')));
			}
			$moduleId = $this->post('moduleId');
	//		empty($moduleId) && empty($place_id) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('module_add_id_empty')));
			$moduleId = $moduleId === '无' ? 0 : intval($moduleId);
			$unlinked = $this->post('unlinked');
			$unlinked = $unlinked === '无链接' ? true : false;
			if(!$moduleId){//外链
				$hyperLink = $this->post('hyperLink');
				$title = $this->post('title');
				$content = $this->post('content');
				$covers = $this->post('covers');
				if(empty($title) && empty($content) && empty($covers))
					$this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('module_add_title_empty')));
				else{
					empty($title) && $title = '';
					empty($content) && $content = '';
					empty($covers) && $covers = '';
				}
				//样式
				$style = $this->post('style');
				if(empty($style)) $style = 2;
				else $style = intval($style) - 1;
				$data = compact('moduleId', 'brandId', 'title', 'hyperLink', 'style', 'content');
				!empty($content) && $data['content'] = $content;
				if($unlinked){
					$data['hyperLink'] = '';
					$data['moduleId'] = 0;
				}else{
					$data['moduleId'] = -1;
				}
				//封面图片
				if(!empty($covers)){
					count($covers) > 4 && $this->echo_json(array('code'=>3, 'msg'=>'封面图片最多只能4张'));
					$data['images'] = implode(',', $covers);
				}
				//保存数据
				$this->db->insert('ApplyPlaceSpecialProperty', $data);
				$applyPropertyId = $this->db->insert_id();
				if(!$applyPropertyId)
					$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('module_add_faild')));
				else{
					//关联地点
					if(!$place_id){
						$places = array();
						foreach($pids as $k=>$placeId){
							$pd = compact('applyPropertyId', 'placeId');
							$places[] = $pd;
						}
						!empty($places) && $this->db->insert_batch('ApplyPropertyAtPlace', $places);
					}else{
						$this->db->insert('ApplyPropertyAtPlace', array('applyPropertyId'=>$applyPropertyId, 'placeId'=>$place_id));
					}
					$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_success'), 'refer'=>'/profile/checks'));
				}
			}else{//自定义
				$module = $modules[$moduleId];
				$flist = array();
				$f_list = $module['fieldes'];
				foreach($f_list as $k=>$v){
					$flist[$v['fieldId']] = $v;
				}
				//ApplyPlaceSpecialProperty数据
				$title = $this->post('title');
				$content = $this->post('content');
				$covers = $this->post('covers');
				if(empty($title) && empty($content) && empty($covers))
					$this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('module_add_title_empty')));
				else{
					empty($title) && $title = '';
					empty($content) && $content = '';
					empty($covers) && $covers = '';
				}
				//样式
				$style = $this->post('style');
				if(empty($style)) $style = 2;
				else $style = intval($style) - 1;
				$data = compact('moduleId', 'brandId', 'title', 'style');
				!empty($content) && $data['content'] = $content;
				//封面图片
				if(!empty($covers)){
					count($covers) > 4 && $this->echo_json(array('code'=>3, 'msg'=>'封面图片最多只能4张'));
					$data['images'] = implode(',', $covers);
				}
				
				//保存ApplyPlaceSpecialProperty数据
				$this->db->insert('ApplyPlaceSpecialProperty', $data);
				$applyPropertyId = $this->db->insert_id();
				if(!$applyPropertyId)
					$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('module_add_faild')));
				//字段值
				$fchecks = $this->post('fieldIds');
				if(!empty($fchecks)){
					$datas = array();
				//	$flag = 0;
					foreach($fchecks as $k=>$fieldId){
						$field = $flist[$fieldId];
						$mValue = '';
						switch($field['fieldType']){
							case 'rich_image'://图片
								$images = $this->post($fieldId.'_imgs');
								$titles = $this->post($fieldId.'_titles');
								$detailes = $this->post($fieldId.'_ds');
								$mds = array();
								if(!empty($images)){
									for($i=0;$i<count($images);$i++){
										$t = $titles[$i];
										$d = $detailes[$i];
							//			(empty($t) || empty($d)) && $flag += 1;
							//			if(!empty($t) && !empty($d))
											$mds[] = array('title'=>$t, 'detail'=>$d, 'image'=>$images[$i]);
									}
									
									$mValue = json_encode($mds);
								}
								break;
							case 'checkbox':
								$vs = $this->post($fieldId.'_checks');
								!empty($vs) && $mValue = implode(',', $vs);
								break;
							case 'datetime':
							case 'time':
								$data = $this->post($fieldId).':00';
								!empty($data) && $mValue = $data;
								break;
							default:
								$data = $this->post($fieldId);
								!empty($data) && $mValue = $data;
								break;
						}
						!empty($mValue) && $datas[] = compact('fieldId', 'applyPropertyId', 'moduleId', 'mValue');
					}
				//	$flag > 0 && $this->echo_json(array('code'=>5, 'msg'=>'请为批量上传的每张图片填写标题和描述'));
					//保存数据
					$this->db->insert_batch('ApplyPlaceModuleData', $datas);
				}
				//关联地点
				if(!$place_id){
					$places = array();
					foreach($pids as $k=>$placeId){
						$pd = compact('applyPropertyId', 'placeId');
						$places[] = $pd;
					}
					!empty($places) && $this->db->insert_batch('ApplyPropertyAtPlace', $places);
				}else{
					$this->db->insert('ApplyPropertyAtPlace', array('applyPropertyId'=>$applyPropertyId, 'placeId'=>$place_id));
				}
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_success'), 'refer'=>'/profile/checks'));
			}			
		}else{//修改扩展数据
			$edit = $this->db->where('id', $editId)->get('ApplyPlaceSpecialProperty')->first_row('array');
			$createDate = gmdate('Y-m-d H:i:s', time()+8*3600);
			$status = 0;
			if($edit['status'] == 20){//编辑已审核通过的
				$this->_make_data();
			}else{//编辑待审核和被驳回的
				$pids = $this->post('places');
				(!isset($pids) || empty($pids)) && $this->echo_json(array('code'=>4, 'msg'=>$this->lang->line('module_place_empty')));
				$moduleId = $edit['moduleId'];
		//		empty($moduleId) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('module_add_id_empty')));
				$moduleId = $moduleId === '无' ? 0 : $moduleId;
				$unlinked = $this->post('unlinked');
				$unlinked = $unlinked === '无链接' ? true : false;
				if(!$moduleId){//外链
					$hyperLink = $this->post('hyperLink');
					$title = $this->post('title');
					$content = $this->post('content');
					//封面图片
					$covers = $this->post('covers');
					
					empty($title) && empty($content) && empty($cover) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('module_add_title_empty')));
					//样式
					$style = $this->post('style');
					if(empty($style)) $style = 2;
					else $style = intval($style) - 1;
					$data = compact('moduleId', 'brandId', 'title', 'hyperLink', 'style', 'status', 'createDate');
					!empty($content) && $data['content'] = $content;
					if($unlinked){
						$data['hyperLink'] = '';
						$data['moduleId'] = 0;
					}else{
						$data['moduleId'] = -1;
					}
					!empty($covers) && count($covers) > 4 && $this->echo_json(array('code'=>3, 'msg'=>'封面图片最多只能4张'));
					if(!empty($covers)){
						$data['images'] = implode(',', $covers);
					}
					//保存数据
					$id = $this->db->where('id', $editId)->update('ApplyPlaceSpecialProperty', $data);
					if(!$id)
						$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('modulel_edit_faild')));
					else{
					//关联地点
						$applyPropertyId = $editId;
						$this->db->where('applyPropertyId', $applyPropertyId)->delete('ApplyPropertyAtPlace');
						$places = array();
						foreach($pids as $k=>$placeId){
							$places[] = compact('applyPropertyId', 'placeId');
						}
						$this->db->insert_batch('ApplyPropertyAtPlace', $places);
						$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_success'), 'refer'=>'/profile/checks'));
					}
				}else{//自定义
					$module = $modules[$moduleId];
					$flist = array();
					$f_list = $module['fieldes'];
					foreach($f_list as $k=>$v){
						$flist[$v['fieldId']] = $v;
					}
					//ApplyPlaceSpecialProperty数据
					$title = $this->post('title');
					$content = $this->post('content');
					//封面图片
					$covers = $this->post('covers');
					
					empty($title) && empty($content) && empty($cover) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('module_add_title_empty')));
					//样式
					$style = $this->post('style');
					if(empty($style)) $style = 2;
					else $style = intval($style) - 1;
					$data = compact('moduleId', 'brandId', 'title', 'style', 'status', 'createDate');
					!empty($content) && $data['content'] = $content;
					!empty($covers) && count($covers) > 4 && $this->echo_json(array('code'=>3, 'msg'=>'封面图片最多只能4张'));
					if(!empty($covers)){
						$data['images'] = implode(',', $covers);
					}
					
					//保存ApplyPlaceSpecialProperty数据
					$applyPropertyId = $editId;
					$this->db->where('id', $editId)->update('ApplyPlaceSpecialProperty', $data);
					//字段值
					$fchecks = $this->post('fieldIds');
						
					if(!empty($fchecks)){
						$datas = array();
						foreach($fchecks as $k=>$fieldId){
							$field = $flist[$fieldId];
							$mValue = '';
							//先清理旧数据
							$this->db->where(compact('fieldId', 'applyPropertyId', 'moduleId'))->delete('ApplyPlaceModuleData');
							switch($field['fieldType']){
								case 'rich_image':
									$images = $this->post($fieldId.'_imgs');
									$titles = $this->post($fieldId.'_titles');
									$detailes = $this->post($fieldId.'_ds');
									$mds = array();
									if(!empty($images)){
										for($i=0;$i<count($images);$i++){
											$t = $titles[$i];
											$d = $detailes[$i];
											$mds[] = array('title'=>$t, 'detail'=>$d, 'image'=>$images[$i]);
										}
										
										$mValue = json_encode($mds);
									}
									break;
								case 'checkbox':
									$vs = $this->post($fieldId.'_checks');
									!empty($vs) && $mValue = implode(',', $vs);
									break;
								default:
									$data = $this->post($fieldId);
									!empty($data) && $mValue = $data;
									break;
							}
							!empty($mValue) && $datas[] = compact('fieldId', 'applyPropertyId', 'moduleId', 'mValue');
						}
						//保存数据
						$this->db->insert_batch('ApplyPlaceModuleData', $datas);
					}
					//关联地点
					$this->db->where('applyPropertyId', $applyPropertyId)->delete('ApplyPropertyAtPlace');//先清空老数据
					$places = array();
					foreach($pids as $k=>$placeId){
						$places[] = compact('applyPropertyId', 'placeId');
					}
					$this->db->insert_batch('ApplyPropertyAtPlace', $places);
					
					$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_success'), 'refer'=>'/profile/checks'));
				}
			}
		}
	}
	
	/**
	 * 获取商家店铺列表
	 * Create by 2012-10-10
	 * @author liuw
	 * @return array
	 */
	function _get_places(){
		$brandId = $this->auth['brand_id'];
		return $this->db->select('id, placename')->where('brandId', $brandId)->get('Place')->result_array();
	}
	
	/**
	 * 已通过审核的碎片排序
	 * Create by 2012-10-26
	 * @author liuw
	 */
	function sequence(){
		if($this->is_post()){
			$id = $this->post('ids');
			if(!empty($id)){
				$ids = explode('|', $id);
				//更新排序值
				$max_rank = count($ids);
				$datas = array();
				foreach($ids as $k=>$sid){
					$datas[] = array(
						'id'=>$sid,
						'rankOrder'=>$max_rank--
					);
				}
				$this->db->update_batch('PlaceOwnSpecialProperty', $datas, 'id');
			}
			$this->echo_json(array('code'=>0, 'msg'=>'排序已保存'));
		}
	}
	
	/**
	 * 预览模型关联的地点
	 * Create by 2012-10-29
	 * @author liuw
	 * @param int $app_id,申请/正式数据记录ID
	 * @param int $is_publish，是否是正式数据
	 */
	function link_places($app_id, $is_publish=0){
		$places = $query = array();
		$this->db->select('Place.id, Place.placename');
		$this->db->from('Place');
		if($is_publish){//正式数据
			$this->db->join('PlaceOwnModule', 'PlaceOwnModule.placeId = Place.id', 'left');
			$query = $this->db->where('PlaceOwnModule.applyId', $app_id)->order_by('PlaceOwnModule.createDate', 'asc')->get()->result_array();
			
		}else{//待审核数据
			$this->db->join('ApplyPropertyAtPlace', 'ApplyPropertyAtPlace.placeId = Place.id', 'left');
			$query = $this->db->where('ApplyPropertyAtPlace.applyPropertyId', $app_id)->order_by('Place.id', 'asc')->get()->result_array();
		}
		foreach($query as $row){
			$places[$row['id']] = $row['placename'];
		}
		$this->assign('list', $places);
		$this->display('places');
	}
	
	
	/**
	 * 预览扩展信息的详情
	 * Create by 2012-10-29
	 * @author liuw
	 * @param int $app_id,申请/正式数据记录ID
	 * @param int $is_publish，是否是正式数据
	 */
	function preview($app_id, $is_publish=0){
		$info = array();
		$modules = $this->_get_modules();
		if($is_publish){//正式数据
			$this->db->select('PlaceOwnSpecialProperty.*, Place.placename');
			$this->db->join('Place', 'Place.id = PlaceOwnSpecialProperty.placeId', 'left');
			$info = $this->db->where('PlaceOwnSpecialProperty.id', $app_id)->limit(1)->get('PlaceOwnSpecialProperty')->first_row('array');
			if(!in_array($info['moduleId'], array(-1, 0))){//查询自定义数据
				$mod = $modules[$info['moduleId']];
				if(empty($mod))
					$this->assign(array('error'=>1, 'msg'=>'您的权限不够，无法操作该扩展模型。请联系管理员'));
				else{
					$fields = array();
					$flist = $mod['fieldes'];
					foreach($flist as $k=>$field){
						$fields[$field['fieldId']] = $field;
					}
					$query = $this->db->where(array('moduleId'=>$info['moduleId'], 'placeId'=>$info['placeId']))->get('PlaceModuleData')->result_array();
					foreach($query as $row){
						$field = $fields[$row['fieldId']];
						$f = array('name'=>$field['fieldName'], 'type'=>$field['fieldType']);
						//格式化数据
						switch($field['fieldType']){
							case 'rich_image':
								$values = json_decode($row['mValue'], true);
								if(!empty($values)){
									foreach($values as $k=>&$v){
										$v['image'] = image_url($v['image'], 'common', 'odp');
										unset($v);
									}
									$f['images'] = $values;
								}
								break;
							case 'image':
								$f['image'] = image_url($row['mValue'], 'common', 'odp');
								break;
							default:
								$f['value'] = $row['mValue'];
								break;
						}
						$info['fields'][] = $f;
					}
				}
		//		print_r($info['fields']);
			}
		}else{//待审核请求			
			//主表数据
			$info = $this->db->where('id', $app_id)->limit(1)->get('ApplyPlaceSpecialProperty')->first_row('array');
			//查询适用的地点
			$query = $this->db->select('ApplyPropertyAtPlace.placeId, Place.placename')->join('ApplyPropertyAtPlace', 'ApplyPropertyAtPlace.placeId = Place.id', 'left')->where('ApplyPropertyAtPlace.applyPropertyId', $app_id)->order_by('Place.id', 'asc')->get('Place')->result_array();
			$info['places'] = $query;			
			if(!in_array($info['moduleId'], array(-1, 0))){
				//查询自定义数据
				$mod = $modules[$info['moduleId']];
				if(empty($mod))
					$this->assign(array('error'=>1, 'msg'=>'您的权限不够，无法操作该扩展模型。请联系管理员'));
				else{
					$fields = array();
					$flist = $mod['fieldes'];
					foreach($flist as $k=>$field){
						$fields[$field['fieldId']] = $field;
					}
					
					$query = $this->db->where(array('moduleId'=>$info['moduleId'], 'applyPropertyId'=>$app_id))->get('ApplyPlaceModuleData')->result_array();
					foreach($query as $row){
						$field = $fields[$row['fieldId']];
						$f = array('name'=>$field['fieldName'], 'type'=>$field['fieldType']);
						//格式化数据
						switch($field['fieldType']){
							case 'rich_image':
								$values = json_decode($row['mValue'], true);
								foreach($values as $k=>&$v){
									$v['image'] = image_url($v['image'], 'common', 'odp');
									unset($v);
								}
								$f['images'] = $values;
								break;
							case 'image':
								$f['image'] = image_url($row['mValue'], 'common', 'odp');
								break;
							default:
								$f['value'] = $row['mValue'];
								break;
						}
						$info['fields'][] = $f;
					}
				}
			}
		}
		$this->assign('info', $info);
		$this->display('preview');
	}
}
   
 // File end