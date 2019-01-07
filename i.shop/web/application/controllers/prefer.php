<?php
/**
 * 商家优惠管理
 * Create by 2012-9-26
 * @author liuw
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Prefer extends MY_Controller{
	
	/**
	 * 发布优惠
	 * 叫兽说没有使用次数这个属性了，所以没设置
	 * Create by 2012-9-26
	 * @author liuw
	 * @return code:1=标题为空，2=介绍为空，3=有效时限未正确设置，4=详情为空，5=没有选择店铺，6=发布失败，0=发布成功，other=接口返回的错误
	 */
	function add(){
		if($this->is_post()){
			$this->_add_new();
		}else{
			$cfg = $this->config->item('image_cfg');
			$this->assign('upload_path', $cfg['upload_view']);
			$this->assign('scale', $this->config->item('prefer_img'));
			$this->assign('active', 'prefer');
			$this->assign('menu', '');
			
			//计算比例
			$prefer_img = $this->config->item('prefer_img');
			$this->assign('t_w', $prefer_img['w']);
			$this->assign('t_h', $prefer_img['h']);
			$this->assign('ratio', '100:'.ceil(floatval($prefer_img['h'])/$prefer_img['w']*100));
			
			//商家下辖所有地点
			$brand_id = $this->auth['brand_id'];
			$places = $this->db->select('id, placename')->where('brandId', $brand_id)->get('Place')->result_array();
			$this->assign('places', $places);
			
			$this->display('index');
		}
	}
	
	/**
	 * 待审核列表
	 * Create by 2012-9-26
	 * @author liuw
	 * @param int $page
	 */
	function check($page=1){
		$list = array();
		$brand = $this->db->where('id', $this->auth['brand_id'])->get('Brand')->first_row('array');
		$all_count = empty($brand) ? 0 : intval($brand['placeCount']);
		//查询待审核的优惠总数
		$count = $this->db->where(array('brandId'=>$this->auth['brand_id'], 'status < '=>20))->count_all_results('ApplyPreference');
		if($count){
			//分页
			$parr = paginate('/prefer/check', $count, $page);
			//数据
			$query = $this->db->where(array('brandId'=>$this->auth['brand_id'], 'status < '=>20))->order_by('createDate', 'desc')->limit($parr['size'], $parr['offset'])->get('ApplyPreference')->result_array();
			foreach($query as $row){
				//图片地址
				$row['image'] = image_url($row['image'], 'common', 'odp');
				!empty($row['icon']) && $row['icon'] = image_url($row['icon'], 'common', 'odp');
				$row['s_begin'] = substr($row['beginDate'], 0, -9);
				$row['s_end'] = substr($row['endDate'], 0, -9);
				$row['createDate'] = gmdate('Y/m-d H:i', strtotime($row['createDate'])+8*3600);
				$row['detail'] = cut_string($row['detail'], 43, '..');
				$row['s_status'] = $row['status'] == 10 ? '被驳回' : '等待审核..';
				$pc = $this->_get_place_count($row['id']);
				$row['p_count'] = $pc >= $all_count ? '全部' : $pc.'家门店';
				$list[$row['id']] = $row;
			}
		}
		$this->assign('list', $list);
		$this->assign('active', 'prefer');
		$this->assign('menu', 'check');
		$this->display('check');
	}
	
	/**
	 * 已审核列表
	 * Create by 2012-9-26
	 * @author liuw
	 * @param string $keyword
	 * @param int $page
	 */
	function publish($keyword='',$page=1){
		!empty($keyword) && $keyword = urldecode($keyword);
		$brand = $this->db->where('id', $this->auth['brand_id'])->get('Brand')->first_row('array');
		$all_count = empty($brand) ? 0 : intval($brand['placeCount']);
		//查询总长度
		$this->db->where(array('status'=>20, 'brandId'=>$this->auth['brand_id']));
		if(!empty($keyword))
			$this->db->where("(title LIKE '%{$keyword}%' OR detail LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')");
		$count = $this->db->count_all_results('ApplyPreference');
		$list = array();
		if($count){
			$arr = array();
			if(!empty($keyword)){
				$this->db->where("(ApplyPreference.title LIKE '%{$keyword}%' OR ApplyPreference.detail LIKE '%{$keyword}%' OR ApplyPreference.description LIKE '%{$keyword}%')");
				$arr[] = $keyword;
			}
			//分页
			$parr = paginate('/prefer/publish', $count, $page, $arr);
			//列表
			$this->db->select('Preference.*, ApplyPreference.id AS applyId');
			$this->db->from('Preference');
			$this->db->join('ApplyPreference', 'ApplyPreference.preferId=Preference.id', 'inner');
			$this->db->where(array('ApplyPreference.status'=>20, 'ApplyPreference.brandId'=>$this->auth['brand_id']));
			$query = $this->db->order_by('ApplyPreference.checkDate', 'desc')->limit($parr['size'], $parr['offset'])->get()->result_array();
			
			foreach($query as $row){
				$row['image'] = image_url($row['image'], 'common', 'odp');
				!empty($row['icon']) && $row['icon'] = image_url($row['icon'], 'common', 'odp');
				$row['s_begin'] = substr($row['beginDate'], 0, -9);
				$row['s_end'] = substr($row['endDate'], 0, -9);
				$row['createDate'] = gmdate('Y/m-d H:i', strtotime($row['createDate'])+8*3600);
				$row['detail'] = cut_string($row['detail'], 43, '..');
				$endDate = strtotime($row['endDate'])+8*3600;
				$endDate <= time()+8*3600 && $row['status'] = -1;
				$pc = $this->_get_place_count($row['applyId']);
				$row['p_count'] = $pc >= $all_count ? '全部' : $pc.'家门店';
				$row['ended'] = 0;
				//过期标志
				if($row['status'] == 2)
					$row['ended'] = 1;
				else{//通过时间判断
					$now = time()+8*3600;
					$end = strtotime($row['endDate'])+8*3600;
					$end <= $now && $row['ended'] = 1;
				}
				$list[$row['id']] = $row;
			}
		}
		$this->assign('list', $list);
		$this->assign('keyword', $keyword);
		$this->assign('page', $page);
		$this->assign('active', 'prefer');
		$this->assign('menu', 'publish');
		$this->display('publish');
	}
	
	/**
	 * 删除正式表的优惠数据
	 * Create by 2012-11-26
	 * @author liuweijava
	 * @param int $id
	 */
	function del_publish(){
		if($this->is_post()){
			$id = $this->post('id');
			$q = $this->db->where(array('id'=>$id, 'status'=>1, 'isSend'=>0))->get('Preference')->first_row('array');
			empty($q) && $this->echo_json(array('code'=>1, 'msg'=>'优惠已发布或已过期'));
			//删除优惠
			$this->db->where('id', $id)->delete('Preference');
			$this->echo_json(array('code'=>0, 'refer'=>'/prefer/publish'));
		}
	}
	
	/**
	 * 删除优惠
	 * Create by 2012-9-26
	 * @author liuw
	 * @param int $id，申请ID
	 */
	function delete($id){
		//查询数据
		$prefer = $this->db->where('id', $id)->get('ApplyPreference')->first_row('array');
		if($prefer){
			//删除数据
			$this->db->where('id', $id)->delete('ApplyPreference');
			//如果是通过审核的优惠，地点优惠数-1
			if($prefer['status'] == 20)
				$this->db->join('ApplyPreferAtPlace', 'ApplyPreferAtPlace.placeId=Place.id')->where('ApplyPreferAtPlace.applyPreferId', $id)->set('preferCount', 'preferCount-1', false)->update('Place');
			//删除关联地点
			$this->db->where('applyPreferId', $id)->delete('ApplyPreferAtPlace');
		}
		$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('do_success')));
	}
	
	/**
	 * 修改优惠
	 * Create by 2012-9-26
	 * @author liuw
	 * @param int $id，申请ID
	 * @param int $is_new，是否新增，只有优惠是通过审核时该值为1
	 */
	function edit($id, $is_new=0){
		if($this->is_post()){
		//	if($is_new){//新增
		//		$this->_add_new();
		//	}else{//修改
				//已通过审核的需要修改status=1
			if($is_new){
				$publish = $this->db->where('id', $id)->get('ApplyPreference')->first_row('array');
				$publish_id = !empty($publish) && !empty($publish['preferId']) ? $publish['preferId'] : false;
				$publish_id && $this->db->where('id', $publish_id)->set('status', 1)->update('Preference');
			}
			//优惠类型
			$type = $this->post('type');
			empty($type) && $type = 0;
			//详情页图片
			$image = $this->post('image');
			empty($image) && $image = '';
			//适用地点
			$places = $this->post('prefer_places');
			empty($places) && $this->echo_json(array('code'=>5, 'msg'=>$this->lang->line('prefer_add_places_empty')));
			//包包图标
			$icon = $image;
			if(!empty($image)){
				$large_image_location = FRAMEWORK_PATH.'/www/data/img/'.str_replace('./','',$image);
				$it = FRAMEWORK_PATH.'/www/data/img/t_'.str_replace('./','',$image);
				$image = $this->_api_upload_img();
				
				//生成包包图标
			//	$icon = make_icon($icon, 'prefer_icon');
				$icon = '';
				$ds = $this->config->item('prefer_icon');
				//先缩放
				list($w, $h, $t) = getimagesize($large_image_location);
				$w < $ds['w'] && $ds['w'] = $w;
				$h < $ds['h'] && $ds['h'] = $h;
				$s = $ds['w'] < $ds['h'] ? $ds['w'] / $w : $ds['h'] / $h;
				//先缩
				$this->load->library('upload_image');
				$this->upload_image->resizeImage($large_image_location, $w, $h, $s);
				//计算裁切ICON的左上角坐标
				list($w, $h, $t) = getimagesize($large_image_location);
				$start_x = $w > $ds['w'] ? floor(($w - $ds['w']) / 2) : 0;
				$start_y = $h > $ds['h'] ? floor(($h - $ds['h']) / 2) : 0;
				$w < $ds['w'] && $ds['w'] = $w;
				$h < $ds['h'] && $ds['h'] = $h;
				$thumb = $this->upload_image->resizeThumbnailImage($large_image_location, $large_image_location, $ds['w'], $ds['h'], $start_x, $start_y, 1);
				$img_param = array(
					'api'		 => $this->lang->line('api_upload_image'),
					'uid'		 => $this->auth['id'],
					'has_return' =>	true,
					'attr'		 => array(
						'file'		 => '@'.$thumb,
						'file_type'  => 'common',
						'resolution' => 'odp'
					)
				);
				$result = json_decode($this->call_api($img_param), true);
				if(intval($result['result_code']) != 0)
					$this->echo_json(array('code'=>$result['result_code'], 'msg'=>'生成图标失败了：'.$result['result_msg']));
				$icon = $result['file_name'];
				@unlink($large_image_location);
			}
			//标题
			$title = $this->post('title');
			empty($title) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('prefer_add_title_empty')));
			//描述
			$descTitle = $this->post('desctitle');
			empty($descTitle) && $descTitle = '';
			$description = $this->post('description');
			empty($description) && $description = '';
			//详情
			$detail = $this->post('detail');
			empty($detail) && $this->echo_json(array('code'=>4, 'msg'=>$this->lang->line('prefer_add_detail_empty')));
			$endDate = $this->post('endDate');
			empty($endDate) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('prefer_add_date_empty')));
		//	$beginDate .= ' 00:00:00';
			$end = strtotime($endDate);
			$endDate = gmdate('Y-m-d 23:59:59', $end+8*3600);
			//是否唯一
		//	$isUnique = $this->post('isUnique');
		//	empty($isUnique) && $isUnique = 0;
			//conditions
			$m = $this->db->where('brandId', $this->auth['brand_id'])->get('BrandMemberCard')->first_row('array');
			$life_limit = $this->post('lifeLimit');
			$cs = array(
				'conditions'=>array(
					array('UNIQUE'=>array('VALUE'=>/*!$isUnique?false:*/true)),
					array('MEMBER_CARD_ID'=>array('VALUE'=>$m['id']))
				),
			);
			if(!$life_limit)
				$cs['conditions'][]['LIFE_LIMIT'] = array('VALUE'=>1);
			$conditions = json_encode($cs);
			//可适用次数
			$frequencyLimit = intval($this->post('frequencyLimit'));
			//更新提交时间
			$createDate = gmdate('Y-m-d H:i:s', time()+8*3600);
			$status = 0;
			
			$data = compact('type', 'title', 'detail', 'description', 'conditions', 'descTitle', 'createDate', 'endDate', 'frequencyLimit'/*, 'isUnique'*/);
			!empty($image) && $data['image'] = $image;
			!empty($icon) && $data['icon'] = $icon;
			$data['status'] = 0;
			//修改
			$this->db->where('id', $id)->update('ApplyPreference', $data);
			//记录适用的店铺
			$this->db->where('applyPreferId', $id)->delete('ApplyPreferAtPlace');
			$datas = array();
			foreach($places as $placeId){
				$datas[] = array('applyPreferId'=>$id, 'placeId'=>$placeId);
			}
			$this->db->insert_batch('ApplyPreferAtPlace', $datas);
							
			$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('prefer_edit_success'), 'refer'=>'/prefer/check'));
		}else{
			//查询数据
			if($is_new){
				$info = $this->db->select('Preference.*, ApplyPreference.id as appId')->join('ApplyPreference', 'ApplyPreference.preferId=Preference.id', 'inner')->where('Preference.id', $id)->get('Preference')->first_row('array');
			//	$this->db->where('id', $id)->set('status')
			}else 
				$info = $this->db->where('id', $id)->get('ApplyPreference')->first_row('array');
		
			$info['image_url'] = image_url($info['image'], 'common', 'odp');
			!empty($info['icon']) && $info['icon_url'] = image_url($info['icon'], 'common', 'odp');
			$conditions = json_decode($info['conditions'], true);
			$info['isUnique'] = $conditions['conditions'][0]['UNIQUE']['VALUE'] ? 1 : 0;
			$info['endDate'] = substr($info['endDate'], 0, -9);
			isset($conditions['conditions'][2]['LIFE_LIMIT']) && $info['lifeLimit'] = 1;
			
			//适用店铺
			$plist = array();
			$q = $this->db->select('placeId')->where('applyPreferId', $is_new ? $info['appId'] : $info['id'])->get('ApplyPreferAtPlace')->result_array();
			foreach($q as $r){
				$plist[] = $r['placeId'];
			}
			$brand_id = $this->auth['brand_id'];
			$places = $this->db->select('id, placename')->where('brandId', $brand_id)->get('Place')->result_array();
			foreach($places as &$row){
				in_array($row['id'], $plist) && $row['selected'] = 1;
				unset($row);
			}
			//是否是全选了的
			$this->assign('checkall', count($places) == count($plist) ? 1 : 0);
			
			$cfg = $this->config->item('image_cfg');
			$this->assign('upload_path', $cfg['upload_view']);
			$prefer_img = $this->config->item('prefer_img');
			$this->assign('t_w', $prefer_img['w']);
			$this->assign('t_h', $prefer_img['h']);
			$this->assign('ratio', '100:'.ceil(floatval($prefer_img['h'])/$prefer_img['w']*100));
			$this->assign('active', 'prefer');
			$this->assign('menu', 'edit');
			$this->assign('info', $info);
			$this->assign('plist', $places);
			$is_new && $current_url = '/prefer/edit/'.$info['appId'].'/1/';
			$this->assign('current_url', $current_url);
			$this->assign('is_new', $is_new);
			$this->display('edit');
		}
	}
	
	/**
	 * 发布优惠
	 * Create by 2012-11-26
	 * @author liuweijava
	 */
	function send_publish(){
		if($this->is_post()){
			$id = $this->post('id');
			//检查优惠
			$prefer = $this->db->where('id', $id)->get('Preference')->first_row('array');
			empty($prefer) && $this->echo_json(array('code'=>1, 'msg'=>'优惠不存在'));
			$prefer['status'] == 0 && $prefer['isSend'] == 1 && $this->echo_json(array('code'=>2, 'msg'=>'优惠已发布过了'));
			//检查是否已过期
			$now = time()+8*3600;
			$end = strtotime($prefer['endDate']) + 8*3600;
			($prefer['status'] == 2 || $end <= $now) && $this->echo_json(array('code'=>3, 'msg'=>'优惠已过期'));
			//发布
			$this->db->where('id', $id)->set('status', 0, false)->set('isSend', 1, false)->set('beginDate', gmdate('Y-m-d H:i:s', time()+8*3600))->update('Preference');
			$this->echo_json(array('code'=>0, 'msg'=>'优惠已发布', 'refer'=>$current_url));
		}
	}
	
	/**
	 * 会员卡
	 * Create by 2012-9-27
	 * @author liuw
	 * @return 0=编辑成功，1=没传图片，2=详情为空，3=摘要为空，4=编辑失败
	 */
	function card(){
		$brandId = $this->auth['brand_id'];
		//查询会员卡
		$card = $this->db->where('brandId', $brandId)->order_by('createDate', 'desc')->limit(1)->get('ApplyBrandMemberCard')->first_row('array');
		if(empty($card)){
			$card = $this->db->where(array('brandId'=>$brandId, 'isBasic'=>1))->get('BrandMemberCard')->first_row('array');
			$card['status'] = 20;
		}
		if($this->is_post()){//编辑
			$status = 0;
			$image = $this->post('image');
			//生成缩略图
			$size = $this->config->item('card_img');
			
			$content = $this->post('content');
			empty($content) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('card_update_content_empty')));
			$summary = $this->post('summary');
			empty($summary) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('card_update_summary_empty')));
			cstrlen($summary) > 30 && $this->echo_json(array('code'=>3, 'msg'=>'会员卡摘要不能超过30个汉字'));
			
			$cardId = $this->post('cardId');
			$brandId = $this->auth['brand_id'];
			$title = $this->auth['brand_name'];
			//是否已有申请
			$rs = $this->db->where(array('cardId'=>$cardId, 'brandId'=>$brandId, 'isBasic'=>1))->get('ApplyBrandMemberCard')->first_row('array');
			if(empty($rs)){//首次修改
				if(!empty($image)){
				//	empty($image) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('card_update_image_empty')));
					$icon = $image;
					$large_image_location = FRAMEWORK_PATH.'/www'.str_replace('./','',$image);
					//上传图片
					$image = $this->_api_upload_img('card_bimg');
					//缩略图
					$icon = make_icon($icon, 'card_img', $image);
		//			@unlink($large_image_location);
				}else
					$image = $this->post('old_img');
				//conditions
			//	$conditions = json_encode(array('conditions'=>array(array('MEMBER_CARD_ID'=>$cardId))));
				$checkDate = gmdate('Y-m-d H:i:s', time()+8*3600);
				$data = compact('content', 'summary', 'status', 'brandId', 'image', 'title', 'checkDate');
				//$data['title'] = '';
				//cardId
				$card = $this->db->where(array('brandId'=>$brandId, 'isBasic'=>1))->get('BrandMemberCard')->first_row('array');
				$data['cardId'] = $card['id'];
				$this->db->insert('ApplyBrandMemberCard', $data);
				$id = $this->db->insert_id();
				if(!$id)
					$this->echo_json(array('code'=>4, 'msg'=>$this->lang->line('do_faild')));
			}else{
			//	$conditions = json_encode(array('conditions'=>array(array('MEMBER_CARD_ID'=>$cardId))));
				$edit = compact('content', 'summary', 'status', 'title');
				if(!empty($image)){
					$icon = $image;
					//上传图片
					$large_image_location = FRAMEWORK_PATH.'/www'.str_replace('./','',$image);
					$image = $this->_api_upload_img('card_bimg');
					//缩略图
					$icon = make_icon($icon, 'card_img', $image);
				//	@unlink($large_image_location);
					$edit['image'] = $image;
				}
				$this->db->where('id', $rs['id'])->update('ApplyBrandMemberCard', $edit);
			}
			
			$this->echo_json(array('code'=>0, 'msg'=>'会员卡信息已提交到IN成都审核,审核成功后即可展示', 'refer'=>''));
		}else{
			$cfg = $this->config->item('image_cfg');
			$this->assign('upload_path', $cfg['upload_view']);
			$this->assign('scale', $this->config->item('card_bimg'));
			$img = $this->config->item('card_bimg');
			$this->assign('ratio', '100:'.ceil(floatval($img['h'])/$img['w']*100));
			$t_w = $img['w'];
			$t_h = $img['h'];
			$this->assign(compact('t_w', 't_h'));
			$this->assign('active', 'prefer');
			$this->assign('menu', 'card');
			!empty($card) && !empty($card['image']) && $card['image_src'] = image_url($card['image'], 'common', 'odp');
			!empty($card) && !empty($card['image']) && $card['icon'] = image_url($card['image'], 'common', 'thumb');
			$this->assign('card', $card);
			$this->display('card');
		}
	}
	
	function view_place($prefer_id, $publish=1){
		$list = array();			
		if($publish){
			$query = $this->db->select('Place.id, Place.placename')->join('Place', 'Place.id=PlaceOwnPrefer.placeId', 'left')->where('PlaceOwnPrefer.preferId', $prefer_id)->get('PlaceOwnPrefer')->result_array();
			foreach($query as $row){
				$list[$row['id']] = $row['placename'];
			}
		}else{
			$query = $this->db->select('Place.id, Place.placename')->join('Place', 'Place.id=ApplyPreferAtPlace.placeId', 'left')->where('ApplyPreferAtPlace.applyPreferId', $prefer_id)->get('ApplyPreferAtPlace')->result_array();
			foreach($query as $row){
				$list[$row['id']] = $row['placename'];
			} 
		}
		$this->assign('list', $list);
		$this->display('v_place');
	}
	
	/**
	 * 预览已通过审核的优惠
	 * Create by 2012-10-18
	 * @author liuw
	 * @param int $prefer_id
	 */
	function preview($prefer_id, $publish=1){
		//查询数据
		if($publish){
			$this->db->select('Preference.*');
			$info = $this->db->where('Preference.id', $prefer_id)->get('Preference')->first_row('array');
			if(!empty($info)){
				//详情图
				!empty($info['image']) && $info['image'] = image_url($info['image'], 'common', 'odp');
				!empty($info['icon']) && $info['icon'] = image_url($info['icon'], 'common', 'odp');
				//beginDate
				!empty($info['beginDate']) && $info['beginDate'] = substr($info['beginDate'], 0, -9);
				//endDate
				$info['endDate'] = substr($info['endDate'], 0, -9);
				//关联地点
			//	$query = $this->db->select('Place.id, Place.placename')->join('Place', 'Place.id = ApplyPreferAtPlace.placeId','inner')->where('ApplyPreferAtPlace.applyPreferId', $prefer_id)->get('ApplyPreferAtPlace')->result_array();
				$query = $this->db->select('Place.id, Place.placename, Place.address')
									->join('Place', 'Place.id = PlaceOwnPrefer.placeId', 'inner')
									->where('PlaceOwnPrefer.preferId', $prefer_id)
									->get('PlaceOwnPrefer')
									->result_array();
				foreach($query as $row){
					$info['places'][$row['id']] = $row;
				}
			}
			$this->assign('prefer', $info);	
		}else{
			$info = $this->db->where('id', $prefer_id)->get('ApplyPreference')->first_row('array');
			if(!empty($info)){
				//详情图
				!empty($info['image']) && $info['image'] = image_url($info['image'], 'common', 'odp');
				//beginDate
				$info['beginDate'] = substr($info['beginDate'], 0, -9);
				//endDate
				$info['endDate'] = substr($info['endDate'], 0, -9);
				//关联地点
				$query = $this->db->select('Place.id, Place.placename, Place.address')->join('Place', 'Place.id = ApplyPreferAtPlace.placeId','inner')->where('ApplyPreferAtPlace.applyPreferId', $prefer_id)->get('ApplyPreferAtPlace')->result_array();
				foreach($query as $row){
					$info['places'][$row['id']] = $row;
				}
			}
			$this->assign('prefer', $info);	
		}	
		$this->display('view');
	}
	
	/**
	 * 
	 * Create by 2012-9-27
	 * @author liuw
	 * @return code:1=标题为空，2=介绍为空，3=有效时限未正确设置，4=详情为空，5=没有选择店铺，6=发布失败，7=未上传详情图片,0=发布成功，other=接口返回的错误
	 */
	function _add_new(){
		//优惠类型
		$type = $this->post('type');
		empty($type) && $type = 0;
		//详情页图片
		$image = $this->post('image');
		empty($image) && $this->echo_json(array('code'=>7, 'msg'=>$this->lang->line('prefer_add_image_empty')));
		//适用地点
		$places = $this->post('prefer_places');
		empty($places) && $this->echo_json(array('code'=>5, 'msg'=>$this->lang->line('prefer_add_places_empty')));
	//	$isUnique = $this->post('isUnique');
	//	empty($isUnique) && $isUnique = 0;
		//标题
		$title = $this->post('title');
		empty($title) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('prefer_add_title_empty')));
		//介绍
		$descTitle = $this->post('desctitle');
		empty($descTitle) && $descTitle = '';
		$description = $this->post('description');
		empty($description) && $description = '';
		//详情
		$detail = $this->post('detail');
		empty($detail) && $this->echo_json(array('code'=>4, 'msg'=>$this->lang->line('prefer_add_detail_empty')));
		$beginDate = gmdate('Y-m-d H:i:s', time()+8*3600);
		$endDate = $this->post('endDate');
		empty($endDate) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('prefer_add_date_empty')));
	//	$beginDate .= ' 00:00:00';
		$end = strtotime($endDate);
		$endDate = gmdate('Y-m-d 23:59:59', $end+8*3600);
		//conditions
		//获得会员卡ID
		$m = $this->db->where('brandId', $this->auth['brand_id'])->get('BrandMemberCard')->first_row('array');
		//可否重复获得
		$life_limit = $this->post('lifeLimit');
		$cs = array(
			'conditions'=>array(
				array('UNIQUE'=>array('VALUE'=>/*!$isUnique?false:*/true)),
				array('MEMBER_CARD_ID'=>array('VALUE'=>$m['id']))
			),
		);
		if(!$life_limit)
			$cs['conditions'][]['LIFE_LIMIT'] = array('VALUE'=>1);
		$conditions = json_encode($cs);
		
		$large_image_location = FRAMEWORK_PATH.'/www/data/img/'.str_replace('./','',$image);
		$icon = $image;
		
		$image = $this->_api_upload_img();
		//生成包包图标
	//	$icon = make_icon($icon, 'prefer_icon');
		$ds = $this->config->item('prefer_icon');
		list($w, $h, $t) = getimagesize($large_image_location);
		$w < $ds['w'] && $ds['w'] = $w;
		$h < $ds['h'] && $ds['h'] = $h;
		$s = $ds['w'] < $ds['h'] ? $ds['w'] / $w : $ds['h'] / $h;
		//先缩
		$this->load->library('upload_image');
		$this->upload_image->resizeImage($large_image_location, $w, $h, $s);
		//计算裁切ICON的左上角坐标
		list($w, $h, $t) = getimagesize($large_image_location);
		$start_x = $w > $ds['w'] ? floor(($w - $ds['w']) / 2) : 0;
		$start_y = $h > $ds['h'] ? floor(($h - $ds['h']) / 2) : 0;
		$w < $ds['w'] && $ds['w'] = $w;
		$h < $ds['h'] && $ds['h'] = $h;
		$thumb = $this->upload_image->resizeThumbnailImage($large_image_location, $large_image_location, $ds['w'], $ds['h'], $start_x, $start_y, 1);
		$img_param = array(
				'api'		 => $this->lang->line('api_upload_image'),
				'uid'		 => $this->auth['id'],
				'has_return' =>	true,
				'attr'		 => array(
					'file'		 => '@'.$thumb,
					'file_type'  => 'common',
					'resolution' => 'odp'
				)
			);
		$result = json_decode($this->call_api($img_param), true);
		if(intval($result['result_code']) != 0)
			$this->echo_json(array('code'=>$result['result_code'], 'msg'=>'生成图标失败了：'.$result['result_msg']));
		$icon = $result['file_name']; 
		@unlink($large_image_location);
		
		//可适用次数
		$frequencyLimit = intval($this->post('frequencyLimit'));
		
		//保存数据
		$brandId = $this->auth['brand_id'];
		$data = compact('type', 'title', 'image', 'icon', 'beginDate', 'endDate', 'detail', 'description', 'brandId', 'conditions', 'descTitle', 'frequencyLimit'/*, 'isUnique'*/);
		$this->db->insert('ApplyPreference', $data);
		$id = $this->db->insert_id();
		if(!$id)
			$this->echo_json(array('code'=>6, 'msg'=>$this->lang->line('do_faild')));
		//记录适用的店铺
		$datas = array();
		foreach($places as $placeId){
			$datas[] = array('applyPreferId'=>$id, 'placeId'=>$placeId);
		}
		$this->db->insert_batch('ApplyPreferAtPlace', $datas);
		$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('prefer_add_success'), 'refer'=>'/prefer/check'));
	}
	
	/**
	 * 调用API上传图片
	 * Create by 2012-9-28
	 * @author liuw
	 * @return string，图片正式名称
	 */
	function _api_upload_img($cfg_key = 'prefer_img'){
		$image = $this->post('image');
		if(!empty($image)){
			//计算原图裁切坐标，主要是计算Y轴坐标
			$cfg = $this->config->item($cfg_key);
			$x1 = intval($this->post('x1'));
			$y1 = intval($this->post('y1'));
		//	$x2 = intval($this->post('x2'));
		//	$y2 = intval($this->post('y2'));
			$uw = floatval($this->post('w'));
			$uh = floatval($this->post('h'));
			$large_image_location = FRAMEWORK_PATH.'/www/data/img/'.str_replace('./','',$image);
			list($w, $h, $t, $att) = getimagesize($large_image_location);
			$exp = (float)$x1 / $uw;
			$x1 = ceil($w * ((float)$x1 / $uw));
		//	$x2 = floor($w * ($x2 / 360));
			$y1 = $this->post("y1");
			$exp = (float)$y1 / $uh;
			$y1 = ceil($h * $exp);
		//	$y2 = ceil($this->post('h')*$exp);
			$width = $cfg['w'];
			$height = $cfg['h'];
			$s_x = $s_y = 0;
			//Scale the image to the thumb_width set above
			if($w <= $cfg['w']){
				$width = $w;
			}else{
				$width = $cfg['w'];
			}
			$y1 > 0 && $height + $y1 > $h && $y1 = $h - $height;
			$s_x = $x1;
			$s_y = $y1;
	//		exit($width.':'.$height.'		'.$s_x.':'.$s_y);
			$this->load->library('upload_image');
			$cropped = $this->upload_image->resizeThumbnailImage($large_image_location, $large_image_location,$width,$height,$s_x,$s_y,1);
	//		exit(1);
			$img_param = array(
				'api'		 => $this->lang->line('api_upload_image'),
				'uid'		 => $this->auth['id'],
				'has_return' =>	true,
				'attr'		 => array(
					'file'		 => '@'.$cropped,
					'file_type'  => 'common',
					'resolution' => 'odp'
				)
			);
			$result = json_decode($this->call_api($img_param), true);
			if(intval($result['result_code']) != 0)
				$this->echo_json(array('code'=>$result['result_code'], 'msg'=>'上传图片失败了：'.$result['result_msg']));
			$image = $result['file_name'];
		}
		return $image;
	}
	
	/**
	 * 获得地点的地址
	 * Create by 2012-11-13
	 * @author liuweijava
	 * @param int $place_id
	 */
	function get_place_addr($place_id){
		$rs = $this->db->select('address')->where('id', $place_id)->get('Place')->first_row('array');
		echo $rs['address'];
		exit;
	}
	
	/**
	 * 计算适用店铺数量
	 * Create by 2012-9-29
	 * @author liuw
	 * @param unknown_type $prefer_id
	 */
	function _get_place_count($prefer_id){
		return $this->db->where('applyPreferId', $prefer_id)->count_all_results('ApplyPreferAtPlace');
	}
	
}
   
 // File end