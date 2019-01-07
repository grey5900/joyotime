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
class TagHelper extends AppHelper {
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
	public function name() {
		return $this->get($this->row, 'name', '');
	}
	public function addList($category) {
		return '/tags/add/' . $category;
	}
	public function editLink() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('编辑'), "/tags/edit/{$id}");
	}
	public function subCategoryLink() {
		$category = $this->get($this->row, 'category');
		return $this->Html->link(__('管理标签'), "/tags/index/{$category}");
	}
	public function delete() {
		$id = $this->get($this->row, '_id');
		return $this->Html->link(__('删除'), "#common-hide-modal", array(
				'data-url'=>"/tags/delete/" . $id,
				'class'=>'delete-hide-link',
				'data-toggle'=>"modal" 
		));
	}
	public function category($getCategory) {
		$category = $this->get($this->row, 'category');
		if($getCategory){
			return $category;
		}else{
			
			return $this->Html->link($category, "/tags/index/{$category}");
		}
		
	}
}