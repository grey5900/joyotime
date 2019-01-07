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
class PackagesHelper extends AppHelper {
	public $helpers = array(
			'Html',
			'Time' 
	);
	private $row = array();
	
	/**
	 *
	 * @param array $row        	
	 * @return PackageHelper
	 */
	public function init(&$row = array()) {
		$this->row = $row;
		return $this;
	}
	public function title() {
		return $this->get($this->row, 'title', '');
	}
	public function created() {
		$created = $this->get($this->row, 'created', array());
		if ($created && isset($created['sec'])) {
			return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
		}
		return '';
	}
	/**
	 * get packages status
	 * 
	 * @return string
	 */
	public function status() {
		$status = $this->get($this->row, 'status', 0);
		$id = $this->get($this->row, '_id');
		switch (intval($status)) {
			case Package::PENDING :
				return '<span class="processed-color text-danger" id="status-' . $id . '">' . __('未上架') . '</span>';
				break;
			case Package::AVALIABLE :
				return '<span class="reverted-color" id="status-' . $id . '">' . __('已上架') . '</span>';
				break;
		}
		return '';
	}
	/**
	 *
	 * @deprecated add
	 * @return string
	 */
	public function addList() {
		$link = '/packages/add';
		return $link;
	}
	public function relationVoiceLink($id) {
		$link = '/packages/relationVoice/' . $id . '/1';
		return $link;
	}
	/**
	 * new relation voice
	 *
	 * @param unknown $id        	
	 * @return string
	 */
	public function newRelationLink($id) {
		$link = '/packages/relationVoice/' . $id . '/2';
		return $link;
	}
	/**
	 * handle status link
	 */
	public function statusHandle() {
		$status = $this->get($this->row, 'status');
		$id = $this->get($this->row, '_id');
		$status_str = $status == Package::PENDING ? '上架' : '下架';
		return $this->Html->link(__($status_str), "#package-hide-modal", array(
				'data-url'=>"/packages/status/" . $id,
				'class'=>'package-hide-link',
				'data-toggle'=>"modal" 
		));
	}
	
	/**
	 * edit link
	 */
	public function edit() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('Edit'), "/packages/edit/{$id}");
	}
	/**
	 * relation link
	 */
	public function relation() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link('关联鱼说', "/packages/relationVoice/{$id}/1");
	}
	/**
	 * push voice link
	 * 
	 * @param string $voice_id        	
	 * @param string $package_id        	
	 * @param array $voice_ids        	
	 */
	public function pushVoiceLink($voice_id, $package_id, $voice_ids) {
		$id = $this->get($this->row, '_id');
		$str = '';
		foreach ($voice_ids as $key=>$val) {
			if (in_array($voice_id, $voice_ids)) {
				$str = '(已关联)';
			}
		}
		if ($str) {
			return '<span class="text-muted">已关联</span>';
		} else {
			return $this->Html->link('加入鱼说包', "javascript:void(0);", array(
					'data-url'=>"/packages/pushVoice/{$voice_id}/$package_id",
					'class'=>'push-voice-link',
					'data-loading-text'=>'正在加入...' 
			));
		}
	}
	public function pullVoiceLink($voice_id, $package_id) {
		return $this->Html->link('删除', "javascript:void(0);", array(
				'data-url'=>"/packages/pullVoice/{$voice_id}/$package_id",
				'class'=>'pull-voice-link',
				'data-loading-text'=>'正在删除...' 
		));
	}
	// favorite_total 收藏数
	public function favoriteTotal() {
		return $this->get($this->row, 'favorite_total', 0);
		
	}
	/**
	 * show relation voices
	 */
	public function relationVoices() {
		$voice_total = $this->get($this->row, 'voice_total', 0);
		$id = $this->get($this->row, '_id');
		return $this->Html->link($voice_total, "/packages/relationVoice/{$id}/1");
	}
	
	/**
	 * delete link
	 */
	public function delete() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__("删除"), "#delete-hide-link", array(
				'data-url'=>"/packages/delete/{$id}",
				'class'=>'delete-hide-link',
				'data-toggle'=>"modal" 
		));
		
		// return $this->Html->link ( '', "/voices/delete/{$id}" );
	}
	/**
	 * time
	 */
	public function voiceLengthTotal() {
		$voice_length_total = $this->get($this->row, 'voice_length_total', 0);
		return $this->Time->format($voice_length_total);
	}
	/**
	 * Get cover with different size
	 *
	 * @param array $row        	
	 * @param string $demension
	 *        	The $demension available values including,
	 *        	x80, x160, x640, source
	 * @return string
	 */
	public function cover(&$row = array(), $demension = 'x80') {
		$cover = $this->gets($row, 'cover', array());
		$title = $this->gets($row, 'title');
		// print_r($title);exit;
		switch ($demension) {
			case 'x80' :
				$width = $height = 80;
				break;
			case 'x160' :
				$width = $height = 160;
				break;
			case 'x640' :
				$width = $height = 640;
				break;
			case 'source' :
				$width = $height = 640;
				break;
			default :
				$width = $height = 80;
		}
		if ($cover && isset($cover[$demension])) {
			return $cover[$demension];
		}
		return '';
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
		$packageId = $this->get($this->row, '_id');
		$url = '';
		if ($packageId) {
			$url = $webHostUrl.'package'.DS. $packageId;
		}
		return $url;
	}
}