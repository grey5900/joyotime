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
APP::uses('Security', 'Utility');
APP::uses('Validation', 'Utility');
APP::uses('CredentialItem', 'Item');
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/users",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="User",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="username",type="string",required="true"),
 *     @SWG\Property(name="email",type="string",required="true"),
 *     @SWG\Property(name="avatar",type="Array",required="true",items="$ref:Avatar"),
 *     @SWG\Property(name="latest_voice_post",type="Date",required="true"),
 *     @SWG\Property(name="voice_total",type="int",required="true"),
 *     @SWG\Property(name="favorite_size",type="int",required="true"),
 *     @SWG\Property(name="purchase_total",type="int",required="true"),
 *     @SWG\Property(name="voice_length_total",type="int",required="true"),
 *     @SWG\Property(name="money",type="int",required="true"),
 *     @SWG\Property(name="cost",type="int",required="true"),
 *     @SWG\Property(name="earn",type="int",required="true"),
 *     @SWG\Property(name="income",type="int",required="true"),
 *     @SWG\Property(name="cash",type="float",required="true"),
 *     @SWG\Property(name="locale",type="string",required="true"),
 *     @SWG\Property(name="role",type="string",required="true"),
 *     @SWG\Property(name="device_code",type="Array",required="true"),
 *     @SWG\Property(name="is_contributor",type="int"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Avatar",
 *   @SWG\Properties(
 *     @SWG\Property(name="source",type="string",required="true"),
 *     @SWG\Property(name="x80",type="string",required="true"),
 *     @SWG\Property(name="x180",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Users",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int",required="true"),
 *     @SWG\Property(name="items",type="Array", items="$ref:User",required="true")
 *   )
 * )
 */

/**
 * The class is used to authorize and create account for each user.
 *
 * @package		app.Controller
 */
class UsersController extends AppController {
    
    public $name = 'Users';
    
    public $components = array('Param');
    
    public $uses = array(
        'User', 
        'Voice', 
        'Favorite', 
        'Follow', 
        'Checkout', 
        'AuthorizeToken',
        'SyncQueue',
    );
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'index');
    	$this->OAuth->allow($this->name, 'add');
    	$this->OAuth->allow($this->name, 'view');
    	
    	// Register all callbacks for this controller...
    	$this->User->getEventManager()->attach($this->Voice);
    	$this->User->getEventManager()->attach($this->Favorite);
    	$this->User->getEventManager()->attach($this->Checkout);
    	$this->User->getEventManager()->attach(new \Model\Index\User());
    	$this->User->getEventManager()->attach(new \Model\Queue\Welcome\Mail());
    }
    
/**
 * Create a new account
 *
 * @SWG\Api(
 *   path="/users.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create an new account.",
 *       notes="Create an new account.",
 *       responseClass="User",
 *       nickname="register",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="X-Device",
 *           description="The code of device got from client side",
 *           paramType="header",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="username",
 *           description="The username is unique.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="email",
 *           description="A valid email address",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="password",
 *           description="Password length between 6 - 12",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="avatar",
 *           description="The key got from QiNiu.com",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="role",
 *           description="The permission of user, available values included `admin` and `user`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="`zh_CN`, `zh_TW`, `en_US`",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="is_verified",
 *           description="0: not yet, 1: already verified",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="verified_description",
 *           description="Information of verified",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="certified",
 *           description="`sina_weibo`, `qzone`, `twitter` and `facebook`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="open_id",
 *           description="It's an unique identical got from third-party of certified",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="The avatar upload to fail"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Invalid device code"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="The registerion is to fail"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function add() {
		$this->initial($this->request->data);
		
		if (FALSE == ($user = $this->User->register($this->request->data))) {
			return $this->fail(400, $this->errorMsg($this->User));
		}
		$this->Patch->patchPath($user['User']);
		$this->Patch->patchCash($user['User']);
		$this->sync($user['User']);
		
		return $this->result(array(
			'auth_token' => $this->AuthorizeToken->add($this->getCredentialItem($user['User'])),
			'user' => $user['User']
		));
	}
	
/**
 * Get credential of user
 *
 * @param array $user
 * @return CredentialItem
 */
	private function getCredentialItem(&$user) {
		$item = new CredentialItem($user);
		return $item;
	}
    
