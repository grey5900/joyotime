<?php
/**
 * Create by 2012-12-11
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class User extends Controller{
	
	var $_tables;
	var $_stars;
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('cache');
		//数据模型
		$this->load->model('user_model', 'm_user');
		$this->load->model('post_model', 'm_post');
		$this->load->model('place_model', 'm_place');
		$this->load->model('usership_model', 'm_uship');
		$this->load->model('userresetpwd_model', 'm_resetpwd');
		$this->load->model('post_model','m_post');
		$this->load->model('placecollection_model','m_collection');
		$this->_tables = $this->config->item('tables');
		//配置
		$this->config->load('config_common');
		$this->_stars = $this->config->item('star');
		
		$this->load->language('passport');
		
		//每个页面都有的内容
		//好有总数 +8个好友
		//图片总数和最新一张图片
		//地点册总数和3个地点册
		
	}
	
	/**
	 * 用户首页，时间轴
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @param int $uid
	 * @param int $sub_page
	 * @param int $page
	 * @param string $time
	 */
	public function index($uid=0, $sub_page=1, $page=1, $time=''){
		!$uid && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid );
		//是否可关注
		if(empty($this->auth))
			$info['can_follow']=1;
		elseif($uid == $this->auth['uid'])
			$info['can_follow']=-1;
		else{
			$c = $this->m_uship->check_ship($this->auth['uid'], $uid);
			$info['can_follow'] = $c ? 0 : 1;
			unset($c);
		}
		
		$ulist = $this->m_user->get_all_friends($uid,8);
		
		$condition['where'] = array(
			'uid' => $uid,
			'photo !=' => '',
			'status' => 1
		); 
		$condition['order'] = array('createDate','desc');
		$condition['limit'] = array('size'=>1,'offset'=>0);
		$photo = $this->m_post->list_post($condition,'mdp')[0];
		
		$place_collection = $this->m_collection->list_place_collection(1,3,'new',' AND pc.uid='.$uid);
		
		
		
		//TITLE、KEYWORD、DESCRIPTION
		//$title = $this->lang->line('site_current_title').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$keywords = $this->lang->line('site_current_keyword').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$this->assign(compact('title', 'keywords'));
		
		//时间轴
		$timelines = $this->m_post->get_user_timeline($uid);
		
		if(empty($time)){
			//最近的一个月
			$years = array_keys($timelines);
			$year = $years[0];
			$month = $timelines[$year][0][0];
			$base_on_time = false;
		}else{
			list($year, $month) = explode('-', $time);
			$base_on_time = true;
			
		}
		//开始时间
		$begin = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		//结束时间
		if($month == 12 || empty($month)){
			$end = ($year+1).'-01-01';
		}else{
			$end = $year.'-'.($month + 1 < 10 ? '0'.($month + 1) : $month + 1).'-01';
		}
		
		
		$where = array(
			$this->_tables['post'].'.uid'=>$uid,
			$this->_tables['post'].'.status <'=>2, // status < 2
			$this->_tables['post'].'.type <='=>4  //暂时只取type=2 ,4（点评）的数据
			//$this->_tables['post'].'.createDate >= '=>$begin,
			//$this->_tables['post'].'.createDate < '=>$end
		);
		
		if(!empty($time)){
			$where[$this->_tables['post'].'.createDate >= '] = $begin;
			$where[$this->_tables['post'].'.createDate < '] = $end;
		}
		
		$conditions = compact('where');
		//长度
		$count = $this->m_post->count_post($conditions);
		if($count){
			//分页
			$parr = $this->paginate('/user', $count, $page, array('uid'=>$uid, 'sub_page'=>$sub_page, 'time_suffix'=>$time));
			if($page <= $parr['total_page']){
				//小分页
				$offset = $parr['offset'] + 20 * ($sub_page - 1);
				$sub_page += 1;
				$conditions['limit'] = array('size'=>20, 'offset'=>$offset);
				//POST列表
				$list = $this->m_post->list_post($conditions, 'hdp', true);
				unset($conditions);
			}
			//二次划分，20条一页
		//	!empty($list) && $list = array_chunk($list, 20);
			//右边-用户最新的4张图片
			$user_imgs = array();
			if($info['photoCount'] > 0){
				$condition = array(
			//		'select' => array($this->_tables['post'].'.id',$this->_tables['post'].'.photoName'),
					'where' => array($this->_tables['post'].'.uid'=>$uid,$this->_tables['post'].'.photo !='=>'', $this->_tables['post'].'.status < '=>2),
					'limit' => array('size'=>4, 'offset'=>0)
				);
				$user_imgs = $this->m_post->list_post($condition, 'thmdp', true);
			}
		}
		
		/*地主定位*/
		//var_dump($info['mayors']);
		/*if($info['mayors']){
			$xy_arr = array();
			$center = "";
			foreach($info['mayors'] as $k => $row){
				$xy_arr[$k] = $row['longitude'].",".$row['latitude'].",".($k+1);
				if(!$center) $center = $row['longitude'].",".$row['latitude'];
			}
			$info['mayor_center'] = $center;
			$info['mayor_markers'] = implode("|",$xy_arr);
		}*/
		
				
		
		$this->title = (!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//页面输出
		$this->assign(compact('timelines','list', 'info', 'sub_page', 'time', 'user_imgs', 'page','year','base_on_time','ulist','photo','place_collection'));
		$this->display('timeline');//timeline3.0
	}
	
	/**
	 * 更多资料
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @param int $uid
	 */
	public function info($uid=0){
		!$uid && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid);
		
		//TITLE、KEYWORD、DESCRIPTION
		//$title = $this->lang->line('site_current_title').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$keywords = $this->lang->line('site_current_keyword').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$this->assign(compact('title', 'keywords'));
		
		//转换星座
		if(!empty($info['birthdate'])){
			list($y, $m, $d) = explode('-', $info['birthdate']);
			$m = intval($m);
			$d = intval($d);
			$stars = $this->_stars[$m];
			$ds = array_values($stars);
			$stars = array_flip($stars);
			if($d <= $ds[0])
			 $info['star'] = $stars[$ds[0]];
			//年龄
			$n_y = intval(gmdate('Y', time()+8*3600));
			$info['age'] = abs($n_y - $y);
		}else{
			$info['star'] = '-';
			$info['age'] = '-';
		}
		//性别
		$info['gender'] = $info['gender'] ? '先生' : '女士';
		//最后登录时间
		$info['last_signin'] = get_date($info['lastSigninDate']);
		$this->title = (!empty($info['nickname'])?$info['nickname'] : $info['username']);
		$this->assign('info', $info);
		$this->display('info');
	}
	
	/**
	 * 用户图片墙
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @param int $uid
	 * @param int $page
	 */
	public function photo($uid=0, $page=1){
		!$uid && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid);
		
		//TITLE、KEYWORD、DESCRIPTION
		//$title = $this->lang->line('site_current_title').(!empty($info['nickname'])?$info['nickname'] : $info['username']).'_图片墙';
		//$keywords = $this->lang->line('site_current_keyuserresetpwdword').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$this->assign(compact('title', 'keywords'));
		
		//图片总数
		$condition = array('where'=>array($this->_tables['post'].'.uid'=>$uid, $this->_tables['post'].'.photo != '=>'', $this->_tables['post'].'.type <= '=>4, $this->_tables['post'].'.status'=>1)); // stauts < 2
		$count = $this->m_post->count_post($condition);
		
		if($count){
			//分页
			$parr = $this->paginate('/user_photo', $count, $page, array('uid'=>$uid));
			if($page <= $parr['total_page']){
				//列表
				$condition['limit'] = $parr;
				$list = $this->m_post->list_post($condition, 'mdp');
			}
		}
		$this->title = (!empty($info['nickname'])?$info['nickname'] : $info['username']);
		empty($list) && $list = array();
		$this->assign(compact('info', 'list'));
		$this->display('photo');
	}
	
	/**
	 * 好友
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @param int $uid
	 * @param int $type 好友类型，0=关注，1=粉丝
	 * @param int $page
	 */
	public function friend($uid=0,$type=0,$page=1){
		!$uid && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid );
		//是否可关注
		if(empty($this->auth))
			$info['can_follow']=1;
		elseif($uid == $this->auth['uid'])
			$info['can_follow']=-1;
		else{
			$c = $this->m_uship->check_ship($this->auth['uid'], $uid);
			$info['can_follow'] = $c ? 0 : 1;
			unset($c);
		}
		
		$ulist = $this->m_user->get_all_friends($uid,8);
		
		$condition['where'] = array(
			'uid' => $uid,
			'photo !=' => '',
			'status' => 1
		); 
		$condition['order'] = array('createDate','desc');
		$condition['limit'] = array('size'=>1,'offset'=>0);
		$photo = $this->m_post->list_post($condition,'mdp')[0];
		
		$place_collection = $this->m_collection->list_place_collection(1,3,'new',' AND pc.uid='.$uid);
		
		//TITLE、KEYWORD、DESCRIPTION
		//$title = $this->lang->line('site_current_title').(!empty($info['nickname'])?$info['nickname'] : $info['username']).'_'.($type ? '粉丝' : '好友');
		//$keywords = $this->lang->line('site_current_keyword').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$this->assign(compact('title', 'keywords'));
		
		//总数
		$count = $this->m_user->count_friends($uid, $type);
		$list = array();
		if($count){
			//分页
			$parr = $this->paginate('/user_friend', $count, $page, array('uid'=>$uid, 'type'=>$type), 32);
			$list = $this->m_user->get_friends($uid, $type, $parr['size'], $parr['offset']);
		}
		$this->title = (!empty($info['nickname'])?$info['nickname'] : $info['username']);
		$this->assign(compact('info', 'list', 'type','ulist','photo','place_collection'));
		$this->display('friend');//friend3.0
	}
	
	/**
	 * 地主地点列表
	 * Create by 2012-12-11
	 * @author liuweijava
	 * @param int $uid
	 * @param int $page
	 */
	public function mayor($uid=0,$page=1){
		!$uid && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid);
		
		//TITLE、KEYWORD、DESCRIPTION
		//$title = $this->lang->line('site_current_title').(!empty($info['nickname'])?$info['nickname'] : $info['username']).'_地主地点';
		//$keywords = $this->lang->line('site_current_keyword').(!empty($info['nickname'])?$info['nickname'] : $info['username']);
		//$this->assign(compact('title', 'keywords'));
		
		//地点总数
		$count = $this->m_place->count_by_mayorId($uid);
		$list = $cooditions = array();
		$prev = $next = 0;
		if($count){
			//计算上一页页码
			$prev = $page - 1 > 0 ? $page - 1 : 1;
			//计算下一页页码
			$sumpage = ceil($count / 10);
			$next = $page + 1 < $sumpage ? $page + 1 : $sumpage;
			//分页
			$parr = $this->paginate('/user_mayor', $count, $page, array('uid'=>$uid), 10);
			//数据
			$list = $this->m_place->list_order_mayordate_desc(array('mayorId'=>$uid), $parr['size'], $parr['offset']);
			if(!empty($list)){
				$i = 1;
				foreach($list as $k=>$v){
					$cooditions[$i++] = array(
						'lat' => $v['latitude'],
						'lng' => $v['longitude'],
						'title' => $v['placename']
						//'mayorDate' => $v['mayorDate']
					);
				}
			}
		}
		$this->title = (!empty($info['nickname'])?$info['nickname'] : $info['username']);
		$this->assign(compact('info', 'list', 'prev', 'next', 'cooditions'));
		$this->display('mayor');
	}
	
	public function join($place_id){
		//不做了。。。
	}
	
	public function collection($uid = 0, $sub_page=1, $page=1, $sort = 'create', $time=''){
		!$uid && $uid = $this->auth['uid'];
		
		$info = get_data('user', $uid );
		//是否可关注
		if(empty($this->auth))
			$info['can_follow']=1;
		elseif($uid == $this->auth['uid'])
			$info['can_follow']=-1;
		else{
			$c = $this->m_uship->check_ship($this->auth['uid'], $uid);
			$info['can_follow'] = $c ? 0 : 1;
			unset($c);
		}
		
		$ulist = $this->m_user->get_all_friends($uid,8);
		
		$condition['where'] = array(
			'uid' => $uid,
			'photo !=' => '',
			'status' => 1
		); 
		$condition['order'] = array('createDate','desc');
		$condition['limit'] = array('size'=>1,'offset'=>0);
		$photo = $this->m_post->list_post($condition,'mdp')[0];
		
		$place_collection = $this->m_collection->list_place_collection(1,3,'new',' AND pc.uid='.$uid);
		
		//时间轴
		$timelines = $this->m_collection->get_collection_timeline($uid);
		if(empty($time)){
			//最近的一个月
			$years = array_keys($timelines);
			$year = $years[0];
			$month = $timelines[$year][0][0];
			$base_on_time = false;
		}else{
			list($year, $month) = explode('-', $time);
			$base_on_time = true;
			
		}
		//开始时间
		$begin = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		//结束时间
		if($month == 12 || empty($month)){
			$end = ($year+1).'-01-01';
		}else{
			$end = $year.'-'.($month + 1 < 10 ? '0'.($month + 1) : $month + 1).'-01';
		}
		
		$where = ' status=0 and uid='.$uid;
		if(!empty($time)){
			$where .= " AND (createDate >= '{$begin}' and createDate < '{$end}' ) ";
		}
		$total = $this->m_collection->count_place_collections('new',$where);
		if($total){
			//未完待续。。。
		}
		
		$this->assign(compact('info','ulist','photo','place_collection','timelines'));
		$this->display('collection');
	}
	
	/**
	 * 获取用户的签到路线
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 */
	public function get_route($uid){
		$this->echo_json($this->m_post->get_route($uid));
	}
	
	public function get_mayors($uid){
		$this->echo_json($this->m_post->get_mayors($uid));
	}
	
	/**
	 * 获取用户好友
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 * @param int $be_follow
	 */
	public function get_friends($uid, $be_follow=0){
		$this->echo_json($this->m_user->get_friends($uid, $be_follow));
	}
	
	/**
	 * 关注用户
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $be_follow
	 */
	public function follow($be_follow){
		if(empty($this->auth)){
			$this->echo_json(array('code'=>1, 'msg'=>'没登录'));
		}else{
			if($be_follow == $this->auth['uid']) $this->echo_json(array('code'=>-1, 'msg'=>'您不能关注自己'));
			$this->load->model('usership_model', 'm_uship');
		//	$args = array(array('follower'=>$this->auth['uid'], 'beFollower'=>$be_follow));
			$status = $this->m_uship->follow($this->auth['uid'], $be_follow);
			if($status===false){
				 $this->echo_json(array('code'=>-2, 'msg'=>'您已经关注了'.$uname.'，无法重复关注。'));
			}
			$bf = $this->m_user->get_info($be_follow);
			$uname = !empty($bf['nickname']) ? $bf['nickname'] : $bf['username'];
			//更新缓存
			get_data('user', $this->auth['uid'], true);
			get_data('user', $be_follow, true);
			$this->echo_json(array('code'=>0, 'msg'=>'您关注了 '.$uname));
		}
	}
	
	/**
	 * 取消关注
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $be_follow
	 */
	public function unfollow($be_follow){
		if(empty($this->auth)){
			$this->echo_json(array('code'=>1, 'msg'=>'没登录'));
		}else{
			$this->load->model('usership_model', 'm_uship');
			$bf = $this->m_user->get_info($be_follow);
			$uname = !empty($bf['nickname']) ? $bf['nickname'] : $bf['username'];
			//取消关注
			$status = $this->m_uship->un_follow($this->auth['uid'], $be_follow);
			if($status===false){
				 $this->echo_json(array('code'=>-2, 'msg'=>'您没有关注'.$uname.'，无法取消关注。'));
			}
			//更新缓存
			get_data('user', $this->auth['uid'], true);
			get_data('user', $be_follow, true);
			$this->echo_json(array('code'=>0, 'msg'=>'您取消了对 '.$uname.' 的关注'));
		}
	}
	
	/**
	 * 用户已获得的会员卡列表
	 * Create by 2012-12-27
	 * @author liuweijava
	 * @param int $uid
	 * @param int $page
	 */
	public function cards($uid=0, $page=1){
		!$uid && $this->auth['uid'] && $uid = $this->auth['uid'];
		//用户详情
		$info = get_data('user', $uid);
		
		//TITLE
		//$title = $this->lang->line('site_current_title').($info['nickname'] ? $info['nickname'] : $info['username']).'_会员卡';
		//$this->assign('title', $title);
		
		//查询会员卡总数
		$this->load->model('userownmembercard_model', 'm_uomc');
		$count = $this->m_uomc->count_cards($uid);
		if($count){
			//分页
			$parr = $this->paginate('/user_cards', $count, $page, array('uid'=>$uid));
			//列表
			$list = $this->m_uomc->list_cards($uid, true, $parr['size'], $parr['offset']);
		}
		empty($list) && $list = array();
		//显示
		$this->assign(compact('info', 'count', 'list'));
		$this->display('card');
	}
	
	/**
	 * 找回密码
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param int $step
	 * @param string $code
	 */
	public function reset_pwd($step=0, $code=''){
		$this->assign('step', $step);
		if($step == 1){//重置密码
			if($this->is_post()){
				$rp_id = $this->input->post('rp_id');
				$email = trim($this->input->post('email'));
				$password = $this->input->post('newpwd');
				$repwd = $this->input->post('repwd');
				$code = $this->post('code');
				
				//检查CODE是否过期
				$rs = $this->m_resetpwd->select(array('secretCode'=>$code, 'isUse'=>0));
				if(empty($rs) || $rs['email'] != $email){
					$this->echo_json(array('code'=>1, 'msg'=>'提交的重置已过期或错误的访问'));
					return;
				}
				
				//检查密码长度
				if(cstrlen($password) < 2 || cstrlen($password) > 15)
					$this->echo_json(array('code'=>1, 'msg'=>lang_message('signup_password_len_fail')));
				elseif($password !== $repwd)
					$this->echo_json(array('code'=>2, 'msg'=>lang_message('signup_password_fail')));
				else{
					$code = $this->m_user->reset_pwd($email, strtoupper(md5($password)));
					switch($code){
						case 1:$msg = '邮箱未关联到账户，重置密码失败了';break;
						default:
							//更新请求状态
							$this->m_resetpwd->used_reset($rp_id);
							$msg = lang_message('reset_pwd_suc');
							break;
					}
					$this->echo_json(compact('code', 'msg'));
				}
			}else{
			    // 判断是否手机端过来的
			    if(is_mobile()) {
			        redirect(sprintf('http://%s/i/reset_pwd/%s/%s', $this->in_host, $step, $code), '', REDIRECT_CODE);
			    }
			    
				//检查CODE是否过期
				$rs = $this->m_resetpwd->select(array('secretCode'=>$code, 'isUse'=>0));
				if(empty($rs)){
					$this->assign('err', 1);
					$this->assign('msg', '连接已过期或失效，请重新申请重置密码');
				}else{
					$now = time();
					$create = strtotime($rs['applyDate']);
					$minus = abs($create-$now);
					if($minus >= 24*3600){
						$this->assign('err', 1);
						$this->assign('msg', lang_message('reset_timeout'));
					}else{
						$this->assign('email', $rs['email']);
						$this->assign('rpid', $rs['id']);
					}
				}
				$this->assign('code', $code);
				$this->display('reset_pwd');
			}
		}else{//发邮件
			if($this->is_post()){
				$email = $this->post('email');
				if(empty($email))
					$this->echo_json(array('code'=>1, 'msg'=>'请填写您注册的邮箱地址'));
				else{
					//检查邮箱是否合法
					$c = $this->m_user->select(array('email'=>$email));
					if(empty($c))
						$this->echo_json(array('code'=>2, 'msg'=>'该邮箱未被注册过'));
					else{
						//邮件链接
						$query_string = strtoupper(md5('email:'.$email.'-'.microtime(true)));
						$data = array(
							'email' => $email,
							'secretCode' => $query_string,
							'applyDate' => date('Y-m-d H:i:s'),
							'isUse' => 0
						);
						//检查是否有过找回密码的请求
						$r = $this->m_resetpwd->select(array('email'=>$email));
						if($r){//编辑老数据
							$this->m_resetpwd->update($data);
						}else{
							$this->m_resetpwd->insert($data);
						}
						//邮件
						$link = base_url() . 'reset_pwd_1/'.$query_string;
						$replies = array('reset_url'=>$link);
						$content = lang_message('reset_mail_content', array($link, $link));
						$emails = array(
							array('mail'=>$email)
						);
						$this->load->library('mail');
						//发邮件
						$rs = $this->mail->send($emails, lang_message('reset_mail_subject'), $content);
						if($rs['code'] == 1)
							$this->echo_json(array('code'=>0, 'msg'=>lang_message('reset_mail_send_suc')));
						else 
							$this->echo_json(array('code'=>3, 'msg'=>lang_message('reset_mail_send_err')));
					}
				}
			}else{
			    // 判断是否手机端过来的
			    if(is_mobile()) {
			        redirect(sprintf('http://%s/i/reset_pwd/%s/%s', $this->in_host, $step, $code), '', REDIRECT_CODE);
			    }
			    
				$this->display('reset_pwd');
			}
		}
	}
	
	/**
	 * 邀请好友 
	 * Create by 2012-12-19
	 * @author liuweijava
	 */
	public function invite_friend(){
		
	}
	
}
   
 // File end