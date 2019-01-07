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
App::uses('Report', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class ReportsController extends AppController
{
    public $name = 'Reports';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'ReportApi' => array(
            'className' => 'FishSayingApi.Report'
        )
    );
    
    public $uses = array('Report');
    
    public function index() {
        $limit = 20;
        $items = array();
        $total = 0;
        $query = array();
        
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->ReportApi->index($query);
        if(!$responser->isFail()) {
            $voices = $responser->getData();
            $this->Report->results = $voices['items'];
            $this->Report->count = $total = $voices['total'];
            $items = $this->paginate('Report');
        }
        $this->set('items', $items);
        $this->set('total', $total);
    }
    
    public function done($id) {
        $this->autoLayout = false;
        $this->autoRender = false;
	    $data = array(
	        'status' => (int) Report::STATUS_DONE,
	    );
		$responser = $this->ReportApi->edit($id, $data);
		if($responser->isFail()) {
			return json_encode(array(
			    'result' => false, 
			    'message' => __('The operation is to fail').$responser->getMessage()));
		} else {
			return json_encode(array(
			    'result' => true, 
			    'message' => __('The operation has finished in successfully')));
		}
    }
}