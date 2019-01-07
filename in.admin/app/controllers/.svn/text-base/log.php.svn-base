<?php
/**
 * 日志管理
 * Create by 2012-3-16
 * @author liuw
 * @copyright Copyright(c) 2012-2014 joyotime
 */
  
 // Define and include
if ( ! defined('BASEPATH')) exit('No direct script access allowed');   
   
 // Code
class Log extends MY_Controller{
	
	/**
	 * 查询操作日志
	 * Create by 2012-3-16
	 * @author liuw
	 */
	public function index(){
		//获得检索和分页参数 
		$page = $this->post('pageNum');
		$page = empty($page) ? 1 : intval($page);
		$size = $this->post('numPerPage');
		$size = empty($size) ? 20 : intval($size);
		$order = $this->post('orderField');
		$order = empty($order) ? 'dateline' : $order;
		$by = $this->post('orderDirection');
		$by = empty($by) ? 'desc' : $by;
		$actDesc = $this->post('actdesc');
		$actDesc = empty($actDesc) ? FALSE : $actDesc;
		$sType = $this->post('type');
		$sType = empty($sType) ? FALSE : $sType;
		$sVal = $this->post('typeval');
		$sVal = $sType && !empty($sVal) ? $sVal : FALSE;
		$begin = $this->post('actbegin');
		$begin = empty($begin) ? FALSE : strtotime($begin.' 00:00:00');
		$end = $this->post('actend');
		$end = empty($end) ? FALSE : strtotime($end.' 23:59:59');
        // 2012.09.06动作查询
        $acturi = $this->post('acturi');
		
		$prop = array(
			'pageNum' => $page,
			'numPerPage' => $size,
			'orderField' => $order,
			'orderDirection' => $by,
			'actdesc' => $actDesc ? $actDesc : '',
			'type' => $sType ? $sType : 'atruename',
			'typeval' => $sVal ? $sVal : '',
			'actbegin' => $begin ? gmdate('Y-m-d', $begin) : '',
			'actend' => $end ? gmdate('Y-m-d', $end) : '',
			'acturi' => $acturi ? $acturi : ''
		);
		
		//组装查询条件
		$this->db->from('MorrisAdminLog');
		$this->db->order_by($order, $by);
        if($acturi)
            $this->db->like('acturi', $acturi);
		if($actDesc)
			$this->db->like('actdesc', $actDesc);
		if($sType && $sVal)
			$this->db->where($sType,$sVal);
		if($begin || $end){
			if($begin && !$end)
				$this->db->where('dateline >= ', $begin, FALSE);
			elseif(!$begin && $end)
				$this->db->where('dateline <= ', $end, FALSE);
			else 
				$this->db->where('dateline BETWEEN '.$begin.' AND '.$end);
		}
		
		//数据总长度
		$count = $this->db->count_all_results();
		if($count){
			$prop['count'] = $count;
		
			$this->db->from('MorrisAdminLog');
			$this->db->order_by($order, $by);
            if($acturi)
                $this->db->like('acturi', $acturi);
			if($actDesc)
				$this->db->like('actdesc', $actDesc);
			if($sType && $sVal)
				$this->db->where($sType,$sVal);
			if($begin || $end){
				if($begin && !$end)
					$this->db->where('dateline >= ', $begin, FALSE);
				elseif(!$begin && $end)
					$this->db->where('dateline <= ', $end, FALSE);
				else 
					$this->db->where('dateline BETWEEN '.$begin.' AND '.$end);
			}
			$start = $size * ($page - 1);
			$this->db->limit($size, $start);
			$list = array();
			$query = $this->db->get();
			foreach($query->result_array() as $row){
				$list[$row['id']] = $row;
			}
			$prop['list'] = $list;
		}
		$this->assign($prop);		
		$this->display('index','log');
	}
	
	/**
	 * 显示高级检索界面
	 * Create by 2012-3-16
	 * @author liuw
	 */
	public function advsearch(){
		$this->assign('do','advsearch');
		$this->display('index','log');
	}
	
} 
   
 // File end