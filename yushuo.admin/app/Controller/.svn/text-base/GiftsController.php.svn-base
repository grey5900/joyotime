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
class GiftsController extends AppController {
    
    public $name = 'Gifts';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'BroadcastApi' => array(
        	'className' => 'FishSayingApi.Broadcast'
        )
    );
    
    public $uses = array('Gift');
    
    public function add() {
        
    }
    
    public function send() {
        if($this->request->is('POST')) {
            $this->request->data['Gift']['seconds'] = $this->request->data['Gift']['seconds'] * 60;
            $responser = $this->BroadcastApi->add($this->request->data['Gift']);
            if(!$responser->isFail()) {
                $this->Session->setFlash(__('赠送成功'));
            } else {
                $this->Session->setFlash($responser->getMessage());
            }
            return $this->redirect('/gifts/index');
        }
    }
    
    public function index() {
        $limit = 20;
        $items = array();
        $total = 0;
        
        $query = array(
            'type' => BroadcastComponent::TYPE_GIFT,
            'limit' => $limit,
            'page' => isset($this->request->query['page']) ? $this->request->query('page') : 1,
        );
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->BroadcastApi->index($query);
        if(!$responser->isFail()) {
        	$result = $responser->getData();
        	$this->Gift->results = $result['items'];
        	$this->Gift->count = $result['total'];
        	$items = $this->paginate('Gift');
        	$items = is_array($items) ? $items : array();
        	$total = $result['total'];
        } else {
        	$this->Session->setFlash($responser->getMessage());
        }
        
        $this->set('items', $items);
        $this->set('total', $total);
    }
}
