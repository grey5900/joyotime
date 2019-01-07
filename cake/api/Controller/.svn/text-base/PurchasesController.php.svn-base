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
 *  resourcePath="/purchases",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 */

/**
 * The class is used to CRUD favorites and voice list in favorite for each user. 
 *
 * @package		app.Controller
 */
class PurchasesController extends AppController {
    
    public $name = 'Purchases';
    
    public $uses = array('Checkout', 'User', 'Voice', 'Purchased', 'Subsidy');
    
    public $components = array('Param');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    
    	// Register all callbacks for this controller...
    	$this->Checkout->getEventManager()->attach($this->User);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/purchases.{format}",
 *   description="Buy a voice",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Buy a voice",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="buy",
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
 *           name="voice_id",
 *           description="The id of voice",
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
 *          ),
 *          @SWG\ErrorResponse(
 *            code="402",
 *            reason="When user have no enough menoey"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="409",
 *            reason="Purchased already"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="409",
 *            reason="Purchased yourself voice"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function add($userId) {
        $user = $this->User->getById($userId);
        if(!$user) return $this->fail(400, __('Invalid user id'));
        
        $voiceIds = explode(',', $this->request->data('voice_id'));
        $voice = array();
        
        try {
            foreach($voiceIds as $voiceId) {
                $voiceId = trim($voiceId);
                $voice = $this->buyOne($user, $voiceId);
            }
        } catch(Exception $e) {
            return $this->fail(400, $e->getMessage());
        }
        return count($voiceIds) == 1 ? $this->result($voice) : $this->success();
    }
    
    private function buyOne($user, $voiceId) {
        $userId = $user['User']['_id'];
        if(!$this->isMongoId($voiceId)) {
            throw new CakeException(__('Invalid voice id'));
        }
        
        $voice = $this->Voice->findById($voiceId);
        if(!$voice || !isset($voice['Voice']['status'])) {
            throw new CakeException('Invalid voice: '.$voiceId);
        }
        
        if($voice['Voice']['user_id'] == $userId) {
            throw new CakeException(__('Purchase yourself voice'));
        }
        
        if($voice['Voice']['status'] != Voice::STATUS_APPROVED) {
            throw new CakeException(__('The voice has been pulled off shelf'));
        }
        
        if($this->isBought($userId, $voiceId)) {
            throw new CakeException(__('Purchased already'));
        }
        
        // Calculate exactly price that user should pay for.
        $price = $this->Price->calc($voice['Voice']['length']);
        // Free voice don't need to create checkout...
        if($price > 0) {
            $subsidy = 0;
            if(!$this->Subsidy->exist($this->Param->deviceCode(), $voiceId)) {
                $this->Subsidy->add($this->Param->deviceCode(), $voiceId);
                $subsidy = $this->Price->subsidy($price);
            }
        	// The cost operation in an transaction.
        	if(!$this->User->cost($userId, $price)) {
        	    throw new CakeException(__('Purchase fails, might no enough money'));
        	}
        	if(!$this->User->earn($voice['Voice']['user_id'], $price + $subsidy)) {
        	    throw new CakeException(__('Purchase fails'));
        	}
        	// Generate checkout for each user in respectively.
        	$this->Checkout->voiceIncome($user, $voice, $price);
        	$this->Checkout->voiceCost($user, $voice, $price);
        	$this->Checkout->subsidy($voice['Voice']['user_id'], $voiceId, $subsidy);
        	// Update earn total for voice...
        	$this->Voice->updateEarnTotal($voiceId, $price);
        }
        
        // Increase 1 for checkout_total of voice
        $this->Voice->updateCheckoutTotal($voiceId);
        
        // after pay transaction remove user data from cache...
        $this->Purchased->push($userId, $voiceId);
        
        $voice['Voice']['bought'] = 1;
        $this->Patch->patchPath($voice['Voice']);
        $this->Patch->patchUser($voice['Voice']);
        
        return $voice['Voice'];
    }
    
/**
 * Is bought whatever it is free or not
 * 
 * @param string $userId
 * @param string $voiceId
 */
    private function isBought($userId, $voiceId) {
        return $this->Purchased->isExist($userId, $voiceId);
    }
    
/**
 * @SWG\Api(
 *   path="/users/{user_id}/purchases.{format}",
 *   description="Get track list of purchased",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get track list of purchased",
 *       notes="",
 *       responseClass="Voices",
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
 *           name="limit",
 *           description="The limitation number for each page, default is 10",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="20",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="year",
 *           description="The year got in last time",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="month",
 *           description="The month got in last time",
 *           paramType="query",
 *           required="false",
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
    public function index($userId = '') {
        $limit = $this->request->query('limit');
        $limit = $limit ? intval($limit) : 20;
        
        if(!$userId || !$this->isMongoId($userId)) {
            return $this->fail(400, __('Invalid user id'));
        } 
        
        $sinceYear = $this->request->query('year');
        $sinceMonth = $this->request->query('month');
        $groups = $this->Purchased->byGroup($userId, $limit, $sinceYear, $sinceMonth);
        // Get voice item for each voice id...
        $count = count($groups);
        $result = array();
        foreach($groups as $voices) {
            if(is_array($voices['list'])) {
                foreach($voices['list'] as $idx => &$voice) {
                    $purchaseDate = '';
                    if(stristr($voice, '#')) {
                        list($voiceId, $purchaseDate) = explode('#', $voice);
                    } else {
                        $voiceId = $voice;
                    }
                    $row = $this->Voice->findById($voiceId);
                    if($row && isset($row['Voice'])) {
                        $this->Patch->patchPath($row['Voice']);
                        $this->Patch->patchUser($row['Voice']);
                        $voice = $row['Voice'];
                        if($purchaseDate) {
                            $voice['purchase_date'] = $purchaseDate;
                        }
                        // Keep model consistent...
                        $voice['bought'] = 1;
                    } else {
                    	unset($voices['list'][$idx]);
                    }
                }
            }
            $result[] = $voices;
        }
        
        return $this->results($result, $this->Purchased->count($userId));
    }
    
    /**
     * @SWG\Api(
     *   path="/purchases.{format}",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       httpMethod="GET",
     *       summary="Calucate final price for purchase, supported multiple voices",
     *       notes="",
     *       responseClass="CalculateResulte",
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
     *           name="voice_id",
     *           description="Allow multiple values seperated by comma",
     *           paramType="query",
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
    public function view() {
        $amount = 0;
        $canbuy = 0;
        $unpurchased_count = 0;
        $unpurchased_voices = array();
        
        $credential = $this->OAuth->getCredential();
        $userId = $credential->getUserId();
        $user = $this->User->findById($userId);
        if(!$user) return $this->fail(400, __('Auth token is invalid or expired already'));
        
        $voiceIds = explode(',', $this->request->query('voice_id'));
        
        foreach($voiceIds as $voiceId) {
            $voiceId = trim($voiceId);
            if(!$this->isBought($userId, $voiceId)) {
                $voice = $this->Voice->find('first', array(
            		'fields' => array('length', 'status', 'isfree', 'user_id'),
            		'conditions' => array('_id' => new MongoId($voiceId))
                ));
                if(!$voice) continue;
                if($voice[$this->Voice->name]['status'] != 1) continue;
                if($voice[$this->Voice->name]['isfree'] == 1) continue;
                if($voice[$this->Voice->name]['user_id'] == $user[$this->User->name]['_id']) continue;
                $amount += (int)$voice[$this->Voice->name]['length'];
                $unpurchased_count += 1;
                $unpurchased_voices[] = $voice[$this->Voice->name]['_id'];
            }
        }
        
        if($user[$this->User->name]['money'] >= $amount) $canbuy = 1;
        
        $data = array(
            'amount' => array(
                'time' => $amount
            ),
            'can_buy' => $canbuy,
            'unpurchased_count' => $unpurchased_count,
            'unpurchased_voices' => $unpurchased_voices
        );
        
        return $this->result($data);
    }
}