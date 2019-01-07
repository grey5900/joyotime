<?php
/**
 * The project of FishSaying is a SNS platform which is
 * based on voice sharing for each other with journey.
 *
 * The contributor platform is used to CP create/publish costomize content.
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
 * @since         FishSaying(tm) v 1.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
APP::uses('Component', 'Controller');
/**
 * @package app.Controller.Component
 */
class SubmitterComponent extends Component {
    
    public $components = array(
        'QiNiu',
        'VoiceApi' => array(
        	'className' => 'FishSayingApi.Voice'
        ),
        'ConnectApi' => array(
        	'className' => 'FishSayingApi.Connect'
        )
    );
    
    /**
     * The path of tmp file
     * 
     * @var string
     */
    private $tmp = '';
    
    /**
     * The info of transcode
     * 
     * @var array
     */
    private $transcode = array();
    
    public function initSession($voice) {
    	$this->ConnectApi->token();
    	CakeSession::write('Auth.User._id', $voice['transcode']['user_id']);
    	CakeSession::write('auth_token', $voice['transcode']['auth_token']);
    }
    
    /**
     * Upload voice file to file server
     *
     * @param data
     */
    public function upload(&$data) {
        try {
            $this->tmp = $data['voice']['tmp_name'];
        	$data['voice'] = $this->QiNiu->upload(
    			CakeSession::read('Api.Token.uptoken.voice'),
    			$data['voice']['tmp_name']
        	);
        	return true;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
    
/**
 * @param array $data
 * @return ResponserComponent
 */
    public function save(&$data) {
        $this->transcode = $data['transcode'];
        unset($data['transcode']);
        if(isset($data['_id']) && !empty($data['_id'])) {
            return $this->VoiceApi->edit($data['_id'], $data);
        } else {
            return $this->VoiceApi->add($data);
        }
    }
    
/**
 * Clean tmp files
 */
    public function clean() {
        if($this->tmp) {
            unlink($this->tmp);
        }
    }
    
    /**
     * Get uniqid
     * 
     * @return string
     */
    public function getUniqid() {
        if(isset($this->transcode['uniqid'])) {
            return $this->transcode['uniqid'];
        }
        return '';
    }
}