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
APP::uses('Voice', 'Model');
APP::uses('AppModel', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/voices",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Location",
 *   @SWG\Properties(
 *     @SWG\Property(name="lat",type="float",required="true"),
 *     @SWG\Property(name="lng",type="float",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Cover",
 *   @SWG\Properties(
 *     @SWG\Property(name="source",type="string",description="The url of source image is stored on QiNiu.com",required="true"),
 *     @SWG\Property(name="x80",type="string",description="The url of thumbnail image is stored on QiNiu.com",required="false"),
 *     @SWG\Property(name="x160",type="string",description="The url of thumbnail image is stored on QiNiu.com",required="false"),
 *     @SWG\Property(name="x400",type="string",description="The url of thumbnail image is stored on QiNiu.com",required="false")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Voice",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="user",type="Array",items="$ref:User",required="true"),
 *     @SWG\Property(name="title",type="string",required="true"),
 *     @SWG\Property(name="cover",type="Array",items="$ref:Cover",required="true"),
 *     @SWG\Property(name="length",type="int",required="true"),
 *     @SWG\Property(name="trial_voice",type="string",required="true"),
 *     @SWG\Property(name="voice",type="string",required="true"),
 *     @SWG\Property(name="status",type="string",required="true"),
 *     @SWG\Property(name="isfree",type="boolean",required="true"),
 *     @SWG\Property(name="location",type="Array",items="$ref:Location",required="true"),
 *     @SWG\Property(name="language",type="string",required="true"),
 *     @SWG\Property(name="score",type="double",required="true"),
 *     @SWG\Property(name="bought",type="boolean",required="true"),
 *     @SWG\Property(name="checkout_total",type="int",required="true"),
 *     @SWG\Property(name="comment_total",type="int",required="true"),
 *     @SWG\Property(name="earn_total",type="int",required="true"),
 *     @SWG\Property(name="address",type="string",required="true"),
 *     @SWG\Property(name="address_components",type="string",required="true"),
 *     @SWG\Property(name="deleted",type="int",required="false"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Voices",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Voice")
 *   )
 * )
 */

/**
 * The class is used to CRUD for voice.
 *
 * @package		app.Controller
 */
class VoicesController extends AppController {
    
    public $name = 'Voices';
    
    public $uses = array('Voice', 'User', 'Follow', 
        'Purchased', 'SyncQueue', 'VoiceIndex', 'Package');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->OAuth->allow($this->name, 'index');
        $this->OAuth->allow($this->name, 'view');
        
        // Register all callbacks for this controller...
        $evt = $this->Voice->getEventManager();
        $evt->attach($this->User);
        $evt->attach($this->Follow);
        $evt->attach($this->Package);
        $evt->attach(new \Model\Index\Voice());
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/voices.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Post a voice",
 *       notes="",
 *       responseClass="Voice",
 *       nickname="add",
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
 *           description="The id of user who is the owner of voice",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="The subject of voice",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="It's file key got from qiniu.com",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="length",
 *           description="The length of voice, unit is second.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="voice",
 *           description="It's file key got from qiniu.com",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="tags",
 *           description="seperate by comma",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="The initial status for the vocie. 0:pending/1:approved/2:invalid/3:unavailable",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           defaultValue="0",
 *           @SWG\AllowableValues(valueType="LIST", values="[0, 1, 2, 3]"),
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="cover_offset_y",
 *           description="Doulbe, .2, offset by left top",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="0.25",
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="isfree",
 *           description="Dose it free? 0: not free, 1: It's free.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="address",
 *           description="Address for location",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="address_components",
 *           description="The components for detail address of voice, JSON string.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="created_from",
 *           description="Whether voice was created from web, the value is `contrib`, otherwise it's empty or not exist.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="`zh_CN` or `en_US`",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
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
    public function add($userId) {
        $data = $this->request->data;
        $data['user_id'] = $userId;
        if($this->Voice->create($data)) {
            $saved = $this->Voice->save();
            if($saved) {
            	$this->Patch->patchPath($saved['Voice']);
            	$this->Patch->patchUser($saved['Voice']);
            	$this->sync($saved['Voice']);
            	return $this->result($saved['Voice']);
            }
        }
        return $this->fail(400, $this->errorMsg($this->Voice));
    }
    
    private function sync(&$saved) {
        $this->SyncQueue->enqueue(array('type' => 'cover', 'url' => $saved['cover']['source']));
        $this->SyncQueue->enqueue(array('type' => 'voice', 'url' => $saved['voice']));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/voices/{voice_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Update a voice",
 *       notes="",
 *       responseClass="Voice",
 *       nickname="edit",
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
 *           description="The id of user who is the owner of voice",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="The id of voice",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="The subject of voice",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="tags",
 *           description="It is seperated by comma, e.g. one, two, three. 
 *           The max number is 5 for each voice",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="It is a square picture as voice cover, there are four different size of images will be scaled by server automatically, such as 80x80, 160x160, 640x640 and source size in respectively.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="file"
 *         ),
 *         @SWG\Parameter(
 *           name="length",
 *           description="The length of voice, unit is second.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="voice",
 *           description="Voice file.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="file"
 *         ),
 *         @SWG\Parameter(
 *           name="tags",
 *           description="seperate by comma",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="The initial status for the vocie. 0:pending/1:approved/2:invalid/3:unavailable",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="0",
 *           @SWG\AllowableValues(valueType="LIST", values="[0, 1, 2, 3]"),
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="isfree",
 *           description="Dose it free? 0 is not, 1 is yes.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="0",
 *           @SWG\AllowableValues(valueType="LIST", values="[0, 1]"),
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="address",
 *           description="The address of location",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="address_components",
 *           description="The components for detail address of voice, JSON string.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="The voice belongs to which language.",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="zh_CN",
 *           @SWG\AllowableValues(valueType="LIST", values="['zh_CN', 'en_US']"),
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="recommend",
 *           description="Recommend from chief editor, 1: recommend it, 0: not yet",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="403",
 *            reason="Permission denied"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function edit($userId, $voiceId) {
		$this->Voice->id = $voiceId;
		$row = ($row = $this->Voice->findById($voiceId)) ? $row['Voice'] : array();
		if(!$row) return $this->fail(400, __('Invalid voice'));
		
		// Check user permission....
		$cred = $this->OAuth->getCredential();
		if(!$cred->isSame($row['user_id']) && !$cred->isAdmin()) 
		    return $this->fail(403, __('Permission denied'));
		
		$saved = $this->Voice->save($this->request->data);
		$this->sync($saved['Voice']);
		return ($saved)
		    ? $this->success() 
		    : $this->fail(400, $this->errorMsg($this->Voice));
    }
    
/**
 * @SWG\Api(
 *   path="/voices.{format}",
 *   description="Search voices near by a location",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Search voices near by a location",
 *       notes="",
 *       responseClass="Voices",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="The id of user who is the owner of voice",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="Search criteria for title",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="tags",
 *           description="more than one tag seperated by comma",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="radius",
 *           description="unit: kilometer",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="0:pending/1:approved/2:invalid/3:unavailable",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="owner",
 *           description="The id of user, server will find out whether the user bought these voices or not",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_agent",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="recommend",
 *           description="1: recommended, 0: not yet",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="radius",
 *           description="unit: kilometers",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="sort",
 *           description="`expert`, `hot`, `approved` and `status_modified`, default is `modified` if no sort specified.",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
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
 *           description="The limit number for each page, default is 20",
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
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function index() {
        $conditions = $order = array();
        $point = null;
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = is_numeric($page) ? intval($page) : 1;
        $limit = is_numeric($limit) ? intval($limit) : 20;
        
        // Save query criteria history
        $hist = new \Model\Query\Criteria\History();
        $solr = new \Model\Query\Solr\Voice();
        
        $solr->setPage($page, $limit);
        $solr->available();
        $solr->setRadius($this->request->query('radius'));
        $solr->author($this->request->query('user_id'));
        $solr->language($this->request->query('language'));
        $solr->status($this->request->query('status'));
        $solr->title($this->request->query('title'));
        $solr->tags($this->request->query('tags'));
        $solr->bySort($this->request->query('sort'));
        if(isset($this->request->query['recommend'])) 
            $solr->recommend($this->request->query['recommend']);
        
        $latitude = $this->request->query('latitude');
        $longitude = $this->request->query('longitude');
        
        if($latitude && $longitude) {
        	$point = new \Model\Data\Point($longitude , $latitude);
        	$solr->geo($point);
        }
        
        $resultset = $solr->getResultSet();
        
        if($point) $hist->add($this->request->query('title'), $point);
        else $hist->add($this->request->query('title'));
        
        $voices = array();
        $geodist = 'geodist()';
        foreach($resultset as $doc) {
            $item = $this->Voice->findById($doc->_id);
            if($item) {
                $item['Voice']['distance'] = round($doc->$geodist * 1000, 1);
                $voices[] = $item['Voice'];
            }
        }
        
        $boughtVoiceIds = array();
        $owner = $this->request->query('owner');
        if($owner) {
            $boughtVoiceIds = $this->Purchased->voices($owner);
        }
        
        foreach($voices as &$voice) {
            $this->Patch->patchBought($voice, $boughtVoiceIds, $owner);
            $this->Patch->patchPath($voice);
            $this->Patch->patchUser($voice);
            $this->Patch->patchPrice($voice);
        }
        
        // $this->httpCache(true, time() + 3600);
        return $this->results($voices, $resultset->getNumFound());
    }
    
/**
 * @SWG\Api(
 *   path="/voices/{voice_id}.{format}",
 *   description="Get a voice",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get a voice",
 *       notes="",
 *       responseClass="Voice",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Please use a standard format id of mongo.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="owner",
 *           description="The id of user, server will find out whether the user bought these voices or not",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="404",
 *            reason="No Found"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function view($voiceId) {
        $owner = $this->request->query('owner');
        
        $boughtVoiceIds = array();
        if($owner) {
        	$boughtVoiceIds = $this->Purchased->voices($owner);
        }
        
        if(strlen($voiceId) > 6) { // Whether it is a short id
            $voice = $this->Voice->findById($voiceId);
        } else {
            // It is short id...
            $voice = $this->Voice->findByShortId($voiceId);
        }
        
        if($voice && isset($voice['Voice'])) {
        	$voice = $voice['Voice'];
            $this->Patch->patchBought($voice, $boughtVoiceIds, $owner);
            $this->Patch->patchPath($voice);
            $this->Patch->patchUser($voice);
            $this->Patch->patchPrice($voice);
            return $this->result($voice);
        } 
        return $this->fail(404);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/voices/{voice_id}.{format}",
 *   description="Remove a voice",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove a voice",
 *       notes="",
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
 *           name="user_id",
 *           description="The id of user who is the owner of voice",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Please use a standard format id of mongo.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function delete($userId, $voiceId) {
        $conditions = array();
        $credential = $this->OAuth->getCredential();
        
        if($credential->isAdmin()) {
            $conditions = array(
                '_id' => new MongoId($voiceId)
            );
        } else {
            $conditions = array(
                '_id' => new MongoId($voiceId),
                'user_id' => $credential->getUserId()
            );
        }
        
        $result = $this->Voice->updateAll(array(
            '$set' => array('deleted' => 1)
        ), $conditions);
        
        $this->Voice->read(null, $voiceId);
        $this->Voice->getEventManager()->dispatch(
                new CakeEvent('Model.afterDelete', $this->Voice));
        
        return ($result) ? $this->success() : $this->fail(500);
    }
}