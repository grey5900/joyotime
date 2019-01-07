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
 *  resourcePath="/favorites",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Favorite",
 *   @SWG\Properties(
 *     @SWG\Property(name="title",type="string"),
 *     @SWG\Property(name="size",type="int"),
 *     @SWG\Property(name="user_id",type="string"),
 *     @SWG\Property(name="isdefault",type="int"),
 *     @SWG\Property(name="thumbnail",type="Array",items="$ref:Covers"),
 *     @SWG\Property(name="created",type="Date"),
 *     @SWG\Property(name="modified",type="Date")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Covers",
 *   @SWG\Properties(
 *     @SWG\Property(name="items",type="Array", items="$ref:Cover")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Favorites",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Favorite")
 *   )
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class FavoritesController extends AppController {
    
    public $name = 'Favorites';
    
    public $uses = array('Favorite', 'Voice', 'Purchased', 'Package');
    
    public $components = array();
    
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	 
    	// Register all callbacks for this controller...
    	$this->Favorite->getEventManager()->attach($this->Package);
    }
    
/**
 * @SWG\Api(
 *   path="/favorites.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create favorite",
 *       notes="",
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
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="The title of favorite",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="package_id",
 *           description="",
 *           paramType="form",
 *           required="false",
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
    public function add() {
        $fav = $this->Favorite->save(am($this->request->data, 
                array('user_id' => $this->OAuth->getCredential()->getUserId())));
        if(isset($fav['Favorite']['_id'])) {
            return $this->success(201, 
                array("Location: /favorites/".$fav['Favorite']['_id']), 
                array('_id' => $fav['Favorite']['_id']));
        } 
        return $this->fail(400, $this->errorMsg($this->Favorite));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites/{favorite_id}/voices.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Push voice into favorite",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="push",
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
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="favorite_id",
 *           description="Id of favorite",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="The id of voice file",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid favorite id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid voice id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="409",
 *            reason="The voice already exists in favorite"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function push($userId, $favoriteId) {
        $userId = $this->OAuth->getCredential()->getUserId();
        if(!$this->permission($favoriteId)) {
        	return false;
        }
        
        $voiceId = $this->request->data('voice_id');
        if(!$voiceId || !$this->isMongoId($voiceId)) {
            return $this->fail(400, __('Invalid voice id'));
        }
        
        $voice = $this->Voice->findById($voiceId);
        if(!$voice || !isset($voice['Voice']['status'])) {
            return $this->fail(400, __('Invalid voice id'));
        }
        
        if($voice['Voice']['status'] != Voice::STATUS_APPROVED) {
            return $this->fail(409, __('The voice has been pulled off shelf'));
        }
        
        $favorite = $this->Favorite->findById($favoriteId);
        if(!$favorite || !isset($favorite['Favorite'])) {
            
            return $this->fail(400, __('Invalid favorite id'));
        }
        
        if(!isset($favorite['Favorite']['thumbnail']) 
            || (isset($favorite['Favorite']['thumbnail']) && count($favorite['Favorite']['thumbnail']) < 1)) {
            $voice = $this->Voice->findById($voiceId);
            if(!$voice || !isset($voice['Voice'])) {
            	return $this->fail(400, __('Invalid voice id'));
            }
            $updated = $this->Favorite->push($favoriteId, $voiceId, $voice['Voice']['cover']);
        } else {
            $updated = $this->Favorite->push($favoriteId, $voiceId);
        }
        
        if($updated) {
            $favorite = $this->Favorite->findById($favoriteId);
            if($favorite && isset($favorite['Favorite'])) {
                $this->Patch->patchThumbnailPath($favorite['Favorite']);
                return $this->result($favorite['Favorite']);
            }
        }
        return $this->fail(409, __('The voice already exists in favorite'));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites/{favorite_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Modify favorite title",
 *       notes="",
 *       responseClass="SuccessResponse",
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
 *           description="Id of current user",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="favorite_id",
 *           description="Id of favorite",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="The id of voice file",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid favorite id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid voice id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="409",
 *            reason="The voice already exists in favorite"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function edit($userId = '', $favoriteId = '') {
        $title = $this->request->data('title');
        if(!$title) {
            return $this->fail(400, __('Invalid title'));
        }
        
        if(!$favoriteId || !$this->isMongoId($favoriteId)) {
        	return $this->fail(400, __('Invalid favorite id'));
        }
        
        if(!$this->permission($favoriteId)) {
            return false;
        }
        
        $result = $this->Favorite->updateAll(array('$set' => array(
            'title' => $title
        )), array(
            '_id' => new MongoId($favoriteId),
            'isdefault' => array('$ne' => 1),
        ));
        
        if(!$result) {
            return $this->fail(400, __('Favorites updating fails'));
        }
        return $this->success();
    }
        
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites/{favorite_id}/voices.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove voice from favorite",
 *       notes="",
 *       responseClass="SuccessResponse",
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
 *           description="Id of current user",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="favorite_id",
 *           description="The id of favorite",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Allow multiple values seperated by comma",
 *           paramType="query",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid favorite id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid voice id"
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
    public function pull($userId = '', $favoriteId = '') {
        if(!$userId || !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        if(!$favoriteId || !$this->isMongoId($favoriteId)) {
            return $this->fail(400, __('Invalid favorite id'));
        }
        
        if(!$this->permission($favoriteId)) {
        	return false;
        }
        
        $voiceIds = $this->request->query('voice_id');
        if(!$voiceIds) return $this->fail(400, __('Invalid voice id'));
        $voices = explode(',', $voiceIds);
        foreach($voices as &$voice) {
        	$voice = trim($voice);
        	if(!$this->isMongoId($voice)) $this->fail(400, __('Invalid voice id'));
        	if(!$this->Favorite->pull($favoriteId, $voice)) $this->fail(500, __('Deletion fails'));
        }
        return $this->success();
    }
        
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites/{favorite_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove whole favorite",
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
 *           description="Id of current user",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="favorite_id",
 *           description="The id of favorite",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid favorite id"
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
    public function delete($userId = '', $favoriteId = '') {
        if(!$userId || !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        }
        if(!$favoriteId || !$this->isMongoId($favoriteId)) {
            return $this->fail(400, __('Invalid favorite id'));
        }
        
        if(!$this->permission($favoriteId)) {
            return false;
        }
        
        $result = FALSE;
        if(!$this->Favorite->isDefault($favoriteId)) {
            $result = $this->Favorite->delete($favoriteId);
        }
        if($result) {
            return $this->success();
        }
        
        return $this->fail(500, __('Deletion fails'));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get favorites of someone",
 *       notes="",
 *       responseClass="Favorites",
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
 *           paramType="path",
 *           required="true",
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
    public function index($userId) {
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $favors = $this->Favorite->find('all', array(
            'conditions' => array('user_id' => $userId),
            'order' => array('created' => 'asc'),
            'page' => $page,
            'limit' => $limit
        ));
        if(is_array($favors)) {
            foreach($favors as &$fav) {
                $this->Patch->patchThumbnailPath($fav['Favorite']);
            }
        }
        $total = $this->Favorite->find('count', array(
            'conditions' => array('user_id' => $userId)
        ));
        return $this->results(Hash::extract($favors, '{n}.Favorite'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/favorites/{favorite_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View voices within a favorite",
 *       notes="",
 *       responseClass="FavoriteItems",
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
 *           name="user_id",
 *           description="Id of current user",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="favorite_id",
 *           description="Id of favorite",
 *           paramType="path",
 *           required="true",
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
 *            reason="Invalid favorite id"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function view($userId, $favoriteId) {
        if(!$this->permission($favoriteId)) {
        	return false;
        }
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $voices = array();
        $favor = $this->Favorite->getVoices($favoriteId, $page, $limit);
        
        $boughtVoiceIds = array();
        $owner = $userId;
        if($owner) {
        	$boughtVoiceIds = $this->Purchased->voices($owner);
        }
        
        if(isset($favor['Favorite']['voices'])) {
            foreach($favor['Favorite']['voices'] as $voiceId) {
                $item = $this->Voice->findById($voiceId);
                if(isset($item['Voice'])) {
                    $this->Patch->patchBought($item['Voice'], $boughtVoiceIds, $owner);
                    $this->Patch->patchPath($item['Voice']);
                    $this->Patch->patchUser($item['Voice']);
                    $this->Patch->patchPrice($item['Voice']);
                    $voices[] = $item['Voice'];
                }
            }
        }
        $total = isset($favor['Favorite']['size']) ? $favor['Favorite']['size'] : 0;
        return $this->results($voices, $total);
    }
    
    private function permission($favoriteId) {
        $favorite = $this->Favorite->findById($favoriteId);
        if(!isset($favorite['Favorite']['user_id'])) {
        	return $this->fail(400, __('Invalid favorite id'));
        }
        
        //Check permissions for access/write resource requesting...
        if($this->OAuth->getCredential()->getUserId() != $favorite['Favorite']['user_id']) {
            return $this->fail(403);
        }
        
        return true;
    }
}