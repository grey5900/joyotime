<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Voice', 'Model');
require_once(VENDORS."emoji/emoji.php");
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
 * @copyright     Copyright (c) fishsaying.com. (http://www.fishsaying.com)
 * @link          http://fishsaying.com FishSaying(tm) Project
 * @since         FishSaying(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class ReportHelper extends AppHelper {
    
    public $helpers = array('Html', 'Voice', 'User');
    
    private $row = array();
    
    const STATUS_PENDING = 0;
    const STATUS_DONE = 1;
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
    	$this->row = $row;
    	$this->Voice->init($row['voice']);
    	$this->User->init($row['user']);
    	return $this;
    }
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function status($status = false) {
        if(!is_numeric($status) && !$status) {
            $status = (int) $this->get($this->row, 'status', 0);
        }
        switch($status) {
            case self::STATUS_DONE:
                return '<span class="done-color">'.__('已处理').'</span>';
                break;
            default:
            	return $this->Html->link(__('未处理'), "javascript:void(0);", array(
            			'data-url' => "/reports/done/".$this->id(),
            			'class' => 'report-pending-link',
            			'data-loading-text' => __('正在处理中...')
            	));
        }
    }
    
    public function content($substr = false) {
        $limit = 40;
        $content = $this->get($this->row, 'content', '');
        if($content) {
            $content = emoji_unified_to_html($content);
        }
        if($substr) {
            $len = mb_strlen($content);
            if($len > $limit) {
                $content = mb_substr($content, 0, $limit).'...';
            }
        }
        return $content;
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
}