<?php
/**
 * POST详情
 * Create by 2012-12-19
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Review extends Controller{
	
	var $_tables;
	
	public function __construct(){
		parent::__construct();
		$this->config->load('config_post');
		$this->load->model('post_model', 'm_post');
		$this->_tables = $this->config->item('tables');
	}
	
	/**
	 * POST详情页
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $post_id
	 * @param int $page
	 */
	public function index($post_id, $page=1){
		//POST详情
		$post = $this->m_post->get_post($post_id);
		if(!$post){
			show_404();
		}
		//$title = $this->lang->line('site_current_title').($post['owner']['nickname']?$post['owner']['nickname']:$post['owner']['username']).'在'.$post['placename'].'发布的'.($post['type']==2?'点评':'图片');
		
		//$this->assign('title', $title);
		
		//地点的图片数
		$condition = array(
			'where'=>array($this->_tables['post'].'.type < '=>5 , $this->_tables['post'].'.photo != '=>'', $this->_tables['post'].'.status <'=>2), // status < 2
		);
		
		if($post['type'] < 4) {
		    $condition['where'][$this->_tables['post'].'.placeId'] = $post['placeId'];
		}
		
		
		$imgs = array();
		
		if(!$post['placeId']){
			$cache_id = 'cache_place_0_post_count';
			$post['place_photo_count'] = xcache_get($cache_id);
			
			if(!$post['place_photo_count'] && $post['place_photo_count']!==0){
				$post['place_photo_count'] = $this->m_post->count_post($condition);
				xcache_set($cache_id, $post['place_photo_count'], 1800);
			}
		}else{
			$post['place_photo_count'] = $this->m_post->count_post($condition);
		}
		
		$list = array();
		//图片列表
		if($post['place_photo_count']){
			$condition['select'] = array($this->_tables['post'].'.photo', $this->_tables['post'].'.id');
			$img_arr = array();
			//计算查询数量，当前POST类型不是图片时查询最新的5条，是图片时查询左右各两条
			$size = $this->config->item('review_pic_size');
			$side = $post['type'] != 3 ? $size : floor((float)$size / 2);
			//当前POST非图片的情况
			if($post['type'] != 3){
				$condition['limit']=array('size'=>$side, 'offset'=>0);
				$list = $this->m_post->list_post($condition, 'thmdp');
				$img_prev = $list[0]['id'];
				$img_next = $list[1]['id'];
			}else{
				$limit = array('offset'=>0);
				$c_l = $c_r = $condition;
				$c_l['where'][$this->_tables['post'].'.id < '] = $post_id;
				$c_r['where'][$this->_tables['post'].'.id > '] = $post_id;
				//左边的长度
				$l_count = $this->m_post->count_post($c_l);
				//右边的长度
				$r_count = $this->m_post->count_post($c_r);
				
				$r_side = $l_count >= $side ? $side : abs($l_count + 1 - $size);
				$l_side = $r_count >= $side ? $side : abs($r_count + 1 - $size);
				
				//左边
				if($l_count){
					$limit['size'] = $l_side;
					$c_l['limit'] = $limit;
					$left_arr = $this->m_post->list_post($c_l, 'thmdp');
					unset($condition['where'][$this->_tables['post'].'.id < ']);
					$img_prev = $left_arr[0]['id'];
					//反转
					$left_arr = array_reverse($left_arr);
					$list = array_merge($left_arr, $list);
					unset($left_arr);
				}
				
				//当前的
				$list[] = array('id'=>$post_id, 'photoName'=>$post['photoName'],'class'=>'active');
				
				//右边
				if($r_count){
					$limit['size'] = $r_side;
					$c_r['limit'] = $limit;
					$c_r['order'] = array($this->_tables['post'].'.createDate', 'asc');
					$right_arr = $this->m_post->list_post($c_r,'thmdp');
				//	$right_arr = array_reverse($right_arr);
					$img_next = $right_arr[0]['id'];
					$list = array_merge($list, $right_arr);
					unset($right_arr);
				}
				empty($img_prev) && $img_prev = $post_id;
				empty($img_next) && $img_next = $post_id;
				unset($c_l, $c_r, $l_count, $r_count, $l_side, $r_side);
			}
			$imgs = compact('list', 'img_prev', 'img_next');
		}
		empty($imgs) && $imgs = array();
		//右边-用户最新的4张图片
		$user_imgs = array(); 
		if($post['owner']['photoCount']){
			$condition = array(
			//	'select' => array($this->_tables['post'].'.id',$this->_tables['post'].'.photoName'),
				'where' => array($this->_tables['post'].'.uid'=>$post['uid'],$this->_tables['post'].'.photo != '=>'',$this->_tables['post'].'.type <= '=>4, $this->_tables['post'].'.status < '=>2),
				'limit' => array('size'=>4, 'offset'=>0),
				'order' => array('id','desc')
			);
			$user_imgs = $this->m_post->list_post($condition, 'thmdp');
		}
		
		//右边-可能感兴趣的4条POST
		$interest = $this->m_post->list_post_for_tag($post_id, 4, 0, 'thmdp');
		
		//回复
		$this->list_reply($post_id, $page);
		$this->title = strip_tags($post['title']);
		//
		//页面显示
		$this->assign(compact('post', 'imgs', 'user_imgs', 'interest'));
		$this->display('index');
	}
	
	/**
	 * 去刷新POST的一些缓存信息
	 * @param POST的ID号 $id
	 */
	public function flush_post_cache($id = 0) {
	    $id = formatid($id);
	    if($id > 0) {
            // 更新回复缓存
            get_data_ttl('post_replies', $id, 60);
            // 更新POST的标签
            get_data_ttl('post_tags', $id, 1800);
	    }
	}
	
	/**
	 * 回复列表
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $post_id
	 */
	public function list_reply($post_id, $page=1){
		//回复总数
		$pagesize=10;
		
		$post = $this->m_post->select(array('id'=>$post_id));		
		$count = $post ? $post['replyCount'] : 0;
		if($count){
			//分页
			$parr = $this->paginate('/review', $count, $page, array('post_id'=>$post_id), $pagesize);
			//数据
			$this->load->model('reply_model', 'm_reply');
			$list = $this->m_reply->list_reply($post_id, 'post', $parr['size'], $parr['offset']);
		}
	//	print_r($list);exit;
		empty($list) && $list = array();
		$this->assign(compact('count', 'list'));
	//	$this->display('reply');
	}
}   
   
 // File end