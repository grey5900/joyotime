<?php
App::uses('AppHelper', 'View/Helper');
App::uses('Receipt', 'Model');
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

class CommentHelper extends AppHelper {
    
    public $helpers = array('Html', 'User', 'Time', 'Voice');
    
    private $row = array();
    
    /**
     * @param array $row
     * @return UserHelper
     */
    public function init(array $row = array()) {
    	$this->row = $row;
    	$this->User->init($row['user']);
    	if(isset($row['voice'])) {
    	    $this->Voice->init($row['voice']);
    	}
    	return $this;
    }
    
    public function id() {
        return $this->get($this->row, '_id', '');
    }
    
    public function score() {
        $score = $this->get($this->row, 'score', 0);
        return round($score, 0);
    }
    
    public function content() {
        return $this->get($this->row, 'content', '');
    }
    
    public function created() {
        $created = $this->get($this->row, 'created', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
    
    public function modified() {
        $created = $this->get($this->row, 'modified', array());
        if($created && isset($created['sec'])) {
            return strftime('%Y-%m-%d %H:%M:%S', $created['sec']);
        }
        return '';
    }
    
    public function hidden() {
        $hide = $this->get($this->row, 'hide', NULL);
        if($hide) {
            return $this->hiddenMark();
        }
        return '';
    }
    
    public function hideLink() {
        $hide = $this->get($this->row, 'hide', NULL);
        if(!$hide) {
            return $this->Html->link(__('屏蔽'), "#comment-hide-modal", array(
                'data-url' => "/comments/hide/".$this->id(),
                'class' => 'comment-hide-link',
                'data-toggle' => "modal",
            ));
        }
        return '';
    }
    
    public function hiddenMark() {
        return '<span class="hidden-item isError mr5">'.__('已屏蔽').'</span>';
    }
}