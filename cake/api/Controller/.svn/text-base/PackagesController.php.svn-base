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
APP::uses('Feedback', 'Model');

/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/packages",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="PackageResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="user_id",type="string",required="true"),
 *     @SWG\Property(name="user",type="Array", items="$ref:User",required="true"),
 *     @SWG\Property(name="content",type="string",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Packages",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:PackageResponse")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class PackagesController extends AppController {
    
    public $name = 'Packages';
    
    public $uses = array('Package', 'Voice', 'Purchased');
    
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
    	$this->Package->getEventManager()->attach($this->Voice);
    	$this->Package->getEventManager()->attach(new \Model\Index\Package());
    }
    
/**
 * @SWG\Api(
 *   path="/admin/packages.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create a package",
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
 *           name="title",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="The key of file",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="float"
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
        if($this->Package->save($this->request->data)) {
            return $this->success();
        }
        return $this->fail(400, $this->errorMsg($this->Package));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/packages/{package_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Update a package",
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
 *           name="package_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="title",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="cover",
 *           description="The key of file",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="0: pending, 1: avaliable",
 *           paramType="form",
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
    public function admin_edit($packageId) {
        $this->Package->id = $packageId;
        if($this->Package->save($this->request->data)) {
        	return $this->success();
        }
        return $this->fail(400, $this->errorMsg($this->Package));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/packages/{package_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Delete a package",
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
 *           name="package_id",
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
    public function admin_delete($packageId) {
        $this->Package->id = $packageId;
        $data = array('deleted' => true);
        if($this->Package->save($data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Package));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/packages/{package_id}/voice/{voice_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Push a voice into package",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="admin_voice_add",
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
 *           name="package_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="offset",
 *           description="The current position different from orign",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="404",
 *            reason="No Found"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="409",
 *            reason="Conflict"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_voice_add($packageId, $voiceId) {
        $voice = $this->Voice->findById($voiceId);
        if(!$voice) return $this->fail(404);
        $result = $this->Package->push($packageId, 
                $voice[$this->Voice->name], 
                $this->request->data('offset'));
        return ($result) ? $this->success() : $this->fail(409);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/packages/{package_id}/voice/{voice_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Pull a voice from package",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="admin_voice_delete",
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
 *           name="package_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="voice_id",
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
    public function admin_voice_delete($packageId, $voiceId) {
        $voice = $this->Voice->findById($voiceId);
        $voice = ($voice) ? $voice['Voice'] : array();
        $result = $this->Package->pull($packageId, $voice);
        return ($result) ? $this->success() : $this->fail(400);
    }
    
/**
 * @SWG\Api(
 *   path="/packages.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get package list",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="keyword",
 *           description="term or word",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="deleted",
 *           description="0: not delete yet, 1: deleted already",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="0",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="0: pending, 1: avaliable",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="1",
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="latitude",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="longitude",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="float"
 *         ),
 *         @SWG\Parameter(
 *           name="radius",
 *           description="unit: kilometer",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="1",
 *           dataType="float"
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
    public function index() {
        $conditions = $order = array();
        $point = null;
        
        $page  = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page  = is_numeric($page) ? intval($page) : 1;
        $limit = is_numeric($limit) ? intval($limit) : 20;
        
        $solr = new \Model\Query\Solr\Package();
        
        $solr->setPage($page, $limit);
        $solr->available();
        $solr->language($this->Param->language());
        $solr->status($this->request->query('status'));
        $solr->title($this->request->query('keyword'));
        $solr->setRadius($this->request->query('radius'));
        
        $latitude  = $this->request->query('latitude');
        $longitude = $this->request->query('longitude');
        
        if($latitude && $longitude) {
        	$point = new \Model\Data\Point($longitude , $latitude);
        	$solr->geo($point);
        }
        
        $resultset = $solr->getResultSet();
        
        $packages = array();
        $geodist = 'geodist()';
        foreach($resultset as $doc) {
        	$item = $this->Package->findById($doc->_id);
        	if($item) {
        		$item['Package']['distance'] = round($doc->$geodist * 1000, 1);
        		unset($item['Package']['voices']);
        		$this->Patch->patchPath($item['Package']);
        		$packages[] = $item['Package'];
        	}
        }
        
        // $this->httpCache(true, time() + 3600);
        return $this->results($packages, $resultset->getNumFound());
    }
    
/**
 * @SWG\Api(
 *   path="/packages/{package_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get a package",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="package_id",
 *           description="",
 *           paramType="path",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="owner",
 *           description="",
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
    public function view($packageId) {
        $package = $this->Package->findById($packageId);
        if(!$package) return $this->fail(404);
        $package = $package['Package'];
        $voices = array();
        
        foreach($package['voices'] as $id) {
            $voice = $this->Voice->findById($id);
            if($voice) $voices[] = $voice['Voice'];
        }
        
        $owner = $this->request->query('owner');
        $boughtVoiceIds = ($owner) ? $this->Purchased->voices($owner) : array();
        
        foreach($voices as &$voice) {
        	$this->Patch->patchBought($voice, $boughtVoiceIds, $owner);
        	$this->Patch->patchPath($voice);
        	$this->Patch->patchUser($voice);
        	$this->Patch->patchPrice($voice);
        }
        $package['voices'] = $voices;
        $this->Patch->patchPath($package);
        return $this->result($package);
    }
}