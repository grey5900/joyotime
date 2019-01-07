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
APP::uses('PriceComponent', 'Controller/Component');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/checkouts",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="Checkout",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="type",type="int",required="true"),
 *     @SWG\Property(name="avatar",type="string"),
 *     @SWG\Property(name="title",type="string"),
 *     @SWG\Property(name="voice_id",type="string"),
 *     @SWG\Property(name="amount",type="Array",items="$ref:CheckoutAmount",required="true"),
 *     @SWG\Property(name="from",type="Array",items="$ref:CheckoutFrom"),
 *     @SWG\Property(name="to",type="Array",items="$ref:CheckoutTo"),
 *     @SWG\Property(name="receipt",type="Array",items="$ref:Receipt"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CheckoutAmount",
 *   @SWG\Properties(
 *     @SWG\Property(name="time",type="int",required="true"),
 *     @SWG\Property(name="money",type="Array",items="$ref:CheckoutMoney")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CheckoutFrom",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="username",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CheckoutTo",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="username",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Receipt",
 *   @SWG\Properties(
 *     @SWG\Property(name="data",type="string",required="true"),
 *     @SWG\Property(name="raw",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CheckoutMoney",
 *   @SWG\Properties(
 *     @SWG\Property(name="value",type="int",required="true"),
 *     @SWG\Property(name="currency",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Checkouts",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:Checkout")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="ExchangeResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="cash",type="float",required="true"),
 *     @SWG\Property(name="draw_fee",type="float",required="true"),
 *     @SWG\Property(name="currency",type="string",required="true")
 *   )
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class CheckoutsController extends AppController {
    
    public $name = 'Checkouts';
    
    public $uses = array('Checkout', 'User', 'Voice');
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/checkouts.{format}",
 *   description="Get checkouts by someone",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get checkouts by someone",
 *       notes="The field of type is Enum, the available values are below:
 *       <br />TYPE_VOICE_INCOME: 1,
 *       <br />TYPE_VOICE_COST: 2,
 *       <br />TYPE_PAYMENT: 3,
 *       <br />TYPE_DRAW: 4,
 *       <br />TYPE_TRANSFER: 5,
 *       <br />TYPE_RECEIVED: 6,
 *       <br />TYPE_DRAW_REVERSE: 7,
 *       <br />TYPE_OFFICIAL_GIFT: 8,
 *       ",
 *       responseClass="Checkouts",
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
 *           name="type",
 *           description="Allow multiple values are seperated by comma, e.g. 1, 2, 4, 6",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=true,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="The current page number, default is 1",
 *           paramType="path",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="1",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="The limitation number for each page, default is 20",
 *           paramType="path",
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
    public function index() {
        $conditions = array();
        
        $userId = $this->OAuth->getCredential()->getUserId();
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        // process type...
        if($this->request->query('type')) {
            $typeStr = urldecode($this->request->query('type'));
            $types = explode(',', $typeStr);
            foreach($types as &$item) {
                $item = (int) $item;
            }
            $conditions = array_merge($conditions, array(
                'type' => array('$in' => $types)
            ));
        }
        
        if($userId) {
            $conditions = array_merge($conditions, array(
                'user_id' => $userId
            ));
        }
        
        $cos = $this->Checkout->find('all', array(
            'fields' => array('receipt' => 0),
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        foreach($cos as &$co) {
            $this->Patch->patchUser($co['Checkout']);
            $this->Patch->patchPath($co['Checkout']);
        }
        $total = $this->Checkout->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($cos, '{n}.Checkout'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/checkouts.{format}",
 *   description="Get checkouts",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get checkouts",
 *       notes="The field of type is Enum, the available values are below:
 *       <br />TYPE_VOICE_INCOME: 1,
 *       <br />TYPE_VOICE_COST: 2,
 *       <br />TYPE_PAYMENT: 3,
 *       <br />TYPE_DRAW: 4,
 *       <br />TYPE_TRANSFER: 5,
 *       <br />TYPE_RECEIVED: 6,
 *       <br />TYPE_DRAW_REVERSE: 7,
 *       <br />TYPE_OFFICIAL_GIFT: 8,
 *       ",
 *       responseClass="Checkouts",
 *       nickname="admin_index",
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
 *           name="type",
 *           description="Allow multiple values are seperated by comma, e.g. 1, 2, 4, 6",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=true,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="processed",
 *           description="Just only for withdrawal, available values likes below,
 *           <br />WITHDRAWAL_DONT_PROCESS_YET = 1000
 *           <br />WITHDRAWAL_PROCESSED = 1001
 *           <br />WITHDRAWAL_REVERTED = 1002
 *           <br />Allow multiple values are seperated by comma, e.g. 1001, 1002",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=true,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="The current page number, default is 1",
 *           paramType="path",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="1",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="The limitation number for each page, default is 20",
 *           paramType="path",
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
    public function admin_index() {
        $conditions = array();
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        // process type...
        if($this->request->query('type')) {
            $typeStr = urldecode($this->request->query('type'));
            $types = explode(',', $typeStr);
            foreach($types as &$item) {
                $item = (int) $item;
            }
            $conditions = array_merge($conditions, array(
                'type' => array('$in' => $types)
            ));
        }
        
        // process processed...
        if($this->request->query('processed')) {
            $items = explode(',', urldecode($this->request->query('processed')));
            foreach($items as &$item) {
                $item = (int) $item;
            }
            $conditions = array_merge($conditions, array(
                'processed' => array('$in' => $types)
            ));
        }
        
        $cos = $this->Checkout->find('all', array(
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        foreach($cos as &$co) {
            $this->Patch->patchUser($co['Checkout']);
            $this->Patch->patchPath($co['Checkout']);
        }
        $total = $this->Checkout->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($cos, '{n}.Checkout'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/checkouts/transfer.{format}",
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
    public function transfer() {
        $userId = $this->OAuth->getCredential()->getUserId();
    	$payee  = $this->request->data('payee');
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
    
/**
 * @SWG\Api(
 *   path="/checkouts/tip.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Tip from payer to payee.",
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
 *           name="payee",
 *           description="user id",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="seconds",
 *           description="Unit: second",
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
    public function tip() {
    	$seconds = $this->request->data('seconds');
    	
    	$payer = $this->User->getById($this->OAuth->getCredential()->getUserId());
    	$payer = ($payer) ? $payer['User'] : array();
    	$payee = $this->User->getById($this->request->data('payee'));
    	$payee = ($payee) ? $payee['User'] : array();
    
    	if(!$payer || !$payee) return $this->fail(400, __('Invalid user id'));
    
    	// The cost operation in an transaction.
    	if(!$this->User->cost($payer['_id'], $seconds)) 
    	    return $this->fail(400, __('Transfer fails'));
    	if(!$this->User->earn($payee['_id'], $seconds)) 
    	    return $this->fail(400, __('Transfer fails'));
    	if(!$this->Checkout->tip($payer, $payee, $seconds)) 
    	    return $this->fail(400, __('Transfer fails'));
    	
    	return $this->success();
    }
}