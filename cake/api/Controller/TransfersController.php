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
APP::uses('PriceComponent', 'Controller/Component');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/transfers",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class TransfersController extends AppController {
    
    public $name = 'Transfers';
    
    public $uses = array('Checkout', 'User');
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/transfers.{format}",
 *   description="Transfer time from payer to payee.",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Transfer time from payer to payee.",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="transfer",
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
 *           description="The userid of payer",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="payee",
 *           description="The userid of payee.",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="seconds",
 *           description="how much time want to transfer?",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
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
    public function add($userId) {
        $payee = $this->request->data('payee');
        $seconds = $this->request->data('seconds');
        
        if(!($user = $this->User->getById($userId)) 
        	|| !($toUser = $this->User->getById($payee))) {
        	return $this->fail(400, __('Invalid user id'));
        }
        
        // The cost operation in an transaction.
        if(!$this->User->cost($userId, $seconds)) {
        	return $this->fail(400, __('Transfer fails'));
        }
        // Transfer seconds to payee
        if(!$this->User->transfer($payee, $seconds)) {
        	return $this->fail(400, __('Transfer fails'));
        }
        // Generate checkout for payer.
        if(!($saved = $this->Checkout->transfer($user['User'], $toUser['User'], $seconds))) {
        	return $this->fail(400, __('Transfer fails'));
        }
        // Generate checkout for payee.
        if(!($saved = $this->Checkout->received($user['User'], $toUser['User'], $seconds))) {
        	return $this->fail(400, __('Transfer fails'));
        }
        
        return $this->success();
    }
}