<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
/*
 * 活动
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-17
 */

class Event extends MY_Controller {
	
	var $configs;
	
	function __construct(){
		parent::__construct();
		$this->configs = require_once(config_item('event_config'));
	}
	
	/**
	 * 活动首页
	 * Create by 2012-9-24
	 * @author liuw
	 * @param int $event_id
	 * @param int $uid
	 */
	function index($event_id=0, $uid=0, $lat=0, $lng=0){
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		
		!$event_id && $event_id = $this->get('id');
		!$uid && $uid = $this->get('uid');
		!$uid && $uid = 0;
		!$lat && $lat = $this->get('lat');
		!$lng && $lng = $this->get('lng');
		empty($lat) && $lat = 0;
		empty($lng) && $lng = 0;
		
		(empty($event_id) || !$event_id) && die($this->lang->line('event_id_empty'));
	//	($cfg['must_apply'] && empty($uid)) && die($this->lang->line('event_user_faild'));
		//(!$cfg['must_apply'] && empty($uid)) && $uid = 0;
		empty($uid) && $uid = 0;
		$this->assign(compact('event_id', 'uid', 'lat', 'lng'));
		//活动详情
		$tmp = $cfg['template'].'_index';
		$event = $this->db->where('id', $event_id)->get('Event')->first_row('array');
		if(!empty($event)){
			$this->db->where('id', $event_id)->set('hits', 'hits+1', false)->update('Event');
		}
		//是否可以报名
		$can_join = 0;
		//最新参与的5名好友
		$joines = array();
		
		if($cfg['must_apply']){//必须报名
			if(!empty($uid))
				$can_join = $this->_can_join($event_id, $uid);
		}
		//最新参与的5名好友
		$joines = $this->joines($event_id, $uid, 5);
	//	$count_joines = $this->_get_feed_joins($event_id);
		//动态
		$feed = array();
		$cfg['allow_post'] && $feed = $this->feeds($event_id,$uid, 1);
		
		//-------add 2012-11-08 参与人数增加POST参与活动的人数-----
		$fc = $this->_get_feed_joins($event);
	/*	if($cfg['old_event']){
			$old = $this->db->where('id', $cfg['old_event'])->get('Event')->first_row('array');
			$fc += $this->_get_feed_joins($old)+$old['joins'];
		} */
		$event['joins'] = $event['joins'] + $fc;	
		$count_joines = $event['joins'];	
		//---------
		//-------add 2012-11-08 抽奖日志--------	
		//抽奖日志
		$logs = array();
		if($cfg['prize']){
			$sql = 'SELECT a.uid , a.prizeId , a.eventId , a.createDate , c.nickname, c.username, p.prizeName FROM ( select uid , prizeId, eventId, MAX(createDate) AS createDate from  EventLotteryLog where  eventId = ? GROUP BY uid, prizeId ORDER BY createDate DESC LIMIT 100 ) a LEFT JOIN EventUser c ON a.uid=c.uid and c.eventId = a.eventId LEFT JOIN EventPrize p ON p.eventId = a.eventId AND p.prizeId = a.prizeId';
			$query = $this->db->query($sql, array($event_id))->result_array();
			$this->config->load('event_config');
			$tconfigs = $this->config->item('event_cfg');
			foreach($query as $row){//格式化日志
				if(!empty($row['username']) || !empty($row['nickname'])){
					$uname = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
					$tcfg = $tconfigs[$event_id];
					$t = $tcfg['t'];
					$d = !empty($row['prizeId']) ? '获得了' : '未获奖';
					$prize = '';
					if(!empty($row['prizeName'])){
						$prize = format_msg($tcfg['p'], array('prize'=>$row['prizeName']));
					}else 
						$t = str_replace('@{p}', '', $t);
					$u = $uname;
					$logs[] = format_msg($t, array('u'=>$u, 'd'=>$d, 'p'=>$prize));		
				}	
			}
		}
		//----------
		//--------Add 2012-11-08 奖品列表------
		$prizes = array();
		if($cfg['prize']){
			$rs = $this->db->where(array('eventId'=>$event_id, 'status'=>0))->get('EventPrize')->result_array();
			foreach($rs as $row){
				$prizes[$row['prizeId']] = $row;
			}
		}
		//----------
		$this->assign(compact('event', 'can_join', 'feed', 'joines', 'cfg', 'logs', 'prizes', 'count_joines', 'uid'));
		$this->display($tmp, 'm');
	//	$this->echo_json(compact('event', 'can_join', 'feed', 'joines', 'uid', 'lat', 'lng'));
	}
	
	/**
	 * 显示PART页面
	 * Create by 2012-10-25
	 * @author liuw
	 * @param int $event_id
	 * @param int $uid
	 * @param int $part
	 * @param float $lat
	 * @param float $lng
	 */
	function part($event_id, $uid=0, $part=1, $lat=0, $lng=0){
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		
		//------Add 2012-111-09 HITS+1
		$this->db->where('id', $event_id)->set('hits', 'hits+1', false)->update('Event');
		//-----------
		
		!$event_id && $event_id = $this->get('id');
		!$uid && $uid = $this->get('uid');
		!$lat && $lat = $this->get('lat');
		!$lng && $lng = $this->get('lng');
		empty($lat) && $lat = 0;
		empty($lng) && $lng = 0;
		
		(empty($event_id) || !$event_id) && die($this->lang->line('event_id_empty'));
		//($cfg['must_apply'] && empty($uid)) && die($this->lang->line('event_user_faild'));
		empty($uid) && $uid = 0;
		
		//活动详情
		$event = $this->db->where('id', $event_id)->limit(1)->get('Event')->first_row('array');
		
		if($cfg['apply_for_tag'] != 0)//获取对应的TAG
			$tags = $this->db->where('eventId', $event_id)->order_by('createDate', 'asc')->get('EventTag')->result_array();	
			
		//抽奖日志
		$logs = array();
	//	$sql = 'SELECT DISTINCT a.*, c.nickname, c.username FROM (SELECT DISTINCT uid,prizeId,eventId,MAX(createDate) AS createDate FROM EventLotteryLog GROUP BY uid, prizeId) a INNER JOIN EventLotteryLog b ON b.createDate=a.createDate LEFT JOIN EventUser c ON c.uid=a.uid WHERE b.eventId=? ORDER BY b.createDate DESC LIMIT 100';
		$sql = 'SELECT a.uid , a.prizeId , a.eventId , a.createDate , c.nickname, c.username, p.prizeName FROM ( select uid , prizeId, eventId, MAX(createDate) AS createDate from  EventLotteryLog where  eventId = ? GROUP BY uid, prizeId ORDER BY createDate DESC LIMIT 100 ) a LEFT JOIN EventUser c ON a.uid=c.uid and c.eventId = a.eventId LEFT JOIN EventPrize p ON p.eventId = a.eventId AND p.prizeId=a.prizeId';
		$query = $this->db->query($sql, array($event_id))->result_array();
		foreach($query as $row){//格式化日志
		//	$user = $this->db->where(array('uid'=>$row['uid'],'eventId'=>$event_id))->limit(1)->get('EventUser')->first_row('array');
				if(!empty($row['nickname']) || !empty($row['username'])){
				$uname = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
				$logs[] = sprintf('[%s]用户%s', $uname, !empty($row['prizeName']) ? '已中奖 ['.$row['prizeName'].']':'未中奖');		
			}	
		}
		
		//统计通过POST参与到活动的人数
		$keyword = $cfg['keyword'];
		$sql = 'SELECT COUNT(uid) AS f_count FROM (SELECT DISTINCT uid FROM Post WHERE status < 2 AND type IN (2,3) ';
		if(!empty($keyword))
			$sql .= "AND content LIKE '%{$keyword}%'";
		$sql .= 'GROUP BY uid) tmp';
		$rs = $this->db->query($sql)->first_row('array');
		$event['joins'] = $event['joins'] + (!empty($rs) ? intval($rs['f_count']) : 0);
		
		$tmp = $cfg['template'].'_part'.$part;
		$this->assign(compact('tags', 'event', 'event_id', 'uid', 'lat', 'lng', 'logs'));
		$this->display($tmp, 'm');
	}
	
