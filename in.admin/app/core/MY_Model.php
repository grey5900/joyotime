<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * 模型定义的基类
 * @author chenglin.zhu@gmail.com
 * @date 2012-11-12
 */

/*
 * ----------------------------------------------------
 * 命名的一些规则
 * ----------------------------------------------------
 * 添加 insert_
 * 批量添加 binsert_
 * 删除 delete_
 * 查询 select_
 * 修改 update_
 * 替换 replace_
 * 批量替换 breplace_
 * 查询列表 list_
 * 统计 count_
 * 
 * where条件
 * by = 
 * like 
 * in 
 * all
 * 
 * 字段名
 * 
 * 逻辑尽量写在controller中
 * 
 * 在controller中引用的时候，用
 * $this->load->model('name_model', 'm_name');
 */

class MY_Model extends CI_Model {
	protected $_tables;
	
	function __construct() {
		parent::__construct();

		$this->_tables = $this->config->item('tables');
		$this->db2 = $this->load->database('db2', true);
		
		$class_name = get_class($this);
		$table_name = substr($class_name, 0, -6);
		$this->table = $this->_tables[strtolower($table_name)];
	}
	
	/**
	 * 切换数据库
	 */
	function change_db() {
	    $tmp = $this->db;
	    $this->db = $this->db2;
	    $this->db2 = $tmp;
	    unset($tmp);
	}
	
	/**
	 * 如果调用函数
	 * @param string $method 函数名 
	 * @param mixed $args 参数
	 * 例：
	 * 1.select_by_id
	 * 		where id = ?
	 * 2.select_in_id
	 * 		where id in (?)
	 */
	function __call($method, $args) {
		if(method_exists($this, $method)) {
			// 如果方法存在，那么直接访问
			$rtn = call_user_func_array(array($this, $method), $args);
		} else {
// 			// 不存在那么根据方法名称构造需要的数据库操作
// 			$class_name = strtolower(get_class($this));
// 			// 得到表名
// 			$classes = explode('_', $class_name);
// 			$table = $this->_tables[$classes[0]];
            $table = $this->table;
			// 获取操作
			$method_part = explode('_', $method);
			
			$need_where = in_array($method_part[0], array('delete', 'select', 'update', 'list', 'count'));
			
			if($need_where) {
			    // 先判断是否有order排序条件
			    if('order' == $method_part[3]) {
			        // 需要排序
			        $ofield = $method_part[4]?$method_part[4]:'';
			        $order = strtolower($method_part[5]?$method_part[5]:'');
			        $order = $order=='random'?'random':($order?'DESC':'ASC');
			        
			        $this->db->order_by($ofield, $order);
			    }
			    
				// 需要条件才去判断条件
				$field = $method_part[2];
				switch($method_part[1]) {
					case 'by':
						$this->db->where(array($field => $args[0]));
						break;
					case 'in':
						$this->db->where_in($field, $args[0]);
						break;
					case 'like':
						$this->db->like($field, $args[0]);
						break;
					case 'order':
					    // 后面直接跟的排序条件
					    if(is_array($args[0])) {
					        // where参数
					        $this->db->where($args[0]);
					    } else {
					        // order参数
        			        $order = strtolower($method_part[3]);
        			        $order = $order=='random'?'random':($order?'DESC':'ASC');
        			        $this->db->order_by($field, $order);
					    }
					    break;
					default:
					    // 情况，直接把参数当做条件
					    is_array($args[0])?$this->db->where($args[0]):$this->db->where($args[0], null, false);
				}
			}
			
			switch($method_part[0]) {
				case 'select':
					$rtn = $this->db->limit(1)->get($table)->row_array();
					break;
				case 'delete':
					$rtn = $this->db->delete($table);
					break;
				case 'update':
					$this->db->set($args[1]);
					$rtn = $this->db->update($table);
					break;
				case 'list':
					// limit(?)  =>  limit ?
					// limit(a, b) => limit b, a      b offset a count
					$args[1]?($args[2]?$this->db->limit($args[1], $args[2]):$this->db->limit($args[1])):'';
					$rtn = $this->db->get($table)->result_array();
					break;
				case 'insert':
					$b = $this->db->insert($table, $args[0]);
					$rtn = $b | $this->db->insert_id();
					break;
				case 'binsert':
					$rtn = $this->db->insert_batch($table, $args[0]);
					break;
				case 'replace':
					$rtn = $this->db->replace($table, $args[0]);
					break;
				case 'breplace':
					$rtn = $this->db->replace($table, $args[0]);
					break;
				case 'count':
					$rtn = $this->db->count_all_results($table);
					break;
			}			
		}

		return $rtn;
	}
}