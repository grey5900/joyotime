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
 *  resourcePath="/tags",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="TagResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="name",type="string",required="true"),
 *     @SWG\Property(name="ref_total",type="int",required="true"),
 *     @SWG\Property(name="category",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class TagsController extends AppController {
    
    public $name = 'Tags';
    
    public $uses = array('Tag');
    
    public $components = array('Param');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	
    	$this->OAuth->allow($this->name, 'index');
    	$this->OAuth->allow($this->name, 'view');
    	
    	// Register all callbacks for this controller...
//     	$this->Package->getEventManager()->attach($this->Voice);
//     	$this->Package->getEventManager()->attach(new \Model\Index\Package());
    }
    
/**
 * @SWG\Api(
 *   path="/tags.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get tags",
 *       responseClass="TagResponse",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="category",
 *           description="Search by category name",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="page",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="`zh_CN`, `en_US`, default is `zh_CN`",
 *           paramType="query",
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
    public function index() {
        $cond = array();
        $cate = $this->request->query('category');
        if($cate) $cond['category'] = $cate;
        $cond['language'] = $this->Param->language()?:'zh_CN';
        $rows = $this->Tag->find('all', array(
            'conditions' => $cond,
            'order' => array('category' => 'desc'),
            'page'  => $this->request->query('page')?:1,
            'limit' => $this->request->query('limit')?:20
        ));
        $total = $this->Tag->find('count', array('conditions' => $cond));
        return $this->results(Hash::extract($rows, '{n}.Tag'), $total);
    }
    
/**
 * @SWG\Api(
 *   path="/tags/{tag_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="A tag detail",
 *       responseClass="TagResponse",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="tag_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple="false",
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
    public function view($tagId) {
        $item = $this->Tag->findById($tagId);
        if(!$item) return $this->fail(404);
        return $this->result($item['Tag']);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/tags.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Add a new one",
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
 *           name="name",
 *           description="The name of tag",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="category",
 *           description="The name of category",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="`zh_CN` or `en_US`, default is `zh_CN`",
 *           paramType="query",
 *           required="false",
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
    public function admin_add() {
        $this->request->data['language'] = $this->Param->language()?:'zh_CN';
        if($this->Tag->save($this->request->data)) {
            return $this->success();
        }
        return $this->fail(400, $this->errorMsg($this->Tag));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/tags/{tag_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Edit tag",
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
 *           name="tag_id",
 *           description="The id of tag",
 *           paramType="path",
 *           required="query",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="name",
 *           description="The name of tag",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="category",
 *           description="The name of category",
 *           paramType="form",
 *           required="false",
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
    public function admin_edit($tagId) {
        $this->Tag->id = $tagId;
        if($this->Tag->save($this->request->data)) {
            return $this->success();
        }
        return $this->fail(400, $this->errorMsg($this->Tag));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/tags/{tag_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Delete a tag",
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
 *           name="tag_id",
 *           description="Id of tag",
 *           paramType="path",
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
    public function admin_delete($tagId) {
        if($this->Tag->delete($tagId)) {
            return $this->success();
        }
        return $this->fail(400, $this->errorMsg($this->Tag));
    }    
}