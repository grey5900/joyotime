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
 *  resourcePath="/users/recommend",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 */

/**
 * @package		app.Controller
 */
class RecommendUsersController extends AppController {
    
    public $name = 'Users';
    
    public $components = array('Param');
    
    public $uses = array(
        'User', 
        'Follow', 
        'RecommendUser'
    );
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'index');
    	
    	// Register all callbacks for this controller...
    	$this->User->getEventManager()->attach($this->RecommendUser);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/users/recommend/{user_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Set/cancel someone as recommend user, ordering recommend users",
 *       notes="",
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
 *           name="user_id",
 *           description="id of user who you want to modify",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="recommend",
 *           description="1 or 0",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="recommend_reason",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="recommend_offset",
 *           description="",
 *           paramType="form",
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
    public function admin_edit($userId) {
        $this->User->id = $userId;
        
        $fields = array(
            'recommend',
            'recommend_reason',
            'recommend_offset'
        );
        foreach($this->request->data as $key => $val)
            if(!in_array($key, $fields)) unset($this->request->data[$key]);
        
        if($this->User->save($this->request->data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->User));
    }

/**
 * @SWG\Api(
 *   path="/users/recommend.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get recommend users",
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
 *           description="default is 20",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           defaultValue="10",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function index() {
    	$users = array();
    	$uid = $this->OAuth->getCredential()->getUserId();
    	$total = 0;
    	
    	if($this->Param->language() != 'zh_CN') return $this->results(array(), 0);
    	
    	$recommends = $this->RecommendUser->find('all', array(
			'page'  => $this->request->query('page'),
			'limit' => $this->request->query('limit')?:20
    	));

    	foreach($recommends as $id) $users[] = $this->User->getById($id);
    	
    	if($users) {
    	    $follows = ($uid) ? $this->Follow->getFollowed($uid) : array();
    		foreach($users as &$user) {
    			$this->Patch->patchPath($user['User']);
    			$this->Patch->patchFollow($user['User'], $follows);
    		}
    		$total = $this->RecommendUser->find('count');
    	} 
    	return $this->results(Hash::extract($users, '{n}.User'), $total);
    }
}