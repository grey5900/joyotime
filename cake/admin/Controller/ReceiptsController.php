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
App::uses('AppController', 'Controller');
App::uses('Receipt', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class ReceiptsController extends AppController
{
    public $name = 'Receipts';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'ReceiptApi' => array(
            'className' => 'FishSayingApi.Receipt'
        )
    );
    
    public $uses = array('Receipt');
    
    public function index() {
        $limit = 20;
        $items = array();
        $total = 0;
        $query = array(
            'status' => Receipt::STATUS_PAID.', '.Receipt::STATUS_PRICE_EXCEPTION,
            'page' => $this->request->query('page'),
            'limit' => $limit
        );
        
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->ReceiptApi->index($query);
        if(!$responser->isFail()) {
            $receipts = $responser->getData();
            $this->Receipt->results = $receipts['items'];
            $this->Receipt->count = $total = $receipts['total'];
            $items = $this->paginate('Receipt');
        }
        $this->set('items', $items);
        $this->set('total', $total);
    }
}