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
 *  resourcePath="/banners",
 *  basePath="http://staging.api.fishsaying.com"
 * )
 * 
 * @SWG\Model(
 *   id="BannerResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="cover",type="string",required="true"),
 *     @SWG\Property(name="link",type="string",required="true")
 *   )
 * )
 */

/**
 * @package		app.Controller
 */
class BannersController extends AppController {
    
    public $name = 'Banners';
    
//     public $uses = array('Tag');
    
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
    }
    
/**
 * @SWG\Api(
 *   path="/banners.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get banners",
 *       responseClass="BannersResponse",
 *       nickname="index",
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
        if($this->Param->language() != 'zh_CN') return $this->results(array(), 0);
        $banners = array(
//             array(
//                 'cover' => 'http://pic26.nipic.com/20121123/86074_101357359000_2.jpg',
//                 'link' => 'voice://52cbb92d670333312c8b45a3'
//             ),
//             array(
//                 'cover' => 'http://pic26.nipic.com/20121211/9910198_164005659000_2.jpg',
//                 'link' => 'newpost://0?title=test_title&location=34.0,118.3&tags=tag1,tag2,tag3'
//             ),
//             array(
//                 'cover' => 'http://pic22.nipic.com/20120702/7416978_134323512000_2.jpg',
//                 'link' => 'http://www.baidu.com/'
//             )
        );
        return $this->results($banners, 0);
    }
}