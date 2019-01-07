<?php
/**
 * Create by 2012-11-22
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Fragment_model extends MY_Model{
	
	function __construct(){
		parent::__construct();
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
	 * 查询碎片数量
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param mixed $categoryId
	 * @param mixed $keyword
	 */
	function count_frag($categoryId=0, $keyword=false){
		if($categoryId){
			$this->db->where('FIND_IN_SET(fid, (SELECT fragmentId FROM WebNewsCategory WHERE id=\''.$categoryId.'\'))');
		}else{
			if($this->auth['role'][0]!=1){
				$this->db->where('FIND_IN_SET(fid , (SELECT GROUP_CONCAT(fragmentId,",") FROM WebNewsCategory WHERE id in ('.implode(",",$this->newsRight).') and parentId=0 and status=1))');
			}
		}
		if($keyword){
			$this->db->like('name', $keyword);
		}
		
		return $this->db->count_all_results('WebRecommendFragment');
	}
	
	/**
	 * 查询碎片列表
	 * Create by 2012-11-22
	 * @author liuweijava
	 * @param int $size
	 * @param int $offset
	 * @param mixed $categoryId
	 * @param mixed $keyword
	 */
	function search_frag($size, $offset, $categoryId=0, $keyword=false){
		if($categoryId){
			$this->db->where('FIND_IN_SET(fid, (SELECT GROUP_CONCAT(fragmentId,",") FROM WebNewsCategory WHERE id=\''.$categoryId.'\' or parentId=\''.$categoryId.'\'))');
		}else{
			if($this->auth['role'][0]!=1){
				$this->db->where('FIND_IN_SET(fid , (SELECT GROUP_CONCAT(fragmentId,",") FROM WebNewsCategory WHERE (id in ('.implode(",",$this->newsRight).') or parentId in ('.implode(",",$this->newsRight).') ) and status=1))');/* and parentId=0*/
			}
		}
		if($keyword){
			$this->db->like('name', $keyword);
		}
		
		$query = $this->db->order_by('orderValue', 'desc')->order_by('dateline', 'desc')->limit($size, $offset)->get('WebRecommendFragment')->result_array();
		foreach($query as &$row){
			//已关联的频道
			$cats = get_categorys($row['fid'], -1);
			if(!empty($cats)){
				$cs = array();
				foreach($cats as $k=>$c){
					$cs[] = $c['id'].":".$c['catName'];
				}
				$row['cates'] = implode(',', $cs);
				unset($cs, $row);
			}
			unset($cats);
		}
		return $query;
	}
	
	/**
	 * 查询碎片详情
	 * Create by 2012-11-23
	 * @author liuweijava
	 * @param int $fid
	 * @return array
	 */
	function get_frag($fid){
		$info = $this->db->where('fid', $fid)->get('WebRecommendFragment')->first_row('array');
		//碎片关联的频道
		$info['cates'] = get_categorys($fid);
		
		return $info;
	}
	
	/**
	 * 更新频道的推荐碎片
	 * Create by 2012-11-23
	 * @author liuweijava
	 * @param int $fid
	 * @param array $post
	 * @param boolean $link,是否是建立关联的操作
	 */
	function update_category_frag($fid, $cids=array(), $link=true){
		$datas = array();
		
		if($link){
			//先清理旧的关联
			$this->update_category_frag($fid, array(), false);
			//频道列表
			$query = $this->db->select('id, fragmentId')->where_in('id', $cids)->get('WebNewsCategory')->result_array();
			foreach($query as $row){
				if(empty($row['fragmentId'])){//没关联碎片的情况
					$fragmentId = array($fid);
					$datas[] = array('id'=>$row['id'], 'fragmentId'=>implode(',', $fragmentId));
				}else{
					$fragmentId = explode(',', $row['fragmentId']);
					!in_array($fid, $fragmentId) && $fragmentId[] = $fid;//没关联指定碎片的才增加关联
					$datas[] = array('id'=>$row['id'], 'fragmentId'=>implode(',', $fragmentId));
				}
			}
			
		}else{
			//频道列表
			if(!empty($cids))
				$this->db->where_in('id', $cids);
			$query = $this->db->select('id, fragmentId')->where('FIND_IN_SET(\''.$fid.'\', fragmentId)')->get('WebNewsCategory')->result_array();
			$datas = array();
			foreach($query as $row){
				if(strpos($row['fragmentId'], ','.$fid) !== false){//关联多个碎片的情况
					$fragmentId = str_replace(','.$fid, '', $row['fragmentId']);
					$datas[] = array('id'=>$row['id'], 'fragmentId'=>$fragmentId);
				}else if(strpos($row['fragmentId'], $fid.',') !== false){
					$fragmentId = str_replace($fid.',', '', $row['fragmentId']);
					$datas[] = array('id'=>$row['id'], 'fragmentId'=>$fragmentId);
				}else{//只关联了指定的碎片
					$datas[] = array('id'=>$row['id'], 'fragmentId'=>'');
				}
			}
			
		}
		//var_dump($datas);
		//更新数据
		!empty($datas) && $this->db->update_batch('WebNewsCategory', $datas, 'id');
	}
	/**
	 * @return int $code,0=编辑成功，1=碎片名称重复，2=保存失败
	 */
	function _make_frag($info, $fid=0, $cids=array()){
		//var_dump($info);exit;
		if(!$fid){//添加碎片
			//检查碎片名称是否重复
			$c = $this->db->where('name', $name)->count_all_results('WebRecommendFragment');
			if($c > 0){
				return 1;
			}else{//保存数据
				$this->db->insert('WebRecommendFragment', $info);
				$fid = $this->db->insert_id();
				if(!$fid)
					return 2;
				else {
					//碎片保存成功,如果是团房碎片，读取接口，存入数据
					if($info['style'] == "f_tf"){
						$info['id'] = $fid;
						$this->pre_rec_tf($info);
					}
				}
			}
		}else{//修改碎片
			//var_dump($info);exit;
			$this->db->where('fid', $fid)->update('WebRecommendFragment', $info);
			//碎片保存成功,如果是团房碎片，读取接口，存入数据
			if($info['style'] == "f_tf"){
				$info['id'] = $fid;
				$this->pre_rec_tf($info);
			}
		}
		//关联到频道
		!empty($cids) && $this->update_category_frag($fid, $cids);
		return 0;
	}
	
	/**
	 * 编辑碎片
	 * Create by 2012-11-23
	 * @author liuweijava
	 * @return int $code,0=编辑成功，1=碎片名称重复，2=保存失败
	 */
	function make_frag(){
		$do_suc = false;
		$fid = $this->input->post('fid');
		$cids = $this->input->post('cat_cids');
		!empty($cids) && $cids = explode(',', $cids);
		
		//碎片属性
		$data = array();
		$name = $this->input->post('name');
		$dataSource = $this->input->post('dataSource');
		!empty($dataSource) && $dataSource === 'other' && $dataSource = $this->input->post('dataSource_v');
		$description = $this->input->post('description');
		$fregType = $this->input->post('fregType');
		$style = $this->input->post('style');
		$orderValue = $this->input->post('orderValue');
		
		$dateline = time()+8*3600;
		
		$data = compact('name', 'description', 'dataSource', 'fregType', 'orderValue', 'style', 'dateline');
		//碎片规则
		$max_len = $this->input->post('rule_max_length');
		$jump_link = $this->input->post('rule_jump_link');
		$rule = array();
		!empty($max_len) && $rule['max_length'] = intval($max_len);
		!empty($jump_link) && $rule['jump_link'] = $jump_link;
		$pic_size = $this->input->post('rule_pic_size');
		!empty($pic_size) && $rule['pic_size'] = $pic_size;
		!empty($rule) && $data['rule'] = encode_json($rule);
		//扩展属性
		$extraProperty = $this->input->post('extraProperty');
		if(!empty($extraProperty)){
			$extraProperties = array();
			$extras = explode("\n", $extraProperty);
			foreach($extras as $k=>$va){
				$prop = explode('|', $va);
				$exs = array();
				foreach($prop as $ke=>$v){
					$val = explode('=', $v);
					$exs[$val[0]] = $val[1];
				}
				$extraProperties[$exs['key']] = $exs;
			}
			!empty($exs) && $data['extraProperty'] = encode_json($extraProperties);
		}else{
			$data['extraProperty'] = "";
		}
		
		return $this->_make_frag($data, $fid, $cids);
	}
	
	/**
	 * 删除碎片
	 * Create by 2012-11-23
	 * @author liuweijava
	 */
	function del_frags(){
		$fids = $this->input->post('fids');
		//删除碎片表数据
		$this->db->where_in('fid', $fids)->delete('WebRecommendFragment');
		//删除推荐数据
		$this->db->where_in('fragmentId', $fids)->delete('WebRecommendData');
		//清理与频道的关联关系
		foreach($fids as $k=>$fid){
			$query = $this->db->select('id')->where('FIND_IN_SET(\''.$fid.'\', fragmentId)')->get('WebNewsCategory')->result_array();
			$cids = array();
			foreach($query as $row){
				$cids[] = $row['id'];
			}
			$this->update_category_frag($fid, $cids, false);
		}
	}
	
	/**
	 * 更新碎片数据
	 * Create by 2012-11-27
	 * @author liuweijava
	 * @param int $fid
	 */
	function flush_data($fid){
		
		//碎片属性
		$frag = $this->get_frag($fid);
		!empty($frag['rule']) && $frag['rule'] = json_decode($frag['rule'], true);
		!empty($frag['rule']) && !empty($frag['rule']['pic_size']) && $frag['rule']['pic_size'] = explode('*', $frag['rule']['pic_size']);
		!empty($frag['extraProperty']) && $frag['extraProperty'] = json_decode($frag['extraProperty'], true);
		
		//查询数据
		$list = $this->db->where('fragmentId', $fid)->order_by('orderValue', 'asc')->get('WebRecommendData')->result_array();
		foreach($list as &$r){
			if(!empty($r['extraData'])){
				$exd = json_decode($r['extraData'], true);
				foreach($exd as $k=>&$e){
					$t = $frag['extraProperty'][$k]['type'];
					$e = array(
						'type'=>$t,
						'data'=>$e
					);
					unset($e);
				}
				$r['extraData'] = $exd;
			}
			unset($r);
		}
		$cache_data = $list;
	//	print_r($list);exit;
		//生成缓存
		$cache_id = 'fragment_'.$fid;
		//$this->template->assign('frag', $frag);
		//$this->template->assign('list', $list);
		//$cache_data = $this->template->fetch($frag['style'], 'fragment/temp');
		set_cache($cache_id, $cache_data);
		
		
		
		//修改碎片的最后更新时间
		$this->db->where('fid', $fid)->set('dateline', time()+8*3600, false)->update('WebRecommendFragment');
		return $cache_data;
	}
	
	/**
	 * 复制一个碎片，名称需要前台指定
	 * Create by 2012-12-7
	 * @author liuweijava
	 * @param int $fid
	 * @return int 0=复制成功；1=碎片名重复；2=复制碎片属性失败；3=建立频道联系失败
	 */
	function clone_fragment($fid){
		//检查重命名是否已被使用
		$c = $this->db->where('name', $clone_name)->count_all_results('WebRecommendFragment');
		//获得要复制的碎片的详细属性
		$frag = $this->db->where('fid', $fid)->get('WebRecommendFragment')->first_row('array');
		//修改属性并添加新碎片
		$frag['name'] .= '[copy_'.($c+1).']';
		unset($frag['fid']);
		//复制
		return $this->_make_frag($frag);		
	}
	
	/**
	 * 预载入成都团房数据
	 * @param array $info 碎片数据
	 */
	function pre_rec_tf($info){
		//修改了方式，又不用取数据了。。不过留着吧！免得又需要
		/*$back = http_request($info['dataSource'],array(),array(),'GET',true);
		
		$back = json_decode($back,true);
		$data = $back['list'];
		if(!empty($data) && is_array($data)){
			$all_rec_data = array();
			foreach($data as $k=>$row){
				//成都团房数据
				$extraData['market_price'] = $row['market_price'] > 0 ? $row['market_price']."元/㎡" : "待定";
				$extraData['end_time']	   = $row['end_time'];
				$extraData['youhui']	   = $row['youhui'];
				$extraData['fanli']        = $row['fanli'];
				$extraData['max_youhui']   = $row['max_youhui'];
				$extraData['now_number']   = $row['now_number'];
				$extraData['tenper']       = $row['tenper'];
				$json_extraData = encode_json($extraData);
				$rec_data = array(
					'fragmentId' => $info['id'],
					'orderValue' => $k,
					'title' => $row['title'],
					'link' =>	($row['url']),
					'summary' => '',
					'image' =>  ($row['image']),
					'dateline' => time(),
					'extraData' => $json_extraData
				);
				
				unset($extraData,$json_extraData);
				$all_rec_data[] = $rec_data;
			}
			
			if(!empty($all_rec_data)){
				$this->db->where('fragmentId', $info['id'])->delete('WebRecommendData');
				$this->db->insert_batch('WebRecommendData', $all_rec_data);
			}
		}*/
	}
	
}
 // File end