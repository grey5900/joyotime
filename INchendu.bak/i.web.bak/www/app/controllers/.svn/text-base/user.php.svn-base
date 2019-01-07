<?php
/**
 * 用户帐号和设置
 * Create by 2012-5-14
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */

// Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
// Code
class User extends MY_Controller{

	function __construct(){
		parent::__construct();
        
		//查询帐号信息
	/*	$profile = $this->db->where('id', $this->auth['id'])->get('User')->first_row('array');
		//头像
		$profile['avatars'] = array(
			'odp' => image_url($profile['avatar'], 'head', 'hodp'),
			'udp' => image_url($profile['avatar'], 'head', 'hudp'),
			'hdp' => image_url($profile['avatar'], 'head', 'hhdp'),
			'mdp' => image_url($profile['avatar'], 'head', 'hmdp'),
		);
		//显示名字，默认显示昵称，没有昵称显示用户名
		$profile['uname'] = !empty($profile['nickname']) ? $profile['nickname'] : $profile['username'];
		//查询等级
		$exp = $profile['exp'];
		$sql = 'SELECT level FROM UserLevelConstans WHERE minExp <= ? AND maxExp > ? LIMIT 1';
		$rs = $this->db->query($sql, array($exp, $exp))->first_row('array');
		$profile['level'] = $rs['level'];
		//查询已获得的勋章
		
		$this->assign(compact('profile'));*/
		$this->_profile();
	}
    
    function _profile($id = 0) {
        //获取用户基本信息
        $profile = $this->db->where('id', $id?$id:$this->auth['id'])->where('status != ', '1')->select('User.*,UserLevelConstans.level')->join('UserLevelConstans','UserLevelConstans.minExp <= User.exp AND UserLevelConstans.maxExp > User.exp', 'inner')->get('User')->first_row('array');
        if(empty($profile)) {
            return;
        }
        
        // $profile['avatar'] = image_url($profile['avatar'], 'head', 'hmdp');
        //格式化生日
        // $b_date = empty($profile['birthdate'])?FALSE:strtotime($profile['birthdate']);
        // $profile['b_year'] = $b_date ? gmdate('Y', $b_date) : '请选择年';
        // $profile['b_month'] = $b_date ? gmdate('m', $b_date) : '请选择月';
        // $profile['b_day'] = $b_date ? gmdate('d', $b_date) : '请选择日';     
        list($profile['b_year'], $profile['b_month'], $profile['b_day']) = explode('-', $profile['birthdate']);
        $profile['b_year'] = intval($profile['b_year']);
        $profile['b_month'] = intval($profile['b_month']);
        $profile['b_day'] = intval($profile['b_day']);
        !empty($profile['nickname']) && $profile['nickname'] = $profile['username'];
        
        $this->assign('profile', $profile);
        
    }