	/**
	 * 统计POST参与活动的人数
	 * Create by 2012-11-8
	 * @author liuweijava
	 * @param array $eventId
	 */
	function _get_feed_joins($event){
		$eventId = $event['id'];
		$cfg = $this->configs[$event['id']];
		$sql = 'SELECT COUNT(DISTINCT uid) AS f_count FROM Post p LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.status < 2 AND p.createDate > \''.substr($event['createDate'], 0, -9).'\'';
		if(isset($cfg['allow_post']) && !empty($cfg['allow_post'])){
			$add = "('".implode("','", $cfg['allow_post'])."')";
			$sql .= ' AND p.type IN '.$add.' ';
		}
		if(isset($cfg['follow_brand']) && $cfg['follow_brand'])
			$sql .= " AND pl.brandId = '{$cfg['follow_brand']}' ";
		if(isset($cfg['keyword']) && !empty($cfg['keyword']))
			$sql .= " AND p.content LIKE '%{$cfg['keyword']}%' ";
		if(isset($cfg['place_id']) && !empty($cfg['place_id'])){
			$sp = implode("','", $cfg['place_id']);
			$sql .= " AND p.placeId IN ('{$sp}')";
		}
		$rs = $this->db->query($sql)->first_row('array');
		return !empty($rs) && $rs['f_count'] ? $rs['f_count'] : 0;
	}
	
