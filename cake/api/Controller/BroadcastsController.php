<?php
use Swagger\Annotations as SWG;
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
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/broadcasts",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class BroadcastsController extends AppController {
    
    public $name = 'Broadcasts';
    
    public $uses = array(
        'Broadcast', 
        'User',
        'Checkout'
    );
    
/**
 * @SWG\Api(
 *   path="/broadcasts.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="<b>[Deprecated]</b> Check whether user has new broadcasting message or not 
 and put new messages into notification box",
 *       notes="Return always be success",
 *       responseClass="SuccessResponse",
 *       nickname="add",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function add() {
        return $this->success();
    }
    
/**
 * @SWG\Api(
 *   path="/admin/broadcasts.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Send message to everyone",
 *       notes="Just only for administrator",
 *       responseClass="SuccessResponse",
 *       nickname="admin_add",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="message",
 *           description="Something wanna tell user",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="seconds",
 *           description="unit sec, reward",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="link",
 *           description="Redirect link",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="The id of user who will receive it, no need to provide if you wanna send to everyone",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=true,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_add() {
        $userId = $this->request->data('user_id');
        $seconds = $this->request->data('seconds');
        $message = $this->request->data('message');
        
        if($this->Broadcast->create($this->request->data)) {
        	$saved = $this->Broadcast->save();
        	if($saved) {
        		if($userId  && $saved['Broadcast']['type'] == Broadcast::TYPE_GIFT) {
        			$this->Checkout->gift($userId, $seconds, $message);
        			$this->User->gift($userId, $seconds);
        		}
        		return $this->success(201);
        	}
        }
        return $this->fail(400, $this->errorMsg($this->Broadcast));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/broadcasts.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get histories of message broadcasting",
 *       notes="Just only for administrator",
 *       responseClass="BroadcastResponse",
 *       nickname="admin_index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="type",
 *           description="Broadcast type",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="Receiver's id",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_index() {
        $conditions = array();
        
        if($this->request->query('type')) {
            $conditions = array_merge($conditions, array(
                'type' => $this->request->query('type')
            ));
        }
        
        if($this->request->query('user_id')) {
            $conditions = array_merge($conditions, array(
                'user_id' => $this->request->query('user_id')
            ));
        }
        
        $items = $this->Broadcast->find('all', array(
    		'conditions' => $conditions,
    		'order' => array('created' => 'desc'),
    		'page' => $this->request->query('page'),
    		'limit' => $this->request->query('limit')
        ));
        
        $total = 0;
        if(count($items)) {
        	foreach($items as &$row) {
        		$this->Patch->patchUser($row['Broadcast']);
        	}
        	$total = $this->Broadcast->find('count', array(
        		'conditions' => $conditions
        	));
        }
        return $this->results(Hash::extract($items, '{n}.Broadcast'), $total);
    }
}