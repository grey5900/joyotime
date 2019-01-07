<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * User表操作
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-12-6
 */

class User_Model extends MY_Model {
	
	public function __construct(){
		parent::__construct();
		$this->load->helper('cache');
		$this->load->helper('api');
	}
	
	/**
	 * 获取用户详情
	 * Create by 2012-12-12
	 * @author liuweijava
	 * @param int $uid
	 * @return array
	 */
	public function get_info($uid){ 
		$this->_stars = $this->config->item('star');
        $user = $this->select(array('id'=>$uid));
        empty($user) && $user = array();
        if(!empty($user)){
	        $user['uid'] = $uid;
	    
		    $user['name'] = $user['nickname']?$user['nickname']:$user['username'];
		    $user['avatar_url'] = image_url($user['avatar'], 'head');
		    $user['avatar_m'] = image_url($user['avatar'], 'head', 'hmdp');
		    $user['avatar_h'] = image_url($user['avatar'], 'head', 'hhdp');
	        //等级
	        $l = $this->db->where(array('minExp <= '=>$user['exp'], 'maxExp > '=>$user['exp']))->order_by('level', 'desc')->limit(1)->get($this->_tables['userlevelconstans'])->first_row('array');
			$user['levelname'] = $l['title'];
	        $user['level'] = $l['level'];
			unset($l);
			//同步设置
			$c_sina = $this->db->where('uid', $uid)->count_all_results($this->_tables['usersinabindinfo']);
			$user['is_sync_sina'] = $c_sina ? 1 : 0;
			$c_tencent = $this->db->where('uid', $uid)->count_all_results($this->_tables['usertencentbindinfo']);
			$user['is_sync_tencent'] = $c_tencent ? 1 : 0;
			unset($c_sina, $c_tencent);
			//背景图
			$user['background'] = !empty($user['background']) ? image_url($user['background'], 'background') : '/static/img/background_default.png';
	        //关注开关
	  /*      if(empty($this->auth))
	        	$user['can_follow'] = 1;
	        elseif($uid == $this->auth['uid'])
	        	$user['can_follow'] = -1;
	        else{
	        	$c = $this->db->where(array('follower'=>$this->auth['uid'], 'beFollower'=>$uid))->count_all_results($this->_tables['usership']);
	        	$user['can_follow'] = $c > 0 ? 0 : 1;
	        	unset($c);
	        }*/
			
			/*获取用户地主定位信息*/
			/*$mayors = $this->db->select("latitude,longitude")->where("mayorId",$uid)->order_by("mayorDate desc")->limit(10)->get("Place")->result_array();
			$user['mayors'] = $mayors;*/
			
			if(!empty($user['birthdate'])){
				list($y, $m, $d) = explode('-', $user['birthdate']);
				$m = intval($m);
				$d = intval($d);
				$stars = $this->_stars[$m];
				$ds = array_values($stars);
				$stars = array_flip($stars);
				if($d <= $ds[0])
				 $user['star'] = $stars[$ds[0]];
				//年龄
				$n_y = intval(gmdate('Y', time()+8*3600));
				$user['age'] = abs($n_y - $y);
			}else{
					$user['star'] = '-';
					$user['age'] = '-';
			}
			//性别
			$user['gender'] = $user['gender'] ? '男' : '女';
			
        }
        return $user;
	}
	
	/**
	 * 修改密码
	 * Create by 2012-12-12
	 * @author liuweijava
	 * @param array $user
	 * @param string $old_pwd
	 * @param string $new_pwd
	 * @return array code:1=原密码错误，2=修改密码失败，0=修改密码成功
	 */
	public function modify_pwd($user, $old_pwd, $new_pwd){
		//检查原密码是否正确
		if(strtoupper(md5($old_pwd)) !== $user['password'])
			return array('code'=>1, 'msg'=>lang_message('modify_pwd_old_err'));
		else{
			$password = strtoupper(md5($new_pwd));
			$this->db->where('id', $user['id'])->set('password', $password)->update($this->_tables['user']);
			return array('code'=>0, 'msg'=>lang_message('modify_pwd_success'));
		}
	}
	
	/**
	 * 重置密码
	 * Create by 2012-12-30
	 * @author liuweijava
	 * @param string $email
	 * @param string $pwd
	 * @return int code:0=重置成功，1=账号不存在，2=修改失败
	 */
	public function reset_pwd($email, $pwd){
		$user = $this->db->where('email', $email)->limit(1)->get($this->_tables['user'])->first_row('array');
		if(empty($user))
			return 1;
		else{
			$this->db->where('id', $user['id'])->set('password', $pwd)->update($this->_tables['user']);
			return 0;
		}
	}
	
