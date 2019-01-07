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
App::uses('QiNiu', 'Controller/Component');
require_once VENDORS.'wideimage/lib/WideImage.php';

/**
 * @package		app.Controller
 */
class TranscodesController extends AppController
{
    public $name = 'Transcodes';
    
    public $layout = 'fishsaying';
    
    public $components = array();
    
    public $uses = array('Transcode');

/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }

    public function index() {
        $items = $this->Transcode->find();
        $total = count($items);
        
        $this->set('items', $items);
        $this->set('total', $total);
    }
    
    public function retry($id = '') {
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