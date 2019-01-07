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
APP::uses('BaseComponent', 'QiNiu.Controller/Component');
/**
 * @package
 * @category
 * @subpackage
 *
 * @SWG\Resource(
 *  apiVersion="0.2",
 *  swaggerVersion="1.1",
 *  resourcePath="/startups",
 *  basePath="http://staging.api.fishsaying.com/startups"
 * )
 * 
 * @SWG\Model(
 *   id="ConnectResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="uptoken",type="Array",required="true",items="$ref:UptokenResponse"),
 *     @SWG\Property(name="update",type="Array",required="true",items="$ref:UpdateResponse"),
 *     @SWG\Property(name="min_upload_length",type="int",required="true"),
 *     @SWG\Property(name="min_withdraw_second",type="int",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="UptokenResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="cover",type="string",required="true"),
 *     @SWG\Property(name="avatar",type="string",required="true"),
 *     @SWG\Property(name="voice",type="string",required="true")
 *   )
 * )
 * 
 * @SWG\Model(
 *   id="UpdateResponse",
 *   @SWG\Properties(
 *     @SWG\Property(name="version",type="string",required="true"),
 *     @SWG\Property(name="description",type="string",required="true")
 *   )
 * )
 * 
 */
APP::uses('AppController', 'Controller');
/**
 * The class is used to CRUD comment for voice.
 *
 * @package		app.Controller
 */
class StartupsController extends AppController {
    
    public $name = 'Startups';
    
    public $uses = array(
        'User', 'Version', 'UserAgent', 'FSConfig', 'Checkout'
    );
    
    public $components = array('QiNiu.Token', 'Param');
    
    private $userData = array();
    
    const DailySignInVersion = '1.3.0';
    
/**
 * (non-PHPdoc)
 * @see Controller::beforeFilter()
 */
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->OAuth->allow($this->name, 'connect');
    	// Register all callbacks for this controller...
    	$this->Checkout->getEventManager()->attach($this->User);
    }
    
/**
 * @SWG\Api(
 *   path="/connect.{format}",
 *   @SWG\Operations(
 *     @SWG\Operation(
 *       httpMethod="GET",
 *       summary="Get initialize information on startup",
 *       notes="",
 *       responseClass="ConnectResponse",
 *       nickname="connect",
 *       @SWG\Parameters(
 *         @SWG\Parameter(
 *           name="Authorization",
 *           description="Authorize token",
 *           paramType="header",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="user_agent",
 *           description="Format looks like this: ios-6.1-0.3.0, android-4.2-0.1.0",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="X-Device",
 *           description="",
 *           paramType="header",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="string"
 *         ),
 *         @SWG\Parameter(
 *           name="gmt",
 *           description="",
 *           paramType="query",
 *           required="false",
 *           allowMultiple="false",
 *           dataType="int"
 *         )
 *       )
 *     )
 *   )
 * )
 */
    public function connect() {
        $this->UserAgent->extract($this->getUserAgent());
        $this->Version->extract($this->UserAgent->getFishsayingVersion());
        switch(strtolower($this->UserAgent->getPlatform())) {
            case 'ios':
                return $this->ios();
                break;
            case 'android':
                return $this->android();
                break;
            default:
                return $this->web();
        }
    }
    
    private function web() {
        return $this->result($this->common());
    }
    
    private function android() {
        $ver = $this->Version->getLatest('android');
        $configure = $this->common();
        $configure['update'] = array(
    		'version'       => $ver['version'],
			'download_link' => Configure::read('APP.Android.Download.Link'),
			'description'   => $ver['description']
        );
        return $this->result($configure);
    }
    
    private function ios() {
        $ver = $this->Version->getLatest('ios');
        $configure = $this->common();
        $configure['update'] = array(
			'version'      => $ver['version'],
        	'download_url' => Configure::read('APP.IOS.Download.Link'),
			'description'  => $ver['description']
		);
        $configure['function'] = array(
    		'withdraw' => $this->enable($ver['version']),
    		'transfer' => $this->enable($ver['version']),
    		'alipay'   => $this->enable($ver['version'])
        );
        return $this->result($configure);
    }
    
    private function common() {
        $conf = $this->FSConfig->find('first');
        $splash = Configure::read('Config.language') == 'zh_CN' 
                ? (string)Hash::get($conf, 'FSConfig.splash') : '';
        return array(
    		/**
    		 * Generate three upload tokens of qiniu
             */
    		'uptoken' => array(
				'cover'  => $this->Token->upload(BaseComponent::BUCKET_COVER),
				'avatar' => $this->Token->upload(BaseComponent::BUCKET_AVATAR),
				'voice'  => $this->Token->upload(BaseComponent::BUCKET_VOICE)
    		),
    		'min_upload_length'   => Configure::read('Min.Upload.Length'),
            'max_upload_length'   => Configure::read('Max.Upload.Length'),
    		'min_withdraw_second' => Configure::read('Min.Withdrwal.Second'),
        	'share_domain'        => Configure::read('Domain.Share'),
    		'terms_of_service'    => Configure::read('URL.Terms.Of.Service'),
    		'payment_version'     => Configure::read('Payment.Version'),
            'alipay_callback_url' => Configure::read('Alipay.Callback'),
            'alipay_products'     => Configure::read('Alipay.Product'),
            'contact'             => Configure::read('Contact.Phone'),
            'splash'              => $splash,
            'daily_signup'        => $this->Version->compareTo(self::DailySignInVersion) >= 0 ? $this->dailySignIn() : 0,
            'user'                => $this->getUser(),
            'filters'             => $this->filters(),
            'web_host'            => 'http://'.Configure::read('URL.Website.Domain').'/r/'
        );
    }
    
    private function getUser() {
        $credential = $this->OAuth->getCredential();
        if($credential) {
        	$user = $this->User->getById($credential->getUserId());
        
        	if($user && isset($user[$this->User->name])) {
        		$user = $user[$this->User->name];
        		$this->Patch->patchPath($user);
        		$this->Patch->patchCash($user);
        	}
        } else {
        	$user = array();
        }
        return $user;
    }
    
/**
 * Compare version to decide whether enable functions
 * 
 * It will enable if client version lower than current 
 * defined version in configure file.
 * 
 * @param string $defined version string
 * @return boolean
 */
    private function enable($defined) {
        if($this->Version->compareTo($defined) == 1) {
            return false;
        }
        return true;
    }
    
    private function dailySignIn() {
        $credential = $this->OAuth->getCredential();
        $userId = $credential->getUserId();
        if(!$userId) return 0;
        $dc = $this->Param->deviceCode();
        if(!$dc) return 0;
        $gmt = (int)$gmt = isset($this->request->query['gmt']) ? $this->request->query['gmt'] : 8;
        $daily = new \Model\Daily\SignIn();
        if(!$daily->exist($userId, $dc) && $daily->add($userId, $dc, $gmt)) {
            $award = $daily->award();
            return $this->Checkout->dailySignIn($userId, $award) ? $award : 0;
        }
        return 0;
    }
    
    private function filters() {
        return array(
    		array(
				'title' => __('Nearby'),
				'sort'  => 'near',
				'type'  => 'voice'
    		),
    		array(
				'title' => __('Featured'),
				'sort'  => 'hot',
				'type'  => 'voice'
    		),
    		array(
				'title' => __('Experts'),
				'sort'  => 'expert',
				'type'  => 'voice'
    		)
        );
    }
}