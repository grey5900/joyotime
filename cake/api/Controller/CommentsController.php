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
APP::uses('Checkout', 'Model');
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/comments",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Comment",
 *   @SWG\Properties(
 *     @SWG\Property(name="voice_id",type="string",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="user",type="Array",items="$ref:User"),
 *     @SWG\Property(name="content",type="string",required="true"),
 *     @SWG\Property(name="score",type="int",required="true"),
 *     @SWG\Property(name="hide",type="boolean",description="It shouldn't be displayed on client-side if hide is true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Comments",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Comment")
 *   )
 * )
 */

/**
 * The class is used to CRUD comment for voice.
 *
 * @package		app.Controller
 */
class CommentsController extends AppController {
    
    public $name = 'Comments';
    
    public $uses = array('Comment', 'Voice', 'Checkout');
    
    public function beforeFilter() {
        parent::beforeFilter();
        
        $this->OAuth->allow($this->name, 'view');
        $this->OAuth->allow($this->name, 'index');
        
        // Register all callbacks for this controller...
        $this->Comment->getEventManager()->attach($this->Voice);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/comments.{format}",
 *   description="Post a comment",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Post a comment",
 *       notes="Only user who is bought the voice, can post comment",
 *       responseClass="Comment",
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
 *           description="Please use a standard mongo Id format.",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="Please use a standard mongo Id format.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="score",
 *           description="user remark for the voice, range 1 ~ 5, integer.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="content",
 *           description="max length of chinese character is 140.",
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
    public function add($userId = '') {
        $data = $this->request->data;
        $data['user_id'] = $userId;
        if(!isset($data['voice_id'])) {
        	return $this->fail(400, __('Invalid voice id'));
        }
        
        $voice = $this->Voice->findById($data['voice_id']);
        if(!$voice) {
        	return $this->fail(400, __('Invalid voice id'));
        }
        
        // check whether the user has permit to comment the voice...
        if(!$this->isCommentable($data['user_id'], $voice)) {
            return $this->fail(403, __('No grant for comment'));
        }
        // Get comment if user has commented
        $comment = $this->isExist($data['user_id'], $data['voice_id']);
        
        $data = array(
            'user_id' => $data['user_id'],
            'voice_id' => $data['voice_id'],
            'content' => $data['content'],
            'score' => $data['score'],
            'hide' => false,
        	'voice_title' => $voice['Voice']['title'],
        	'voice_user_id' => $voice['Voice']['user_id'],
        );
        
        // Just only update if comment has existed.
        if($comment && isset($comment['Comment']['_id'])) {
            $data = array_merge($data, array('_id' => new MongoId((string)$comment['Comment']['_id'])));
        }
        
        $saved = $this->Comment->save($data);
        if($saved) {
            $this->Patch->patchUser($saved['Comment']);
            $saved['Comment']['_id'] = (string) $saved['Comment']['_id'];
        	return $this->result($saved['Comment']);
        }
        return $this->fail(400, $this->errorMsg($this->Comment));
    }
    
/**
 * @SWG\Api(
 *   path="/comments/{comment_id}.{format}",
 *   description="Delete a comment",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Delete a comment",
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
 *           name="comment_id",
 *           description="The id of comment you want to remove",
 *           paramType="path",
 *           required="true",
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
    public function delete($id) {
        $conditions = array();
        $credential = $this->OAuth->getCredential();
        if($credential->isAdmin()) {
            $result = $this->Comment->delete($id);
        } else {
            $userId = $credential->getUserId();
            $result = $this->Comment->deleteByAuthor($id, $userId);
        }
        return $result ? $this->success() : $this->fail(500);
    }
    
/**
 * @SWG\Api(
 *   path="/comments.{format}",
 *   description="Get comments",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get comments",
 *       notes="Get comments",
 *       responseClass="Comments",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="hide",
 *           description="`yes`: show hidden items only;<br />`no`: show non-hidden items only;<br />`all`: show all items including hidden and non-hidden ones.<br />(Default is `no`)",
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
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function index($voiceId = '') {
        if(!$this->request->is('GET')) {
            return $this->fail(405);
        }
        if(!$voiceId) {
            $voiceId = $this->request->query('voice_id');
        }
        $userId = $this->request->query('user_id');
        $hide = $this->request->query('hide');
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        
        // filter hide first...
        $conditions = array();
        
        if($hide) {
            switch(strtolower($hide)) {
                case 'yes': 
                    $conditions['hide'] = true;
                    break;
                case 'no':
                    $conditions['hide'] = false;
                    break;
            }
        } else {
            $conditions['hide'] = false;
        }
        
        if($voiceId) {
            $conditions['voice_id'] = $voiceId;
        }
        if($userId) {
            $conditions['user_id'] = $userId;
        }
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $comments = $this->Comment->find('all', array(
            'conditions' => $conditions,
            'order' => array('modified' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        
        foreach($comments as &$comment) {
            $this->Patch->patchUser($comment['Comment']);
            $this->Patch->patchVoice($comment['Comment']);
        }
        
        $total = $this->Comment->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($comments, '{n}.Comment'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/comments/{comment_id}.{format}",
 *   description="View a comment content",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View a comment content.",
 *       notes="",
 *       responseClass="Comment",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="comment_id",
 *           description="The id of comment",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Response"
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
    public function view($id = '') {
        if(!$this->request->is('GET')) {
        	return $this->fail(405);
        }
        
        if(!$id || !$this->isMongoId($id)) {
            return $this->fail(400);
        }
        $conditions = array(
            '_id' => new MongoId($id),
        );
        $comment = $this->Comment->find('first', array(
            'conditions' => $conditions
        ));
        if($comment) {
            $this->Patch->patchUser($comment['Comment']);
            return $this->result($comment['Comment']);
        }
        return $this->fail(404);
    }
    
/**
 * Check the user has grant to comment voice
 *
 * @param string $userId            
 * @param array $voice
 * @return boolean
 */
    private function isCommentable($userId, $voice = array()) {
        if($voice && isset($voice['Voice']['isfree']) && isset($voice['Voice']['length'])) {
            if($voice['Voice']['isfree'])
                return true;
            if($voice['Voice']['length'] <= 30)
                return true;
        }
        return $this->Checkout->find('count', array(
            'conditions' => array(
                'type' => Checkout::TYPE_VOICE_COST,
                'user_id' => $userId,
                'voice_id' => $voice['Voice']['_id'] 
            ) 
        )) > 0;
    }

/**
 * Check whether the user has commented already.
 *
 * @param string $userId            
 * @param string $voiceId            
 * @return boolean array comment id will return if existed.
 */
    private function isExist($userId, $voiceId) {
        $row = $this->Comment->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                'voice_id' => $voiceId,
                'hide' => false 
            ) 
        ));
        if($row && isset($row['Comment']['_id'])) {
            return $row;
        }
        return false;
    }
}