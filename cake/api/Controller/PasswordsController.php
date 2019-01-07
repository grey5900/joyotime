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
APP::uses('CredentialItem', 'Item');
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/passwords",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * @package		app.Controller
 */
class PasswordsController extends AppController {
    
    public $name = 'Passwords';
    
    public $uses = array('User', 'AuthorizeToken');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'reset_add');
    	$this->OAuth->allow($this->name, 'reset_edit');
    }
    
/**
 * @SWG\Api(
 *   path="/passwords/reset.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       responseClass="SuccessResponse",
 *       summary="密码重置",
 *       nickname="reset_add",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="email",
 *           description="注册邮箱",
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
    public function reset_add() {
        $queue = new \Model\Queue\Password\Reset();
        if($queue->enqueue(array('email' => $this->request->data('email')))) {
            return $this->success(200);
        }
        return $this->fail(400, $this->errorMsg($queue));
	}
	
/**
 * @SWG\Api(
 *   path="/passwords/reset.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       responseClass="SuccessResponse",
 *       summary="重置为新密码",
 *       nickname="reset_view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="email",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="expire",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="hash",
 *           description="Hash string",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="password",
 *           description="",
 *           paramType="form",
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
	public function reset_edit() {
	    $email  = $this->request->data('email');
	    $hash   = $this->request->data('hash');
	    $expire = $this->request->data('expire');
	     
	    $reset = new \Model\Queue\Password\Reset();
	    // Check whether URL is expired/invalid or not
	    if($expire >= time() && $hash == $reset->encode($email, $expire) && $reset->firstTime($hash)) {
    	    $user  = $this->User->findByEmail($email);
    	    $name  = $this->User->name;
    	    if(isset($user[$name]) && ($user = $user[$name])) {
        	    $update = array();
        	    $update['_id']      = $user['_id'];
        	    $update['password'] = $this->request->data('password');
        	    if($this->User->save($update)) {
        	        $reset->record($hash);
        	        return $this->success();
        	    }
        	    return $this->fail(400, $this->errorMsg($this->User));
    	    }
	    }
	    return $this->fail(400, __('Sorry, the resetting link has expired'));
	}
}