	/**
	 * 设置基本信息
	 * Create by 2012-5-14
	 * @author liuw
	 */
	function settings(){
		$id = $this->auth['id'];
		if($this->is_post()){
			$edit = array();
			$nickname = $this->post('nickname');
			if(!empty($nickname))
				$edit['nickname'] = $nickname;
			$description = $this->post('description');
			if(!empty($description))
				$edit['description'] = $description;
			$gender = $this->post('gender');
			if(!empty($gender))
				$edit['gender'] = intval($gender)-1;
			$tel = $this->post('tel');
			if(!empty($tel))
				$edit['cellphone_no'] = $tel;
			$invite_uid = trim($this->post('invite_uid'));
			if(isset($invite_uid) && !empty($invite_uid)){
				$edit['invite_uid'] = $invite_uid;
			}
			$data = array(
				'api'=>$this->lang->line('api_user_edit_base'),
				'uid'=>$id,
				'attr'=>$edit,
				'has_return'=>false
			);
            $this->call_api($data, 'post');
			//保存生日
			$birth_year = intval($this->post('birth_year'));
			$birth_month = intval($this->post('birth_month'));
			$birth_day = intval($this->post('birth_day'));
			if($birth_year && $birth_month && $birth_day){
				// $edit['birthdate'] = implode('-', compact('birth_year', 'birth_month', 'birth_day')).' 00:00:00';
				$edit = array('birthdate'=>$birth_year . '-' . $birth_month . '-' . $birth_day);
				$this->db->where('id', $id)->update('User', $edit);
			}
			$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('user_settings_success')));
	//		$this->showmessage($this->lang->line('user_settings_success'), '/user_settings');
		}else{
			$this->display('settings');
		}
	}
	
	/**
	 * 用户时间轴
	 * Create by 2012-5-28
	 * @author liuw
	 * @param mixed $uid
	 * @param boolean $is_name
	 */
	function index($uid = 0, $is_name=FALSE){
		if($uid === 0) {
		    // 用户进入自己的页面，
		    $this->is_login(true);
            forward('/user/' . $this->auth['id'] . '/');
		}
		//用户信息
		$info = $this->db->query('SELECT * FROM User WHERE id=? AND status!=1', array($uid))->first_row('array');

		$nickname = !empty($info['nickname']) ? $info['nickname'] : $info['username'];
    	//title
    	$this->assign('site_title', $nickname.'的时光轴 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $nickname.','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', $info['description'].'；'.$this->lang->line('site_description'));

		if(empty($info)) {
		    $this->showmessage('没有该用户，请访问正确的用户页面');
		}
		//是否是自己
		$info['is_mine'] = empty($this->auth) || $uid == $this->auth['id'];
		//检查是否已关注
		if(!$info['is_mine']){
			$rs = $this->db->where(array('follower'=>$this->auth['id'], 'beFollower'=>$uid))->count_all_results('UserShip');
			$info['has_follow'] = $rs > 0 ? 1 : 0;
		}elseif(empty($this->auth)){
			$info['has_follow'] = 0;
		}else
			$info['has_follow'] = -1;
		//收藏数
		$info['f_count'] = $this->db->where('uid', $uid)->count_all_results('UserFavorite');
		//用户等级
		$rs = $this->db->query('SELECT level FROM UserLevelConstans WHERE minExp <= ? AND maxExp > ? LIMIT 1', array($info['exp'], $info['exp']))->first_row('array');
		$info['level'] = $rs['level'];
		//头像
		$info['avatar'] = image_url($info['avatar'], 'head', 'hhdp');
		//签名
		$info['description'] = htmlspecialchars($info['description']);
		//昵称
		$info['nickname'] = !empty($info['nickname']) ? $info['nickname'] : $info['username'];
		if($this->auth['id']!=$info['id'])
			$info['nickname'] = get_my_desc($this->auth['id'], array('uid'=>$info['id'], 'name'=>$info['nickname']));
		//生日
		$info['birthdate'] = !empty($info['birthdate'])?$info['birthdatee']:'';
		//背景
		$info['background'] = !empty($info['background'])?image_url($info['background'], 'background', 'odp'):'/img/user_header_default.jpg';
		//注册时间
		//$info['createDate'] = substr($info['createDate'], 0, -3)/*gmdate('Y年m月d日', strtotime($info['createDate']))*/;
		$this->assign('info', $info);
		
		//时间线
		$sql = 'SELECT DISTINCT YEAR(DATE(createDate)) AS c_year, MONTH(DATE(createDate)) AS c_month FROM Post WHERE uid=? AND status < ? AND type IN (?,?) GROUP BY createDate ORDER BY createDate DESC';
		$query = $this->db->query($sql, array($uid, $this->config->item('post_close'), $this->config->item('post_tip'), $this->config->item('post_photo')))->result_array();
		$list = $anchors = array();
		$index = 0;
		foreach($query as $row){
    		$list[$row['c_year']]['months'][$row['c_month']] = format_month(intval($row['c_month']));
    		$anchors[$row['c_year'].'-'.($row['c_month'] < 10 ? '0'.$row['c_month']:$row['c_month'])] = $row['c_year'].'年'.($row['c_month'] < 10 ? '0'.$row['c_month']:$row['c_month']).'月';
    		if($index <= 0)
    			$list[$row['c_year']]['active'] = 1;
    		$index++;
		}
		//获取最近一个月的日期
		$keys = array_keys($anchors);
		$now = $keys[0];
		if(!isset($now) || empty($now))
			$now = date('Y-m', time());
		$this->assign('now', $now);
		$this->assign('now_show', $anchors[$now]);
		//获取有POST的最近一个月的POST列表
		$now_posts = $this->get_posts($uid, $now, 0);
		$this->assign('now_list', $now_posts);
		//移除list中最近的一个月
		unset($anchors[$now]);
		$this->assign('list', $list);
		$this->assign('anchors', $anchors);		
    	$this->assign('istimeline', true);
    	$this->assign('tip', $this->config->item('post_tip'));
    	$this->assign('photo', $this->config->item('post_photo'));
		
		$this->display('timeline');
	}
	
	/**
	 * 查询指定时间点的post列表
	 * Create by 2012-6-15
	 * @author liuw
	 * @param int $uid
	 * @param int $timeline
	 */
	public function get_posts($uid, $timeline, $is_show=1){
		//查询指定时间点范围内的所有非屏蔽的点评和照片
		$sql = 'SELECT p.*, pl.isBusiness,pl.placename FROM Post p INNER JOIN Place pl ON pl.id=p.placeId WHERE p.uid=? AND p.status<? AND p.type<>? AND p.createDate LIKE ? ORDER BY p.createDate DESC';
		$arr = array($uid, $this->config->item('post_close'), $this->config->item('post_checkin'), $timeline.'%');
		$query = $this->db->query($sql, $arr)->result_array();
		$list = array();
		foreach($query as $row){
			//post类型
			switch($row['type']){
				case $this->config->item('post_tip'):$row['str_type'] = '点评';$row['praise_t'] = 'tip';break;
				case $this->config->item('post_photo'):$row['str_type'] = '上传照片到';$row['praise_t'] = 'image';break;
				default:$row['str_type']= $row['praise_t'] = '-';break;
			}
			//赞、收藏状态
			if(empty($this->auth) || empty($this->auth[id])){
				$row['has_praise'] = $row['has_fav'] = 0;
			}else{
				//赞的状态
				$row['has_praise'] = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$row['id']))->count_all_results('UserPraise');
				//收藏的状态
				$row['has_fav'] = $this->db->where(array('uid'=>$this->auth['id'], 'itemId'=>$row['id']))->count_all_results('UserFavorite');
			}
			//前3个赞的人
			$praiser = array();
			if($row['praiseCount'] > 0){
				$psql = 'SELECT u.id, IF(u.nickname IS NOT NULL AND u.nickname!=\'\',u.nickname, u.username) AS uname, u.isVerify FROM UserPraise up INNER JOIN User u ON u.id=up.uid WHERE up.itemId=? AND up.itemType=? ORDER BY up.createDate DESC LIMIT 3';
				$parr = array($row['id'], $row['type']);
				$q = $this->db->query($psql, $parr)->result_array();
				foreach($q as $r){
					$r['uname'] = get_my_desc($this->auth['id'], array('uid'=>$r['id'], 'name'=>$r['uname']));
					$praiser[$r['id']] = $r;
				}
			}
			$row['praises'] = $praiser;
			//最新的2条回复，按时间正序排序
			$replies = array();
			if($row['replyCount'] > 0){
				$rsql = 'SELECT pr.*, u.avatar,IF(u.nickname IS NOT NULL AND u.nickname != \'\',u.nickname,u.username) AS uname, u.isVerify FROM PostReply pr INNER JOIN User u ON u.id=pr.uid WHERE pr.postId=? AND pr.status <= 1 ORDER BY pr.createDate DESC LIMIT 2';
				$q = $this->db->query($rsql, array($row['id']))->result_array();
	    		foreach($q as $r){
					$r['uname'] = get_my_desc($this->auth['id'], array('uid'=>$r['uid'], 'name'=>$r['uname']));
	    			//头像
	    			$r['avatar'] = image_url($r['avatar'], 'head', 'hhdp');
	    			//时间
	    			//$r['createDate'] = substr($r['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($r['createDate']))*/;
	    			$replies[$r['id']] = $r;
	    		}
			}
	    	$row['replies'] = array_reverse($replies, true);
	    	$row['content'] = nl2br(htmlspecialchars($row['content']));
    		//$row['createDate'] = substr($row['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($row['createDate']))*/;
    		if($row['type'] == $this->config->item('post_tip')){//点评
    			//星级
    			$row['star'] = ceil($row['level']);
    		}elseif($row['type'] == $this->config->item('post_photo')){//照片
    			//照片
    			$row['photo'] = !empty($row['photoName']) ? image_url($row['photoName'], 'user', 'mdp') : '';
                if($row['photo']) {
                    $wh = image_wh($row['photoName'], 400);
                    $row['h'] = $wh['h'];
                    $row['w'] = $wh['w'];
                    // $row['w'] = $wh['w'];
                    // $row['h'] = $row['w']?intval($wh['h']*((float)390/$row['w'])):0;
                    // $row['w'] = 390;
                }
    		}
			
			$list[$row['id']] = $row;
		}
		if($is_show){
			$this->assign('list', $list);
	    	$this->assign('tip', $this->config->item('post_tip'));
	    	$this->assign('photo', $this->config->item('post_photo'));
	    	
	    	$this->assign('feeds', $feeds);
	    	
			$this->display('post_list');
		}else
			return $list;
	}
	
	/**
	 * 分类获取用户的最新动态
	 * Create by 2012-6-15
	 * @author liuw
	 * @param int $uid
	 */
	function get_feeds($uid = 0){
		//获得用户动态的类型集合，排除点评和照片
		$feeds = array();
		$sql = 'SELECT DISTINCT type FROM UserFeed WHERE uid=? AND type NOT IN (?, ?) ORDER BY type ASC';
		$s = 'SELECT * FROM UserFeed WHERE uid=? AND type=? ORDER BY createDate DESC LIMIT 1';
		$arr = array($uid, $this->config->item('post_tip'), $this->config->item('post_photo'));
		$query = $this->db->query($sql, $arr)->result_array();

		foreach($query as $row){
			//查询最新的一条数据
			$param = array($uid, $row['type']);
			$feed = $this->db->query($s, $param)->first_row('array');

			//格式化内容
			$detail = json_decode($feed['detail'], TRUE);
			if(isset($detail['is_crowned']) && $detail['is_crowned'] > 0)
				$f_type = 11;
			else
				$f_type = $feed['type'];
			//feed内容模板
			$temps = $this->config->item('feed_type');
			$temp = $this->lang->line($temps[$f_type]);
			//格式化feed内容
			switch($f_type){
				case 1://签到
					$replies = array(
						'place_id' => $detail['place_id'],
						'placename' => $detail['placename'],
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate']
					);
					$intro = format_msg($temp, $replies);
					break;
				case 11://成为地主
					$replies = array(
						'place_id' => $detail['place_id'],
						'placename' => $detail['placename'],
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate']
					);
					$intro = format_msg($temp, $replies);
					break;
				case 4://分享post
					//查询post所在的地点
					$feed['s_feed'] = $this->db->query('SELECT * FROM UserFeed WHERE id=?', array($feed['feedId']))->first_row('array');
					$feed['s_feed']['detail'] = json_decode($feed['s_feed']['detail'], TRUE);
					$rs = $this->db->query('SELECT placeId, type FROM Post WHERE id=?', array($feed['s_feed']['detail']['item_id']))->first_row('array');
					$p_type = $rs['type'] == $this->config->item('post_checkin') ? '签到' : ($rs['type']==$this->config->item('post_tip') ? '点评':'照片');
					$replies = array(
						'uid' => $feed['s_feed']['uid'],
						'uname' => $feed['s_feed']['nickname'],
						'place_id' => $rs['placeId'],
						'placename' => $feed['placename'],
						'post_type' => $p_type,
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate'],
						'f_content' => !empty($detail['share_content']) ? $detail['share_content']:'',
						'post_content' => !empty($detail['s_feed']['detail']['content']) ? $detail['s_feed']['detail']['content'] : ''
					);
					$intro = format_msg($temp, $replies);
					break;
				case 5://获得勋章
					$replies = array(
						'badge_icon'=>image_url($detail['badge_uri'], 'common', 'odp'),
						'badge_name'=>$detail['badge_name'],
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate']
					);
					$intro = format_msg($temp, $replies);
					break;
				case 6://分享团购
					$replies = array(
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate']
					);
					$intro = format_msg($temp, $replies);
					break;
				case 7://分享电影票
					$replies = array(
						//'create_date' => substr($feed['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($feed['createDate']))*/,
						'create_date' => $feed['createDate']
					);
					$intro = format_msg($temp, $replies);
					break;
				default://未知
					$intro = FALSE;
					break;
			}
			if($intro !== FALSE)
				$feeds[] = $intro;
		}
		$this->assign('feeds', $feeds);
		$this->display('feeds');
	}
	
	/**
	 * 修改头像
	 * Create by 2012-6-11
	 * @author liuw
	 */
	function avatar(){
	    $this->is_login();
		$cfg = $this->config->item('image_cfg');
		if($this->is_post()){
			//Get the new coordinates to crop the image.
			$x1 = $this->post("x1");
			$y1 = $this->post("y1");
			$x2 = $this->post("x2");
			$y2 = $this->post("y2");
			$src = $this->post('src');
			$large_image_location = FCPATH.str_replace('./','',$src);
	//		$thumb = './'.str_replace('.','_t.', str_replace('./','',$src));
			list($w, $h, $t, $att) = getimagesize($large_image_location);
			//Scale the image to the thumb_width set above
			$this->load->library('upload_image');
			$cropped = $this->upload_image->resizeThumbnailImage($large_image_location, $large_image_location,$x2-$x1,$y2-$y1,$x1,$y1,1);
			//调用api更新用户头像
			$params = array(
				'api'=>$this->lang->line('api_user_edit_avatar'),
				'uid'=>$this->auth['id'],
		    	'has_return'=>TRUE,
				'attr'=>array(
					'uploaded_file'=>'@'.$cropped
				),
			);
			//删除本地图片
			unlink($cropped);
			$result = $this->call_api($params); 
			if($result['result_code'] > 0)
				$json = array('code'=>0, 'msg'=>$this->lang->line('user_upload_avatar_faild'));
			else{
				$avatar_uri = $result['avatar_uri'];
				$json = array('code'=>1, 'msg'=>$this->lang->line('user_upload_avatar_success'), 'avatar'=>$avatar_uri);
			}
			$this->echo_json($json);
		}else{
			//照片保存地址
			$this->assign('upload_path', $cfg['upload_view']);
			$this->display('avatar');
		}
	}
	
	/**
	 * 用户基本信息
	 * Create by 2012-5-18
	 * @author liuw
	 * @param mixed $uid
	 * @param boolean $is_name 为真时查询用户名
	 */
	function info($uid, $is_name=FALSE){
		//查询用户资料
		$sql = 'SELECT * FROM User WHERE ';
		if($is_name !== FALSE)
			$sql .= 'username = \'?\'';
		else 
			$sql .= 'id=?';
		$arr = array($uid);
		$info = $this->db->query($sql, $arr)->first_row('array');
		$nickname = !empty($info['nickname']) ? $info['nickname'] : $info['username'];
		if($this->auth['id'] != $uid)
			$nickname = get_my_desc($this->auth['id'], array('uid'=>$uid, 'name'=>$nickname));
		$this->assign('site_title', $nickname.'的个人资料 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $nickname.','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', $info['description'].'；'.$this->lang->line('site_description'));
    	
		unset($info['password']);
		//格式化数据
		
		//性别
		switch($info['gender']){
			case 1:$info['gender'] = '男';break;
			case 0:$info['gender'] = '女';break;
			default:$info['gender'] = '-';break;
		}
		if(!empty($info['birthdate'])){
			//年龄
			$b_year = intval(substr($info['birthdate'], 0, 4));
			$now = intval(date('Y', time()));
			$info['age'] = $now - $b_year;
			//星座
			$b_month = intval(date('m', $info['birthdate']));
			$b_day = intval(date('d', $info['birthdate']));
			$stars = $this->config->item('star');
			$star_two = $stars[$b_month];
			$star_names = array_keys($star_two);
			if($b_day <= $star_two[$star_names[0]])
				$info['star'] = $star_names[0];
			else
				$info['star'] = $star_names[1];
		}else{
			$info['age'] = '-';
			$info['star'] = '-';
		}	
		//大头像
		$info['avatar'] = image_url($info['avatar'], 'head', 'hhdp');
		//签名
		$info['description'] = htmlspecialchars($info['description']);
		//收藏数
		$info['favoriteCount'] = $this->db->where('uid', $uid)->count_all_results('UserFavorite');
		//背景
		$info['background'] = !empty($info['background'])?image_url($info['background'], 'background', 'odp'):'/img/user_header_default.jpg';
		//最后登录
		//$info['last_signin'] = substr($info['lastSigninDate'],0, -3);/*gmdate('Y-m-d H:i', strtotime($info['lastSigninDate']));*/
		$info['last_signin'] = $info['lastSigninDate'];
		!empty($info['nickname']) && $info['nickname'] = $info['username'];
		$info['nickname'] = get_my_desc($this->auth['id'], array('uid'=>$info['id'], 'name'=>$info['nickname']));
		$this->assign('nickname', $info['nickname']);
		$this->assign('info', $info);
		//是否已关注
		if($uid != $this->auth['id']){
			$has_follow = $this->db->where(array('follower'=>$this->auth['id'], 'beFollower'=>$uid))->count_all_results('UserShip');
			$this->assign('has_followed', $has_follow > 0 ? 1 : 0);
		}
		$this->display('info');
	}
	
	/**
	 * 显示是地主的地点列表
	 * Create by 2012-5-29
	 * @author liuw
	 * @param int $uid
	 * @param int $page
	 */
	function mayor($uid, $page=1){
		//用户属性
		$info = $this->db->where('id', $uid)->get('User')->first_row('array');
        $info['avatar'] = image_url($info['avatar'], 'head', 'hhdp');
		$nickname = !empty($info['nickname']) ? $info['nickname'] : $info['username'];
		if($this->auth['id'] != $uid)
			$nickname = get_my_desc($this->auth['id'], array('uid'=>$uid, 'name'=>$nickname));
		$this->assign('site_title', $nickname.'的地主 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $nickname.','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', $user['description'].'；'.$this->lang->line('site_description'));
		$this->assign('nickname', $nickname);
		//是否已关注 
		if($uid != $this->auth['id']){
			$has_followed = $this->db->where(array('follower'=>$this->auth['id'], 'beFollower'=>$uid))->count_all_results('UserShip');
			$this->assign('has_followed', $has_followed);
		}
		$this->assign('info', $info);
		
		//地主地点总数
		$placeCount = $info['mayorshipCount'];
		$cooditions = $places = array();
		//每页10条纪录，查询地点列表
		if($placeCount){
			//总页数
			$total_page = ceil($placeCount / 10);
			//计算开始游标
			$start = 10*($page - 1);
			//查询数据
			$sql = 'SELECT * FROM Place WHERE mayorId=? ORDER BY mayorDate DESC LIMIT ?, ?';
			$query = $this->db->query($sql, array($uid, $start, 10))->result_array();
			//封装数据
			$i = 1;
			foreach($query as $row){
				$cood = array('lat'=>$row['latitude'], 'lng'=>$row['longitude'], 'title'=>$row['placename']);
				$cooditions[$i] = $cood;
				$data = array(
					'id'=>$row['id'],
					'name'=>$row['placename'],
					//'date'=>gmdate('Y年m月d日 H:i', strtotime($row['mayorDate'])),
					'date'=>$row['mayorDate'],
					'lat'=>$row['latitude'],
					'lng'=>$row['longitude']
				);
				$places[$i++] = $data;
			}
			$this->assign(compact('cooditions', 'places'));
			//上一页
			$prev = $page - 1 > 0 ? $page - 1 : 0;
			//下一页
			$next = $page + 1 < $total_page ? $page + 1 : 0;
			$this->assign('prev', $prev);
			$this->assign('next', $next);
		}
		
		$this->display('mayor');
	}
	
	/**
	 * 显示图片墙
	 * Create by 2012-5-16
	 * @author liuw
	 * @param int $uid
	 * @param int $page
	 */
	function photo($uid, $page=1){
		//查询用户信息
		$user = $this->db->where('id', $uid)->get('User')->first_row('array');
        $user['avatar'] = image_url($user['avatar'], 'head', 'hhdp');
		$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['username'];
		if($this->auth['id'] != $uid)
			$nickname = get_my_desc($this->auth['id'], array('uid'=>$uid, 'name'=>$nickname));
		//title
		$this->assign('site_title', $nickname.'的图片墙 - '.$this->lang->line('site_title'));
    	$this->assign('site_keywords', $nickname.','.$this->lang->line('site_keywords'));
    	$this->assign('site_description', $user['description'].'；'.$this->lang->line('site_description'));
		$this->assign('nickname', $nickname);
		$this->assign('info', $user);
		//检查当前用户是否已关注了指定用户
		$this->assign('has_followed', $this->_has_followed($this->auth['id'], $uid));
		//查询总长度
		$count = $this->db->where(array('uid'=>$uid, 'status < '=>$this->config->item('post_close'), 'type'=>$this->config->item('post_photo')))->count_all_results('Post');
		if($count){
			//分页
			$url_arr = array('uid'=>$uid);
			$parr = paginate('/user_photo', $count, $page, $url_arr, 15);
			//数据
			$sql = 'SELECT p.*, if(u.nickname is not null and u.nickname != \'\', u.nickname, u.username) AS uname, u.avatar, u.isVerify AS u_isverify, pl.isVerify, pl.placename, '.
					'(SELECT COUNT(*) FROM UserFavorite WHERE uid=? AND itemType=p.type AND itemId=p.id) AS favorite '.
					'FROM Post p, User u, Place pl '.
					'WHERE u.id=p.uid AND pl.id=p.placeId AND u.id=? AND p.status < ? AND p.type=\''.$this->config->item('post_photo').'\' ORDER BY p.createDate DESC LIMIT ?,?';
			$arr = array($this->auth['id'], $uid, $this->config->item('post_close'), $parr['offset'], $parr['per_page_num']);
			$query = $this->db->query($sql, $arr)->result_array();
			$list = array();
			foreach($query as $row){
				//头像
				$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
				//照片缩略图
				if(!empty($row['photoName'])){
					$row['photo'] = image_url($row['photoName'], 'user', 'thweb');
				/*	list($width, $height, $type, $attr) = @getimagesize($row['photo']);
					$row['p_w'] = !empty($width) ? $width : 320;
					$row['p_h'] = !empty($height) ? $height : 320;*/
                    $wh = image_wh($row['photoName']);
                    $row['w'] = $wh['w'];
                    $row['h'] = $wh['h'];
			     }else{
					$row['photo'] = '';
				/*	$row['p_w'] = 320;
					$row['p_h'] = 320;*/
				}
				//时间
				//$row['createDate'] = substr($row['createDate'], 0, -3)/*gmdate('Y-m-d H:i', strtotime($row['createDate']))*/;

				//是否已收藏 
    			if(!empty($this->auth)){
    				$favorite = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserFavorite');
    				if($favorite > 0)
    					$row['favorite'] = $favorite;
    			}
    			//是否已赞 
    			if(!empty($this->auth)){
    				$praise = $this->db->where(array('uid'=>$this->auth['id'],'itemId'=>$row['id'],'itemType'=>$row['type']))->count_all_results('UserPraise');
    				if($praise > 0)
    					$row['praise'] = $praise;
    			}

				$list[$row['id']] = $row;
			}
			$this->assign('list', $list);
		}
		
		$this->display('photo');
	}
	
	/**
	 * 修改好友备注
	 * Create by 2012-8-28
	 * @author liuw
	 * @param int $m_uid
	 */
	function mnemonic($m_uid){
		if($this->is_post()){
			//检查是否是关注对象
			$count = $this->db->where(array('beFollower'=>$m_uid, 'follower'=>$this->auth['id']))->count_all_results('UserShip');
			if(!$count)
				$this->echo_json(array('code'=>0, 'msg'=>'请先关注Ta'));
			$uid = $this->auth['id'];
			$mnemonic = $this->post('mnemonic');
			if(mb_strlen($mnemonic) > 6)
				$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('mnemonic_to_long')));
			else{
				//更新数据，包括缓存
				$this->load->model('universal', 'univ');
				$this->univ->set_my_desc($uid, $m_uid, $mnemonic);
				$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('mnemonic_do_success'), 'mnemonic'=>$mnemonic));
			}
		}else{
			exit('更新好友备注');
		}
	}

	/**
	 * 显示用户的好友
	 * Create by 2012-5-14
	 * @author liuw
	 * @param int $uid
	 * @param int $type
	 * @param int $page
	 */
	function friend($uid, $type=0, $page=1){
		if($this->is_post()){
				
		}else{
			$this->assign('type', $type);
			//可不可以备注
			$this->assign('can_mnemonic', $this->auth['id']==$uid?1:0);
			//查询用户信息
			$user = $this->db->where('id', $uid)->get('User')->first_row('array');
            $user['avatar'] = image_url($user['avatar'], 'head', 'hhdp');
			$nickname = !empty($user['nickname']) ? $user['nickname'] : $user['username'];
			if($this->auth['id'] != $uid)
				$nickname = get_my_desc($this->auth['id'], array('uid'=>$uid, 'name'=>$nickname));
			$this->assign('site_title', $nickname.'的好友 - '.$this->lang->line('site_title'));
	    	$this->assign('site_keywords', $nickname.','.$this->lang->line('site_keywords'));
	    	$this->assign('site_description', $user['description'].'；'.$this->lang->line('site_description'));
			$this->assign('nickname', $nickname);
			$this->assign('info', $user);
			//检查当前用户是否已关注了指定用户
			$this->assign('has_followed', $this->_has_followed($this->auth['id'], $uid));
				
			//根据type查询粉丝或关注，默认查询关注
			$count = $this->db->where($type ? 'beFollower':'follower', $uid)->count_all_results('UserShip');
			if($count){
				//分页条
				$url_arr = array('uid'=>$uid, 'type'=>$type);
				$parr = paginate('/user_friend', $count, $page, $url_arr, 30);
				//数据列表
				$sql = 'SELECT u.id,if(u.nickname is not null and u.nickname != \'\',u.nickname,u.username) AS uname, u.isVerify, '.
					'(SELECT COUNT(*) FROM UserShip WHERE follower=\''.$this->auth['id'].'\' AND beFollower=u.id) AS has_follow,'.
					'u.avatar, u.description, u.checkinCount, u.tipCount, u.photoCount FROM User u, UserShip us WHERE us.'.
					($type ? 'follower' : 'beFollower').' = u.id AND us.'.($type ? 'beFollower':'follower').
					' = ? ORDER BY us.createDate DESC LIMIT ?, ?';
				$arr = array($uid, $parr['offset'], $parr['per_page_num']);
				$query = $this->db->query($sql, $arr)->result_array();
				$list = array();
				foreach($query as $row){
					$row['uname'] = get_my_desc($this->auth['id'], array('uid'=>$row['id'],'name'=>$row['uname']));
					//获取AVATAR的地址
					$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
					//获取AVATAR的地址
					$row['description'] = htmlspecialchars($row['description']);
					$list[$row['id']] = $row;
				}
				$this->assign('list', $list);
			}
				
			$this->display('friend');
		}
	}
	
	/**
	 * 当前登录用户关注操作
	 * Create by 2012-5-14
	 * @author liuw
	 */
	function do_follow(){
	    $this->is_login();
		if($this->is_post()){
			$do = $this->post('do');
			$do = empty($do) || $do === 'f' ? 1:0;//关注还是取消关注
			$be_follower = $this->post('bf');
			$follower = $this->auth['id'];
			$code = 0;
			$msg = '';
			
			//检查参数
			if(empty($be_follower)){
				$msg = $this->lang->line('user_follow_not_befollower');
			}else{
			//处理逻辑
				switch($do){
					case 1://关注
						//检查是否已关注过
						$rs = $this->db->where('follower', $follower)->where('beFollower', $be_follower)->count_all_results('UserShip');
						if($rs > 0)
							$msg = $this->lang->line('user_follow_has_followed');
						else{
							//加关注
							$this->db->insert('UserShip', array('follower'=>$follower, 'beFollower'=>$be_follower));
							//＋1
							$this->db->query('UPDATE User SET followCount=followCount+1 WHERE id=?', array($follower));
							$this->db->query('UPDATE User SET beFollowCount=beFollowCount+1 WHERE id=?', array($be_follower));
							$code = 1;
							$bf = $this->db->where('id', $be_follower)->get('User')->first_row('array');
							$msg = $this->lang->line('user_follow_success').(!empty($bf['nickname']) ? $bf['nickname'] : $bf['username']);
						}
						break;
					default://取消关注
						//检查是否已关注过
						$rs = $this->db->where('follower', $follower)->where('beFollower', $be_follower)->count_all_results('UserShip');
						if($rs <= 0)
							$msg = $this->lang->line('user_follow_un_follow');
						else{
							//取消关注
							$this->db->where(array('follower'=>$follower, 'beFollower'=>$be_follower))->delete('UserShip');
							// -1
							$this->db->query('UPDATE User SET followCount=if(followCount-1 <= 0, 0, followCount-1) WHERE id=?', array($follower));
							$this->db->query('UPDATE User SET beFollowCount=if(beFollowCount-1 <= 0, 0, beFollowCount-1) WHERE id=?', array($be_follower));
							
							$bf = $this->db->where('id', $be_follower)->get('User')->first_row('array');
							$code = 1;
							$msg = preg_replace("/@{user}/", !empty($bf['nickname'])?$bf['nickname']:$bf['username'], $this->lang->line('user_follow_un_follow_success'));
							
						}
						//清理备注
						$this->load->model('universal', 'univ');
						$this->univ->set_my_desc($follower, $be_follower, '');
						break;
				}
			}
			$this->echo_json((compact('code', 'msg')));
		}
	}
	
	/**
	 * 积分规则
	 * Create by 2012-5-21
	 * @author liuw
	 */
	public function score(){
		//查询积分规则
		$query = $this->db->order_by('createDate', 'asc')->where('actionBegin <= CURRENT_TIMESTAMP AND actionEnd > CURRENT_TIMESTAMP')->get('UserPointCase')->result_array();
		$list = array();
		foreach($query as $row){
			$row['description'] = str_replace('n', '几', $row['description']);
			$list[] = $row;
		}
		$this->assign('list', $list);
		$this->display('score');
	}
	
	/**
	 * 修改邮箱
	 * Create by 2012-5-21
	 * @author liuw
	 */
	public function email(){
	    $this->is_login();
		if($this->is_post()){
			$password = $this->post('password');
			$email = $this->post('email');
			//检查密码
			$rs = $this->db->where('id',$this->auth['id'])->select('password')->get('User')->first_row('array');
			$result = array();
			if(empty($rs) || $rs['password'] !== strtoupper(md5($password)))
				$result = array('code'=>0, 'msg'=>$this->lang->line('user_old_pwd_fail'));
			else{
				$edit = array('email'=>$email);
				$this->db->where('id', $this->auth['id'])->update('User', $edit);
				$result = array('code'=>1, 'msg'=>$this->lang->line('user_set_email_success'));
			}
			$this->echo_json(($result));
		}else{
			$this->display('email');
		}
	}
	
	/**
	 * 修改密码 
	 * Create by 2012-5-21
	 * @author liuw
	 */
	public function revisepassword(){
	    $this->is_login();
		if($this->is_post()){
			$result = array();
			$oldpwd = $this->post('oldpwd');
			$newpwd = $this->post('newpwd');
			$renew = $this->post('renew');
			//检查原密码
			$rs = $this->db->select('password')->where('id', $this->auth['id'])->get('User')->first_row('array');
			if($rs['password'] !== strtoupper(md5($oldpwd)))
				$result = array('code'=>0, 'msg'=>$this->lang->line('user_old_pwd_fail'));
			elseif($renew !== $newpwd)
				$result = array('code'=>0, 'msg'=>$this->lang->line('signup_twopwd_not_equal'));
			else{
				//修改密码
				$edit = array('password'=>strtoupper(md5($newpwd)));
				$this->db->where('id', $this->auth['id'])->update('User', $edit);
				$result = array('code'=>1, 'msg'=>$this->lang->line('user_set_pwd_success'));
			}
			$this->echo_json(($result));
		}else{
			$this->display('revisepassword');
		}
	}
	
	/**
	 * 用户同步设置
	 * Create by 2012-5-31
	 * @author liuw
	 */
	public function sync(){
	    $this->is_login();
		$sync_pfs = $this->config->item('sync_platforms');
    	$api_uri = $this->config->item('api_serv').$this->config->item('api_folder');
    	$this->assign('api_uri', $api_uri);
		if($this->is_post()){
			
		}else{
			//检查绑定状态  
			foreach($sync_pfs as $key=>$sync){
				$sync['has_bind'] = $this->db->where('uid', $this->auth[id])->count_all_results($sync['table']);
				$sync_pfs[$key] = $sync;
			}
			$this->assign('syncs', $sync_pfs);
			$this->assign('uid', $this->auth['id']);
			$this->display('sync');
		}
	}
	
	/**
	 * 我的会员卡列表
	 * Create by 2012-9-19
	 * @author liuw
	 * @param int $page
	 */
	public function my_card($page=1){
		//检查登录状态
		$this->is_login(true);
		$uid = $this->auth['id'];
		$list = array();
		//查询会员卡总数
		$count = $this->db->where('uid', $uid)->count_all_results('UserOwnMemberCard');
		if($count){
			//分页
			$parr = paginate('/user_my_card', $count, $page, array(), 10);
			//数据
			$where = array(
				'UserOwnMemberCard.uid'=>$uid,
				'BrandMemberCard.status'=>1,
			);
			$select = 'BrandMemberCard.*, Brand.tel, Brand.logo, UserOwnMemberCard.*';
			$this->db->select($select);
			$this->db->join('BrandMemberCard', 'BrandMemberCard.id = UserOwnMemberCard.memberCardId', 'inner');
			$this->db->join('Brand', 'Brand.id = BrandMemberCard.brandId', 'inner');
			$this->db->where($where);
			$query = $this->db->order_by('UserOwnMemberCard.createDate', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get('UserOwnMemberCard')->result_array();
			foreach($query as $row){
				//会员卡图标
				$row['image'] = $row['image'];
				//品牌图标
				$row['logo'] = $row['logo'];
				$list[$row['memberCardId']] = $row;
			}
		}
		$this->assign('list', $list);
		$this->echo_json($list);
	}
	
	/**
	 * 检查当前登录用户是否已关注了指定的用户
	 * Create by 2012-5-16
	 * @author liuw
	 * @param int $follower
	 * @param int $be_follower
	 * @return int
	 */
	private function _has_followed($follower, $be_follower){
		return $this->db->where(array('follower'=>$follower, 'beFollower'=>$be_follower))->count_all_results('UserShip') > 0 ? 1 : 0;
	}

}
 
// File end