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
 *  resourcePath="/coupons",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="CouponResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="codes",type="Array",items="$ref:CouponCodesResponse",required="true"),
 *     @SWG\Property(name="number",type="int",required="true"),
 *     @SWG\Property(name="length",type="int",required="true"),
 *     @SWG\Property(name="expire",type="int",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="description",type="string",required="true"),
 *     @SWG\Property(name="deleted",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CouponCodesResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="code",type="string",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="false"),
 *     @SWG\Property(name="user",type="Array",items="$ref:User",required="false")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="CouponsResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:CouponResponse")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class CouponsController extends AppController {
    
    public $name = 'Coupons';
    
    public $uses = array('Coupon', 'User');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
/**
 * @SWG\Api(
 *   path="/admin/coupons.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create a batch of coupons",
 *       notes="",
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
 *           name="length",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="number",
 *           description="How many coupons want to generate?",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="expire",
 *           description="timestamp",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
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
    public function admin_add() {
        if($this->Coupon->save($this->request->data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Coupon));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/coupons/{coupon_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Update a coupon",
 *       notes="",
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
 *           name="coupon_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="expire",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
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
    public function admin_edit($couponId) {
        $this->Coupon->id = $couponId;
        if($this->Coupon->save($this->request->data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Coupon));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/coupons/{coupon_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Delete a batch of coupon",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="admin_delete",
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
 *           name="coupon_id",
 *           description="",
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
    public function admin_delete($id) {
        $this->Coupon->id = $id;
        $data = array('deleted' => true);
        if($this->Coupon->save($data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Coupon));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/coupons.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get coupon list",
 *       notes="",
 *       responseClass="CouponsResponse",
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
        $coupons = $this->Coupon->find('all', array(
            'fields' => array('codes' => 0),
            'order' => array('modified' => 'desc'),
            'page' => $this->request->query('page'),
        	'limit' => $this->request->query('limit')?:20
        ));
        
        // $this->httpCache(true, time() + 3600);
        return $this->results(Hash::extract($coupons, '{n}.Coupon'), $this->Coupon->find('count'));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/coupons/{coupon_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get a coupon",
 *       notes="",
 *       responseClass="CouponResponse",
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
 *           name="coupon_id",
 *           description="",
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
    public function admin_view($couponId) {
        $coupon = $this->Coupon->findById($couponId);
        if(!$coupon) return $this->fail(404);
        $coupon = $coupon['Coupon'];
        
        foreach($coupon['codes'] as &$item) {
            if(isset($item['user_id']))
                $item['user'] = $this->User->getById($item['user_id'])['User'];
        }
      
        return $this->result($coupon);
    }
}