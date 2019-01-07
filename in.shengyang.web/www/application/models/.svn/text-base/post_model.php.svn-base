<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Post表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class Post_Model extends MY_Model {
	
	var $hot_conf;
	
	public function __construct(){
		parent::__construct();
		//通用配置
		$this->config->load('config_common');
		$this->hot_conf = $this->config->item('post_hot_params');
		//通用函数
		$this->load->helper('cache');
		//其他数据模型
		$this->load->model('usership_model', 'm_uship');
		$this->load->model('userfavorite_model', 'm_ufav');
		$this->load->model('userpraise_model', 'm_praise');
	}
	
	/**
	 * 查询用户的签到足迹
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 */
	public function get_route($uid){
		$this->db->select($this->_tables['place'].'.id, '.$this->_tables['place'].'.placename, '.$this->_tables['place'].'.latitude, '.$this->_tables['place'].'.longitude');
		$this->db->join($this->_tables['place'].'', $this->_tables['place'].'.id = '.$this->_tables['post'].'.placeId', 'inner');
		$list = $this->db->where(array($this->_tables['post'].'.uid'=>$uid, $this->_tables['post'].'.type'=>1, $this->_tables['post'].'.status < '=>2))->order_by($this->_tables['post'].'.createDate', 'desc')->limit(10)->distinct()->get($this->_tables['post'])->result_array();
		return $list;
	}
	
	public function get_mayors($uid){
		$mayors = $this->db->select("latitude,longitude")->where("mayorId",$uid)->order_by("mayorDate desc")->limit(10)->get($this->_tables['place'])->result_array();
		return $mayors;
	}
	
	/**
	 * 获取POST时间轴，根据$type确定类型
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $id
	 * @param string $type
	 */
	public function get_user_timeline($id){
		$month_cn = $this->config->item('month_CN');
		$col_id = 'uid';
		$this->db->select('YEAR(createDate) AS c_year, MONTH(createDate) AS c_month');
		$this->db->distinct();
		$this->db->where(array($col_id=>$id, 'status <'=>2 ));  // status < 2
		$this->db->where_in("type",array(2,3,4));
		$list = array();
		$query = $this->db->group_by('createDate')->order_by('createDate', 'desc')->get($this->_tables['post'])->result_array();
		foreach($query as $k=>$row){
			$list[$row['c_year']][$k] = array($row['c_month'], $month_cn[intval($row['c_month'])]);	
		}
		unset($query);
		return $list;
	}
	
	/**
	 * 获取地点的POST时间轴，需要检查是否已收藏/分享/赞
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param int $place_id
	 * @param string $order 排序规则，hot=按热度排序，new=按时间排序
	 * @param int $size 每页显示条数
	 * @param int $offset 数据游标的起始位置
	 */
	public function get_post_timeline($place_id, $order='hot', $size=50, $offset=0){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		//返回字段
// 		$select = array(
// 			$this->_tables['post'].'.*',
// 			$this->_tables['user'].'.avatar',
// 			$this->_tables['user'].'.nickname',
// 			$this->_tables['user'].'.username',
// 			$this->_tables['place'].'.placename'
// 		);
		$select = array($this->_tables['post'].'.*');
// 		if($uid){
// 			//取好友的备注
// 			$select[] = $this->_tables['usermnemonic'].'.mnemonic';
// 		}
		$list = array();
		if($order == 'hot'){//按热度排序
			//计算热度
			$score = '(((viewCount+1)*'.$this->hot_conf['P']['hit'].
					'+replyCount*'.$this->hot_conf['P']['reply'].
					'+praiseCount*'.$this->hot_conf['P']['praise'].
					'+shareCount*'.$this->hot_conf['P']['share'].
					')/POW(ROUND(('.(time()+8*3600).'-UNIX_TIMESTAMP(createDate))/3600,'.$this->hot_conf['T']['poor'].') + 2, '.$this->hot_conf['HOT']['gravity'].')) AS score';
			$select[] = $score;
			$this->db->order_by('score', 'desc');
		}
		$this->db->order_by('createDate', 'desc');
		$this->db->select(implode(',', $select), false);
// 		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id = '.$this->_tables['post'].'.uid', 'inner');
// 		$this->db->join($this->_tables['place'], $this->_tables['place'].'.id = '.$this->_tables['post'].'.placeId', 'inner');
// 		if($uid){
// 			$this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
// 		}
		$list = $this->db->where(array('status <'=>2, 'type < '=>5, 'placeId'=>$place_id))->limit($size, $offset)->get($this->_tables['post'])->result_array();
		//status < 2
		//暂时只取出type = 2 ,4（点评,YY）的内容。。
		//格式化数据
		foreach($list as &$row){
			// 到缓存中去获取
			if($uid) {
				$user_mnemonic = get_data('mnemonic', $uid . '-' . $row['uid']);
				$row['nickname'] = $user_mnemonic['mnemonic'];
			}
			// 那么去获取用户的信息
			$user = get_data('user', $row['uid']);
			$row['avatar'] = $user['avatar'];
			$row['avatar_url'] = $user['avatar_m'];
			//添加nickname
			$row['nickname'] = $user_mnemonic['mnemonic'] ? $user_mnemonic['mnemonic'] : ($user['nickname'] ? $user['nickname'] : $user['username']);
			// 获取地点名称
			$place = get_data('place', $row['placeId']);
			$row['placename'] = $place['placename'];
			
			//图片
		// 			if(!empty($row['photoName'])){
// 				$row['photoName'] = image_url($row['photoName'], 'user', 'hdp');
// 				//缓存图片尺寸
// 				$row['photoSize'] = get_data('imagesize', $row['photoName'].'||hdp');
// 			}
			if(!empty($row['photo'])){
				$row['photoName'] = image_url($row['photo'], 'user', 'hdp');
				//缓存图片尺寸
				$row['photoSize'] = get_data('imagesize', $row['photoName'].'||hdp');
			}
					
			//用户名称
			//$row['nickname'] = !empty($row['mnemonic']) ? $row['mnemonic'] : (!empty($row['nickname']) ? $row['nickname'] : $row['username']);
			
			//用户头像
			$row['avatar_url'] = image_url($row['avatar'], 'head', 'hmdp');
			//得分星级
			$row['star'] = !empty($row['level']) ? ceil($row['level']) : 0;
			!empty($row['pcc']) && $row['pcc'] = '￥'.$row['pcc'];
			$str = '';
			$vico = '';
			if($place['isVerify']) $vico = "<span class='vico'></span>";
			switch($row['type']){
				case 1: $str = '<a class="user-link" href="/user/%d">%s</a> 签到';break; //在<a href="/place/%d" class="place-link">%s</a>'.$vico.'
				case 2: $str = '<a class="user-link" href="/user/%d">%s</a> 发布的点评';break;//<a href="/place/%d" class="place-link">%s</a>'.$vico
				case 3: $str = '<a class="user-link" href="/user/%d">%s</a> 发布的图片';break;//在<a href="/place/%d" class="place-link">%s</a>'.$vico.'
				case 4: $str = '<a class="user-link" href="/user/%d">%s</a> 发布的YY';break;//在<a href="/place/%d" class="place-link">%s</a>'.$vico.'
				case 7: $str = '<a class="user-link" href="/user/%d">%s</a> 分享了';break;//在<a href="/place/%d" class="place-link">%s</a>'.$vico.'
			}
			$row['title'] = sprintf($str, $row['uid'], $row['nickname'], $row['placeId'], $row['placename']);
			//友好的时间格式
			$row['createDate'] = get_date($row['createDate']);
			//TAGS
// 			$this->db->select($this->_tables['tag'].'.id, '.$this->_tables['tag'].'.content');
// 			$this->db->join($this->_tables['tag'], $this->_tables['tag'].'.id = '.$this->_tables['postowntag'].'.tagId', 'left');
// 			$t_list = $this->db->where($this->_tables['postowntag'].'.postId', $row['id'])->get($this->_tables['postowntag'])->result_array();
// 			$tag_html = '<li><span>#%s#</span></li>';
// 			$tags = array();
// 			foreach($t_list as $t){
// 				$tags[] = sprintf($tag_html,$t['content']);
// 			}
// 			!empty($tags) && $row['tag'] = '<ul>'.implode(' ', $tags).'</ul>';
// 			empty($tags) && $row['tag'] = '';
// 			unset($tags, $t_list);
			//回复列表
// 			if($row['replyCount']){
// 				$row['replies'] = $this->get_reply($row['id']);
// 			}
			//是否已收藏和赞
// 			if($uid){
// 				//是否已赞
// 				$c_p = $this->db->where(array('itemType'=>$row['type'], 'itemId'=>$row['id'], 'uid'=>$uid))->count_all_results($this->_tables['userpraise']);
// 				$row['mpid'] = $c_p ? 1 : 0;
// 				//是否已收藏
// 				$c_f = $this->db->where(array('itemType'=>$row['type'], 'itemId'=>$row['id'], 'uid'=>$uid))->count_all_results($this->_tables['userfavorite']);
// 				$row['mfid'] = $c_f ? 1 : 0;
// 				unset($c_p, $c_f);
// 			}
            // 哎，继续优化这坨东西
			// 从缓存中获取
			// 是否赞过
			if($uid) {
			    $row['mpid'] = abs(get_data('post_praised', $uid . '-' . $row['id'] . '-1' )); //type= 1 赞
			} else {
			    $row['mpid'] = 0;
			}
			// TAGS
			$tags = get_data('post_tags', $row['id']);
			$tags = array_filter($tags);
			$row['tag'] = '';
			if($tags) {
			    $tag_html = '<li><span>%s</span></li>';
			    $tags_html = array();
			    foreach($tags as $tag){
			    				$tags_html[] = sprintf($tag_html, $tag);
			    }
			    $tags_html && ($row['tag'] = '<ul>'.implode(' ', $tags_html).'</ul>');
			}
			// 回复
			if($row['replyCount']){
			    $row['replies'] = get_data('post_replies', $row['id']);
			}
		}
		unset($row);
		return $list;
	}
	
	/**
	 * 查询POST数据长度
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param array $conditions,where=查询条件[mixed]
	 * @return int
	 */
	public function count_post($conditions=array()){
		if(!empty($conditions['where']))
			$this->db->where($conditions['where']);
		if(!empty($conditions['where_in']))
			$this->db->where_in($conditions['where_in']['column'], $conditions['where_in']['in']);
		if(!empty($conditions['like']))
			$this->db->like($conditions['like']['column'], $conditions['like']['like']);
		return $this->db->count_all_results($this->_tables['post']);
	}
	
	/**
	 * 查询POST列表
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param array $conditions,select=要查询的字段[array]；where=查询条件[mixed]；order=排序规则[array]；limit=结果集长度[array]
	 * @param string $size 图片尺寸
	 * @param bool $deal_list 仅表示不需要再去获取POST列表了
	 * @return array
	 */
	public function list_post($conditions=array(), $size='hdp', $from_user=false,$deal_list=false){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		
		if($deal_list === false)
		{
			(empty($conditions) || empty($conditions['select'])) && $conditions['select'] = array($this->_tables['post'].'.*');
			// 下面的关联查询干掉，大爷的，明明有缓存了为什么还要去查询，那要缓存做什么？
			//关联place表获得placename
	// 		$this->db->join($this->_tables['place'], $this->_tables['place'].'.id='.$this->_tables['post'].'.placeId', 'inner');
	// 		$conditions['select'][] = $this->_tables['place'].'.placename';
			//关联user表获得username和nickname
	// 		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['post'].'.uid', 'inner');
	// 		$conditions['select'][] = $this->_tables['user'].'.username';
	// 		$conditions['select'][] = $this->_tables['user'].'.nickname';
	// 		$conditions['select'][] = $this->_tables['user'].'.avatar';
			//如果当前有登录用户，需要查询用户备注
	// 		if($uid){
	// 			$conditions['select'][] = $this->_tables['usermnemonic'].'.mnemonic';
	// 			$this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
	// 		}
			$select = implode(',', $conditions['select']);
			$select && $this->db->select($select);
			//查询条件
			if(!empty($conditions['where']))
				$this->db->where($conditions['where']);
			if(!empty($conditions['where_in']))
				$this->db->where_in($conditions['where_in']['column'], $conditions['where_in']['in']);
			if(!empty($conditions['like']))
				$this->db->or_like($conditions['like']['column'], $conditions['like']['like']);
			//排序
			if(!empty($conditions['order']))
				$this->db->order_by($conditions['order'][0], $conditions['order'][1]);
			else 
				$this->db->order_by($this->_tables['post'].'.createDate', 'desc');
			if(!empty($conditions['limit']))
				$this->db->limit($conditions['limit']['size'], $conditions['limit']['offset']);	
			$list = $this->db->get($this->_tables['post'])->result_array();			
		}else{
			$list = $deal_list;
		}

		
		foreach($list as &$row){
 			$row['nickname'] = $row['mnemonic'] ? $row['mnemonic'] : ($row['nickname'] ? $row['nickname'] : $row['username']);
			// 到缓存中去获取
			
 			if($uid) {
				$user_mnemonic = get_data('mnemonic', $uid . '-' . $row['uid']);
				$row['nickname'] = $user_mnemonic['mnemonic'];
			}
			
			// 那么去获取用户的信息
			$user = get_data('user', $row['uid']);
			
			//var_dump($user);exit;
			empty($row['nickname']) && ($row['nickname'] = ($user['nickname']?$user['nickname']:$user['username']));
			$row['avatar'] = $user['avatar'];
			$row['avatar_url'] = $user['avatar_m'];
			// 获取地点名称
			$place = get_data('place', $row['placeId']);
			$row['placename'] = $place['placename'];
			
			//图片
// 			if(!empty($row['photoName'])){
// 				$row['photoName'] = image_url($row['photoName'], 'user', $size);
// 				//缓存图片尺寸
// 				$row['photoSize'] = get_data('imagesize', $row['photoName'].'||'.$size);
// 			}
            if(!empty($row['photo'])){
				$row['photoName'] = image_url($row['photo'], 'user', $size);
				//缓存图片尺寸
				$row['photoSize'] = get_data('imagesize', $row['photoName'].'||'.$size);
			}
			//用户头像
			// 简直受不了了。为什么写了那么多HTML代码在这里面。。。。啊啊啊啊啊啊啊啊啊啊啊。简直要被弄疯
// 			$row['avatar_url'] = image_url($row['avatar'], 'head', 'hmdp');
			$str = '';
			$vico = '';
			if($place['isVerify']) $vico = "<span class='vico'></span>";
			switch($row['type']){
				case 1: $str = '%s在<a href="/place/%d" class="place-link">%s</a>'.$vico.'签到';break;
				case 2: $str = '%s点评了<a href="/place/%d" class="place-link">%s</a>'.$vico;break;
				case 3: $str = '%s在<a href="/place/%d" class="place-link">%s</a>'.$vico.'发布的图片';break;
				case 4: $str = '%s发布了';break;
				case 7: $str = '%s分享了';break;
			}
			
			$from_user && $str = substr($str, 2);
// 			$name = $row['nickname'] ? $row['nickname'] : $row['username'];
			$row['title'] = $from_user ? sprintf($str, $row['placeId'], $row['placename']) : sprintf($str, '<a href="/user/'.$row['uid'].'">'.$row['nickname'].'</a>', $row['placeId'], $row['placename']);
			//友好的时间格式
			$row['createDate_old'] = $row['createDate']; 
			$row['createDate'] = get_date($row['createDate']);
			//$row['content'] = format_html($row['content']);
			//星级
			$row['star'] = !empty($row['level']) ? ceil($row['level']) : 0;
			//人均消费
			!empty($row['pcc']) && $row['pcc'] = '￥'.$row['pcc'];
			//TAGS
// 			$this->db->select($this->_tables['tag'].'.id, '.$this->_tables['tag'].'.content');
// 			$this->db->join($this->_tables['tag'], $this->_tables['tag'].'.id = '.$this->_tables['postowntag'].'.tagId', 'left');
// 			$t_list = $this->db->where($this->_tables['postowntag'].'.postId', $row['id'])->get($this->_tables['postowntag'])->result_array();
// 			$tag_html = '<li><span>#%s#</span></li>';
// 			$tags = array();
// 			foreach($t_list as $t){
// 				$tags[] = sprintf($tag_html,$t['content']);
// 			}
// 			!empty($tags) && $row['tag'] = '<ul>'.implode(' ', $tags).'</ul>';
// 			empty($tags) && $row['tag'] = '';
// 			unset($tags, $t_list);            
			//回复列表
// 			if($row['replyCount']){
// 				$row['replies'] = $this->get_reply($row['id']);
// 			}
			
			//update 2013/1/6
			//是否已经赞过
//  			$this->load->model("userpraise_model","m_userpraise");
// 			$is_praised = $this->m_userpraise->check_praise($this->auth['uid'],$row['id'],$row['type']);
//  			$row['is_praised'] = $is_praised;
            
			// 从缓存中获取
            // 是否赞过
            if($this->auth['uid']) {
			    $row['is_praised'] = get_data('post_praised', $this->auth['uid'] . '-' . $row['id'] . '-1' );
			} else {
			    $row['is_praised'] = 1;
			}
           
			// TAGS
			$tags = get_data('post_tags', $row['id']);
			$row['tag'] = '';
			$tags = array_filter($tags);
			if($tags) {
    			$tag_html = '<li><span>%s</span></li>';
    			$tags_html = array();
    			foreach($tags as $tag){
    				$tags_html[] = sprintf($tag_html, $tag);
    			}
    			$tags_html && ($row['tag'] = '<ul>'.implode(' ', $tags_html).'</ul>');
			}
			// 回复
			if($row['replyCount']){
			    $row['replies'] = get_data_ttl('post_replies', $row['id'], 600);
			}
			
		}
		unset($row);
		
		return $list;
	}
	
	/**
	 * 更新统计数
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $post_id
	 * @param string $stat_col
	 * @param boolean $is_minus true=减少，false=增加
	 */
	public function update_stat_count($post_id, $stat_col, $is_minus=false){
		$set = $stat_col.($is_minus?'-1':'+1');
		$this->db->where('id', $post_id)->set($stat_col, $set, false)->update($this->_tables['post']);
	}
	
	/**
	 * 获取图片尺寸
	 * Create by 2012-12-18
	 * @author liuweijava
	 * @param string $image_name
	 * @return array
	 */
	public function image_wh($image_name, $size='thweb'){
		$image = image_url($image_name, 'user', $size);
		list($w, $h) = getimagesize($image);
		//重算高度，宽度固定为220
		if($size === 'mdp'){
			$s_w = 220;
			$s_h = floor(((float)$h) * ((float)$s_w / $w));
			$w = $s_w;
			$h = $s_h;
		}
		return compact('w', 'h');
	}
	
	/**
	 * 获取POST详情
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $post_id
	 * @return array
	 */
	public function get_post($post_id){
		//POST数据，包含用户和地点信息
		$this->db->select($this->_tables['post'].'.*, '.$this->_tables['place'].'.placename, '.$this->_tables['place'].'.photoCount');
		$this->db->join($this->_tables['place'], $this->_tables['place'].'.id='.$this->_tables['post'].'.placeId', 'left');
		$info = $this->db->where(array($this->_tables['post'].'.id'=>$post_id,$this->_tables['post'].'.status < '=>2))->limit(1)->get($this->_tables['post'])->first_row('array');
		if(!empty($info)){
			//格式化数据
			!empty($info['photo']) && ($info['photoName'] = image_url($info['photo'], 'user'));
			$info['star'] = !empty($info['level']) ? ceil($info['level']) : 0;
			
			!empty($info['pcc']) && $info['pcc'] = '￥'.$info['pcc'];
			$info['createDate'] = substr($info['createDate'], 0, -3);
			//用户信息
			$owner = get_data('user', $info['uid']);
			//检查是否已关注,0=未关注，1=已关注，2=不可以关注
			if(empty($this->auth)){
				$owner['is_followed'] = 0;
			}elseif($this->auth['uid'] == $info['uid']){
				$owner['is_followed'] = 2;
			}elseif($this->m_uship->check_ship($this->auth['uid'], $info['uid'])){
				$owner['is_followed'] = 1;
			}else{
				$owner['is_followed'] = 0;
			}
			$info['owner'] = $owner;
			unset($owner);
			//检查是否已收藏，0=未收藏，1=已收藏，2=不可以收藏
			(empty($this->auth) || $this->auth['uid'] == $info['uid']) && $info['is_favorite'] = 2;
			!empty($this->auth) && $this->auth['uid'] != $info['uid'] && $info['is_favorite'] = $this->m_ufav->check_favorite($this->auth['uid'], $post_id, $info['type']) ? 1 : 0;
			//检查是否已赞，0=未赞，1=已赞，2=不可以赞
			(empty($this->auth) || $this->auth['uid'] == $info['uid']) && $info['is_praise'] = 2;
			!empty($this->auth) && $this->auth['uid'] != $info['uid'] && $info['is_praise'] = $this->m_praise->check_praise($this->auth['uid'], $post_id, $info['type']) ? 1 : 0;
			//上一条，-1=没有上一条
			$prev = $this->db->select('id')->where(array('id < '=>$post_id, 'type'=>$info['type'], 'status < '=>2, 'placeId'=>$info['placeId']))->order_by('id', 'desc')->limit(1)->get($this->_tables['post'])->first_row('array');
			$info['prev_id'] = empty($prev) ? -1 : $prev['id'];
			//下一条，-1=没有下一条
			$next = $this->db->select('id')->where(array('id > '=>$post_id, 'type'=>$info['type'], 'status < '=>2, 'placeId'=>$info['placeId']))->order_by('id', 'asc')->limit(1)->get($this->_tables['post'])->first_row('array');
			$info['next_id'] = empty($next) ? -1 : $next['id'];
			//POST标题
			$str = '';
			$vico = '';
			if($info['isVerify']) $vico = "<span class='vico'></span>";
			switch($info['type']){
				case 1: $str = '<a href="/user/%d">%s</a>在<a href="/place/%d">%s</a>'.$vico.'签到';break;
				case 2: $str = '<a href="/user/%d">%s</a>点评了<a href="/place/%d">%s</a>'.$vico;break;
				case 3: $str = '<a href="/user/%d">%s</a>在<a href="/place/%d">%s</a>'.$vico.'发布的图片';break;
// 				case 4: $str = '<a href="/user/%d">%s</a>在<a href="/place/%d">%s</a>'.$vico.'发布的YY';break;
				case 4: $str = '<a href="/user/%d">%s</a>发布的YY';break;
				case 7: $str = '<a href="/user/%d">%s</a>分享了';break;
			}
			$name = $info['owner']['nickname'] ? $info['owner']['nickname'] : $info['owner']['username'];
			$info['title'] = sprintf($str, $info['uid'], $name, $info['placeId'], $info['placename']);
			//$info['content'] = format_html($info['content']);
			
			// TAGS
			$tags = get_data('post_tags', $info['id']);
			$tags = array_filter($tags);
			$info['tag'] = '';
			if($tags) {
    			$tag_html = '<li><span>%s</span></li>';
    			$tags_html = array();
    			foreach($tags as $tag){
    				$tags_html[] = sprintf($tag_html, $tag);
    			}
    			$tags_html && ($info['tag'] = '<ul>'.implode(' ', $tags_html).'</ul>');
			}
		}
		empty($info) && $info = array();
		return $info;
	}
	
	/**
	 * 查询TAG关联的POST列表
	 * Create by 2012-12-19
	 * @author liuweijava
	 * @param int $post_id
	 * @param int $size
	 * @param int $offset
	 * @param string $pic_size
	 * @return array
	 */
	public function list_post_for_tag($post_id, $size=0, $offset=0, $pic_size='odp'){
		$uid = false;
		!empty($this->auth['uid']) && $uid = $this->auth['uid'];
		//查询指定POST的TAG
		$tlist = $this->db->select('tagId')->where('postId', $post_id)->get($this->_tables['postowntag'])->result_array();
		$tags = array();
		foreach($tlist as $k=>$v){
			$tags[] = $v['tagId'];
		}
		$p = $this->select(array('id'=>$post_id));
		//查询关联的POST
		$select = array(
			$this->_tables['post'].'.*',
			$this->_tables['place'].'.placename',
			$this->_tables['user'].'.avatar',
			$this->_tables['user'].'.username',
			$this->_tables['user'].'.nickname',
		);
		if($uid){
			$select[] = $this->_tables['usermnemonic'].'.mnemonic';
		}
		
		
		if($tags){//关联了TAG的
			//2013 - 09 - 03 优化
			/*$this->db->from($this->_tables['postowntag']);
			$this->db->join($this->_tables['post'], $this->_tables['post'].'.id='.$this->_tables['postowntag'].'.postId', 'left');
			$this->db->join($this->_tables['place'], $this->_tables['place'].'.id='.$this->_tables['post'].'.placeId', 'left');
			$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['post'].'.uid', 'left');
			$uid && $this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
			$this->db->where(array($this->_tables['postowntag'].'.postId != '=> $post_id ,$this->_tables['post'].'.type < '=>5 ));
			$this->db->where_in($this->_tables['postowntag'].'.tagId', $tags);
			if($size || $offset){
				$size && $offset && $this->db->limit($size, $offset);
				$size && $this->db->limit($size);
			}
			$list = $this->db->order_by($this->_tables['post'].'.createDate', 'desc')->get()->result_array();
			*/
			
			$tags_string = implode(',',$tags);
			
			$sql ="SELECT b.*, c.placename, d.avatar, d.username, d.nickname
			  FROM  ( select m.postId 
			            from PostOwnTag m
			           inner join Post n
			              on m.tagId IN ( $tags_string ) and m.postId = n.id and m.postId !=  $post_id
			           where n.type < 5
			           order by m.postId desc
			           limit $offset,$size ) a
			 inner  JOIN Post b 
			    ON  a.postId= b.id
			  LEFT  JOIN Place c
			    ON  b.placeId = c.id
			  LEFT  JOIN User d
			    ON  b.uid = d.id ";
			
			$list = $this->db->query($sql)->result_array();
			unset($sql);
		}else{//无TAG时取同一地点下有回复的最新的POST
			$this->db->select(implode(',', $select));
			$this->db->from($this->_tables['post']);
			$this->db->join($this->_tables['place'], $this->_tables['place'].'.id='.$this->_tables['post'].'.placeId', 'inner');
			$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['post'].'.uid', 'inner');
			$uid && $this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
			$this->db->where(array($this->_tables['post'].'.placeId'=>$p['placeId'],$this->_tables['post'].'.type < '=>5, $this->_tables['post'].'.status < '=>2, $this->_tables['post'].'.id != '=>$post_id, $this->_tables['post'].'.replyCount > '=>0));
			if($size || $offset){
				$size && $offset && $this->db->limit($size, $offset);
				$size && $this->db->limit($size);
			}
			$list = $this->db->order_by($this->_tables['post'].'.createDate', 'desc')->get()->result_array();
		}
		empty($list) && $list = array();
		foreach($list as &$row){
			$row['nickname'] = $row['mnemonic'] ? $row['mnemonic'] : ($row['nickname'] ? $row['nickname'] : $row['username']);
			//标题
			$str = '';
			$vico = '';
			if($row['isVerify']) $vico = "<span class='vico'></span>";
			switch($row['type']){
				case 1: 
					$str = '<a class="user-link" href="/user/%d">%s</a>在<a href="/place/%d" class="place-link">%s</a>'.$vico.'签到';
					break;
				case 2: 
					$str = '<a class="user-link" href="/user/%d">%s</a>点评了<a href="/place/%d" class="place-link">%s</a>'.$vico;
					break;
				case 3: 
					$str = '<a class="user-link" href="/user/%d">%s</a>在<a href="/place/%d" class="place-link">%s</a>'.$vico.'发布的图片';
					break;
				case 4: 
					$str = '<a class="user-link" href="/user/%d">%s</a>发布了';
					break;
				case 7: 
					$str = '<a class="user-link" href="/user/%d">%s</a>分享了';
					break;
			}
			$name = $row['nickname'] ? $row['nickname'] : $row['username'];
			$row['title'] = sprintf($str, $row['uid'], $name, $row['placeId'], $row['placename']);
			//图片
			!empty($row['photo']) && ($row['photoName'] = image_url($row['photo'], 'user', $pic_size));
			//头像
			$row['avatar_url'] = image_url($row['avatar'], 'head', 'hmdp');
			//友好时间
			$row['createDate'] = get_date($row['createDate']);
			unset($row);
		}
		return $list;
	}
	
	/**
	 * 获取最新的两条回复
	 * Create by 2012-12-24
	 * @author liuweijava
	 * @param int $post_id
	 * @return array
	 */
	public function get_reply($post_id, $num = 2){
		$uid = !empty($this->auth['uid']) ? $this->auth['uid'] : false;
		//查询最新的两条回复
// 		$this->db->select($this->_tables['postreply'].'.*, '.$this->_tables['user'].'.avatar, '.$this->_tables['user'].'.nickname, '.$this->_tables['user'].'.username'.($uid ? ', '.$this->_tables['usermnemonic'].'.mnemonic':''));
// 		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['postreply'].'.uid', 'inner');
// 		$uid && $this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
// 		$r_list = $this->db->where($this->_tables['postreply'].'.postId', $post_id)->order_by($this->_tables['postreply'].'.createDate', 'desc')->limit($num)->get($this->_tables['postreply'])->result_array();
		
		// 这里只需要查询回复信息，用户的相关信息从缓存获取
		$this->db->where(array('itemId' => $post_id,'itemType' => 19 ,'status'=>0) );
		$r_list = $this->db->order_by('createDate', 'desc')->limit($num)->get($this->_tables['reply'])->result_array();
		
		$list = array();
		foreach($r_list as $r){
			//update 2013/1/7
// 			$name = $r['mnemonic'] ? $r['mnemonic'] : ($r['nickname'] ? $r['nickname'] : $r['username']);
			
			$user = get_data("user", $r['uid']);
		    if($uid) {
		        $user_mnemonic = get_data('mnemonic', $uid . '-' . $r['uid']);
			    $name = $user_mnemonic['mnemonic'];
			} else {
			    $name = $user['name'];
			}
			$re_replay_string = "";
			if($r['replyTo'] && $r['replyId']) {
				//回复别人的回复
				$re_user = get_data("user", $r['replyTo']);
				$r_name = $re_user['mnemonic'] ? $re_user['mnemonic'] : ($re_user['nickname'] ? $re_user['nickname'] : $re_user['username']);
				$re_replay_string = "回复<a href='/user/".$r['replyTo']."' class='name'>".$r_name."</a>";
			}
			$item = array(
				'id' =>	$r['id'],
				'uid' =>	$r['uid'],
				'name' => $name,
// 				'avatar' => image_url($r['avatar'], 'head', 'hmdp'),
			    'avatar' => $user['avatar_h'],
				'content' => htmlspecialchars($r['content']),
				'createDate' => get_date($r['createDate']),
				're_string' => $re_replay_string
			);
			$list[] = $item;
		}
		unset($r_list);
		return $list;
	}
	
	/**
	 * 获取通过post报名用户
	 * @param 活动对象 $event
	 * @param 地点的ID mixed $place_ids
	 */
	function get_event_apply($event, $places_ids = array()) {
		// 这里如果没有关联地点和TAG，那么直接返回塞。
		$apply_property = json_decode ( $event ['applyProperty'], true );
		if (empty($apply_property['tagids']) && empty($places_ids)) {
			return array();
		}
		
	    // 查询已报名用户
	    $apply_list = $this->db->where(array('eventId' => $event['id']))->get($this->_tables['webeventapply'])->result_array();
        $apply_uids = array();
        foreach($apply_list as $row) {
            $apply_uids[] = $row['uid'];
        }
        /*
         * $this->db->from($this->_tables['postowntag']);
			$this->db->join($this->_tables['post'], $this->_tables['post'].'.id='.$this->_tables['postowntag'].'.postId', 'left');
			$this->db->join($this->_tables['place'], $this->_tables['place'].'.id='.$this->_tables['post'].'.placeId', 'left');
			$this->db->join($this->_tables['user'], $this->_tables['user'].'.id='.$this->_tables['post'].'.uid', 'left');
			$uid && $this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
         * */
        
        //$apply_property = json_decode ( $event ['applyProperty'], true );
		//$event ['apply_form'] = ! empty ( $apply_property ['form'] ) ?   $apply_property ['form'] : false;
		$event ['apply_tags'] = ! empty ( $apply_property ['tagids'] ) ? $apply_property ['tagids'] : false;
	    /*$this->db->select('uid')->where(($event['applyProperty']?"(content LIKE '%#{$event['applyProperty']}#%' 
	            OR content LIKE '%＃{$event['applyProperty']}＃%') AND ":'') 
	            . "createDate BETWEEN '{$event['startDate']}' AND '{$event['endDate']}'", null, false);*/
		
        $this->db->from($this->_tables['post'].' p');
        $event ['apply_tags'] &&$this->db->join($this->_tables['postowntag'].' pt','p.id=pt.postId','left');
		$this->db->select('p.uid')->where("p.createDate BETWEEN '{$event['startDate']}' AND '{$event['endDate']}'",null,false);
        
        $event ['apply_tags'] && $this->db->where_in('pt.tagId',$event ['apply_tags']);
	    $places_ids && $this->db->where_in('p.placeId', $places_ids);
	    $apply_uids && $this->db->where_not_in('p.uid', $apply_uids);
		$list = $this->db->get()->result_array(); //$this->_tables['post']
        //echo '//', $this->db->last_query();
        /*$sql = "select p.uid from {$this->_tables['postowntag']} pt left join {$this->_tables['post']} p on p.id=pt.postId where p.createDate BETWEEN '{$event['startDate']}' AND '{$event['endDate']}'
        ";
        $event ['apply_tags'] && $sql .=" and pt.tagId in (".implode(",",$event ['apply_tags']).") ";
        $places_ids && $sql .=" and p.placeId in (".implode(",",$places_ids).") " ;
        $apply_uids && $sql .= " and p.uid not in (".implode(",",$apply_uids).")";
        var_dump( $sql);*/
	    $uids = array();
	    foreach($list as $row) {
	        $uids[] = $row['uid'];
	    }
	    //var_dump($list);
	    
	    return array_unique($uids);
	}
}