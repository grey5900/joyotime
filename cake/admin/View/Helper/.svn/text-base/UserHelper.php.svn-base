<?php
App::uses('AppHelper', 'View/Helper');
require_once (VENDORS . "emoji/emoji.php");
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The RESTful style API is used to communicate with each client-side.
 *
 * PHP 5
 *
 * FishSaying(tm) : FishSaying (http://www.fishsaying.com)
 * Copyright (c) fishsaying.com. (http://fishsaying.com)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link http://fishsaying.com FishSaying(tm) Project
 * @since FishSaying(tm) v 0.0.1
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class UserHelper extends AppHelper {
	public $helpers = array(
		'Html', 
		'Time' 
	);
	private $row = array();
	
	/**
	 *
	 * @param array $row        	
	 * @return UserHelper
	 */
	public function init(&$row = array()) {
		$this->row = $row;
		return $this;
	}
	public function username() {
		return emoji_unified_to_html($this->get($this->row, 'username', ''));
	}
	/**
	 *
	 *
	 * 用户列表用户类型单独处理
	 * 
	 * @return string
	 */
	public function rolename($type='') {
		$belong_partner = $this->get($this->row, 'belong_partner', '');
		//if ($belong_partner != '') {
		//	return '<span class="role-partner">' . $belong_partner . '</span>';
		//}
		$role = $this->get($this->row, 'role', 'user');
		$roleStr = '';
		$belong_span = $type =='' ?'<br /><span class="belong_partner">'.$belong_partner.'</span>':'';
		$roleArr = array(
				'admin'=>'<span class="role-admin">' . __('管理员') . '</span>'.$belong_span,
				'robot'=>'<span class="role-robot">' . __('普通账号') . '</span>'.$belong_span,
				'checker'=>'<span class="role-checker">' . __('审核账号') . '</span>'.$belong_span,
				'user'=>'<span class="role-user">' . __('普通账号') . '</span>'.$belong_span,
				'freeze'=>'<span class="role-freeze">' . __('合作导游账号') . '</span>'.$belong_span
		);
		if (strpos($role, '|') !== false) {
			$roles = explode('|',$role);
			foreach ($roles as $item){
				$roleStr .= $roleArr[$item].'|';
			}
			return trim($roleStr,'|');
		}
		return in_array($role, array_keys($roleArr)) ? $roleArr[$role] :'';
	}
	
	/**
	 * google api
	 *
	 * @param string $data
	 *        	二维码包含的信息，可以是数字、字符、二进制信息、汉字。不能混合数据类型，数据必须经过UTF-8 URL-encoded.如果需要传递的信息超过2K个字节，请使用POST方式
	 * @param int $size
	 *        	生成二维码的尺寸设置
	 * @param string $EC_level
	 *        	可选纠错级别，QR码支持四个等级纠错，用来恢复丢失的、读错的、模糊的、数据。
	 *        	L-默认：可以识别已损失的7%的数据
	 *        	M-可以识别已损失15%的数据
	 *        	Q-可以识别已损失25%的数据
	 *        	H-可以识别已损失30%的数据
	 * @param int $margin
	 *        	生成的二维码离图片边框的距离
	 */
	public function orimage($data, $size = 200, $EC_level = 'L', $margin = '0') {
		$url = urlencode($data);
		return '<img src="http://chart.apis.google.com/chart?chs=' . $size . 'x' . $size . '&cht=qr&chld=' . $EC_level . '|' . $margin . '&chl=' . $data . '" width="' . $size . '" height="' . $size . '"/>';
	}
	public function orurl($webHostUrl) {
		$id = $this->get($this->row, '_id');
		$url = '';
		if ($id) {
			$url = $webHostUrl . 'user' . DS . $id;
		}
		return $url;
	}
	public function id() {
		return $this->get($this->row, '_id', '');
	}
	public function avatar() {
		$avatar = $this->get($this->row, 'avatar', array());
		$avatar = $this->get($avatar, 'x80', Configure::read('Default.Avatar'));
		return $this->Html->image($avatar);
	}
	public function locale() {
		return $this->get($this->row, 'locale', 'zh_CN');
	}
	public function email() {
		return $this->get($this->row, 'email', '');
	}
	public function verifiedDescription() {
		$id = $this->get($this->row, '_id', '');
		return '<span id="vd-' . $id . '">' . $this->get($this->row, 'verified_description', '') . '</span>';
	}
	public function recommendReason() {
		$id = $this->get($this->row, '_id', '');
		return '<span id="vd-' . $id . '">' . $this->get($this->row, 'recommend_reason', '') . '</span>';
	}
	public function money() {
		$time = $this->get($this->row, 'money', '');
		return $this->Time->format($time);
	}
	public function income() {
		$time = $this->get($this->row, 'income', '');
		return $this->Time->format($time);
	}
	public function earn() {
		$time = $this->get($this->row, 'earn', '');
		return $this->Time->format($time);
	}
	public function created() {
		$created = $this->get($this->row, 'created', array());
		if ($created && isset($created['sec'])) {
			return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
		}
		return '';
	}
	public function cost() {
		$time = $this->get($this->row, 'cost', '');
		return $this->Time->format($time);
	}
	public function purchaseTotal() {
		return $this->get($this->row, 'purchase_total', 0);
	}
	public function voiceTotal() {
		return $this->get($this->row, 'voice_total', 0);
	}
	public function voiceLengthTotal() {
		$time = $this->get($this->row, 'voice_length_total', 0);
		return $this->Time->format($time);
	}
	public function isAdmin() {
		$sess = CakeSession::read('Auth.User');
		$role = $this->get($sess, 'role', '');
		return 'admin' == $role;
	}
	public function role() {
		return $this->get($this->row, 'role', '');
	}
	public function setRole() {
		if ($this->isAdmin()) {
			return $this->Html->link(__('设置权限'), "#setting-role-modal", 
					array(
						'data-url'=>"/users/role/" . $this->id(), 
						'username'=>$this->username(), 
						'data-role'=>$this->role(), 
						'class'=>'setting-role-link', 
						'data-toggle'=>"modal" 
					));
		}
		return '';
	}
	public function sendMessage() {
		return $this->Html->link(__('推送消息'), "#send-message-modal", array(
			'data-url'=>"/users/send_message/" . $this->id(), 
			'username'=>$this->username(), 
			'class'=>'send-message-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function sendGift() {
		return $this->Html->link(__('赠送时长'), "#send-gift-modal", array(
			'data-url'=>"/users/send_gift/" . $this->id(), 
			'username'=>$this->username(), 
			'class'=>'send-gift-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function authUserLink() {
		return '/users/authList';
	}
	public function recommendUserLink() {
		return '/users/recommendList';
	}
	public function recommendAddUserLink() {
		return '/users/recommendAddList';
	}
	public function authAddLink() {
		return '/users/authAddList';
	}
	public function authEditLink() {
		return $this->Html->link(__('编辑认证信息'), "#user-auth-modal", array(
			'data-url'=>"/users/authDo/" . $this->id(), 
			'user_id'=>$this->id(), 
			'class'=>'user-auth-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function recommendEditLink() {
		return $this->Html->link(__('编辑理由'), "#user-recommend-modal", array(
			'data-url'=>"/users/recommend/" . $this->id(), 
			'user_id'=>$this->id(), 
			'class'=>'user-auth-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function authCancelLink() {
		return $this->Html->link(__('取消认证'), "#common-hide-modal", array(
			'data-url'=>"/users/authCancel/" . $this->id(), 
			'username'=>$this->username(), 
			'class'=>'auth-cancel-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function recommendDeleteLink() {
		return $this->Html->link(__('删除'), "#common-hide-modal", array(
			'data-url'=>"/users/recommendDelete/" . $this->id(), 
			'username'=>$this->username(), 
			'class'=>'auth-cancel-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function authLink() {
		$userId = $this->get($this->row, '_id', '');
		return $this->Html->link(__('认证'), "#user-auth-modal", array(
			'data-url'=>"/users/authDo/" . $userId, 
			'username'=>$this->username(), 
			'class'=>'user-auth-link', 
			'data-toggle'=>"modal" 
		));
	}
	public function recommendLink() {
		$userId = $this->get($this->row, '_id', '');
		$recommend = $this->get($this->row, 'recommend', '');
		if ($recommend == 1) {
			return '<span class="text-muted">已推荐</span>';
		}
		return $this->Html->link(__('推荐'), "#user-recommend-modal", array(
			'data-url'=>"/users/recommend/" . $userId, 
			'username'=>$this->username(), 
			'class'=>'user-recommend-link', 
			'data-toggle'=>"modal" 
		));
	}
	/**
	 * 关联链接
	 */
	public function relationPartnerLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('关联'), "#partner-hide-modal", array(
			'data-url'=>"/partners/pushUser/" . $id, 
			'class'=>'partner-hide-link', 
			'data-toggle'=>"modal" 
		));
	}
	/**
	 * 机构认证 充值链接
	 */
	public function rechargeLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('充值'), "#recharge-modal", array(
				'data-url'=>"/partners/pushUser/" . $id,
				'class'=>'user-hide-link',
				'data-toggle'=>"modal"
		));
	}
	/**
	 * 机构认证 编辑认证信息
	 */
	public function agencyAuthInfoEditLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('编辑认证信息'), "/users/agencyAuthInfoEdit/" . $id);
	}
	/**
	 * 机构认证 取消认证
	 */
	public function agencyAuthCancelLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('取消认证'), "#common-hide-modal", array(
				'data-url'=>"/users/AgencyAuthCancel/" . $id,
				'class'=>'user-hide-link',
				'data-toggle'=>"modal"
		));
	}
	/**
	 * 机构认证 认证
	 */
	public function agencyAuthLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('认证'), "#common-hide-modal", array(
				'data-url'=>"/users/doAgencyAuth/" . $id,
				'class'=>'partner-hide-link',
				'data-toggle'=>"modal"
		));
	}
	/**
	 * 取消关联链接
	 */
	public function relationPartnerCancleLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('取消关联'), "#partner-hide-modal", array(
			'data-url'=>"/partners/pullUser/" . $id, 
			'class'=>'partner-hide-link', 
			'data-toggle'=>"modal" 
		));
	}
}