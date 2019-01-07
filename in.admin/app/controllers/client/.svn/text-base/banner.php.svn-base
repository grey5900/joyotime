<?php
/**
 * Banner图片管理
 * Create by 2012-4-9
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class Banner extends MY_Controller{
    var $link_type;
    function __construct() {
        parent::__construct();
        
        $this->link_type = $this->config->item('link_type');
        $this->assign('link_type', $this->link_type);
    }

	/**
	 * 显示banner列表，分成已启用和未启用两个列表
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function index(){
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		$this->assign('type', $type);
		$enables = $disables = array();
		//已启用的banner列表，只取5个
		$query = $this->db->where('type', $type)->where('disable',0,FALSE)->order_by('orderValue','desc')->get('ClientBanner');
		foreach($query->result_array() as $row){
			//检查链接类型
			if(!isset($row['content']) || empty($row['content']))
			$row['content'] = '无链接';
			else{
				$links = explode('//',$row['content']);
				switch($links[0]){
					case 'place:'://地点
						//查询到地点名称
						$rs = $this->db->select('placename')->where('id',intval($links[1]),FALSE)->limit(1)->get('Place')->first_row('array');
						$row['content'] = '地点: '.$rs['placename'];
						break;
					case 'user:'://用户
						//查询用户昵称，没有昵称就返回用户名
						$rs = $this->db->select('username,nickname')->where('id',intval($links[1]),FALSE)->limit(1)->get('User')->first_row('array');
						$row['content'] = '用户: '.(isset($rs['nickname'])&&!empty($rs['nickname'])?$rs['nickname']:$rs['username']);
						break;
					case 'badge:'://勋章
						//查询勋章名称
						//$rs=
						$row['content'] = '勋章: ';
						break;
					default:break;//HTTP链接
				}
			}
			$enables[$row['id']] = $row;
		}
		//未启用的banner列表
		$disables = $this->listBanner($type);
		$this->assign(compact('enables','disables'));
		$this->assign('page_rel', 'jbsxBox2');
		$this->display('index', 'banner');
	}

	public function list_disable($type=0){
		$this->assign('disables', $this->listBanner($type));
		$this->assign('page_rel', 'jbsxBox2');
		$this->assign('type', $type);
		$this->display('list', 'banner');
	}

	/**
	 * 添加banner
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function add(){
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		if($this->is_post()){
			$images = $this->post('banner');//图片名称
			$size = count($this->config->item('banner_image'));
			if(empty($images) || count($images) < $size){
				$this->error(str_replace("@{size}", $size.'', $this->lang->line('banner_image_empty')));
			}else{
				$suc_banner = true;
				foreach($images as $k=>$img){
					$suc_banner = !empty($img);
				}
				if(!$suc_banner)
					$this->error(str_replace("@{size}", $size.'', $this->lang->line('banner_image_empty')));
				else{
					$image = isset($images)&&!empty($images)?$images[0]:'';
					$name = $this->post('name');
					// $c_type = $this->post('c_type');
					// $content = '';
					// if(isset($c_type) && !empty($c_type)){
						// switch($c_type){
                            // case 'groupon':
                            // case 'filmticket':
							// case 'http':
								// $content = $this->post('content');
								// if(strpos($content, $c_type . '://') === FALSE) {
								    // $content = $c_type . '://'.$content;
								// }
								// break;
							// default:
								// $content = $c_type.'://'.$this->post('content_id');
								// break;
						// }
					// }
                    // Add By piggy 
                    // 去掉空格
                    // $content = trim($content);
                    // 2012.09.10新的
                    $item_type = $this->post('item_type');
                    if(in_array($item_type, array(1, 4))) {
                        $item_id = $this->post('content_id');
                    } else {
                        $item_id = $this->post('item_id');
                    }
                    $protocol = $this->link_type[$item_type]['key'];
                    if($protocol) {
                        $pre_url = $protocol . '://';
                        if(strpos($item_id, $pre_url) === 0) {
                            // 如果开始部分为协议部分
                            $content = $item_id;
                        } else {
                            $content = $pre_url . $item_id;
                        }
                    }
                    $content = trim($content);
                    
					$startDate = $this->post('startDate');
					$endDate = $this->post('endDate');
					$data = compact('name','image');
					$data['hyperLink'] = $content;
					if($startDate)
					   $data['startDate'] = $startDate;
					if($endDate)
					   $data['endDate'] = $endDate;
					$data['type'] = $type;
					$data['disable'] = $this->post('disable');
					$data['title'] = $this->post('title');
					//保存数据
					$this->db->insert('ClientBanner', $data);
					$id = $this->db->insert_id();
					if(!$id)
					$this->error($this->lang->line('banner_add_error'));
					else{
						$this->success($this->lang->line('banner_add_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'closeCurrent');
					}
				}
			}
		}else{
		    $this->assign('type', $type);
			$cfg = $this->config->item('banner_image');
			$this->assign('cfg', $cfg);
			$this->assign('info', array('disable', '1'));
			$this->assign('post_url', site_url(array('client','banner','add', 'type', $type)));
			$this->display('make','banner');
		}
	}

	/**
	 * 编辑banner
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function edit(){
		$id = $this->get('id');
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		if($this->is_post()){
			$images = $this->post('banner');
			$image = isset($images)&&!empty($images)?$images[0]:'';
			$name = $this->post('name');
			// $c_type = $this->post('c_type');
			// $content = FALSE;
			// if(isset($c_type) && !empty($c_type)){
				// switch($c_type){
                    // case 'groupon':
                    // case 'filmticket':
                    // case 'http':
                        // $content = $this->post('content');
                        // if(strpos($content, $c_type . '://') === FALSE) {
                            // $content = $c_type . '://'.$content;
                        // }
                        // break;
					// case 'n':
						// $content = '';
						// break;
					// default:
						// $content = $c_type.'://'.$this->post('content_id');
						// break;
				// }
			// }
            // // Add By piggy 
            // // 去掉空格
            // $content = trim($content);
            
            // 2012.09.10新的
            $item_type = $this->post('item_type');
            if(in_array($item_type, array(1, 4))) {
                $item_id = $this->post('content_id');
            } else {
                $item_id = $this->post('item_id');
            }
            $protocol = $this->link_type[$item_type]['key'];
            if($protocol) {
                $pre_url = $protocol . '://';
                if(strpos($item_id, $pre_url) === 0) {
                    // 如果开始部分为协议部分
                    $content = $item_id;
                } else {
                    $content = $pre_url . $item_id;
                }
            }
            $content = trim($content);
            
			$startDate = $this->post('startDate');
			$endDate = $this->post('endDate');
			//属性集合
			$edit = array();
// 			if(isset($image)&&!empty($image))
			$edit['image'] = $image;
// 			if(isset($name)&&!empty($name))
			$edit['name'] = $name;
			if($content !== FALSE)
// 			$edit['content'] = $content;
			$edit['hyperLink'] = $content;
			if(!empty($startDate))
			$edit['startDate'] = $startDate;
			if(!empty($endDate))
			$edit['endDate'] = $endDate;
			$edit['disable'] = $this->post('disable');
			$edit['title'] = $this->post('title');
			$this->db->where('type', $type)->where('id', $id)->update('ClientBanner', $edit);
			$this->success($this->lang->line('do_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'closeCurrent');
		}else{
			$info = $this->db->where('type', $type)->where('id', $id)->limit(1)->get('ClientBanner')->first_row('array');
			//关联的资源
			// if(!empty($info['content'])){
				// $cs = explode('//', $info['content']);
				// switch($cs[0]){
					// case 'place:'://地点
						// //查询地点名称
						// $info['item_id']=$cs[1];
						// $rs = $this->db->select('placename')->where('id', $cs[1])->get('Place')->first_row('array');
						// $info['item'] = $rs['placename'];
						// break;
					// case 'user:'://用户
						// $info['item_id']=$cs[1];
						// $rs = $this->db->where('id',$cs[1])->get('User')->first_row('array');
						// $info['item']=!empty($rs['nickname'])?$rs['nickname']:$rs['username'];
						// break;
					// case 'badge:'://勋章
						// break;
					// default://url
						// $info['item_id']=0;
						// $info['item']=$info['content'];
						// break;
				// }
			// }
			if($info['hyperLink']) {
                $pos = strpos($info['hyperLink'], ':');
                $item_type = substr($info['hyperLink'], 0, $pos);
                foreach($this->link_type as $k=>$v) {
                    if($v['key'] == $item_type) {
                        $this->assign('item_type', $k);
                        $value = substr($info['hyperLink'], $pos+3);
                        if('inplace' == $item_type || 'inuser' == $item_type) {
                            // 如果是地点或者用户需要去获取他的名称
                            $row = $this->db->get_where(ucfirst(str_replace('in', '', $item_type)), array('id'=>$value))->row_array();
                            if('inplace' == $item_type) {
                                $value = $row['placename'];
                            } else {
                                $value = $row['nickname']?$row['nickname']:$row['username'];
                            }
                            $this->assign('item_value', array('id'=>$row['id'],'value'=>$value));
                        } else {
                            $this->assign('item_value', $value);
                        }
                        
                        break;
                    }
                }
            }
			
            $this->assign('type', $type);
			$this->assign('info', $info);
			$cfg = $this->config->item('banner_image');
			$this->assign('cfg', $cfg);
			$this->assign('post_url', site_url(array('client','banner','edit','id',$id, 'type', $type)));
			$this->display('make','banner');
		}
	}

	/**
	 * 删除banner
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function delete(){
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		$id = $this->get('id');
		$this->db->where('id', $id)->where('type',$type)->delete('ClientBanner');
		$this->success($this->lang->line('banner_delete_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'forward');
	}

	/**
	 * 设置banner的启用或禁用
	 * Create by 2012-4-9
	 * @author liuw
	 */
	public function onoff(){
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		$op = $this->get('do');
		$op = isset($op)&&!empty($op)?$op:'on';
		$id = $this->get('id');
		switch($op){
			case 'on'://启用banner
				//检查目前启用了几个banner
// 				$count = $this->db->where('type', $type)->where('disable',0,FALSE)->count_all_results('ClientBanner');
// 				if($count && $count == 5){
					//已经启用了5个，提示不能再启用
// 					$this->error($this->lang->line('banner_has_on_five'), $this->_index_rel, $this->_index_uri, 'forward');
// 				}else{
					//获得当前最高排序的Banner的排序值
					$rs = $this->db->select('orderValue')->where('type', $type)->where('disable',0,FALSE)->order_by('orderValue', 'desc')->limit(1)->get('ClientBanner')->first_row('array');
					$edit = array('orderValue'=>intval($rs['orderValue'])+10, 'disable'=>0);
					//启用
					$this->db->where('id', $id)->update('ClientBanner', $edit);
					$this->success($this->lang->line('banner_on_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'forward');
// 				}
				break;
			case 'off'://禁用banner
				$edit = array('orderValue'=>0, 'disable'=>1);
				$this->db->where('id', $id)->update('ClientBanner', $edit);
				$this->success($this->lang->line('banner_off_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'forward');
				break;
		}
	}

	/**
	 * 调整排序值
	 * Create by 2012-4-11
	 * @author liuw
	 */
	public function set_top(){
		//id
		$id = $this->get('id');
		$type = $this->get('type');
		$type = !empty($type) ? intval($type) : 0;
		$this->assign('type', $type);
		if($this->is_post()){
			$orderValue = intval($this->post('orderValue'));
			//检查排序值是否合法
			if($orderValue <= 0)
			$this->error('排序值不能为0或负数');
			else{
				//检查排序值是否已被使用
				$oldOrder = intval($this->post('oldOrder'));
				$disable = intval($this->post('disable'));
				$rs = $this->db->where('disable', $disable)->where('orderValue', $orderValue)->count_all_results('ClientBanner');
				if($rs > 0)
				$this->error($this->lang->line('banner_order_has_used'));
				else{
					//更新
					$this->db->where('id', $id)->update('ClientBanner', array('orderValue'=>$orderValue));
					$this->success($this->lang->line('do_success'), $this->_index_rel.'_type_'.$type, $this->_index_uri.'/type/'.$type, 'closeCurrent');
				}
			}
		}else{
			//banner属性
			$info = $this->db->where('id', $id)->limit(1)->get('ClientBanner')->first_row('array');
			$this->assign('info', $info);
			$this->display('set_top', 'banner');
		}
	}

	private function listBanner($type){
		$disables = array();
		//未启用的banner列表
		$keyword = $this->post('keyword');
		$keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		//查总数
		if(!empty($keyword)) {
			$this->db->like('concat(name, title)',$keyword, false);
			$this->assign('keyword', $keyword);
		}
		$count = $this->db->where('type', $type)->where('disable',1,FALSE)->count_all_results('ClientBanner');
// 		echo $this->db->last_query();
		if($count){
			//分页
			$parr = $this->paginate($count);
			//列表
			$this->db->where('disable',1,FALSE);
			if(!empty($keyword)) {
				$this->db->like('concat(name, title)',$keyword, false);
			}
			$query = $this->db->where('type', $type)->order_by('id','desc')->limit($parr['per_page_num'],$parr['offset'])->get('ClientBanner');
			foreach($query->result_array() as $row){
				//检查链接类型
				if(!isset($row['content']) || empty($row['content']))
				$row['content'] = '无链接';
				else{
					$links = explode('//',$row['content']);
					switch($links[0]){
						case 'place:'://地点
							//查询到地点名称
							$rs = $this->db->select('placename')->where('id',intval($links[1]),FALSE)->limit(1)->get('Place')->first_row('array');
							$row['content'] = '地点: '.$rs['placename'];
							break;
						case 'user:'://用户
							//查询用户昵称，没有昵称就返回用户名
							$rs = $this->db->select('username,nickname')->where('id',intval($links[1]),FALSE)->limit(1)->get('User')->first_row('array');
							$row['content'] = '用户: '.(isset($rs['nickname'])&&!empty($rs['nickname'])?$rs['nickname']:$rs['username']);
							break;
						case 'badge:'://勋章
							//查询勋章名称
							//$rs=
							$row['content'] = '勋章: ';
							break;
						default:break;//HTTP链接
					}
				}
				$disables[$row['id']] = $row;
			}
		}
		return $disables;
	}

	/**
	 * 查询关联数据
	 * Create by 2012-4-20
	 * @author liuw
     * 
     * 方法已废弃，请使用Controller lookup里面的
	 */
	// public function lookup_datas(){
		// $type = $this->get('type');
		// $keyword = $this->post('keyword');
		// $keyword = isset($keyword)&&!empty($keyword)?$keyword:'';
		// //确定表和搜索字段
		// $table = $column = $order_col = $label = '';
		// switch($type){
			// case 'place':$table = 'Place';$column = 'placename';$label = '地点名称'; $order_col = 'createDate';break;
			// case 'user':$table = 'User';$column = 'username'; $label = '用户名'; $order_col = 'createDate';break;
			// case 'badge':$table = ''; $column = ''; $label = '勋章名称'; $order_col = '';break;
		// }
		// $this->assign(compact('type', 'label', 'keyword'));
		// //查询数据总数
		// if($type === 'place')
		// $this->db->where('status', 0);
		// if(!empty($keyword))
		// $this->db->like($column, $keyword);
		// $count = $this->db->count_all_results($table);
		// if($count){
			// //分页
			// $parr = $this->paginate($count);
			// //数据
			// $list = array();
			// if($type === 'place')
			// $this->db->where('status', 0);
			// if(!empty($keyword))
			// $this->db->like($column, $keyword);
			// $query = $this->db->order_by($order_col, 'desc')->limit($parr['per_page_num'], $parr['offset'])->get($table);
			// foreach($query->result_array() as $row){
				// $data = array();
				// //差异化封装数据
				// switch($type){
					// case 'place':
						// $data['name'] = $row['placename'];
						// $data['address'] = $row['address'];
						// $data['createDate'] = $row['createDate'];
						// break;
					// case 'user':
						// $data['name'] = $row['username'];
						// $data['realName'] = $row['realName'];
						// $data['nickname'] = $row['nickname'];
						// $data['description'] = $row['description'];
						// break;
					// case 'badge':
						// break;
				// }
				// $list[$row['id']] = $data;
			// }
			// $this->assign('list', $list);
		// }
		// $this->assign('is_dialog', true);
// 
		// $this->display('lookup', 'banner');
	// }

}
 
// File end