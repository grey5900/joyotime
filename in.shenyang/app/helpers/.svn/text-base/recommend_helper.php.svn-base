<?php
/**
 * 网站新推荐通用函数
 * Create by 2012-11-22
 * @author liuweijava
 * @copyright Copyright(c) 2012-
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code

/**
 * 获取频道列表，如果$fid大于0则查询指定碎片关联的所有频道
 * Create by 2012-11-23
 * @author liuweijava
 * @param int $fid
 */
function get_categorys($fid=0, $status = 1 ,$parentId = 1){
	global $CI;
	
	$role_keys = $CI->auth['role'];
	$roles = get_data("role");
	$newsRights = array();
	foreach($role_keys as $k=>$row){
		$newsRights = array_merge($newsRights,$roles[$row]['newsRights']);
	}
	$newsRights = array_unique(array_filter($newsRights));
	empty($newsRights) && ($newsRights = array(0));
	
	//$where_sql = array('parentId'=>0);
    //$status >= 0 && ($where_sql['status'] = $status);
    //if ($CI->auth['role'][0] != 1){ $where_sql['status'] = $status; }
    $where_sql = $parentId ? " 1=1 " : " parentId = 0 ";// parentId = 0 ";
    $status >= 0 && ($where_sql .= " and status=".$status);
    ( $CI->auth['role'][0] != 1 ) && $where_sql .= " and id in (".implode(",",$newsRights).")";
	$CI->db->select('id, catName')->where($where_sql,null,false);
	if($fid)
		$CI->db->where('FIND_IN_SET(\''.$fid.'\', fragmentId)');
	$query = $CI->db->order_by('dateline', 'desc')->get('WebNewsCategory')->result_array();
	foreach($query as &$row){
		$row['dateline'] = gmdate('Y-m-d H:i', $row['dateline']);
		//已分配的碎片
		$fs = array();
		if(!empty($row['fragmentId'])){
			$q = $this->db->where_in('fid', $row['fragmentId'])->get('WebRecommendFragment')->result_array();
			foreach($q as $r){
				$fs[] = $r['name'];
			}
			$row['frag'] = implode(',', $fs);
		}
	}
	unset($row);
	return $query;
}

/**
 * 碎片模板文件列表
 * Create by 2012-11-23
 * @author liuweijava
 */
function frag_tmp_list(){
	global $CI;
	//模板目录的绝对路径
	$CI->config->load('fragtemp_config');
	$list = $CI->config->item('frag_tmp');
	//获得文件名列表
/*	$list = array();
	$handler = opendir($folder);
	while(($f = readdir($handler)) !== false){
		if($f !== '.' && $f !== '..' && strpos(strtolower($f), '.svn') === false)
			$list[] = $f;
	} */
	return $list;
}

function flush_rec_data($fid){
	global $CI;
	
	$CI->load->model('fragment', 'frag');
	return $CI->frag->flush_data($fid);
}

 // File end