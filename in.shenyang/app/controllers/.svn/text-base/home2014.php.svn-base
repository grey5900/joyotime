<?php
// Define and include
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Home2014 extends MY_Controller {
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * 热点推荐
	 */
	function hots($do = 'post') {
		$keywords = trim($this->post('keywords'));
		$where_sql = "type = '" . $this->config->item('home_digest')['home_' . $do]['id'] . "'";
		if($keywords !== '') {
			$where_sql .= " AND name LIKE '%{$keywords}%'";
			$keywords = dstripslashes($keywords);
		}
		
		$total_num = $this->db->where($where_sql)->from('HomePageDataBase')->count_all_results();
		$paginate = $this->paginate($total_num);
		$list = $this->db->order_by('rankOrder', 'desc')
			->limit($paginate['per_page_num'], $paginate['offset'])
			->where($where_sql)->get('HomePageDataBase')->result_array();
		
		$this->assign(compact('keywords', 'list'));
		
		$this->display($do);
	}
	
	/**
	 * 添加，所有的能同意最好统一到一起
	 */
	function add($do = 'post', $id = 0) {
		$id = intval($id);
		if($id > 0) {
			// 获取原来的数据
			$data = $this->db->get_where('HomePageDataBase', array('id' => $id))->row_array();
		}
		
		if($this->is_post()) {
			$name = trim($this->post('name'));
			$title = trim($this->post('title'));
			$image = $this->post('image');
			$rank_order = intval($this->post('rank_order'));
			$expire_date = $this->post('expire_date');
			$link = $this->post('link');
			$point = intval($this->post('point'));
			
			if (empty($name) && empty($image) && empty($link)) {
				$this->error('请检查标题、图片或链接，不能都为空');
			}
			
			$link_type = $this->post('link_type');
// 			if($link_type == 'topic') {
// 				// 需要处理井号
// 				$title = '#' . trim($title, '#') . '#';
// 			}
			$item_type = 0;
			$item_id = 0;
			$schema = $this->config->item('home_link_type')[$link_type]['schema'];
			if (!in_array($link_type, array('http', 'any'))) {
				// 那么就都是填写的id号
				$item_type = $this->config->item('home_link_type')[$link_type]['type'];
				$item_id = intval(ltrim($link, $schema . '://'));
				$link = $schema . '://' . $item_id;
			} else {
				$link = ($schema?($schema . '://'):'') . ltrim($link, $schema . '://');
			}
			
			$extensions = array();
			if($link_type == 'user') {
				// 需要处理头像
				$user = $this->db->select('avatar')->get_where('User', array('id' => $item_id))->row_array();
				if(empty($user)) {
					$this->error('错误的用户ID号');
				}
				if(empty($user['avatar'])) {
					$this->error('该用户没有头像哦，亲');
				}
				$image = $user['avatar'];
			} elseif ($link_type == 'product') {
				$product = $this->db->select('price')->get_where('Product', array('id' => $item_id))->row_array();
				$extensions['price'] = intval($product['price']);
			} elseif ($link_type == 'place') {
				$place = $this->db->select('level')->get_where('Place', array('id' => $item_id))->row_array();
				$extensions['level'] = floatval($place['level']);
			} elseif ($link_type == 'event') {
				$link = $this->config->item('web_site') . '/event_new/detail/' . $item_id;
			}
			if($do == 'topic') {
				// 提交话题且图片为空的话，去获取默认的精彩话题图片
				$topic = $this->db->select('description, icon, hotThreshold')->get_where('Topic', array('id' => $item_id))->row_array();
				if(empty($topic)) {
					$this->error('错误的话题ID号');
				}
				if(empty($image)) {
					$image = $topic['icon'];
				}
				$extensions['hot'] = intval($topic['hotThreshold']);
				$title = $topic['description'];
			}
			if ($point>0) {
				$extensions['repay_point'] = intval($point);
			}
			
			$data1 = array(
					'name' => $name,
					'title' => $title,
					'image' => $image,
					'hyperLink' => $link,
					'extensions' => $extensions?encode_json($extensions):'',
					'rankOrder' => $rank_order,
					'expireDate' => $expire_date,
					'itemType' => $item_type,
					'itemId' => $item_id
			);
			
			if(empty($data)) {
				// 添加
				$data1['type'] = $this->config->item('home_digest')['home_' . $do]['id'];
			}
			
			if ($data) {
				// 修改
				$b = $this->db->where(array('id' => $id))->update('HomePageDataBase', $data1);
			} else {
				$b = $this->db->insert('HomePageDataBase', $data1);
			}
			
			if($b) {
				$this->success(($data?'修改':'添加').'成功', 'home2014_hots_' . $do, '/home2014/hots/' . $do, 'closeCurrent');
			} else {
				$this->success(($data?'修改':'添加').'失败');
			}
		}
		
		if($id > 0) {
			foreach($this->config->item('home_link_type') as $key => $value) {
				if($value['type'] == $data['itemType']) {
					$data['hyperLink'] = ltrim($data['hyperLink'], $value['schema'] . '://');
					$data['link_type'] = $key;
				}
			}
		}
		
		if($data) {
			$data['extensions'] = json_decode($data['extensions'], true);
		}
		$this->assign('label_title', $this->config->item('home_digest')['home_' . $do]['label']);
		$this->assign('data', $data);
		$this->assign('conf_home_digest', $this->config->item('home_digest'));
		$this->assign('link_type', $this->config->item('home_link_type'));
		$this->assign('do', $do);
		$this->display('add');
	}
	
	/**
	 * 修改统一一个
	 */
	function edit($do = 'post', $id = 0) {
		$this->add($do, $id);
	}
	
	function del($do = 'post', $id = 0) {
		$id = intval($id);
		if($id > 0) {
			$this->db->where(array('id' => $id, 'type' => $this->config->item('home_digest')['home_' . $do]['id']))->delete('HomePageDataBase');
		}
		$this->success('删除成功', 'home2014_hots_post', '/home2014/hots_post');
	}
}