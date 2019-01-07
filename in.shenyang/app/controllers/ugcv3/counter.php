<?php
/**
 * 回复管理
 * Create by 2012-3-22
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Counter extends MY_Controller{
	
	function __construct(){
		parent::__construct();
		
	}
	
	public function index_old(){
		$startDate = $this->post('startDate');
		$endDate = $this->post('endDate');
		if($this->is_post()){
			$type = $this->post('type');
			//yy 点评2  回复 分享 地点册 图片    1:签到,2:点评,3:照片,4:YY,5:评价POST,6购买商品,7:分享,8:成为会员
            //9:关注好友
			//100 精华 300精华 其他分值的精华 总分值
			//参与发布人数
			//时间段内总参与发布人数
			//var_dump($_POST);
			if(!empty($startDate) && !empty($endDate)){
				$start = strtotime($startDate);
				$end = strtotime($endDate);
				//时间跨度内有多少天。。max 31天
				
				$timetable = array();
				for($i = $start ;$i <= $end ; $i=$i+3600*24){
					if($i> $start + 50*3600*24){
						break;
					}
					$day = date('Y-m-d',$i);
					$s = $day.' 00:00:00';
					$e = $day.' 23:59:59';
					
					$tmp_array = array();
					
					//$cancel_count = 0;
					$base_where = "createDate >= '{$s}' and createDate <= '{$e}'";
					if($type!='essence'){
						// post {
						//yy 4
						$this->db->where('type',4)
								->where($base_where,null,false);
						$yy_count = $this->db->count_all_results($this->_tables['post']);
						
						//点评 2
						$this->db->where('type',2)
								->where($base_where,null,false);
						$tip_count = $this->db->count_all_results($this->_tables['post']);
						//图片 
						$this->db->where_in('type',array(2,4))
								->where($base_where,null,false)
								->where("photo is not null ",null,false);
						$photo_count = $this->db->count_all_results($this->_tables['post']);
						
						//分享 7
						$this->db->where('type',7)
								->where($base_where,null,false);
						$share_count = $this->db->count_all_results($this->_tables['post']);
						
						$post_user_count = $this->db->select('count(distinct uid) as uc',null)->where($base_where,null,false)->where_in('type',array('2,4,7'))->get($this->_tables['post'])->row_array();
						$post_all_user_count = $this->db->select('count(distinct uid) as uc',null)->where($base_where,null,false)->get($this->_tables['post'])->row_array();
						//删除或屏蔽条数
						$this->db->where($base_where,null,false)->where('status > 1',null,false);
						$post_cancel_count = $this->db->count_all_results($this->_tables['post']);
						
						// } post
						
						// reply {
						$reply_count = $this->db->where($base_where,null,false)->count_all_results($this->_tables['reply']);
						$reply_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($base_where,null,false)->get($this->_tables['reply'])->row_array();
						$reply_cancel_count = $this->db->where($base_where,null,false)->where('status > 0',null,false)->count_all_results($this->_tables['reply']);
						// } reply
						
						// placecoll {
						$placecoll_count = $this->db->where($base_where,null,false)->count_all_results($this->_tables['placecollection']);
						$placecoll_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($base_where,null,false)->get($this->_tables['placecollection'])->row_array();
						$placecoll_cancel_count = $this->db->where($base_where,null,false)->where('status > 0',null,false)->count_all_results($this->_tables['placecollection']);
						// } placecoll
						
						// privateMessage
						$pm_count = $this->db->where($base_where,null,false)->count_all_results($this->_tables['userprivatemessage']);
						$pm_user_count =  $this->db->select('count(distinct sender) as uc',null)->where($base_where,null,false)->get($this->_tables['userprivatemessage'])->row_array();
						// privateMessage
						
						//总UGC人数
					}else{
						//post精华
						$post_jing100 = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',100)->count_all_results($this->_tables['post']);
						$post_jing300 = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',300)->count_all_results($this->_tables['post']);
						$post_jingother = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore != 100 and essenceScore!=300',null,false)->count_all_results($this->_tables['post']);
						$post_sumjing = $this->db->where($base_where,null,false)->select('sum(essenceScore) as total')->where('isEssence',1)->get($this->_tables['post'])->row_array();
						//地点册精华
						$placecoll_jing100 = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',100)->count_all_results($this->_tables['placecollection']);
						$placecoll_jing300 = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',300)->count_all_results($this->_tables['placecollection']);
						$placecoll_jingother = $this->db->where($base_where,null,false)->where('isEssence',1)->where('essenceScore != 100 and essenceScore!=300',null,false)->count_all_results($this->_tables['placecollection']);
						$placecoll_sumjing = $this->db->where($base_where,null,false)->select('sum(essenceScore) as total')->where('isEssence',1)->get($this->_tables['placecollection'])->row_array();
					}
					$tmp_array = array(
						'yy_count' => $yy_count,
						'tip_count' => $tip_count,
						'photo_count' => $photo_count,
						'share_count' => $share_count,
						'post_user_count' => $post_user_count['uc'],
						'post_all_user_count' => $post_all_user_count['uc'],
						'post_cancel_count' => $post_cancel_count,
						'reply_count' => $reply_count,
						'reply_user_count' => $reply_user_count['uc'],
						'reply_cancel_count' => $reply_cancel_count,
						'placecoll_count' => $placecoll_count,
						'placecoll_user_count' => $placecoll_user_count['uc'],
						'placecoll_cancel_count' => $placecoll_cancel_count,
						'pm_count' => $pm_count,
						'pm_user_count' => $pm_user_count['uc'],					
						'post_jing100' => $post_jing100,
						'post_jing300' => $post_jing300,
						'post_jingother' => $post_jingother,
						'post_sumjing' => $post_sumjing['total']+0,
						'placecoll_jing100' => $placecoll_jing100,
						'placecoll_jing300' => $placecoll_jing300,
						'placecoll_jingother' => $placecoll_jingother,
						'placecoll_sumjing' => $placecoll_sumjing['total']+0,
					);
					
					$timetable[$day] = $tmp_array;
					unset($tmp_array);
				}
				$date_where = "createDate >= '{$startDate}' and createDate <= '{$endDate}'";
				$total_post_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->where_in('type','2,4,7')->get($this->_tables['post'])->row_array();//$this->db->where($date_where,null,false)->
				$total_all_post_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['post'])->row_array();//$this->db->where($date_where,null,false)->
				
				$total_reply_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['reply'])->row_array();
				$total_placecoll_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['placecollection'])->row_array();
				$total_pm_user_count =  $this->db->select('count(distinct sender) as uc',null)->where($date_where,null,false)->get($this->_tables['userprivatemessage'])->row_array();
			}
		}
		$this->assign(compact('startDate','endDate','timetable','total_post_user_count','total_reply_user_count','total_placecoll_user_count','total_all_post_user_count','total_pm_user_count','type'));
		$this->display('counter','ugcv3');
	}
	
	public function index(){
		$startDate = $this->post('startDate');
		$endDate = $this->post('endDate');
		if($this->is_post()){
			$type = $this->post('type');
			//yy 点评2  回复 分享 地点册 图片    1:签到,2:点评,3:照片,4:YY,5:评价POST,6购买商品,7:分享,8:成为会员
            //9:关注好友
			//100 精华 300精华 其他分值的精华 总分值
			//参与发布人数
			//时间段内总参与发布人数
			//var_dump($_POST);
			if(!empty($startDate) && !empty($endDate)){
				$start = strtotime($startDate);
				$end = strtotime($endDate);
				//时间跨度内有多少天。。max 31天
				
				$timetable = array();
				
				//挫，太挫了，不能这样子做，还是做分组吧
				$base_where = "createDate >= '{$startDate}' and createDate <= '{$endDate}'";
				if($type!='essence'){
						$yy = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where('type',4)
								    ->where($base_where,null,false)->group_by('day')->get($this->_tables['post'])->result_array();
						$yy_count = array_value_to_be_key($yy,'day');
						//var_dump($yy_count);
						//点评 2
						$tip = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where('type',2)
								    ->where($base_where,null,false)->group_by('day')->get($this->_tables['post'])->result_array();
						$tip_count = array_value_to_be_key($tip,'day');
						//图片 
						$photo = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where_in('type',array(2,4))
								    ->where($base_where,null,false)->where("photo is not null ",null,false)->group_by('day')->get($this->_tables['post'])->result_array();
						$photo_count = array_value_to_be_key($photo,'day');
						
						//分享 7
						$share = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where('type',7)
								    ->where($base_where,null,false)->group_by('day')->get($this->_tables['post'])->result_array();
						$share_count = array_value_to_be_key($share,'day');
						
						
						$post_user = $this->db->select('count(distinct uid) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
													->where($base_where,null,false)->where_in('type',array('2,4,7'))
													->group_by('day')
													->get($this->_tables['post'])->result_array();
						$post_user_count = array_value_to_be_key($post_user,'day');		

						
						$post_all_user = $this->db->select('count(distinct uid) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
													->where($base_where,null,false)->group_by('day')->get($this->_tables['post'])->result_array();
						$post_all_user_count = array_value_to_be_key($post_all_user,'day');		
													
						//删除或屏蔽条数
						//$this->db->where($base_where,null,false)->where('status > 1',null,false);
						//$post_cancel_count = $this->db->count_all_results($this->_tables['post']);
						$post_cancel = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
													->where($base_where,null,false)->where('status > 1',null,false)
													->group_by('day')->get($this->_tables['post'])->result_array();
						$post_cancel_count = array_value_to_be_key($post_cancel,'day');		
						// } post
						
						// reply {
						$reply = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
											->where($base_where,null,false)->group_by('day')->get($this->_tables['reply'])->result_array();
						$reply_count = array_value_to_be_key($reply,'day');		
						
						$reply_user =  $this->db->select('count(distinct uid) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												  ->where($base_where,null,false)->group_by('day')->get($this->_tables['reply'])->result_array();
						$reply_user_count = array_value_to_be_key($reply_user,'day');	
							
						$reply_cancel = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('status > 0',null,false)
												->group_by('day')
												->get($this->_tables['reply'])->result_array();
						$reply_cancel_count = array_value_to_be_key($reply_cancel,'day');
						// } reply
						
						// placecoll {
						$placecoll = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
											->where($base_where,null,false)->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_count = array_value_to_be_key($placecoll,'day');		
						
						$placecoll_user =  $this->db->select('count(distinct uid) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												  ->where($base_where,null,false)->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_user_count = array_value_to_be_key($placecoll_user,'day');	
							
						$placecoll_cancel = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('status > 0',null,false)
												->group_by('day')
												->get($this->_tables['placecollection'])->result_array();
						$placecoll_cancel_count = array_value_to_be_key($placecoll_cancel,'day');
						
						// } placecoll
						
						// privateMessage
						$pm = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
											->where($base_where,null,false)->group_by('day')->get($this->_tables['userprivatemessage'])->result_array();
						$pm_count = array_value_to_be_key($pm,'day');		
						
						$pm_user =  $this->db->select('count(distinct sender) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												  ->where($base_where,null,false)->group_by('day')->get($this->_tables['userprivatemessage'])->result_array();
						$pm_user_count = array_value_to_be_key($pm_user,'day');	
						// privateMessage
						
				}else{
						//post精华
						$post_jing100 = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												 ->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',100)
												 ->group_by('day')->get($this->_tables['post'])->result_array();
						$post_jing100 = array_value_to_be_key($post_jing100,'day');	
						
						$post_jing300 = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',300)
												->group_by('day')->get($this->_tables['post'])->result_array();
						$post_jing300 = array_value_to_be_key($post_jing300,'day');		
											
						$post_jingother = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('isEssence',1)->where('essenceScore != 100 and essenceScore!=300',null,false)
												->group_by('day')->get($this->_tables['post'])->result_array();
						$post_jingother = array_value_to_be_key($post_jingother,'day');		
						$post_sumjing = $this->db->where($base_where,null,false)->select('sum(essenceScore) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where('isEssence',1)
												->get($this->_tables['post'])->group_by('day')->get($this->_tables['post'])->result_array();
						$post_sumjing = array_value_to_be_key($post_sumjing,'day');		
						//地点册精华
						$placecoll_jing100 = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												 ->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',100)
												 ->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_jing100 = array_value_to_be_key($post_jing100,'day');	
						
						$placecoll_jing300 = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('isEssence',1)->where('essenceScore',300)
												->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_jing300 = array_value_to_be_key($post_jing300,'day');		
											
						$placecoll_jingother = $this->db->select('count(id) as total ,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)
												->where($base_where,null,false)->where('isEssence',1)->where('essenceScore != 100 and essenceScore!=300',null,false)
												->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_jingother = array_value_to_be_key($post_jingother,'day');		
						$placecoll_sumjing = $this->db->where($base_where,null,false)->select('sum(essenceScore) as total,DATE_FORMAT(createDate, \'%Y-%m-%d\') as day',false)->where('isEssence',1)
												->group_by('day')->get($this->_tables['placecollection'])->result_array();
						$placecoll_sumjing = array_value_to_be_key($post_sumjing,'day');	
						
				}
				
				$timetable = array(
						'yy_count' => $yy_count,
						'tip_count' => $tip_count,
						'photo_count' => $photo_count,
						'share_count' => $share_count,
						'post_user_count' => $post_user_count,
						'post_all_user_count' => $post_all_user_count,
						'post_cancel_count' => $post_cancel_count,
						'reply_count' => $reply_count,
						'reply_user_count' => $reply_user_count,
						'reply_cancel_count' => $reply_cancel_count,
						'placecoll_count' => $placecoll_count,
						'placecoll_user_count' => $placecoll_user_count,
						'placecoll_cancel_count' => $placecoll_cancel_count,
						'pm_count' => $pm_count,
						'pm_user_count' => $pm_user_count,					
						'post_jing100' => $post_jing100,
						'post_jing300' => $post_jing300,
						'post_jingother' => $post_jingother,
						'post_sumjing' => $post_sumjing,
						'placecoll_jing100' => $placecoll_jing100,
						'placecoll_jing300' => $placecoll_jing300,
						'placecoll_jingother' => $placecoll_jingother,
						'placecoll_sumjing' => $placecoll_sumjing,
				);
				
				$date_list = array();
				$starttimestamp = strtotime($startDate);
				$endtimestamp = strtotime($endDate);
				
				for($i=$starttimestamp;$i<=$endtimestamp;$i=$i+3600*24){
					$date_list [] = date('Y-m-d',$i);
				}
				
				$date_where = "createDate >= '{$startDate}' and createDate <= '{$endDate}'";
				$total_post_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->where_in('type','2,4,7')->get($this->_tables['post'])->row_array();//$this->db->where($date_where,null,false)->
				$total_all_post_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['post'])->row_array();//$this->db->where($date_where,null,false)->
				
				$total_reply_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['reply'])->row_array();
				$total_placecoll_user_count =  $this->db->select('count(distinct uid) as uc',null)->where($date_where,null,false)->get($this->_tables['placecollection'])->row_array();
				$total_pm_user_count =  $this->db->select('count(distinct sender) as uc',null)->where($date_where,null,false)->get($this->_tables['userprivatemessage'])->row_array();
			}
		}
		$this->assign(compact('startDate','endDate','timetable','total_post_user_count','total_reply_user_count','total_placecoll_user_count','total_all_post_user_count','total_pm_user_count','type','date_list'));
		$this->display('counter','ugcv3');
	}
	
	function export_counter($date){
		$startDate = $date." 00:00:00";
		$endDate = $date." 23:59:59";
			
		$date_where = "createDate >= '{$startDate}' and createDate <= '{$endDate}'";
		$yy = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)->where('type',4)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['post'])->result_array();
		$yy_count = array_value_to_be_key($yy,'hour');
				 
		$tip = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)->where('type',2)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['post'])->result_array();
		$tip_count = array_value_to_be_key($tip,'hour');
		
		$photo = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)->where('photo is not null',null,false)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['post'])->result_array();
		$photo_count = array_value_to_be_key($photo,'hour');
		$share = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)->where('type',7)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['post'])->result_array();
		$share_count = array_value_to_be_key($share,'hour');
		$reply = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['reply'])->result_array();
		$reply_count = array_value_to_be_key($reply,'hour');
		$pm = $this->db->select('count(id) as total , DATE_FORMAT(createDate, \'%H\') as hour',false)
				 ->where($date_where,null,false)
				 ->group_by('hour')->get($this->_tables['userprivatemessage'])->result_array();
		$pm_count = array_value_to_be_key($pm,'hour');
		$filename = sprintf ( '%s UGC 内容时间曲线', $date  );
		header ( 'Content-type: application/vnd.ms-excel; charset=gbk' );
		header ( 'Content-Disposition: attachment; filename="' . $filename . '.xls"' );
			
			
		$str = "小时\tYY\t点评\t回复\t分享\t私信\n";
			
		for($i=0;$i<=23;$i++){
			$hour_key = str_pad($i,2,'0',STR_PAD_LRFT);
			$str .= "{$hour_key}\t".intval($yy_count[$hour_key]['total'])."\t".intval($tip_count[$hour_key]['total'])."\t".intval($reply_count[$hour_key]['total'])."\t".intval($share_count[$hour_key]['total'])."\t".intval($pm_count[$hour_key]['total'])."\n";		
		}
		echo mb_convert_encoding ( $str, 'GBK', 'utf-8' );
        exit;
	}
}   
   
