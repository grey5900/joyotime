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
APP::uses('Report', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/reports",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Report",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="user",type="Array", items="$ref:User",required="true"),
 *     @SWG\Property(name="voice_id",type="string",required="true"),
 *     @SWG\Property(name="voice",type="Array", items="$ref:Voice",required="true"),
 *     @SWG\Property(name="content",type="string",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Reports",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Report")
 *   )
 * )
 */

/**
 * The class is used to CRUD reports.
 *
 * @package		app.Controller
 */
class ReportsController extends AppController {
    
    public $name = 'Reports';
    
    public $uses = array('Report', 'Voice');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'index');
    	$this->OAuth->allow($this->name, 'add');
    	$this->OAuth->allow($this->name, 'delete');
    	$this->OAuth->allow($this->name, 'edit');
    	$this->OAuth->allow($this->name, 'view');
    }
    
    
/**
 * @SWG\Api(
 *   path="/reports.{format}",
 *   description="Post a report",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Post a report",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="add",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="Id of current user",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Id of voice",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="content",
 *           description="The content of report",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 0 is pending, 1 is processed",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           defaultValue="0",
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid content"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid user id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid voice id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Fail to create report"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function add() {
        $userId = $this->request->data('user_id');
        $voiceId = $this->request->data('voice_id');
        $content = $this->request->data('content');
        
        if(!$content) {
            return $this->fail(400, __('Invalid content'));
        }
        
        if(!$userId || !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        
        if(!$voiceId || !$this->isMongoId($voiceId)) {
            return $this->fail(400, __('Invalid voice id'));
        }
        
        $status = $this->request->data('status');
        $status = $this->getStatus($status);
        
        // initial data
        $data = array(
    		'user_id' => $userId,
    		'voice_id' => $voiceId,
    		'content' => $content,
    		'status' => $status,
        );
        
        $id = '';    // report id
        if($this->Report->create($data)) {
            $saved = $this->Report->save();
            if($saved && isset($saved['Report']['_id'])) {
            	$id = (string) $saved['Report']['_id'];
            }
        }
        
        if($id) {
            return $this->success(201, array("Location: /reports/$id"), array('_id' => $id));
        }
        return $this->fail(500, $this->errorMsg($this->Report));
    }
    
/**
 * @SWG\Api(
 *   path="/reports/{report_id}.{format}",
 *   description="Change status for report",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Change status for report",
 *       notes="",
 *       responseClass="Report",
 *       nickname="edit",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="report_id",
 *           description="Id of report",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 0 is pending, 1 is processed",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid report id"
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
    public function edit($reportId = '') {
        if(!$reportId || !$this->isMongoId($reportId)) {
        	return $this->fail(400, __('Invalid report id'));
        }
        
        $status = $this->request->data('status');
        $status = $this->getStatus($status);
        
        $updated = $this->Report->updateAll(array(
                '$set' => array('status' => $status)
            ), array(
    		    '_id' => new MongoId($reportId),
            )
        );
        
        if($updated) {
            $row = $this->Report->findById($reportId);
            if($row && isset($row['Report'])) {
                return $this->result($row['Report']);
            }
        }
        return $this->fail(500, __('Fail to update the report'));
    }
    
/**
 * Return a valid status of report
 * 
 * @param int $status
 * @return number
 */
    private function getStatus($status) {
        return intval($status) > 0 ? Report::STATUS_DONE : Report::STATUS_PENDING;
    } 

/**
 * @SWG\Api(
 *   path="/reports/{report_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove a report",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="delete",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="report_id",
 *           description="The id of report",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 0 is pending, 1 is processed",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid report id"
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
    public function delete($reportId = '') {
        if(!$reportId || !$this->isMongoId($reportId)) {
            return $this->fail(400, __('Invalid report id'));
        }
        
        $status = $this->request->query('status');
        $status = $this->getStatus($status);
        
        $result = $this->Report->delete($reportId);
        if($result) {
            return $this->success();
        }
        
        return $this->fail(500, __('Deletion fails'));
    }
    
/**
 * @SWG\Api(
 *   path="/reports.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get reports",
 *       notes="",
 *       responseClass="Reports",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="Id of current user",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Id of voice",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="Two kinds of status: 0 is pending, 1 is processed",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
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
    public function index() {
        $userId = $this->request->query('user_id');
        if($userId && !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        $voiceId = $this->request->query('voice_id');
        if($voiceId && !$this->isMongoId($voiceId)) {
            return $this->fail(400, __('Invalid voice id'));
        }
        
        $conditions = array();
        
        if(isset($this->request->query['status'])) {
            $status = $this->request->query('status');
            $conditions = array_merge($conditions, array('status' => $this->getStatus($status)));
        }
        if($userId) {
            $conditions = array_merge($conditions, array('user_id' => $userId));
        }
        if($voiceId) {
            $conditions = array_merge($conditions, array('voice_id' => $voiceId));
        }
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $rows = $this->Report->find('all', array(
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        if($rows) {
            foreach($rows as &$row) {
            	$voiceId = $row['Report']['voice_id'];
            	$item = $this->Voice->findById($voiceId);
            	if(isset($item['Voice'])) {
            		$this->Patch->patchPath($item['Voice']);
            		$this->Patch->patchUser($item['Voice']);
            		$row['Report']['voice'] = $item['Voice'];
            	}
            	$this->Patch->patchUser($row['Report']);
            }
        }
        $total = $this->Report->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($rows, '{n}.Report'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/reports/{report_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View report",
 *       notes="",
 *       responseClass="Report",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="report_id",
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
    public function view($reportId = '') {
        if(!$reportId || !$this->isMongoId($reportId)) {
        	return $this->fail(400, __('Invalid report id'));
        }
        
        $row = $this->Report->findById($reportId);
        
        if(isset($row['Report'])) {
            $voiceId = $row['Report']['voice_id'];
            $item = $this->Voice->findById($voiceId);
            if(isset($item['Voice'])) {
            	$this->Patch->patchPath($item['Voice']);
            	$this->Patch->patchUser($item['Voice']);
            	$row['Report']['voice'] = $item['Voice'];
            }
            $this->Patch->patchUser($row['Report']);
        }
        return $this->result($row['Report']);
    }
}