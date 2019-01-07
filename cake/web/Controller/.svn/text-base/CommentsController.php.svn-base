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
App::uses ( 'AppController', 'Controller' );
App::uses ( 'Comment', 'Model' );
/**
 * FishSaying Controller
 *
 * Handle events posted from fishsaying server.
 *
 * @package app.Controller
 */
class CommentsController extends AppController {
	public $name = 'Comments';
	public $layout = 'fishsaying';
	public $components = array (
			'CommentApi' => array (
					'className' => 'FishSayingApi.Comment' 
			) 
	);
	public $uses = array (
			'Comment' 
	);
	/**
	 * comment lists for voice
	 */
	public function getList() {
		$this->autoLayout = false;
		$this->autoRender = false;
		if ($this->request->is ( 'post' )) {
			$voice_id = $this->request->data ( 'voice_id' );
			if (! $voice_id) {
				echo json_encode ( array (
						'result' => false,
						'message' => 'forbidden' 
				) );
				exit;
			}
			$limit = 20;
			$items = array ();
			$total = 0;
			$query = array ();
			$query ['voice_id'] = $voice_id;
			$query ['limit'] = $limit;
			$query ['page'] = $this->request->data( 'page' );
			$this->paginate = array (
					'limit' => $limit,
					'paramType' => "querystring" 
			);
			//print_r($query);exit;
			$responser = $this->CommentApi->index ( $query );
			if ($responser->isFail ()) {
				echo json_encode ( array (
						'result' => false,
						'message' => __ ( 'The operation is to fail' ) . $responser->getMessage () 
				) );
			} else {
				$comments = $responser->getData ();
				foreach ($comments['items'] as $key=>$val){
					$comments['items'][$key]['modified']['sec'] = date('Y-m-d H:i:s',$val['modified']['sec']);
				}
				echo json_encode ( array (
						'result' => true,
						'data' => $comments 
				) );
			}
		}else{
			echo json_encode ( array (
					'message' => 'forbidden'
			) );
		}
		
	}
}