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
App::uses('Voice', 'Model');
App::uses('Transcode', 'Model');
App::uses('QiNiu', 'Controller/Component');
require_once VENDORS.'wideimage/lib/WideImage.php';

/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class VoicesController extends AppController
{
    public $name = 'Voices';
    
    public $layout = 'fishsaying';
    
    public $components = array('QiNiu', 
        'VoiceApi' => array(
            'className' => 'FishSayingApi.Voice'
        ),
        'ConnectApi' => array(
            'className' => 'FishSayingApi.Connect'
        ),
        'FileInfo',
        'TranscodeFile'
    );
    
    public $uses = array('Voice', 'Transcode');

/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
    public function test() {
        $this->autoLayout = false;
        $this->autoRender = false;
    }

    public function index() {
        $limit = 20;
        $items = array();
        $total = 0;
        
        $query = array(
            'user_id' => $this->Auth->user('_id'),
            'page' => $this->request->query('page'),
            'limit' => $limit
        );
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->VoiceApi->index($query);
        
        if(!$responser->isFail()) {
        	$voices = $responser->getData();
        	$this->Voice->results = $voices['items'];
        	$this->Voice->count = $total = $voices['total'];
        	$items = $this->paginate('Voice');
        }
        $this->set('items', $items);
        $this->set('total', $total);
        
        return $this->render('index');
    }
    
    public function create() {
        if($this->request->is('post')) {
        	try {
	            $voice = new \Model\Data\Raw\Voice($this->request->data('voices'));
	            if($voice->cover->has()) {
	            	$voice->cover->crop($voice->crop);
	            	$voice->cover->upload();
	            }
	            if($voice->voice->has()) {
	            	if($voice->voice->available()) {
	            		if($voice->voice->encoding()) {
	            			$this->response(__('文件正在服务器端转码...'), '/transcodes');
	            		} 
	            	} 
	            	$voice->voice->upload();
	            }
	            
	            $responser = $this->VoiceApi->add($voice->toArray());
	            if($responser->isFail()) {
	            	$this->failResponse($responser);
	            } else {
	            	$this->response(__('保存成功'));
	            }
        	} catch(\Model\Exception\Upload $e) {
        		$this->response($e->getMessage(), $e->getUrl());
        	}
        }
    }
    
    public function add() {
        if($this->request->is('post')) {
            $data = $this->request->data('voices');
            $token = $this->ConnectApi->token();
            
            if($this->hasCover($data)) {
                $this->cropCover($data);
            } else {
                $data['cover'] = '';
            }

            $this->chkAddress($data);

            // Just put it into encoding queue...
            if($this->encode($data)) {
            	$this->response(__('文件正在服务器端转码...'), '/transcodes');
            }
            
            if(!$this->isMP4($data)) {
                $this->response(__('保存失败，文件必须为mp3/mp4/wma'));
            }
            
            $this->uploadVoice($data);

            $responser = $this->VoiceApi->add($data);
            if($responser->isFail()) {
                $this->failResponse($responser);
            } else {
                $this->response(__('保存成功'));
            }
        }
        
        $this->set('required', true);
        $this->set('coverDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_COVER));
        $this->set('voiceDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_VOICE));
        $this->set('token', $this->ConnectApi->token());
        $this->set('data_input_status', 'add');
        return $this->render('add');
    }
    
    /**
     * @param ResponserComponent $responser
     */
	private function failResponse(ResponserComponent $responser) {
		$this->Session->setFlash(__('保存失败').$responser->getMessage(), 'flash', array(
		    'class' => 'alert-danger' 
		));
	}

    
/**
 * Upload voice file to file server
 * 
 * @param data
 */
	private function uploadVoice(&$data) {
		$data['voice'] = $this->QiNiu->upload(
		    CakeSession::read('Api.Token.uptoken.voice'), 
		    $data['voice']['tmp_name']
		);
	}
    
/**
 * Check field of `address`
 * 
 * @param data
 */
	private function chkAddress(&$data) {
		if(!isset($data['address'])) {
			$data['address'] = '未知地区';
		}
	}

    
/**
 * Crop cover if need
 * 
 * @param data
 * @param image
 * @param cropped
 * @return void
 */
	private function cropCover(&$data) {
		$image = WideImage::load($data['cover']['tmp_name']);
		$cropped = $image->crop(
				$data['crop']['left'],
				$data['crop']['top'],
				$data['crop']['width'],
				$data['crop']['height']);
	    $data['cover']['tmp_name'] .= '.jpg'; 
		$cropped->saveToFile($data['cover']['tmp_name']);
		$this->uploadCover($data);
	}
	
	/**
	 * @param data
	 */
	private function hasCover($data) {
		return isset($data['cover']['error']) 
		    && $data['cover']['error'] == 0 
		    && isset($data['crop']['left'])
		    && is_numeric($data['crop']['left']);
	}

	
	/**
	 * Upload cover to file server
	 * 
	 * @param data
	 */
	private function uploadCover(&$data) {
		$data['cover'] = $this->QiNiu->upload(
			CakeSession::read('Api.Token.uptoken.cover'),
			$data['cover']['tmp_name']
		);
	}
    
	/**
	 * @param data
	 */
	private function encode($data) {
	    if(!$this->hasVoice($data)) {
	        return false;
	    }
	    $tmpName = $data['voice']['tmp_name'];
		if($this->FileInfo->isMP3($tmpName)) {
		    $data['transcode']['uniqid'] = uniqid('transcode');
		    $data['transcode']['user_id'] = $this->Auth->user('_id');
		    $data['transcode']['auth_token'] = CakeSession::read('auth_token');
		    $data['transcode']['status'] = Transcode::STATUS_PENDING;
		    $data['transcode']['created'] = time();
		    $data['transcode']['modified'] = time();
		    $data['voice']['tmp_name'] = $this->TranscodeFile->move($tmpName);
		    $this->Transcode->save($data);
		    CakeResque::enqueue('encodings', 'VoiceShell',
		        array('encode', $data)
		    );
		    return true;
		}
		return false;
	}
    
	/**
	 * @param data
	 */
	private function isMP4($data) {
	    if(!$this->hasVoice($data)) {
	        return false;
	    }
	    $tmpName = $data['voice']['tmp_name'];
		return $this->FileInfo->isMP4($tmpName);
	}
	
	/**
	 * @param data
	 */
	private function hasVoice(&$data) {
		return isset($data['voice']['tmp_name']) && !empty($data['voice']['tmp_name']);
	}

	
	/**
	 * @param string $uri
	 * @return void
	 */
	private function response($message, $uri = '/voices') {
		$this->Session->setFlash($message,'flash');
		$this->redirect($uri);
	}
    
    public function edit($id = '') {
        if($this->request->is('post')) {
            $token = $this->ConnectApi->token();
            $voice = $this->request->data('voices');
            $voice['_id'] = $id;
            
            $this->hasAddressComponent($voice);
            // Set voice status to pending...
            $voice['status'] = Voice::STATUS_PENDING;
            
            if($this->hasCover($voice)) {
                $this->cropCover($voice);
            } else {
                unset($voice['cover']);
            }
            
            if($this->encode($voice)) {
            	$this->response(__('文件正在服务器端转码...'), '/transcodes');
            }
            
            if($this->hasVoice($voice) && !$this->isMP4($voice)) {
            	$this->response(__('保存失败，文件必须为mp3/mp4/wma'));
            }
            
            if($this->isUploadVoice($voice)) {
                $this->uploadVoice($voice);
            } else {
                unset($voice['voice']);
            }
            
            $responser = $this->VoiceApi->edit($id, $voice);
            if($responser->isFail()) {
            	$this->Session->setFlash(__('保存失败').$responser->getMessage(), 'flash', array(
                    'class' => 'alert-danger' 
                ));
            } else {
            	$this->Session->setFlash(__('保存成功'));
            	$this->redirect('/voices');
            }
        }
        $responser = $this->VoiceApi->view($id);
        if(!$responser->isFail()) {
            $voice = $responser->getData();
            $this->request->data['voices'] = $voice;
            $this->request->data['voices']['latitude'] = $voice['location']['lat'];
            $this->request->data['voices']['longitude'] = $voice['location']['lng'];
            $this->request->data['voices']['address_components'] = '';
        }
        $this->set('coverDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_COVER));
        $this->set('voiceDownload', $this->QiNiu->getDomain(QiNiuComponent::BUCKET_VOICE));
        $this->set('required', false);
        $this->set('data_input_status', 'edit');
        $this->set('token', $this->ConnectApi->token());
        return $this->render('add');
    }
    
	/**
	 * Check whether there is address component or not
	 * 
	 * it will unset an empty `address_component`
	 * 
	 * @param voice
	 */
	private function hasAddressComponent(&$voice) {
		if(isset($voice['address_components']) && empty($voice['address_components'])) {
			unset($voice['address_components']);
		}
	}

    
	/**
	 * Check whether voice uploaded or not
	 * 
	 * @param voice
	 */
	private function isUploadVoice($voice) {
		return isset($voice['voice']['error']) && $voice['voice']['error'] == 0;
	}


    public function remove($id = '') {
        $this->autoLayout = false;
        $this->autoRender = false;
        $responser = $this->VoiceApi->delete($id);
        if($responser->isFail()) {
            return json_encode(array(
                'message' => __('解说删除失败'),
                'result' => true
            ));
        } else {
            return json_encode(array(
        		'message' => __('解说删除成功'),
        		'result' => false
            ));
        }
    }
}