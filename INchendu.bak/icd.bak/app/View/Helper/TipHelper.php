<?php
APP::uses('AppHelper', 'View/Helper');
class TipHelper extends AppHelper {
    
/**
 * @var array
 */
    private $tips = array();
    
/**
 * @var TipItem
 */
    private $item;
    
    public $helpers = array(
        'Html'
    );
    
/**
 * Initialize helper
 * @param array $tips
 */
    public function initialize($tips = array()) {
        if(isset($tips['result_list']['page_data'])) {
            $this->tips = $tips['result_list']['page_data'];
        } else {
            $this->tips = $tips;
        }
        $this->item = new TipItem();
    }
    
    public function item($item = array()) {
        $this->item->initialize($item, $this->Html);
        return $this->item;
    }
    
/**
 * To check whether product has exist or not.
 * @return boolean It returns true if exist.
 */
    public function has() {
        return count($this->tips) > 0;
    }
    
/**
 * @return number
 */
    public function count() {
        return count($this->tips);
    }
    
/**
 * Returns all events
 * @return array
 */
    public function getAll() {
        return $this->tips;
    }
}

class TipItem {
    
    private $item = array();
/**
 * @var HtmlHelper
 */
    private $html;
    
    public function initialize($item = array(), HtmlHelper $html) {
        $this->item = $item;
        $this->html = $html;
    }
    
    public function cover($options = array()) {
        if(isset($this->item['photo_uri']) && !empty($this->item['photo_uri'])) {
            return $this->html->image($this->item['photo_uri'], $options);
        }
    }
    
    public function content() {
        if(isset($this->item['content'])) {
            return $this->item['content'];
        }
    }
    
    public function hot() {
        if(isset($this->item['is_essence']) && $this->item['is_essence'] > 0) {
        	return '<i class="icon-hot"></i>';
        }
        return '';
    }
    
    public function avatar() {
        if(isset($this->item['user']['avatar_uri'])) {
            return $this->item['user']['avatar_uri'];
        }
        return Configure::read('DefaultImage.Icon.User');
    }
    
    public function nickname() {
        if(isset($this->item['user']['nickname'])) {
            return $this->item['user']['nickname'];
        }
        return '无主之地';
    }
}