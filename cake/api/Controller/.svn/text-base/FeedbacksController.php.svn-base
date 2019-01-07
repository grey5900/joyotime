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
APP::uses('Feedback', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/feedbacks",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Feedback",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="user",type="Array", items="$ref:User",required="true"),
 *     @SWG\Property(name="content",type="string",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Feedbacks",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Feedback")
 *   )
 * )
 */

/**
 * The class is used to CRUD reports.
 *
 * @package		app.Controller
 */
class FeedbacksController extends AppController {
    
    public $name = 'Feedbacks';
    
    public $uses = array('Feedback', 'User');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'add');
    }
    
/**
 * @SWG\Api(
 *   path="/feedbacks.{format}",
 *   description="Post a feedback",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Post a feedback",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="add",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="Id of current user",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="content",
 *           description="The content of feedback",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="contact",
 *           description="The contact of user",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Internal Server Error"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function add() {
        $id = NULL;    // feedback id
        
        // merge user information...
        $this->mergeUserInfo();
        // merge user agent...
        $this->mergeUserAgent();
        
        if(!$this->Feedback->enqueue($this->request->data)) {
            return $this->fail(500, __('Saving feedback fails'));
        }
        return $this->success();
    }
    
    private function mergeUserInfo() {
        if(TRUE == ($userId = $this->request->data('user_id'))) {
        	if(FALSE == ($user = $this->User->getById($userId))) {
        		return $this->fail(400, __('Invalid user id'));
        	}
        	// merge user email
        	$this->request->data = array_merge(
        			$this->request->data,
        			array('email' => $user['User']['email']));
        	// merge user name
        	$this->request->data = array_merge(
        			$this->request->data,
        			array('username' => $user['User']['username']));
        }
    }
    
    private function mergeUserAgent() {
        $this->request->data = array_merge(
        		$this->request->data,
        		array('user_agent' => $this->getUserAgent()));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/feedbacks/{feedback_id}.{format}",
 *   description="Change status for feedbacks",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Change status for feedbacks",
 *       notes="Just only for administrator",
 *       responseClass="SuccessResponse",
 *       nickname="admin_edit",
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
 *           name="feedback_id",
 *           description="Id of feedback",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 1000 is pending, 1001 is processed",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           @SWG\AllowableValues(valueType="LIST", values="['1000', '1001']"),
 *           defaultValue="1000",
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid feedback id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Fail to update the report"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_edit($feedbackId = '') {
        $saved = $this->Feedback->save(array(
            '_id' => $feedbackId,
            'status' => $this->request->data('status')
        ));
        
        if($saved) {
            return $this->success();
        }
        return $this->fail(500, $this->errorMsg($this->Feedback));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/feedbacks/{feedback_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove a feedback",
 *       notes="Just only for administrator",
 *       responseClass="SuccessResponse",
 *       nickname="delete",
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
 *           name="feedback_id",
 *           description="The id of feedbacks",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid feedback id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Fail to delete"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_delete($feedbackId = '') {
        if($this->Feedback->delete($feedbackId)) {
            return $this->success();
        }
        
        return $this->fail(500, __('Deletion fails'));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/feedbacks.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get feedbacks",
 *       notes="Just only for administrator",
 *       responseClass="Feedbacks",
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
 *           description="Id of current user",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 1000 is pending, 1001 is processed",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           @SWG\AllowableValues(valueType="LIST", values="['1000', '1001']"),
 *           defaultValue="1000",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="The current page number, default is 1",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="1",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="The limitation number for each page, default is 20",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="20",
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid user id"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_index() {
        $userId = $this->request->query('user_id');
        if($userId && !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        $conditions = array();
        
        if(isset($this->request->query['status'])) {
            $conditions = array_merge($conditions, array('status' => $this->request->query('status')));
        }
        if($userId) {
            $conditions = array_merge($conditions, array('user_id' => $userId));
        }
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $rows = $this->Feedback->find('all', array(
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        if($rows) {
            foreach($rows as &$row) {
            	$this->Patch->patchUser($row['Feedback']);
            }
        }
        $total = $this->Feedback->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($rows, '{n}.Feedback'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/feedbacks/{feedback_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View feedback",
 *       notes="Just only for administrator",
 *       responseClass="Feedback",
 *       nickname="view",
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
 *           name="feedback_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid report id"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_view($feedbackId = '') {
        if(!$feedbackId || !$this->isMongoId($feedbackId)) {
        	return $this->fail(400, __('Invalid feedback id'));
        }
        
        $row = $this->Feedback->findById($feedbackId);
        
        if(isset($row['Feedback'])) {
            $this->Patch->patchUser($row['Feedback']);
            return $this->result($row['Feedback']);
        }
        return $this->result();
    }
}