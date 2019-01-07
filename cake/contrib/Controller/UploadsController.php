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
App::uses('AppController', 'Controller');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class UploadsController extends AppController
{
    public $name = 'Uploads';
    
    public $autoLayout = false;
    public $autoRender = false;
    
    public $uses = false;
    
    public $components = array(
    	'ConnectApi' => array(
    		'className' => 'FishSayingApi.Connect'
    	)
    );
    
    public function beforeFilter() {
        $session = $this->request->data('session');
        if($session) {
        	CakeSession::id($session);
          	CakeSession::start();
        }
        parent::beforeFilter();
        $this->ConnectApi->token();
    }
    
    public function test() {
    	$this->render('test');
    }
    
    public function cover() {
        $resp  = new \Controller\Response\Upload\Cover();
       
        try {
            if(!isset($_FILES['cover']) || !is_array($_FILES['cover'])) {
            	throw new Exception(__('找不到上传数据'));
            }
            if(empty($_POST) || !is_array($_POST)) {
            	throw new Exception(__('找不到切图数据'));
            }
    		$cover = new \Model\Data\Upload\Cover($_FILES['cover']);
    		$crop  = new \Model\Data\Crop($_POST);
    		
    		if(!$cover->has()) throw new Exception(__('找不到上传的文件'));
			$cover->crop($crop);
			$file = $cover->upload();
			if(!$file) throw new Exception(__('上传文件失败'));
			return $resp->success($file);
        } catch(Exception $e) {
            return $resp->fail($e->getMessage());
        }
    }
    
    public function voice() {
        $resp  = new \Controller\Response\Upload\Voice();
       
        try {
            if(!isset($_FILES['Filedata']) || !is_array($_FILES['Filedata'])) {
                throw new Exception(__('找不到上传数据'));
            }
           
            $voice = new \Model\Data\Upload\Voice($_FILES['Filedata']);
            if(!$voice->has()) throw new Exception(__('找不到上传文件'));
            if(!$voice->available()) throw new Exception(__('请上传后缀名为m4a的文件'));
            $convert = new \Utility\Encode\Audio($voice, Configure::read('FFMPEG_BIN_PATH'));
            $len = $convert->duration();
            if($len < Configure::read('VOICE.DURATION.MIN') || $len > Configure::read('VOICE.DURATION.MAX')) {
                throw new Exception(__('请确保音频时长在1分钟到5分钟之间'));
            }
            if($convert->need() && $voice->move()) {
                $convert->encode();
            }
            return $resp->success($voice->save(), $len);
        } catch(Exception $e) {
            return $resp->fail($e->getMessage());
        }
    }
    
/**
 * Generate response message
 * 
 * @param mixed $file
 * @param string $message
 * @return string
 */
    private function resp($file, $message = '') {
    	$result = (bool) $file;
    	if($result) {
    		return json_encode(array('result' => $result, 'file' => $file));
    	} else {
    		return json_encode(array('result' => $result, 'message' => $message));
    	}
    }
}