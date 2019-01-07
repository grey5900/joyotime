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
 *  resourcePath="/themes",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="ThemeResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="_id",type="string",required="true"),
 *     @SWG\Property(name="title",type="string",required="true"),
 *     @SWG\Property(name="description",type="string",required="true"),
 *     @SWG\Property(name="voices",type="Array",required="true"),
 *     @SWG\Property(name="language",type="string",required="true"),
 *     @SWG\Property(name="status",type="int",required="true"),
 *     @SWG\Property(name="created",type="Date",required="true"),
 *     @SWG\Property(name="modified",type="Date",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="Themes",
 *   @SWG\Properties(
 *     @SWG\Property(name="total",type="int"),
 *     @SWG\Property(name="items",type="Array", items="$ref:ThemeResponse")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class ThemesController extends AppController {
    
    public $name = 'Themes';
    
    public $uses = array('Theme', 'Voice');
    
    /**
     * (non-PHPdoc)
     * @see Controller::beforeFilter()
     */
    public function beforeFilter() {
    	parent::beforeFilter();
    	
    	$this->OAuth->allow($this->name, 'index');
    	$this->OAuth->allow($this->name, 'view');
    	
    	// Register all callbacks for this controller...
    	$this->Theme->getEventManager()->attach($this->Voice);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/themes.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="POST",
 *       summary="Create a themes",
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
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="true",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="zh_CN or en_US",
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
        if($this->Theme->save($this->request->data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Theme));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/themes/{theme_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Update a theme",
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
 *           name="theme_id",
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
 *           name="description",
 *           description="",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="zh_CN or en_US",
 *           paramType="form",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="string"
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
    public function admin_edit($id) {
        $this->Theme->id = $id;
        if($this->Theme->save($this->request->data)) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Theme));
    }
    
    /**
     * @SWG\Api(
     *   path="/admin/themes/{theme_id}.{format}",
     *   @SWG\Operations(
     *     @SWG\Operation(
     *       httpMethod="DELETE",
     *       summary="Delete a theme",
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
     *           name="theme_id",
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
        $this->Theme->id = $id;
        if($this->Theme->save(array('deleted' => true))) return $this->success();
        return $this->fail(400, $this->errorMsg($this->Theme));
    }
    
/**
 * @SWG\Api(
 *   path="/admin/themes/{theme_id}/voice/{voice_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Push a voice into theme",
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
 *           name="theme_id",
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
 *           name="reason",
 *           description="Reason of recommand",
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
    public function admin_voice_add($themeId, $voiceId) {
        $result = $this->Theme->push($themeId, $voiceId, $this->request->data('reason'));
        return ($result) ? $this->success() : $this->fail(400);
    }
    
/**
 * @SWG\Api(
 *   path="/admin/themes/{theme_id}/voice/{voice_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="DELETE",
 *       summary="Pull a voice from theme",
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
 *           name="theme_id",
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
    public function admin_voice_delete($themId, $voiceId) {
        $result = $this->Theme->pull($themId, $voiceId);
        return ($result) ? $this->success() : $this->fail(400);
    }
    
/**
 * @SWG\Api(
 *   path="/themes.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get theme list",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="deleted",
 *           description="0: not delete yet, 1: deleted already",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="status",
 *           description="0: pending, 1: avaliable",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           dataType="int"
 *         ),
 *         @SWG\Parameter(
 *           name="language",
 *           description="zh_CN or en_US",
 *           paramType="query",
 *           required="false",
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
    public function index() {
        $conditions = $order = array();
        
        if(isset($this->request->query['status']))   $conditions['status'] = $this->request->query('status');
        if(isset($this->request->query['language'])) $conditions['language'] = $this->request->query('language');
        if(isset($this->request->query['deleted']))  $conditions['deleted'] = $this->request->query('deleted');
        
        $results = $this->Theme->find('all', array(
        	'fields' => array('voices' => 0),
        	'conditions' => $conditions,
        	'page' => $this->request->query('page'),
        	'limit' => $this->request->query('limit')?:20
        ));
        $total = $this->Theme->find('count', array(
        	'conditions' => $conditions
        ));
        
        // $this->httpCache(true, time() + 3600);
        return $this->results($results, $total);
    }
    
/**
 * @SWG\Api(
 *   path="/themes/{theme_id}.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get a theme",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="view",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="theme_id",
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
    public function view($themeId) {
        $theme = $this->Theme->findById($themeId);
        if(!$theme) return $this->fail(404);
        $theme = $theme['Theme'];
        $voices = array();
        
        foreach($theme['voices'] as $item) {
            $voice = $this->Voice->findById($item['voice_id']);
            $voice['Voice']['theme_reason'] = (string)$item['reason'];
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
        $theme['voices'] = $voices;
        return $this->result($theme);
    }
}