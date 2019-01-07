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
APP::uses('CredentialItem', 'Item');
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="1.3",
 *  swaggerVersion="1.1",
 *  resourcePath="/configures",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 */

/**
 * @package		app.Controller
 */
class ConfiguresController extends AppController {
    
    public $name = 'Configures';
    
    public $uses = array('FSConfig');
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    }
    
/**
 * @SWG\Api(
 *   path="/admin/configures.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="PUT",
 *       summary="Configure API",
 *       notes="",
 *       responseClass="SuccessResponse",
 *       nickname="authorize",
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
 *           name="splash",
 *           description="URL of secondary bootup image",
 *           paramType="form",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="401",
 *            reason="Unauthorized"
 *          ),
 *          @SWG\ErrorResponse(
 *            code="400",
 *            reason="Bad Request"
 *          )
 *       )
 *     )
 *   )
 * )
 */
    public function admin_edit() {
        if($this->request->is('put')) {
            $item = $this->FSConfig->find('first', array(
                'order' => array('_id' => 'desc')
            ));
            if($item) $item = $item['FSConfig'];
            else $item['_id'] = null;
            
            $this->FSConfig->id = $item['_id'];
            if($this->FSConfig->save($this->request->data))
                return $this->success();
        }
        return $this->fail(400);
	}
	
/**
 * @SWG\Api(
 *   path="/admin/configures.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Configure API",
 *       notes="",
 *       responseClass="ConfiguresResponse",
 *       nickname="authorize",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="true",
 *           allowMultiple="false",
 *           dataType="string"
 *         )
 *       ),
 *       @SWG\ErrorResponses(
 *          @SWG\ErrorResponse(
 *            code="401",
 *            reason="Unauthorized"
 *          )
 *       )
 *     )
 *   )
 * )
 */
	public function admin_index() {
	    $item = $this->FSConfig->find('first', array(
	    	'order' => array('_id' => 'desc')
	    ));
	    $item = isset($item['FSConfig']) ? $item['FSConfig'] : array();
	    return $this->result($item);
	}
}