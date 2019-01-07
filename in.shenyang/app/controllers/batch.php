<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/**
 * 批量操作的
 */

class Batch extends MY_Controller {
	var $sqlite;
	var $link_type;
	
	var $title = array(
			'present_item' => '派发道具',
			'system_message' => '发送系统消息'
			);
	
	function __construct() {
		parent::__construct();
		$this->sqlite = new PDO(sprintf('sqlite:%s', $this->config->item('sqlite_file')));
		
		$this->link_type = $this->config->item('link_type');
		$this->assign('link_type', $this->link_type);
	}
	
	/**
	 * 批量发道具
	 */
	function present() {
		if($this->is_post()) {
			// 提交派送
			$id = intval($this->post('item'));
			$user_file = $_FILES['user_file'];
			$message = $this->post('message');
			 
			if($id <= 0) {
				$this->error('提交数据错误，请至少选择用户和道具');
			}
			 
			if(cstrlen($message) > 255) {
				$this->error('附言最多255个字哦。亲');
			}
	
			$arr = file($user_file['tmp_name']);
			$ids = array();
			if (count($arr) > 0) {
				foreach($arr as $val) {
					$val = intval(trim($val));
					if($val > 0 && !in_array($val, $ids)) {
						$ids[] = $val;
					}
				}
			}
			
			$l = count($ids);
			if($l == 0 || $l > 1000) {
				$this->error('提交的用户ID大于0个小于1000个');
			}
// 			echo sprintf("INSERT INTO task(title, create_date, num) VALUES('%s', '%s', %d)", 
// 					$this->title['present_item'], now(), $l);
			// 写入任务列表
			$n = $this->sqlite->exec(sprintf("INSERT INTO task(title, create_date, num) VALUES('%s', '%s', %d)", 
					$this->title['present_item'], now(), $l));
			
			if ($n > 0) {
				$task_id = $this->sqlite->lastInsertId();
				foreach ($ids as $value) {
					$action = array(
							'method' => 'POST',
							'url' => '/props/present',
							'params' => array('id' => $id, 'uid' => $value, 'message' => $message),
							'header' => array('uid' => INSY)
							);
					// 写入需要发道具的队列中
					$this->sqlite->exec(sprintf("INSERT INTO queue(action, task_id, create_date) VALUES('%s', '%s', '%s')",
							json_encode($action), $task_id, now()));
				}
			} else {
				$this->error('提交失败，请重试');
			}
			$this->success('提交批量任务成功', 'batch_present', '', 'closeCurrent');
		}
		 
		// 获取道具列表
		$items = $this->db2->select('id, name')->get($this->_tables['item'])->result_array();
		$this->assign('items', $items);
		 
		$this->display('present');
	}
	
	/**
	 * 批量发送系统消息
	 */
	function add_system_message() {
		if($this->is_post()) {
			// 提交全局消息
			$content = trim($this->post('content'));
			if(strlen(content) <= 0) {
				$this->error('请输入发送内容');
			}
	
			if(strlen($content) > 420) {
				$this->error('内容不能超过420个字符，中文不超过140个字');
			}
			
			$user_file = $_FILES['user_file'];
			$arr = file($user_file['tmp_name']);
			$ids = array();
			if (count($arr) > 0) {
				foreach($arr as $val) {
					$val = intval(trim($val));
					if($val > 0 && !in_array($val, $ids)) {
						$ids[] = $val;
					}
				}
			}
			
			$l = count($ids);
			if($l == 0 || $l > 1000) {
				$this->error('提交的用户ID大于0个小于1000个');
			}
			
			// 写入任务列表
			$n = $this->sqlite->exec(sprintf("INSERT INTO task(title, create_date, num) VALUES('%s', '%s', %d)",
					$this->title['system_message'], now(), $l));
			
			if ($n > 0) {
				$task_id = $this->sqlite->lastInsertId();
				foreach ($ids as $value) {
					$data = array();
					$data['content'] = $content;
					$data['type'] = $this->post('item_type');
					$data['recieverId'] = $value;
					// 发送类型1为机甲手动发送，默认为0
					$data['sendType'] = 1;
					if(in_array($data['type'], array(1, 4))) {
						$data['itemId'] = $this->post('content_id');
					} else {
						$data['itemId'] = $this->post('item_id');
					}
					$protocol = $this->link_type[$data['type']]['key'];
					if($protocol) {
						$pre_url = $protocol . '://';
						if(strpos($data['itemId'], $pre_url) === 0) {
							// 如果开始部分为协议部分
							$data['relatedHyperLink'] = $data['itemId'];
						} else {
							$data['relatedHyperLink'] = $pre_url . $data['itemId'];
						}
					} else {
						$data['relatedHyperLink'] = $data['itemId'];
					}
					
					$b = $this->db->insert('SystemMessage', $data);
					$sys_msg_id = $this->db->insert_id();
					
					$action = array(
							'method' => 'POST',
							'url' => '/private_api/push/push_system_message',
							'params' => array('sys_msg_id' => $sys_msg_id),
							'type' => 'private'
							);
					// 写入需要发道具的队列中
					$this->sqlite->exec(sprintf("INSERT INTO queue(action, task_id, create_date) VALUES('%s', '%s', '%s')",
							json_encode($action), $task_id, now()));
				}
			} else {
				$this->error('提交失败，请重试');
			}
			
			$this->success('提交批量任务成功', 'batch_add_system_message', '', 'closeCurrent');
		}
	
		$this->display('add_system_message');
	}
}