	/**
	 * 抽奖
	 * Create by 2012-10-26
	 * @author liuw
	 * @return code:0=获奖，1=未获奖，2=不是会员，3=奖品已发完，4=未关联TAG，5=已获过奖了，6=抽奖已结束，7=周期抽奖次数已达上限，8=未知错误，9=未报名，-1=未登录，10=活动未开始
	 */
	function lottery($event_id){
		$callback = $this->get('callback');
		empty($callback) && $callback = '';
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		$p_freq = $cfg['prize_freq'];
		//list(最大抽奖次数，间隔小时，间隔天数，间隔月数，间隔年数)
		list($lot_count, $interval, $unit) = explode('|', $p_freq);
		$can_winning = $cfg['can_winning'];//可中奖次数
		$event = $this->db->where(array('id'=>$event_id, 'status'=>0))->get('Event')->first_row('array');
		if($this->is_post() && !empty($event)){
			//检查抽奖是否已结束
			$now = time()+8*3600;
			$rs = $this->db->select('createDate, status')->where('id', $event_id)->get('Event')->first_row('array');
			$start = strtotime($rs['createDate'])+8*3600;
			$run = floor((abs($start-$now)) / (24*3600));
			!$run && $run = 1;
		//	exit($rs['status'].' : '.$run.' - '.$cfg['prize_day']);
		//	$this->echo_json(array('code'=>6, 'msg'=>$rs), $callback);
			($rs['status'] || $run > intval($cfg['prize_day'])) && $this->echo_json(array('code'=>6), $callback);
				
			$uid = $this->post('uid');	
			(empty($uid) || !$uid) && $this->echo_json(array('code'=>-1), $callback);
			//检查是否报名
			if($cfg['must_apply']){
				$app = $this->db->where(array('uid'=>$uid, 'eventId'=>$event_id, 'status'=>0))->count_all_results('EventUser');
				!$app && $this->echo_json(array('code'=>9), $callback);
			}
			//检查是否关联了标签
			if($cfg['apply_for_tag']){
				$u_tags = $this->db->where(array('uid'=>$uid, 'eventId'=>$event_id))->count_all_results('EventUserOwnTag');
				$u_tags <= 0 && $this->echo_json(array('code'=>4, 'msg'=>'还未加入帮派，无师门奖励，请选择你中意的门派（可多选），抱好大腿才有前途。'), $callback);
			}
			//检查是否是指定品牌的会员
			if($cfg['follow_brand']){
				$brand = $this->db->where('id', $cfg['follow_brand'])->get('Brand')->first_row('array');
				$card = $this->db->where(array('uid'=>$uid, 'brandId'=>$cfg['follow_brand'], 'isBasic'=>1))->count_all_results('UserOwnMemberCard');
			//	echo $this->db->last_query();
				!$card && $this->echo_json(array('code'=>2,'msg'=>'请先通过客户端关注品牌['.$brand['name'].']后再来抽奖吧'), $callback);
			}
			//检查是否已达到可中奖次数
			$winnes = $this->db->where(array('eventId'=>$event_id, 'uid'=>$uid, 'prizeId > '=>0))->count_all_results('EventLotteryLog');
			$winnes == $can_winning && $this->echo_json(array('code'=>5, 'msg'=>$this->lang->line('event_lottery_has_winned')), $callback);
			//检查是否已到最大抽奖次数
			$now = time()+8*3600;
			$begin = gmdate('Y-m-d 00:00:00', $now);
			$can_lot = true;
			if($interval !== '*'){//设置了间隔年数的
				switch($unit){
					case 'y'://以年为单位间隔
						$end_date = $now + 365*24*3600*intval($interval);
						$end = gmdate('Y-m-d 23:59:59', $end_date);
						$int_date = '本年度';
						$next_freq = '下年度';
						break;
					case 'm'://以月为单位间隔
						$end_date = $now + 31*24*3600*intval($interval);
						$end = gmdate('Y-m-d 23:59:59', $end_date);
						$int_date = '本月';
						$next_freq = '下月';
						break;
					case 'd'://以天为单位间隔，需要判断间隔天数，如果只是1天的话直接处理$now
						$intv = intval($interval);
						if($intv == 1){
							$end = gmdate('Y-m-d 23:59:59', $now);
							$int_date = '今天';
							$next_freq = '明天';
						}else{
							$end_date = $now + 24*3600*$intv;
							$end = gmdate('Y-m-d 23:59:59', $end_date);
							$int_date = '这'.$interval.'天';
							$next_freq = '下'.$interval.'天';
						}
						break;
					case 'h'://以小时为单位
						$end_date = $now + 3600*intval($interval);
						$end = gmdate('Y-m-d H:00:00', $end_date);
						$begin = gmdate('Y-m-d H:00:00', $now);
						$int_date = '这'.$interval.'个小时';
						$next_freq = '下'.$interval.'个小时';
						break;
				}
				//检查已抽了多少次
				$count = $this->db->where(array('eventId'=>$event_id, 'uid'=>$uid))->where("createDate BETWEEN '{$begin}' AND '{$end}'")->count_all_results('EventLotteryLog');
				$count+1 > intval($lot_count) && $can_lot = false;
			}
			if(!$can_lot){
				$this->echo_json(array('code'=>7, 'msg'=>format_msg($this->lang->line('event_lottery_close'), compact('int_date', 'next_freq'))), $callback);
			}
			
			//--------edit 2012-11-08 根据配置确定是从缓存获取奖品还是从数据库获取奖品
			$prize = null;
			//抽奖
			$data = array('eventId'=>$event_id, 'uid'=>$uid);
			$all_count = 0;
			//从缓存中获取奖品列表
			if($cfg['gen_prize_list']){
				$cache = get_cache($event_id.'_event_'.gmdate('Y-m-d',time()+8*3600));
				//检查还有没有奖品
			//	exit('prize_date:'.$cache['date'].' now:'. gmdate('Y-m-d', time()+8*3600));
				($cache['date'] !== gmdate('Y-m-d', time()+8*3600) || !count($cache['prizes'])) && $this->echo_json(array('code'=>3, 'msg'=>$this->lang->line('event_lottery_prize_empty')), $callback);
			}else{
				$p_list = $this->get_prize_list($event_id);
				$cache = array();
				foreach($p_list as $row){
					$cache['prizes'][] = $row;
				}
				empty($cache['prizes']) && $this->echo_json(array('code'=>2), $callback);
			}
			empty($cache) && $this->echo_json(array('code'=>2), $callback);
			$idx = rand(0, intval($cfg['max_index']));
			$prizes = $cache['prizes'];
			isset($prizes[$idx]) && !empty($prizes[$idx]) && $prize = $prizes[$idx];	
		//	$p_count = $prize['prizeCount'] - $prize['grantCount'];
		//	!$p_count && $prize = null;//奖品已经发完了，算这次抽奖未中奖		
			
			if($prize != null && !empty($prize)){
				if($cfg['gen_prize_list']){
					unset($prizes[$idx]);
					$cache['prizes'] = $prizes;
					//更新奖品清单
					set_cache($event_id.'_event_'.gmdate('Y-m-d',time()+8*3600), $cache, 24*3600);
				}
				//检查是否已发完
				$data['prizeId'] = $prize['prizeId'];
				//更新奖品发放数量
				$this->db->where('prizeId', $prize['prizeId'])->set('grantCount', 'grantCount+1', false)->update('EventPrize');
				$msg = $prize;
				//记录抽奖日志
				$this->db->insert('EventLotteryLog', $data);
				unset($prize);
				$this->echo_json(array('code'=>0, 'msg'=>$msg), $callback);	
			}else{ 
				$data['prizeId'] = 0;
				$hs_lot = intval($lot_count) - ($count+1);
				if($hs_lot >= 0){
			//		$has_lot <= 0 && $has_lot = 1;
				/*	if($count == 0)
						$msg = '运气也是一种实力，师弟/师妹，今日还剩2次机会哦~';
					elseif($count == 1)
						$msg = '师弟/师妹，看来你的实力还需历练哦，今日还剩1次机会哦~';
					$arr = compact('has_lot', 'msg');
				//	$msg = format_msg($this->lang->line('event_lottery_fail'), $arr);*/
					$msg = $count+1;
					$code = 1;
					//记录抽奖日志
					$this->db->insert('EventLotteryLog', $data);
				}else{
					$code = 7;
					$msg = format_msg($this->lang->line('event_lottery_close'), compact('int_date', 'next_freq'));
					//记录抽奖日志
				//	$this->db->insert('EventLotteryLog', $data);
				}
				$this->echo_json(compact('code', 'msg'), $callback);
			}	
		}else{
			$this->echo_json(array('code'=>10), $callback);
		}
	}
	
	/**
	 * 投放奖品
	 * Create by 2012-10-25
	 * @author liuw
	 * @param int $event_id
	 */
	function put_prize($event_id){
		$now_time = time()+8*3600;
		$now = gmdate('Y-m-d',$now_time);
		$cache = get_cache($event_id.'_event_'.$now);
		if(!isset($cache) || empty($cache)){
			//计算缓存生命周期
		//	$this->config->load('event_config');
		//	$cfgs = $this->config->item('event_cfg');
			$cfg = $this->configs[$event_id];
			$p_roles = $cfg['prize_role'];
			$run_day = $cfg['prize_day'];
			//检查是否已超出了抽奖期
			$e = $this->db->where('id', $event_id)->limit(1)->get('Event')->first_row('array');
			$begin = strtotime($e['createDate'])+8*3600;
			$runned = ceil(abs($begin - $now_time) / (24*3600));
			$has_run = $run_day - $runned;
			$has_run < 0 && $has_run = 0;
			$size = $this->get_prize_size($event_id, $cfg, $has_run, $runned);
			if($size){//没过期
				//获得还没有发完的奖品
				$prizes = $this->db->where(array('eventId'=>$event_id, 'status'=>0, 'prizeCount - grantCount > '=>0))->order_by('prizeId', 'asc')->get('EventPrize')->result_array();
				//生成奖品列表
				$list = array();
			//	echo $size.'<br/>';
				for($i=0;$i<$size;$i++){
					$id = rand(0, count($prizes)-1);
					$prize = $prizes[$id];
					//检查投放规则
					$role = $p_roles[$prize['prizeId']];
					if(!empty($role)){
						$max = $role['max'];//检查最大投放量
						//检查当天发了几份
						$now_size = $this->db->where(array('prizeId'=>$prize['prizeId'], 'eventId'=>$event_id))->where("createDate BETWEEN '{$now} 00:00:00' AND '{$now} 23:59:59'")->count_all_results('EventLotteryLog');
				//		echo $this->db->last_query().'<br/>';
						if($now_size < $max){
							$c = $this->_count_now_list($list, $prize['prizeId']);
							$now_size += $c;
				//			echo $prize['prizeName'].'最大投放：'.$max.'; 已投放：'.$now_size.'; 当前奖池：'.$c.'; 已抽走：'.$prize['grantCount'].'; 总数：'.$prize['prizeCount'].'<br/>';
							$prize['grantCount'] + $c < $prize['prizeCount'] && $now_size < $max && $list[] = $prize;//超过或达到最大投放量就停止投放
						}
					}else{
						//检查剩余数量
						$c = $this->_count_now_list($list, $prize['prizeId']);
						$putSize = $prize['grantCount'] + $c;
						//数据库还有剩余的奖品才加入奖池，否则跳过
						if($putSize < $prize['prizeCount']){
							$list[] = $prize;
						}						
					}
				}
				$cache = array(
					'date' => $now,
					'prizes' => $list
				);
			//	print_r($cache);
				//缓存列表
				$end = strtotime($now.' 23:59:59')+8*3600;
				$life = $end - $now_time;
				set_cache($event_id.'_event_'.$now, $cache, $life);
			}
		}
	}
	
