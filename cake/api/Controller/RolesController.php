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
 *  apiVersion="1.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/roles",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * Assignment role and permissions
 * 
 * @package		app.Controller
 */
class RolesController extends AppController {
    
    public $name = 'Roles';
    
    public $uses = array('User');
    
/**
 * @SWG\Api(
 *   path="/admin/roles.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Change user role",
 *       notes="Just only for administrator
<br />For now, there are four roles:
<br />`admin`
<br />`user`
<br />`checker`
<br />`freeze`
<br /> After role updated successful, the user will be forced offline. 
",
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
 *           description="The id of user whom role is adjusting",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="role",
 *           description="`admin`, `user`, `checker` and `freeze`",
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
    public function admin_edit() {
    	$update = array();
        $userId = $this->request->data('user_id');
        $role	= $this->request->data('role');
        
        // update role...
        if(!$userId) return $this->fail(400, __('user_id invalid'));
        if(!$role) 	 return $this->fail(400, __('role invalid'));
        
//         $items = explode(',', $role);
//         foreach($items as &$item) $item = trim($item);
//         $update['role'] = implode('|', $items);
        $update['role'] = $role;
        $update['_id']  = $userId;

        if($this->User->save($update)) {
        	$this->OAuth->clean($userId);
        	return $this->success(200);
        }
        return $this->fail(400, $this->errorMsg($this->User));
    }
}