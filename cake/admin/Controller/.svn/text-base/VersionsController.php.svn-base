<?php
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
APP::uses('AppController', 'Controller');

/**
 * @package     app.Controller
 */
class VersionsController extends AppController {
    
    public $name = 'Versions';
    
    public $layout = 'fishsaying';
    
    public $components = array('VersionApi' => array(
        'className' => 'FishSayingApi.Version'
    ));
    
    public $uses = array('Version');
    
    public function add() {
        if($this->request->is('POST')) {
            $responser = $this->VersionApi->add($this->request->data['Version']);
            if(!$responser->isFail()) {
                $this->Session->setFlash(__('提交成功'));
                return $this->redirect('/versions/index/'.$this->request->data['Version']['platform']);
            } else {
                $this->Session->setFlash(__('提交失败, '.$responser->getMessage()));
            }
        }
        $this->set('platform', '');
    }
    
    public function index($platform = '') {
        $limit = 20;
        $items = array();
        $total = 0;
        
        $query = array(
            'limit' => $limit,
            'page' => $this->request->query('page')
        );
        if($platform) $query['platform'] = $platform;
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->VersionApi->index($query);
        if(!$responser->isFail()) {
        	$result = $responser->getData();
        	$this->Version->results = $result['items'];
        	$this->Version->count = $result['total'];
        	$items = $this->paginate('Version');
        	$items = is_array($items) ? $items : array();
        	$total = $result['total'];
        } else {
        	$this->Session->setFlash($responser->getMessage());
        }
        
        $this->set('platform', $platform);
        $this->set('items', $items);
        $this->set('total', $total);
    }
}
