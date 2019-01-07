<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @package       app.Model
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('AppController', 'Controller');

/**
 * The controller is used to control and handle upload progress.
 *
 * @package       app.Controller
 */
class UploadController extends AppController
{
    public $name = 'Upload';
    /**
     * The instance of class UploadComponent
     * 
     * @var UploadComponent
     */
    public $uploader;
    
    public $autoLayout = false;
    public $autoRender = false;
    
    public $uses = array();
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
        parent::beforeFilter();
    }
    
/**
 * Control cover uploading
 * @return void
 */
    public function cover() {
        $this->uploader = $this->Components->load('ImageAttachment', array('options' => array(
            'script_url' => '/upload/cover',
    		'upload_dir' => Configure::read('Upload.Cover.upload_dir').DS.$this->Auth->user('id').DS,
    		'upload_url' => Configure::read('Upload.Cover.upload_url').'/'.$this->Auth->user('id').'/',
        )));
        $this->uploader->handle();
    }
    
/**
 * Control figure uploading
 * Image file from ueditor
 * @return void
 */
    public function figure() {
        $this->uploader = $this->Components->load('FigureAttachment', array('options' => array(
            'script_url' => '/upload/figure',
    		'upload_dir' => Configure::read('Upload.Figure.upload_dir').DS.$this->Auth->user('id').DS,
    		'upload_url' => Configure::read('Upload.Figure.upload_url').'/'.$this->Auth->user('id').'/',
        )));
        $this->uploader->handle();
    }
}