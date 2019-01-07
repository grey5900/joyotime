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
 *  apiVersion="1.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/versions",
 *  basePath="http://staging.api.fishsaying.com/"
 * )
 * 
 * @SWG\Model(
 *   id="VersionResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="version",type="string",required="true"),
 *     @SWG\Property(name="platform",type="string",required="true"),
 *     @SWG\Property(name="description",type="string",required="true"),
 *     @SWG\Property(name="created",type="string",required="true")
 *   )
 * )
 */
APP::uses('AppController', 'Controller');
/**
 * The class is used to CRUD comment for voice.
 *
 * @package		app.Controller
 */
class VersionsController extends AppController {
    
    public $name = 'Versions';
    
    public $uses = array(
        'Version', 'UserAgent'
    );
    
    public $components = array();
    
    private $userData = array();
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
/**
 * @SWG\Api(
 *   path="/admin/versions.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Post a new version",
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
 *           name="platform",
 *           description="",
 *           defaultValue="android",
 *           @SWG\AllowableValues(valueType="LIST", values="['ios', 'android']"),
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="version",
 *           description="format: .2.11",
 *           paramType="form",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_add() {
        return $this->Version->save($this->request->data) 
            ? $this->success(): $this->fail(400, $this->errorMsg($this->Version));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/versions.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get versions",
 *       notes="",
 *       responseClass="VersionResponse",
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
 *           name="platform",
 *           description="",
 *           defaultValue="android",
 *           @SWG\AllowableValues(valueType="LIST", values="['ios', 'android']"),
 *           paramType="query",
 *           required="false",
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
 *           description="The limitation number for each page, default is 20",
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
    public function admin_index() {
        $page = $this->request->query('page');
        $limit = $this->request->query('limit');
        $page = $page ? intval($page) : 1;
        $limit = $limit ? intval($limit) : 20;
        
        $conditions = array();
        
        if($this->request->query('platform')) {
            $conditions['platform'] = $this->request->query('platform');
        }
        
        $vers = $this->Version->find('all', array(
            'conditions' => $conditions,
            'order' => array('created' => 'desc'),
            'page' => $page,
            'limit' => $limit
        ));
        
        $total = $this->Version->find('count', array(
        	'conditions' => $conditions
        ));
        return $this->results(Hash::extract($vers, '{n}.Version'), $total);
    }
}