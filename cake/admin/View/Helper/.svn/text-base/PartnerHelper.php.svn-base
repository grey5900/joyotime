<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Partner', 'Model');

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
class PartnerHelper extends AppHelper {
	public $helpers = array(
		'Html', 
		'Time' 
	)
	;
	private $row = array();
	
	/**
	 *
	 * @param array $row        	
	 * @return PartnerHelper
	 */
	public function init(&$row = array()) {
		$this->row = $row;
		return $this;
	}
	public function name() {
		return $this->get($this->row, 'name', '');
	}
	public function created() {
		$created = $this->get($this->row, 'created', array());
		if ($created && isset($created['sec'])) {
			return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
		}
		return '';
	}
	public function addLink() {
		return '/partners/add';
	}
	// 关联用户数
	public function relationUserNum() {
		$userTotal = $this->get($this->row, 'user_total', 0);
		$id = $this->get($this->row, '_id');
		return $this->Html->link($userTotal, "/partners/relationUser/" . $id);
	}
	// 解说数
	public function voiceNum() {
		return $this->get($this->row, 'voice_total', 0);
	}
	// 总时长
	public function timeLength() {
		$length = $this->get($this->row, 'voice_length_total', 0);
		return $this->formatTime($length);
	}
	public function formatTime($length) {
		if ($length) {
			return $this->Time->format($length);
		} else {
			return "0'0''";
		}
	}
	// 销量
	public function saleNum() {
		return $this->get($this->row, 'checkout_total', 0);
	}
	// 播放次数
	public function playNum() {
		return $this->get($this->row, 'play_total', 0);
	}
	// 总收入
	public function earnTotal() {
		$earnTotal = $this->get($this->row, 'earn_total', 0);
		return $this->Time->format($earnTotal);
	}
	public function editLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('编辑'), "/partners/edit/" . $id);
	}
	public function relationUserLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('关联帐号'), "/partners/relationUser/" . $id);
	}
	public function statisticsLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('统计'), "/partners/statistics/" . $id);
	}
}