<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Transcode', 'Model');

class TranscodeHelper extends AppHelper {
    
    public $helpers = array('Html');
    
    private $row = array();
    
    public function init(&$row = array()) {
        $this->row = $row;
    }
    
    public function status() {
        if($this->gets($this->row, 'transcode.status') == Transcode::STATUS_FAIL) {
            return __('转码失败...');
        } 
        return __('正在转码中...');
    }
    
    public function title() {
        return $this->gets($this->row, 'title', '');
    }
    
    public function length(&$row = array()) {
        $sec = intval($this->gets($this->row, 'length', 0));
        if($sec > 0) {
            return $sec.__('秒');
        }
        return '--';
    }
    
    public function created() {
    	return strftime('%Y-%m-%d %H:%M:%S', $this->gets($this->row, 'transcode.created', time()));
    }
    
    public function retry() {
        $id = $this->gets($this->row, 'transcode.uniqid');
        if($id) {
    	    // return $this->Html->link(__('重试'), "", array('class' => 'retry-link','data-toggle' => 'modal','data-remove' => '/transcodes/retry/'.$id));
        }
        return '';
    }
}