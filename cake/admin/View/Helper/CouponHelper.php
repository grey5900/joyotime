<?php

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
class CouponHelper extends AppHelper {
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
	public function number() {
		return $this->get($this->row, 'number', 0);
	}
	public function addList() {
		return '/coupons/add';
	}
	public function editLink($role) {
		$id = $this->get($this->row, '_id');
		if ($role == 'admin') {
			return $this->Html->link(__('编辑'), "/coupons/edit/{$id}");
		}
	}
	public function export($role) {
		if ($role == 'admin') {
			$id = $this->get($this->row, '_id');
			return $this->Html->link(__('导出'), "/coupons/export/" . $id);
		}
	}
	public function detail($role) {
		if ($role == 'admin') {
			$id = $this->get($this->row, '_id');
			
			return $this->Html->link(__('明细'), "/coupons/detail/" . $id);
		}
	}
	public function delete() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('删除'), "#common-hide-modal", array(
				'data-url'=>"/tags/delete/" . $id,
				'class'=>'delete-hide-link',
				'data-toggle'=>"modal" 
		));
	}
	public function description() {
		return $this->get($this->row, 'description', '');
	}
	public function length() {
		$length = $this->get($this->row, 'length', 0);
		if ($length != 0) {
			$length = round($length / 60, 2);
		}
		return $length;
	}
	public function expire() {
		$expire = $this->get($this->row, 'expire', 0);
		if ($expire) {
			return strftime('%Y-%m-%d', $expire);
		}
		return '';
	}
	public function code() {
		return $this->get($this->row, 'code', '');
	}
	public function codeUseUser() {
		return $this->gets($this->row, 'user.username', '未使用');
	}
	public function used() {
		$used = $this->get($this->row, 'used', 0);
		if ($used) {
			return date('Y-m-d H:i:s', $used);
		}
	}
	public function batchNumber() {
		$created = $this->get($this->row, 'created', 0);
		if ($created) {
			return $created['sec'];
		}
	}
}