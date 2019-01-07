<?php
/**
 * The project of FenPay is a CRM platform based on Weixin MP API.
 *
 * Use it to communicates with Weixin MP.
 *
 * PHP 5
 *
 * FenPay(tm) : FenPay (http://fenpay.com)
 * Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) in.chengdu.cn. (http://in.chengdu.cn)
 * @link          http://fenpay.com FenPay(tm) Project
 * @since         FenPay(tm) v 0.0.1
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('HttpSocket', 'Network/Http');
App::uses('WeixinApiComponent', 'Controller/Component');
/**
 * The class is used to get specified instances of 
 * Inchengdu api wrapped classes. 
 * 
 * @package app.Controller.Component
 */
class InchengduApiComponent extends Component {
    
    public $components = array(
        'ApiHeader'  
    );
    
/**
 * Get handler of Inchengdu api.
 * @return InchengduPlaceApi
 */
    public function place() {
        return new InchengduPlaceApi($this->ApiHeader);
    }
}

abstract class InchengduApi {
    
/**
 * @var HttpSocket
 */
    protected $http;
/**
 * @var ApiHeaderComponent
 */
    protected $header;
    
    public function __construct(ApiHeaderComponent $ApiHeader) {
        $this->http = new HttpSocket();
        $this->header = $ApiHeader;
    }
}

/**
 * The class is used to get content all about place 
 * from inchengdu api.
 *
 */
class InchengduPlaceApi extends InchengduApi {
    
/**
 * Search places by keyword
 * 
 * @param WeixinReceivePackage $recv
 * @return PlacesSearchFinder
 */
    public function searchByKeyword(WeixinReceivePackage $recv) {
        $finder = new PlacesSearchFinder($recv, $this->http, $this->header);
        return $finder;
    }
    
/**
 * Search places by location
 * 
 * @param WeixinReceivePackage $recv
 * @return PlacesCategoryFinder
 */
    public function searchByLocation(WeixinReceivePackage $recv) {
        $searcher = new PlacesCategoryFinder($recv, $this->http, $this->header);
        return $searcher;
    }
    
    public function searchByLocationAndKeyword(WeixinTextReceivePackage $recv, 
            array $point) {
        $searcher = new PlacesQuickFinder($recv, $point, $this->http, $this->header);
        return $searcher;
    }
    
/**
 * @param number $placeId
 * @return array
 */
    public function getDetail($placeId) {
        $url = Configure::read('Api.baseurl') . 'place/get_detail';
        $return = $this->http->get($url, array(
    		'id' => $placeId,
        ), array(
        	'header' => $this->header->toArray($url)
        ));
        
        if($return) {
        	return json_decode($return, true);
        }
        return array();
    }
    
/**
 * @param number $placeId
 * @return array
 */
    public function listProduct($placeId) {
        $url = Configure::read('Api.baseurl') . 'place/list_product';
        $return = $this->http->get($url, array(
    		'id' => $placeId,
        ), array(
        	'header' => $this->header->toArray($url)
        ));
        
        if($return) {
        	return json_decode($return, true);
        }
        return array();
    }
    
/**
 * @param number $placeId
 * @return array
 */
    public function listEvent($placeId) {
        $url = Configure::read('Api.baseurl') . 'place/list_event';
        $this->header->setHeader('X-Dpi', 'udp');
        $headers = $this->header->toArray($url);
        $return = $this->http->get($url, array(
    		'id' => $placeId,
        ), array(
        	'header' => $headers
        ));
        
        if($return) {
        	return json_decode($return, true);
        }
        return array();
    }
    
/**
 * @param number $placeId
 * @return array
 */
    public function listCollection($placeId) {
        $url = Configure::read('Api.baseurl') . 'place/list_collection';
        $this->header->setHeader('X-Dpi', 'udp');
        $headers = $this->header->toArray($url);
        $return = $this->http->get($url, array(
    		'id' => $placeId,
        ), array(
        	'header' => $headers
        ));
        
        if($return) {
        	return json_decode($return, true);
        }
        return array();
    }
    
/**
 * @param number $placeId
 * @return array
 */
    public function listTip($placeId, $sort=0) {
        $url = Configure::read('Api.baseurl') . 'place/list_tip';
        $this->header->setHeader('X-Dpi', 'udp');
        $headers = $this->header->toArray($url);
        $return = $this->http->get($url, array(
    		'id' => $placeId,
    		'sort' => $sort,
            'page_size' => 5,
            'page_num' => 1,
        ), array(
        	'header' => $headers
        ));
        
        if($return) {
        	return json_decode($return, true);
        }
        return array();
    }
}

abstract class DataFinder {
    
/**
 * Current page number
 * @var number
 */
    protected $page = 1;
/**
 * Limit number
 * @var number
 */
    protected $limit = 9;
    
/**
 * @return array The result from db or api
 */
    abstract function getResult();
    
/**
 * @return PlacesResponse
 */
    abstract function getResponse();
    
/**
 * @param ResponsePager $pager
 */
    public function next(ResponsePager $pager) {
    	if($pager->hasNext()) {
    		$pager->finder()->setLimit($pager->limit);
    		$pager->finder()->setPage($pager->getNext());
    		$pager->store();
    		return $pager->finder();
    	}
    	return $this;
    }
    
