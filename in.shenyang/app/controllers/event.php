<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');   
/*
 * 活动
 * 
 * Author: piglet chenglin.zhu@gmail.com
 * Created on 2012-9-3
 */

class Event extends MY_Controller {
    var $link_type;
    function __construct() {
        parent::__construct();
        
        $this->link_type = $this->config->item('link_type');
        $this->assign('link_type', $this->link_type);
    }
    
    
    function index() {
        
        $this->display('index');
    }
    
    /**
     * 展示中
     */
    function show() {
        $this->_search_list();
        
        $this->display('show');
    }
    
    /**
     * 停用的
     */
    function hide() {
        $title = $this->post('title');
        $pager = site_url(array('event', 'hide'));
        
        $this->_search_list(1, $title);
        $this->assign('page_url', $pager);
        
        $this->display('hide');
    }
    
    /**
     * 查询活动列表
     * 
     * @param $title 需要搜索的标题 模糊匹配 like
     */
    function _search_list($status = 0, $title = '') {        
        $where_sql = array();
        if($title) {
            $title_txt = daddslashes($title);
            $where_sql[] = "Event.name like '%{$title_txt}%'";
        }
        if($status >= 0) {
            $where_sql[] = "Event.status ='{$status}'";
        }
        $where_sql && $where_sql = implode(' and ', $where_sql);
        
        $select_sql = "Event.*, (SELECT COUNT(DISTINCT uid) FROM EventUser WHERE eventId=Event.id AND status=0) AS jCount, (SELECT COUNT(0) FROM EventPrize WHERE eventId=Event.id AND status=0) AS pCount, (SELECT COUNT(0) FROM EventTag WHERE eventId=Event.id AND status=0) AS tCount, (SELECT COUNT(0) FROM EventLotteryLog WHERE eventId=Event.id AND prizeId > 0) AS wCount";
        
        $total_num = $this->db->where($where_sql)->from('Event')->count_all_results();
        $paginate = $this->paginate($total_num);
        
        $list = $this->db->select($select_sql)->order_by('Event.rankOrder', 'desc')
                         ->limit($paginate['per_page_num'], $paginate['offset'])
                         ->where($where_sql)
                         ->from('Event')
                         ->get()->result_array();
        //获取奖品和TAG总数
     /*   foreach($list as $k=>&$v){
        	//奖品数量
        	$prizeCount = $this->db->where(array('eventId'=>$v['id'], 'status'=>0))->count_all_results('EventPrize');
        	!empty($prizeCount) && $prizeCount && $v['pCount'] = $prizeCount;
        	//TAG数量
        	$tagCount = $this->db->where(array('eventId'=>$v['id'], 'status'=>0))->count_all_results('EventTag');
        	!empty($tagCount) && $tagCount && $v['tCount'] = $tagCount;
        	//获奖人数
        	$winCount = $this->db->where(array('eventId'=>$v['id'], 'prizeId > '=>0))->count_all_results('EventLotteryLog');
        	!empty($winCount) && $winCount && $v['wCount'] = $winCount;
        	unset($v);
        } */
                      
        $this->assign(compact('title', 'list'));
    }
    
    /**
     * 获奖名单
     * Create by 2012-11-8
     * @author liuweijava
     */
    function winning(){
    	$eventId = $this->get('id');
    	$list = array();
    	//查询获奖总数
    	$count = $this->db->where(array('eventId'=>$eventId, 'prizeId > '=>0))->count_all_results('EventLotteryLog');
    	if($count){
    		//分页
    		$parr = $this->paginate($count);
    		//数据
    		$this->db->select('User.id, User.username, User.nickname, EventPrize.prizeId, EventPrize.prizeName, EventLotteryLog.createDate');
    		$this->db->from('EventLotteryLog');
    		$this->db->join('User', 'User.id = EventLotteryLog.uid', 'left');
    		$this->db->join('EventPrize', 'EventPrize.prizeId = EventLotteryLog.prizeId', 'left');
    		$list = $this->db->where(array('EventLotteryLog.eventId'=>$eventId, 'EventLotteryLog.prizeId > '=>0))->order_by('EventLotteryLog.createDate', 'desc')->limit($parr['per_page_num'], $parr['offset'])->get()->result_array();
    	}
    	$is_dialog = 1;
    //	$page_rel = build_rel(array('event', 'winning'));
    	$this->assign(compact('eventId', 'list', 'is_dialog'));
    	$this->display('winning');
    }
    