	/**
	 * 计算指定奖品在现在奖池中的数量
	 * Create by 2012-11-15
	 * @author liuweijava
	 * @param array $list
	 * @param int $prizeId
	 * @return int
	 */
	function _count_now_list($list, $prizeId){
		$count = 0;
		foreach($list as $k=>$v){
			$v['prizeId'] == $prizeId && $count+=1;
		}
		return $count;
	}
	
	/**
	 * 更新缓存 
	 * Create by 2012-11-14
	 * @author liuweijava
	 * @param int $event_id
	 */
	function update_cache($event_id){
		$now_time = time()+8*3600;
		$now = gmdate('Y-m-d',$now_time);
		@set_cache($event_id.'_event_'.$now, '', -1);
		$this->put_prize($event_id);
	}
	
	/**
	 * 获取随机的奖品列表
	 * Create by 2012-11-12
	 * @author liuweijava
	 * @param int $event_id
	 */
	function get_prize_list($event_id){
		$list = array();
		$now_time = time()+8*3600;
		$now = gmdate('Y-m-d',$now_time);
		$cfg = $this->configs[$event_id];
		$p_roles = $cfg['prize_role'];
		$run_day = $cfg['prize_day'];
		//检查是否已超出了抽奖期
		$e = $this->db->where('id', $event_id)->limit(1)->get('Event')->first_row('array');
		$begin = strtotime($e['createDate'])+8*3600;
		$runned = ceil(abs($begin - $now_time) / (24*3600));
		$has_run = $run_day - $runned;
		$has_run < 0 && $has_run = 0;
	//	$size = $this->get_prize_size($event_id, $cfg, $has_run, $runned);
		if($has_run >= 0){//没过期
			//奖品总数
		//	$rs = $this->db->query('SELECT SUM(prizeCount) AS allCount FROM EventPrize WHERE eventId = ? AND status = 0 AND prizeCount-grantCount > 0', array($event_id))->first_row('array');
		//	$a_c = intval($rs['allCount']);
			//还没发完的奖品列表
			$prizes = $this->db->where(array('eventId'=>$event_id, 'status'=>0, 'prizeCount - grantCount > '=>0))->get('EventPrize')->result_array();
			foreach($prizes as $row){
				//计算剩余时间的日发送量
				$c = floor(($row['prizeCount'] - $row['grantCount']) / ($has_run ? $has_run : 1));
				!$c && $c = 1;
				for($i=0;$i<$c;$i++){
					$list[] = $row;
				}
			}
		}
		return $list;
	}
	
	/**
	 * 计算当天需要投放的奖品数量
	 * Create by 2012-10-25
	 * @author liuw
	 * @param array $cfg
	 * @param int $remaining
	 * @param int $the_day
	 */
	function get_prize_size($event_id, $cfg, $remaining, $the_day){
		if($remaining > 0){
			$run_day = $cfg['prize_day'];
			//奖品总数
			$rs = $this->db->select('SUM(prizeCount) AS allCount')->where(array('eventId'=>$event_id, 'status'=>0, 'prizeCount > '=>0))->get('EventPrize')->first_row('array');
			$c_all = intval($rs['allCount']);
			$p_count = $c_all;
			if($the_day < ceil($run_day / 2))
				$ln = ceil($p_count * 0.12);
			elseif($p_count > 0)
				$ln = ceil($p_count / ($run_day - $the_day));
			else 
				$ln = 0;
			return $ln;
		}else
			return 0;
	}
	
