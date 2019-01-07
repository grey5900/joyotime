<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Voice', 'Model');
require_once (VENDORS . "emoji/emoji.php");

define('FORMAT_DATE', '%Y-%m-%d');
define('FORMAT_TIME', '%Y-%m-%d %H:%M:%S');
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
class VoiceHelper extends AppHelper {
	const STATUS_PENDING = 0;
	const STATUS_APPROVED = 1;
	const STATUS_INVALID = 2;
	const STATUS_UNAVAILABLE = 3;
	public $helpers = array(
			'Html',
			'Time' 
	);
	private $row = array();
	
	/**
	 *
	 * @var UserHelper
	 */
	private $user;
	public $rows = array();
	public function setUser(UserHelper $user) {
		$this->user = $user;
	}
	
	/**
	 *
	 * @param array $row        	
	 * @return UserHelper
	 */
	public function init(array $row = array()) {
		$this->row = $row;
		return $this;
	}
	
	/**
	 *
	 * @param int $type
	 *        	status of voice
	 * @return string
	 */
	public function listLink($status) {
		$link = '/voices/index/' . $status;
		if ($this->user) {
			return $link . '/' . $this->user->id();
		}
		return $link;
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
		$shortId = $this->get($this->row, 'short_id');
		$url = '';
		if ($shortId) {
			$url = $webHostUrl. 'voice'.DS. $shortId;
		}
		return $url;
	}
	public function subTitle($status) {
		$text = $author = '';
		switch (intval($status)) {
			case self::STATUS_PENDING :
				$text = '<span class="pending-color">' . __('Pending') . '</span>';
				break;
			case self::STATUS_APPROVED :
				$text = '<span class="approved-color">' . __('Approved') . '</span>';
				break;
			case self::STATUS_INVALID :
				$text = '<span class="invalid-color">' . __('Invalid') . '</span>';
				break;
			case self::STATUS_UNAVAILABLE :
				$text = '<span class="unavailable-color">' . __('Unavailable') . '</span>';
				break;
		}
		
		$info = '<em>' . __("（共" . $this->user->voiceTotal() . "条，总时长" . $this->user->voiceLengthTotal() . "）") . '</em>';
		
		if (count($this->rows) > 0) {
			return __("“" . $this->user->username() . "”" . $text . "的鱼说" . $info);
		}
	}
	public function offset() {
		return $this->get($this->row, 'offset', 0);
	}
	public function status() {
		$status = $this->get($this->row, 'status', 0);
		switch (intval($status)) {
			case self::STATUS_PENDING :
				return '<span class="pending-color">' . __('Pending') . '</span>';
				break;
			case self::STATUS_APPROVED :
				return '<span class="approved-color">' . __('Approved') . '</span>';
				break;
			case self::STATUS_INVALID :
				return '<span class="invalid-color">' . __('Invalid') . '</span>';
				break;
			case self::STATUS_UNAVAILABLE :
				return '<span class="unavailable-color">' . __('Unavailable') . '</span>';
				break;
		}
		return '<span class="pending-color">' . __('Pending') . '</span>';
	}
	public function id() {
		return $this->get($this->row, '_id');
	}
	public function title() {
		$title = $this->get($this->row, 'title', '');
		if ($title) {
			$title = emoji_unified_to_html($title);
		}
		return $title;
	}
	public function edit() {
		$status = $this->get($this->row, 'status', 0);
		$id = $this->get($this->row, '_id');
		if ($status == self::STATUS_INVALID && $id) {
			return $this->Html->link(__('Edit'), "/voices/edit/{$id}");
		}
	}
	public function remove() {
		$status = $this->get($this->row, 'status', 0);
		$id = $this->get($this->row, '_id');
		if ($status == self::STATUS_INVALID && $id) {
			return $this->Html->link(__(''), "#remove", array(
					'class'=>'icon-delete',
					'data-toggle'=>'modal',
					'data-remove'=>'/voices/remove/' . $id 
			));
		}
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
	public function cover($demension = 'x80') {
		
		$cover = $this->get($this->row, 'cover', array());
		$title = $this->get($this->row, 'title');
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
		if (isset($cover[$demension])) {
			return $cover[$demension];
		}
		return '';
	}
	public function address() {
		$voice = $this->get($this->row, 'voice');
		if ($voice) {
			return $voice;
		}
		return '';
	}
	public function point() {
		$lat = $this->get($this->row['location'], 'lat');
		$lng = $this->get($this->row['location'], 'lng');
		if ($lat && $lng) {
			return new Point($lat, $lng);
		}
		return new Point();
	}
	public function isfree() {
		$isfree = $this->get($this->row, 'isfree');
		if ($isfree) {
			return $this->Html->link(__('Free'), 'javascript:;', array(
					'class'=>'isfree' 
			));
		}
		return '';
	}
	public function language() {
		return $this->get($this->row, 'language');
	}
	public function author($row = array(), $type) {
		if ($row) {
			$username = $this->get($row['user'], 'username');
		} else {
			$username = $this->get($this->row['user'], 'username');
		}
		if ($username) {
			return html_entity_decode($this->Html->link(emoji_unified_to_html($username), "/voices/index/$type/" . $this->get($this->row, 'user_id')));
		} else {
			return __('anonymous');
		}
	}
	public function invalid() {
		$id = $this->get($this->row, '_id');
		$status = $this->get($this->row, 'status', 0);
		if ($status != self::STATUS_INVALID && $id) {
			return $this->Html->link(__('Reject'), '#invalidmodal', array(
					'data-url'=>"/voices/invalid/{$id}",
					'data-toggle'=>"modal",
					'class'=>'invalid-link' 
			));
		}
	}
	public function approved($title = 'Approve') {
		$id = $this->get($this->row, '_id');
		$status = $this->get($this->row, 'status', 0);
		$score = $this->get($this->row, 'score', 0);
		
		if (($status != self::STATUS_APPROVED   && $id)  && ( $score!=0 || $title=='Reshelf') ) {
			
			return $this->Html->link($title, "#common-hide-modal", array(
					'data-url'=>"/voices/approved/" . $id,
					'class'=>'score-tip-link',
					'data-toggle'=>"modal"
			));
			/**
			return $this->Html->link($title, "javascript:void(0);", array(
					'data-url'=>"/voices/approved/{$id}",
					'class'=>'approved-link',
					'data-loading-text'=>'正在上架...' 
			));
		**/	
		
		}else{
			return $this->Html->link($title, "#score-hide-modal", array(
					'data-url'=>"/voices/approved/" . $id,
					'class'=>'score-tip-link',
					'data-toggle'=>"modal"
			));
		}
		//if ($status != self::STATUS_APPROVED && $id && $score==0) {
			
		//}
	}
	public function recommendCancel() {
		$id = $this->get($this->row, '_id');
		$status = $this->get($this->row, 'recommend', 0);
		if ($status == Voice::RECOMMENDED_STATUS) {
			return $this->Html->link(__("取消推荐"), "#common-hide-modal", array(
					'data-url'=>"/voices/recommendCancel/" . $id,
					'class'=>'recommend-tip-link',
					'data-toggle'=>"modal" 
			));
		}
	}
	/**
	 * 取得用户类型名称
	 * @return string|Ambigous <string>
	 */
	public function rolename($type='') {
		$belong_partner = isset($this->row['user']['belong_partner']) ? $this->row['user']['belong_partner']:'';
		//if($belong_partner!=''){
			//return '<span class="role-partner">'.$belong_partner.'</span>';
		//}
		$role = $this->get($this->row['user'], 'role', 'user');
		$roleStr = '';
		$belong_span = $type =='' ?'<br /><span class="belong_partner">'.$belong_partner.'</span>':'';
		$roleArr = array(
				'admin'=>'<span class="role-admin">' . __('管理员') . '</span>'.$belong_span,
				'checker'=>'<span class="role-checker">' . __('审核账号') . '</span>'.$belong_span,
				'robot'=>'<span class="role-robot">' . __('普通账号') . '</span>'.$belong_span,
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
	public function getApproveType(){
		return $this->row['user']['username'];
	}
	public function unavailable() {
		$id = $this->get($this->row, '_id');
		$status = $this->get($this->row, 'status', 0);
		if ($status != self::STATUS_UNAVAILABLE && $id) {
			return $this->Html->link(__('Off shelf'), "#unavailable-modal", array(
					'data-url'=>"/voices/unavailable/{$id}",
					'class'=>'unavailable-link',
					'data-toggle'=>"modal" 
			));
		}
	}
	
	/**
	 *
	 * @deprecated
	 *
	 *
	 * @see VoiceHelper::time()
	 */
	public function created() {
		$created = $this->get($this->row, 'created', array());
		if ($created && isset($created['sec'])) {
			return strftime(FORMAT_TIME, $created['sec']);
		}
		return '';
	}
	/**
	 * 首页上架时间
	 * @return string
	 */
	public function approvedTime() {
		$approved = $this->get($this->row, 'approved', array());;
		if ($approved && isset($approved->sec)) {
			return strftime(FORMAT_TIME, $approved->sec);
		}
		
		return '';
	}
	public function time($name = 'created') {
		$time = $this->get($this->row, $name, array());
		if (isset($time['sec'])) {
			return strftime(FORMAT_TIME, $time['sec']);
		} elseif (isset($time)) {
			return strftime(FORMAT_TIME, $time);
		}
		return '';
	}
	public function approvedDate() {
		$approved = $this->get($this->row, 'approved','');;
		$time = $this->get($this->row, 'status_modified', array());
		if ($time && isset($time['sec'])) {
			return strftime(FORMAT_TIME, $time['sec']);
		}
		return '';
	}
	public function invalidDate() {
		$time = $this->get($this->row, 'status_modified', array());
		if ($time && isset($time['sec'])) {
			return strftime(FORMAT_TIME, $time['sec']);
		}
		return '';
	}
	public function unavailableDate() {
		$time = $this->get($this->row, 'status_modified', array());
		if ($time && isset($time['sec'])) {
			return strftime(FORMAT_TIME, $time['sec']);
		}
		return '';
	}
	public function tags() {
		$tags = $this->get($this->row, 'tags');
		$count = $tags ? count($tags) : 0;
		return $count;
	}
	public function createForm() {
		return $this->get($this->row, 'created_from');
	
		
	}
	
	public function invalidComment() {
		return $this->get($this->row, 'comment', '');
	}
	public function unavailableComment() {
		return $this->get($this->row, 'comment', '');
	}
	public function checkoutTotal() {
		return $this->get($this->row, 'checkout_total', 0);
	}
	function recommendLink() {
		$voiceId = $this->get($this->row, '_id', '');
		return $this->Html->link('推荐', "javascript:void(0);", array(
				'data-url'=>"/voices/recommend/{$voiceId}",
				'class'=>'recommend-voice-link',
				'data-loading-text'=>'正在处理...' 
		));
	}
	/**
	 * Get earn total of voice.
	 *
	 * @param array $row        	
	 * @return float The unit is minute. e.g. 23.5 min
	 */
	public function earnTotal() {
		$earnTotal = $this->get($this->row, 'earn_total', 0);
		return $this->Time->format($earnTotal);
	}
	public function bought(){
		return $this->get($this->row, 'bought', 0);
	}
	/**
	 * Get play total of voice.
	 *
	 * @param array $row        	
	 * @return int
	 */
	public function playTotal() {
		return $this->get($this->row, 'play_total', 0);
	}
	public function tagTotal($index) {
		$tags = $this->get($this->row, 'tags', '');
		$id = $this->get($this->row, '_id', '');
		$tag_total = 0; 
		if($tags){
			$tag_total = count($tags);
		}
		return $this->Html->link($tag_total, "/voices/tag/$id/$index");
	}
	public function length() {
		$length = $this->get($this->row, 'length', 0);
		return $this->Time->format($length);
	}
	public function commentTotal() {
		return $this->get($this->row, 'comment_total', 0);
	}
	public function score() {
		$score = $this->get($this->row, 'score', 0);
		if ($score) {
			return round($score * 2, 1);
		}
		return round($score, 1);
	}
}

/**
 * The model of coordinate
 */
class Point {
	private $lat = 0;
	private $lng = 0;
	public function __construct($lat = '', $lng = '') {
		$this->lat = $lat;
		$this->lng = $lng;
	}
	public function latitude() {
		return $this->lat;
	}
	public function longitude() {
		return $this->lng;
	}
}