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
 *  apiVersion="1.0",
 *  swaggerVersion="1.1",
 *  resourcePath="/charges",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class ChargesController extends AppController {
    
    public $name = 'Charges';
    
    public $uses = array('Checkout', 'User', 'Voice');
    
    public $components = array('Price');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'view');
    }
    
/**
 * @SWG\Api(
 *   path="/charges/{gateway}/{currency}/{seconds}.{format}",
 *   description="Get charges from different gateway",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get charges from different gateway",
 *       notes="",
 *       responseClass="ExchangeResponse",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="seconds",
 *           description="The number of time, unit is second",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="currency",
 *           description="The default value is CNY if no valid parameter supplied",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           @SWG\AllowableValues(valueType="LIST", values="['CNY', 'USD']"),
 *           defaultValue="CNY",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="gateway",
 *           description="The name of payment gateway",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           @SWG\AllowableValues(valueType="LIST", values="['alipay']"),
 *           defaultValue="alipay",
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
    public function view($gateway = 'alipay', $currency = 'CNY', $second = '') {
    	if(!$currency) {
    		return $this->fail(400, __('Invalid currency'));
    	}
    	if(!$gateway) {
    		return $this->fail(400, __('Invalid gateway'));
    	}
    	if(!$second || $second <= 0) {
    		return $this->fail(400, __('Invalid second'));
    	}
    	try {
        	$cash = $this->Price->toCash($currency);
        	$amount = $cash->calc($second);
        	$fee = $this->Price->fee($gateway)->draw($amount);
    	} catch(Exception $e) {
    	    return $this->fail(400, $e->getMessage());
    	}
    	return $this->result(array(
			'cash' => $amount,
			'draw_fee' => $fee,
			'currency' => $cash->currency()
    	));
    }
}