<?php
use Monolog\Handler\Mongo;
/**
 *
 * @abstract 内容合作商管理相关方法
 *           统计相关数据用的是mongodb slave数据库
 *          
 */
App::uses('AppController', 'Controller');
App::uses('SlaveVoice', 'Model');
/**
 * FishSaying Controller
 *
 *
 * @package app.Controller
 */
class PartnersController extends AppController {
	public $name = 'Partners';
	public $layout = 'fishsaying';
	public $components = array(
		'ConnectApi'=>array(
			'className'=>'FishSayingApi.Connect' 
		), 
		'UserApi'=>array(
			'className'=>'FishSayingApi.User' 
		), 
		'RoleApi'=>array(
			'className'=>'FishSayingApi.Role' 
		), 
		'Patch' 
	);
	public $uses = array(
		'Partner', 
		'User', 
		'SlaveUser', 
		'SlaveVoice' 
	);
	public function index($kw = '') {
		$page = $this->request->query('page');
		$limit = $this->request->query('limit');
		
		$conditions = array();
		if ($kw) {
			$conditions['name'] = new MongoRegex("/$kw/i");
		}
		$page = $page ? intval($page) : 1;
		$limit = $limit ? intval($limit) : 20;
		$this->paginate = array(
			'limit'=>$limit, 
			'paramType'=>"querystring" 
		);
		$partners = $this->Partner->find('all', array(
			'conditions'=>$conditions, 
			'order'=>array(
				'modified'=>'desc' 
			), 
			'page'=>$page, 
			'limit'=>$limit 
		));
		
		$total = $this->Partner->find('count', array(
			'conditions'=>$conditions 
		));
		
		$items = Hash::extract($partners, '{n}.Partner');
		
		$this->Partner->results = $items;
		$this->Partner->count = $total;
		$items = $this->paginate('Partner');
		$items = is_array($items) ? $items : array();
		$items = Hash::extract($partners, '{n}.Partner');
		foreach ($items as $key=>$val) {
			if ($val['user_id']) {
				$totalConditions = array(
					'status'=>SlaveVoice::STATUS_APPROVED,
					"user_id"=>array(
						'$in'=>$val['user_id'] 
					) 
				);
				// 解说数
				$voice_total = $this->SlaveUser->getRelationStatistics($val['user_id'], 'voice_total');
				// 总时长
				$voice_length_total = $this->SlaveUser->getRelationStatistics($val['user_id'], 'voice_length_total');
				// 销量
				$checkout_total = $this->SlaveVoice->getRelationStatistics($totalConditions, 'checkout_total');
				// 播放次数
				$play_total = $this->SlaveVoice->getRelationStatistics($totalConditions, 'play_total');
				// 总收入
				$earn_total = $this->SlaveVoice->getRelationStatistics($totalConditions, 'earn_total');
				
				$items[$key]['voice_total'] = $voice_total;
				$items[$key]['voice_length_total'] = $voice_length_total;
				$items[$key]['checkout_total'] = $checkout_total;
				$items[$key]['play_total'] = $play_total;
				$items[$key]['earn_total'] = $earn_total;
			}
		}
		
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('kw', $kw);
	}
	
