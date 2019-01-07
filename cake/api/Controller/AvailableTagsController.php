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
 *  resourcePath="/available_tags",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="AvailableTagsResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="title",type="string",required="true"),
 *     @SWG\Property(name="counter",type="int",required="true")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class AvailableTagsController extends AppController {
    
    public $name = 'AvailableTags';
    
    public $uses = array('Tag');
    
    const MAX_LIMIT = 10;
    
/**
 * @SWG\Api(
 *   path="/available_tags.{format}",
 *   description="",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get related tags by specified keyword",
 *       responseClass="AvailableTagsResponse",
 *       nickname="index",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="keyword",
 *           description="Query criteria",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="limit",
 *           description="The limit number for each page",
 *           paramType="query",
 *           required="false",
 *           allowMultiple=false,
 *           defaultValue="5",
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
    	$keyword = $this->request->query('keyword');
    	$limit = $this->request->query('limit');
    	if(!$limit) {
    	    $limit = self::MAX_LIMIT;
    	}
    	if($keyword && $limit && $limit <= self::MAX_LIMIT) {
            $rows = $this->Tag->findByRegex($keyword, $limit);
            return $this->result(Hash::extract($rows, '{n}.Tag'));
    	}
    	return $this->fail(400);
    }
}