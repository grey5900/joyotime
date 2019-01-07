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
APP::uses('Checkout', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/withdrawals",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * @package		app.Controller
 */
class WithdrawalsController extends AppController {
    
    public $name = 'Withdrawals';
    
    public $uses = array('Checkout', 'User', 'Voice', 'ReverseWithdrawal', 'Withdrawal');
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/withdrawals.{format}",
 *   description="Create a transaction for withdraw deposite",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create a transaction for withdraw deposite",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="withdraw",
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
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="second",
 *           description="The number of time, unit is second",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="gateway",
 *           description="The name of payment gateway",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           @SWG\AllowableValues(valueType="LIST", values="['alipay', 'paypal']"),
 *           defaultValue="alipay",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="account",
 *           description="The email address as user's account of payment gateway",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="realname",
 *           description="The realname of user that is used in payment gateway",
 *           paramType="form",
 *           required="false",
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
    public function add($userId) {
        $second = $this->request->data('second');
        $gateway = $this->request->data('gateway');
    
        // Check whether $second less than 20 mins...
    	if(!$second || $second <= 0 || !is_numeric($second) || $second < 20 * 60) {
    		return $this->fail(400, __('Invalid second'));
    	}
    
    	if(!$gateway || !Gateway::exist($gateway)) {
    		return $this->fail(400, __('Invalid gateway'));
    	}
    	
    	// transaction information of gateway
    	$realname = $this->request->data('realname');
    	$account = $this->request->data('account');
    	/*
    	 * @todo need to refactoring here for validation phone number
    	 */
    	if(!$account /* || !Validation::email($account) */) {
    		return $this->fail(400, __('Invalid account'));
    	}
    	
    	$gateway = strtolower($gateway);
    	if($gateway == Gateway::ALIPAY) {
    	    if(!$realname) {
    	        return $this->fail(400, __('Invalid realname'));
    	    }
    	}
    	// try to get currency by gateway name
    	try {
    	    $currency = $this->getCurrency($gateway);
    	} catch(NotFoundException $e) {
    	    return $this->fail(400, $e->getMessage());
    	}
    	
    	$user = $this->User->getById($userId);
    	if(!$user) {
    		return $this->fail(400, __('Invalid user id'));
    	}
    	
    	// The cost operation in an transaction.
    	if(!$this->User->withdraw($userId, $second)) {
    		return $this->fail(400, __('Withdrawal fails. Earnings are not enough to withdraw.'));
    	}
    	// Generate checkout for each user in respectively.
    	$cash = $this->Price->toCash($currency);
    	$fee = $this->Price->fee($gateway);
    	$saved = $this->Withdrawal->add($userId, $second, $cash, $fee, $account, $realname);
    
    	return $this->success(201, 
    	        array('Location' => "/users/$userId/withdrawals/".$saved['Withdrawal']['_id']), 
    	        array('_id' => $saved['Withdrawal']['_id']));
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/withdrawals/{checkout_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="View a withdrawal checkout",
 *       notes="",
 *       responseClass="Checkout",
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
 *           name="checkout_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
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
    public function view($checkoutId) {
        $co = $this->Withdrawal->findById($checkoutId);
        if(!$co || !isset($co['Withdrawal'])) {
            return $this->fail(404);
        }
        return $this->result($co['Withdrawal']);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/withdrawals.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get list of withdrawal checkouts",
 *       notes="",
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
 *           description="",
 *           paramType="path",
 *           required="true",
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
 *           description="The limit number for each page, default is 20",
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
    public function index($userId) {
    	$page = $this->request->query('page');
    	$limit = $this->request->query('limit');
    	
    	$conditions = array(
    	    'user_id' => $userId,
    	    'type' => Checkout::TYPE_DRAW
    	);
    	
        $rows = $this->Checkout->find('all', array(
    		'conditions' => $conditions,
    		'order' => array('created' => 'desc'),
    		'page' => $page > 0 ? intval($page) : 1,
    		'limit' => $limit > 0 ? intval($limit) : 20
        ));
        $total = $this->Checkout->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($rows, '{n}.Checkout'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/withdrawals.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get list of withdrawal checkouts",
 *       notes="The values of `processed` are: 
 *       <br />DONT_PROCESS_YET = 1000
 *       <br />PROCESSED = 1001
 *       <br />REVERTED = 1002",
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
 *           name="processed",
 *           description="Allow multiple values are seperated by comma, e.g. 1000, 1001, 1002",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=true,
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
    	$page = $this->request->query('page');
    	$limit = $this->request->query('limit');
    	
    	$conditions = array(
    	    'type' => Checkout::TYPE_DRAW
    	);
    	
    	// process processed...
    	if($this->request->query('processed')) {
    		$typeStr = urldecode($this->request->query('processed'));
    		$types = explode(',', $typeStr);
    		foreach($types as &$item) {
    			$item = (int) $item;
    		}
    		$conditions = array_merge($conditions, array(
    			'processed' => array('$in' => $types)
    		));
    	}
    	
        $rows = $this->Withdrawal->find('all', array(
    		'conditions' => $conditions,
    		'order' => array('created' => 'desc'),
    		'page' => $page > 0 ? intval($page) : 1,
    		'limit' => $limit > 0 ? intval($limit) : 20
        ));
       
        foreach($rows as &$row) {
            $this->Patch->patchUser($row['Withdrawal']);
        }
        
        $total = $this->Withdrawal->find('count', array(
            'conditions' => $conditions
        ));
        return $this->results(Hash::extract($rows, '{n}.Withdrawal'), $total);
    }
    
/**
 * Get currency by $gateway
 *
 * @param string $gateway
 * @throws NotFoundException
 * @return string
 */
    private function getCurrency($gateway) {
    	switch($gateway) {
    		case Gateway::ALIPAY:
    			return Cash::TYPE_CNY;
    			break;
    		case Gateway::PAYPAL:
    			return Cash::TYPE_USD;
    			break;
    		default:
    			throw new NotFoundException(__("No found according currency by `$gateway`"));
    	}
    }
    
/**
 * @SWG\Api(
 *   path="/admin/withdrawals/{checkout_id}.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Accept withdrawal",
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
 *           paramType="path",
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
    public function admin_edit($coId = '') {
    	if(!$coId || !$this->isMongoId($coId)) {
    		return $this->fail(400, __('Invalid checkout id'));
    	}
    
    	$co = $this->Withdrawal->findById($coId);
    	if($co && isset($co['Withdrawal']['type'])
        	&& isset($co['Withdrawal']['user_id'])
        	&& $co['Withdrawal']['type'] == Checkout::TYPE_DRAW
        	&& !$this->ReverseWithdrawal->exist($coId)) {
    		
    	    if($this->Withdrawal->update($coId, Withdrawal::PROCESSED)) {
    	        return $this->success();
    	    }
    	}
    	return $this->fail(400, __('Invalid request'));
    }
}