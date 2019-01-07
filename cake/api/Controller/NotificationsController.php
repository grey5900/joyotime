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

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/notifications",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 *
 * @SWG\Model(
 *   id="Notification",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="mergable",type="int"),
 *     @SWG\Property(name="message",type="string",required="true"),
 *     @SWG\Property(name="template",type="string"),
 *     @SWG\Property(name="merged",type="int"),
 *     @SWG\Property(name="link",type="string",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 *
 * @SWG\Model(
 *   id="Notifications",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int",required="true"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Notification",required="true")
 *   )
 * )
 *
 * @SWG\Model(
 *   id="NotificationCount",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int",required="true")
 *   )
 * )
 */
APP::uses('AppController', 'Controller');
/**
 * @package		app.Controller
 */
class NotificationsController extends AppController {
    
    public $name = 'Notifications';
    
    public $uses = array(
    	'Notification', 
    	'NotificationCounter',
    	'User'
    );
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/notifications.{format}",
 *   description="Get notifications for someone",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get notifications for someone",
 *       notes="",
 *       responseClass="Notifications",
 *       nickname="index",
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
 *           name="user_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="The current page number",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           defaultValue="1",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           defaultValue="20",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="lastime",
 *           description="It's timestamp",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
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
    public function index($userId = '') {
    	// Start to process broadcasting events...
//     	$this->broadcasting();
    	
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $conditions = array(
            'user_id' => $userId
        );
        
        // conditions of lastime...
        $lastime = $this->request->query('lastime');
        if($lastime) {
            $lastime = $this->getLastime(intval($this->request->query['lastime']) + 1);
            if(!$lastime) {
                return $this->fail(400, __('Invalid lastime'));
            }
        	$conditions = array_merge($conditions, array('modified' => 
        	    array('$gt' => $lastime)
        	));
        }
        
        // Read the latest notifications
        $items = $this->Notification->find('all', array(
    		'conditions' => $conditions,
            'order' => array('modified' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        
        // Set mark of `isread` to true for each notifications are found this time
        $this->setRead($items);
        $this->NotificationCounter->clean($userId);
        
        $total = $this->Notification->find('count', array(
        	'conditions' => $conditions,
        ));
        
        return $this->results(Hash::extract($items, '{n}.Notification'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/notifications/new_messages_total.{format}",
 *   description="Return count number of new messages",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Return count number of new messages",
 *       notes="",
 *       responseClass="NotificationCount",
 *       nickname="pull",
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
 *           name="user_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function pull($userId = '') {
        $total = $this->NotificationCounter->count($userId);
        if(!$total) $total = 0;
        return $this->result(array(
            'total' => (int)$total
        ));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/notifications.{format}",
 *   description="Send a notification to user by admin",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Send a notification to user by admin",
 *       notes="",
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
 *           name="user_id",
 *           description="The id of user who receive message",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="message",
 *           description="Something admin wanna tell user",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_add() {
        $userId = $this->request->data('user_id');
        if(!$userId || !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        $message = $this->request->data('message');
        if(empty($message)) {
        	return $this->fail(400, __('Invalid message'));
        }
        
        $data = array(
            'message' => $message,
            'user_id' => $userId
        );
        
        CakeResque::enqueue('notification', 'NotificationShell',
            array('broadcast', $data)
        );
        
        return $this->success();
    }
    
/**
 * Set mark of `isread` to true for each notifications are found this time.
 * 
 * @param items
 * @return boolean The result for this update
 */
	 private function setRead($items) {
		$ids = Hash::extract($items, '{n}.Notification._id');
        foreach($ids as &$id) {
            $id = new MongoId($id);
        }
        return $this->Notification->updateAll(
        		array('isread' => 1),
        		array('_id' => array('$in' => $ids)));
	}

    
/**
 * Get an instance of mongoDate
 * 
 * @param string $lastime
 * @return MongoDate|boolean
 */
    private function getLastime($lastime) {
        try {
            return new MongoDate($lastime);
        } catch(Exception $e) {
            return false;
        }
    }
}