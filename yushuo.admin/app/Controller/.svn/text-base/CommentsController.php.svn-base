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
App::uses('Comment', 'Model');
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package		app.Controller
 */
class CommentsController extends AppController
{
    public $name = 'Comments';
    
    public $layout = 'fishsaying';
    
    public $components = array(
        'CommentApi' => array(
            'className' => 'FishSayingApi.Comment'
        )
    );
    
    public $uses = array('Comment');
    
    public function index() {
        $limit = 20;
        $items = array();
        $total = 0;
        $query = array(
            'hide' => 'all',
            'page' => $this->request->query('page'),
            'limit' => $limit
        );
        
        $this->paginate = array(
    		'limit' => $limit,
    		'paramType' => "querystring",
        );
        $responser = $this->CommentApi->index($query);
        if(!$responser->isFail()) {
            $receipts = $responser->getData();
            $this->Comment->results = $receipts['items'];
            $this->Comment->count = $total = $receipts['total'];
            $items = $this->paginate('Comment');
        }
        $this->set('items', $items);
        $this->set('total', $total);
    }
    
    public function hide($id) {
        $this->autoLayout = false;
        $this->autoRender = false;
		$responser = $this->CommentApi->delete($id);
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