	/**
	 * TAG报名
	 * Create by 2012-10-25
	 * @author liuw
	 */
	function tag_join(){
		if($this->is_post()){
			$callback = $this->get('callback');
			empty($callback) && $callback = '';
			$eventId = $this->post('event_id');
			$uid = $this->post('uid');
			$tagId = $this->post('tagId');
			$latitude = $this->post('lat');
			empty($latitude) && $latitude = 0;
			$longitude = $this->post('lng');
			empty($longitude) && $longitude = 0;
			(empty($eventId) || !$eventId) && $this->echo_json(array('code'=>9), $callback);
			(empty($uid) || $uid == 0) && $this->echo_json(array('code'=>-1, 'msg'=>$this->lang->line('event_user_faild')), $callback);
			//检查参数
	//		(empty($latitude) || empty($longitude)) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('event_join_faild_for_param')));
			//检查活动状态
			$event = $this->db->where('id', $eventId)->get('Event')->first_row('array');
			(empty($event) || $event['status']) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('event_over')), $callback);
			//检查TAG			
			$q = $this->db->where('tagId', $tagId)->limit(1)->get('EventTag')->first_row('array');
			(empty($q) || $q['status']==1)&&$this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('event_join_faild')), $callback);
			$user = $this->db->where('id', $uid)->limit(1)->get('User')->first_row('array');
			$username = $user['username'];
			$nickname = !empty($user['nickname']) ? $user['nickname'] : $username;
			//检查是否已报名
			$has_join = $this->_can_join($eventId, $uid);
			if(!$has_join){//已报过名
				$data = compact('uid', 'tagId', 'eventId');
				//检查是否已关联过所选TAG
				$c = $this->db->where($data)->count_all_results('EventUserOwnTag');
				if(!$c){
					$this->db->insert('EventUserOwnTag', $data);
					//更新TAG人数
					$this->db->where('tagId', $tagId)->set('memberCount', 'memberCount+1', false)->update('EventTag');
					$msg = format_msg($this->lang->line('event_join_tag_success'), array('tag'=>$q['tagName']));
					$this->echo_json(array('code'=>0, 'msg'=>$msg), $callback);
				}else{
					$this->echo_json(array('code'=>1, 'msg'=>format_msg($this->lang->line('event_tag_has_joined'), array('tag'=>$q['tagName']))), $callback);
				}
			}else{//新报名
				$data = compact('eventId', 'uid', 'latitude', 'longitude', 'username', 'nickname');
				$this->db->insert('EventUser', $data);
				$id = $this->db->insert_id();
				if(!$id)
					$this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('event_join_faild')), $callback);
				else{
					//更新活动的参加人数
					$this->db->where('id', $eventId)->set('joins', 'joins+1', false)->update('Event');
					//关联TAG
					$data = compact('uid', 'tagId', 'eventId');
					$this->db->insert('EventUserOwnTag', $data);
					//更新TAG人数
					$this->db->where('tagId', $tagId)->set('memberCount', 'memberCount+1', false)->update('EventTag');
					$msg = format_msg($this->lang->line('event_join_tag_success'), array('tag'=>$q['tagName']));
					$this->echo_json(array('code'=>0, 'msg'=>$msg), $callback);
				}
			}
		}
	}
	
	/**
	 * 指定用户是否可参加指定的活动
	 * Create by 2012-9-24
	 * @author liuw
	 * @param int $event_id
	 * @param int $uid
	 * @return boolean
	 */
	function _can_join($event_id, $uid){
		$count = $this->db->where(array('eventId'=>$event_id, 'uid'=>$uid, 'status'=>0))->count_all_results('EventUser');
		return !$count ? 1:0;
	}
	
	/**
	 * 参加活动
	 * Create by 2012-9-24
	 * @author liuw
	 * @param int $vent_id
	 * @param int $uid
	 * @param float $lat
	 * @param float $lng
	 * @return array， code:0=报名成功，1=活动或用户ID异常，2=报名失败;msg=提示信息
	 */
	function join(){
		if($this->is_post()){
			$callback = $this->get('callback');
			empty($callback) && $callback = '';
			$eventId = $this->post('event_id');
			$uid = $this->post('uid');
			$latitude = $this->post('lat');
			empty($latitude) && $latitude = 0;
			$longitude = $this->post('lng');
			empty($longitude) && $longitude = 0;
			
			(empty($eventId) || empty($uid) || $uid == 0) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('event_user_faild')), $callback);
			//检查参数
			(empty($latitude) || empty($longitude)) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('event_join_faild_for_param')), $callback);
			//检查活动状态
			$event = $this->db->where('id', $eventId)->get('Event')->first_row('array');
			(empty($event) || $event['status']) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('event_over')), $callback);
			
			//检查是否可以报名
			!$this->_can_join($eventId, $uid) && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('event_has_joined')), $callback);
			//报名
			$data = compact('eventId', 'uid', 'latitude', 'longitude');
			//记录报名数据
			$this->db->insert('EventUser', $data);
			$id = $this->db->insert_id();
			!$id && $this->echo_json(array('code'=>2, 'msg'=>$this->lang->line('event_join_faild')), $callback);
			//报名人数+1
			$this->db->where('id', $eventId)->set('joins', 'joins+1', false)->update('Event');
			$this->echo_json(array('code'=>0, 'msg'=>$this->lang->line('event_join_success')), $callback);
		}
	}
	
	/**
	 * 报名参加活动
	 * Create by 2012-11-8
	 * @author liuweijava
	 * @return code:0=报名成功，1=报名失败，2=已报过名了，3=活动异常（包括以过期），-1=用户异常，5=未知错误。不再返回提示消息，由前台根据code组装消息
	 */
	function join_v2(){
		if($this->is_post()){
			$callback = $this->get('callback');
			empty($callback) && $callback = '';
			$eventId = $this->post('event_id');
			(empty($eventId) || !$eventId) && $this->echo_json(array('code'=>3), $callback);
			$uid = $this->post('uid');
			(empty($uid) || !$uid) && $this->echo_json(array('code'=>-1), $callback);
			$lat = $this->post('lat');
			empty($lat) && $lat = 0;
			$lng = $this->post('lng');
			empty($lng) && $lng = 0;
			//检查活动是否正常
			$rs = $this->db->where(array('id'=>$eventId, 'status'=>0))->count_all_results('Event');
			!$rs && $this->echo_json(array('code'=>3), $callback);
			//检查是否已报过名了
			$rs = $this->db->where(array('uid'=>$uid, 'eventId'=>$eventId, 'status'=>0))->count_all_results('EventUser');
			$rs && $this->echo_json(array('code'=>2), $callback);
			//获取用户名和昵称
			$user = $this->db->select('username, nickname')->where('id', $uid)->get('User')->first_row('array');
			//报名
			$data = array('uid'=>$uid, 'eventId'=>$eventId, 'latitude'=>$lat, 'longitude'=>$lng);
			!empty($user) && !empty($user['username']) && $data['username'] = $user['username'];
			!empty($user) && !empty($user['nickname']) && $data['nickname'] = $user['nickname'];
			$this->db->insert('EventUser', $data);
			$id = $this->db->insert_id();
			!$id && $this->echo_json(array('code'=>1), $callback);
			if($id){
				$this->db->where('id', $eventId)->set('joins', 'joins+1', false)->update('Event');
				$this->echo_json(array('code'=>0), $callback);
			}
		}
	}
	
	/**
	 * 动态列表
	 * Create by 2012-9-24
	 * @author liuw
	 * @param int $event_id
	 * @param int $is_index 是否是活动首页的请求,如果是则返回一个数据，不是则调用模板显示数据列表
	 */
	function feeds($event_id, $uid=0, $is_index=1, $page=1){
	//	$event_id = $event['id'];
		(empty($event_id) || !$event_id) && die($this->lang->line('event_id_empty'));
		$this->assign(compact('event_id', 'uid'));
		//确定分页参数
		$size = $is_index ? 1 : 20;
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		$tmp = $cfg['template'].'_feed';
		$event = $this->db->where('id', $event_id)->get('Event')->first_row('array');
		
		//sql
		//-------Edit 2012-11-08 增加判断品牌的逻辑
		$arr = array($event['createDate']);
		if($cfg['must_apply'] && (!isset($cfg['follow_brand']) || empty($cfg['follow_brand']))){
			$arr[] = $event_id;
			$c_sql = 'SELECT COUNT(*) AS size FROM Post p LEFT JOIN EventUser eu ON eu.uid=p.uid WHERE p.createDate > ? AND eu.eventId=? AND p.status <= 1';
			$sql = 'SELECT p.*, u.avatar, u.username,u.nickname, pl.placename FROM Post p LEFT JOIN EventUser eu ON eu.uid=p.uid LEFT JOIN User u ON u.id=p.uid LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.createDate > ? AND eu.eventId=? AND p.status <= 1';
		}else{
			$c_sql = 'SELECT COUNT(*) AS size FROM Post p LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.createDate > ? AND p.status <= 1';
			$sql = 'SELECT p.*, u.avatar, u.username,u.nickname, pl.placename FROM Post p LEFT JOIN User u ON u.id=p.uid LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.createDate > ? AND p.status <= 1';
		}
		
		if(!empty($cfg['place_id'])){//指定了地点的
			$c_sql .=" AND p.placeId IN (".implode(',', $cfg['place_id']).")";
			$sql .= " AND p.placeId IN (".implode(',', $cfg['place_id']).")";
		}
		if(!empty($cfg['keyword'])){//有关键词
			if(empty($cfg['allow_post'])){
				$c_sql .= " AND (p.type=1 OR (p.type IN (2,3) AND p.content LIKE '%{$cfg['keyword']}%'))";
				$sql .= " AND (p.type=1 OR (p.type IN (2,3) AND p.content LIKE '%{$cfg['keyword']}%'))";
			}elseif(count($cfg['allow_post']) > 1 && in_array(1, $cfg['allow_post'])){
				$types = array();
				foreach($cfg['allow_post'] as $type){
					$type != 1 && $types[] = $type;
				}
				$c_sql .= " AND (p.type=1 OR (p.type IN (".implode(',', $types).") AND p.content LIKE '%{$cfg['keyword']}%'))";
				$sql .= " AND (p.type=1 OR (p.type IN (".implode(',', $types).") AND p.content LIKE '%{$cfg['keyword']}%'))";
			}elseif(!in_array(1, $cfg['allow_post'])){
				$c_sql .= " AND p.type IN (".implode(',', $cfg['allow_post']).") AND p.content LIKE '%{$cfg['keyword']}%'";
				$sql .= " AND p.type IN (".implode(',', $cfg['allow_post']).") AND p.content LIKE '%{$cfg['keyword']}%'";
			}
		}elseif(!empty($cfg['allow_post'])){
			$c_sql .= " AND p.type IN (".implode(",", $cfg['allow_post']).")";
			$sql .= " AND p.type IN (".implode(",", $cfg['allow_post']).")";
		}
		
		if($cfg['follow_brand']){
			$c_sql .= " AND pl.brandId = '{$cfg['follow_brand']}'";
			$sql .= " AND pl.brandId = '{$cfg['follow_brand']}'";
		}
		
		$sql .= ' ORDER BY p.createDate DESC';
		//数据长度
		$query = $this->db->query($c_sql, $arr)->first_row('array');
		$count = empty($query) ? 0 : intval($query['size']);
		$this->assign('feed_count', $count);
		if($count){
			
			if($is_index){//首页数据
				switch($is_index){
					case 1:
						$sql .= ' LIMIT 1';
						$query = $this->db->query($sql, $arr)->first_row('array');
						$query['uname'] = !empty($query['nickname']) ? $query['nickname'] : $query['username'];
				//		$query['uname'] = get_my_desc($uid, array('uid'=>$query['uid'], 'name'=>$query['uname']));
						$query['avatar'] = image_url($query['avatar'], 'head', 'hhdp');
						$query['createDate'] = gmdate('Y年m月d日 H:i', strtotime($query['createDate'])+8*3600);
						$query['type'] == 2 && $query['star'] = intval($query['level']);
						!empty($query['content']) && $query['content'] = cut_string($query['content'], 24, '...');
						if($query['type'] == 3)
							$query['photoName'] = image_url($query['photoName'], 'user', 'mdp');
						break;
					default:
						$sql .= ' LIMIT '.$is_index;
						$query = $this->db->query($sql, $arr)->result_array();
						foreach($query as &$row){
							$row['uname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
					//		$query['uname'] = get_my_desc($uid, array('uid'=>$query['uid'], 'name'=>$query['uname']));
							$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
							$row['createDate'] = gmdate('Y年m月d日 H:i', strtotime($row['createDate'])+8*3600);
							$row['type'] == 2 && $row['star'] = intval($row['level']);
							!empty($row['content']) && $row['content'] = cut_string($row['content'], 24, '...');
							if($row['type'] == 3)
								$row['photoName'] = image_url($row['photoName'], 'user', 'mdp');
							unset($row);
						}
						break;
				}
				return $query;
			}else{//列表
				$parr = paginate('/event/feeds', $count, $page, array('event_id'=>$event_id,'uid'=>$uid, 'is_index'=>0), $size);
				$sql .= ' LIMIT ?, ?';
				$arr[] = $parr['offset'];
				$arr[] = $parr['per_page_num'];
				$list = array();
				$query = $this->db->query($sql, $arr)->result_array();
				foreach($query as $row){
					$row['uname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
				//	$row['uname'] = get_my_desc($uid, array('uid'=>$row['uid'], 'name'=>$row['uname']));
					$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
					if($row['type'] == 3)
						$row['photoName'] = image_url($row['photoName'], 'user', 'mdp');
					$row['createDate'] = gmdate('Y年m月d日 H:i', strtotime($row['createDate'])+8*3600);
					$row['type'] == 2 && $row['star'] = intval($row['level']);
					$list[$row['id']] = $row;
				}
				$this->assign('list', $list);
		//		$this->echo_json($list);
				$this->display($tmp, 'm');
			}
		}else{
			return null;
		}
	}
	
	/**
	 * 最新的5条动态
	 * Create by 2012-9-24
	 * @author liuw
	 * @param int $event_id
	 * @param int $is_index 是否是活动首页的请求,如果是则返回一个数据，不是则调用模板显示数据列表
	 */
	function joines($event_id=0, $uid=0, $is_index=1, $page=1){
		(empty($event_id) || !$event_id) && die($this->lang->line('event_id_empty'));
		$this->assign(compact('event_id', 'uid'));
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		//确定分页参数
		$size = $is_index ? $is_index : 20;
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$tmp = $cfg['template'].'_user';
		
		$event = $this->db->where('id', $event_id)->get('Event')->first_row('array');
		
		//sql
		//-------Edit 2012-11-08 增加判断品牌的逻辑
		$arr = array($event['createDate']);
		if($cfg['must_apply']){
			$arr[] = $event_id;
			$c_sql = 'SELECT COUNT(*) AS size FROM Post p LEFT JOIN EventUser eu ON eu.uid=p.uid WHERE p.createDate > ? AND eu.eventId=? AND p.status <= 1';
			$sql = 'SELECT u.id, u.avatar, u.username,u.nickname, u.description FROM Post p LEFT JOIN EventUser eu ON eu.uid=p.uid LEFT JOIN User u ON u.id=p.uid WHERE p.createDate > ? AND eu.eventId=? AND p.status <= 1';
		}else{
			$c_sql = 'SELECT COUNT(*) AS size FROM Post p LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.createDate > ? AND p.status <= 1';
			$sql = 'SELECT u.id, u.avatar, u.username,u.nickname, u.description FROM Post p LEFT JOIN User u ON u.id=p.uid LEFT JOIN Place pl ON pl.id=p.placeId WHERE p.createDate > ? AND p.status <= 1';
		//	$arr[] = $cfg['follow_brand'];
		}
		
		if(!empty($cfg['place_id'])){//指定了地点的
			$c_sql .=" AND p.placeId IN (".implode(',', $cfg['place_id']).")";
			$sql .= " AND p.placeId IN (".implode(',', $cfg['place_id']).")";
		}
		if(!empty($cfg['keyword'])){//有关键词
				$c_sql .= " AND p.content LIKE '%{$cfg['keyword']}%'";
				$sql .= " AND p.content LIKE '%{$cfg['keyword']}%'";
		}
		if(!empty($cfg['allow_post'])){
			$c_sql .= " AND p.type IN (".implode(",", $cfg['allow_post']).")";
			$sql .= " AND p.type IN (".implode(",", $cfg['allow_post']).")";
		}
		
		if($cfg['follow_brand']){
			$c_sql .= " AND pl.brandId = '{$cfg['follow_brand']}'";
			$sql .= " AND pl.brandId = '{$cfg['follow_brand']}'";
		}
		$c_sql .= ' GROUP BY p.uid';
		$sql .= ' GROUP BY p.uid ORDER BY p.createDate DESC';
		//数据长度
		$query = $this->db->query($c_sql, $arr)->result_array();
		$count = empty($query) ? 0 : count($query);
	//	echo $this->db->last_query().'<br/>';
		$this->assign('count_joines', $count);
		if($count){
			
			if($is_index){//首页数据
				switch($is_index){
					case 1:
						$sql .= ' LIMIT 1';
						$query = $this->db->query($sql, $arr)->first_row('array');
						$query['uname'] = !empty($query['nickname']) ? $query['nickname'] : $query['username'];
				//		$query['uname'] = get_my_desc($uid, array('uid'=>$query['uid'], 'name'=>$query['uname']));
						$query['avatar'] = image_url($query['avatar'], 'head', 'hhdp');
						$query['createDate'] = gmdate('Y年m月d日 H:i', strtotime($query['createDate'])+8*3600);
						$query['type'] == 2 && $query['star'] = intval($query['level']);
						!empty($query['content']) && $query['content'] = cut_string($query['content'], 24, '...');
						if($query['type'] == 3)
							$query['photoName'] = image_url($query['photoName'], 'user', 'mdp');
						break;
					default:
						$sql .= ' LIMIT '.$is_index;
						$query = $this->db->query($sql, $arr)->result_array();
						foreach($query as &$row){
							$row['uname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
					//		$query['uname'] = get_my_desc($uid, array('uid'=>$query['uid'], 'name'=>$query['uname']));
							$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
							$row['createDate'] = gmdate('Y年m月d日 H:i', strtotime($row['createDate'])+8*3600);
							$row['type'] == 2 && $row['star'] = intval($row['level']);
							!empty($row['content']) && $row['content'] = cut_string($row['content'], 24, '...');
							if($row['type'] == 3)
								$row['photoName'] = image_url($row['photoName'], 'user', 'mdp');
							unset($row);
						}
						break;
				}
				return $query;
			}else{//列表
				$parr = paginate('/event/feeds', $count, $page, array('event_id'=>$event_id,'uid'=>$uid, 'is_index'=>0), $size);
				$sql .= ' LIMIT ?, ?';
				$arr[] = $parr['offset'];
				$arr[] = $parr['per_page_num'];
				$list = array();
				$query = $this->db->query($sql, $arr)->result_array();
	//	echo $this->db->last_query().'<br/>';
				foreach($query as $row){
					$row['uname'] = !empty($row['nickname']) ? $row['nickname'] : $row['username'];
				//	$row['uname'] = get_my_desc($uid, array('uid'=>$row['uid'], 'name'=>$row['uname']));
					$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
					$list[$row['id']] = $row;
				}
				$this->assign('list', $list);
		//		$this->echo_json($list);
				$this->display($tmp, 'm');
			}
		}else{
			return null;
		}
	}
	
    /**
     * 记录点击
     */
    function hit($id = 0) {
        $id = intval($id);
        $id <= 0 && die();
        $this->db->where(array('id' => $id))->set('hits', 'hits+1', false)->update('Event');
    }
    
    /**
     * 申请参加活动活动
     * 
     * @return 1：未登录  -1：出错（活动ID或者用户ID出错） 2：已报过名 0：参加成功
     */
    function apply($id = 0, $uid = 0, $lat=0, $lng=0, $callback='?') {
	//	$this->config->load('event_config');
	//	$cfgs = $this->config->item('event_cfg');
		$cfg = $this->configs[$event_id];
		($cfg['must_apply'] && empty($uid)) && $this->echo_json(array('code'=>1, 'msg'=>$this->lang->line('event_user_faild')));
    	$code = 0;
    	
        $id = intval($id);
        $uid = intval($uid);
        $lat = floatval($lat);
        $lng = floatval($lng);
        
        $callback = $this->input->get('callback');
        empty($callback) && $callback='?';
        
        ($id <= 0 || $uid < 0) && $code = -1;
        
        // 判断用户是否登录
        ($uid == 0 && $code == 0) && $code = 1;
        
        if($code == 0){
	        // 获取活动信息
	        $event = $this->db->get_where('Event', array('id' => $id))->row_array();
	        empty($event) && $code = -1;
        }
        if($code == 0){
	        // 加入活动用户列表
	        $user = $this->db->get_where('EventUser', array('uid' => $uid, 'eventId' => $id))->row_array();
	        $user && $code = 2;
        }
        
        if($code == 0){
	        $this->db->insert('EventUser', array('uid' => $uid, 'eventId' => $id, 'latitude'=>$lat, 'longitude'=>$lng));
	        $new_id = $this->db->insert_id();
	        if(empty($new_id) || $new_id <= 0)
	        	$code = -1;
	        else
		        // 活动人数
		        $this->db->where(array('id' => $id))->set('joins', 'joins+1', false)->update('Event');
        }
        
        echo $callback.'('.json_encode(array('code'=>$code)).')';
    }
    
    /**
     * 查询活动的详细数据
     * Create by 2012-9-18
     * @author liuw
     * @param int $id
     * @return json['code','event']
     */
    function get_event($id=0, $callback='?'){
    	$callback = $this->input->get('callback');
    	empty($callback)&&$callback='?';
    	
    	$code = 0;
    	
    	$id = intval($id);
    	$id <= 0 && $code = -1;
    	if($code == 0){
	    	//查询数据
	    	$event = $this->db->where('id', $id)->get('Event')->first_row('array');
    	}
    	
    	echo $callback.'('.json_encode(compact('code', 'event')).')';
    }
    
    /**
     * 检查指定用户是否已报名参加了指定的活动
     * Create by 2012-9-18
     * @author liuw
     * @param int $id
     * @param int $uid
     * @return int 0=未参加，1=已参加
     */
   	function has_joined($id=0, $uid=0, $callback='?'){
   		$code = 0;
   		
   		$id = intval($id);
   		$uid = intval($uid);
   		$callback = $this->input->get('callback');
   		empty($callback)&&$callback='?';
   		
   		($id <=0 || $uid <= 0) && $code = 1;
   		
   		if($code == 0){
	   		$query = $this->db->where(array('eventId'=>$id, 'uid'=>$uid))->get('EventUser')->first_row('array');
	   		if(!empty($query)){
	   			$code = 1;
	   		}
   		}
   		
    	echo $callback.'('.json_encode(compact('code')).')';
   	}
   	
   	/**
   	 * 根据用户发表的POST被赞的总数对用户进行排序
   	 * Create by 2012-12-3
   	 * @author liuweijava
   	 * @param int $event_id
   	 * @param int $return_size,返回长度
   	 */
   	function top_user_praise($event_id, $uid, $size = 10, $ajax = 0){
   		//活动配置
		$cfg = $this->configs[$event_id];
		$tmp = $cfg['template'].'_top';
   		//活动详情
   		$event = $this->db->where('id', $event_id)->get('Event')->first_row('array');
   		//组合SQL
   		$this->db->select('SUM(Post.praiseCount) AS allPraiseCount, Post.uid, User.avatar, User.username, User.nickname, User.description');
   		//关联表
   		$this->db->from('Post');
   		$this->db->join('User', 'User.id=Post.uid', 'left');//用户属性
   		//活动属性
   		if($cfg['must_apply']){//必须报名
   			$this->db->join('EventUser', 'EventUser.uid = Post.uid', 'inner');
   			if(isset($cfg['follow_brand']) && !empty($cfg['follow_brand'])){
   				$this->db->join('UserOwnMemberCard', 'UserOwnMemberCard.uid = EventUser.uid AND UserOwnMemberCard.isBasic=1', 'inner');
   				$this->db->where('UserOwnMemberCard.brandId', $cfg['follow_brand']);
   			}
   		}
   		//限制了地点的
   		if(isset($cfg['place_id']) && !empty($cfg['place_id'])){
   			$this->db->where_in('Post.placeId', $cfg['place_id']);
   		}
   		//查询条件
   		//$this->db->where('Post.createDate >= ', substr($event['createDate'], 0, -9));
   		if(isset($cfg['keyword']) && !empty($cfg['keyword']))
   			$this->db->like('Post.content', $cfg['keyword']);
   		//限制了POST类型
   		if(isset($cfg['allow_post']) && !empty($cfg['allow_post'])){
   			$this->db->where_in('Post.type', $cfg['allow_post']);
   		}
   		
   		$top = $this->db->group_by('Post.uid')->having('allPraiseCount > 0')->order_by('allPraiseCount', 'desc')->limit($size)->get()->result_array();
   		//echo $this->db->last_query();exit;
   		$tops = array();
   		foreach($top as &$row){
   			//头像
   			$row['avatar'] = image_url($row['avatar'], 'head', 'hmdp');
   			$tops[$row['uid']] = $row;
   			unset($row);
   		}
   		if(!$ajax){
   			$this->assign('top', $tops);
   			$this->assign(compact('event_id', 'uid'));
   			$this->display($tmp, 'm');
   		}else{
   			$this->echo_json($top);
   		}
   	}
   	
   	function wenming_list($code,$page=1){
   		if($code = "wenming"){
   			
   			$this->load->model ( 'webevent_model', 'm_webevent' );
   			$this->load->model ( 'post_model', 'm_post' );
   			$id = 110;
			
			// 获取活动内容
			$event = $this->m_webevent->select_by_id ( $id );
			$apply_property = json_decode ( $event ['applyProperty'], true );
			$event ['apply_tags'] = tags_to_array ( $apply_property ['keyword'] );
			$event ['default_tags'] = reset ( $event ['apply_tags'] );
			$where = 'a.`type`<5 ';//and a.`status` <2';
	   		$where_tagIds = array ();
			if (! empty ( $apply_property ['keyword'] ))
			{
				//获取出标签ID
				$tags = '';
				foreach ( $event ['apply_tags'] as $v )
				{
					$tags .= "'$v',";
				}
				$tags = trim ( $tags, ',' );
				$where_tagIds = $apply_property ['tagids'];
			}
   			
   			$table = $this->_tables ['post'];
   			
   			$xcache_count_key = "wenming_110_list_count";
   			
   			$count = xcache_get($xcache_count_key);
   			
   			if(!$count && $count!==0){
	   			$sql = "select count(a.id) as num from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).")";	
				$tpl = $this->db->query( $sql )->row_array(0);	
				$count = $tpl['num'];
				xcache_set($xcache_count_key,$count,3600*24);
   			}
   		
			
   			if($count){
   				$pagesize = 50;
   				$limit['offset'] =  ($page-1)*$pagesize;
				$limit['size'] = $pagesize;
				
				
				$xcache_key = "wenming_110_list_".$page;
				//缓存！
				$list = xcache_get($xcache_key);
				
				if(!$list){
					$sql = "select a.* from `$table` a,`PostOwnTag` b where a.id=b.postId and $where and b.tagId in(".implode(',',$where_tagIds).") ";
					$sql .= "order by length(photo) >0 desc ,id desc "; /* */ 
					$sql .= " limit {$limit['offset']},{$limit['size']}";		
					$feeds = $this->db->query( $sql )->result_array();
					$list = $feeds ? $this->m_post->list_post(array(),'thweb',false,$feeds) : array();
					xcache_set($xcache_key,$list,3600*24);
				}
				//输出表格
				$pagination = $this->list_page($count,$page);
				
				$this->assign(compact('list','count'));
				$this->display('wenming_list','event_new');
			}
   		}
   	}
   	
	private function list_page($count=0,$page)
	{
		$post_page_size = $this->post_page_size ? $this->post_page_size : 50;
		$limit = array ('size' => $post_page_size, 'offset' => 0 );
		if ($count > 0)
		{
			//分页
			$parr = $this->paginate ( '/event/wenming_list/wenming', $count, $page,false, $post_page_size );
			//计算OFFSET
			$offset = 50 * ($page - 1);
			
			//POST列表
			$limit = array ('size' => $post_page_size, 'offset' => $offset );
		}
		return $limit;		
	}
}

/**
 * 保存缓存，这里只有先把这坨留在这里
 * @param str $key
 * @param mixed $value
 * @param int $expire
 */
function set_cache($key, $value, $expire=0) {
	xcache_set($key, $value, $expire);
}

/**
 * 获取
 * @param str $key
 */
function get_cache($key) {
	return xcache_get($key);
}