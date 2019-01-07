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
APP::uses('Validation', 'Utility');
APP::uses('Checkout', 'Model');
APP::uses('Withdrawal', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/reverse_withdrawals",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class ReverseWithdrawalsController extends AppController {
    
    public $name = 'ReverseWithdrawals';
    
    public $uses = array('Checkout', 'User', 'Voice', 'Withdrawal', 'ReverseWithdrawal');
    
/**
 * @SWG\Api(
 *   path="/admin/reverse_withdrawals.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create a transaction for reverse withdrawal",
 *       notes="Just only for administrator",
 *       responseClass="SuccessResponse",
 *       nickname="admin_add",
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
 *           name="checkout_id",
 *           description="The id of checkout you want to revert",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="reason",
 *           description="That's why revert withdrawal",
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
    public function admin_add() {
        $coId = $this->request->data('checkout_id');
        if(!$coId || !$this->isMongoId($coId)) {
        	return $this->fail(400, __('Invalid checkout id'));
        }
        
        $reason = $this->request->data('reason');
        if(!$reason) {
        	return $this->fail(400, __('Invalid reason supplied'));
        }
        
        $co = $this->Withdrawal->findById($coId);
        if($co && isset($co['Withdrawal']['type']) 
            && isset($co['Withdrawal']['user_id'])
            && isset($co['Withdrawal']['amount']['time'])
            && isset($co['Withdrawal']['processed'])
            && $co['Withdrawal']['processed'] == Withdrawal::NOT_PROCESSED_YET
            && $co['Withdrawal']['type'] == Withdrawal::TYPE
            && !$this->ReverseWithdrawal->exist($coId)) {
            
            // Increase money and earn according to amount.time user withdraw, but income...
            if(!$this->User->withdrawRevert($co['Withdrawal']['user_id'], $co['Withdrawal']['amount']['time'])) {
                return $this->fail(400, __('Revert withdraw fails'));
            }
            // Set Checkout.processed is reverted already...
            $this->Withdrawal->update($coId, Withdrawal::REVERTED);
            // Add checkout for reverse withdrawal...
            $checkout = $this->ReverseWithdrawal->add(
                    $co['Withdrawal']['user_id'], 
                    $coId, 
                    $co['Withdrawal']['amount']['time'],
                    $reason);
            if(!$checkout || !isset($checkout['ReverseWithdrawal']['_id'])) {
                return $this->fail(400, __('Create checkout for revert withdrawal fails'));
            }
            
            return $this->success(201,
            		array('Location' => "/users/{$co['Withdrawal']['user_id']}/reverse_withdrawals/".$checkout['ReverseWithdrawal']['_id']),
            		array('_id' => $checkout['ReverseWithdrawal']['_id']));
            
        }
        return $this->fail(400, __('Invalid request'));
    }
}