/**
 * Initial all fields.
 * 
 * @param array $data
 */
    private function initial(array &$data = array()) {
        if($data) {
            $data['locale'] = $this->Param->language();
            $data['device_code'] = $this->Param->deviceCode();
        }
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Modify user profile.",
 *       notes="If you want to change password, the old password and password have to be suppiled at the same time.
<br /><h3>How to bind/unbind certified on user account?</h3>
<br />If you want to <b>bind</b> any certified on account, it simply provides `certified` 
and `open_id` at the same time. That's all. On the other hand, you can just provide `certified` and left empty 
with `open_id` to <b>unbind</b> specified `certified` from user account.",
 *       responseClass="User",
 *       nickname="profile",
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
 *           description="user id",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="username",
 *           description="new username",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="email",
 *           description="new email",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="avatar",
 *           description="The key got from QiNiu.com",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="old_password",
 *           description="The current password",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="password",
 *           description="The new password",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="is_contributor",
 *           description="It's flag for identicate whether user is contributor or not",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="certified",
 *           description="`sina_weibo`, `qzone`, `twitter` and `facebook`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="open_id",
 *           description="It's an unique identical got from third-party of certified",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="locale",
 *           description="`zh_CN`, `zh_TW` and `en_US`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="",
 *           paramType="form",
 *           required="false",
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
    public function edit($userId = '') {
        $userId = $this->OAuth->getCredential()->getUserId();
        $this->User->id = $userId;
        
        $fields = array(
            'username',
            'email',
            'password',
            'old_password',
            'avatar',
            'cover',
            'locale',
            'is_contributor',
            'certified',
            'open_id',
            'description'
        );
        foreach($this->request->data as $key => $val)
            if(!in_array($key, $fields)) unset($this->request->data[$key]);
        
        if($this->User->save($this->request->data)) {
            $user = $this->User->getById($userId);
            $this->Patch->patchPath($user['User']);
            $this->Patch->patchCash($user['User']);
            $this->sync($user['User']);
            return $this->result($user['User']);
        }
        return $this->fail(400, $this->errorMsg($this->User));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/users/{user_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Modify user profile.",
 *       notes="",
 *       responseClass="User",
 *       nickname="profile",
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
 *           description="user id",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="username",
 *           description="new username",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="email",
 *           description="new email",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="avatar",
 *           description="The key got from QiNiu.com",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="old_password",
 *           description="The current password",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="password",
 *           description="The new password",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="is_contributor",
 *           description="It's flag for identicate whether user is contributor or not",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="certified",
 *           description="`sina_weibo`, `qzone`, `twitter` and `facebook`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="open_id",
 *           description="It's an unique identical got from third-party of certified",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="locale",
 *           description="`zh_CN`, `zh_TW` and `en_US`",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="is_verified",
 *           description="0: not yet, 1: already verified",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="verified_description",
 *           description="Information of verified",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="belong_partner",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="",
 *           paramType="form",
 *           required="false",
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
    public function admin_edit($userId) {
        $this->User->id = $userId;
        
        $fields = array(
            'username',
            'email',
            'password',
            'old_password',
            'avatar',
            'cover',
            'locale',
            'is_contributor',
            'certified',
            'open_id',
            'is_verified',
            'verified_description',
            'belong_partner',
            'description'
        );
        foreach($this->request->data as $key => $val)
            if(!in_array($key, $fields)) unset($this->request->data[$key]);
        
        if($this->User->save($this->request->data)) {
            $user = $this->User->getById($userId);
            $this->Patch->patchPath($user['User']);
            $this->Patch->patchCash($user['User']);
            $this->sync($user['User']);
            return $this->result($user['User']);
        }
        return $this->fail(400, $this->errorMsg($this->User));
    }
    
    private function sync(&$user) {
        if(isset($user['avatar']['source'])) {
        	$this->SyncQueue->enqueue(
        			array('type' => 'avatar', 'url' => $user['avatar']['source']));
        }
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET", 
 *       summary="View an user profile.",
 *       notes="",
 *       responseClass="User",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="user id",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="404",
 *            reason="No Found"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function view($userId = '') {
        $user = $this->User->getById($userId);
        if(!$user) {
        	return $this->fail(404);
        }
        $user = $user['User'];
        $this->Patch->patchPath($user);
        $this->Patch->patchCash($user);
        return $this->result($user);
    }
    
/**
 * @SWG\Api(
 *   path="/users.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get index of users",
 *       notes="",
 *       responseClass="Users",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="default is 1",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           defaultValue="1",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="default is 10",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           defaultValue="10",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="username",
 *           description="The exactly name or a part of name",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="is_contributor",
 *           description="1: already, 0: not yet",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="is_verified",
 *           description="1: verified already, 0: not yet",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="sort",
 *           description="`voice_income`",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="except",
 *           description="The username that don't want to be found",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function index() {
    	$conditions = $order = $ordered = array();
    	$sort = '';
    	$username = $this->request->query('username');
    	$usernames = array();
    	$uid = $userId = $this->OAuth->getCredential()->getUserId();
    	 
    	if(!empty($username)) {
    		$except = $this->request->query('except');
    		$usernames = $this->getUserId($username);
    		$conditions['_id'] = array('$in' => $usernames);
    		if($except) $conditions['username']['$ne'] = $except;
    		$order = array();
    		$sort = 'username';
    	} else if($this->request->query('sort') == 'voice_income') {
    		$order = array('voice_income_total' => 'desc');
    		$conditions['voice_income_total'] = array('$gt' => 0);
    	} else $order = array('created' => 'desc');
    	
    	if(isset($this->request->query['is_verified'])) {
    		$conditions['is_verified'] = (int) $this->request->query['is_verified'];
    		$order = array('modified' => 'desc');
    	}
    	if(isset($this->request->query['is_contributor']))
    		$conditions['is_contributor'] = (int) $this->request->query['is_contributor'];
    
    	$users = $this->User->find('all', array(
			'fields' => array('password' => 0),
			'conditions' => $conditions,
			'order' => $order,
			'page'  => $this->request->query('page'),
			'limit' => $this->request->query('limit')?:20
    	));
    	
    	if($sort) {
    		$flip = array();
    		foreach($users as $user) $flip[$user['User']['_id']] = $user;
    		if($sort == 'username') foreach($usernames as $id)  if(isset($flip[$id])) $ordered[] = $flip[$id];
    	} else $ordered = &$users;
    	 
    	$total = 0;
    	if($ordered) {
    		$follows = ($uid) ? $this->Follow->getFollowed($uid) : array();
    		foreach($ordered as &$user) {
    			$this->Patch->patchPath($user['User']);
    			$this->Patch->patchFollow($user['User'], $follows);
    		}
    		$total = $this->User->find('count', array(
    			'conditions' => $conditions
    		));
    	}
    	return $this->results(Hash::extract($ordered, '{n}.User'), $total);
    }
    
    private function getUserId($name) {
    	$solr = new \Model\Query\Solr\User();
    	$solr->setPage(1, 10);
    	$solr->username($name);
    	$resultset = $solr->getResultSet();
    	$ids = array();
    	foreach($resultset as $doc) $ids[] = $doc->_id;
        return $ids;
    }
}