	/**
	 * 修改个人资料
	 * Create by 2012-12-12
	 * @author liuweijava
	 * @param int $uid
	 * @param array $data
	 * @return array,code=0:操作成功，code > 0:操作失败
	 */
	public function edit_basic($uid, $data){
		$data['basic']['uid'] = $uid;
        $result = json_decode(call_api('setting', $data['basic']), true);
        if(empty($result) || $result['result_code']){
          	//修改失败
           	return array('code'=>$result['result_code'], 'msg'=>$this->lang->line('setting_fail'));
        }else{
			if(!empty($data['birth'])){
				$edit = array('birthdate'=>$data['birth']);
				$this->db->where('id', $uid)->update($this->_tables['user'], $edit);
			}
			//更新缓存
			get_data('user', $uid, true);
			// 更新备注
			get_data('mnemonic', $uid . '-' . $uid, true);
			//修改成功
			return array('code'=>$result['result_code'], 'msg'=>$this->lang->line('setting_success'));
        }
	}
	
	/**
	 * 查询指定用户的好友数量
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 * @param boolean $be_follow true=查粉丝；false=查关注
	 */
	public function count_friends($uid, $be_follow=true){
		$this->db->where(($be_follow ? 'beFollower' : 'follower'), $uid);
		return $this->db->count_all_results($this->_tables['usership']);
	}
	
	/**
	 * 查询指定用户的好友
	 * Create by 2012-12-13
	 * @author liuweijava
	 * @param int $uid
	 * @param boolean $be_follow true=查粉丝；false=查关注
	 * @param int $size
	 * @param int $offset
	 */
	public function get_friends($uid, $be_follow=true, $size=7, $offset=0){
		$this->db->select($this->_tables['user'].'.id, '.$this->_tables['user'].'.nickname, '.$this->_tables['user'].'.username, '.$this->_tables['user'].'.avatar, '.$this->_tables['user'].'.description, '.$this->_tables['usermnemonic'].'.mnemonic');
		$this->db->join($this->_tables['user'], $this->_tables['user'].'.id = '.$this->_tables['usership'].'.'.($be_follow ? 'follower' : 'beFollower'), 'inner');		
		$this->db->join($this->_tables['usermnemonic'], $this->_tables['usermnemonic'].'.mUid='.$this->_tables['user'].'.id AND '.$this->_tables['usermnemonic'].'.uid='.$uid, 'left');
		//查询
		$list = $this->db->where($this->_tables['usership'].($be_follow ? '.beFollower' : '.follower'), $uid)->order_by($this->_tables['usership'].'.createDate', 'desc')->limit($size, $offset)->get($this->_tables['usership'])->result_array();
		foreach($list as &$row){
			$row['nickname'] = $row['mnemonic'] ? $row['mnemonic'] : ($row['nickname'] ? $row['nickname'] : $row['username']);
			$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
			unset($row);
		}
		return $list;
	}
	
	/**
	 * 更新好友数
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param boolean $be_follow TRUE=更新粉丝数；FALSE=更新关注数
	 * @param boolean $is_minus TRUE=减少1，FALSE=增加1
	 */
	public function update_friend_count($uid, $be_follow = true, $is_minus = false){
		$col = $be_follow ? 'beFollowCount' : 'followCount';
		$this->db->where('id', $uid)->set($col, $col.($is_minus?'-':'+').'1', false)->update($this->_tables['user']);
	}
	
	/**
	 * 更新统计数
	 * Create by 2012-12-14
	 * @author liuweijava
	 * @param int $uid
	 * @param string $stat_col
	 * @param boolean $is_minus TRUE=减少1，FALSE=增加1
	 */
	public function update_stat_count($uid, $stat_col, $is_minus = false){
		$set = $stat_col.(is_minus ? '-1':'+1');
		$this->db->where('id', $uid)->set($stat_col, $set, false)->update($this->_tables['user']);
	}
	
	public function get_all_friends($uid,$size=8){
		$sql ='select DISTINCT follower+beFollower-'.$uid.' as uid 
		       from '.$this->_tables['usership'].' where 
			   beFollower='.$uid.' or follower='.$uid.' 
			   order by createDate desc 
		       limit '.$size;
		
		$list = $this->db->query($sql)->result_array();
		foreach($list as &$row){
			$user = get_data('user',$row['uid']);
			$row['avatar'] = $user['avatar_m'];
			$row['nickname'] = $user['name'];
			unset($user);
		}
		return $list;
	}
}