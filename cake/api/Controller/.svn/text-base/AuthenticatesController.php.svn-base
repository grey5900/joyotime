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
 *  resourcePath="/authenticates",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * @package		app.Controller
 */
class AuthenticatesController extends AppController {
    
    public $name = 'Authenticates';
    
    public $uses = array('User', 'AuthorizeToken', 'Certification');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'authorize');
    }
    
/**
 * @SWG\Api(
 *   path="/authenticates.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Authorize the client has grant for operate resouces",
 *       notes="The method should access via HTTPS
 *       <br />The value of timestamp must be within an hour from now based on server timezone is UTC.
 *       <br />The secrect is genereated by md5(private_key+timestamp).
 *       <br />During the whole testing, please uses <b>DYhG93b0qyJfIxfs2guVoUubbwvniR2G0FgaC9mi</b> as private_key
 *        and it's a case-senstive.
 *       <br />If you got a token, make sure assign to a query param named <b>api_key</b> 
 *       while accessing any API of fish saying except this one.
 *       <br />The format following like below:
 *       <br />http://staging.api.fishsaying.com/users/login.json?api_key=your_got_token
 *       <br /><hr />If you just want to use api document to test it, 
 *       you have to input the token what you got from server into inputfield on the top bar.
 *       <br />
 *       <br />There is a convenient way to test, please use <b>362edb126af7cacbae0d20051cbb2e76</b> as token.
 *       If you don't wanna generate token anymore. :)",
 *       responseClass="User",
 *       nickname="authorize",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="authorize",
 *           description="A valid email address or username or unique id got from third-party",
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
 *           name="certified",
 *           description="Name of certified, including `sina_weibo`, `qzone`, `facebook` and `twitter`. Case insensitive",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="401",
 *            reason="Unauthorized"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function authorize() {
        $user = array();
        
        // Check whether user want to login by certified...
        if($this->request->data('certified')) {
            $user = $this->User->loginByCertified(
                $this->request->data('certified'), 
                $this->request->data('authorize'));
        } else {
            $user = $this->User->login(
                $this->request->data('authorize'), 
                $this->request->data('password'));
        }
		
		if(isset($user['User']) && ($user = $user['User'])) {
			$this->Patch->patchPath($user);
			$this->Patch->patchCash($user);
			
			return $this->result(array(
				'auth_token' => $this->AuthorizeToken->add(new CredentialItem($user)),
				'user' => $user
			));
		}
		return $this->fail(400, __('Username or password is wrong'));
	}
}