    public function setLimit($limit) {
        $this->limit = $limit;
    }
    
    public function setPage($page = 0) {
        if(!$page || $page < 0) {
            $this->page = 1;
        } else {
            $this->page = $page;
        }
    }
    
    public function getLimit() {
        return $this->limit;
    }
    
    public function getPage() {
        return $this->page;
    }
}

class PlacesSearchFinder extends DataFinder {
    
/**
 * The path of api.
 * @var string
 */
    private $url;
    
/**
 * @var string
 */
    private $keyword;
/**
 * @var HttpSocket
 */
    private $http;
/**
 * @var ApiHeaderComponent
 */
    private $header;
/**
 * @var PlaceCategoryResponse
 */
    private $response;
    
    public function __construct(WeixinTextReceivePackage $recv, 
            HttpSocket $http, ApiHeaderComponent $header) {
        $this->http = $http;
        $this->header = $header;
        $this->keyword = $recv->getContent();
        $this->url = Configure::read('Api.baseurl') . 'place/search';
        $this->response = new PlaceSearchResponse();
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResponse()
 */
    public function getResponse() {
    	return $this->response;
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResult()
 */
    public function getResult() {
        $return = $this->http->get($this->url, array(
            'keyword' => (string) $this->keyword,
            'sort' => 1,
    		'page_size' => $this->limit,
    		'page_num' => $this->page,
        ), array(
    		'header' => $this->header->toArray($this->url)
        ));
        if($return) {
            return json_decode($return, true);
        }
        return array();
    }
}

class PlacesCategoryFinder extends DataFinder {
    
/**
 * The path of api.
 * @var string
 */
    private $url;
/**
 * The location array get from weixin server.
 * @var array
 */
    private $location;
/**
 * @var HttpSocket
 */
    private $http;
/**
 * @var ApiHeaderComponent
 */
    private $header;
/**
 * @var PlaceCategoryResponse
 */
    private $response;
    
    public function __construct(WeixinLocationReceivePackage $recv, 
            HttpSocket $http, ApiHeaderComponent $header) {
        $this->http = $http;
        $this->header = $header;
        $this->url = Configure::read('Api.baseurl') . 'place/category';
        $this->location = $recv->getLocation();
        $this->response = new PlaceCategoryResponse();
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResponse()
 */
    public function getResponse() {
        return $this->response;
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResult()
 */
    public function getResult() {
        $return = $this->http->get($this->url, array(
            'cid' => 1,    // all for 餐饮
    		'sort' => 1,
    		'lat' => (double) $this->location['x'],
    		'lng' => (double) $this->location['y'],
    		'page_size' => $this->limit,
    		'page_num' => $this->page,
        ), array(
    		'header' => $this->header->toArray($this->url)
        ));
        if($return) {
            return json_decode($return, true);
        }
        return array();
    }
}

/**
 * The class is used to get data from api server 
 * and return specify response instance.
 *
 */
class PlacesQuickFinder extends DataFinder {
    
/**
 * The path of api.
 * @var string
 */
    private $url;
/**
 * The keyword get from weixin server.
 * @var string
 */
    private $keyword;
/**
 * The location array get from weixin server.
 * @var array
 */
    private $location;
/**
 * @var HttpSocket
 */
    private $http;
/**
 * @var ApiHeaderComponent
 */
    private $header;
/**
 * @var PlaceCategoryResponse
 */
    private $response;
    
    public function __construct(WeixinTextReceivePackage $recv, array $point, 
            HttpSocket $http, ApiHeaderComponent $header) {
        $this->http = $http;
        $this->header = $header;
        $this->url = Configure::read('Api.baseurl') . 'place/quick_search';
        $this->location = $point;
        $this->keyword = $recv->getContent();
        $this->response = new PlaceCategoryResponse();
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResponse()
 */
    public function getResponse() {
        return $this->response;
    }
    
/**
 * (non-PHPdoc)
 * @see DataFinder::getResult()
 */
    public function getResult() {
        $return = $this->http->get($this->url, array(
            'keyword' => $this->keyword,    
            'lat' => $this->location['x'],  
            'lng' => $this->location['y'],  
    		'page_size' => $this->limit,
    		'page_num' => $this->page,
        ), array(
    		'header' => $this->header->toArray($this->url)
        ));
        if($return) {
            return json_decode($return, true);
        }
        return array();
    }
}

abstract class DataWrapper {
    
/**
 * @return number The total of results.
 */
    abstract function getTotal();
    
/**
 * Append $item to end of list.
 */
    abstract function append($item);
}

class PlacesWrapper extends DataWrapper {
    
/**
 * @var array
 */
    private $data;
/**
 * @var DataFinder
 */
    private $finder;
    
    public function __construct(DataFinder $finder) {
        $this->data = $finder->getResult();
        $this->finder = $finder;
    }
    
    /**
     * (non-PHPdoc)
     * @see DataWrapper::getTotal()
     */
    public function getTotal() {
        if(isset($this->data['result_list']['page_info']['total_count'])) {
            return intval($this->data['result_list']['page_info']['total_count']);
        }
        return 0;
    }
    
    public function append($item) {
        if(isset($this->data['result_list']['page_data'])) {
        	array_push($this->data['result_list']['page_data'], $item);
        }
    }
    
    public function toArray() {
        return $this->data;
    }
    
/**
 * @return PlacesResponse
 */
    public function getResponse() {
        $response = $this->finder->getResponse();
        if(isset($this->data['result_list']['page_data'])) {
            $response->setMessages($this->data['result_list']['page_data']);
        }
        return $response;
    }
}

/**
 * The class is used to control limit items
 * are displayed and pagination.
 *
 */
class ResponsePager {

/**
 * The prefix of pagination information in cache.
 * 
 * @var string
 */
    const CACHE_CONFIG = 'Response.pager';

/**
 * The identical for user.
 * 
 * @var string int
 */
    public $openId = '';

/**
 * The current page num, the initial starts from 0.
 * 
 * @var int
 */
    public $curPage = 1;

/**
 * The max page number calcualted.
 * 
 * @var int
 */
    public $maxPage = 1;

/**
 * The limitation number of items displayed for each page.
 * 
 * @var int
 */
    public $limit = 9;

/**
 * @var DataFinder
 */
    private $finder;
    
    /**
     * It doesn't allow use 'new' keyword to intialize an instance.
     */
    private function __construct() {
        
    }

/**
 * 
 * @param DataFinder $finder
 * @param PlacesWrapper $wrapper
 */
    public static function getInstance(WeixinReceivePackage $recv, DataFinder $finder, DataWrapper $wrapper) {
        $pager = new ResponsePager();
        $pager->openId = $recv->getFromUserName();
        $pager->limit = $finder->getLimit();
        $pager->curPage = $finder->getPage();
        $pager->setFinder($finder);
        $pager->maxPage = ceil($wrapper->getTotal() / $pager->limit);
        return $pager;
    }

    /**
     * Get instance of ResponsePager from cache
     * If no found in cache, return false.
     *
     * @param int $openId            
     * @return ResponsePager
     */
    public static function getFromCache($openId) {
        $pages = Cache::read($openId, self::CACHE_CONFIG);
        $pages = unserialize($pages);
        
        $pager = false;
        if($pages) {
            $pager = new ResponsePager();
            $pager->openId = $pages['openId'];
            $pager->curPage = $pages['curPage'];
            $pager->maxPage = $pages['maxPage'];
            $pager->limit = $pages['limit'];
            $pager->setFinder($pages['finder']);
        } 
        return $pager;
    }

/**
 * Get next page of instance by from user name.
 *
 * @return DataFinder
 */
    public function getNext() {
        if($this->hasNext()) {
        	$this->finder->setLimit($this->limit);
        	$this->finder->setPage(++$this->curPage);
        	$this->store();
        }
        return $this->finder;
    }

    /**
     * Is there next resultset?
     * 
     * @return boolean true means there is next result-set.
     */
    public function hasNext() {
        return ($this->curPage < $this->maxPage) ? true : false;
    }

    /**
     * Store 
     */
    public function store() {
        Cache::write($this->openId, $this->toArray(), self::CACHE_CONFIG);
    }
    
/**
 * @return DataFinder
 */
    public function finder() {
        return $this->finder;
    }
    
/**
 * @param DataFinder $finder
 */
    public function setFinder(DataFinder $finder) {
        $this->finder = $finder;
    }
    
/**
 * To array of ResponsePager
 * 
 * @return array
 */
    private function toArray() {
        $pager = array(
            'openId' => $this->openId,
            'curPage' => $this->curPage,
            'maxPage' => $this->maxPage,
            'limit' => $this->limit,
            'finder' => $this->finder,
        );
        return serialize($pager);
    }
    
/**
 * Append a new help item to end of data container
 * if there is next page.
 *
 * @return ResponsePager
 */
    public function appendNext(DataWrapper $wrapper, $item) {
    	if($this->hasNext()) {
    		$wrapper->append($item);
    	}
    	return $this;
    }
}

class ResponsePoint {
    
    const CACHE_CONFIG = 'Response.point';
    
/**
 * Save coordinate into cache
 * @param string $openId
 * @param WeixinLocationReceivePackage $recv
 */
    public function savePoint(WeixinLocationReceivePackage $recv) {
        Cache::write($recv->getFromUserName(), $recv->getLocation(), self::CACHE_CONFIG);
    }
    
/**
 * Get coordinate
 * @param string $openId
 * @return coordinate array('x' => 10.022, 'y' => 32.110);
 */
    public function getPoint($openId) {
        return Cache::read($openId, self::CACHE_CONFIG);
    }
}