<?php
/**
 * 客户端背景图管理
 * Create by 2012-4-9
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Background extends MY_Controller{
	/**
	 * 背景设置
	 */
	public function set_bg() {
		// 查询AppSetting
		$sql = "SELECT svalue FROM AppSetting WHERE skey='index_bg'";
		$bg = $this->db->query($sql)->row_array();
		
		if ($this->is_post()) {
			$data = array(
					'name' => '启动画面图片',
					'avaliable' => 1,
					'image' => $this->post('image'),
					'lastUpdate' => now()
					);
			
			$b = $this->db->replace('AppSetting', array('skey' => 'index_bg', 'svalue' => encode_json($data)));
			if($b) {
				$api_rtn = api_update_cache('AppSetting');
				$this->success('启动画面图片设置成功', '', '', '', $api_rtn);
			} else {
				$this->success('启动画面图片设置失败');
			}
		}
		
		$this->assign(array(
				'bg' => json_decode($bg['svalue'], true)
				));
		
		$this->display('set_bg', 'background');
	}
	
	/**
	 * 小游戏图片
	 */
	public function set_game() {
		// 查询AppSetting
		$sql = "SELECT svalue FROM AppSetting WHERE skey='point_game'";
		$bg = $this->db->query($sql)->row_array();
		
		if ($this->is_post()) {
			$data = array(
					'name' => '积分小游戏界面设置',
					'headImage' => $this->post('head_image'),
					'cardImage' => $this->post('card_image'),
					'lastUpdate' => now()
					);
			
			$b = $this->db->replace('AppSetting', array('skey' => 'point_game', 'svalue' => encode_json($data)));
			if($b) {
				$api_rtn = api_update_cache('AppSetting');
				$this->success('积分小游戏界面设置成功', '', '', '', $api_rtn);
			} else {
				$this->success('积分小游戏界面设置失败');
			}
		}
		
		$this->assign(array(
				'bg' => json_decode($bg['svalue'], true)
				));
		
		$this->display('set_game', 'background');
	}
	
	/**
	 * 显示背景图列表
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function index(){
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		$this->assign('keyword',$keyword);
		//查询总数
		if(!empty($keyword))
			$this->db->like('name', $keyword);
		$count = $this->db->where('type', 0)->count_all_results('ClientBackground');
		if($count){
			//分页
			$parr = $this->paginate($count);
			$cfg = $this->config->item('background_image');
			//数据列表
			if(!empty($keyword))
				$this->db->like('name', $keyword);
			$query = $this->db->where('type',0)->order_by('isForce', 'desc')->order_by('id', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get('ClientBackground');
			$list = array();
			foreach($query->result_array() as $row){
				//平台名称
				$pf = $cfg[$row['type']];
				$row['platform'] = strtoupper($pf['name']);
				$list[$row['id']] = $row;
			}
			$this->assign('list', $list);
		}
		$this->display('index','background');
	}
	
	/**
	 * 添加背景图
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function add(){
		$cfg = $this->config->item('background_image');
		if($this->is_post()){
			$datas = array();
			$name = $this->post('name');
			$data['name'] = $name;
			$uploads = $allsize = 0;
			//获取图片
			$suc_image = true;
			foreach($cfg as $key=>$val){
				if(!empty($val['sizes'])){
					$images = $this->post($val['name']);
					foreach($images as $k=>$img){
						$suc_image = !empty($img);
					}
					$uploads += count($images);
					if(isset($images)&&!empty($images)){
						$data['type'] = $key;
						$data['image'] = $images[0];
					}
					$datas[] = $data;
				}
			}
			//检查图片数量
			foreach($cfg as $c){
				if(isset($c['sizes'])&&!empty($c['sizes'])&&is_array($c['sizes']))
					$allsize += count($c['sizes']);
			}
			if($uploads < $allsize || !$suc_image)
				$this->error(str_replace('@{size}', $allsize.'', $this->lang->line('background_image_empty')));
			else{
				//保存数据
				$this->db->insert_batch('ClientBackground', $datas);
				$this->success($this->lang->line('do_success'), build_rel(array('client','background','index')), build_uri(array('client','background','index')), 'closeCurrent');
			}
		}else{
			$this->assign('cfg', $cfg);
			$this->display('make', 'background');
		}
	}
	
	/**
	 * 编辑背景图
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function edit(){
		$id = $this->get('id');
		$cfg = $this->config->item('background_image');
		if($this->is_post()){
			//获得名称
			$rs = $this->db->where('id', $id)->get('ClientBackground')->first_row('array');
			$oldname = $rs['name'];
			$name = $this->post('name');
			$data['name'] = $name;
			//获取图片
			foreach($cfg as $key=>$val){
				if(!empty($val['sizes'])){
					$data['type'] = $key;
					$images = $this->post($val['name']);
					if(isset($images)&&!empty($images)){
						$data['image'] = $images[0];
					}
					//更新数据
					$this->db->where('name', $oldname)->where('type', $key)->update('ClientBackground', $data);
				}
			}
			$this->success($this->lang->line('do_success'), build_rel(array('client','background','index')), build_uri(array('client','background','index')), 'closeCurrent');
		}else{
			$this->assign('cfg', $cfg);
			$info = $this->db->where('id', $id)->limit(1)->get('ClientBackground')->first_row('array');
			//查询图片
			$rs = $this->db->select('type, image')->where('name', $info['name'])->order_by('type','asc')->get('ClientBackground');
			$images = array();
			foreach($rs->result_array() as $r){
				$images[$r['type']] = $r['image'];
			}
			$this->assign('info', $info);
			$this->assign('images', $images);
			$this->display('make', 'background');
		}
	}
	
	/**
	 * 删除背景图
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function delete(){
		$id = $this->get('id');
		//获得名称
		$rs = $this->db->where('id', $id)->get('ClientBackground')->first_row('array');
		$name = $rs['name'];
		$this->db->where('name', $name)->delete('ClientBackground');
		$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
	}
	
	/**
	 * 设置/取消背景图默认显示状态
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function set_def(){
		$id = $this->get('id');
		//获得名称
		$rs = $this->db->where('id', $id)->get('ClientBackground')->first_row('array');
		$name = $rs['name'];
		$isForce = $this->get('stat');
		switch($isForce){
			case 1://强制默认
				//先取消之前设置的默认背景图
				$this->db->where('isForce', 1)->update('ClientBackground', array('isForce'=>0));
				//更新
				$this->db->where('name', $name)->update('ClientBackground', array('isForce'=>$isForce));
				break;
			case 0://取消默认
				$this->db->where('isForce', 1)->update('ClientBackground', array('isForce'=>0));
				break;
		}
		$this->success($this->lang->line('do_success'), $this->_index_rel, $this->_index_uri, 'forward');
	}
	
}   
   
 // File end