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
 *  resourcePath="/follows",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Follow",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="follower_id",type="string",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Follows",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int",required="true"),
 *     @SWG\Property(name="items",type="Array", items="$ref:User",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="FollowCount",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int",required="true")
 *   )
 * )
 */

/**
 * The class is used to CRUD for follow.
 *
 * @package		app.Controller
 */
class FollowsController extends AppController {
    
    public $name = 'Follows';
    
    public $components = array('RequestHandler');
    
    public function beforeFilter() {
        parent::beforeFilter();
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/follows.{format}",
 *   description="Build a relationship with one user to another.",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Build a relationship with one user to another.",
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
 *           description="The id of user who is current user logged-in already.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="follower_id",
 *           description="The id of user who is followed.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid user id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid follower id"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Don't allow follow yourself"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function edit($userId) {
        $data = $this->request->data;
        $data['user_id'] = $this->OAuth->getCredential()->getUserId();
        
        // Create relationship ...
        if($this->Follow->create($data) && TRUE == ($saved = $this->Follow->save())) {
            $id = $saved['Follow']['_id'];
        	
            return $this->success(201,
            		array("Location: /users/$userId/follows/$id"),
            		array('_id' => $id));
        }
        
        return $this->fail(400, $this->errorMsg($this->Follow));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/follows.{format}",
 *   description="Get relationship of followings by someone",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get relationship of followings by someone",
 *       notes="",
 *       responseClass="Follows",
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
 *           description="please use standard format mongo id.",
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
    public function index($userId) {
        $limit = $this->request->query('limit');
        $limit = $limit ? $limit : 20;
        
        $follows = $this->Follow->find('all', array(
            'conditions' => array('user_id' => $userId),
            'order' => array(
                'new_posts' => 'desc',
                'modified' => 'desc',
            ),
            'page' => $this->request->query('page'),
            'limit' => $limit
        ));
        $users = array();
        foreach($follows as &$follow) {
            $item = array('user_id' => $follow['Follow']['follower_id']);
            $this->Patch->patchUser($item);
            if(isset($item['user'])) {
                $users[] = array_merge($item['user'], array('new_posts' => $follow['Follow']['new_posts']));
            }
        }
        
        $total = $this->Follow->find('count', array(
            'conditions' => array('user_id' => $userId)
        ));
        return $this->results($users, $total);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/follows/{follower_id}.{format}",
 *   description="View relationship between user and follower.",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View relationship between user and follower",
 *       notes="",
 *       responseClass="Follow",
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
 *           description="please use standard format mongo id.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="follower_id",
 *           description="please use standard format mongo id.",
 *           paramType="path",
 *           required="true",
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
    public function view($userId, $followerId) {
        $follow = $this->Follow->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'follower_id' => $followerId
            )
        ));
        
        if($follow && isset($follow['Follow']['new_posts'])) {
            // Resort set after reset the new posts to 0
            if($follow['Follow']['new_posts'] > 0) {
                $this->Follow->resetNewPosts($userId, $followerId);
            }
            
            return $this->result(array(
                'user_id' => $userId,
                'follower_id' => $followerId,
                'new_posts' => $follow['Follow']['new_posts']
            ));
        }
        return $this->fail(404);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/follows/{follower_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Remove relationship between user and follower",
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
 *           description="please use standard format mongo id.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="follower_id",
 *           description="please use standard format mongo id.",
 *           paramType="path",
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
    public function delete($userId, $followerId) {
        $result = $this->Follow->deleteAll(array(
            'user_id' => $userId,
            'follower_id' => $followerId
        ));
        if($result) {
            return $this->success();
        }
        return $this->fail(400);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/follows/new_posts.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get count of new posts which is posted by following of current user",
 *       notes="",
 *       responseClass="FollowCount",
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
 *           description="please use standard format mongo id.",
 *           paramType="path",
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
    public function pull() {
        $userId = $this->OAuth->getCredential()->getUserId();
        if(!$userId) {
            return $this->fail(401);
        }
        $count = (int)$this->Follow->countNewPosts($userId);
        return $this->result(array(
        	'total' => $count
        ));
    }
}