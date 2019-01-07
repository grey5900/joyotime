<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Place表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Place_Model extends MY_Model {
	//配置
	var $post_conf;
	var $assert_conf;
	
	function __construct(){
		parent::__construct();
		$this->config->load('config_common');
		$this->post_conf = $this->config->item('post_type');
		$this->assert_conf = $this->config->item('assert_type');
	}
	
	/**
	 * 查询地点详情
	 * Create by 2012-12-17
	 * @author liuweijava
	 * @param int $place_id
	 * @return array
	 */
	public function get_info($place_id){
		//PLACE表数据
		$this->db->select($this->_tables['place'].'.*, '.$this->_tables['placecategory'].'.icon AS c_icon ,'.$this->_tables['placeowncategory'].'.placeCategoryId' );
		$this->db->from($this->_tables['place']);
		$this->db->join($this->_tables['placeowncategory'], $this->_tables['placeowncategory'].'.placeId = '.$this->_tables['place'].'.id', 'left');
		$this->db->join($this->_tables['placecategory'], $this->_tables['placecategory'].'.id = '.$this->_tables['placeowncategory'].'.placeCategoryId', 'left');
		$place = $this->db->where(array($this->_tables['place'].'.id'=>$place_id))->limit(1)->get()->first_row('array');
		
		if(empty($place))
			return array();
		//地点图标
		$place['icon'] = empty($place['icon']) ? image_url($place['c_icon'], 'common', 'odp') : image_url($place['icon'],'common', 'odp');
		//统计访客数
		//$place['visitorCount'] = $this->get_visitor_count($place_id);
		//统计收藏次数
		
		$place['favoriteCount'] = $this->get_favorite_count($place_id);
		//如果当前有已登录用户，检查是否已收藏
		if(!empty($this->auth)){
			$query = $this->db->where(array('itemType'=>$this->assert_conf['type'], 'itemId'=>$place_id, 'uid'=>$this->auth['uid']))->count_all_results($this->_tables['userfavorite']);
			$place['has_favorited'] == $query > 0 ? 1 : 0;
			unset($query);
		}
		
		//地点地主
		if($place['mayorId']){
			$this->db->select($this->_tables['user'].'.*, '.$this->_tables['userlevelconstans'].'.level');
			$this->db->join($this->_tables['userlevelconstans'], $this->_tables['userlevelconstans'].'.minExp <= '.$this->_tables['user'].'.exp AND '.$this->_tables['userlevelconstans'].'.maxExp > '.$this->_tables['user'].'.exp', 'inner');
			$mayor = $this->db->where($this->_tables['user'].'.id', $place['mayorId'])->get($this->_tables['user'])->first_row('array');
			empty($mayor) && $mayor = array();
			//头像			
			if(!empty($mayro)){
				$mayor['name'] = !empty($mayor['nickname']) ? $mayor['nickname'] : $mayor['username'];
				$mayor['avatar_url'] = image_url($mayor['avatar'], 'head');
			    $mayor['avatar_m'] = image_url($mayor['avatar'], 'head', 'hmdp');
			    $mayor['avatar_h'] = image_url($mayor['avatar'], 'head', 'hhdp');
			    $place['mayor'] = $mayor;
			}
			unset($mayor);
		}
		
		//地点星级
		$place['star'] = ceil($place['level']);
		
		//会员卡
		$mcard = array();
		if($place['brandId']){
			$mcard = $this->db->where(array('brandId'=>$place['brandId'], 'isBasic'=>1, 'status'=>0))->order_by('createDate', 'desc')->limit(1)->get($this->_tables['brandmembercard'])->first_row('array');
			!empty($mcard) && !empty($mcard['image']) && $mcard['image_url'] = image_url($mcard['image'], 'thumb');
		}
		
		empty($mcard) && $mcard = array();
		$place['member_card'] = $mcard;
		unset($mcard);
		
		//地点的模型，只显示封面
		$this->db->select($this->_tables['placeownspecialproperty'].'.*, '.$this->_tables['placemodule'].'.name');
		$this->db->join($this->_tables['placemodule'], $this->_tables['placemodule'].'.id = '.$this->_tables['placeownspecialproperty'].'.moduleId', 'left');
		$profiles = $this->db->where(array($this->_tables['placeownspecialproperty'].'.placeId'=>$place_id, $this->_tables['placeownspecialproperty'].'.status'=>0))->order_by($this->_tables['placeownspecialproperty'].'.rankOrder', 'desc')->get($this->_tables['placeownspecialproperty'])->result_array();
		
		//格式化
		$profiles_list = array();
		foreach($profiles as $row){
			//图片
			if(!empty($row['images'])){
				$images = explode(',', $row['images']);
				$row['images'] = array();
				foreach($images as $img){
					$row['images'][] = image_url($img, 'common');
				}
			}
			$row['show_name'] = $row['title']?$row['title']:$row['name'];
			$profiles_list[$row['id']] = $row;
		}
		unset($profiles);
		$place['profiles'] = $profiles_list;
		
		return $place;
	}
	
	/**
	 * 统计访客数量
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @return int
	 */
	public function get_visitor_count($place_id){
		$sql = 'SELECT COUNT(*) AS visitorCount FROM (SELECT DISTINCT uid FROM '.$this->_tables['post'].' WHERE placeId=? AND type=? GROUP BY uid) AS tmp';
		$query = $this->db->query($sql, array($place_id, $this->post_conf['checkin']))->first_row('array');
		return $query['visitorCount'];
	}
	
	/**
	 * 统计收藏数量
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @return int
	 */
	public function get_favorite_count($place_id){
		$sql = 'SELECT COUNT(*) AS favoriteCount FROM '.$this->_tables['userfavorite'].' WHERE itemType = ? AND itemId = ?';
		$query = $this->db->query($sql, array($this->assert_conf['place'], $place_id))->first_row('array');
		return $query['favoriteCount'];
	}
	
	/**
	 * 统计会员数量
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @return int
	 */
	public function get_member_count($place_id){
		$this->db->where(array($this->_tables['place'].'.id'=>$place_id, $this->_tables['userownmembercard'].'.isBasic'=>1));
		$this->db->join($this->_tables['place'], $this->_tables['place'].'.brandId='.$this->_tables['userownmembercard'].'.brandId', 'inner');
		return $this->db->count_all_results($this->_tables['userownmembercard']);
	}
	
	/**
	 * 获取收藏者列表
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $size
	 * @param int $offset
	 * @return array
	 */
	public function get_favorites($place_id, $size=40, $offset=0){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		$select = array($this->_tables['user'].'.*');
		if($uid){
			$select[] = $this->_tables['usership'].'.createDate AS followDate';
			$this->db->join($this->_tables['usership'], $this->_tables['usership'].'.beFollower = '.$this->_tables['user'].'.id AND '.$this->_tables['usership'].'.follower = '.$uid, 'left');
		}
		$this->db->select(implode(',', $select));
		$this->db->join($this->_tables['userfavorite'], $this->_tables['userfavorite'].'.uid='.$this->_tables['user'].'.id', 'inner');
		$list = $this->db->where(array($this->_tables['userfavorite'].'.itemType'=>$this->assert_conf['place'], $this->_tables['userfavorite'].'.itemId'=>$place_id))->order_by($this->_tables['userfavorite'].'.createDate','desc')->limit($size, $offset)->get($this->_tables['user'])->result_array();
		foreach($list as &$row){
			$row['avatar_url'] = image_url($row['avatar'],'head', 'hmdp');
			unset($row);
		}
		return $list;
	}
	
	/**
	 * 获取访客列表
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $size
	 * @param int $offset
	 * @return array
	 */
	public function get_visitors($place_id, $size=40, $offset=0){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		$select = array($this->_tables['user'].'.*');
		if($uid){
			$select[] = $this->_tables['usership'].'.createDate AS followDate';
			$this->db->join($this->_tables['usership'], $this->_tables['usership'].'.beFollower = '.$this->_tables['user'].'.id AND '.$this->_tables['usership'].'.follower = '.$uid, 'left');
		}
		$this->db->select(implode(',', $select));
		$this->db->join('((SELECT DISTINCT uid, MAX(createDate) AS createDate FROM '.$this->_tables['post'].' WHERE placeId='.$place_id.' AND type='.$this->assert_conf['place'].' GROUP BY uid) AS tmp ORDER BY createDate DESC)', 'tmp.uid = '.$this->_tables['user'].'.id', 'inner');
		$list = $this->db->limit($size, $offset)->get($this->_tables['user'])->result_array();
		foreach($list as &$row){
			//头像
			$row['avatar_url'] = image_url($row['avatar'], 'head', 'hmdp');
			unset($row);
		}
		return $list;
	}
	
	/**
	 * 获取会员
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param int $size
	 * @param int $offset
	 * @return array
	 */
	public function get_members($place_id, $size=40, $offset=0){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		$select = array('DISTINCT ' . $this->_tables['user'].'.*');
		if($uid){
			$select[] = $this->_tables['usership'].'.createDate AS followDate';
			$this->db->join($this->_tables['usership'], $this->_tables['usership'].'.beFollower = '.$this->_tables['userownmembercard'].'.uid AND '.$this->_tables['usership'].'.follower = '.$uid, 'left');
		}
		$this->db->select(implode(',', $select), false);
		$this->db->join($this->_tables['place'], $this->_tables['place'].'.brandId='.$this->_tables['userownmembercard'].'.brandId', 'inner');
		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['userownmembercard'].'.uid', 'inner');
		$this->db->where(array($this->_tables['place'].'.id'=>$place_id, $this->_tables['userownmembercard'].'.isBasic'=>1));
		$list = $this->db->order_by($this->_tables['userownmembercard'].'.createDate', 'desc')->limit($size, $offset)->get($this->_tables['userownmembercard'])->result_array();
		foreach($list as &$row){
			//头像
			$row['avatar_url'] = image_url($row['avatar'], 'head', 'hmdp');
			unset($row);
		}
		return $list;
	}
	
	/**
	 * 获取地点相关的新闻，10条
	 * Create by 2012-12-24
	 * @author liuweijava
	 * @param int $place_id
	 * @return array
	 */
	public function get_newes($place_id){
		//$this->db->select($this->_tables['webnews'].'.id, '.$this->_tables['webnews'].'.subject, '.$this->_tables['webnewscategory'].'.domain');
		//$this->db->join($this->_tables['webnews'], $this->_tables['webnews'].'.id = '.$this->_tables['webnewsplace'].'.newsId', 'inner');
		//$this->db->join($this->_tables['webnewscategory'], $this->_tables['webnewscategory'].'.id='.$this->_tables['webnews'].'.newsCatId', 'inner');
		//不需要查询出domain
		$this->db->select($this->_tables['webnews'].'.id, '.$this->_tables['webnews'].'.subject');
		$this->db->join($this->_tables['webnews'], $this->_tables['webnews'].'.id = '.$this->_tables['webnewsplace'].'.newsId', 'inner');
		$list = $this->db->where(array($this->_tables['webnewsplace'].'.placeId'=>$place_id, $this->_tables['webnews'].'.status'=>1))->order_by($this->_tables['webnews'].'.dateline', 'desc')->limit(10)->get($this->_tables['webnewsplace'])->result_array();
		empty($list) && $list = array();
		return $list;
	}
	
	/**
	 * 美食排行
	 */
	function delicious_places(){
		//需要是美食地点
		//点评数排行
		//10个地点
		//自然上周
		//$this->db->where('status',0);
		$start = date("Y-m-d H:i:s",strtotime("last Sunday -6 days"));
		$end = date("Y-m-d H:i:s",strtotime("last Sunday +1 day"));
		
		
		$list = $this->db->select('p.placeId id,count(p.placeId) countPost')
				 ->from($this->_tables['post'].' p')
				 ->join($this->_tables['place'].' pl','pl.id=p.placeId','inner')
				 ->join($this->_tables['placeowncategory'].' poc','poc.placeId = p.placeId','inner')
				 ->join($this->_tables['placecategoryship'].' pcs','pcs.child = poc.placeCategoryId','left')
				 ->where("( poc.placeCategoryId = 1 or pcs.parent=1) and (p.createDate >= '{$start}' and p.createDate < '{$end}') and p.type = 2 and pl.status=0 and p.status<2",null,false)
				 ->group_by('p.placeId')
				 ->order_by('countPost','desc')
				 ->limit(10)
				 ->get()->result_array();
		foreach($list as &$row){
			//点评数 icon id placename
			$p_info = get_data('place',$row['id']);
			$row['icon'] = $p_info['icon'];
			$row['placename'] = $p_info['placename'];
		}		 
		return $list;
	}
	
}