    /**
     * 添加
     */
    function add() {
        $id = $this->get('id');
        // 获取活动
        $event = $this->_check_event($id, false);
        
        if($this->is_post()) {
            $data = array();
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
                    $data['url'] = $item_id;
                } else {
                    $data['url'] = $pre_url . $item_id;
                }
            }
            $data['status'] = intval($this->post('status')); // 状态
            $data['name'] = $this->post('title');
            $data['rankOrder'] = intval($this->post('rank_order'));
            $image = $this->post('image');
            foreach($image as $img) {
                if(empty($img)) {
                    $this->error('请上传活动需要的三种尺寸图片');
                }
                $data['image'] = $img;
            }
            $tip = '添加';
            if(empty($id)) {
                // 新建
                $b = $this->db->insert('Event', $data);
                $id = $this->db->insert_id();
            } else {
                // 修改
                $b = $this->db->where(array('id'=>$id))->update('Event', $data);
                $tip = '修改';
            }
            $arr = array('event', 'index');
            $b?$this->success($tip . '活动成功', build_rel($arr), site_url($arr), 'closeCurrent'):$this->error($tip . '活动失败');
        }
        
        if($event) {
            if($event['url']) {
                $pos = strpos($event['url'], ':');
                $item_type = substr($event['url'], 0, $pos);
                foreach($this->link_type as $k=>$v) {
                    if($v['key'] == $item_type) {
                        $this->assign('item_type', $k);
                        $value = substr($event['url'], $pos+3);
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
            $this->assign('event', $event);
        }
        
        $this->display('add');
    }

    /**
     * 修改活动
     */
    function edit() {
        $this->add();
    }
    
    /**
     * 修改排序值
     */
    function modify_rank() {
        $id = $this->get('id');
        
        $event = $this->_check_event($id);
        
        if($this->is_post()) {
            // 提交修改排序值
            $b = $this->db->where(array('id'=>$id))->update('Event', array('rankOrder'=>intval($this->post('rank_order'))));
        
            $b?$this->success('修改排序值成功', '', '', 'closeCurrent', array('action'=>'reload', 'id'=>'event_show_tab', 'url'=>site_url(array('event', 'show')))):$this->error('修改排序值出错啦，亲');
        }
        
        $this->assign(array('title'=>'修改活动：'.$event['name'], 'rank_order'=>$event['rankOrder']));
        
        $this->display('modify_rank', 'main');
    }
    
    /**
     * 禁用
     */
    function banned() {
        $b = $this->_change_status(1);
        
        $arr = array('event', 'index');
        $b?$this->success('禁用活动成功', build_rel($arr), site_url($arr)):$this->error('禁用活动失败');
    }
    
    /**
     * 启用
     */
    function unban() {
        $b = $this->_change_status(0);
        
        $arr = array('event', 'index');
        $b?$this->success('启用活动成功', build_rel($arr), site_url($arr)):$this->error('启用活动失败');
    }
    
    /**
     * 删除
     */
    function delete() {
        $id = $this->get('id');
        
        $event = $this->_check_event($id);
        
        $b = $this->db->delete('Event', array('id'=>$id));
        
        $b?$this->success('删除活动成功', '', '', '', array('action'=>'reload', 'id'=>'event_hide_tab', 'url'=>site_url(array('event', 'hide')))):$this->error('删除活动失败');
    }
    
    /**
     * 改变状态
     */
    function _change_status($status) {
        if(!in_array($status, array(0, 1))) {
            $this->error('错误的状态');
        }
        
        $id = $this->get('id');
        
        $event = $this->_check_event($id);
        
        if($status != $event['status']) {
            // 改变的状态和之前状态不一样才去更新
            return $this->db->where(array('id'=>$id))->update('Event', array('status' => $status));
        }
        
        return true;
    }
    
    /**
     * 检查活动
     */
    function _check_event($id, $is_check = true) {        
        $event = $this->db->get_where('Event', array('id'=>$id))->row_array();
        
        if($is_check) {
            if(empty($event)) {
                $this->error('错误的活动');
            }
        }
        
        return $event;
    }
    
    /**
     * 配置活动参数
     * Create by 2012-11-7
     * @author liuweijava
     */
    function setting(){    	
    	$eventId = $this->get('id');
    	//获得网站的活动配置
//     	$web_cfg = pathinfo(FCPATH, PATHINFO_DIRNAME) . '/' . $this->config->item('web_event_config');
    	$web_cfg = $this->config->item('web_event_config');
    	$configs = file_exists($web_cfg) ? require($web_cfg) : array();
    	$conf = array();
    	!empty($configs) && isset($configs[$eventId]) && !empty($configs[$eventId]) && $conf = $configs[$eventId];
    	
    	if($this->is_post()){
    		$items = $this->post('item');
    		$conf = array();
    		//格式化配置
    		if($items) {
	    		foreach($items as $k=>$v){
	    			$value = $this->post($v);
	    			empty($value) && $value = false;
	    			if($value !== false){
	    				if(is_array($value)){
	    					switch($v){
	    						case 'prize_freq':$value = implode('|', $value);break;
	    						default: break;
	    					}
	    				}else if($v === 'place_id')
	    					$value = explode(',', $value);
	    				$conf[$v] = $value;
	    			}elseif($v === 'template')
	    				$conf[$v] = $eventId;
	    			elseif($v !== 'keyword')
	    				$conf[$v] = 1;
	    		}
    		}
    		//检查是否有prize_role
    		$role_prize_ids = $this->post('role_pid');
    		$role_prize_maxs = $this->post('role_max');
    		if(!empty($role_prize_ids)){
    			$role = array();
    			for($i=0;$i<count($role_prize_ids);$i++){
    				$pid = $role_prize_ids[$i];
    				$max = $role_prize_maxs[$i];
    				!empty($max) && !empty($pid) && $role[$pid] = array('max'=>intval($max));
    			}
    			$conf['prize_role'] = $role;
    		}
    		//更新配置
    		$configs[$eventId] = $conf;
    		@file_put_contents($web_cfg, "<?php\t\nif ( ! defined('BASEPATH')) exit('No direct script access allowed'); \t\n return ".var_export($configs, true).";");
    		@chmod($web_cfg, 0777);
	    	//更新奖品池缓存
	    	@file_get_contents($this->config->item('web_site').'/event/update_cache/'.$eventId);
    		$this->success('活动设置完毕', $this->_index_rel, $this->_index_uri, 'closeCurrent');
    	}else{  
    		if(!empty($conf)){
    			!empty($conf['prize_freq']) && $conf['prize_freq'] = explode('|', $conf['prize_freq']);
    			!empty($conf['place_id']) && $conf['place_id'] = implode(',', $conf['place_id']);
    			$conf['template'] = str_replace('_', '', $conf['template']);
    		}
    		$this->assign('conf', $conf);    	
    		$this->display('setting');
    	}
    }
    
    /**
     * 配置活动奖品
     * Create by 2012-11-7
     * @author liuweijava
     */
    function set_prize(){
    	$eventId = $this->get('id');
    	$this->assign('id', $eventId);
    	//奖品列表
    	$list = $this->db->where('eventId', $eventId)->order_by('prizeId', 'desc')->get('EventPrize')->result_array();
    	empty($list) && $list = array();
    	$this->assign('list', $list);
    	$this->display('prize');
    }
    
    /**
     * 配置活动的用户聚合TAG
     * Create by 2012-11-7
     * @author liuweijava
     */
    function set_event_tag(){
    	$eventId = $this->get('id');
    	//查询TAG
    	$list = array();
    	//数据
    	$list = $this->db->where('eventId', $eventId)->order_by('tagId', 'desc')->get('EventTag')->result_array();
    	$this->assign('list', $list);
    	$this->assign('id', $eventId);
    	$this->display('tag');
    }
    
    function del_tag(){
    	$eventId = $this->get('id');
    	if($this->is_post()){
    		$ids = $this->post('tids');
    		if(!empty($ids)){
    			//删除TAG
    			$this->db->where('eventId', $eventId)->where_in('tagId', $ids)->delete('EventTag');
    			//删除关系
    			$this->db->where('eventId', $eventId)->where_in('tagId', $ids)->delete('EventUserOwnTag');
    		}
    		$this->success($this->lang->line('do_success'), build_rel(array('event', 'set_event_tag')), site_url(array('event', 'set_event_tag', 'id', $eventId)), 'forward');
    	}
    }
    
    function stat_tag(){
    	if($this->is_post()){
    		$tagId = $this->post('tids');
    		$tags = $this->db->where_in('tagId', $tagId)->get('EventTag')->result_array();
    		$datas = array();
    		foreach($tags as $t){
    			$data = array('tagId'=>$t['tagId']);
	    		if($t['status'])//启用
	    			$data['status'] = 0;
	    		else//停用
	    			$data['status'] = 1;
	    		$datas[] = $data;
    		}
    		$this->db->update_batch('EventTag', $datas, 'tagId');
    		$this->success($this->lang->line('do_success'), build_rel(array('event', 'set_event_tag')), site_url(array('event', 'set_event_tag', 'id', $this->get('id'))), 'forward');
    	}
    }
    
    function add_tag(){
    	$eventId = $this->get('id');
    	if($this->is_post()){
    		$this->_make_tag();
    	}else{
    		$this->assign('tag', array());
    		$this->assign('eventId', $eventId);
    		$this->display('make_tag');
    	}
    }
    
    function edit_tag(){
    	$tagId = $this->get('tid');
    	$tag = $this->db->where('tagId', $tagId)->get('EventTag')->first_row('array');
    	if($this->is_post()){    		
    		$this->_make_tag();
    	}else{
    		$this->assign('tag', $tag);
    		$this->assign('eventId', $tag['eventId']);
    		$this->display('make_tag');
    	}
    }
    
    function _make_tag(){
    	$eventId = $this->post('eventId');
    	$tagName = $this->post('tagName');
    	$tagId = $this->post('tagId');
    	empty($tagId) && $tagId = false;
    	if(!$tagId){//添加TAG
    		//检查标签名是否重复
    		$count = $this->db->where('tagName', $tagName)->count_all_results('EventTag');
    		$count && $this->error('标签名称重复了！');
    		$this->db->insert('EventTag', array('eventId'=>$eventId, 'tagName'=>$tagName));
    		$tagId = $this->db->insert_id();
    		!$tagId && $this->error($this->lang->line('do_error'));
    		$tagId && $this->success($this->lang->line('do_success'), build_rel(array('event', 'set_event_tag')), site_url(array('event', 'set_event_tag', 'id', $eventId)), 'closeCurrent');
    	}else{//修改TAG
    		$this->db->where('tagId', $tagId)->set('tagName', $tagName)->update('EventTag');  		
    		$tagId && $this->success($this->lang->line('do_success'), build_rel(array('event', 'set_event_tag')), site_url(array('event', 'set_event_tag', 'id', $eventId)), 'closeCurrent');
    	}
    }
    
    function add_prize(){
    	if($this->is_post()){
    		$this->_make_prize();
    	}else{
	    	$eventId = $this->get('id');
	    	$prize = array();
    		$this->assign(compact('eventId', 'prize'));
    		$this->display('make_prize');
    	}
    }
    
    function edit_prize(){
    	if($this->is_post()){
    		$this->_make_prize();
    	}else{
//     		$web_cfg = pathinfo(FCPATH, PATHINFO_DIRNAME) . '/' . $this->config->item('web_event_config');
    		$web_cfg = $this->config->item('web_event_config');
    		$configs = @require($web_cfg);
	    	$eventId = $this->get('id');
	    	$prizeId = $this->get('pid');
	    	$prize = $this->db->where('prizeId', $prizeId)->get('EventPrize')->first_row('array');	    	
	    	
    		$conf = $configs[$eventId];
    		$roles = !empty($conf) && isset($conf['prize_role']) ? $conf['prize_role'] : false;
    		if($roles){
    			$pr = $roles[$prizeId];
    			!empty($pr) && !empty($pr['max']) && $this->assign('max', $pr['max']);
    		}
	    	
	    	$this->assign(compact('eventId', 'prize'));
	    	$this->display('make_prize');
    	}
    }
    
    function del_prize(){
    	if($this->is_post()){
    		$pids = $this->post('pids');
    		//删除用户获得的奖品
    		$this->db->where_in('prizeId', $pids)->set('prizeId', '0')->update('EventLotteryLog');
    		//删除奖品
    		$this->db->where_in('prizeId', $pids)->delete('EventPrize');
    		$this->success($this->lang->line('do_success'), build_rel(array('event', 'set_prize')), site_url(array('event', 'set_prize', 'id', $this->get('id'))), 'forward');
    	}
    }
    
    function stat_prize(){
    	if($this->is_post()){
    		$pids = $this->post('pids');
    		$rs = $this->db->where_in('prizeId', $pids)->get('EventPrize')->result_array();
    		$datas = array();
    		$eventId = 0;
    		foreach($rs as $row){
    			$data = array('prizeId'=>$row['prizeId']);
    			$eventId = $row['eventId'];
    			if($row['status'])
    				$data['status'] = 0;
    			else 
    				$data['status'] = 1;
    			$datas[] = $data;
    		}
    		$this->db->update_batch('EventPrize', $datas, 'prizeId');
	    	//更新奖品池缓存
	  //  	@file_get_contents($this->config->item('web_site').'/event/update_cache/'.$eventId);
    		
    		$this->success($this->lang->line('do_success'), build_rel(array('event', 'set_prize')), site_url(array('event', 'set_prize', 'id', $this->get('id'))), 'forward');
    	}
    }
    
    function _make_prize(){
    	$eventId = $this->post('eventId');
    	$prizeId = $this->post('prizeId');
    	$prizeName = $this->post('prizeName');
    	$prizeCount = $this->post('prizeCount');
//     	$web_cfg = pathinfo(FCPATH, PATHINFO_DIRNAME) . '/' . $this->config->item('web_event_config');
    	$web_cfg = $this->config->item('web_event_config');
    	//检查奖品名是否重复
    	$c = $this->db->where('prizeName', $prizeName)->count_all_results('EventPrize');
    	$flag = true;
    	$c && $flag = false;
    	if($prizeId){//编辑奖品
    		$edit = array('prizeName'=>$prizeName, 'prizeCount'=>$prizeCount);
    		$this->db->where('prizeId', $prizeId)->update('EventPrize', $edit);
    	}else{//添加奖品
    		!$flag && $this->error('奖品名称重复了');
    		$data = compact('prizeName', 'prizeCount', 'eventId');
    		$this->db->insert('EventPrize', $data);
    		$prizeId = $this->db->insert_id();
    		!$prizeId && $this->error($this->lang->line('do_error'));
    	}
    	
    	if($prizeId){
    		$configs = @require($web_cfg);
    		$conf = $configs[$eventId];
    		$roles = $conf['prize_role'];
    		(!isset($roles) || empty($roles)) && $roles = array();
    		$max = $this->post('max');
    		if(!empty($max)){//更新奖品抽奖规则
    		//	empty($roles) && $roles = array();
    			$roles[$prizeId] = array('max'=>intval($max));
    			$conf['prize_role'] = $roles;
    		}elseif(!empty($roles)){
    			unset($roles[$prizeId]);
    			if(empty($roles))
    				unset($conf['prize_role']);
    			else 
    				$conf['prize_role'] = $roles;
    		}
    		$configs[$eventId] = $conf;
    		//更新配置文件
    		@file_put_contents($web_cfg, "<?php\t\nif ( ! defined('BASEPATH')) exit('No direct script access allowed'); \t\n return ".var_export($configs, true).";");
    		@chmod($web_cfg, 0777);
    	}
    	//更新奖品池缓存
    	@file_get_contents($this->config->item('web_site').'/event/update_cache/'.$eventId);
    //	$this->error($html);
    	
    	$this->success($this->lang->line('do_success'), build_rel(array('event', 'set_prize')), site_url(array('event', 'set_prize', 'id', $eventId)), 'closeCurrent');
    }
    
    /**
     * 报名列表
     * Create by 2012-11-30
     * @author liuweijava
     */
    function joins(){
    	$eventId = $this->get('id');
    	empty($eventId) && $this->error('请选择活动');
    	$list = array();
  
    	//查询总的报名人数
    	$q = $this->db->query('SELECT COUNT(DISTINCT uid) AS num FROM EventUser WHERE eventId=? AND status=0', array($eventId))->first_row('array');
    	$count = !empty($q) ? intval($q['num']) : 0;
    	if($count){
    		//分页
    		$parr = $this->paginate($count);
    		//列表
    		$sql = 'SELECT DISTINCT eu.uid, eu.createDate, u.username, u.nickname, u.avatar FROM EventUser eu LEFT JOIN User u ON u.id=eu.uid WHERE eu.eventId=? AND eu.status=0 ORDER BY eu.createDate DESC LIMIT ?, ?';
    		$list = $this->db->query($sql, array($eventId, $parr['offset'], intval($parr['per_page_num'])))->result_array();
    		foreach($list as &$row){
    			$row['showName'] = !empty($row['nickname']) ? $row['nickname'] : (!empty($row['username']) ? $row['username'] : '第三方平台用户');
    			$row['avatar'] = image_url($row['avatar'], 'head', 'hhdp');
    			unset($row);
    		}
    	}
    	$this->assign('list', $list);
    	$this->assign('is_dialog', 1);
    	$this->display('joins');
    }
}
