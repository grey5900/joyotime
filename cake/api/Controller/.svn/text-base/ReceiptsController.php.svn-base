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
 *  resourcePath="/receipts",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="ReceiptResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="type",type="int",required="true"),
 *     @SWG\Property(name="status",type="string",required="true"),
 *     @SWG\Property(name="amount",type="Array",items="$ref:CheckoutAmount",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 *
 */

/**
 * @package		app.Controller
 */
class ReceiptsController extends AppController {
    
    public $name = 'Receipts';
    
    public $uses = array('Checkout', 'Product', 'User', 'Receipt', 'Coupon');
    
    public $components = array('IosReceipt');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	
    	$this->OAuth->allow($this->name, 'edit');
    
    	// Register all callbacks for this controller...
    	$evt = $this->Receipt->getEventManager();
    	$evt->attach($this->User);
    	$evt->attach($this->Checkout);
    }
    
/**
 * @SWG\Api(
 *   path="/receipts/alipay.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Generate new receipt for this payment by Alipay",
 *       notes="",
 *       responseClass="SuccessResponse",
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
 *           name="pro_id",
 *           description="The id of products",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Fails to save receipt"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function alipay() {
        $saved = $this->Receipt->alipay(
                $this->OAuth->getCredential()->getUserId(), 
                $this->Product->alipay($this->request->data('pro_id'), 'price'),
                $this->Product->alipay($this->request->data('pro_id'), 'seconds'));
        if($saved) {
            $id = $saved['Receipt']['_id'];
            return $this->success(201, 
                    array("Location: /receipts/$id"), array('_id' => $id));
        }
        return $this->fail(500, $this->errorMsg($this->Receipt));
    }
    
/**
 * @SWG\Api(
 *   path="/receipts/ios.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Generate new receipt for purchase in ios",
 *       notes="",
 *       responseClass="SuccessResponse",
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
 *           name="receipt",
 *           description="The raw data come from Appstore",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Fails to save receipt"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function ios() {
        try {
            $exist = $this->Receipt->exist($this->request->data('receipt'));
            if($exist) {
                return $this->fail(400, __('The receipt has existed'));
            }
            // verify the receipt
        	$info = $this->IosReceipt->getReceiptData(
        	        $this->request->data('receipt'), 
        	        Configure::read('Iap.Sandbox'));
        }
        catch (Exception $ex) {
        	// unable to verify receipt, or receipt is not valid
        	if($ex->getCode() == 21007 && Configure::read('Iap.Sandbox') == false ) {
        		// Re-try sandbox for apple checking...
        	    $this->log('Re-try to checking receipt using sandbox...', Configure::read('Log.Ios'));
        		try {
	        		$info = $this->IosReceipt->getReceiptData(
	        	        $this->request->data('receipt'), 
	        	        true);
	        		
	        		$this->log('The metadata is below...', Configure::read('Log.Ios'));
	        		$this->log($info, Configure::read('Log.Ios'));
        		} catch(Exception $e) {
        		    $this->log($e, Configure::read('Log.Ios'));
        			return $this->fail(400, $e->getMessage());
        		}
        	} else {
        		return $this->fail(400, $ex->getMessage());
        	}
        }
        
        $saved = $this->Receipt->ios(
        		$this->OAuth->getCredential()->getUserId(),
        		$this->Product->ios($info['product_id']),
        		$this->request->data('receipt'),
        		$info);
         
        if($saved) {
        	$id = $saved['Receipt']['_id'];
        	return $this->success(201,
        			array("Location: /receipts/$id"), array('_id' => $id));
        }
        
        return $this->fail(500, $this->errorMsg($this->Receipt));
    }
    
/**
 * @SWG\Api(
 *   path="/receipts/qr.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Generate new receipt for QR",
 *       notes="",
 *       responseClass="SuccessResponse",
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
 *           name="coupon",
 *           description="Code for promotion",
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
    public function qr() {
        $userId = $this->OAuth->getCredential()->getUserId();
        $coupon = $this->request->data('coupon');
        $second = $this->Coupon->available($coupon, $userId);
        if($second <= 0) return $this->fail(400, __('Invalid coupon code'));
        return $this->Receipt->coupon($userId, $coupon, $second) 
            ? $this->result(array('seconds' => $second)) : $this->fail(400, $this->errorMsg($this->Receipt));
    }
    
/**
 * @SWG\Api(
 *   path="/receipts/{receipt_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Change status from pending to paid",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="edit",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="receipt_id",
 *           description="The id of receipt",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="price",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="trade_no",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="403",
 *            reason="The token is invalid"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="500",
 *            reason="Internal Server Error"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function edit($receiptId) {
        // Check whether token come from payment server...
        if($this->request->query('api_key') != Configure::read('Payment.Access.Token')) {
            return $this->fail(403);
        }
        
        if($this->Receipt->paid($receiptId, $this->request->data)) {
            return $this->success();
        }
        
        // Sending mail if payment fails...
        ClassRegistry::init('Error')->enqueue(
            array(
                'message' => "Receipt [$receiptId] fails to handle, because of price exception...", 
                'context' => print_r($this->request->data, TRUE)
            )
        );
        return $this->fail(500);
    }
    
/**
 * @SWG\Api(
 *   path="/receipts/{receipt_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get detail information of receipt",
 *       notes="",
 *       responseClass="ReceiptResponse",
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
 *           name="receipt_id",
 *           description="The id of receipt",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function view($receiptId) {
        $userId = $this->OAuth->getCredential()->getUserId();
        $receipt = $this->Receipt->find('first', array(
            'conditions' => array(
                'user_id' => $userId,
                '_id' => new MongoId($receiptId)
            )
        ));
        if($receipt) {
            $this->Patch->patchUser($receipt);
        }
        return $this->result($receipt['Receipt']);
    }
    
/**
 * @SWG\Api(
 *   path="/receipts.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get receipt",
 *       notes="",
 *       responseClass="ReceiptsResponse",
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
 *           name="status",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
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
 *       )
 *     )
 *   )
 * )
 */
    public function index() {
        $credential = $this->OAuth->getCredential();
        if(!$credential->isAdmin()) {
            return $this->fail(403);
        }
        
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $limit = is_numeric($limit) ? intval($limit) : 20;
        
        $conditions = array();
        
        $status = $this->request->query('status');
        if($status) {
            $items = explode(',', urldecode($status));
            foreach($items as &$item) {
            	$item = (int) trim($item);
            }
            $conditions['status'] = array('$in' => $items);
        }
        
        $receipts = $this->Receipt->find('all', array(
            'conditions' => $conditions,
            'order' => array(
                'created' => 'desc'
            ),
            'page' => $page,
            'limit' => $limit
        ));
        
        foreach($receipts as &$receipt) {
            $this->Patch->patchUser($receipt['Receipt']);
        }
        
        $total = 0;
        if(count($receipts)) {
            $total = $this->Receipt->find('count', array(
                'conditions' => $conditions
            ));
        }
        return $this->results(Hash::extract($receipts, '{n}.Receipt'), $total);
    }
}