	/**
	 * 统计 内容合作商所有用户的鱼说
	 */
	public function statistics($id = "") {
		$partner = $this->Partner->getById($id);
		$type = $this->request->query('type'); // list 是列表,export是導出
		$page = $this->request->query('page');
		$limit = $type == 'export' ? 9999 : $this->request->query('limit');
		$startTime = $this->request->query('startTime');
		$endTime = $this->request->query('endTime');
		
		// 总时长
		$length = 0;
		
		$page = $page ? intval($page) : 1;
		$limit = $limit ? intval($limit) : 20;
		$this->paginate = array(
			'limit'=>$limit, 
			'paramType'=>"querystring" 
		);
		$timeCondition = '';
		if ($startTime) {
			$timeCondition['approved'] = array(
				'$gte'=>new MongoDate(strtotime($startTime)) 
			);
		}
		if ($startTime && $endTime) {
			$timeCondition['approved'] = array(
				'$gte'=>new MongoDate(strtotime($startTime)), 
				'$lte'=>new MongoDate(strtotime($endTime . ' 23:59:59')) 
			);
		}
		if ($startTime == '' && $endTime) {
			$timeCondition['approved'] = array(
				'$lte'=>new MongoDate(strtotime($endTime . ' 23:59:59')) 
			);
		}
		$conditions = array(
			'status'=>SlaveVoice::STATUS_APPROVED, 
			"user_id"=>array(
				'$in'=>$partner['user_id'] 
			) 
		);
		// 合并时间查询条件
		if ($timeCondition) {
			$conditions = array_merge($conditions, $timeCondition);
		}
		$voices = $this->SlaveVoice->find('all', array(
			'conditions'=>$conditions, 
			'order'=>array(
				'approved'=>'desc' 
			), 
			'page'=>$page, 
			'limit'=>$limit 
		));
		$items = Hash::extract($voices, '{n}.SlaveVoice');
		//执行导出csv
		if ($type == 'export') {
			return $this->export($items);
		}
		$total = $this->SlaveVoice->find('count', array(
			'conditions'=>$conditions 
		));
		
		$this->SlaveVoice->results = $items;
		$this->SlaveVoice->count = $total;
		$items = $this->paginate('SlaveVoice');
		
		$items = Hash::extract($voices, '{n}.SlaveVoice');
		
		foreach ($items as &$voice) {
			// 格式化图片地址
			$this->Patch->patchPath($voice);
			// 格式化用户相关信息
			$this->Patch->patchUser($voice);
		}
		// 统计时长
		$length = $this->SlaveVoice->getRelationStatistics($conditions, 'length');
		// 总收入
		$earn_total = $this->SlaveVoice->getRelationStatistics($conditions, 'earn_total');
		//dump($items);
		$this->set('earn_total', $earn_total);
		$this->set('length', $length);
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('id', $id);
		$this->set('partner', $partner);
		$this->set('startTime', $startTime);
		$this->set('endTime', $endTime);
	}
	/**
	 * 增加
	 *
	 * @return string
	 */
	public function add() {
		if ($this->request->is('post')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$data = $this->request->data['partner'];
			if ($this->Partner->isNameUnique($data['name'])) {
				$result = false;
				$msg = '合作商名称已存在';
			} else {
				$result = $this->Partner->save($data);
				$msg = $this->errorMsg($this->Partner);
			}
			$response = new \Controller\Response\Response();
			return $result ? $response->message(TRUE, __('保存成功')) : $response->message(FALSE, $msg);
		}
		$this->set('required', true);
		return $this->render('add');
	}
	/**
	 * 增加
	 *
	 * @return string
	 */
	public function edit($id = '') {
		$this->set('token', $this->ConnectApi->token());
		if ($this->request->is('PUT')) {
			$this->autoLayout = false;
			$this->autoRender = false;
			$data = $this->request->data['partner'];
			$org_name = $this->request->data['org_name'];
			$id = $data['_id'];
			unset($data['_id']);
			if ($this->Partner->isNameUnique($data['name']) && $org_name!=$data['name']) {
				$result = false;
				$msg = '合作商名称已存在';
			} else {
				$result = $this->Partner->updateAll($data, array(
					'_id'=>new MongoId($id) 
				));
				$msg = $this->errorMsg($this->Partner);
			}
			$response = new \Controller\Response\Response();
			return $result ? $response->message(TRUE, __('更新成功')) : $response->message(FALSE, $msg);
		}
		$data = $this->Partner->findById($id);
		if (! $data) {
			$this->redirect('/partners/index');
		}
		$this->request->data['partner'] = $data['Partner'];
		$this->set('data', $data);
		$this->set('required', true);
		return $this->render('add');
	}
	/**
	 * 已关联的用户列表
	 *
	 * @param string $id        	
	 */
	public function relationUser($id) {
		$data = $this->Partner->findById($id);
		
		$userIds = $data['Partner']['user_id'];
		$users = array();
		if ($userIds) {
			foreach ($userIds as $key=>$val) {
				$responser = $this->UserApi->view($val);
				if (! $responser->isFail()) {
					$user = $responser->getData();
					array_push($users, $user);
				}
			}
		}
		$this->set('data', $data);
		$this->set('items', $users);
		$this->set('id', $id);
	}
	/**
	 * 待关联用户列表
	 *
	 * @param string $id        	
	 * @param string $kw
	 *        	搜索条件
	 */
	public function userList($id, $kw = '') {
		$data = $this->Partner->findById($id);
		$limit = 20;
		$items = array();
		$total = 0;
		$query = array(
			'page'=>$this->request->query('page'), 
			'limit'=>$limit 
		);
		if ($kw) {
			$query = am($query, array(
				'username'=>$kw 
			));
		}
		$this->paginate = array(
			'limit'=>$limit, 
			'paramType'=>"querystring" 
		);
		$query['status'] = 1;
		$responser = $this->UserApi->index($query);
		if (! $responser->isFail()) {
			$users = $responser->getData();
			$this->redirectPage($users, $limit);
			$this->User->results = $users['items'];
			$this->User->count = $users['total'];
			$items = $this->paginate('User');
			$items = is_array($items) ? $items : array();
			$total = $users['total'];
			$partner_users = $this->relationUsers();
		} else {
			$this->Session->setFlash($responser->getMessage());
		}
		$this->set('data', $data);
		$this->set('items', $items);
		$this->set('total', $total);
		$this->set('partner_users', $partner_users);
		$this->set('kw', $kw);
		$this->set('id', $id);
	}
	/**
	 * 已关联的所有用户ID
	 */
	private function relationUsers() {
		$partner_users = array();
		$condition = array(
			'conditions'=>array(
				'user_total'=>array(
					'$gt'=>0 
				) 
			) 
		);
		// 取出已关联的所有用户
		$partners = $this->Partner->find('all', $condition);
		if ($partners) {
			foreach ($partners as $key=>$val) {
				foreach ($val['Partner']['user_id'] as $k=>$v) {
					array_push($partner_users, $v);
				}
			}
		}
		return $partner_users;
	}
	/**
	 * 向内容合作商中增加用户
	 */
	public function pushUser($userId) {
		$this->autoRender = false;
		$partnerId = $this->request->query('partner_id');
		$role = $this->request->query('role');
		$partnerName = $this->request->query('partner_name');
		$msg = '关联失败';
		
		if ($partnerName == '' || $partnerId == '')
			return;
		$responser = $this->UserApi->view($userId);
		if (! $responser->isFail()) {
			$user = $responser->getData();
		} else {
			throw '用户不存在或已被刪除';
		}
		// 把用戶ID放入內容提供商
		$result = $this->Partner->push($partnerId, $user);
		// 把用户设为冻结帐户类型
		if ($role == 'true' && $result) {
			$responser = $this->RoleApi->edit(array(
				'user_id'=>$userId, 
				'role'=>'freeze' 
			));
		}
		// 修改用戶所哪個內容提供商
		$responser = $this->UserApi->edit($userId, array(
			'belong_partner'=>$partnerName 
		));
		
		$response = new \Controller\Response\Response();
		return ! $responser->isFail() ? $response->message(TRUE, __('关联成功')) : $response->message(FALSE, $msg);
	}
	/**
	 * 删除内容合作商中用户
	 */
	public function pullUser($userId) {
		$this->autoRender = false;
		$partnerId = $this->request->query('partner_id');
		$role = $this->request->query('role');
		
		$responser = $this->UserApi->view($userId);
		if (! $responser->isFail()) {
			$user = $responser->getData();
		}
		$result = $this->Partner->pull($partnerId, $user);
		
		if ($role == 'true' && $result) {
			$responser = $this->RoleApi->edit(array(
				'role'=>'user', 
				'user_id'=>$userId 
			));
		}
		// 清空用戶所屬哪個內容合作商
		$responser = $this->UserApi->edit($userId, array(
			'belong_partner'=>'' 
		));
		$response = new \Controller\Response\Response();
		return ! $responser->isFail() ? $response->message(TRUE, __('取消关联成功')) : $response->message(FALSE, '取消关联失败');
	}
	/**
	 * 导出相关内容合作商发表的鱼说
	 */
	private function export($data) {
		require_once (VENDORS . "emoji/emoji.php");
		$this->autoRender = false;
		$items[0]['title'] = '标题';
		$items[0]['language'] = '语言';
		$items[0]['length'] = '时长';
		$items[0]['score'] = '评分';
		$items[0]['user_id'] = '作者';
		$items[0]['checkout_total'] = '销量';
		$items[0]['comment_total'] = '评论';
		$items[0]['earn_total'] = '总收入';
		$items[0]['lng'] = '经度';
		$items[0]['lat'] = '纬度';
		$items[0]['play_total'] = '播放次数';
		$items[0]['approved'] = '首次上架时间';
		if ($data) {
			foreach ($data as $k=>$v) {
				$user = $this->SlaveUser->getById($v['user_id']);
				$items[$k + 1]['title'] = strip_tags(emoji_unified_to_html($v['title'])); // preg_match('/\S/', $v['title']);
				$items[$k + 1]['language'] = $v['language'];
				$items[$k + 1]['length'] = $v['length'];
				$items[$k + 1]['score'] = $v['score'] * 2;
				$items[$k + 1]['user_id'] = strip_tags(emoji_unified_to_html($user['username']));
				$items[$k + 1]['checkout_total'] = $v['checkout_total'];
				$items[$k + 1]['comment_total'] = $v['comment_total'];
				$items[$k + 1]['earn_total'] = $v['earn_total'];
				$items[$k + 1]['lng'] = $v['location']['lng'];
				$items[$k + 1]['lat'] = $v['location']['lat'];
				$items[$k + 1]['play_total'] = isset($v['play_total']) ? $v['play_total'] : '';
		
				$items[$k + 1]['approved'] = date('Y-m-d H:i:s', $v['approved']->sec);
			}
			
			$filename = 'fs' . date('YmdHis') . '.csv';
			ob_clean();
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename=' . $filename);
			$fp = fopen('php://output', 'w');
			fwrite($fp, "\xEF\xBB\xBF");
			foreach ($items as $fields) {
				
				fputcsv($fp, $fields);
			}
			fclose($fp);
		}
		return;
	}
}